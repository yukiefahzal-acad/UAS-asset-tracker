<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show Admin login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('assets.index');
        }
        return view('auth.login');
    }

    /**
     * Show Admin register form.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('assets.index');
        }
        return view('auth.register');
    }

    /**
     * Handle Admin registration (Created with 'pending' status).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'pending', // Requires Superadmin approval
        ]);

        return redirect()->route('login')
            ->with('warning', 'Pendaftaran berhasil! Akun Anda berstatus "Menunggu Persetujuan" (Pending Approval) dan harus disetujui oleh Super Admin sebelum dapat masuk.');
    }

    /**
     * Handle Admin authentication (Check status approved).
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // Find user by username or email
        $user = User::where('email', $login)->orWhere('name', $login)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors([
                'login' => 'Username / Email atau Password salah.',
            ])->onlyInput('login');
        }

        // Check if user is approved
        if (!$user->isApproved()) {
            if ($user->status === 'pending') {
                return back()->withErrors([
                    'login' => 'Akun Anda masih dalam status Menunggu Persetujuan (Pending) dari Super Admin.',
                ])->onlyInput('login');
            } elseif ($user->status === 'rejected') {
                return back()->withErrors([
                    'login' => 'Mohon maaf, permohonan akun Admin Anda telah ditolak oleh Super Admin.',
                ])->onlyInput('login');
            }
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('assets.index'))
            ->with('success', "Selamat datang kembali, " . Auth::user()->name . "!");
    }

    /**
     * Log out Admin session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', 'Anda telah keluar dari sistem.');
    }
}
