<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // active | pending (vendeur à valider) | suspended | rejected
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->index();
            }
            // admin qui a créé le compte (pour savoir qui peut le gérer)
            if (! Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            // mot de passe provisoire à changer à la première connexion
            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false);
            }
        });

        if (! Schema::hasTable('announcements')) {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sender_id')->nullable();
                $table->string('title');
                $table->text('body');
                $table->string('audience', 20)->default('all'); // all | client | vendeur | livreur
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_by', 'must_change_password']);
        });
    }
};
