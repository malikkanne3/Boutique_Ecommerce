<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class LivreurMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->role !== 'livreur') {
            abort(403, 'Accès réservé aux livreurs.');
        }
        return $next($request);
    }
}