<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session (Log out).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect authenticated user based on their assigned role.
     */
    protected function redirectBasedOnRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user && $user->isStaff()) {
            return redirect()->intended(route('staff.bookings.index'));
        }

        if (\Route::has('staff.bookings.index')) {
            return redirect()->intended(route('staff.bookings.index'));
        }

        if (\Route::has('staff.dashboard')) {
            return redirect()->intended(route('staff.dashboard'));
        }

        return redirect()->intended('/dashboard');
    }
}
