<?php

namespace App\Support;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageNotification;
use App\Mail\PurchaseRequestConfirmation;
use App\Mail\PurchaseRequestNotification;
use App\Models\ContactMessage;
use App\Models\PurchaseRequest;
use App\Models\Vehicle;

/**
 * Jeux de données de démonstration pour tester la configuration d'envoi
 * sans créer de véritable demande ni de message en base.
 */
class MailSamples
{
    /**
     * Les quatre e-mails du site, indexés par libellé.
     *
     * @return array<string, \Illuminate\Mail\Mailable>
     */
    public static function mails(string $email): array
    {
        $purchase = self::purchase($email);
        $contact = self::contact($email);

        return [
            "Confirmation client — demande d'achat" => new PurchaseRequestConfirmation($purchase),
            "Notification entreprise — demande d'achat" => new PurchaseRequestNotification($purchase),
            'Confirmation visiteur — message de contact' => new ContactMessageConfirmation($contact),
            'Notification entreprise — message de contact' => new ContactMessageNotification($contact),
        ];
    }

    /**
     * Demande d'achat d'exemple : un véhicule réel est utilisé si possible.
     */
    public static function purchase(string $email): PurchaseRequest
    {
        $vehicle = Vehicle::query()->whereNotNull('prix_vente')->orderBy('nom')->first();

        if (! $vehicle) {
            $vehicle = new Vehicle([
                'nom' => 'ZENITH PURE X',
                'slug' => 'zenith-pure-x',
                'marque' => 'ZENITH',
                'annee' => now()->year,
                'categorie' => 'Super Sport',
                'etat' => 'Neuf',
                'prix_vente' => 24500000,
            ]);
        }

        $purchase = new PurchaseRequest([
            'nom' => 'Awa Diop',
            'email' => $email,
            'telephone' => '+228 90 12 34 56',
            'interet' => 'Financement',
            'message' => 'Bonjour, je souhaite organiser un essai ce week-end si possible.',
            'statut' => 'en_attente',
        ]);

        // Valeurs de démonstration pour la référence et la date affichées dans l'e-mail.
        $purchase->id = 9999;
        $purchase->created_at = now();
        $purchase->setRelation('vehicle', $vehicle);

        return $purchase;
    }

    /**
     * Message de contact d'exemple.
     */
    public static function contact(string $email): ContactMessage
    {
        $contact = new ContactMessage([
            'nom' => 'Awa Diop',
            'email' => $email,
            'telephone' => '+228 90 12 34 56',
            'message' => 'Bonjour, je cherche un SUV hybride récent. Pouvez-vous me rappeler cette semaine ?',
        ]);

        $contact->id = 9999;
        $contact->created_at = now();

        return $contact;
    }
}
