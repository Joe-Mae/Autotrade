@php
    $siteName = $site['site_name'] ?? 'AUTOTRADE';
    $siteTagline = $site['site_tagline'] ?? '';
    $contactEmail = $site['contact_email'] ?? '';
    $contactPhone = $site['contact_phone'] ?? '';
    $contactAddress = $site['contact_address'] ?? '';
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>@yield('subject', $siteName)</title>
</head>
<body style="margin:0;padding:0;background:#0a0a0a;color:#f5f5f0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;-webkit-font-smoothing:antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#0a0a0a;padding:28px 14px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;background:#111111;border:1px solid #262626;">
                    {{-- En-tête --}}
                    <tr>
                        <td style="padding:24px 30px;border-bottom:2px solid #cfff04;">
                            <span style="display:inline-block;font-size:20px;font-weight:800;letter-spacing:1px;color:#f5f5f0;">{{ $siteName }}</span>
                            @if($siteTagline !== '')
                                <span style="display:block;margin-top:6px;font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#8a8a82;">{{ $siteTagline }}</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Corps --}}
                    <tr>
                        <td style="padding:30px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Pied de page --}}
                    <tr>
                        <td style="padding:22px 30px;border-top:1px solid #262626;font-size:11px;line-height:1.7;color:#8a8a82;">
                            @if($contactAddress !== ''){{ $contactAddress }}<br>@endif
                            @if($contactPhone !== ''){{ $contactPhone }}@endif
                            @if($contactPhone !== '' && $contactEmail !== '') · @endif
                            @if($contactEmail !== ''){{ $contactEmail }}@endif
                        </td>
                    </tr>
                </table>

                <p style="margin:16px 0 0;font-size:10px;letter-spacing:2px;text-transform:uppercase;color:#5c5c57;">
                    {{ $siteName }} — Précision. Performance. Contrôle.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>
