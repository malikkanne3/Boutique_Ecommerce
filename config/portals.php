<?php

/*
| Liens secrets des espaces professionnels.
| Par défaut : générés à partir de APP_KEY (stables, impossibles à deviner).
| Pour imposer vos propres liens, ajoutez dans .env :
|   PORTAL_ADMIN=mon-lien-admin   PORTAL_VENDEUR=...   PORTAL_LIVREUR=...
| Affichez-les avec :  php artisan portails:liens
*/
$key  = (string) env('APP_KEY', 'eshop-sn');
$make = fn (string $role) => substr(hash_hmac('sha256', 'portal-' . $role, $key), 0, 24);

return [
    'admin'   => env('PORTAL_ADMIN') ?: $make('admin'),
    'vendeur' => env('PORTAL_VENDEUR') ?: $make('vendeur'),
    'livreur' => env('PORTAL_LIVREUR') ?: $make('livreur'),
];
