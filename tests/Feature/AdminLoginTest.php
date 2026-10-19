<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        $this->seed(\Database\Seeders\AdminSeeder::class);

        return User::where('email', 'admin@autotrade.fr')->firstOrFail();
    }

    public function test_login_page_offers_a_password_visibility_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('name="password"', false)
            ->assertSee('data-password-toggle="admin-password"', false)
            ->assertSee('password-toggle__eye', false)
            ->assertSee('Afficher le mot de passe', false);
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $this->admin();

        $this->post(route('login.submit'), [
            'email' => 'admin@autotrade.fr',
            'password' => 'admin123',
        ])->assertRedirect(route('admin'));

        $this->assertAuthenticated();
    }

    public function test_login_is_refused_with_a_wrong_password(): void
    {
        $this->admin();

        $this->from(route('login'))
            ->post(route('login.submit'), [
                'email' => 'admin@autotrade.fr',
                'password' => 'mauvais-mot-de-passe',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
