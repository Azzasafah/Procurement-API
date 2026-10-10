<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('web.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('web.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function switchRole(Request $request)
    {
        $user = null;
        $email = $request->input('email');

        // Handle alias budi@procurement.app to employee@procurement.app
        if ($email === 'budi@procurement.app') {
            $email = 'employee@procurement.app';
        }

        if ($email) {
            $user = User::where('email', $email)->first();
        }

        if (!$user && $request->filled('role')) {
            $user = User::where('role', $request->input('role'))->where('is_active', true)->first()
                ?? User::where('role', $request->input('role'))->first();
        }

        if (!$user) {
            return back()->with('error', 'Akun pengguna untuk uji coba peran tidak ditemukan.');
        }

        Auth::login($user);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return redirect()->route('web.dashboard')
            ->with('success', "Berhasil masuk sebagai " . strtoupper($user->role) . " ({$user->name})");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    public function profile()
    {
        return view('auth.profile');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return back()->with('error', 'Kata sandi saat ini tidak sesuai.');
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return back()->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }
}
