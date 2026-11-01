@extends('layouts.app')

@section('title', 'Demande d\'achat — ' . $vehicle->nom . ' | ' . $site['site_name'])

@section('content')
<section class="container py-5" style="max-width:1280px">
    <p class="section-label">Demande d'achat</p>
    <h1 class="page-title mt-3" style="font-size:clamp(2.5rem,6vw,4.5rem)">{{ $vehicle->nom }}</h1>
    <div class="row g-5 mt-3">
        <div class="col-lg-7">
            <form method="POST" action="{{ route('demande.achat.store', $vehicle) }}" class="border border-custom bg-panel p-4 p-md-5">
                @csrf
                {{-- Champ piège anti-robot : masqué et inaccessible au clavier. --}}
                <div aria-hidden="true" style="position:absolute;left:-9999px;top:-9999px;width:1px;height:1px;overflow:hidden">
                    <input type="text" name="site_web" tabindex="-1" autocomplete="off">
                </div>
                <h2 class="font-display fs-3 mb-2">Votre projet d'achat</h2>
                <p class="text-muted-custom mb-4">Indiquez-nous vos coordonnées et votre projet. Un conseiller vente vous recontacte sous 24 h avec une offre personnalisée.</p>
                <input name="nom" value="{{ old('nom') }}" required class="form-control form-control-dark mb-3" placeholder="Nom et prénom">
                @error('nom')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <input name="email" type="email" value="{{ old('email') }}" required class="form-control form-control-dark mb-3" placeholder="E-mail">
                @error('email')<div class="text-danger small mb-2">{{ $message }}</div>@enderror
                <div class="row g-2 mb-3">
                    <div class="col-md-5">
                        <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Pays (indicatif)</label>
                        <select name="phone_code" required class="form-control form-control-dark">
                            @php
                                $countries = [
                                    ['🇫🇷','France','+33'],['🇧🇯','Bénin','+229'],['🇧🇫','Burkina Faso','+226'],
                                    ['🇧🇮','Burundi','+257'],['🇨🇲','Cameroun','+237'],['🇨🇦','Canada','+1'],
                                    ['🇨🇫','Centrafrique','+236'],['🇹🇩','Tchad','+235'],['🇨🇳','Chine','+86'],
                                    ['🇨🇮','Côte d\'Ivoire','+225'],['🇨🇩','Congo - RDC','+243'],
                                    ['🇨🇬','Congo - Brazzaville','+242'],['🇩🇿','Algérie','+213'],
                                    ['🇪🇬','Égypte','+20'],['🇦🇪','Émirats arabes unis','+971'],
                                    ['🇪🇹','Éthiopie','+251'],['🇬🇦','Gabon','+241'],['🇬🇭','Ghana','+233'],
                                    ['🇬🇳','Guinée','+224'],['🇩🇪','Allemagne','+49'],['🇮🇳','Inde','+91'],
                                    ['🇮🇷','Iran','+98'],['🇯🇵','Japon','+81'],['🇰🇪','Kenya','+254'],
                                    ['🇱🇧','Liban','+961'],['🇲🇱','Mali','+223'],['🇲🇦','Maroc','+212'],
                                    ['🇲🇷','Mauritanie','+222'],['🇳🇪','Niger','+227'],['🇳🇬','Nigéria','+234'],
                                    ['🇳🇱','Pays-Bas','+31'],['🇵🇹','Portugal','+351'],['🇵🇰','Pakistan','+92'],
                                    ['🇶🇦','Qatar','+974'],['🇬🇧','Royaume-Uni','+44'],['🇷🇼','Rwanda','+250'],
                                    ['🇸🇳','Sénégal','+221'],['🇨🇭','Suisse','+41'],['🇸🇦','Arabie saoudite','+966'],
                                    ['🇺🇸','États-Unis','+1'],
                                    ['🇹🇳','Tunisie','+216'],['🇹🇬','Togo','+228'],['🇹🇷','Turquie','+90'],
                                ];
                            @endphp
                            @foreach($countries as [$flag, $country, $dial])
                                <option value="{{ $dial }}" @selected(old('phone_code', '+228') === $dial)>
                                    {{ $flag }}  {{ $country }} ({{ $dial }})
                                </option>
                            @endforeach
                        </select>
                        @error('phone_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col">
                        <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Numéro de téléphone</label>
                        <input type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                               class="form-control form-control-dark" placeholder="70 00 00 00"
                               inputmode="tel" autocomplete="tel-national">
                        @error('phone_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="select-ornament">
    <select name="interet" class="form-control form-control-dark mb-3" required>
                    <option value="" disabled selected hidden>Options de paiement</option>
                    <option value="comptant" @selected(old('interet')==='comptant')>Acheter directement (paiement comptant)</option>
                    <option value="financement" @selected(old('interet')==='financement')>Financer mon achat (crédit)</option>
                    <option value="reprise" @selected(old('interet')==='reprise')>Faire reprendre mon ancien véhicule</option>
                    <option value="essai" @selected(old('interet')==='essai')>Réserver un essai avant d'acheter</option>
                </select>
                </div>
                <textarea name="message" rows="4" class="form-control form-control-dark mb-3" placeholder="Message (optionnel)">{{ old('message') }}</textarea>
                <button type="submit" class="btn-volt w-100 skew-reverse border-0">ENVOYER MA DEMANDE</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="border border-custom bg-panel p-4 sticky-top" style="top:100px">
                <img src="{{ asset($vehicle->image) }}" alt="{{ $vehicle->nom }} à vendre" class="img-fluid mb-3" style="aspect-ratio:4/3;object-fit:cover">
                <h3 class="font-display fs-4">{{ $vehicle->nom }}</h3>
                <p class="small text-muted-custom text-uppercase mb-3">{{ $vehicle->marque }} · {{ $vehicle->etat }} · {{ $vehicle->annee }}</p>
                <div class="d-flex justify-content-between align-items-end">
                    <div>
                        <span class="d-block text-muted-custom" style="font-size:10px;text-transform:uppercase;letter-spacing:0.1em">Prix de vente</span>
                        <span class="font-display fs-2 text-volt">{{ \App\Models\Vehicle::cfa($vehicle->prix_vente) }}</span>
                    </div>
                    <span class="{{ $vehicle->disponible ? 'badge-dispo' : 'badge-indispo' }}">
                        {{ $vehicle->badge_label }}
                    </span>
                </div>
                <hr class="border-custom">
                <p class="small text-muted-custom mb-3">{{ $vehicle->description }}</p>
                <ul class="list-unstyled small text-muted-custom mb-0" style="line-height:2">
                    <li>✓ Garantie 12 mois incluse</li>
                    <li>✓ Financement &amp; reprise possibles</li>
                    <li>✓ Essai sur rendez-vous</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
