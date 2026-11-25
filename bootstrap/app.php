<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Nécessaire derrière ngrok : Laravel lit l'hôte et le https envoyés par le proxy
        $middleware->trustProxies(at: '*');

        // Un visiteur non connecté qui ouvre une page d'un espace secret arrive sur la connexion de cet espace
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            foreach (['admin', 'vendeur', 'livreur'] as $role) {
                $slug = config('portals.' . $role);
                if ($slug && ($request->is($slug) || $request->is($slug . '/*'))) {
                    return url('/' . $slug);
                }
            }
            return route('login');
        });

        // Comptes désactivés / à valider déconnectés tout de suite + mot de passe provisoire à changer
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EnsureAccountActive::class,
            \App\Http\Middleware\ForcePasswordChange::class,
        ]);

        $middleware->alias([
            'admin'   => \App\Http\Middleware\AdminMiddleware::class,
            'livreur' => \App\Http\Middleware\LivreurMiddleware::class,
            'role'    => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();