@extends('layouts.admin')

@section('title', ($editing ? 'Modifier' : 'Ajouter') . ' un véhicule — Admin')
@section('admin_section', 'flotte')

@section('content')
<div style="max-width:980px">
    <div class="admin-head">
        <div>
            <p class="section-label mb-0">{{ $editing ? 'Modification' : 'Nouveau véhicule' }}</p>
            <h1 class="admin-title">{{ $editing ? 'Modifier' : 'Ajouter' }} <span class="text-volt">un véhicule</span></h1>
            <p class="admin-subtitle">Renseignez les caractéristiques puis les photos du véhicule.</p>
        </div>
    </div>

    <form method="POST"
          action="{{ $editing ? route('admin.vehicles.update', $vehicle) : route('admin.vehicles.store') }}"
          class="border border-custom bg-panel p-4 p-md-5 mt-4"
          enctype="multipart/form-data">
        @csrf
        @if($editing)
            @method('PUT')
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Modèle</label>
                <input name="nom" value="{{ old('nom', $vehicle->nom) }}" required class="form-control form-control-dark">
                @error('nom')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Marque</label>
                <input name="marque" value="{{ old('marque', $vehicle->marque) }}" required class="form-control form-control-dark">
                @error('marque')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Année</label>
                <input type="number" name="annee" value="{{ old('annee', $vehicle->annee ?? date('Y')) }}" required min="1950" max="2100" class="form-control form-control-dark">
                @error('annee')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Type de véhicule</label>
                <select name="categorie" required class="form-control form-control-dark">
                    @foreach(['Super Sport','Luxe & Confort','Électrique','SUV'] as $c)
                        <option value="{{ $c }}" @selected(old('categorie', $vehicle->categorie) === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                @error('categorie')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">État</label>
                <select name="etat" required class="form-control form-control-dark">
                    @foreach(['Neuf','Occasion','Reconditionné'] as $e)
                        <option value="{{ $e }}" @selected(old('etat', $vehicle->etat ?? 'Occasion') === $e)>{{ $e }}</option>
                    @endforeach
                </select>
                @error('etat')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Moteur</label>
                <select name="carburant" required class="form-control form-control-dark">
                    @foreach(['Essence','Diesel','Hybride','Électrique'] as $c)
                        <option value="{{ $c }}" @selected(old('carburant', $vehicle->carburant) === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                @error('carburant')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Transmission</label>
                <select name="boite" required class="form-control form-control-dark">
                    @foreach(['Manuel','Automatique','Semi-automatique'] as $b)
                        <option value="{{ $b }}" @selected(old('boite', $vehicle->boite) === $b)>{{ $b }}</option>
                    @endforeach
                </select>
                @error('boite')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Kilométrage</label>
                <input type="number" name="km" value="{{ old('km', is_numeric($vehicle->km) ? (int) $vehicle->km : '') }}" required min="0" step="1" class="form-control form-control-dark" placeholder="ex: 8400">
                @error('km')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Prix ( en FCFA )</label>
                <input type="number" name="prix_vente" value="{{ old('prix_vente', $vehicle->prix_vente) }}" min="0" step="0.01" class="form-control form-control-dark" placeholder="Prix">
                @error('prix_vente')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Photos — 4 photos max, 2 Mo chacune</label>
                <input type="file" name="photos[]" id="photosInput" accept="image/*" multiple class="form-control form-control-dark">
                @error('photos.*')<div class="text-danger small">{{ $message }}</div>@enderror
                @error('photos')<div class="text-danger small">{{ $message }}</div>@enderror

                <div class="mt-3" id="photoManager">
                    <p class="small text-muted-custom mb-2">
                        Aperçu des photos : les photos déjà enregistrées et celles que vous venez de choisir.
                        <strong>Cochez « Premier plan »</strong> sous la photo à afficher en tête de la page Véhicules.
                    </p>
                    <div class="row g-2" id="photoGrid">
                        @if($editing && is_array($vehicle->galerie))
                            @foreach(array_slice($vehicle->galerie, 0, 4) as $g)
                                <div class="col-6 col-md-3" data-photo data-value="{{ $g }}">
                                    <div class="border border-custom p-1 h-100 d-flex flex-column">
                                        <img src="{{ asset($g) }}" alt="Photo du véhicule" class="w-100" style="height:90px;object-fit:cover">
                                        <div class="form-check d-flex align-items-center justify-content-center gap-1 mt-2 mb-0">
                                            <input class="form-check-input photo-primary" type="checkbox"
                                                   id="photo_exist_{{ $loop->index }}"
                                                   value="{{ $g }}"
                                                   {{ $vehicle->image === $g ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="photo_exist_{{ $loop->index }}">Premier plan</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <p class="small text-muted-custom mb-0 {{ ($editing && is_array($vehicle->galerie) && count($vehicle->galerie)) ? 'd-none' : '' }}" id="photoEmpty">
                        Aucune photo pour le moment. Choisissez jusqu'à 4 fichiers ci-dessus.
                    </p>
                    <input type="hidden" name="primary_choice" id="primary_choice" value="{{ $editing ? $vehicle->image : '' }}">
                </div>
            </div>
            <div class="col-12">
                <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px">Description</label>
                <textarea name="description" rows="5" class="form-control form-control-dark">{{ old('description', $vehicle->description) }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <button type="submit" class="btn-volt border-0">{{ $editing ? 'ENREGISTRER' : 'AJOUTER AU CATALOGUE' }}</button>
            <a href="{{ route('admin') }}" class="btn-outline-custom">Annuler</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    var input  = document.getElementById('photosInput');
    var grid   = document.getElementById('photoGrid');
    var hidden = document.getElementById('primary_choice');
    var empty  = document.getElementById('photoEmpty');
    if (!input || !grid || !hidden) return;

    var MAX = 4;

    function existingTiles() { return Array.prototype.slice.call(grid.querySelectorAll('[data-photo]:not([data-new])')); }
    function newTiles()      { return Array.prototype.slice.call(grid.querySelectorAll('[data-new]')); }
    function allBoxes()      { return Array.prototype.slice.call(grid.querySelectorAll('.photo-primary')); }

    function refreshEmpty() {
        if (empty) empty.classList.toggle('d-none', grid.querySelectorAll('[data-photo]').length > 0);
    }

    function selectOnly(box) {
        allBoxes().forEach(function (other) { if (other !== box) other.checked = false; });
        hidden.value = box.checked ? box.value : '';
    }

    function bind(box) {
        box.addEventListener('change', function () {
            if (box.checked) {
                selectOnly(box);
            } else if (hidden.value === box.value) {
                hidden.value = '';
            }
        });
    }

    allBoxes().forEach(bind);

    input.addEventListener('change', function () {
        newTiles().forEach(function (t) { t.remove(); });

        var files = Array.prototype.slice.call(input.files || []);
        var room  = MAX - existingTiles().length;

        if (files.length > Math.max(room, 0)) {
            files = files.slice(0, Math.max(room, 0));
            try {
                var dt = new DataTransfer();
                files.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
            } catch (e) {}
            alert('Maximum ' + MAX + ' photos au total. ' + (room > 0 ? room + ' photo(s) ont été conservées.' : 'Supprimez une photo existante pour en ajouter.'));
        }

        files.forEach(function (file, index) {
            var tile = document.createElement('div');
            tile.className = 'col-6 col-md-3';
            tile.setAttribute('data-photo', '');
            tile.setAttribute('data-new', '');
            tile.setAttribute('data-value', 'new:' + index);

            var wrap = document.createElement('div');
            wrap.className = 'border border-custom p-1 h-100 d-flex flex-column';

            var img = document.createElement('img');
            img.className = 'w-100';
            img.style.height = '90px';
            img.style.objectFit = 'cover';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);

            var check = document.createElement('div');
            check.className = 'form-check d-flex align-items-center justify-content-center gap-1 mt-2 mb-0';

            var box = document.createElement('input');
            box.type = 'checkbox';
            box.className = 'form-check-input photo-primary';
            box.id = 'photo_new_' + index;
            box.value = 'new:' + index;

            var label = document.createElement('label');
            label.className = 'form-check-label small';
            label.setAttribute('for', box.id);
            label.textContent = 'Premier plan';

            check.appendChild(box);
            check.appendChild(label);
            wrap.appendChild(img);
            wrap.appendChild(check);
            tile.appendChild(wrap);
            grid.appendChild(tile);

            bind(box);

            // Sélectionne automatiquement la première nouvelle photo si rien n'est encore choisi.
            if (!hidden.value && index === 0) {
                box.checked = true;
                selectOnly(box);
            }
        });

        refreshEmpty();
    });

    refreshEmpty();
})();
</script>
@endpush

@endsection
