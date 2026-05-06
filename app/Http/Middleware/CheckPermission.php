<?php

// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->hasPermission($permission)) {
            return response()->json(['error' => 'Sem permissão'], 403);
        }

        return $next($request);
    }
}