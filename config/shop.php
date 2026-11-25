<?php

return [
    // Numéros marchands affichés au client après sa commande (laisser vide = le livreur donne les instructions)
    'wave_number'         => env('WAVE_NUMBER'),
    'orange_money_number' => env('OM_NUMBER'),

    // Délai (en jours après la livraison) pour signaler un problème
    'dispute_days' => 14,

    // Rémunération du livreur par course (FCFA)
    'courier_fee' => (int) env('COURIER_FEE', 1000),

    'payment_methods' => [
        'cash'         => ['label' => 'Espèces à la livraison', 'icon' => 'cash-coin',      'hint' => 'Vous payez le livreur en espèces à la réception.'],
        'wave'         => ['label' => 'Wave',                   'icon' => 'phone-vibrate',  'hint' => 'Paiement mobile Wave à la livraison (ou au numéro marchand).'],
        'orange_money' => ['label' => 'Orange Money',           'icon' => 'phone',          'hint' => 'Paiement mobile Orange Money à la livraison (ou au numéro marchand).'],
    ],
];
