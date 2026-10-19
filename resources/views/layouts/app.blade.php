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
    <title>@yield('title', $site['site_name'] . ' — ' . $site['site_tagline'])</title>
    <meta name="description" content="@yield('description', $site['site_tagline'] . ' — supercars, berlines de luxe, SUV et électriques. Garantie 12 mois, financement et reprise.')">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('head')
</head>
<body>
    @include('partials.header')

    @if(session('success') && ! session('popup'))
        <div class="container pt-3" style="max-width:1280px">
            <div class="success-msg">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="container pt-3" style="max-width:1280px">
            <div class="alert alert-danger rounded-0">{{ session('error') }}</div>
        </div>
    @endif

    @yield('content')

    @include('partials.footer')

    @if($popup = session('popup'))
        <div class="confirm-modal is-open" id="confirmModal" role="dialog" aria-modal="true" aria-labelledby="confirmModalTitle">
            <div class="confirm-modal-backdrop" data-close></div>
            <div class="confirm-modal-panel">
                <div class="confirm-modal-icon" aria-hidden="true">✓</div>
                <p class="section-label mb-2">Merci !</p>
                <h2 class="font-display fs-2 text-volt mb-3" id="confirmModalTitle">
                    {{ is_array($popup) ? ($popup['title'] ?? 'Confirmation') : 'Confirmation' }}
                </h2>
                <p class="text-muted-custom mb-4">
                    {{ is_array($popup) ? ($popup['message'] ?? '') : $popup }}
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <button type="button" class="btn-volt border-0" data-close>FERMER</button>
                    <a href="{{ route('catalogue') }}" class="btn-outline-custom text-decoration-none">VOIR LE CATALOGUE</a>
                </div>
            </div>
        </div>
        <script>
            (function () {
                var modal = document.getElementById('confirmModal');
                if (!modal) return;
                function close() { modal.classList.remove('is-open'); }
                modal.querySelectorAll('[data-close]').forEach(function (el) { el.addEventListener('click', close); });
                document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
            })();
        </script>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleLabel = document.getElementById('themeToggleLabel');

        function updateThemeToggle() {
            const nextTheme = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
            themeToggleLabel.textContent = `Mode ${nextTheme === 'light' ? 'clair' : 'sombre'}`;
            themeToggle.setAttribute('aria-label', `Activer le mode ${nextTheme === 'light' ? 'clair' : 'sombre'}`);
            themeToggle.title = `Activer le mode ${nextTheme === 'light' ? 'clair' : 'sombre'}`;
        }

        updateThemeToggle();
        themeToggle.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'light' ? 'dark' : 'light';
            document.documentElement.dataset.theme = nextTheme;
            try {
                localStorage.setItem('autotrade-theme', nextTheme);
            } catch (error) {}
            updateThemeToggle();
        });
    </script>
    @stack('scripts')
</body>
</html>
