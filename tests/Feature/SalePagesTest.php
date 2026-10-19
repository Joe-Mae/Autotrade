<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SalePagesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_public_sale_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/catalogue')->assertOk();
        $this->get('/contact')->assertOk();
    }

    public function test_vente_page_redirects_to_catalogue(): void
    {
        $this->get('/vente')->assertRedirect(route('catalogue'));
    }

    public function test_catalogue_filters_render(): void
    {
        $this->get('/catalogue?categorie=SUV&etat=Occasion')->assertOk();
    }

    public function test_vehicle_sale_pages_render(): void
    {
        $vehicle = Vehicle::query()->firstOrFail();

        $this->get(route('vehicule.show', $vehicle))->assertOk();
        $this->get(route('demande.achat', $vehicle))->assertOk();
    }

    public function test_purchase_request_submission(): void
    {
        $vehicle = Vehicle::query()->firstOrFail();

        $this->post(route('demande.achat.store', $vehicle), [
            'nom' => 'Test Achat',
            'email' => 'test@example.test',
            'phone_code' => '+228',
            'phone_number' => '90 00 00 00',
            'interet' => 'comptant',
            'message' => 'test',
        ])->assertRedirect(route('demande.achat', $vehicle));

        $pr = PurchaseRequest::latest()->first();
        $this->assertNotNull($pr);
        $this->assertStringStartsWith('+228 ', $pr->telephone);
        $this->assertStringContainsString('90 00 00 00', $pr->telephone);
        $pr->delete();
    }

    public function test_admin_console_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_admin_sales_console_renders(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Inventaire des véhicules')
            ->assertSee('Ajouter un véhicule')
            ->assertSee('Votre flotte');

        $this->actingAs($admin)->get('/admin/vehicules/creer')->assertOk();
    }

    public function test_admin_can_export_vehicles_csv(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.vehicles.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Nom;Marque', $response->streamedContent());
    }

    public function test_admin_notifications_endpoint_returns_payload(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.notifications'))
            ->assertOk()
            ->assertJsonStructure([
                'unread_messages',
                'pending_purchases',
                'latest_message_id',
                'latest_purchase_id',
                'ts',
            ]);
    }

    public function test_admin_settings_page_renders(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/parametres')
            ->assertOk()
            ->assertSee('PARAMÈTRES')
            ->assertSee('ENREGISTRER LES PARAMÈTRES');
    }

    public function test_admin_settings_text_update(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        // Initialiser la session / jeton CSRF.
        $this->actingAs($admin)->get('/admin/parametres');

        $this->actingAs($admin)
            ->post('/admin/parametres', [
                '_token' => session()->token(),
                'site_name' => 'AUTOTRADE TEST',
                'site_tagline' => 'tagline test',
                'hero_title_1' => 'TEST HERO',
            ])
            ->assertRedirect(route('admin.settings'));

        $this->assertSame('AUTOTRADE TEST', Setting::get('site_name'));
        $this->assertSame('TEST HERO', Setting::get('hero_title_1'));

        // Restauration pour ne pas polluer la base.
        Setting::put('site_name', 'AUTOTRADE');
        Setting::put('site_tagline', Setting::defaults()['site_tagline']);
        Setting::put('hero_title_1', Setting::defaults()['hero_title_1']);
    }

    public function test_admin_settings_image_upload(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)->get('/admin/parametres');

        $file = UploadedFile::fake()->image('slide.jpg', 400, 300);

        $this->actingAs($admin)
            ->post('/admin/parametres', [
                '_token' => session()->token(),
                'site_name' => 'AUTOTRADE',
                'site_tagline' => 'tagline test',
                'hero_title_1' => 'TEST HERO',
                'logo' => $file,
            ])
            ->assertRedirect(route('admin.settings'));

        $path = Setting::get('logo', '');
        $this->assertNotEmpty($path);
        $this->assertFileExists(public_path($path));

        @unlink(public_path($path));
        Setting::put('logo', '');
        Setting::put('site_tagline', Setting::defaults()['site_tagline']);
        Setting::put('hero_title_1', Setting::defaults()['hero_title_1']);
    }

    public function test_admin_vehicle_form_shows_live_photo_manager(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/vehicules/creer')
            ->assertOk()
            ->assertSee('photoGrid')
            ->assertSee('photoManager')
            ->assertSee('Premier plan')
            ->assertSee('primary_choice')
            ->assertSee('new:');
    }

    public function test_admin_can_pick_new_upload_as_primary_image(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $vehicle = Vehicle::create([
            'slug' => 'test-primary-'.uniqid(),
            'nom' => 'Test Primary',
            'marque' => 'Test',
            'categorie' => 'SUV',
            'etat' => 'Occasion',
            'annee' => 2020,
            'carburant' => 'Essence',
            'boite' => 'Manuel',
            'puissance' => '—',
            'km' => '1000',
            'places' => 2,
            'image' => 'assets/car-storm.jpg',
            'galerie' => ['assets/car-storm.jpg'],
            'prix_vente' => 1000,
            'disponible' => true,
            'badge' => 'Disponible',
            'description' => 'test',
        ]);

        $this->actingAs($admin)->get(route('admin.vehicles.edit', $vehicle));

        $this->actingAs($admin)
            ->put(route('admin.vehicles.update', $vehicle), [
                '_token' => session()->token(),
                'nom' => 'Test Primary',
                'marque' => 'Test',
                'categorie' => 'SUV',
                'etat' => 'Occasion',
                'annee' => 2020,
                'carburant' => 'Essence',
                'boite' => 'Manuel',
                'km' => '1000',
                'prix_vente' => 1000,
                'description' => 'test',
                'disponible' => 1,
                'primary_choice' => 'new:0',
                'photos' => [UploadedFile::fake()->image('nouvelle.jpg', 640, 480)],
            ])
            ->assertRedirect(route('admin'));

        $vehicle->refresh();

        $this->assertStringStartsWith('uploads/vehicles/', (string) $vehicle->image);
        $this->assertSame($vehicle->image, $vehicle->galerie[0]);
        $this->assertContains('assets/car-storm.jpg', $vehicle->galerie);

        @unlink(public_path($vehicle->image));
        $vehicle->delete();
    }

    public function test_admin_can_pick_existing_photo_as_primary_image(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $vehicle = Vehicle::create([
            'slug' => 'test-existing-'.uniqid(),
            'nom' => 'Test Existing',
            'marque' => 'Test',
            'categorie' => 'SUV',
            'etat' => 'Occasion',
            'annee' => 2020,
            'carburant' => 'Essence',
            'boite' => 'Manuel',
            'puissance' => '—',
            'km' => '1000',
            'places' => 2,
            'image' => 'assets/car-storm.jpg',
            'galerie' => ['assets/car-storm.jpg', 'assets/car-fallback.jpg'],
            'prix_vente' => 1000,
            'disponible' => true,
            'badge' => 'Disponible',
            'description' => 'test',
        ]);

        $this->actingAs($admin)->get(route('admin.vehicles.edit', $vehicle));

        $this->actingAs($admin)
            ->put(route('admin.vehicles.update', $vehicle), [
                '_token' => session()->token(),
                'nom' => 'Test Existing',
                'marque' => 'Test',
                'categorie' => 'SUV',
                'etat' => 'Occasion',
                'annee' => 2020,
                'carburant' => 'Essence',
                'boite' => 'Manuel',
                'km' => '1000',
                'prix_vente' => 1000,
                'description' => 'test',
                'disponible' => 1,
                'primary_choice' => 'assets/car-fallback.jpg',
            ])
            ->assertRedirect(route('admin'));

        $vehicle->refresh();

        $this->assertSame('assets/car-fallback.jpg', $vehicle->image);
        $this->assertSame('assets/car-fallback.jpg', $vehicle->galerie[0]);

        $vehicle->delete();
    }

    public function test_purchase_request_shows_confirmation_popup(): void
    {
        $vehicle = Vehicle::query()->firstOrFail();

        $response = $this->followingRedirects()->post(route('demande.achat.store', $vehicle), [
            'nom' => 'Popup Achat',
            'email' => 'popup-achat@example.test',
            'phone_code' => '+228',
            'phone_number' => '91 00 00 00',
            'interet' => 'comptant',
            'message' => 'test',
        ]);

        $response->assertOk();
        $response->assertSee('confirmModal', false);
        $response->assertSee('Demande envoyée');

        PurchaseRequest::where('email', 'popup-achat@example.test')->delete();
    }

    public function test_contact_message_shows_confirmation_popup(): void
    {
        $response = $this->followingRedirects()->post('/contact', [
            'nom' => 'Popup Contact',
            'email' => 'popup-contact@example.test',
            'telephone' => '+228 91 00 00 00',
            'message' => 'test popup',
        ]);

        $response->assertOk();
        $response->assertSee('confirmModal', false);
        $response->assertSee('Message envoyé');

        ContactMessage::where('email', 'popup-contact@example.test')->delete();
    }

    public function test_admin_vehicles_table_has_bulk_selection(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('vehBulkBar')
            ->assertSee('vehCheckAll')
            ->assertSee('veh-check', false);
    }

    public function test_admin_can_bulk_delete_selected_vehicles(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $keep = Vehicle::query()->orderBy('id')->first();
        $targets = Vehicle::query()->where('id', '!=', $keep->id)->limit(2)->get();
        $ids = $targets->pluck('id')->all();

        $this->assertCount(2, $ids);

        $this->actingAs($admin)->get('/admin');

        $this->actingAs($admin)
            ->delete(route('admin.vehicles.bulk'), [
                '_token' => session()->token(),
                'ids' => $ids,
            ])
            ->assertRedirect(route('admin'));

        $this->assertDatabaseMissing('vehicles', ['id' => $ids[0]]);
        $this->assertDatabaseMissing('vehicles', ['id' => $ids[1]]);
        $this->assertDatabaseHas('vehicles', ['id' => $keep->id]);
    }

    public function test_admin_bulk_delete_rejects_empty_selection(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)->get('/admin');

        $this->actingAs($admin)
            ->delete(route('admin.vehicles.bulk'), [
                '_token' => session()->token(),
                'ids' => [],
            ])
            ->assertSessionHasErrors('ids');
    }

    public function test_signing_a_purchase_marks_the_vehicle_as_sold(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();

        $purchase = PurchaseRequest::create([
            'vehicle_id' => $vehicle->id,
            'nom' => 'Client Signé',
            'email' => 'signe@example.test',
            'telephone' => '+228 90 00 00 00',
            'interet' => 'comptant',
            'message' => 'test',
            'statut' => 'en_attente',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.purchases.status', $purchase), ['statut' => 'signee'])
            ->assertRedirect(route('admin').'#ventes');

        // Le véhicule bascule automatiquement en « Vendu ».
        $this->assertFalse($vehicle->fresh()->disponible);

        // Il quitte le catalogue et rejoint la section « Voitures vendues ».
        $this->get('/catalogue')
            ->assertOk()
            ->assertSee('Voitures vendues')
            ->assertSee($vehicle->nom);

        // Sa fiche publique n'offre plus l'achat.
        $this->get(route('vehicule.show', $vehicle))
            ->assertOk()
            ->assertSee('VENDU')
            ->assertDontSee("DEMANDER L'ACHAT", false);
    }

    public function test_cancelling_a_signed_purchase_puts_the_vehicle_back_on_sale(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();

        $purchase = PurchaseRequest::create([
            'vehicle_id' => $vehicle->id,
            'nom' => 'Client Signé',
            'email' => 'signe@example.test',
            'telephone' => '+228 90 00 00 00',
            'interet' => 'comptant',
            'message' => 'test',
            'statut' => 'signee',
        ]);

        $vehicle->update(['disponible' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.purchases.status', $purchase), ['statut' => 'refusee'])
            ->assertRedirect(route('admin').'#ventes');

        $this->assertTrue($vehicle->fresh()->disponible);

        $this->get(route('vehicule.show', $vehicle))
            ->assertOk()
            ->assertSee("DEMANDER L'ACHAT", false);
    }

    public function test_vehicle_stays_sold_while_another_signed_request_exists(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();

        $make = fn (string $statut) => PurchaseRequest::create([
            'vehicle_id' => $vehicle->id,
            'nom' => 'Client',
            'email' => 'client-'.$statut.'@example.test',
            'telephone' => '+228 90 00 00 00',
            'interet' => 'comptant',
            'message' => 'test',
            'statut' => $statut,
        ]);

        $signed = $make('signee');
        $other = $make('en_attente');

        $vehicle->update(['disponible' => false]);

        // On clôt la demande non signée : le véhicule reste vendu.
        $this->actingAs($admin)
            ->patch(route('admin.purchases.status', $other), ['statut' => 'refusee']);

        $this->assertFalse($vehicle->fresh()->disponible);

        // On annule la demande signée : plus aucune vente, le véhicule repasse en vente.
        $this->actingAs($admin)
            ->patch(route('admin.purchases.status', $signed), ['statut' => 'refusee']);

        $this->assertTrue($vehicle->fresh()->disponible);
    }

    public function test_catalogue_hides_sold_vehicles_and_groups_them_in_sold_section(): void
    {
        $sold = Vehicle::query()->whereNotNull('prix_vente')->orderBy('nom')->firstOrFail();
        $sold->update(['disponible' => false]);

        $availableCount = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->count();

        $this->get('/catalogue')
            ->assertOk()
            ->assertSee('Voitures vendues')
            ->assertSee($sold->nom)
            ->assertSee($availableCount.' véhicule(s)');
    }

    public function test_sold_vehicle_page_does_not_offer_purchase(): void
    {
        $sold = Vehicle::query()->whereNotNull('prix_vente')->orderBy('nom')->firstOrFail();
        $sold->update(['disponible' => false]);

        $this->get(route('vehicule.show', $sold))
            ->assertOk()
            ->assertSee('VENDU')
            ->assertDontSee("DEMANDER L'ACHAT", false);
    }

    public function test_available_vehicle_page_offers_purchase(): void
    {
        $available = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();

        $this->get(route('vehicule.show', $available))
            ->assertOk()
            ->assertSee("DEMANDER L'ACHAT", false);
    }

    public function test_vehicle_status_badge_defaults_to_en_vente_when_no_custom_badge(): void
    {
        $available = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();
        $available->update(['badge' => null]);

        $this->get('/catalogue')
            ->assertOk()
            ->assertSee('badge-dispo', false)
            ->assertSee('En vente');
    }

    public function test_vehicle_status_badge_keeps_custom_label_when_set(): void
    {
        $available = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();
        $available->update(['badge' => 'Nouveauté']);

        $this->get('/catalogue')
            ->assertOk()
            ->assertSee('Nouveauté');
    }
}
