<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Informatique',  'description' => 'PC, laptops, composants et périphériques.'],
            ['name' => 'Téléphones',    'description' => 'Smartphones et accessoires téléphonie.'],
            ['name' => 'Accessoires',   'description' => 'Câbles, chargeurs, housses et plus.'],
            ['name' => 'Gaming',        'description' => 'Consoles, jeux vidéo et équipements gaming.'],
            ['name' => 'Audio',         'description' => 'Écouteurs, casques, enceintes Bluetooth.'],
            ['name' => 'Électroménager','description' => 'Appareils pour la maison et la cuisine.'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                ['name' => $data['name'], 'description' => $data['description']]
            );
        }
    }
}
