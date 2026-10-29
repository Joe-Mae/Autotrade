<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminPassword extends Command
{
    protected $signature = 'admin:password
                            {password? : Nouveau mot de passe (saisie masquée si omis)}
                            {--email= : Compte à modifier (par défaut ADMIN_EMAIL)}';

    protected $description = "Modifie le mot de passe du compte administrateur, sans passer par l'interface web.";

    public function handle(): int
    {
        $user = $this->resolveUser();

        if (! $user) {
            $this->error('Aucun compte administrateur trouvé. Utilisez --email=... pour préciser le compte.');

            return self::FAILURE;
        }

        $password = $this->argument('password');

        if ($password === null) {
            $password = (string) $this->secret('Nouveau mot de passe');
            $confirmation = (string) $this->secret('Confirmer le nouveau mot de passe');

            if ($password !== $confirmation) {
                $this->error('Les deux saisies ne correspondent pas. Aucune modification.');

                return self::FAILURE;
            }
        }

        if (strlen((string) $password) < 12) {
            $this->error('Le mot de passe doit contenir au moins 12 caractères. Aucune modification.');

            return self::FAILURE;
        }

        // Le cast « hashed » du modèle se charge du hachage.
        $user->password = (string) $password;

        // Invalide les éventuels cookies « se souvenir de moi » existants.
        $user->setRememberToken(Str::random(60));
        $user->save();

        $this->info("Mot de passe mis à jour pour {$user->email}.");
        $this->line('  Déconnectez-vous puis reconnectez-vous avec le nouveau mot de passe.');

        return self::SUCCESS;
    }

    /**
     * Compte visé : --email s'il est fourni, sinon ADMIN_EMAIL,
     * sinon le premier compte (le site ne gère qu'un administrateur).
     */
    private function resolveUser(): ?User
    {
        $option = trim((string) $this->option('email'));

        if ($option !== '') {
            return User::query()->where('email', $option)->first();
        }

        $configured = trim((string) config('admin.email', 'admin@autotrade.fr'));

        return User::query()->where('email', $configured)->first()
            ?: User::query()->orderBy('id')->first();
    }
}
