@extends('emails.layout')

@section('subject', 'Nouveau message du site')

@section('content')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#cfff04;">Nouveau message</p>
    <h1 style="margin:0 0 18px;font-size:24px;line-height:1.2;font-weight:800;color:#f5f5f0;">
        {{ $contact->nom }}
    </h1>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#c9c9c2;">
        Un visiteur vient d'envoyer un message depuis la page contact du site.
        Répondez directement à cet e-mail pour lui écrire.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;border:1px solid #262626;background:#161616;">
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Référence</span><br>
                <span style="font-size:14px;font-weight:700;color:#f5f5f0;">MSG-{{ str_pad((string) $contact->id, 4, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Contact</span><br>
                <span style="font-size:15px;font-weight:700;color:#f5f5f0;">{{ $contact->nom }}</span>
                <span style="display:block;margin-top:4px;font-size:13px;color:#c9c9c2;">
                    <a href="mailto:{{ $contact->email }}" style="color:#cfff04;text-decoration:none;">{{ $contact->email }}</a>
                    · {{ $contact->telephone }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Message</span><br>
                <span style="font-size:13px;line-height:1.7;color:#c9c9c2;">« {{ $contact->message }} »</span>
            </td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 6px;">
        <tr>
            <td style="background:#cfff04;">
                <a href="{{ url(route('admin', [], false)).'#clients' }}" style="display:inline-block;padding:14px 26px;font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#0a0a0a;text-decoration:none;">
                    Ouvrir la rubrique Clients
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:14px 0 0;font-size:12px;line-height:1.7;color:#8a8a82;">
        Reçu le {{ $contact->created_at?->format('d/m/Y à H:i') }}.
        Retrouvez tous les messages dans la console, rubrique « Clients ».
    </p>
@endsection
