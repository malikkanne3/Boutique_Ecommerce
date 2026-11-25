<?php
namespace App\Console\Commands;

use App\Support\Portal;
use Illuminate\Console\Command;

class ShowPortalLinks extends Command
{
    protected $signature = 'portails:liens {--url= : Lien public ngrok (ex : https://abcd.ngrok-free.app)}';
    protected $description = 'Affiche (et enregistre) les liens secrets de connexion admin / vendeur / livreur';

    public function handle(): int
    {
        $local = rtrim((string) config('app.url'), '/');
        $ngrok = rtrim((string) ($this->option('url') ?: env('NGROK_URL', '')), '/');

        $rows = [['Client', $local . '/login', $ngrok ? $ngrok . '/login' : '—']];
        foreach (['admin' => 'Admin', 'vendeur' => 'Vendeur', 'livreur' => 'Livreur'] as $role => $label) {
            $rows[] = [$label, $local . Portal::path($role), $ngrok ? $ngrok . Portal::path($role) : '—'];
        }

        $this->table(['Espace', 'Lien local', 'Lien ngrok'], $rows);
        $this->warn('Gardez ces liens pour vous : ils ne sont affichés nulle part sur le site.');
        $this->line('Astuce : php artisan portails:liens --url=https://VOTRE-LIEN.ngrok-free.app');

        $lines = ["LIENS SECRETS E-SHOP SN (" . now()->format('d/m/Y H:i') . ")", ''];
        foreach ($rows as [$label, $localUrl, $ngrokUrl]) {
            $lines[] = $label . ' : ' . $localUrl . ($ngrokUrl !== '—' ? '   |   ' . $ngrokUrl : '');
        }
        @file_put_contents(storage_path('app/liens-acces.txt'), implode(PHP_EOL, $lines) . PHP_EOL);
        $this->line('Enregistrés aussi dans : storage/app/liens-acces.txt');

        return self::SUCCESS;
    }
}
