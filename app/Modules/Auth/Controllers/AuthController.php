<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Redirect & Render Layout berdasarkan Role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'umkm_owner' => redirect()->route('umkm.dashboard'),
                'customer' => redirect()->route('customer.dashboard'),
                default => abort(403, 'Role tidak teridentifikasi.'),
            };
        }

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan data kami.',
        ]);
    }

    // Method untuk menampilkan dashboard sesuai role
    public function renderDashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('adminplatform'); // resources/views/adminplatform.blade.php
        }

        if ($user->isUmkmOwner()) {
            return view('umkm'); // resources/views/umkm.blade.php
        }

        return view('customer'); // resources/views/customer.blade.php
    }
}