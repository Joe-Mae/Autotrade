@extends('layouts.app')

@section('title', 'Connexion admin — ' . $site['site_name'])

@section('content')
<section class="container py-5" style="max-width:480px">
    <p class="section-label">Administration</p>
    <h1 class="page-title mt-3" style="font-size:clamp(2.5rem,6vw,3.5rem)">CONNEXION</h1>
    <p class="text-muted-custom small text-uppercase mt-3 mb-5" style="letter-spacing:0.08em">
        Accès réservé à l'équipe {{ $site['site_name'] }}
    </p>

    <form method="POST" action="{{ route('login.submit') }}" class="border border-custom bg-panel p-4 p-md-5">
        @csrf
        <div class="mb-3">
            <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px;letter-spacing:0.1em">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-control form-control-dark @error('email') is-invalid @enderror"
                   placeholder="admin@autotrade.fr">
            @error('email')
                <div class="text-danger small mt-2">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="small text-uppercase text-muted-custom fw-bold d-block mb-2" style="font-size:10px;letter-spacing:0.1em">Mot de passe</label>
            <div class="password-field">
                <input type="password" name="password" id="admin-password" required
                       class="form-control form-control-dark"
                       placeholder="••••••••" autocomplete="current-password">
                <button type="button" class="password-toggle" data-password-toggle="admin-password"
                        aria-label="Afficher le mot de passe" aria-pressed="false" title="Afficher le mot de passe">
                    <svg class="password-toggle__eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="password-toggle__eye-off d-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
        </div>
        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
            <label class="form-check-label text-muted-custom" for="remember">Se souvenir de moi</label>
        </div>
        <button type="submit" class="btn-volt w-100 skew-reverse border-0">SE CONNECTER</button>
    </form>

    <script>
        (function () {
            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                var input = document.getElementById(button.getAttribute('data-password-toggle'));
                if (!input) return;

                button.addEventListener('click', function () {
                    var reveal = input.type === 'password';
                    input.type = reveal ? 'text' : 'password';
                    button.setAttribute('aria-pressed', reveal ? 'true' : 'false');
                    button.setAttribute('aria-label', reveal ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
                    button.setAttribute('title', reveal ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
                    button.querySelector('.password-toggle__eye').classList.toggle('d-none', reveal);
                    button.querySelector('.password-toggle__eye-off').classList.toggle('d-none', !reveal);
                    input.focus();
                });

                // La touche Échap remasque le mot de passe.
                input.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && input.type === 'text') {
                        button.click();
                    }
                });
            });
        })();
    </script>
</section>
@endsection
