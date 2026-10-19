<?php

namespace App\Providers;

use App\Models\ContactMessage;
use App\Models\PurchaseRequest;
use App\Models\Setting;
use App\Models\Vehicle;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $site = Setting::map();
        } catch (Throwable $e) {
            // La table n'existe pas encore (migration, installation).
            $site = Setting::defaults();
        }

        View::share('site', $site);

        $this->applyMailSettings($site);

        // Badges et compteurs affichés dans la console d'administration.
        View::composer('layouts.admin', function ($view): void {
            try {
                $badges = [
                    'vehicles' => Vehicle::count(),
                    'pending_purchases' => PurchaseRequest::where('statut', 'en_attente')->count(),
                    'unread_messages' => ContactMessage::where('lu', false)->count(),
                    'latest_message_id' => (int) ContactMessage::max('id'),
                    'latest_purchase_id' => (int) PurchaseRequest::max('id'),
                ];
            } catch (Throwable $e) {
                $badges = [
                    'vehicles' => 0,
                    'pending_purchases' => 0,
                    'unread_messages' => 0,
                    'latest_message_id' => 0,
                    'latest_purchase_id' => 0,
                ];
            }

            $view->with('adminBadges', $badges);
        });
    }

    /**
     * Applique la configuration SMTP saisie dans l'administration.
     * Tant qu'aucun hôte n'est renseigné, la configuration du .env est conservée.
     */
    private function applyMailSettings(array $site): void
    {
        $host = trim((string) ($site['mail_host'] ?? ''));

        if ($host !== '') {
            $encryption = trim((string) ($site['mail_encryption'] ?? ''));
            $port = (int) ($site['mail_port'] ?: ($encryption === 'ssl' ? 465 : 587));

            config([
                'mail.default' => ($site['mail_mailer'] ?? 'log') === 'smtp' ? 'smtp' : 'log',
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port,
                'mail.mailers.smtp.username' => $site['mail_username'] ?: null,
                'mail.mailers.smtp.password' => $site['mail_password'] ?: null,
                // Laravel 11 : « smtps » pour le SSL implicite ; null laisse STARTTLS (port 587).
                'mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : null,
            ]);
        }

        $fromAddress = trim((string) ($site['mail_from_address'] ?? ''));

        if ($fromAddress !== '') {
            config(['mail.from.address' => $fromAddress]);
        }

        $fromName = trim((string) ($site['mail_from_name'] ?? ''));

        if ($fromName !== '') {
            config(['mail.from.name' => $fromName]);
        }
    }
}
