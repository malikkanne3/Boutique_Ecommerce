<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---- Messages directs : pièces jointes, photo, localisation, transfert ----
        Schema::table('messages', function (Blueprint $table) {
            if (! Schema::hasColumn('messages', 'type')) {
                $table->string('type', 12)->default('text');           // text | image | file | location
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->string('attachment_mime', 100)->nullable();
                $table->unsignedBigInteger('attachment_size')->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->string('forwarded_from')->nullable();
            }
        });

        // ---- Groupes de discussion ----
        if (! Schema::hasTable('chat_groups')) {
            Schema::create('chat_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('chat_group_members')) {
            Schema::create('chat_group_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamp('last_read_at')->nullable();
                $table->timestamps();
                $table->unique(['group_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('group_messages')) {
            Schema::create('group_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id')->index();
                $table->unsignedBigInteger('sender_id')->index();
                $table->text('body')->nullable();
                $table->string('type', 12)->default('text');
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();
                $table->string('attachment_mime', 100)->nullable();
                $table->unsignedBigInteger('attachment_size')->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->string('forwarded_from')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_messages');
        Schema::dropIfExists('chat_group_members');
        Schema::dropIfExists('chat_groups');
    }
};
