<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('favorites')) {
            Schema::create('favorites', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }

        // Avis : produit (product_id rempli) ou vendeur (product_id vide, seller_id rempli)
        if (! Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->unsignedBigInteger('order_id')->nullable();
                $table->unsignedTinyInteger('rating');
                $table->text('comment')->nullable();
                $table->text('reply')->nullable();           // réponse du vendeur
                $table->boolean('is_hidden')->default(false); // modération admin
                $table->timestamps();
            });
        }

        // Retours et litiges
        if (! Schema::hasTable('disputes')) {
            Schema::create('disputes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('user_id')->index();   // client
                $table->unsignedBigInteger('seller_id')->nullable()->index();
                $table->string('reason', 30);
                $table->text('description');
                $table->string('status', 20)->default('open');    // open | seller_replied | escalated | resolved
                $table->string('resolution', 30)->nullable();     // refund | exchange | no_action | agreement
                $table->text('admin_decision')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('dispute_messages')) {
            Schema::create('dispute_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dispute_id')->index();
                $table->unsignedBigInteger('user_id');
                $table->text('body')->nullable();
                $table->string('type', 12)->default('text');
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->string('attachment_mime', 100)->nullable();
                $table->unsignedBigInteger('attachment_size')->nullable();
                $table->timestamps();
            });
        }

        // Notes moyennes (calculées à chaque avis)
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'rating_avg')) {
                $table->decimal('rating_avg', 3, 2)->default(0);
                $table->unsignedInteger('rating_count')->default(0);
            }
        });
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'seller_rating_avg')) {
                $table->decimal('seller_rating_avg', 3, 2)->default(0);
                $table->unsignedInteger('seller_rating_count')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('disputes');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
    }
};
