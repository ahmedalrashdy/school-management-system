<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureResetPasswordNotRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->reset_password_required) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerate();

            return redirect()->route('login')
                ->withErrors(['email' => 'يجب إعادة تعيين كلمة المرور ']);
        }

        return $next($request);
    }
}
