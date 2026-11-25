<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Le champ users.role était un ENUM (admin, client, livreur).
 * On le transforme en simple texte pour accepter le rôle "vendeur".
 * Sur SQLite, il faut reconstruire la table (la contrainte CHECK ne peut pas être modifiée).
 */
return new class extends Migration
{
    // Les PRAGMA SQLite ne sont pas pris en compte dans une transaction
    public $withinTransaction = false;

    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $this->rebuildSqliteUsers();
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY role VARCHAR(20) NOT NULL DEFAULT 'client'");
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
        }
    }

    public function down(): void
    {
        // Rien à faire : on garde une colonne texte libre.
    }

    private function rebuildSqliteUsers(): void
    {
        $table = DB::selectOne("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = 'users'");

        // Déjà sans contrainte CHECK : rien à reconstruire
        if (! $table || stripos($table->sql, 'check') === false) {
            return;
        }

        $columns = DB::select('PRAGMA table_info("users")');
        $indexes = DB::select("SELECT sql FROM sqlite_master WHERE type = 'index' AND tbl_name = 'users' AND sql IS NOT NULL");

        $definitions = [];
        $names = [];
        foreach ($columns as $column) {
            $names[] = '"'.$column->name.'"';
            if ((int) $column->pk === 1) {
                $definitions[] = '"'.$column->name.'" integer primary key autoincrement not null';
                continue;
            }
            $definition = '"'.$column->name.'" '.($column->type ?: 'varchar');
            if ((int) $column->notnull === 1) {
                $definition .= ' not null';
            }
            if ($column->dflt_value !== null) {
                $definition .= ' default '.$column->dflt_value;
            }
            $definitions[] = $definition;
        }
        $list = implode(', ', $names);

        DB::unprepared('PRAGMA foreign_keys = OFF');
        DB::unprepared('BEGIN');

        try {
            DB::unprepared('CREATE TABLE "users__new" ('.implode(', ', $definitions).')');
            DB::unprepared('INSERT INTO "users__new" ('.$list.') SELECT '.$list.' FROM "users"');
            DB::unprepared('DROP TABLE "users"');
            DB::unprepared('ALTER TABLE "users__new" RENAME TO "users"');
            foreach ($indexes as $index) {
                DB::unprepared($index->sql);
            }
            DB::unprepared('COMMIT');
        } catch (\Throwable $e) {
            DB::unprepared('ROLLBACK');
            DB::unprepared('PRAGMA foreign_keys = ON');
            throw $e;
        }

        DB::unprepared('PRAGMA foreign_keys = ON');
    }
};
