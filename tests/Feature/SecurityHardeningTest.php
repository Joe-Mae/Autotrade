<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_security_headers_are_sent_on_public_pages(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_admin_login_is_rate_limited(): void
    {
        $user = User::firstOrFail();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.submit'), [
                'email' => $user->email,
                'password' => 'mauvais-mot-de-passe',
            ]);
        }

        $response = $this->post(route('login.submit'), [
            'email' => $user->email,
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'Trop de tentatives',
            (string) session('errors')->first('email')
        );
        $this->assertGuest();
    }

    public function test_public_contact_form_is_rate_limited(): void
    {
        $payload = [
            'nom' => 'Visiteur',
            'email' => 'visiteur@example.test',
            'telephone' => '90000000',
            'message' => 'Bonjour',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('contact.store'), $payload);
        }

        $this->post(route('contact.store'), $payload)
            ->assertSessionHas('error');
    }

    public function test_honeypot_submissions_are_silently_ignored(): void
    {
        $this->post(route('contact.store'), [
            'nom' => 'Robot',
            'email' => 'robot@example.test',
            'telephone' => '000',
            'message' => 'Spam',
            'site_web' => 'https://spam.example',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseMissing('contact_messages', [
            'email' => 'robot@example.test',
        ]);
    }

    public function test_vehicle_upload_rejects_executable_files(): void
    {
        $user = User::firstOrFail();
        $vehicle = Vehicle::firstOrFail();

        $this->actingAs($user)
            ->put(route('admin.vehicles.update', $vehicle), [
                'nom' => $vehicle->nom,
                'marque' => $vehicle->marque,
                'categorie' => $vehicle->categorie,
                'etat' => $vehicle->etat,
                'annee' => $vehicle->annee,
                'carburant' => $vehicle->carburant,
                'boite' => $vehicle->boite,
                'km' => $vehicle->km,
                'main_image' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('main_image');
    }

    public function test_smtp_password_is_encrypted_at_rest(): void
    {
        $user = User::firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.settings.update'), [
                'site_name' => 'AUTOTRADE',
                'site_tagline' => 'Vente de véhicules',
                'hero_title_1' => 'Titre',
                'mail_password' => 'secret-smtp',
            ])
            ->assertRedirect(route('admin.settings'));

        $stored = (string) Setting::where('key', 'mail_password')->value('value');

        $this->assertNotSame('secret-smtp', $stored);
        $this->assertSame('secret-smtp', Crypt::decryptString($stored));
    }

    public function test_public_forms_contain_the_honeypot_field(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('name="site_web"', false);

        $vehicle = Vehicle::query()->where('disponible', true)->firstOrFail();

        $this->get(route('demande.achat', $vehicle))
            ->assertOk()
            ->assertSee('name="site_web"', false);
    }
}
