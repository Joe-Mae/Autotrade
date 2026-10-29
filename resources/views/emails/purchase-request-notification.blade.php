@extends('emails.layout')

@php
    $accent = \App\Support\Theme::accent($site['color_primary'] ?? null);
    $onAccent = \App\Support\Theme::contrastText($site['color_primary'] ?? null);
@endphp

@section('subject', 'Nouvelle demande d\'achat')

@section('content')
    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:{{ $accent }};">Nouvelle demande d'achat</p>
    <h1 style="margin:0 0 18px;font-size:24px;line-height:1.2;font-weight:800;color:#f5f5f0;">
        {{ $vehicle?->nom ?? 'Véhicule supprimé' }}
    </h1>

    <p style="margin:0 0 20px;font-size:14px;line-height:1.7;color:#c9c9c2;">
        Un prospect vient de soumettre une demande d'achat depuis le site.
        Répondez directement à cet e-mail pour lui écrire.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;border:1px solid #262626;background:#161616;">
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Référence</span><br>
                <span style="font-size:14px;font-weight:700;color:#f5f5f0;">VTE-{{ str_pad((string) $purchase->id, 4, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Client</span><br>
                <span style="font-size:15px;font-weight:700;color:#f5f5f0;">{{ $purchase->nom }}</span>
                <span style="display:block;margin-top:4px;font-size:13px;color:#c9c9c2;">
                    <a href="mailto:{{ $purchase->email }}" style="color:{{ $accent }};text-decoration:none;">{{ $purchase->email }}</a>
                    · {{ $purchase->telephone }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Projet</span><br>
                <span style="font-size:14px;color:#f5f5f0;">{{ $purchase->interet ?: 'Non précisé' }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Véhicule</span><br>
                <span style="font-size:15px;font-weight:700;color:#f5f5f0;">{{ $vehicle?->nom ?? '—' }}</span>
                @if($vehicle)
                    <span style="display:block;margin-top:4px;font-size:12px;color:#8a8a82;">
                        {{ $vehicle->marque }} · {{ $vehicle->annee }} · {{ $vehicle->categorie }} · {{ $vehicle->etat }}
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:14px 18px;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Prix affiché</span><br>
                <span style="font-size:15px;font-weight:800;color:{{ $accent }};">
                    {{ $vehicle && $vehicle->prix_vente !== null ? \App\Models\Vehicle::cfa($vehicle->prix_vente) : 'Sur devis' }}
                </span>
            </td>
        </tr>
        @if($purchase->message)
            <tr>
                <td style="padding:14px 18px;border-top:1px solid #262626;">
                    <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Message</span><br>
                    <span style="font-size:13px;line-height:1.7;color:#c9c9c2;">« {{ $purchase->message }} »</span>
                </td>
            </tr>
        @endif
    </table>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 6px;">
        <tr>
            <td style="background:{{ $accent }};">
                <a href="{{ url(route('admin', [], false)) }}" style="display:inline-block;padding:14px 26px;font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:{{ $onAccent }};text-decoration:none;">
                    Traiter la demande
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:14px 0 0;font-size:12px;line-height:1.7;color:#8a8a82;">
        Reçue le {{ $purchase->created_at?->format('d/m/Y à H:i') }}.
        Ouvrez la rubrique « Ventes » de la console pour passer la demande en « Signée » et marquer le véhicule comme vendu.
    </p>
@endsection
