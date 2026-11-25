<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class CreateTestAccounts extends Command
{
    protected $signature = 'comptes:test {--password=password : Mot de passe des comptes de test}';
    protected $description = 'Crée (ou réinitialise) les 4 comptes de démonstration : admin, vendeur, livreur, client';

    public function handle(): int
    {
        // Les nouvelles colonnes doivent exister (migrations non appliquées = erreur SQL)
        if (! Schema::hasColumn('users', 'shop_name') || ! Schema::hasColumn('users', 'status')) {
            $this->error('La base de données n\'est pas à jour.');
            $this->line('Lancez d\'abord :  php artisan migrate');
            return self::FAILURE;
        }

        $password = (string) $this->option('password');

        $accounts = [
            ['admin@ecommerce.com',   'Admin Principal', 'admin',   null],
            ['vendeur@ecommerce.com', 'Vendeur Démo',    'vendeur', 'Boutique Teranga'],
            ['livreur@ecommerce.com', 'Livreur Dakar',   'livreur', null],
            ['client@ecommerce.com',  'Client Test',     'client',  null],
        ];

        $rows = [];
        foreach ($accounts as [$email, $name, $role, $shop]) {
            User::updateOrCreate(['email' => $email], [
                'name'      => $name,
                'role'      => $role,
                'shop_name' => $shop,
                'status'    => 'active',
                'must_change_password' => false,
                'password'  => $password,
            ]);
            $rows[] = [$role, $email, $password];
        }

        $this->table(['Rôle', 'Email', 'Mot de passe'], $rows);
        $this->warn('À supprimer ou à changer avant de partager le lien ngrok !');

        return self::SUCCESS;
    }
}
