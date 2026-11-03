<?php

use App\Exceptions\RegleMetierException;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Les violations de règles métier sont journalisées puis rendues proprement :
        // 422 JSON pour l'API, message d'erreur de formulaire pour le web.
        $exceptions->dontReport(RegleMetierException::class);

        $exceptions->render(function (RegleMetierException $e, Request $request) {
            Log::warning('Règle métier violée', [
                'exception' => class_basename($e),
                'message' => $e->getMessage(),
                'user_id' => $request->user()?->id,
                'url' => $request->fullUrl(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withInput()->withErrors(['regle' => $e->getMessage()]);
        });
    })->create();
