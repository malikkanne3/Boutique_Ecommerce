<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SetUserPassword extends Command
{
    protected $signature = 'user:password {email : Email du compte} {password : Nouveau mot de passe}';
    protected $description = 'Définit un nouveau mot de passe pour un compte existant';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Aucun compte avec cet email. Comptes existants :');
            $this->table(['Email', 'Rôle'], User::orderBy('role')->get(['email', 'role'])->map(fn ($u) => [$u->email, $u->role])->all());
            return self::FAILURE;
        }

        $user->update(['password' => $this->argument('password')]);
        $this->info("Mot de passe de {$user->email} ({$user->role}) mis à jour.");

        return self::SUCCESS;
    }
}
