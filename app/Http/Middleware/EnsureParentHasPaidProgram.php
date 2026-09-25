<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParentHasPaidProgram
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isParent() && ! $user->hasActivePaidProgram()) {
            return redirect()->route('parent.dashboard')
                ->with('warning', 'Fitur ini hanya dapat diakses setelah Anda memilih program belajar dan menyelesaikan pembayaran.');
        }

        return $next($request);
    }
}
