{{--
    Palette du site.
    Les trois couleurs enregistrées dans l'administration sont converties en
    variables CSS (mode sombre et mode clair) et injectées après les feuilles
    de style : elles prennent donc le dessus sur les valeurs par défaut.
--}}
@php
    $theme = \App\Support\Theme::variables($site);
@endphp
<style>
    :root {
@foreach($theme['dark'] as $name => $value)
        {{ $name }}: {{ $value }};
@endforeach
    }

    :root[data-theme="light"] {
@foreach($theme['light'] as $name => $value)
        {{ $name }}: {{ $value }};
@endforeach
    }
</style>
