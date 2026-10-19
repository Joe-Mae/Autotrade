@extends('emails.layout')

@section('subject', 'Votre demande d\'achat a bien été reçue')

@section('content')
    @php
        $purchaseUrl = $vehicle ? url(route('vehicule.show', $vehicle, false)) : null;
    @endphp

    <p style="margin:0 0 6px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#cfff04;">Demande reçue</p>
    <h1 style="margin:0 0 18px;font-size:26px;line-height:1.2;font-weight:800;letter-spacing:-0.5px;color:#f5f5f0;">
        Merci {{ $purchase->nom }}.
    </h1>

    <p style="margin:0 0 14px;font-size:14px;line-height:1.7;color:#c9c9c2;">
        Nous avons bien reçu votre demande d'achat. Un conseiller {{ $site['site_name'] ?? 'AUTOTRADE' }}
        <strong style="color:#f5f5f0;">vous recontacte sous 24 heures</strong> pour convenir des prochaines étapes.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:22px 0;border:1px solid #262626;background:#161616;">
        <tr>
            <td style="padding:16px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Référence</span><br>
                <span style="font-size:14px;font-weight:700;color:#f5f5f0;">VTE-{{ str_pad((string) $purchase->id, 4, '0', STR_PAD_LEFT) }}</span>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Véhicule</span><br>
                <span style="font-size:16px;font-weight:800;color:#f5f5f0;">{{ $vehicle?->nom ?? 'Véhicule' }}</span>
                @if($vehicle)
                    <span style="display:block;margin-top:4px;font-size:12px;color:#8a8a82;">
                        {{ $vehicle->marque }} · {{ $vehicle->annee }} · {{ $vehicle->categorie }} · {{ $vehicle->etat }}
                    </span>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;border-bottom:1px solid #262626;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Prix de vente</span><br>
                <span style="font-size:16px;font-weight:800;color:#cfff04;">
                    {{ $vehicle && $vehicle->prix_vente !== null ? \App\Models\Vehicle::cfa($vehicle->prix_vente) : 'Sur devis' }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding:16px 18px;">
                <span style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">Votre projet</span><br>
                <span style="font-size:14px;color:#f5f5f0;">{{ $purchase->interet ?: 'À définir avec un conseiller' }}</span>
                @if($purchase->message)
                    <span style="display:block;margin-top:10px;font-size:13px;line-height:1.6;color:#c9c9c2;">« {{ $purchase->message }} »</span>
                @endif
            </td>
        </tr>
    </table>

    @if($purchaseUrl)
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;">
            <tr>
                <td style="background:#cfff04;">
                    <a href="{{ $purchaseUrl }}" style="display:inline-block;padding:14px 26px;font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#0a0a0a;text-decoration:none;">
                        Revoir le véhicule
                    </a>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:0;font-size:13px;line-height:1.7;color:#8a8a82;">
        Un empêchement, une question ? Répondez simplement à cet e-mail ou appelez-nous{{ !empty($site['contact_phone']) ? ' au '.$site['contact_phone'] : '' }}.
    </p>
@endsection
