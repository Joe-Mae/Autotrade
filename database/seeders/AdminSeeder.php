<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Crée le compte administrateur lors de la première installation.
     *
     * Le seeder ne fait rien si un compte existe déjà : il ne peut donc ni
     * réécrire un mot de passe, ni recréer un ancien compte après un changement
     * d'adresse e-mail. Les identifiants initiaux viennent de ADMIN_EMAIL /
     * ADMIN_PASSWORD ; ensuite, utilisez les commandes admin:password et
     * admin:email.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $email = (string) config('admin.email', 'admin@autotrade.fr');
        $password = (string) config('admin.password', '');

        if ($password === '') {
            // Repli réservé au développement et aux tests : jamais utilisé en production,
            // où ADMIN_PASSWORD est défini dans le fichier .env.
            $password = 'admin123';
        }

        User::query()->create([
            'name' => 'Administrateur',
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ]);
    }
}
