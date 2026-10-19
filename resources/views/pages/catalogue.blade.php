@extends('layouts.app')

@section('title', 'Véhicules à vendre — ' . $site['site_name'])

@section('content')
<section class="container py-5" style="max-width:1280px">
    <p class="section-label">Véhicules</p>
    <h1 class="page-title mt-3">NOS VOITURES <span class="text-volt">À VENDRE</span></h1>
    <p class="mt-4 text-uppercase small text-muted-custom" style="max-width:36rem;letter-spacing:0.08em">
        {{ $vehicles->count() }} véhicule(s) disponible(s) à l'achat · Neuf et occasion, garantis 12 mois. Chaque véhicule est vendu avec son carnet d'entretien, une garantie 12 mois et un contrôle technique récent.
    </p>

    <div class="border-top border-custom pt-4 mt-5">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <span class="small text-uppercase text-muted-custom fw-bold" style="letter-spacing:0.1em;font-size:10px;min-width:72px">Catégorie</span>
            <div class="d-flex flex-wrap gap-2">
                @foreach($categories as $cat)
                    <a href="{{ route('catalogue', ['categorie' => $cat, 'etat' => $etat]) }}"
                       class="btn-filter text-decoration-none {{ $categorie === $cat ? 'active' : '' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-3">
            <span class="small text-uppercase text-muted-custom fw-bold" style="letter-spacing:0.1em;font-size:10px;min-width:72px">État</span>
            <div class="d-flex flex-wrap gap-2">
                @foreach($etats as $e)
                    <a href="{{ route('catalogue', ['categorie' => $categorie, 'etat' => $e]) }}"
                       class="btn-filter text-decoration-none {{ $etat === $e ? 'active' : '' }}">
                        {{ $e }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4">
        @forelse($vehicles as $vehicle)
            @include('partials.vehicle-card', ['vehicle' => $vehicle])
        @empty
            <div class="col-12 text-muted-custom">Aucun véhicule ne correspond à cette sélection.</div>
        @endforelse
    </div>

    @if($soldVehicles->count())
    <section class="border-top border-custom mt-5 pt-5">
        <p class="section-label">Voitures vendues</p>
        <h2 class="page-title mt-3" style="font-size:clamp(1.7rem,4vw,3rem)">VÉHICULES RÉCEMMENT <span class="text-volt">VENDUS</span></h2>
        <p class="mt-3 text-muted-custom small" style="max-width:36rem;letter-spacing:0.06em">
            {{ $soldVehicles->count() }} véhicule(s) actuellement affiché(s) comme vendu(s) — consultez-nous pour des modèles similaires à l'achat.
        </p>
        <div class="row g-4 mt-3">
            @foreach($soldVehicles as $vehicle)
                @include('partials.vehicle-card', ['vehicle' => $vehicle])
            @endforeach
        </div>
    </section>
    @endif
</section>

<section class="bg-panel2 border-top border-custom">
    <div class="container py-5" style="max-width:1280px">
        <p class="section-label">Comment acheter</p>
        <h2 class="page-title mt-3 mb-5" style="font-size:clamp(2rem,5vw,3.5rem)">L'ACHAT EN <span class="text-volt">4 ÉTAPES</span></h2>
        <div class="row g-4">
            @foreach([
                ['01', 'Choisissez', "Parcourez le catalogue et comparez les modèles, les états (neuf / occasion) et les prix affichés."],
                ['02', 'Demandez', "Envoyez une demande d'achat ou réservez un essai : un conseiller vous recontacte sous 24 h."],
                ['03', 'Financez', "Achat comptant, financement ou reprise de votre véhicule actuel, selon votre projet."],
                ['04', 'Roulez', "Livraison avec carte grise, carnet d'entretien et garantie 12 mois."],
            ] as $step)
            <div class="col-md-6 col-lg-3">
                <div class="border border-custom bg-panel p-4 h-100">
                    <span class="font-display text-volt fs-4">{{ $step[0] }}</span>
                    <h3 class="font-display fs-4 mt-3 mb-2">{{ $step[1] }}</h3>
                    <p class="small text-muted-custom mb-0">{{ $step[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
