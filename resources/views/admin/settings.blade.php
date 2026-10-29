@extends('layouts.admin')

@section('title', 'Paramètres du site — ' . $site['site_name'])
@section('admin_section', 'gestion')

@section('content')
<div>
    <div class="admin-head">
        <div>
            <p class="section-label mb-0">Administration</p>
            <h1 class="admin-title">PARAMÈTRES <span class="text-volt">DU SITE</span></h1>
            <p class="admin-subtitle">Modifiez les informations et les images affichées sur le site public.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-0 mt-4 mb-0">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-4">
        @csrf

        {{-- Identité --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-4">Identité</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Nom du site</label>
                    <input name="site_name" value="{{ old('site_name', $site['site_name']) }}" required class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Slogan / baseline</label>
                    <input name="site_tagline" value="{{ old('site_tagline', $site['site_tagline']) }}" required class="form-control form-control-dark">
                </div>
                <div class="col-12">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Logo (optionnel)</label>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @if(!empty($site['logo']))
                            <img src="{{ asset($site['logo']) }}" alt="Logo" style="height:48px;width:auto;background:rgba(255,255,255,0.06);padding:6px">
                            <label class="d-flex align-items-center gap-2 small text-muted-custom mb-0">
                                <input type="checkbox" name="remove_logo" value="1"> Supprimer le logo
                            </label>
                        @else
                            <span class="text-muted-custom small">Aucun logo — le monogramme « A » est utilisé.</span>
                        @endif
                    </div>
                    <input type="file" name="logo" accept="image/*" class="form-control form-control-dark mt-2">
                </div>
            </div>
        </div>

        {{-- Palette --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-1">Palette du site</h2>
            <p class="text-muted-custom small mb-0">
                Trois couleurs pilotent l'ensemble du site public, de l'administration et des e-mails.
                L'aperçu ci-dessous se met à jour pendant la saisie ; le site applique les nouvelles
                couleurs dès l'enregistrement.
            </p>
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label for="color_primary" class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Couleur principale</label>
                    <input type="color" id="color_primary" name="color_primary" value="{{ old('color_primary', $site['color_primary']) }}" class="form-control form-control-color form-control-dark w-100" style="height:48px">
                    <p class="text-muted-custom small mb-0 mt-2">Boutons principaux, titres et libellés accentués.</p>
                </div>
                <div class="col-md-4">
                    <label for="color_secondary" class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Couleur secondaire</label>
                    <input type="color" id="color_secondary" name="color_secondary" value="{{ old('color_secondary', $site['color_secondary']) }}" class="form-control form-control-color form-control-dark w-100" style="height:48px">
                    <p class="text-muted-custom small mb-0 mt-2">Contours des boutons, survols et halos.</p>
                </div>
                <div class="col-md-4">
                    <label for="color_dark" class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Fond sombre</label>
                    <input type="color" id="color_dark" name="color_dark" value="{{ old('color_dark', $site['color_dark']) }}" class="form-control form-control-color form-control-dark w-100" style="height:48px">
                    <p class="text-muted-custom small mb-0 mt-2">Arrière-plan du mode sombre et des panneaux.</p>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 mt-4 align-items-center" id="palettePreview">
                <span class="small text-uppercase fw-bold px-3 py-2" id="palettePreviewPrimary" style="font-size:11px;letter-spacing:0.12em;">Bouton principal</span>
                <span class="small text-uppercase fw-bold px-3 py-2" id="palettePreviewSecondary" style="font-size:11px;letter-spacing:0.12em;">Bouton secondaire</span>
                <span class="small fw-bold" id="palettePreviewText" style="letter-spacing:0.12em;">Titre accentué</span>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn-volt border-0" data-palette="#cfff04,#536900,#0a0a0a">Palette d'origine</button>
                <button type="button" class="btn-volt border-0" data-palette="#ffd600,#8a2be2,#000000">Jaune &amp; violet</button>
            </div>
        </div>

        {{-- Hero --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-4">Accueil — bloc principal</h2>
            <div class="row g-3">
                <div class="col-12">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Libellé (au-dessus du titre)</label>
                    <input name="hero_badge" value="{{ old('hero_badge', $site['hero_badge']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Titre — ligne 1</label>
                    <input name="hero_title_1" value="{{ old('hero_title_1', $site['hero_title_1']) }}" required class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Titre — ligne 2 (contour)</label>
                    <input name="hero_title_2" value="{{ old('hero_title_2', $site['hero_title_2']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-12">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Texte de présentation</label>
                    <textarea name="hero_text" rows="3" class="form-control form-control-dark">{{ old('hero_text', $site['hero_text']) }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Bouton principal (libellé)</label>
                    <input name="hero_cta_primary" value="{{ old('hero_cta_primary', $site['hero_cta_primary']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Bouton secondaire (libellé)</label>
                    <input name="hero_cta_secondary" value="{{ old('hero_cta_secondary', $site['hero_cta_secondary']) }}" class="form-control form-control-dark">
                </div>
            </div>
        </div>

        {{-- Diaporama --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-1">Diaporama d'accueil</h2>
            <p class="text-muted-custom small mb-0">4 photos affichées à droite du titre. Cochez « retirer » pour masquer une diapositive. Les images à remplacer : uploadez un nouveau fichier.</p>
            <div class="row g-4 mt-2">
                @for($i = 1; $i <= 4; $i++)
                    @php
                        $imageKey = "hero_slide_{$i}_image";
                        $captionKey = "hero_slide_{$i}_caption";
                        $urlKey = "hero_slide_{$i}_url";
                        $removeKey = "remove_{$imageKey}";
                    @endphp
                    <div class="col-md-6">
                        <div class="border border-custom p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-uppercase text-muted-custom fw-bold" style="font-size:10px;letter-spacing:0.1em">Photo {{ $i }}</span>
                                @if(!empty($site[$imageKey]))
                                    <label class="d-flex align-items-center gap-2 small text-muted-custom mb-0">
                                        <input type="checkbox" name="{{ $removeKey }}" value="1"> Retirer
                                    </label>
                                @endif
                            </div>
                            <img src="{{ asset($site[$imageKey] ?: 'assets/hero-detail.jpg') }}"
                                 alt="Photo {{ $i }}" class="w-100 mb-2" style="aspect-ratio:16/9;object-fit:cover">
                            <input type="file" name="{{ $imageKey }}" accept="image/*" class="form-control form-control-dark mb-2">
                            <input name="{{ $captionKey }}" value="{{ old($captionKey, $site[$captionKey]) }}" class="form-control form-control-dark mb-2" placeholder="Légende (ex: ZENITH PURE X)">
                            <input name="{{ $urlKey }}" value="{{ old($urlKey, $site[$urlKey]) }}" class="form-control form-control-dark" placeholder="Lien (ex: /vehicule/zenith-pure-x)">
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Contact --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-4">Coordonnées</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Adresse</label>
                    <input name="contact_address" value="{{ old('contact_address', $site['contact_address']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Téléphone</label>
                    <input name="contact_phone" value="{{ old('contact_phone', $site['contact_phone']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">E-mail</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $site['contact_email']) }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Horaires</label>
                    <input name="contact_hours" value="{{ old('contact_hours', $site['contact_hours']) }}" class="form-control form-control-dark">
                </div>
            </div>
        </div>

        {{-- E-mails & notifications --}}
        <div class="border border-custom bg-panel p-4 p-md-5 mb-4">
            <h2 class="font-display fs-3 mb-1">E-mails &amp; notifications</h2>
            <p class="text-muted-custom small mb-4">
                À chaque demande d'achat, le client reçoit un e-mail de confirmation et l'entreprise est notifiée.
                Les liens des e-mails utilisent l'adresse <code>APP_URL</code> du site.
            </p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">E-mail de réception (entreprise)</label>
                    <input type="email" name="notification_email" value="{{ old('notification_email', $site['notification_email']) }}" placeholder="{{ $site['contact_email'] }}" class="form-control form-control-dark">
                    <span class="text-muted-custom d-block mt-2" style="font-size:10px">Laissez vide pour utiliser l'adresse de contact ({{ $site['contact_email'] }}).</span>
                </div>
                <div class="col-md-3">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Expéditeur — nom</label>
                    <input name="mail_from_name" value="{{ old('mail_from_name', $site['mail_from_name']) }}" placeholder="{{ $site['site_name'] }}" class="form-control form-control-dark">
                </div>
                <div class="col-md-3">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Expéditeur — adresse</label>
                    <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $site['mail_from_address']) }}" placeholder="{{ $site['contact_email'] }}" class="form-control form-control-dark">
                </div>

                <div class="col-12">
                    <p class="text-muted-custom small text-uppercase fw-bold mb-0 mt-2" style="font-size:10px;letter-spacing:0.15em">Serveur d'envoi</p>
                </div>

                <div class="col-md-3">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Transport</label>
                    <select name="mail_mailer" class="form-control form-control-dark">
                        <option value="log" @selected(($site['mail_mailer'] ?? 'log') === 'log')>Journal (test)</option>
                        <option value="smtp" @selected(($site['mail_mailer'] ?? 'log') === 'smtp')>SMTP</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Serveur SMTP (hôte)</label>
                    <input name="mail_host" value="{{ old('mail_host', $site['mail_host']) }}" placeholder="smtp.gmail.com" class="form-control form-control-dark">
                </div>
                <div class="col-md-3">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Port</label>
                    <input name="mail_port" value="{{ old('mail_port', $site['mail_port']) }}" placeholder="587" class="form-control form-control-dark">
                </div>
                <div class="col-md-3">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Chiffrement</label>
                    <select name="mail_encryption" class="form-control form-control-dark">
                        <option value="tls" @selected(($site['mail_encryption'] ?? 'tls') === 'tls')>TLS (587)</option>
                        <option value="ssl" @selected(($site['mail_encryption'] ?? 'tls') === 'ssl')>SSL (465)</option>
                        <option value="none" @selected(($site['mail_encryption'] ?? 'tls') === 'none')>Aucun</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Utilisateur SMTP</label>
                    <input name="mail_username" value="{{ old('mail_username', $site['mail_username']) }}" autocomplete="off" class="form-control form-control-dark">
                </div>
                <div class="col-md-5">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Mot de passe SMTP</label>
                    <input type="password" name="mail_password" value="" autocomplete="new-password" placeholder="{{ !empty($site['mail_password']) ? '•••••••• (inchangé)' : 'Mot de passe' }}" class="form-control form-control-dark">
                    @if(!empty($site['mail_password']))
                        <label class="d-flex align-items-center gap-2 small text-muted-custom mt-2 mb-0">
                            <input type="checkbox" name="clear_mail_password" value="1"> Supprimer le mot de passe enregistré
                        </label>
                    @endif
                </div>
            </div>
            <p class="text-muted-custom small mb-0 mt-3">
                Avec « Journal (test) », les e-mails sont écrits dans <code>storage/logs/laravel.log</code> au lieu d'être envoyés.
                Choisissez « SMTP » et renseignez votre serveur pour les envois réels.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-3">
            <button type="submit" class="btn-volt border-0">ENREGISTRER LES PARAMÈTRES</button>
            <a href="{{ route('admin') }}" class="btn-outline-custom">Annuler</a>
        </div>
    </form>

    {{-- Test d'envoi des e-mails --}}
    <div class="border border-custom bg-panel p-4 p-md-5 mt-4 mb-4">
        <h2 class="font-display fs-3 mb-1">Tester l'envoi des e-mails</h2>
        <p class="text-muted-custom small mb-2">
            Enregistrez d'abord vos paramètres, puis envoyez les quatre e-mails du site à l'adresse de votre choix
            pour vérifier qu'ils arrivent bien.
        </p>
        <p class="small mb-4">
            Transport actif :
            @if(config('mail.default') === 'smtp')
                <span class="text-volt fw-bold">SMTP</span> — les e-mails partent réellement vers leurs destinataires.
            @else
                <span class="text-volt fw-bold">{{ config('mail.default') }}</span> — les e-mails sont écrits dans <code>storage/logs/laravel.log</code> et ne partent pas.
            @endif
        </p>
        <form method="POST" action="{{ route('admin.settings.test-mail') }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-md-6">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Adresse de test</label>
                <input type="email" name="test_email" value="{{ old('test_email', $site['notification_email'] ?: $site['contact_email']) }}" required class="form-control form-control-dark">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn-volt border-0">ENVOYER UN E-MAIL DE TEST</button>
            </div>
        </form>
    </div>

    {{-- Bandeau défilant (marquee) --}}
    <div class="border border-custom bg-panel p-4 p-md-5 mt-5 mb-4">
        <h2 class="font-display fs-3 mb-1">Bandeau défilant (images qui défilent)</h2>
        <p class="text-muted-custom small mb-4">Ces images défilent de gauche à droite sous le bloc principal. Ajoutez-en ou retirez-les à volonté.</p>

        @if($marqueeItems->isNotEmpty())
            <div class="row g-3 mb-4">
                @foreach($marqueeItems as $item)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border border-custom p-2 h-100 bg-panel2">
                            <img src="{{ asset($item->image) }}" alt="{{ $item->nom }}" class="w-100 mb-2" style="aspect-ratio:16/9;object-fit:cover">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="font-display fs-6">{{ $item->nom }}</div>
                                    <div class="small text-muted-custom">{{ $item->spec }}</div>
                                </div>
                                <form method="POST" action="{{ route('admin.marquee.destroy', $item) }}" onsubmit="return confirm('Retirer cette image du bandeau ?');" class="ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-outline-custom border-0" style="background:transparent;color:#ff4d4d">Retirer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted-custom small mb-4">Aucune image pour le moment. Ajoutez la première ci-dessous.</p>
        @endif

        <hr class="border-custom my-4">
        <h3 class="font-display fs-4 mb-3">Ajouter une image au bandeau</h3>
        <form method="POST" action="{{ route('admin.marquee.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Image *</label>
                    <input type="file" name="marquee_image" accept="image/*" required class="form-control form-control-dark">
                </div>
                <div class="col-md-4">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Nom *</label>
                    <input name="marquee_nom" required class="form-control form-control-dark" placeholder="ex : Vortex SV">
                </div>
                <div class="col-md-4">
                    <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Spécification</label>
                    <input name="marquee_spec" class="form-control form-control-dark" placeholder="ex : 740 HP / 0-100 2.8s">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn-volt border-0">AJOUTER CETTE IMAGE</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const primary = document.getElementById('color_primary');
        const secondary = document.getElementById('color_secondary');
        const dark = document.getElementById('color_dark');
        const preview = document.getElementById('palettePreview');

        if (!primary || !secondary || !dark || !preview) {
            return;
        }

        const chipPrimary = document.getElementById('palettePreviewPrimary');
        const chipSecondary = document.getElementById('palettePreviewSecondary');
        const chipText = document.getElementById('palettePreviewText');

        // Luminance relative : choisit un texte noir ou blanc sur l'aplat principal.
        const contrast = (hex) => {
            const channels = [1, 3, 5]
                .map((index) => parseInt(hex.slice(index, index + 2), 16) / 255)
                .map((value) => (value <= 0.03928 ? value / 12.92 : Math.pow((value + 0.055) / 1.055, 2.4)));

            const luminance = 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];

            return luminance > 0.45 ? '#000000' : '#ffffff';
        };

        const render = () => {
            preview.style.background = dark.value;
            chipPrimary.style.background = primary.value;
            chipPrimary.style.color = contrast(primary.value);
            chipSecondary.style.border = '1px solid ' + secondary.value;
            chipSecondary.style.color = secondary.value;
            chipText.style.color = primary.value;
        };

        [primary, secondary, dark].forEach((field) => field.addEventListener('input', render));

        document.querySelectorAll('[data-palette]').forEach((button) => {
            button.addEventListener('click', () => {
                const [newPrimary, newSecondary, newDark] = button.dataset.palette.split(',');

                primary.value = newPrimary;
                secondary.value = newSecondary;
                dark.value = newDark;
                render();
            });
        });

        render();
    })();
</script>
@endpush
