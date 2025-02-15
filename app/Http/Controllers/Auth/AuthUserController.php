<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
class AuthUserController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        $pageTitle = "Login";
        return view('user.auth.login', compact('pageTitle'));
    }

    // Menangani proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->remember)) {
            return redirect()->route('homepage');
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    // Menampilkan form registrasi
    public function showRegisterForm()
    {
        $pageTitle = "Register";
        return view('user.auth.register', compact('pageTitle'));
    }

    // Menangani proses registrasi
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        Auth::login($user);

        return redirect()->route('user.login');
    }

    // Validator untuk form registrasi
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'jenis_kelamin' => ['required', 'string', 'in:laki-laki,perempuan'],
            'tempat_lahir' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date'],
        ]);
    }

    // Membuat user baru
    protected function create(array $data)
    {
        return User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'jenis_kelamin' => $data['jenis_kelamin'],
            'tempat_lahir' => $data['tempat_lahir'],
            'tanggal_lahir' => $data['tanggal_lahir'],
        ]);
    }

    // Logout user
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // Logout hanya dari guard user
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // Menampilkan profil
    public function showProfile()
    {
        $user = Auth::user();
        $pageTitle = 'Profil Saya';
        return view('user.auth.profile', compact('user', 'pageTitle'));
    }

    // Memperbarui profil
    public function updateProfile(Request $request)
{
    try {
        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        // Handle upload foto
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $fotoPath = $request->file('foto')->store('user_foto', 'public');
            $user->foto = $fotoPath;
        }

        // Update user data
        $updateData = [
            'nama' => $request->nama,
            'foto' => $user->foto,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'user' => $user
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}
