<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        try {
            const savedTheme = localStorage.getItem('autotrade-theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                document.documentElement.dataset.theme = savedTheme;
            }
        } catch (error) {}
    </script>
    <title>@yield('title', 'Administration — ' . $site['site_name'])</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    @include('partials.theme')
    @stack('head')
</head>
<body class="admin-body">
@php
    $section = trim($__env->yieldContent('admin_section')) ?: 'flotte';
    $badges = $adminBadges ?? [
        'vehicles' => 0,
        'pending_purchases' => 0,
        'unread_messages' => 0,
        'latest_message_id' => 0,
        'latest_purchase_id' => 0,
    ];
    $sections = [
        'gestion' => 'Gestion',
        'flotte' => 'Flotte',
        'clients' => 'Clients',
        'ventes' => 'Ventes',
    ];
    $initials = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $site['site_name']), 0, 2)) ?: 'AA';
@endphp
<div class="admin-toast-rack" id="adminToastRack" aria-live="polite"></div>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="admin-sidebar__brand" href="{{ route('admin') }}">
            @if(!empty($site['logo']))
                <img src="{{ asset($site['logo']) }}" alt="{{ $site['site_name'] }}" class="brand-logo">
            @else
                <span class="logo-mark">A</span>
            @endif
            <span class="font-display">{{ $site['site_name'] }}</span>
        </a>

        <p class="admin-sidebar__label">Espace administration</p>

        <nav class="admin-nav">
            <a class="admin-nav__item {{ $section === 'gestion' ? 'is-active' : '' }}" href="{{ route('admin.settings') }}">
                <span class="admin-nav__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/><circle cx="9" cy="7" r="2"/><circle cx="15" cy="12" r="2"/><circle cx="8" cy="17" r="2"/></svg>
                </span>
                Gestion {{ $site['site_name'] }}
            </a>
            <a class="admin-nav__item {{ $section === 'flotte' ? 'is-active' : '' }}" href="{{ route('admin') }}#flotte" data-admin-link="flotte">
                <span class="admin-nav__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 13l1.6-4.8A2 2 0 0 1 8 6.8h8a2 2 0 0 1 1.9 1.4L19.5 13"/><path d="M3.5 13h17v4h-17z"/><circle cx="7.5" cy="17.5" r="1.4"/><circle cx="16.5" cy="17.5" r="1.4"/></svg>
                </span>
                Flotte
                <span class="admin-nav__badge">{{ str_pad((string) $badges['vehicles'], 2, '0', STR_PAD_LEFT) }}</span>
            </a>
            <a class="admin-nav__item {{ $section === 'clients' ? 'is-active' : '' }}" href="{{ route('admin') }}#clients" data-admin-link="clients">
                <span class="admin-nav__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3.5 20a5.5 5.5 0 0 1 11 0"/><path d="M16 5.5a3 3 0 0 1 0 5"/><path d="M20.5 20a5 5 0 0 0-3.8-4.8"/></svg>
                </span>
                Clients
                @if($badges['unread_messages'] > 0)
                    <span class="admin-nav__badge">{{ $badges['unread_messages'] }}</span>
                @endif
            </a>
            <a class="admin-nav__item {{ $section === 'ventes' ? 'is-active' : '' }}" href="{{ route('admin') }}#ventes" data-admin-link="ventes">
                <span class="admin-nav__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-1.8-3 1.8-3-1.8L6 21z"/><path d="M9 8h6M9 12h6"/></svg>
                </span>
                Ventes
                @if($badges['pending_purchases'] > 0)
                    <span class="admin-nav__badge">{{ $badges['pending_purchases'] }}</span>
                @endif
            </a>
        </nav>

        <div class="admin-sidebar__foot">
            <a class="admin-sidebar__link" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></svg>
                Retour au site public
            </a>
        </div>

        <div class="admin-user">
            <span class="admin-user__avatar">{{ $initials }}</span>
            <div>
                <div class="admin-user__name">{{ auth()->user()->name ?? 'Administrateur' }}</div>
                <div class="admin-user__role">{{ $site['site_name'] }} — Administration</div>
            </div>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-breadcrumb">
                <span>Administration</span>
                <span class="sep">›</span>
                <strong data-admin-breadcrumb>{{ $sections[$section] ?? 'Flotte' }}</strong>
            </div>
            <div class="admin-topbar__right">
                <button type="button" class="admin-icon-btn" id="adminThemeToggle" title="Basculer le thème" aria-label="Basculer le thème">
                    <span aria-hidden="true">◐</span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="admin-logout">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M10 4H6a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h4"/><path d="M16 8l4 4-4 4"/><path d="M20 12H9"/></svg>
                        Déconnexion
                    </button>
                </form>
                <a class="admin-icon-btn" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" title="Voir le site public" aria-label="Voir le site public">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6"/><path d="M20 4l-9 9"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/></svg>
                </a>
            </div>
        </header>

        <main class="admin-content">
            @if(session('success'))
                <div class="admin-flash">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="admin-flash admin-flash--danger">{{ session('error') }}</div>
            @endif

            @yield('content')
        </main>

        <footer class="admin-foot">
            <span>{{ $site['site_name'] }} / Administration</span>
            <span>Précision. Performance. Contrôle.</span>
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var toggle = document.getElementById('adminThemeToggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                var next = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
                document.documentElement.dataset.theme = next;
                try { localStorage.setItem('autotrade-theme', next); } catch (e) {}
            });
        }
    })();
</script>
@include('admin.partials.alerts')
@stack('scripts')
</body>
</html>
