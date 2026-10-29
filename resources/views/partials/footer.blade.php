<footer class="site-footer">
    <div class="container" style="max-width:1280px">
        <div class="row g-5 mb-5">
            <div class="col-lg-6">
                <h2 class="font-display display-4 text-uppercase mb-4">
                    DEVENIR<br><span class="text-volt">PROPRIÉTAIRE</span>
                </h2>
                <p class="text-muted-custom mb-4" style="max-width:28rem">
                    Notre service vente vous accompagne dans l'acquisition de véhicules d'exception : financement, reprise, import et personnalisation.
                </p>
                <a href="{{ route('catalogue') }}" class="link-underline">Voir les véhicules à vendre</a>
            </div>
            <div class="col-lg-6">
                <div class="border border-custom bg-panel p-4 p-md-5 skew">
                    <h3 class="font-display fs-3 mb-4">Une question ?</h3>
                    <form action="{{ route('contact.store') }}" method="POST">
                        @csrf
                        {{-- Champ piège anti-robot : masqué et inaccessible au clavier. --}}
                        <div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden">
                            <input type="text" name="site_web" tabindex="-1" autocomplete="off">
                        </div>
                        <input type="email" name="email" class="form-control form-control-dark mb-3" placeholder="Votre e-mail" required>
                        <input type="hidden" name="nom" value="Visiteur footer">
                        <textarea name="message" class="form-control form-control-dark mb-3" rows="3" placeholder="Votre demande d'achat" required></textarea>
                        <button type="submit" class="btn-volt w-100 skew-reverse text-center border-0">ENVOYER LE MESSAGE</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4 pt-4 border-top border-custom">
            <div class="d-flex align-items-center gap-3">
                @if(!empty($site['logo']))
                    <img src="{{ asset($site['logo']) }}" alt="{{ $site['site_name'] }}" class="brand-logo" style="height:24px">
                @else
                    <span class="logo-mark" style="width:32px;height:32px;font-size:1rem;background:rgba(255,255,255,0.05);color:var(--bone)">A</span>
                @endif
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.2em" class="text-muted-custom">
                    © {{ date('Y') }} {{ $site['site_name'] }} — {{ $site['site_tagline'] }}
                </span>
            </div>
            <div class="d-flex gap-4">
                <a href="{{ route('catalogue') }}" class="text-muted-custom text-decoration-none" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em">Véhicules</a>
                <a href="{{ route('contact') }}" class="text-muted-custom text-decoration-none" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em">Contact</a>
                <a href="{{ route('admin') }}" target="_blank" rel="noopener noreferrer" class="text-muted-custom text-decoration-none" style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.15em">Admin</a>
            </div>
        </div>
    </div>
</footer>
