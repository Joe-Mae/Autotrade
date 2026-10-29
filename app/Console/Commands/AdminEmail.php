<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminEmail extends Command
{
    protected $signature = 'admin:email
                            {email : Nouvel e-mail du compte administrateur}
                            {--current= : E-mail actuel (sinon ADMIN_EMAIL, sinon le premier compte)}';

    protected $description = "Modifie l'adresse e-mail du compte administrateur, sans passer par l'interface web.";

    public function handle(): int
    {
        $user = $this->resolveUser();

        if (! $user) {
            $this->error('Aucun compte administrateur trouvé. Utilisez --current=... pour préciser le compte.');

            return self::FAILURE;
        }

        $newEmail = Str::lower(trim((string) $this->argument('email')));

        if (filter_var($newEmail, FILTER_VALIDATE_EMAIL) === false) {
            $this->error("Adresse e-mail invalide : {$newEmail}. Aucune modification.");

            return self::FAILURE;
        }

        $alreadyUsed = User::query()
            ->where('email', $newEmail)
            ->whereKeyNot($user->getKey())
            ->exists();

        if ($alreadyUsed) {
            $this->error("L'adresse {$newEmail} est déjà utilisée par un autre compte. Aucune modification.");

            return self::FAILURE;
        }

        $oldEmail = $user->email;

        $user->email = $newEmail;
        $user->email_verified_at ??= now();
        $user->save();

        $this->info("E-mail mis à jour : {$oldEmail} → {$newEmail}.");
        $this->line('  Connectez-vous désormais avec cette nouvelle adresse.');
        $this->line('  Pensez à mettre à jour ADMIN_EMAIL dans le .env (facultatif).');

        return self::SUCCESS;
    }

    /**
     * Compte visé : --current s'il est fourni, sinon ADMIN_EMAIL,
     * sinon le premier compte (le site ne gère qu'un administrateur).
     */
    private function resolveUser(): ?User
    {
        $option = trim((string) $this->option('current'));

        if ($option !== '') {
            return User::query()->where('email', $option)->first();
        }

        $configured = trim((string) config('admin.email', 'admin@autotrade.fr'));

        return User::query()->where('email', $configured)->first()
            ?: User::query()->orderBy('id')->first();
    }
}
