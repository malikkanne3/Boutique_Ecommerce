<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Accès via ngrok : forcer https et l'URL publique (liens, images du storage)
        if (! $this->app->runningInConsole()) {
            $host = request()->getHost();
            if (str_contains($host, 'ngrok')) {
                $root = 'https://'.$host;
                URL::forceRootUrl($root);
                URL::forceScheme('https');
                config(['app.url' => $root]);
                config(['filesystems.disks.public.url' => $root.'/storage']);
            }
        }

        // Désactiver la vérification SSL pour SMTP
        $this->app->afterResolving('mailer', function ($mailer) {
            $transport = $mailer->getSymfonyTransport();
            if ($transport instanceof \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport) {
                $transport->setStreamOptions([
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ]
                ]);
            }
        });
    }
}