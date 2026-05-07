<?php
// ============================================================
// POUR LARAVEL 10 UNIQUEMENT
// Dans app/Http/Kernel.php, ajoute dans $routeMiddleware :
// ============================================================

// protected $routeMiddleware = [
//     'auth'       => \App\Http\Middleware\Authenticate::class,
//     'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
//     'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
//     'can'        => \Illuminate\Auth\Middleware\Authorize::class,
//     'guest'      => \App\Http\Middleware\RedirectIfAuthenticated::class,
//     'signed'     => \Illuminate\Routing\Middleware\ValidateSignature::class,
//     'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
//     'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
//
//     // ← AJOUTER CETTE LIGNE :
//     'admin'      => \App\Http\Middleware\AdminMiddleware::class,
// ];

// ============================================================
// POUR LARAVEL 11
// Dans bootstrap/app.php (voir bootstrap_app.php dans ce projet)
// ============================================================
