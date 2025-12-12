<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Sprawdza, czy użytkownik ma jedną z wymaganych ról.
     * Użycie: middleware('role:admin,employee')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            // Jeśli użytkownik nie ma roli, zwróć błąd 403 (Forbidden)
            abort(403, 'Nie masz uprawnień do przeglądania tej strony.');
        }

        return $next($request);
    }
}