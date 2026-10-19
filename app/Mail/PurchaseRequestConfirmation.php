<?php

namespace App\Mail;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail envoyé au client : confirmation de réception de sa demande d'achat.
 */
class PurchaseRequestConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PurchaseRequest $purchaseRequest)
    {
        $this->purchaseRequest->loadMissing('vehicle');
    }

    public function envelope(): Envelope
    {
        $vehicle = $this->purchaseRequest->vehicle?->nom;

        return new Envelope(
            subject: 'Votre demande d\'achat a bien été reçue'.($vehicle ? ' — '.$vehicle : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-request-confirmation',
            with: [
                'purchase' => $this->purchaseRequest,
                'vehicle' => $this->purchaseRequest->vehicle,
            ],
        );
    }
}
