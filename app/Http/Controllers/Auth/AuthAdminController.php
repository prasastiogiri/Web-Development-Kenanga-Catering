<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthAdminController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Menangani proses login
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Mendapatkan input login
        $credentials = $request->only('email', 'password');

        // Mencoba untuk login
        if (Auth::guard('usersAdmin')->attempt($credentials, $request->remember)) {
            // Redirect ke dashboard admin jika berhasil login
            return redirect()->route('admin.dashboard');
        }

        // Jika login gagal, kembali ke halaman login dengan error
        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Menampilkan form registrasi
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    // Menangani proses registrasi
    public function register(Request $request)
    {
        // Validasi input registrasi
        $this->validator($request->all())->validate();

        // Membuat admin baru
        $admin = $this->create($request->all());

        // Login otomatis setelah registrasi
        Auth::guard('usersAdmin')->login($admin);

        // Redirect ke dashboard admin setelah registrasi
        return redirect()->route('admin.dashboard');
    }

    // Validator untuk form registrasi
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    // Membuat admin baru
    protected function create(array $data)
    {
        return Admin::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // Logout admin
    public function logout(Request $request)
    {
        Auth::guard('usersAdmin')->logout(); // Logout hanya dari guard admin
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin');
    }
}
