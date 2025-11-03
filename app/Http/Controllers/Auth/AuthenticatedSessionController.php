<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // If user is admin, redirect to dashboard
        if ($user->is_admin) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }
        if ($user->can(\Perm::AccessAdminPanel->value)) {
            return redirect()->intended(route('dashboard.genarel-page', absolute: false));
        }
        // Get primary role (teacher has priority)
        $primaryRole = $user->getPrimaryRole();

        if ($primaryRole === 'مدرس') {
            return redirect()->intended(route('portal.teacher.index', absolute: false));
        }

        if ($primaryRole === 'طالب') {
            return redirect()->intended(route('portal.student.index', absolute: false));
        }

        if ($primaryRole === 'ولي أمر') {
            return redirect()->intended(route('portal.guardian.index', absolute: false));
        }

        // Fallback to dashboard if no role found
        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
