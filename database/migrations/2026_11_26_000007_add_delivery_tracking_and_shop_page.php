<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_available')) {
                $table->boolean('is_available')->default(true);          // livreur disponible
                $table->decimal('last_lat', 10, 7)->nullable();          // dernière position connue (livreur)
                $table->decimal('last_lng', 10, 7)->nullable();
                $table->dateTime('last_seen_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'shop_banner')) {
                $table->string('shop_banner')->nullable();               // page boutique personnalisée
                $table->text('shop_description')->nullable();
                $table->text('shop_hours')->nullable();                  // JSON : horaires par jour
                $table->boolean('vacation_mode')->default(false);
                $table->date('vacation_until')->nullable();
                $table->string('vacation_message', 255)->nullable();
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'courier_lat')) {
                $table->decimal('courier_lat', 10, 7)->nullable();      // position en direct du livreur
                $table->decimal('courier_lng', 10, 7)->nullable();
                $table->dateTime('courier_updated_at')->nullable();
            }
            if (! Schema::hasColumn('orders', 'delivery_code')) {
                $table->string('delivery_code', 4)->nullable();         // code de livraison à 4 chiffres
                $table->string('proof_path')->nullable();               // photo du colis remis
            }
            if (! Schema::hasColumn('orders', 'incident_type')) {
                $table->string('incident_type', 30)->nullable();
                $table->string('incident_note', 500)->nullable();
                $table->dateTime('incident_at')->nullable();
            }
            if (! Schema::hasColumn('orders', 'courier_fee')) {
                $table->unsignedInteger('courier_fee')->default(0);     // rémunération du livreur pour la course
            }
            if (! Schema::hasColumn('orders', 'seller_note')) {
                $table->text('seller_note')->nullable();                // note interne du vendeur
            }
        });

        // Anciennes livraisons : rémunération par défaut pour que les gains ne soient pas à zéro
        DB::table('orders')->where('status', 'delivered')->whereNotNull('livreur_id')->where('courier_fee', 0)
            ->update(['courier_fee' => (int) config('shop.courier_fee', 1000)]);
    }

    public function down(): void
    {
        // Colonnes conservées (rétrocompatibilité)
    }
};
