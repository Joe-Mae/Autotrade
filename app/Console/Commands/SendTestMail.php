<?php

namespace App\Console\Commands;

use App\Support\MailSamples;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTestMail extends Command
{
    protected $signature = 'mail:test {email : Adresse destinataire des e-mails de test}';

    protected $description = "Envoie les quatre e-mails du site (confirmation client + notification entreprise) pour vérifier la configuration d'envoi.";

    public function handle(): int
    {
        $email = trim((string) $this->argument('email'));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error("Adresse e-mail invalide : {$email}");

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('  Transport    : <info>'.config('mail.default').'</info>');
        $this->line('  Expéditeur   : <info>'.config('mail.from.address').'</info>');
        $this->line('  Destinataire : <info>'.$email.'</info>');
        $this->newLine();

        $mails = MailSamples::mails($email);
        $failures = 0;

        foreach ($mails as $label => $mailable) {
            try {
                Mail::to($email)->send($mailable);
                $this->line('  <fg=green>✓</> '.$label);
            } catch (Throwable $e) {
                $failures++;
                $this->line('  <fg=red>✗</> '.$label);
                $this->line('      <fg=red>'.$e->getMessage().'</>');
            }
        }

        $this->newLine();

        if ($failures > 0) {
            $this->error($failures.' envoi(s) sur '.count($mails).' ont échoué. Vérifiez les paramètres SMTP.');

            return self::FAILURE;
        }

        $this->info(count($mails).' e-mail(s) envoyé(s). Vérifiez la boîte de réception (et les indésirables).');

        return self::SUCCESS;
    }
}
