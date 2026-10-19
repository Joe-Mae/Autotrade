<?php

namespace Tests\Feature;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageNotification;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class ContactEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nom' => 'Visiteur Mail',
            'email' => 'visiteur-mail@example.test',
            'telephone' => '+228 90 44 55 66',
            'message' => 'Bonjour, je cherche un SUV hybride.',
        ], $overrides);
    }

    public function test_contact_message_emails_the_visitor_and_the_company(): void
    {
        Mail::fake();

        $this->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('contact'));

        // Confirmation au visiteur, avec le contenu de l'e-mail généré.
        Mail::assertSent(ContactMessageConfirmation::class, function (ContactMessageConfirmation $mail) {
            return $mail->hasTo('visiteur-mail@example.test')
                && str_contains($mail->render(), 'Message reçu')
                && str_contains($mail->render(), 'SUV hybride');
        });

        // Notification à l'entreprise (adresse de contact par défaut).
        Mail::assertSent(ContactMessageNotification::class, function (ContactMessageNotification $mail) {
            return $mail->hasTo('contact@autotrade.fr')
                && $mail->hasReplyTo('visiteur-mail@example.test')
                && str_contains($mail->render(), 'Visiteur Mail');
        });
    }

    public function test_contact_notification_uses_the_dedicated_address_when_configured(): void
    {
        Mail::fake();
        Setting::put('notification_email', 'messages@autotrade.fr');

        $this->post(route('contact.store'), $this->payload());

        Mail::assertSent(ContactMessageNotification::class, fn (ContactMessageNotification $mail) => $mail->hasTo('messages@autotrade.fr'));
        Mail::assertNotSent(ContactMessageNotification::class, fn (ContactMessageNotification $mail) => $mail->hasTo('contact@autotrade.fr'));
    }

    public function test_contact_message_is_saved_even_if_the_mail_transport_fails(): void
    {
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Transport SMTP indisponible'));

        $this->post(route('contact.store'), $this->payload())
            ->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'visiteur-mail@example.test',
        ]);
    }
}
