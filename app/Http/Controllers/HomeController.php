<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Home / landing page – sign in with Google or go to dashboard if already logged in
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.redirect');
        }
        return view('home');
    }

    /**
     * Redirect user to the correct dashboard by role
     */
    public function dashboardRedirect()
    {
        if (! Auth::check()) {
            return redirect()->route('home');
        }

        $user = Auth::user();
        $role = $user->role ?? 'user';

        return match ($role) {
            'superadmin' => redirect()->route('superadmin.dashboard'),
            'approver' => redirect()->route('approver.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}
