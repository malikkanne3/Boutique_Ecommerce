<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateurs
        User::firstOrCreate(['email' => 'admin@ecommerce.com'], [
            'name' => 'Admin Principal', 'password' => Hash::make('password'), 'role' => 'admin',
        ]);
        User::firstOrCreate(['email' => 'client@ecommerce.com'], [
            'name' => 'Client Test', 'password' => Hash::make('password'), 'role' => 'client',
        ]);
        User::firstOrCreate(['email' => 'livreur@ecommerce.com'], [
            'name' => 'Livreur Dakar', 'password' => Hash::make('password'), 'role' => 'livreur',
        ]);

        // Catégories + Produits
        $data = [
            'Électronique' => [
                ['Smartphone Samsung Galaxy A54', 185000, 15, 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80', 'Smartphone Android 6.4 pouces, 128Go, 5000mAh'],
                ['Écouteurs Bluetooth Sony', 25000, 30, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', 'Écouteurs sans fil avec réduction de bruit active'],
                ['Tablette Android 10 pouces', 120000, 10, 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&q=80', 'Tablette 10 pouces, 64Go, WiFi + 4G'],
                ['Chargeur Rapide USB-C 65W', 8500, 50, 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80', 'Chargeur rapide compatible tous appareils USB-C'],
                ['Montre Connectée Smartwatch', 45000, 20, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&q=80', 'Montre connectée avec GPS et suivi cardiaque'],
                ['Clé USB 128Go', 6500, 80, 'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?w=400&q=80', 'Clé USB 3.0 haute vitesse 128Go'],
                ['Câble HDMI 2m', 4500, 60, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80', 'Câble HDMI 4K ultra HD 2 mètres'],
                ['Batterie externe 20000mAh', 18000, 25, 'https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?w=400&q=80', 'Powerbank 20000mAh charge rapide double USB'],
                ['Casque Gaming RGB', 35000, 15, 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=400&q=80', 'Casque gaming avec micro et éclairage RGB'],
                ['Webcam HD 1080p', 22000, 18, 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400&q=80', 'Webcam Full HD avec microphone intégré'],
                ['Souris Sans Fil Logitech', 12000, 40, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80', 'Souris ergonomique sans fil longue autonomie'],
            ],
            'Vêtements' => [
                ['T-shirt Coton Premium', 7500, 100, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400&q=80', 'T-shirt 100% coton, disponible en plusieurs couleurs'],
                ['Jean Slim Fit Homme', 22000, 45, 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=400&q=80', 'Jean coupe slim, tissu stretch confortable'],
                ['Robe Wax Africaine', 18000, 25, 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=400&q=80', 'Robe en tissu wax africain, motifs colorés'],
                ['Veste en Jean Femme', 28000, 30, 'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=400&q=80', 'Veste en jean classique coupe droite'],
                ['Chemise Oxford Homme', 15000, 55, 'https://images.unsplash.com/photo-1607345366928-199ea26cfe3e?w=400&q=80', 'Chemise Oxford 100% coton, coupe classique'],
                ['Boubou Grand Boubou', 35000, 20, 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=400&q=80', 'Grand boubou traditionnel tissu brodé'],
                ['Polo Ralph Lauren', 19000, 35, 'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=400&q=80', 'Polo classique en coton piqué'],
                ['Jupe Longue Wax', 14000, 40, 'https://images.unsplash.com/photo-1583496661160-fb5218afa9a3?w=400&q=80', 'Jupe longue en tissu wax coloré'],
                ['Sweat à Capuche', 16000, 50, 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=400&q=80', 'Sweat molletonné avec capuche et poche kangourou'],
                ['Pantalon Chino Beige', 18000, 38, 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=400&q=80', 'Pantalon chino coupe droite 100% coton'],
                ['Dashiki Africain', 12000, 45, 'https://images.unsplash.com/photo-1590735213920-68192a487bc2?w=400&q=80', 'Dashiki traditionnel africain motifs brodés'],
            ],
            'Alimentation' => [
                ['Café Arabica 1kg', 12000, 60, 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400&q=80', 'Café arabica 100% pur, torréfaction artisanale'],
                ['Huile d\'Argan 500ml', 9500, 40, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 'Huile d\'argan pure pressée à froid'],
                ['Thé Vert Menthe 250g', 4500, 80, 'https://images.unsplash.com/photo-1564890369478-c89ca6d9cde9?w=400&q=80', 'Thé vert à la menthe fraîche qualité premium'],
                ['Miel Naturel 500g', 8000, 35, 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=400&q=80', 'Miel pur naturel de fleurs sauvages'],
                ['Quinoa Bio 500g', 5500, 50, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80', 'Quinoa biologique origine Pérou'],
                ['Pâtes Artisanales 500g', 3500, 90, 'https://images.unsplash.com/photo-1551892374-ecf8754cf8b0?w=400&q=80', 'Pâtes aux œufs faites artisanalement'],
                ['Sauce Pimentée Locale', 2500, 100, 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&q=80', 'Sauce pimentée maison recette traditionnelle'],
                ['Riz Parfumé Thaï 5kg', 7500, 45, 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&q=80', 'Riz jasmin thaïlandais grain long parfumé'],
                ['Beurre de Karité 200g', 6000, 55, 'https://images.unsplash.com/photo-1608157336130-621a5d7b1bb1?w=400&q=80', 'Beurre de karité pur naturel non raffiné'],
                ['Dattes Medjool 500g', 11000, 30, 'https://images.unsplash.com/photo-1559181567-c3190bded94e?w=400&q=80', 'Dattes Medjool premium moelleuses et sucrées'],
                ['Huile de Palme 1L', 3000, 70, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 'Huile de palme rouge naturelle traditionnelle'],
            ],
            'Maison & Déco' => [
                ['Coussin Décoratif Wax', 6500, 35, 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&q=80', 'Coussin décoratif 45x45cm tissu wax africain'],
                ['Lampe de Bureau LED', 15000, 20, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=400&q=80', 'Lampe LED réglable 3 températures de couleur'],
                ['Set Vaisselle 6 personnes', 35000, 12, 'https://images.unsplash.com/photo-1584568694244-14fbdf83bd30?w=400&q=80', 'Service de table 24 pièces en céramique'],
                ['Miroir Mural Rotin', 22000, 15, 'https://images.unsplash.com/photo-1618220179428-22790b461013?w=400&q=80', 'Miroir rond en rotin naturel 60cm'],
                ['Plante Artificielle', 8500, 40, 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?w=400&q=80', 'Plante verte artificielle réaliste avec pot'],
                ['Bougie Parfumée Set 3', 9000, 50, 'https://images.unsplash.com/photo-1603905405980-3f0f27839234?w=400&q=80', 'Set de 3 bougies parfumées aux senteurs naturelles'],
                ['Panier en Osier', 12000, 25, 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&q=80', 'Panier tressé en osier naturel multiusage'],
                ['Tableau Africain 40x60', 18000, 18, 'https://images.unsplash.com/photo-1561214115-f2f134cc4912?w=400&q=80', 'Tableau décoratif art africain moderne'],
                ['Horloge Murale Bois', 14000, 22, 'https://images.unsplash.com/photo-1563861826100-9cb868fdbe1c?w=400&q=80', 'Horloge murale en bois massif silencieuse'],
                ['Tapis Berbère 120x180', 45000, 8, 'https://images.unsplash.com/photo-1600166898405-da9535204843?w=400&q=80', 'Tapis berbère traditionnel fait main laine naturelle'],
                ['Vase Céramique Artisanal', 11000, 30, 'https://images.unsplash.com/photo-1612196808214-b8e1d6145a8c?w=400&q=80', 'Vase en céramique fait main motifs géométriques'],
            ],
            'Sport & Fitness' => [
                ['Tapis de Yoga Premium', 14000, 28, 'https://images.unsplash.com/photo-1601925228008-d2a1e7b7ddf5?w=400&q=80', 'Tapis antidérapant 183x61cm épaisseur 6mm'],
                ['Ballon de Football', 11000, 50, 'https://images.unsplash.com/photo-1614632537197-38a17061c2bd?w=400&q=80', 'Ballon taille 5 cuir synthétique haute résistance'],
                ['Gants de Boxe 10oz', 19000, 18, 'https://images.unsplash.com/photo-1549824506-d2413e0c0671?w=400&q=80', 'Gants de boxe cuir synthétique rembourré'],
                ['Haltères 5kg la paire', 16000, 22, 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&q=80', 'Paire d\'haltères en fonte caoutchoutée 5kg'],
                ['Corde à Sauter Pro', 5500, 60, 'https://images.unsplash.com/photo-1601422407692-ad9026f1f4b7?w=400&q=80', 'Corde à sauter avec roulements à billes'],
                ['Bande de Résistance Set', 8500, 45, 'https://images.unsplash.com/photo-1598289431512-b97b0917afac?w=400&q=80', 'Set de 5 bandes élastiques de résistance'],
                ['Gourde Sport 1L', 6000, 70, 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=400&q=80', 'Gourde isotherme inox 1 litre sans BPA'],
                ['Chaussures Running', 42000, 20, 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80', 'Chaussures de running légères amorties'],
                ['Short de Sport', 8000, 55, 'https://images.unsplash.com/photo-1591195853828-11db59a44f43?w=400&q=80', 'Short de sport séchage rapide respirant'],
                ['Vélo Statique Pliable', 95000, 5, 'https://images.unsplash.com/photo-1534258936925-c58bed479fcb?w=400&q=80', 'Vélo d\'appartement pliable résistance réglable'],
                ['Sac de Sport 40L', 15000, 30, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80', 'Sac de sport imperméable grande capacité 40L'],
            ],
            'Beauté & Santé' => [
                ['Crème Hydratante Karité', 7500, 60, 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80', 'Crème hydratante au beurre de karité naturel'],
                ['Huile de Coco 250ml', 5500, 75, 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80', 'Huile de coco vierge pressée à froid'],
                ['Savon Noir Beldi 200g', 4000, 80, 'https://images.unsplash.com/photo-1607006344380-b6775a0824a7?w=400&q=80', 'Savon noir beldi naturel à l\'huile d\'olive'],
                ['Parfum Oud Intense 50ml', 35000, 20, 'https://images.unsplash.com/photo-1541643600914-78b084683702?w=400&q=80', 'Parfum oriental oud intense longue durée'],
                ['Brosse à Dents Électrique', 22000, 25, 'https://images.unsplash.com/photo-1559590056-03f5a1cef33a?w=400&q=80', 'Brosse électrique 3 modes nettoyage profond'],
                ['Masque Cheveux Argan', 8500, 45, 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80', 'Masque capillaire à l\'huile d\'argan réparateur'],
                ['Sérum Vitamine C', 18000, 30, 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&q=80', 'Sérum éclat vitamine C anti-taches concentré'],
                ['Kit Manucure Complet', 12000, 35, 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&q=80', 'Kit manucure professionnel 15 accessoires'],
                ['Encens Naturel Set', 5000, 55, 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?w=400&q=80', 'Set d\'encens naturels bois de santal et rose'],
                ['Tondeuse Barbe Pro', 28000, 18, 'https://images.unsplash.com/photo-1622296089863-eb7fc530daa8?w=400&q=80', 'Tondeuse barbe professionnelle 20 hauteurs'],
                ['Lotion Corps Karité', 6500, 65, 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80', 'Lotion corps légère karité et aloe vera'],
            ],
            'Informatique' => [
                ['Ordinateur Portable HP', 450000, 8, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&q=80', 'Laptop HP 15 pouces Intel i5 8Go RAM 512Go SSD'],
                ['Souris Gaming RGB', 18000, 30, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80', 'Souris gaming 7200 DPI RGB personnalisable'],
                ['Clavier Mécanique', 35000, 15, 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?w=400&q=80', 'Clavier mécanique rétroéclairé switches bleus'],
                ['Écran 24 pouces Full HD', 85000, 10, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80', 'Moniteur 24 pouces IPS Full HD 75Hz'],
                ['Disque Dur Externe 1To', 32000, 20, 'https://images.unsplash.com/photo-1597048106065-f66de5e4ab8a?w=400&q=80', 'HDD externe 1To USB 3.0 portable'],
                ['Hub USB-C 7 en 1', 22000, 25, 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&q=80', 'Hub USB-C avec HDMI USB 3.0 SD card'],
                ['Tapis de Souris XXL', 8500, 40, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400&q=80', 'Grand tapis de bureau 80x30cm antidérapant'],
                ['Casque Audio Studio', 48000, 12, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&q=80', 'Casque studio monitoring haute fidélité'],
                ['Imprimante Laser', 120000, 6, 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400&q=80', 'Imprimante laser monochrome WiFi recto-verso'],
                ['Routeur WiFi 6', 55000, 10, 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=400&q=80', 'Routeur WiFi 6 double bande 3000Mbps'],
                ['Webcam 4K', 45000, 8, 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400&q=80', 'Webcam 4K Ultra HD avec mise au point auto'],
            ],
            'Livres & Éducation' => [
                ['Français Facile Débutant', 8500, 40, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&q=80', 'Manuel d\'apprentissage du français pour débutants'],
                ['Mathématiques Terminale', 12000, 35, 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400&q=80', 'Cours complet mathématiques terminale avec exercices'],
                ['Atlas du Monde 2024', 18000, 20, 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=400&q=80', 'Atlas géographique mondial édition 2024'],
                ['Apprendre l\'Anglais', 9500, 50, 'https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=400&q=80', 'Guide complet apprentissage anglais avec CD'],
                ['Histoire de l\'Afrique', 15000, 25, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400&q=80', 'Histoire complète du continent africain des origines à nos jours'],
                ['Physique Chimie Lycée', 11000, 30, 'https://images.unsplash.com/photo-1532012197267-da84d127e765?w=400&q=80', 'Physique-Chimie seconde et première exercices corrigés'],
                ['Dictionnaire Larousse', 14000, 28, 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400&q=80', 'Dictionnaire Larousse illustré édition complète'],
                ['Roman Africain Contemporain', 7500, 45, 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=400&q=80', 'Collection romans africains contemporains'],
                ['Guide Entrepreneuriat', 13000, 22, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80', 'Guide pratique pour créer son entreprise en Afrique'],
                ['Cahier d\'Exercices CP', 3500, 80, 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&q=80', 'Cahier d\'exercices cours préparatoire lecture écriture'],
                ['Livre de Cuisine Africaine', 11000, 35, 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?w=400&q=80', '100 recettes traditionnelles africaines illustrées'],
            ],
        ];

        foreach ($data as $categoryName => $products) {
            $slug = \Illuminate\Support\Str::slug($categoryName);
            $category = Category::firstOrCreate(
                ['slug' => $slug],
                ['name' => $categoryName]
            );

            foreach ($products as [$name, $price, $stock, $image, $description]) {
                Product::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($name) . '-' . substr(md5($name), 0, 6)],
                    [
                        'category_id' => $category->id,
                        'name'        => $name,
                        'price'       => $price,
                        'stock'       => $stock,
                        'image'       => $image,
                        'description' => $description,
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}