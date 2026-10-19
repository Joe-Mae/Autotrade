<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@autotrade.fr'],
            [
                'name' => 'Administrateur',
                'password' => 'admin123',
                'email_verified_at' => now(),
            ]
        );

        User::query()->each(function (User $user): void {
            $raw = $user->getRawOriginal('password');

            if (! Hash::isHashed((string) $raw)) {
                $user->password = $raw;
                $user->save();
            }
        });
    }
}
