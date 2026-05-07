<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class FixProductImages extends Command
{
    protected $signature = 'products:fix-images';
    protected $description = 'Fix broken product images';

    public function handle()
    {
        $images = [
            // Électronique
            'Smartphone Samsung Galaxy A54'  => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&q=80',
            'Écouteurs Bluetooth Sony'       => 'https://images.unsplash.com/photo-1484704849700-f032a568e944?w=400&q=80',
            'Tablette Android 10 pouces'     => 'https://images.unsplash.com/photo-1561154464-82e9adf32764?w=400&q=80',
            'Chargeur Rapide USB-C 65W'      => 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=400&q=80',
            'Montre Connectée Smartwatch'    => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=400&q=80',
            'Clé USB 128Go'                  => 'https://images.unsplash.com/photo-1618478210788-ef40d13b31f9?w=400&q=80',
            'Câble HDMI 2m'                  => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&q=80',
            'Batterie externe 20000mAh'      => 'https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&q=80',
            'Casque Gaming RGB'              => 'https://images.unsplash.com/photo-1599669454699-248893623440?w=400&q=80',
            'Webcam HD 1080p'                => 'https://images.unsplash.com/photo-1623949556303-b0d17d198c8b?w=400&q=80',
            'Souris Sans Fil Logitech'       => 'https://images.unsplash.com/photo-1615663245857-ac93bb7c39e7?w=400&q=80',

            // Vêtements
            'T-shirt Coton Premium'          => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=400&q=80',
            'Jean Slim Fit Homme'            => 'https://images.unsplash.com/photo-1598554747436-c9293d6a588f?w=400&q=80',
            'Robe Wax Africaine'             => 'https://images.unsplash.com/photo-1614267861476-0d129972a0f4?w=400&q=80',
            'Veste en Jean Femme'            => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400&q=80',
            'Chemise Oxford Homme'           => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=400&q=80',
            'Boubou Grand Boubou'            => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=400&q=80',
            'Polo Ralph Lauren'              => 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=400&q=80',
            'Jupe Longue Wax'                => 'https://images.unsplash.com/photo-1577900232427-18219b9166a0?w=400&q=80',
            'Sweat à Capuche'                => 'https://images.unsplash.com/photo-1556821840-3a63f15732ce?w=400&q=80',
            'Pantalon Chino Beige'           => 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=400&q=80',
            'Dashiki Africain'               => 'https://images.unsplash.com/photo-1529139574466-a303027c1d8b?w=400&q=80',

            // Alimentation
            'Café Arabica 1kg'               => 'https://images.unsplash.com/photo-1447933601403-0c6688de566e?w=400&q=80',
            "Huile d'Argan 500ml"            => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=400&q=80',
            'Thé Vert Menthe 250g'           => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=400&q=80',
            'Miel Naturel 500g'              => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?w=400&q=80',
            'Quinoa Bio 500g'                => 'https://images.unsplash.com/photo-1612358405970-e5e5c1a1f0e3?w=400&q=80',
            'Pâtes Artisanales 500g'         => 'https://images.unsplash.com/photo-1555949258-eb67b1ef0ceb?w=400&q=80',
            'Sauce Pimentée Locale'          => 'https://images.unsplash.com/photo-1575517111839-3a3843ee7f5d?w=400&q=80',
            'Riz Parfumé Thaï 5kg'           => 'https://images.unsplash.com/photo-1516684732162-798a0062be99?w=400&q=80',
            'Beurre de Karité 200g'          => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?w=400&q=80',
            'Dattes Medjool 500g'            => 'https://images.unsplash.com/photo-1590301157890-4810ed352733?w=400&q=80',
            'Huile de Palme 1L'              => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=400&q=80',

            // Maison & Déco
            'Coussin Décoratif Wax'          => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&q=80',
            'Lampe de Bureau LED'            => 'https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?w=400&q=80',
            'Set Vaisselle 6 personnes'      => 'https://images.unsplash.com/photo-1603199505974-f63c8e92c585?w=400&q=80',
            'Miroir Mural Rotin'             => 'https://images.unsplash.com/photo-1567538096630-e0c55bd6374c?w=400&q=80',
            'Plante Artificielle'            => 'https://images.unsplash.com/photo-1463320726281-696a485928c7?w=400&q=80',
            'Bougie Parfumée Set 3'          => 'https://images.unsplash.com/photo-1602028915047-37269d1a73f7?w=400&q=80',
            'Panier en Osier'                => 'https://images.unsplash.com/photo-1606722590583-6951b5ea92ad?w=400&q=80',
            'Tableau Africain 40x60'         => 'https://images.unsplash.com/photo-1582650625119-3a31f8fa2699?w=400&q=80',
            'Horloge Murale Bois'            => 'https://images.unsplash.com/photo-1509048191080-d2984bad6ae5?w=400&q=80',
            'Tapis Berbère 120x180'          => 'https://images.unsplash.com/photo-1585412727339-54e4bae3bbf9?w=400&q=80',
            'Vase Céramique Artisanal'       => 'https://images.unsplash.com/photo-1565193566173-7a0ee3dbe261?w=400&q=80',

            // Sport & Fitness
            'Tapis de Yoga Premium'          => 'https://images.unsplash.com/photo-1575052814086-f385e2e2ad1b?w=400&q=80',
            'Ballon de Football'             => 'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=400&q=80',
            'Gants de Boxe 10oz'             => 'https://images.unsplash.com/photo-1517438476312-10d79c077509?w=400&q=80',
            'Haltères 5kg la paire'          => 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=400&q=80',
            'Corde à Sauter Pro'             => 'https://images.unsplash.com/photo-1598289431512-b97b0917afac?w=400&q=80',
            'Bande de Résistance Set'        => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400&q=80',
            'Gourde Sport 1L'                => 'https://images.unsplash.com/photo-1523362628745-0c100150b504?w=400&q=80',
            'Chaussures Running'             => 'https://images.unsplash.com/photo-1460353581641-37baddab0fa2?w=400&q=80',
            'Short de Sport'                 => 'https://images.unsplash.com/photo-1562886877-42aee39b5e3b?w=400&q=80',
            'Vélo Statique Pliable'          => 'https://images.unsplash.com/photo-1607962837359-5e7e89f86776?w=400&q=80',
            'Sac de Sport 40L'               => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&q=80',

            // Beauté & Santé
            'Crème Hydratante Karité'        => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?w=400&q=80',
            'Huile de Coco 250ml'            => 'https://images.unsplash.com/photo-1575386808702-7ee0a24c6ffe?w=400&q=80',
            'Savon Noir Beldi 200g'          => 'https://images.unsplash.com/photo-1600857544200-b2f468e10562?w=400&q=80',
            'Parfum Oud Intense 50ml'        => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&q=80',
            'Brosse à Dents Électrique'      => 'https://images.unsplash.com/photo-1559591937-abc09e5a5e24?w=400&q=80',
            'Masque Cheveux Argan'           => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?w=400&q=80',
            'Sérum Vitamine C'               => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?w=400&q=80',
            'Kit Manucure Complet'           => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?w=400&q=80',
            'Encens Naturel Set'             => 'https://images.unsplash.com/photo-1600612253971-2b58f7d4f6a1?w=400&q=80',
            'Tondeuse Barbe Pro'             => 'https://images.unsplash.com/photo-1621607150248-8b8d80c7a00c?w=400&q=80',
            'Lotion Corps Karité'            => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=400&q=80',

            // Informatique
            'Ordinateur Portable HP'         => 'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2?w=400&q=80',
            'Souris Gaming RGB'              => 'https://images.unsplash.com/photo-1613141411244-0e4ac259d217?w=400&q=80',
            'Clavier Mécanique'              => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400&q=80',
            'Écran 24 pouces Full HD'        => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=400&q=80',
            'Disque Dur Externe 1To'         => 'https://images.unsplash.com/photo-1531492746076-161ca9bcad58?w=400&q=80',
            'Hub USB-C 7 en 1'               => 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400&q=80',
            'Tapis de Souris XXL'            => 'https://images.unsplash.com/photo-1616499370260-485b3e5ed653?w=400&q=80',
            'Casque Audio Studio'            => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=400&q=80',
            'Imprimante Laser'               => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=400&q=80',
            'Routeur WiFi 6'                 => 'https://images.unsplash.com/photo-1606904825846-647eb07f5be2?w=400&q=80',
            'Webcam 4K'                      => 'https://images.unsplash.com/photo-1587826080692-f439cd0b70da?w=400&q=80',

            // Livres & Éducation
            'Français Facile Débutant'       => 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=400&q=80',
            'Mathématiques Terminale'        => 'https://images.unsplash.com/photo-1509228468518-180dd4864904?w=400&q=80',
            'Atlas du Monde 2024'            => 'https://images.unsplash.com/photo-1524661135-423995f22d0b?w=400&q=80',
            "Apprendre l'Anglais"            => 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=400&q=80',
            "Histoire de l'Afrique"          => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=400&q=80',
            'Physique Chimie Lycée'          => 'https://images.unsplash.com/photo-1532094349884-543559396326?w=400&q=80',
            'Dictionnaire Larousse'          => 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400&q=80',
            'Roman Africain Contemporain'    => 'https://images.unsplash.com/photo-1495640388908-05fa85288e61?w=400&q=80',
            'Guide Entrepreneuriat'          => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=400&q=80',
            "Cahier d'Exercices CP"          => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400&q=80',
            'Livre de Cuisine Africaine'     => 'https://images.unsplash.com/photo-1466637574441-749b8f19452f?w=400&q=80',
        ];

        $count = 0;
        foreach ($images as $name => $url) {
            $updated = Product::where('name', $name)->update(['image' => $url]);
            if ($updated) {
                $this->line("✅ $name");
                $count++;
            }
        }

        $this->info("$count produits mis à jour !");
    }
}