<?php

namespace App\Http\Controllers;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

abstract class Controller
{
    /**
     * Envoie une série d'e-mails en isolant chaque envoi : une erreur SMTP
     * ne doit jamais interrompre la requête ni empêcher les autres envois.
     *
     * @param  array<int, array{0: string|null, 1: Mailable}>  $mails
     */
    protected function sendNotificationMails(array $mails): void
    {
        foreach ($mails as [$to, $mailable]) {
            if (empty($to)) {
                continue;
            }

            try {
                Mail::to($to)->send($mailable);
            } catch (Throwable $e) {
                report($e);
            }
        }
    }
}
