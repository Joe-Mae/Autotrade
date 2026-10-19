@php
    $prix = $vehicle->prix_vente !== null ? \App\Models\Vehicle::cfa($vehicle->prix_vente) : 'Sur devis';
@endphp
<div class="col-md-6 col-lg-4">
    <a href="{{ route('vehicule.show', $vehicle) }}" class="vehicle-card" aria-label="Voir le détail de {{ $vehicle->nom }}">
        <div class="overflow-hidden">
            <img src="{{ asset($vehicle->image) }}" alt="{{ $vehicle->nom }} à vendre — {{ $vehicle->categorie }}" loading="lazy">
        </div>
        <div class="p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="{{ $vehicle->disponible ? 'badge-dispo' : 'badge-indispo' }}">
                    {{ $vehicle->badge_label }}
                </span>
                <span class="small text-muted-custom text-uppercase fw-bold">{{ $vehicle->etat }} · {{ $vehicle->carburant }}</span>
            </div>
            <h3 class="font-display fs-3 mb-1">{{ $vehicle->nom }}</h3>
            <p class="small text-uppercase text-muted-custom mb-0" style="letter-spacing:0.1em">
                {{ $vehicle->marque }} · {{ $vehicle->annee }} · {{ $vehicle->puissance }} · {{ $vehicle->km }}
            </p>
            <div class="d-flex align-items-center justify-content-between border-top border-custom pt-4 mt-4">
                <div>
                    <span class="d-block text-muted-custom" style="font-size:10px;text-transform:uppercase;letter-spacing:0.1em">
                        Prix de vente
                    </span>
                    <span class="font-display fs-3 text-volt lh-1">
                        {{ $prix }}
                    </span>
                </div>
                <span class="card-plus" aria-hidden="true">+</span>
            </div>
        </div>
    </a>
</div>
