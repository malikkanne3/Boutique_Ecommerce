<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Informatique
            ['category' => 'Informatique', 'name' => 'Laptop Dell Inspiron 15',     'price' => 450000, 'stock' => 8,  'desc' => 'Intel Core i5, 8Go RAM, SSD 256Go, écran 15.6 pouces Full HD.'],
            ['category' => 'Informatique', 'name' => 'PC Bureau HP EliteDesk',      'price' => 320000, 'stock' => 5,  'desc' => 'Intel Core i3, 4Go RAM, disque dur 1To, Windows 11.'],
            ['category' => 'Informatique', 'name' => 'Souris Logitech MX Master 3', 'price' => 35000,  'stock' => 20, 'desc' => 'Souris sans fil ergonomique, DPI ajustable, autonomie 70 jours.'],
            ['category' => 'Informatique', 'name' => 'Clavier mécanique Corsair',   'price' => 55000,  'stock' => 12, 'desc' => 'Switches Cherry MX Red, rétroéclairage RGB, format TKL.'],
            ['category' => 'Informatique', 'name' => 'Moniteur LG 24" Full HD',     'price' => 125000, 'stock' => 7,  'desc' => 'Dalle IPS, 75Hz, temps de réponse 5ms, ports HDMI + VGA.'],

            // Téléphones
            ['category' => 'Téléphones',   'name' => 'Samsung Galaxy A54',          'price' => 280000, 'stock' => 15, 'desc' => '6.4 pouces Super AMOLED, triple caméra 50MP, batterie 5000mAh.'],
            ['category' => 'Téléphones',   'name' => 'Tecno Spark 20',              'price' => 95000,  'stock' => 25, 'desc' => '6.56 pouces HD+, caméra 50MP, RAM 8Go, batterie 5000mAh.'],
            ['category' => 'Téléphones',   'name' => 'iPhone 13 (reconditionné)',   'price' => 420000, 'stock' => 4,  'desc' => 'Écran Super Retina XDR 6.1 pouces, puce A15 Bionic, double caméra.'],
            ['category' => 'Téléphones',   'name' => 'Infinix Hot 40 Pro',          'price' => 110000, 'stock' => 18, 'desc' => 'Écran 6.78 pouces, 256Go stockage, charge rapide 45W.'],

            // Accessoires
            ['category' => 'Accessoires',  'name' => 'Câble USB-C 2m Braided',      'price' => 4500,   'stock' => 50, 'desc' => 'Câble de charge rapide 65W, tressé nylon, compatible tous appareils USB-C.'],
            ['category' => 'Accessoires',  'name' => 'Chargeur rapide 65W GaN',     'price' => 18000,  'stock' => 30, 'desc' => 'Technologie GaN, 3 ports (2 USB-C + 1 USB-A), charge rapide universelle.'],
            ['category' => 'Accessoires',  'name' => 'Powerbank Baseus 20000mAh',   'price' => 28000,  'stock' => 14, 'desc' => '20000mAh, 2 ports USB-A + 1 USB-C, charge rapide 22.5W.'],
            ['category' => 'Accessoires',  'name' => 'Support téléphone voiture',   'price' => 6500,   'stock' => 40, 'desc' => 'Support magnétique universel, fixation tableau de bord ou grille d\'aération.'],

            // Gaming
            ['category' => 'Gaming',       'name' => 'Manette PS5 DualSense',       'price' => 45000,  'stock' => 10, 'desc' => 'Retour haptique, gâchettes adaptatives, microphone intégré.'],
            ['category' => 'Gaming',       'name' => 'Casque Gaming HyperX Cloud',  'price' => 62000,  'stock' => 6,  'desc' => 'Son surround 7.1, micro détachable, coussinets memory foam.'],
            ['category' => 'Gaming',       'name' => 'Tapis de souris XXL Gaming',  'price' => 12000,  'stock' => 22, 'desc' => 'Surface 900×400mm, base antidérapante, bordures cousues.'],
            ['category' => 'Gaming',       'name' => 'Chaise Gaming Ergonomique',   'price' => 185000, 'stock' => 3,  'desc' => 'Dossier inclinable 180°, accoudoirs 4D, coussin lombaire inclus.'],

            // Audio
            ['category' => 'Audio',        'name' => 'Écouteurs JBL Tune 130NC',    'price' => 38000,  'stock' => 16, 'desc' => 'Réduction de bruit active, autonomie 40h, connexion Bluetooth 5.0.'],
            ['category' => 'Audio',        'name' => 'Enceinte JBL Charge 5',       'price' => 95000,  'stock' => 9,  'desc' => 'Waterproof IP67, autonomie 20h, powerbank intégrée, son 360°.'],
            ['category' => 'Audio',        'name' => 'Casque Sony WH-1000XM4',      'price' => 175000, 'stock' => 5,  'desc' => 'ANC leader du marché, autonomie 30h, Multipoint Bluetooth.'],

            // Électroménager
            ['category' => 'Électroménager','name' => 'Ventilateur Bruhm 16"',      'price' => 35000,  'stock' => 11, 'desc' => '3 vitesses, minuterie, oscillation automatique, silencieux.'],
            ['category' => 'Électroménager','name' => 'Fer à repasser Philips',     'price' => 28000,  'stock' => 14, 'desc' => 'Semelle SteamGlide, vapeur 45g/min, coup de vapeur 200g.'],
            ['category' => 'Électroménager','name' => 'Mixeur Hisense 600W',        'price' => 22000,  'stock' => 0,  'desc' => '600W, 4 lames en inox, bol 1.5L, 3 vitesses + pulse.'],
        ];

        foreach ($products as $data) {
            $category = Category::where('name', $data['category'])->first();
            if (!$category) continue;

            $slug = Str::slug($data['name']);
            // Assurer l'unicité du slug
            $originalSlug = $slug;
            $count = 1;
            while (Product::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }

            Product::firstOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $category->id,
                    'name'        => $data['name'],
                    'description' => $data['desc'],
                    'price'       => $data['price'],
                    'stock'       => $data['stock'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
