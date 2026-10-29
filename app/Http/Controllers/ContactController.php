<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageNotification;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function store(Request $request)
    {
        // Champ piège invisible : seuls les robots le remplissent.
        // On simule un succès sans rien enregistrer ni envoyer.
        if ($request->filled('site_web')) {
            return redirect()->route('contact')
                ->with('success', 'Votre message a bien été envoyé. Nous vous répondons sous 24 heures.');
        }

        $data = $request->validate([
            'nom' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'telephone' => 'nullable|string|max:40',
            'message' => 'required|string|max:3000',
        ]);

        $message = ContactMessage::create($data);

        // Confirmation au visiteur, puis alerte à l'entreprise.
        $this->sendNotificationMails([
            [$message->email, new ContactMessageConfirmation($message)],
            [Setting::notificationEmail(), new ContactMessageNotification($message)],
        ]);

        return redirect()->route('contact')
            ->with('success', 'Message enregistré. Notre équipe vous répond sous 2 heures ouvrées.')
            ->with('popup', [
                'title' => 'Message envoyé',
                'message' => 'Votre message a bien été pris en compte. Notre équipe vous répond sous 2 heures ouvrées.',
            ]);
    }
}
