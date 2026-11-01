@extends('layouts.app')

@section('title', $vehicle->nom . ' — ' . $site['site_name'])

@section('content')
<section class="container py-5" style="max-width:1280px">
    <a href="{{ route('catalogue') }}" class="text-muted-custom text-decoration-none small text-uppercase fw-bold" style="letter-spacing:0.15em;font-size:10px">← Catalogue</a>
    <div class="row g-5 mt-2">
        <div class="col-lg-7">
            @php
                $photos = array_values(array_unique(array_filter(array_merge([$vehicle->image], is_array($vehicle->galerie) ? $vehicle->galerie : []))));
                if (empty($photos)) { $photos = ['assets/car-storm.jpg']; }
            @endphp
            <div class="vehicle-gallery" id="vehicleGallery">
                <div class="vehicle-gallery-stage position-relative mb-3">
                    @foreach($photos as $i => $p)
                        <img src="{{ asset($p) }}" alt="{{ $vehicle->nom }} photo {{ $i+1 }}"
                             class="gallery-photo img-fluid skew w-100 {{ $i === 0 ? 'is-active' : '' }}"
                             style="object-fit:cover; {{ $i === 0 ? '' : 'display:none' }}">
                    @endforeach
                    @if(count($photos) > 1)
                        <button type="button" class="gallery-nav gallery-prev" aria-label="Photo précédente">&#8249;</button>
                        <button type="button" class="gallery-nav gallery-next" aria-label="Photo suivante">&#8250;</button>
                    @endif
                </div>
                @if(count($photos) > 1)
                    <div class="d-flex flex-wrap gap-2 justify-content-start mb-3">
                        @foreach($photos as $i => $p)
                            <button type="button" class="gallery-thumb {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}"
                                    style="border:0;background:transparent;padding:0">
                                <img src="{{ asset($p) }}" alt="mini {{ $i+1 }}" style="height:70px;width:95px;object-fit:cover;opacity:{{ $i===0?1:0.5 }};transition:opacity .2s">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
            <script>
            (function(){
                const gallery = document.getElementById('vehicleGallery');
                if(!gallery) return;
                const photos = gallery.querySelectorAll('.gallery-photo');
                const thumbs = gallery.querySelectorAll('.gallery-thumb');
                if(photos.length < 2) return;
                let current = 0;
                let timer = null;
                function show(i){
                    current = (i + photos.length) % photos.length;
                    photos.forEach((p,idx)=>{ p.style.display = idx === current ? '' : 'none'; p.classList.toggle('is-active', idx===current); });
                    thumbs.forEach((t,idx)=>{ t.classList.toggle('is-active', idx===current); const img=t.querySelector('img'); if(img){ img.style.opacity = idx===current ? '1' : '0.5'; } });
                }
                function next(){ show(current+1); }
                gallery.querySelector('.gallery-next')?.addEventListener('click',()=>{ show(current+1); restart(); });
                gallery.querySelector('.gallery-prev')?.addEventListener('click',()=>{ show(current-1); restart(); });
                thumbs.forEach((t,i)=> t.addEventListener('click',()=>{ show(i); restart(); }));
                function stop(){ if(timer){ clearInterval(timer); timer=null; } }
                function restart(){ stop(); timer=setInterval(next,3500); }
                gallery.addEventListener('mouseenter', stop);
                gallery.addEventListener('mouseleave', restart);
                restart();
            })();
            </script>
            @if($vehicle->video)
                <div class="mt-4">
                    <p class="section-label mb-2">Vidéo de présentation</p>
                    <video class="w-100" controls preload="metadata" playsinline
                           poster="{{ asset($vehicle->image) }}"
                           style="border:1px solid var(--border);background:#000;display:block">
                        <source src="{{ asset($vehicle->video) }}" @if($vehicle->video_mime) type="{{ $vehicle->video_mime }}" @endif>
                        Votre navigateur ne prend pas en charge la lecture vidéo.
                    </video>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="{{ $vehicle->disponible ? 'badge-dispo' : 'badge-indispo' }}">
                    {{ $vehicle->badge_label }}
                </span>
                <span class="badge-indispo">{{ $vehicle->etat }}</span>
            </div>
            <h1 class="font-display display-4 mt-3 mb-2">{{ $vehicle->nom }}</h1>
            <p class="small text-uppercase text-muted-custom" style="letter-spacing:0.1em">
                {{ $vehicle->marque }} · {{ $vehicle->categorie }} · {{ $vehicle->annee }}
            </p>
            <p class="text-muted-custom mt-4">{{ $vehicle->description }}</p>
            <dl class="mt-4 mb-0">
                @foreach([
                    'État' => $vehicle->etat,
                    'Année' => $vehicle->annee,
                    'Carburant' => $vehicle->carburant,
                    'Transmission' => $vehicle->boite,
                    'Kilométrage' => $vehicle->km,
                    'Places' => $vehicle->places,
                ] as $k => $val)
                <div class="spec-row">
                    <dt class="small text-uppercase text-muted-custom fw-bold" style="letter-spacing:0.1em;font-size:10px">{{ $k }}</dt>
                    <dd class="mb-0 fw-bold">{{ $val }}</dd>
                </div>
                @endforeach
            </dl>
            <div class="d-flex flex-wrap gap-4 mt-4">
                <div>
                    <span class="d-block text-muted-custom" style="font-size:10px;text-transform:uppercase;letter-spacing:0.1em">Prix de vente</span>
                    <span class="font-display fs-2 text-volt lh-1">{{ \App\Models\Vehicle::cfa($vehicle->prix_vente) }}</span>
                </div>
            </div>
            <ul class="list-unstyled small text-muted-custom mt-4 mb-0" style="line-height:2">
                <li>✓ Garantie 12 mois incluse</li>
                <li>✓ Carnet d'entretien &amp; contrôle technique à jour</li>
                <li>✓ Financement et reprise possibles</li>
                <li>✓ Essai sur rendez-vous</li>
            </ul>
            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                @if($vehicle->disponible)
                    <a href="{{ route('demande.achat', $vehicle) }}" class="btn-volt text-center">DEMANDER L'ACHAT</a>
                @else
                    <span class="btn-outline-custom text-center" style="cursor:default;opacity:0.6">VENDU</span>
                @endif
                <a href="{{ route('contact') }}" class="btn-outline-custom text-center">POSER UNE QUESTION</a>
            </div>
        </div>
    </div>
</section>
@endsection
