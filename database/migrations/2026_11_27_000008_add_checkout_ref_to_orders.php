<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Référence commune aux commandes issues d'un même panier (une commande par vendeur)
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'checkout_ref')) {
                $table->string('checkout_ref', 40)->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        // Colonne conservée
    }
};
