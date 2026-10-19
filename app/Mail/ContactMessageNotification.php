<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * E-mail envoyé à l'entreprise : notification d'un nouveau message de contact.
 */
class ContactMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message du site — '.$this->contactMessage->nom,
            // Répondre à cet e-mail écrit directement au visiteur.
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->nom),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message-notification',
            with: [
                'contact' => $this->contactMessage,
            ],
        );
    }
}
