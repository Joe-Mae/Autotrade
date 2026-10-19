@extends('emails.layout')

@section('subject', 'Votre message a bien été reçu')

@section('content')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#cfff04;">Message reçu</p>
    <h1 style="margin:0 0 18px;font-size:26px;line-height:1.2;font-weight:800;color:#f5f5f0;">
        Merci {{ $contact->nom }}.
    </h1>

    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;color:#c9c9c2;">
        Nous avons bien reçu votre message. Notre équipe {{ $site['site_name'] ?? 'AUTOTRADE' }}
        <strong style="color:#f5f5f0;">vous répond sous 2 heures ouvrées</strong>.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0;border:1px solid #262626;background:#161616;">
        <tr>
            <td style="padding:16px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Référence</span><br>
                <span style="font-size:14px;font-weight:700;color:#f5f5f0;">MSG-{{ str_pad((string) $contact->id, 4, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Votre message</span><br>
                <span style="font-size:13px;line-height:1.7;color:#c9c9c2;">« {{ $contact->message }} »</span>
            </td>
        </tr>
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;">
        <tr>
            <td style="background:#cfff04;">
                <a href="{{ url(route('catalogue', [], false)) }}" style="display:inline-block;padding:14px 26px;font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#0a0a0a;text-decoration:none;">
                    Parcourir le catalogue
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;line-height:1.7;color:#8a8a82;">
        Une urgence, une précision à apporter ? Répondez simplement à cet e-mail ou appelez-nous{{ !empty($site['contact_phone']) ? ' au '.$site['contact_phone'] : '' }}.
    </p>
@endsection
