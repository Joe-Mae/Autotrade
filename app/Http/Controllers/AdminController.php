<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\PurchaseRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::orderBy('nom')->get();
        $purchases = PurchaseRequest::with('vehicle')->latest()->get();
        $messages = ContactMessage::latest()->get();

        $signed = $purchases->where('statut', 'signee');
        $pendingPurchases = $purchases->whereIn('statut', ['en_attente', 'en_cours'])->values();

        // Clients distincts (regroupés par e-mail) issus des demandes et des messages.
        // On repasse en collection simple : Eloquent\Collection::merge() attend des modèles.
        $clients = $purchases
            ->map(fn ($p) => ['nom' => $p->nom, 'email' => $p->email, 'date' => $p->created_at])
            ->toBase()
            ->merge($messages->map(fn ($m) => ['nom' => $m->nom, 'email' => $m->email, 'date' => $m->created_at])->toBase())
            ->filter(fn ($c) => trim((string) $c['email']) !== '')
            ->groupBy(fn ($c) => strtolower((string) $c['email']))
            ->map(fn ($group) => [
                'nom' => $group->sortByDesc('date')->first()['nom'],
                'email' => $group->sortByDesc('date')->first()['email'],
                'contacts' => $group->count(),
            ])
            ->values();

        $stats = [
            'flotte' => $vehicles->count(),
            'disponibles' => $vehicles->where('disponible', true)->count(),
            'vendus' => $vehicles->where('disponible', false)->count(),
            'clients' => $clients->count(),
            'demandes_achat' => $purchases->count(),
            'pending' => $pendingPurchases->count(),
            'signees' => $signed->count(),
            'montant_signe' => (int) $signed->sum(fn ($p) => $p->vehicle?->prix_vente ?? 0),
            'ventes_potentielles' => (int) $purchases->sum(fn ($p) => $p->vehicle?->prix_vente ?? 0),
            'messages_non_lus' => $messages->where('lu', false)->count(),
        ];

        return view('admin.index', compact('vehicles', 'purchases', 'messages', 'clients', 'pendingPurchases', 'stats'));
    }

    /**
     * Export CSV de la flotte, téléchargeable depuis l'inventaire.
     */
    public function exportVehicles()
    {
        $vehicles = Vehicle::orderBy('nom')->get();

        $callback = function () use ($vehicles) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nom', 'Marque', 'Année', 'Catégorie', 'État', 'Moteur', 'Transmission', 'Kilométrage', 'Prix de vente', 'Disponibilité'], ';');

            foreach ($vehicles as $v) {
                fputcsv($out, [
                    $v->nom,
                    $v->marque,
                    $v->annee,
                    $v->categorie,
                    $v->etat,
                    $v->carburant,
                    $v->boite,
                    $v->km,
                    $v->prix_vente ?? '',
                    $v->disponible ? 'Disponible' : 'Vendu',
                ], ';');
            }

            fclose($out);
        };

        return response()->streamDownload($callback, 'flotte-'.now()->format('Y-m-d').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Point de polling léger pour notifier la console admin en temps quasi réel.
     */
    public function notifications()
    {
        $lastMessageId = (int) ContactMessage::max('id');
        $lastPurchaseId = (int) PurchaseRequest::max('id');

        return response()->json([
            'unread_messages' => ContactMessage::where('lu', false)->count(),
            'pending_purchases' => PurchaseRequest::where('statut', 'en_attente')->count(),
            'latest_message_id' => $lastMessageId,
            'latest_purchase_id' => $lastPurchaseId,
            'ts' => now()->timestamp,
        ]);
    }

    public function createVehicle()
    {
        return view('admin.vehicle-form', [
            'vehicle' => new Vehicle,
            'editing' => false,
        ]);
    }

    public function storeVehicle(Request $request)
    {
        $data = $this->validateVehicle($request);
        $data['slug'] = $this->uniqueSlug($data['nom']);

        $uploaded = $this->collectUploadedPhotos($request);
        $data['galerie'] = $this->buildGalerie($request, null, $uploaded);

        if (is_array($data['galerie']) && count($data['galerie'])) {
            $data['image'] = $this->resolvePrimaryImage($request->input('primary_choice'), $uploaded, $data['galerie']);
            $data['galerie'] = $this->orderByPrimary($data['galerie'], $data['image']);
        } else {
            $data['image'] = 'assets/car-storm.jpg';
        }

        Vehicle::create($data);

        return redirect()->route('admin')->with('success', 'Véhicule ajouté au catalogue de vente.');
    }

    public function editVehicle(Vehicle $vehicle)
    {
        return view('admin.vehicle-form', [
            'vehicle' => $vehicle,
            'editing' => true,
        ]);
    }

    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $data = $this->validateVehicle($request);
        if ($vehicle->nom !== $data['nom']) {
            $data['slug'] = $this->uniqueSlug($data['nom'], $vehicle->id);
        }

        // Si le formulaire n'envoie pas la case « disponible » (retirée de l'écran),
        // on conserve la disponibilité actuelle du véhicule (évite de le remettre en vente
        // à chaque édition).
        if (! $request->has('disponible')) {
            $data['disponible'] = $vehicle->disponible;
        }

        $uploaded = $this->collectUploadedPhotos($request);
        $data['galerie'] = $this->buildGalerie($request, $vehicle, $uploaded);

        if (is_array($data['galerie']) && count($data['galerie'])) {
            $fallback = in_array($vehicle->image, $data['galerie'], true) ? $vehicle->image : null;
            $data['image'] = $this->resolvePrimaryImage($request->input('primary_choice'), $uploaded, $data['galerie'], $fallback);
            $data['galerie'] = $this->orderByPrimary($data['galerie'], $data['image']);
        } else {
            $data['image'] = $vehicle->image ?: 'assets/car-storm.jpg';
        }

        $vehicle->update($data);

        return redirect()->route('admin')->with('success', 'Véhicule mis à jour.');
    }

    public function destroyVehicle(Vehicle $vehicle)
    {
        $this->deleteVehicleImages($vehicle);
        $vehicle->delete();

        return redirect()->route('admin')->with('success', 'Véhicule supprimé.');
    }

    /**
     * Suppression de masse : l'admin coche plusieurs véhicules
     * puis les supprime en une seule action.
     */
    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:vehicles,id',
        ]);

        $vehicles = Vehicle::query()->whereIn('id', $data['ids'])->get();

        foreach ($vehicles as $vehicle) {
            $this->deleteVehicleImages($vehicle);
            $vehicle->delete();
        }

        return redirect()
            ->route('admin')
            ->with('success', $vehicles->count().' véhicule(s) supprimé(s).');
    }

    public function toggleDisponible(Vehicle $vehicle)
    {
        $vehicle->update(['disponible' => ! $vehicle->disponible]);

        return redirect()->route('admin')->with('success', $vehicle->disponible ? 'Véhicule remis en vente.' : 'Véhicule marqué comme vendu.');
    }

    public function updatePurchaseStatus(Request $request, PurchaseRequest $purchase)
    {
        $data = $request->validate([
            'statut' => 'required|in:en_attente,en_cours,signee,refusee',
        ]);

        $wasSigned = $purchase->statut === 'signee';
        $purchase->update($data);
        $isSigned = $purchase->statut === 'signee';

        $message = 'Statut de la demande d\'achat mis à jour.';
        $vehicle = $purchase->vehicle;

        if ($vehicle) {
            if ($isSigned) {
                // Une vente est signée : le véhicule passe automatiquement en « Vendu »
                // et quitte donc le catalogue public pour la section « Voitures vendues ».
                if ($vehicle->disponible) {
                    $vehicle->update(['disponible' => false]);
                }
                $message = 'Vente signée : '.$vehicle->nom.' est désormais marqué comme vendu.';
            } elseif ($wasSigned && ! $vehicle->purchaseRequests()->where('statut', 'signee')->exists()) {
                // Plus aucune vente signée pour ce véhicule : il repasse en vente.
                if (! $vehicle->disponible) {
                    $vehicle->update(['disponible' => true]);
                }
                $message = 'Vente annulée : '.$vehicle->nom.' est de nouveau en vente.';
            }
        }

        return redirect(route('admin').'#ventes')->with('success', $message);
    }

    public function markMessageRead(ContactMessage $message)
    {
        $message->update(['lu' => true]);

        return redirect(route('admin').'#clients')->with('success', 'Message marqué comme lu.');
    }

    private function validateVehicle(Request $request): array
    {
        $data = $request->validate([
            'nom' => 'required|string|max:120',
            'marque' => 'required|string|max:80',
            'categorie' => 'required|in:Super Sport,Luxe & Confort,Électrique,SUV',
            'etat' => 'required|in:Neuf,Occasion,Reconditionné',
            'annee' => 'required|integer|min:1950|max:2100',
            'carburant' => 'required|in:Essence,Diesel,Hybride,Électrique',
            'boite' => 'required|in:Manuel,Automatique,Semi-automatique',
            'puissance' => 'nullable|string|max:40',
            'km' => 'required|string|max:40',
            'places' => 'nullable|integer|min:1|max:9',
            'prix_vente' => 'nullable|numeric|min:0',
            'disponible' => 'nullable|boolean',
            'badge' => 'nullable|string|max:60',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|string|max:255',
            // Seules de vraies images sont acceptées : le type MIME est vérifié par la
            // règle « image » et l'extension est ensuite forcée depuis le contenu réel.
            'main_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'galerie' => 'nullable|string',
            'photos' => 'nullable|array|max:4',
            'photos.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['disponible'] = $request->boolean('disponible', true);
        $data['prix_vente'] = $request->filled('prix_vente') ? (int) ceil((float) $request->prix_vente) : null;
        $data['puissance'] = trim((string) $request->input('puissance', '')) ?: '—';
        $data['places'] = (int) ($request->input('places', 2) ?: 2);

        return $data;
    }

    private function storeUploadedImage($file): string
    {
        $directory = public_path('uploads/vehicles');

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Extension déduite du contenu réel du fichier, jamais du nom fourni par le client.
        $extension = strtolower((string) $file->extension());

        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw ValidationException::withMessages([
                'main_image' => 'Format d\'image non autorisé (jpg, jpeg, png ou webp).',
            ]);
        }

        $name = Str::random(40).'.'.$extension;
        $file->move($directory, $name);

        return 'uploads/vehicles/'.$name;
    }

    private function parseGalerie(Request $request): array
    {
        $raw = $request->input('galerie', '');
        if (is_array($raw)) {
            return array_values(array_filter($raw));
        }

        $parts = preg_split('/[\n,]+/', (string) $raw) ?: [];
        $parts = array_map('trim', $parts);

        return array_values(array_filter($parts));
    }

    private function buildGalerie(Request $request, ?Vehicle $vehicle = null, array $uploaded = []): ?array
    {
        $paths = [];

        if ($request->filled('galerie')) {
            $paths = $this->parseGalerie($request);
        } elseif ($vehicle && is_array($vehicle->galerie)) {
            // conserver l'ancienne galerie si aucun texte saisi
            $paths = $vehicle->galerie;
        }

        foreach ($uploaded as $path) {
            $paths[] = $path;
        }

        return count($paths) ? array_values(array_unique(array_slice($paths, 0, 4))) : null;
    }

    /**
     * Supprime les fichiers image téléversés d'un véhicule
     * (uniquement ceux du dossier uploads/vehicles).
     */
    private function deleteVehicleImages(Vehicle $vehicle): void
    {
        $paths = array_merge(
            [$vehicle->image],
            is_array($vehicle->galerie) ? $vehicle->galerie : []
        );

        foreach ($paths as $path) {
            if (is_string($path) && str_starts_with($path, 'uploads/vehicles/')) {
                $file = public_path($path);
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }

    private function resolveVehicleImage(Request $request, ?string $text, ?Vehicle $vehicle = null): string
    {
        if ($request->hasFile('main_image')) {
            return $this->storeUploadedImage($request->file('main_image'));
        }

        if ($request->hasFile('photos')) {
            return $this->storeUploadedImage($request->file('photos')[0]);
        }

        if (is_string($text) && trim($text) !== '' && trim($text) !== 'assets/car-storm.jpg') {
            return trim($text);
        }

        return $vehicle?->image ?: 'assets/car-storm.jpg';
    }

    private function collectUploadedPhotos(Request $request): array
    {
        $uploaded = [];

        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            if ($file && $file->isValid()) {
                $uploaded[] = $this->storeUploadedImage($file);
            }
        }

        foreach ((array) $request->file('photos', []) as $file) {
            if ($file && $file->isValid()) {
                $uploaded[] = $this->storeUploadedImage($file);
            }
        }

        return $uploaded;
    }

    /**
     * Détermine l'image à afficher en premier plan.
     *
     * Le formulaire peut renvoyer :
     * - un chemin existant (photo déjà enregistrée),
     * - « new:N » pour la N-ième photo fraîchement téléversée.
     */
    private function resolvePrimaryImage(?string $choice, array $uploaded, array $galerie, ?string $fallback = null): string
    {
        $choice = (string) $choice;

        if (Str::startsWith($choice, 'new:')) {
            $index = (int) substr($choice, 4);
            if (isset($uploaded[$index])) {
                return $uploaded[$index];
            }
        }

        if ($choice !== '' && in_array($choice, $galerie, true)) {
            return $choice;
        }

        if ($fallback && in_array($fallback, $galerie, true)) {
            return $fallback;
        }

        return $galerie[0];
    }

    private function orderByPrimary(array $galerie, string $primary): array
    {
        $ordered = [$primary];
        foreach ($galerie as $path) {
            if ($path !== $primary) {
                $ordered[] = $path;
            }
        }

        return array_values(array_slice($ordered, 0, 4));
    }

    private function uniqueSlug(string $nom, ?int $ignoreId = null): string
    {
        $base = Str::slug($nom);
        $slug = $base;
        $i = 1;

        while (
            Vehicle::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
