<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---- Produits : disponibilité, réduction sur une période, tarif dégressif ----
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'is_available')) {
                $table->boolean('is_available')->default(true);
            }
            if (! Schema::hasColumn('products', 'discount_percent')) {
                $table->unsignedTinyInteger('discount_percent')->nullable();
                $table->dateTime('discount_starts_at')->nullable();
                $table->dateTime('discount_ends_at')->nullable();
            }
            if (! Schema::hasColumn('products', 'bulk_min_qty')) {
                $table->unsignedInteger('bulk_min_qty')->nullable();
                $table->unsignedTinyInteger('bulk_percent')->nullable();
            }
        });

        // ---- Variantes (taille / couleur) avec stock propre ----
        if (! Schema::hasTable('product_variants')) {
            Schema::create('product_variants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('size', 40)->nullable();
                $table->string('color', 40)->nullable();
                $table->unsignedInteger('stock')->default(0);
                $table->timestamps();
            });
        }

        // ---- Plusieurs photos par produit ----
        if (! Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('path');
                $table->unsignedInteger('position')->default(0);
                $table->timestamps();
            });
        }

        // ---- Lignes de commande : variante choisie ----
        Schema::table('order_product', function (Blueprint $table) {
            if (! Schema::hasColumn('order_product', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->string('size', 40)->nullable();
                $table->string('color', 40)->nullable();
            }
        });

        // ---- Commandes : paiement + localisation exacte ----
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method', 20)->default('cash');   // cash | wave | orange_money
                $table->string('payment_status', 20)->default('pending'); // pending | paid
                $table->decimal('delivery_lat', 10, 7)->nullable();
                $table->decimal('delivery_lng', 10, 7)->nullable();
                $table->string('delivery_note', 255)->nullable();
            }
        });

        // ---- Utilisateurs : photos + position enregistrée ----
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable();
                $table->string('shop_logo')->nullable();
                $table->decimal('address_lat', 10, 7)->nullable();
                $table->decimal('address_lng', 10, 7)->nullable();
            }
        });

        // ---- Notifications : qui a fait l'action (pour afficher sa photo) ----
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'actor_id')) {
                $table->unsignedBigInteger('actor_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
    }
};
