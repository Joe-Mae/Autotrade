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
        $data = $request->validate([
            'nom' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'telephone' => 'required|string|max:40',
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
