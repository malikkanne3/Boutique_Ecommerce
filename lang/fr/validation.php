<?php

return [
    'accepted'    => 'Le champ :attribute doit être accepté.',
    'confirmed'   => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'email'       => 'Le champ :attribute doit être une adresse e-mail valide.',
    'exists'      => 'La valeur sélectionnée pour :attribute est invalide.',
    'image'       => 'Le champ :attribute doit être une image.',
    'in'          => 'La valeur sélectionnée pour :attribute est invalide.',
    'integer'     => 'Le champ :attribute doit être un nombre entier.',
    'lowercase'   => 'Le champ :attribute doit être en minuscules.',
    'numeric'     => 'Le champ :attribute doit être un nombre.',
    'required'    => 'Le champ :attribute est obligatoire.',
    'required_if' => 'Le champ :attribute est obligatoire dans ce cas.',
    'string'      => 'Le champ :attribute doit être une chaîne de caractères.',
    'unique'      => 'Cette valeur de :attribute est déjà utilisée.',
    'url'         => 'Le champ :attribute doit être un lien valide.',
    'max' => [
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'file'    => 'Le fichier :attribute ne peut pas dépasser :max Ko.',
        'string'  => 'Le champ :attribute ne peut pas dépasser :max caractères.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'file'    => 'Le fichier :attribute doit faire au moins :min Ko.',
        'string'  => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'password' => [
        'letters'       => 'Le mot de passe doit contenir au moins une lettre.',
        'mixed'         => 'Le mot de passe doit contenir une majuscule et une minuscule.',
        'numbers'       => 'Le mot de passe doit contenir au moins un chiffre.',
        'symbols'       => 'Le mot de passe doit contenir au moins un symbole.',
        'uncompromised' => 'Ce mot de passe a fuité publiquement. Choisissez-en un autre.',
    ],
    'attributes' => [
        'name' => 'nom', 'email' => 'adresse e-mail', 'password' => 'mot de passe', 'phone' => 'téléphone',
        'shop_name' => 'nom de la boutique', 'title' => 'titre', 'body' => 'message', 'price' => 'prix',
        'stock' => 'stock', 'category_id' => 'catégorie', 'customer_name' => 'nom', 'customer_phone' => 'téléphone',
        'customer_email' => 'e-mail', 'shipping_address' => 'adresse de livraison', 'audience' => 'destinataires',
        'account_type' => 'type de compte', 'password_mode' => 'mode du mot de passe',
    ],
];
