<header class="site-header">
    <nav class="navbar navbar-expand-md">
        <div class="container" style="max-width:1280px">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if(!empty($site['logo']))
                    <img src="{{ asset($site['logo']) }}" alt="{{ $site['site_name'] }}" class="brand-logo">
                @else
                    <span class="logo-mark">A</span>
                @endif
                <span class="font-display fs-3">{{ $site['site_name'] }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav mx-auto gap-md-4">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('catalogue') ? 'active' : '' }}" href="{{ route('catalogue') }}">Véhicules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Nous contacter</a>
                    </li>
                </ul>
                @auth
                    {{-- Liens réservés à l'administrateur connecté (pas de lien « Admin » pour le public). --}}
                    <div class="d-flex align-items-center gap-2 ms-md-3">
                        <a href="{{ route('admin') }}" target="_blank" rel="noopener noreferrer" class="btn-admin">Admin</a>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-outline-custom border-0 py-2 px-3" style="font-size:11px;clip-path:none">Déconnexion</button>
                        </form>
                    </div>
                @endauth
            </div>
            <button type="button" class="btn-theme-toggle ms-md-3" id="themeToggle" aria-label="Mode clair" title="Mode clair">
                <span class="theme-toggle-icon" aria-hidden="true">◐</span>
                <span id="themeToggleLabel">Mode clair</span>
            </button>
        </div>
    </nav>
</header>
