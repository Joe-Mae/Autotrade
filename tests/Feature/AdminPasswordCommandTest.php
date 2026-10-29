<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_admin_password_command_updates_the_password(): void
    {
        $this->artisan('admin:password', ['password' => 'NouveauMotDePasse2026!'])
            ->assertExitCode(0);

        $this->assertTrue(
            Hash::check('NouveauMotDePasse2026!', User::firstOrFail()->password)
        );
    }

    public function test_admin_password_command_rejects_a_short_password(): void
    {
        $this->artisan('admin:password', ['password' => 'court'])
            ->assertExitCode(1);

        $this->assertTrue(
            Hash::check('admin123', User::firstOrFail()->password)
        );
    }

    public function test_admin_password_command_rejects_an_unknown_account(): void
    {
        $this->artisan('admin:password', [
            'password' => 'NouveauMotDePasse2026!',
            '--email' => 'inconnu@example.test',
        ])->assertExitCode(1);
    }
}
