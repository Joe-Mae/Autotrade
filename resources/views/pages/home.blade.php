@extends('layouts.app')

@section('title', $site['site_name'] . ' — Vente de voitures d\'exception')

@section('content')
<section class="border-bottom border-custom">
    <div class="container py-5" style="max-width:1280px">
        <div class="row align-items-center g-5 py-lg-5">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span style="height:2px;width:32px;background:var(--volt)"></span>
                    <span class="badge-label">{{ $site['hero_badge'] }}</span>
                </div>
                <h1 class="hero-title mb-0">
                    <span class="d-block">{{ $site['hero_title_1'] }}</span>
                    <span class="d-block hero-outline">{{ $site['hero_title_2'] }}</span>
                </h1>
                <p class="mt-4 text-uppercase small text-muted-custom" style="max-width:28rem;letter-spacing:0.08em;line-height:1.7">
                    {{ $site['hero_text'] }}
                </p>
                <div class="d-flex flex-wrap gap-3 mt-5">
                    <a href="{{ route('catalogue') }}" class="btn-volt">{{ $site['hero_cta_primary'] }}</a>
                    <a href="{{ route('contact') }}" class="btn-outline-custom">{{ $site['hero_cta_secondary'] }}</a>
                </div>
                <ul class="list-unstyled d-flex flex-wrap gap-4 mt-5 mb-0 small text-uppercase text-muted-custom" style="letter-spacing:0.1em;font-size:10px;font-weight:700">
                    <li>Garantie 12 mois</li>
                    <li>Financement &amp; reprise</li>
                    <li>Essai sur rendez-vous</li>
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="hero-slider skew" id="heroSlider" aria-roledescription="carrousel" aria-label="Véhicules à vendre">
                    @foreach($slides as $i => $slide)
                        <a href="{{ $slide['url'] }}"
                           class="hero-slide {{ $i === 0 ? 'is-active' : '' }}"
                           aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                            <img src="{{ asset($slide['image']) }}"
                                 alt="{{ $slide['caption'] }} à vendre chez {{ $site['site_name'] }}"
                                 loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                            <span class="hero-slide-caption">
                                <span class="font-display fs-4 d-block">{{ $slide['caption'] }}</span>
                            </span>
                        </a>
                    @endforeach

                    <button type="button" class="hero-slider-nav hero-slider-prev" aria-label="Photo précédente">&#8249;</button>
                    <button type="button" class="hero-slider-nav hero-slider-next" aria-label="Photo suivante">&#8250;</button>

                    <div class="hero-slider-dots" aria-label="Choisir une photo">
                        @foreach($slides as $i => $slide)
                            <button type="button"
                                    class="hero-dot {{ $i === 0 ? 'is-active' : '' }}"
                                    data-index="{{ $i }}"
                                    aria-label="Voir {{ $slide['caption'] }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="marquee-wrap">
    <div class="marquee-track">
        @php
            $marqueeDefaults = [
                ['image' => 'assets/car-vortex.jpg', 'nom' => 'Vortex SV', 'spec' => '740 HP / 0-100 2.8s'],
                ['image' => 'assets/car-electron.jpg', 'nom' => 'Electron Z', 'spec' => '100% Electric Performance'],
                ['image' => 'assets/car-heritage.jpg', 'nom' => 'Heritage V8', 'spec' => 'Timeless Power'],
            ];
            if ($marqueeItems->isEmpty()) {
                $marqueeItems = collect($marqueeDefaults)->map(fn($m) => (object) $m);
            }
        @endphp
        @foreach($marqueeItems as $m)
            @foreach(range(1,2) as $_)
            <div class="marquee-item">
                <img src="{{ asset($m->image) }}" alt="{{ $m->nom }}">
                <div>
                    <div class="font-display fs-5">{{ $m->nom ?? $m['nom'] }}</div>
                    <div class="small text-muted-custom">{{ $m->spec ?? $m['spec'] }}</div>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
</div>

