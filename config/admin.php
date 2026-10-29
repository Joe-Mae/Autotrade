<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Compte administrateur initial
    |--------------------------------------------------------------------------
    |
    | Utilisé uniquement lors de la première installation (AdminSeeder) et
    | comme valeur par défaut des commandes admin:password / admin:email.
    | Passer par un fichier de configuration (et non env() directement) permet
    | à « php artisan config:cache » de conserver ces valeurs.
    |
    */

    'email' => env('ADMIN_EMAIL', 'admin@autotrade.fr'),

    'password' => env('ADMIN_PASSWORD'),

];
