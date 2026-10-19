@extends('layouts.admin')

@section('title', 'Administration — ' . $site['site_name'])
@section('admin_section', 'flotte')

@section('content')
{{-- ===================================================================== FLOTTE --}}
<section class="admin-section" data-admin-section="flotte">
    <div class="admin-head">
        <div>
            <p class="section-label mb-0">Le contrôle, sans compromis.</p>
            <h1 class="admin-title">Votre flotte<span class="text-volt">.</span></h1>
            <p class="admin-subtitle">Tous vos véhicules. Une seule vue.</p>
        </div>
        <a href="{{ route('admin.vehicles.create') }}" class="btn-volt admin-add">+ Ajouter un véhicule</a>
    </div>

    {{-- Statistiques --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card__label">Véhicules en flotte</div>
            <span class="stat-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 13l1.6-4.8A2 2 0 0 1 8 6.8h8a2 2 0 0 1 1.9 1.4L19.5 13"/><path d="M3.5 13h17v4h-17z"/><circle cx="7.5" cy="17.5" r="1.4"/><circle cx="16.5" cy="17.5" r="1.4"/></svg>
            </span>
            <div class="stat-card__value">{{ str_pad((string) $stats['flotte'], 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-card__foot"><span class="dot"></span> {{ $stats['disponibles'] }} disponible{{ $stats['disponibles'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Clients</div>
            <span class="stat-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 5.5a3 3 0 0 1 0 5"/><path d="M20.5 20a5 5 0 0 0-3.8-4.8"/></svg>
            </span>
            <div class="stat-card__value">{{ str_pad((string) $stats['clients'], 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-card__foot"><span class="dot"></span> {{ $stats['demandes_achat'] }} demande{{ $stats['demandes_achat'] > 1 ? 's' : '' }} d'achat</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Ventes signées</div>
            <span class="stat-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-1.8-3 1.8-3-1.8L6 21z"/><path d="M9 8h6M9 12h6"/></svg>
            </span>
            <div class="stat-card__value stat-card__value--sm">{{ \App\Models\Vehicle::cfa($stats['montant_signe']) }}</div>
            <div class="stat-card__foot"><span class="dot"></span> {{ $stats['signees'] }} dossier{{ $stats['signees'] > 1 ? 's' : '' }} signé{{ $stats['signees'] > 1 ? 's' : '' }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-card__label">Demandes à traiter</div>
            <span class="stat-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
            </span>
            <div class="stat-card__value">{{ str_pad((string) $stats['pending'], 2, '0', STR_PAD_LEFT) }}</div>
            <div class="stat-card__foot"><span class="dot"></span> {{ $stats['pending'] > 0 ? $stats['pending'].' à valider' : 'à jour' }}</div>
        </div>
    </div>

    {{-- Inventaire --}}
    <div class="panel">
        <div class="panel__head">
            <h2 class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M4 12h10"/><path d="M4 17h7"/><circle cx="18" cy="15" r="3"/></svg>
                Inventaire des véhicules
                <span class="panel__count">{{ $stats['flotte'] }}</span>
            </h2>
            <div class="panel__tools">
                <a href="{{ route('admin.vehicles.export') }}" class="panel__link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 4v11"/><path d="M8 11l4 4 4-4"/><path d="M4 20h16"/></svg>
                    Exporter
                </a>
            </div>
        </div>

        <div class="inv-bar">
            <label class="inv-search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M20 20l-4-4"/></svg>
                <input type="search" id="invSearch" placeholder="Rechercher un véhicule, une marque…" autocomplete="off">
            </label>
            <div class="inv-select">
                <select id="invCategorie" aria-label="Filtrer par catégorie">
                    <option value="all">Toutes les catégories</option>
                    @foreach(['Super Sport', 'Luxe & Confort', 'Électrique', 'SUV'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="veh-bulk-bar" id="vehBulkBar" style="display:none">
            <span class="aside-note"><strong class="text-volt" id="vehBulkCount">0</strong> véhicule(s) sélectionné(s)</span>
            <button type="button" id="vehBulkDelete" class="bulk-del">Supprimer la sélection</button>
            <button type="button" id="vehBulkClear" class="bulk-clear">Tout décocher</button>
        </div>

        <form method="POST" action="{{ route('admin.vehicles.bulk') }}" id="vehBulkForm" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        <div class="at-table-wrap">
            <table class="at-table">
                <thead>
                    <tr>
                        <th class="col-check"><input type="checkbox" id="vehCheckAll" class="at-check" aria-label="Tout sélectionner"></th>
                        <th>Véhicule</th>
                        <th>Catégorie</th>
                        <th>État</th>
                        <th>Prix de vente</th>
                        <th>Disponibilité</th>
                        <th class="col-actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="invBody">
                    @forelse($vehicles as $v)
                        <tr data-row data-categorie="{{ $v->categorie }}" data-search="{{ strtolower($v->nom.' '.$v->marque) }}">
                            <td class="col-check">
                                <input type="checkbox" class="at-check veh-check" value="{{ $v->id }}" data-name="{{ $v->nom }}" aria-label="Sélectionner {{ $v->nom }}">
                            </td>
                            <td>
                                <a class="at-vehicle" href="{{ route('vehicule.show', $v) }}" target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset($v->image) }}" alt="{{ $v->nom }}">
                                    <span>
                                        <span class="at-vehicle__name">{{ $v->nom }}</span>
                                        <span class="at-vehicle__meta d-block">{{ $v->marque }} · {{ $v->annee }}</span>
                                    </span>
                                </a>
                            </td>
                            <td>{{ $v->categorie }}</td>
                            <td>{{ $v->etat }}</td>
                            <td style="color:var(--bone)">{{ $v->prix_vente !== null ? \App\Models\Vehicle::cfa($v->prix_vente) : '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.vehicles.toggle', $v) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="at-status {{ $v->disponible ? '' : 'is-vendu' }}" title="{{ $v->disponible ? 'Marquer comme vendu' : 'Remettre en vente' }}">
                                        <span class="dot"></span> {{ $v->disponible ? 'Disponible' : 'Vendu' }}
                                    </button>
                                </form>
                            </td>
                            <td class="col-actions">
                                <div class="at-actions">
                                    <a href="{{ route('admin.vehicles.edit', $v) }}" class="at-action" title="Modifier" aria-label="Modifier {{ $v->nom }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h4L19 9l-4-4L4 16z"/><path d="M14 6l4 4"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.vehicles.destroy', $v) }}" onsubmit="return confirm('Supprimer {{ $v->nom }} ?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="at-action at-action--danger" title="Supprimer" aria-label="Supprimer {{ $v->nom }}">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"/><path d="M9 7V5h6v2"/><path d="M6 7l1 13h10l1-13"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="at-empty">Aucun véhicule en vente pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="at-foot" id="invCount">{{ $stats['flotte'] }} résultat{{ $stats['flotte'] > 1 ? 's' : '' }}</div>
    </div>

    {{-- Bas de page : à traiter + disponibilité --}}
    <div class="admin-columns">
        <div class="panel">
            <div class="panel__head">
                <h2 class="panel__title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    À traiter
                </h2>
                <span class="aside-note">{{ $stats['pending'] }} demande(s)</span>
            </div>
            <div class="request-list">
                @forelse($pendingPurchases as $p)
                    <div class="request-item">
                        <div>
                            <div class="request-item__ref">Achat · VTE-{{ str_pad((string) $p->id, 4, '0', STR_PAD_LEFT) }}</div>
                            <div class="request-item__name">{{ $p->vehicle?->nom ?? 'Véhicule supprimé' }}</div>
                            <div class="request-item__client">{{ $p->nom }}</div>
                        </div>
                        <button type="button" class="btn-ghost" data-goto-section="ventes">
                            Examiner
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                @empty
                    <div class="at-empty">Aucune demande en attente. Tout est à jour.</div>
                @endforelse
            </div>
        </div>

        <div class="avail">
            <div class="avail__label">Disponibilité de la flotte</div>
            <div class="avail__value">{{ $stats['disponibles'] }} <small>/ {{ $stats['flotte'] }}</small></div>
            <div class="segbar" aria-hidden="true">
                @for($i = 0; $i < max($stats['flotte'], 1); $i++)
                    <span class="{{ $i < $stats['disponibles'] ? 'is-on' : '' }}"></span>
                @endfor
            </div>
            <div class="avail__legend">
                <span>{{ $stats['disponibles'] }} disponible{{ $stats['disponibles'] > 1 ? 's' : '' }}</span>
                <span>{{ $stats['vendus'] }} vendu{{ $stats['vendus'] > 1 ? 's' : '' }}</span>
            </div>
            <a href="{{ route('catalogue') }}" target="_blank" rel="noopener noreferrer" class="avail__link">
                Voir les véhicules disponibles
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ===================================================================== CLIENTS --}}
<section class="admin-section d-none" data-admin-section="clients">
    <div class="admin-head">
        <div>
            <p class="section-label mb-0">Vos visiteurs.</p>
            <h1 class="admin-title">Clients<span class="text-volt">.</span></h1>
            <p class="admin-subtitle">Les messages reçus depuis le site.</p>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head">
            <h2 class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v12H8l-4 4z"/></svg>
                Messages
                <span class="panel__count">{{ $messages->count() }}</span>
            </h2>
            <span class="aside-note">{{ $stats['messages_non_lus'] }} non lu(s)</span>
        </div>
        <div class="at-table-wrap">
            <table class="at-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Message</th>
                        <th class="col-actions">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $m)
                        <tr>
                            <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td style="color:var(--bone)">
                                {{ $m->nom }}
                                @unless($m->lu)<span class="at-badge at-badge--dispo ms-2">Nouveau</span>@endunless
                            </td>
                            <td>{{ $m->email }}</td>
                            <td style="max-width:320px">{{ $m->message }}</td>
                            <td class="col-actions">
                                @unless($m->lu)
                                    <form method="POST" action="{{ route('admin.messages.read', $m) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="panel__link">Marquer lu</button>
                                    </form>
                                @else
                                    <span class="aside-note">Lu</span>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="at-empty">Aucun message pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- ===================================================================== VENTES --}}
<section class="admin-section d-none" data-admin-section="ventes">
    <div class="admin-head">
        <div>
            <p class="section-label mb-0">Le suivi commercial.</p>
            <h1 class="admin-title">Ventes<span class="text-volt">.</span></h1>
            <p class="admin-subtitle">{{ $stats['demandes_achat'] }} demande(s) d'achat · {{ $stats['signees'] }} signée(s).</p>
        </div>
    </div>

    <div class="panel">
        <div class="panel__head">
            <h2 class="panel__title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-1.8-3 1.8-3-1.8L6 21z"/><path d="M9 8h6M9 12h6"/></svg>
                Demandes d'achat
                <span class="panel__count">{{ $purchases->count() }}</span>
            </h2>
        </div>
        <div class="at-table-wrap">
            <table class="at-table">
                <thead>
                    <tr>
                        <th>Réf.</th>
                        <th>Véhicule</th>
                        <th>Client</th>
                        <th>Projet</th>
                        <th>Prix affiché</th>
                        <th class="col-actions">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td>VTE-{{ str_pad((string) $p->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td style="color:var(--bone)">{{ $p->vehicle?->nom ?? '—' }}</td>
                            <td>
                                {{ $p->nom }}
                                <span class="at-vehicle__meta d-block">{{ $p->email }} · {{ $p->telephone }}</span>
                            </td>
                            <td>{{ $p->interet ?? '—' }}</td>
                            <td>{{ $p->vehicle && $p->vehicle->prix_vente !== null ? \App\Models\Vehicle::cfa($p->vehicle->prix_vente) : '—' }}</td>
                            <td class="col-actions">
                                <form method="POST" action="{{ route('admin.purchases.status', $p) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <div class="inv-select" style="flex:none;min-width:150px">
                                        <select name="statut" onchange="this.form.submit()">
                                            @foreach(['en_attente' => 'En attente', 'en_cours' => 'En cours', 'signee' => 'Signée', 'refusee' => 'Refusée'] as $s => $label)
                                                <option value="{{ $s }}" @selected($p->statut === $s)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="at-empty">Aucune demande d'achat pour le moment.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    // --- Navigation entre les sections -------------------------------------
    var labels = { flotte: 'Flotte', clients: 'Clients', ventes: 'Ventes', gestion: 'Gestion' };
    var sections = Array.prototype.slice.call(document.querySelectorAll('[data-admin-section]'));
    if (sections.length) {
        var show = function (name) {
            if (!labels[name]) name = 'flotte';
            sections.forEach(function (s) { s.classList.toggle('d-none', s.dataset.adminSection !== name); });
            document.querySelectorAll('[data-admin-link]').forEach(function (a) {
                a.classList.toggle('is-active', a.dataset.adminLink === name);
            });
            var bc = document.querySelector('[data-admin-breadcrumb]');
            if (bc) bc.textContent = labels[name];
            if (window.history && history.replaceState) history.replaceState(null, '', '#' + name);
        };

        document.querySelectorAll('[data-admin-link]').forEach(function (a) {
            a.addEventListener('click', function (e) { e.preventDefault(); show(a.dataset.adminLink); });
        });
        document.querySelectorAll('[data-goto-section]').forEach(function (b) {
            b.addEventListener('click', function () { show(b.dataset.gotoSection); });
        });

        show((location.hash || '').replace('#', ''));
    }

    // --- Inventaire : recherche, filtre et sélection multiple --------------
    var search = document.getElementById('invSearch');
    var select = document.getElementById('invCategorie');
    var tbody = document.getElementById('invBody');
    var foot = document.getElementById('invCount');
    if (!tbody) return;

    var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr[data-row]'));
    var total = rows.length;

    var boxOf = function (r) { return r.querySelector('.veh-check'); };
    var visibleRows = function () {
        return rows.filter(function (r) { return r.style.display !== 'none'; });
    };

    var bar = document.getElementById('vehBulkBar');
    var counter = document.getElementById('vehBulkCount');
    var all = document.getElementById('vehCheckAll');
    var form = document.getElementById('vehBulkForm');
    var del = document.getElementById('vehBulkDelete');
    var clear = document.getElementById('vehBulkClear');

    function refreshBulk() {
        if (!bar) return;
        var checked = rows.map(boxOf).filter(function (c) { return c && c.checked; });
        if (counter) counter.textContent = checked.length;
        bar.style.display = checked.length ? 'flex' : 'none';

        var vis = visibleRows().map(boxOf).filter(Boolean);
        var visChecked = vis.filter(function (c) { return c.checked; }).length;
        if (all) {
            all.checked = vis.length > 0 && visChecked === vis.length;
            all.indeterminate = visChecked > 0 && visChecked < vis.length;
        }
    }

    function applyFilters() {
        var q = (search && search.value || '').toLowerCase().trim();
        var cat = select ? select.value : 'all';
        rows.forEach(function (r) {
            var okQ = !q || (r.dataset.search || '').indexOf(q) !== -1;
            var okC = cat === 'all' || r.dataset.categorie === cat;
            r.style.display = (okQ && okC) ? '' : 'none';
        });
        if (foot) {
            var n = visibleRows().length;
            foot.textContent = n + (n > 1 ? ' résultats' : ' résultat') + ' sur ' + total;
        }
        refreshBulk();
    }

    if (search) search.addEventListener('input', applyFilters);
    if (select) select.addEventListener('change', applyFilters);

    if (all) all.addEventListener('change', function () {
        visibleRows().forEach(function (r) { var c = boxOf(r); if (c) c.checked = all.checked; });
        refreshBulk();
    });

    rows.forEach(function (r) { var c = boxOf(r); if (c) c.addEventListener('change', refreshBulk); });

    if (clear) clear.addEventListener('click', function () {
        rows.forEach(function (r) { var c = boxOf(r); if (c) c.checked = false; });
        if (all) { all.checked = false; all.indeterminate = false; }
        refreshBulk();
    });

    if (del) del.addEventListener('click', function () {
        var boxes = rows.map(boxOf).filter(function (c) { return c && c.checked; });
        if (!boxes.length) return;
        var names = boxes.slice(0, 3).map(function (c) { return c.dataset.name; }).join(', ');
        var label = 'Supprimer définitivement ' + boxes.length + ' véhicule(s) ?\n(' + names + (boxes.length > 3 ? ', …' : '') + ')\nCette action est irréversible.';
        if (!confirm(label)) return;
        form.querySelectorAll('input[name="ids[]"]').forEach(function (i) { i.remove(); });
        boxes.forEach(function (c) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = c.value;
            form.appendChild(inp);
        });
        form.submit();
    });

    applyFilters();
})();
</script>
@endpush
