<?php

namespace App\Http\Controllers;

use App\Mail\PurchaseRequestConfirmation;
use App\Mail\PurchaseRequestNotification;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function create(string $vehicle)
    {
        $vehicle = $this->findSellableVehicle($vehicle);

        if (! $vehicle) {
            return redirect()
                ->route('catalogue')
                ->with('error', 'Ce véhicule n\'est plus disponible à la vente. Parcourez notre catalogue pour découvrir d\'autres modèles.');
        }

        return view('pages.demande-achat', compact('vehicle'));
    }

    public function store(Request $request, string $vehicle)
    {
        // Champ piège invisible : seuls les robots le remplissent.
        // On simule un succès sans rien enregistrer ni envoyer.
        if ($request->filled('site_web')) {
            return redirect()->route('catalogue')
                ->with('success', 'Votre demande a bien été prise en compte. Un conseiller AUTOTRADE vous recontacte sous 24 heures.');
        }

        $vehicle = $this->findSellableVehicle($vehicle);

        if (! $vehicle) {
            return redirect()
                ->route('catalogue')
                ->with('error', 'Ce véhicule n\'est plus disponible à la vente. Parcourez notre catalogue pour découvrir d\'autres modèles.');
        }

        $data = $request->validate([
            'nom' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone_code' => 'required|string|regex:/^\+\d{1,4}$/',
            'phone_number' => 'required|string|max:30',
            'interet' => 'required|string|max:80',
            'message' => 'nullable|string|max:2000',
        ]);

        $telephone = trim($data['phone_code'].' '.$data['phone_number']);

        $purchase = PurchaseRequest::create([
            'vehicle_id' => $vehicle->id,
            'nom' => $data['nom'],
            'email' => $data['email'],
            'telephone' => $telephone,
            'interet' => $data['interet'] ?? null,
            'message' => $data['message'] ?? null,
            'statut' => 'en_attente',
        ]);

        // Confirmation au client, puis alerte à l'entreprise.
        $this->sendNotificationMails([
            [$purchase->email, new PurchaseRequestConfirmation($purchase)],
            [Setting::notificationEmail(), new PurchaseRequestNotification($purchase)],
        ]);

        return redirect()
            ->route('demande.achat', $vehicle)
            ->with('success', 'Demande enregistrée. Un conseiller vous recontacte sous 24 heures.')
            ->with('popup', [
                'title' => 'Demande envoyée',
                'message' => 'Votre demande a bien été prise en compte. Un conseiller AUTOTRADE vous recontacte sous 24 heures.',
            ]);
    }

    /**
     * Récupère un véhicule vendable par son slug, ou null s'il est
     * introuvable ou sans prix de vente. Évite une page 404 brute.
     */
    private function findSellableVehicle(string $slug): ?Vehicle
    {
        if ($slug === '') {
            return null;
        }

        return Vehicle::query()
            ->where('slug', $slug)
            ->whereNotNull('prix_vente')
            ->first();
    }
}
