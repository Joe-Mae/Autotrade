<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminEmailCommandTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_admin_email_command_updates_the_email(): void
    {
        $this->artisan('admin:email', ['email' => 'nouveau@autotrade.fr'])
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'nouveau@autotrade.fr']);
        $this->assertDatabaseMissing('users', ['email' => 'admin@autotrade.fr']);
    }

    public function test_admin_email_command_rejects_an_invalid_address(): void
    {
        $this->artisan('admin:email', ['email' => 'pas-un-email'])
            ->assertExitCode(1);

        $this->assertDatabaseHas('users', ['email' => 'admin@autotrade.fr']);
    }

    public function test_admin_email_command_rejects_an_address_already_in_use(): void
    {
        User::query()->create([
            'name' => 'Autre compte',
            'email' => 'autre@autotrade.fr',
            'password' => 'MotDePasseAutre2026!',
        ]);

        $this->artisan('admin:email', ['email' => 'autre@autotrade.fr'])
            ->assertExitCode(1);

        $this->assertDatabaseHas('users', ['email' => 'admin@autotrade.fr']);
    }

    public function test_admin_password_command_still_finds_the_account_after_an_email_change(): void
    {
        $this->artisan('admin:email', ['email' => 'nouveau@autotrade.fr'])
            ->assertExitCode(0);

        $this->artisan('admin:password', ['password' => 'NouveauMotDePasse2026!'])
            ->assertExitCode(0);

        $user = User::firstOrFail();

        $this->assertSame('nouveau@autotrade.fr', $user->email);
        $this->assertTrue(Hash::check('NouveauMotDePasse2026!', $user->password));
    }
}
