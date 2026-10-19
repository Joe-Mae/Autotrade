<?php

namespace Tests\Feature;

use App\Mail\PurchaseRequestConfirmation;
use App\Mail\PurchaseRequestNotification;
use App\Models\Setting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class PurchaseEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Client Mail',
            'email' => 'client-mail@example.test',
            'phone_code' => '+228',
            'phone_number' => '90 11 22 33',
            'interet' => 'comptant',
            'message' => 'Je suis très intéressé.',
        ], $overrides);
    }

    public function test_purchase_request_emails_the_client_and_the_company(): void
    {
        Mail::fake();

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->where('disponible', true)->orderBy('nom')->firstOrFail();

        $this->post(route('demande.achat.store', $vehicle), $this->payload())
            ->assertRedirect(route('demande.achat', $vehicle));

        // Confirmation au client, avec le contenu de l'e-mail généré.
        Mail::assertSent(PurchaseRequestConfirmation::class, function (PurchaseRequestConfirmation $mail) use ($vehicle) {
            return $mail->hasTo('client-mail@example.test')
                && str_contains($mail->render(), 'Demande reçue')
                && str_contains($mail->render(), $vehicle->nom);
        });

        // Notification à l'entreprise (adresse de contact par défaut).
        Mail::assertSent(PurchaseRequestNotification::class, function (PurchaseRequestNotification $mail) use ($vehicle) {
            return $mail->hasTo('contact@autotrade.fr')
                && str_contains($mail->render(), $vehicle->nom);
        });

        Mail::assertSent(PurchaseRequestNotification::class, function (PurchaseRequestNotification $mail) {
            return $mail->hasReplyTo('client-mail@example.test');
        });
    }

    public function test_company_notification_uses_the_dedicated_address_when_configured(): void
    {
        Mail::fake();
        Setting::put('notification_email', 'ventes@autotrade.fr');

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->firstOrFail();

        $this->post(route('demande.achat.store', $vehicle), $this->payload());

        Mail::assertSent(PurchaseRequestNotification::class, fn (PurchaseRequestNotification $mail) => $mail->hasTo('ventes@autotrade.fr'));
        Mail::assertNotSent(PurchaseRequestNotification::class, fn (PurchaseRequestNotification $mail) => $mail->hasTo('contact@autotrade.fr'));
    }

    public function test_purchase_request_is_saved_even_if_the_mail_transport_fails(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Transport SMTP indisponible'));

        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->firstOrFail();

        $this->post(route('demande.achat.store', $vehicle), $this->payload())
            ->assertRedirect(route('demande.achat', $vehicle));

        $this->assertDatabaseHas('purchase_requests', [
            'email' => 'client-mail@example.test',
            'statut' => 'en_attente',
        ]);
    }

    public function test_admin_settings_page_exposes_email_configuration(): void
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);
        $admin = User::where('email', 'admin@autotrade.fr')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/parametres')
            ->assertOk()
            ->assertSee('E-mails &amp; notifications', false)
            ->assertSee('notification_email')
            ->assertSee('mail_host')
            ->assertSee('mail_password');
    }
}
