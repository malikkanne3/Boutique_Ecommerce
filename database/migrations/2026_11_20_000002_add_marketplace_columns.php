<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable();
            }
            if (! Schema::hasColumn('users', 'shop_name')) {
                $table->string('shop_name', 120)->nullable();
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address', 255)->nullable();
            }
        });

        // seller_id = vendeur propriétaire (NULL = boutique officielle gérée par l'admin)
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->index();
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'seller_id')) {
                $table->unsignedBigInteger('seller_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('seller_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('seller_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'shop_name', 'address']);
        });
    }
};
