<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminClientsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);

        return User::where('email', 'admin@autotrade.fr')->firstOrFail();
    }

    public function test_a_client_message_opens_in_a_detailed_modal(): void
    {
        ContactMessage::create([
            'nom' => 'Jeanne Test',
            'email' => 'jeanne@example.test',
            'telephone' => '+228 90 00 00 00',
            'message' => "Première ligne du message.\nDeuxième ligne, avec plus de détails sur la demande du client.",
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin'))
            ->assertOk()
            // La cellule message est cliquable…
            ->assertSee('data-message-open', false)
            ->assertSee('Agrandir')
            // …et la fenêtre de lecture détaillée est présente.
            ->assertSee('clientMessageModal', false)
            ->assertSee('data-message-full', false)
            ->assertSee('clientMessageBody', false)
            ->assertSee('clientMessageReadForm', false)
            ->assertSee('RÉPONDRE PAR E-MAIL')
            ->assertSee('Jeanne Test');
    }

    public function test_the_full_message_is_available_for_the_modal(): void
    {
        $long = 'Un message nettement plus long que quatre-vingt-dix caractères, afin de vérifier que la version complète est bien présente dans la page.';

        ContactMessage::create([
            'nom' => 'Paul Test',
            'email' => 'paul@example.test',
            'telephone' => '+228 91 11 11 11',
            'message' => $long,
        ]);

        $this->actingAs($this->admin())
            ->get(route('admin'))
            ->assertOk()
            ->assertSee($long);
    }
}
