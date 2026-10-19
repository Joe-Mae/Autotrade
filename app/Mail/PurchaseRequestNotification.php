<?php

namespace App\Mail;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail envoyé à l'entreprise : notification d'une nouvelle demande d'achat.
 */
class PurchaseRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest->loadMissing('vehicle');
    }

    public function envelope(): Envelope
    {
        $vehicle = $this->purchaseRequest->vehicle?->nom ?? 'Véhicule supprimé';

        return new Envelope(
            subject: 'Nouvelle demande d\'achat — '.$vehicle.' ('.$this->purchaseRequest->nom.')',
            // Répondre à cet e-mail écrit directement au client.
            replyTo: [
                new Address($this->purchaseRequest->email, $this->purchaseRequest->nom),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-request-notification',
            with: [
                'purchase' => $this->purchaseRequest,
                'vehicle' => $this->purchaseRequest->vehicle,
            ],
        );
    }
}