<section class="bg-panel2 border-bottom border-custom">
    <div class="container-fluid p-0" style="max-width:1280px;margin:0 auto">
        <div class="row g-0">
            <div class="col-md-4 cat-block">
                <span class="font-display text-volt fs-4">01</span>
                <h2 class="font-display fs-3 mt-3 mb-2">GARANTIE 12 MOIS</h2>
                <p class="small text-muted-custom mb-0">Chaque véhicule vendu est livré avec son carnet d'entretien et une garantie de 12 mois.</p>
            </div>
            <div class="col-md-4 cat-block">
                <span class="font-display text-volt fs-4">02</span>
                <h2 class="font-display fs-3 mt-3 mb-2">FINANCEMENT & REPRISE</h2>
                <p class="small text-muted-custom mb-0">Payez comptant, financez votre achat ou reprenez la valeur de votre véhicule actuel.</p>
            </div>
            <div class="col-md-4 cat-block">
                <span class="font-display text-volt fs-4">03</span>
                <h2 class="font-display fs-3 mt-3 mb-2">INSPECTION & ESSAI</h2>
                <p class="small text-muted-custom mb-0">150 points contrôlés avant la vente et essai sur rendez-vous avant de signer.</p>
            </div>
        </div>
    </div>
</section>

<section class="container py-5" style="max-width:1280px">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-4 mb-5 pt-4">
        <div style="max-width:36rem">
            <h2 class="font-display page-title mb-3">
                La Sélection <span class="text-volt">Vente</span>
            </h2>
            <p class="fs-5 fst-italic text-muted-custom mb-0">
                Nos modèles les plus demandés cette semaine, inspectés et préparés en atelier avant la livraison.
            </p>
        </div>
        <a href="{{ route('catalogue') }}" class="link-underline">Voir tout le catalogue</a>
    </div>
    <div class="row g-4">
        @foreach($selection as $vehicle)
            @include('partials.vehicle-card', ['vehicle' => $vehicle])
        @endforeach
    </div>
</section>
<section class="bg-panel2 border-top border-custom">
    <div class="container py-5" style="max-width:1280px">
        <div class="row align-items-center g-5 py-lg-5">
            <div class="col-lg-7">
                <p class="section-label">Prêt à acheter ?</p>
                <h2 class="page-title mt-3 mb-4" style="font-size:clamp(2rem,5vw,3.5rem)">VOTRE PROCHAINE <span class="text-volt">VOITURE</span> VOUS ATTEND</h2>
                <p class="text-muted-custom mb-4" style="max-width:34rem">
                    Réservez un essai, demandez un financement ou une reprise : un conseiller vente {{ $site['site_name'] }} vous répond sous 24 h avec une offre personnalisée.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('catalogue') }}" class="btn-volt">EXPLORER LE CATALOGUE</a>
                    <a href="{{ route('contact') }}" class="btn-outline-custom">PARLER À UN CONSEILLER</a>
                </div>
            </div>
            <div class="col-lg-5">
                <img src="{{ asset('assets/car-apex.jpg') }}" alt="Voiture à acheter" class="img-fluid skew w-100" style="object-fit:cover;max-height:420px">
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.hero-slide'));
    const dots = Array.from(slider.querySelectorAll('.hero-dot'));
    if (slides.length < 2) return;

    const DELAY = 4500;
    let current = 0;
    let timer = null;

    function show(index) {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, i) => {
            const active = i === current;
            slide.classList.toggle('is-active', active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach((dot, i) => dot.classList.toggle('is-active', i === current));
    }

    const next = () => show(current + 1);
    const prev = () => show(current - 1);

    function stop() {
        if (timer) clearInterval(timer);
        timer = null;
    }

    function start() {
        stop();
        timer = setInterval(next, DELAY);
    }

    slider.querySelector('.hero-slider-next')?.addEventListener('click', () => { next(); start(); });
    slider.querySelector('.hero-slider-prev')?.addEventListener('click', () => { prev(); start(); });
    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); start(); }));

    // Pause au survol, reprise ensuite.
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    // Navigation au doigt (mobile).
    let touchX = null;
    slider.addEventListener('touchstart', (e) => { touchX = e.touches[0].clientX; stop(); }, { passive: true });
    slider.addEventListener('touchend', (e) => {
        if (touchX === null) return;
        const delta = e.changedTouches[0].clientX - touchX;
        if (Math.abs(delta) > 40) { delta < 0 ? next() : prev(); }
        touchX = null;
        start();
    });

    start();
})();
</script>
@endpush
