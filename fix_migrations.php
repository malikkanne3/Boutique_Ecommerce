<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Catégories
$cat1 = \App\Models\Category::create(['name' => 'Électronique', 'slug' => 'electronique']);
$cat2 = \App\Models\Category::create(['name' => 'Vêtements', 'slug' => 'vetements']);
$cat3 = \App\Models\Category::create(['name' => 'Alimentation', 'slug' => 'alimentation']);

// Produits
\App\Models\Product::create(['name' => 'Smartphone XL', 'description' => 'Téléphone haute performance.', 'price' => 150000, 'stock' => 10, 'is_active' => true, 'category_id' => $cat1->id]);
\App\Models\Product::create(['name' => 'Casque Bluetooth', 'description' => 'Son crystal clear.', 'price' => 25000, 'stock' => 20, 'is_active' => true, 'category_id' => $cat1->id]);
\App\Models\Product::create(['name' => 'T-shirt Premium', 'description' => 'Coton bio 100%.', 'price' => 8000, 'stock' => 50, 'is_active' => true, 'category_id' => $cat2->id]);
\App\Models\Product::create(['name' => 'Jean Slim', 'description' => 'Coupe moderne.', 'price' => 15000, 'stock' => 30, 'is_active' => true, 'category_id' => $cat2->id]);
\App\Models\Product::create(['name' => 'Café Premium', 'description' => 'Arabica 100%.', 'price' => 5000, 'stock' => 100, 'is_active' => true, 'category_id' => $cat3->id]);

echo "OK donnees ajoutees\n";