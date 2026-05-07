# INSTALLATION — E-Shop Laravel

## 1. Créer le projet

```bash
composer create-project laravel/laravel ecommerce
cd ecommerce
```

## 2. Configurer la base de données

Dans `.env` :
```
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

## 3. Installer Breeze (auth)

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run dev
```

## 4. Enregistrer le middleware admin

### Laravel 11 — dans `bootstrap/app.php` :
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### Laravel 10 — dans `app/Http/Kernel.php`, sous `$routeMiddleware` :
```php
'admin' => \App\Http\Middleware\AdminMiddleware::class,
```

## 5. Copier les fichiers du projet

Copier les fichiers dans les bons dossiers :

| Fichier source          | Destination Laravel                         |
|-------------------------|---------------------------------------------|
| migrations/*.php        | database/migrations/                        |
| models/*.php            | app/Models/                                 |
| middleware/*.php         | app/Http/Middleware/                        |
| controllers/*.php       | app/Http/Controllers/                       |
| controllers/Admin/*.php | app/Http/Controllers/Admin/                 |
| views/**                | resources/views/                            |
| routes/web.php          | routes/web.php                              |
| seeders/*.php           | database/seeders/                           |

## 6. Lancer les migrations + seeders

```bash
php artisan migrate
php artisan db:seed
```

## 7. Lien storage

```bash
php artisan storage:link
```

## 8. Lancer le serveur

```bash
php artisan serve
```

---

## Comptes de test

| Rôle  | Email            | Mot de passe |
|-------|------------------|--------------|
| Admin | admin@shop.com   | password     |
| User  | (s'inscrire)     | —            |

---

## URLs principales

| Page              | URL                      |
|-------------------|--------------------------|
| Accueil           | /                        |
| Boutique          | /shop                    |
| Panier            | /cart                    |
| Checkout          | /checkout                |
| Mes commandes     | /my-orders               |
| Dashboard admin   | /admin                   |
| Admin produits    | /admin/products          |
| Admin catégories  | /admin/categories        |
| Admin commandes   | /admin/orders            |
| Admin clients     | /admin/users             |

---

## Fonctionnalités couvertes

- [x] Authentification (Breeze)
- [x] Rôles user/admin avec middleware
- [x] Catalogue avec recherche + filtre catégorie
- [x] Détail produit
- [x] Panier en session (ajout, modification quantité, suppression, vidage)
- [x] Checkout avec validation et transaction DB
- [x] Décrémentation du stock à la commande
- [x] Table pivot order_product (quantity, price)
- [x] Historique commandes client
- [x] Dashboard admin avec stats
- [x] CRUD produits avec upload image + soft delete
- [x] CRUD catégories avec soft delete
- [x] Gestion commandes + changement de statut
- [x] Liste clients + détail
- [x] Scopes Eloquent (active, inStock, byCategory)
- [x] Seeders (admin, catégories, produits)
- [x] Interface responsive Bootstrap 5
- [x] Alertes flash (succès/erreur)
