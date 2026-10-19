<?php

namespace Tests\Feature;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageNotification;
use App\Mail\PurchaseRequestConfirmation;
use App\Mail\PurchaseRequestNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTestToolTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);

        return User::where('email', 'admin@autotrade.fr')->firstOrFail();
    }

    public function test_admin_can_send_the_four_test_emails(): void
    {
        Mail::fake();

        $response = $this->actingAs($this->admin())
            ->post(route('admin.settings.test-mail'), ['test_email' => 'destinataire@example.test']);

        $response->assertRedirect(route('admin.settings'));
        $response->assertSessionHas('success');

        // Les quatre e-mails du site partent bien vers l'adresse demandée.
        Mail::assertSent(PurchaseRequestConfirmation::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));
        Mail::assertSent(PurchaseRequestNotification::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));
        Mail::assertSent(ContactMessageConfirmation::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));
        Mail::assertSent(ContactMessageNotification::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));

        // Aucune ligne de test n'est créée en base.
        $this->assertDatabaseCount('purchase_requests', 0);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_test_email_address_is_validated(): void
    {
        Mail::fake();

        $this->actingAs($this->admin())
            ->post(route('admin.settings.test-mail'), ['test_email' => 'pas-un-email'])
            ->assertSessionHasErrors('test_email');

        Mail::assertNothingSent();
    }

    public function test_test_mail_route_requires_authentication(): void
    {
        $this->post(route('admin.settings.test-mail'), ['test_email' => 'destinataire@example.test'])
            ->assertRedirect(route('login'));
    }

    public function test_mail_test_command_sends_the_emails(): void
    {
        Mail::fake();

        $this->artisan('mail:test', ['email' => 'destinataire@example.test'])->assertSuccessful();

        Mail::assertSent(PurchaseRequestConfirmation::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));
        Mail::assertSent(ContactMessageNotification::class, fn ($mail) => $mail->hasTo('destinataire@example.test'));
    }

    public function test_mail_test_command_rejects_an_invalid_address(): void
    {
        Mail::fake();

        $this->artisan('mail:test', ['email' => 'pas-un-email'])->assertFailed();

        Mail::assertNothingSent();
    }
}
