<?php

namespace App\Http\Controllers\Admin;

use App\Models\Paket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaketController extends Controller
{
    // Menampilkan daftar paket
    public function index(Request $request)
    {
        $pageTitle = "Daftar Paket";
        $pakets = Paket::all();

        return view('admin.paket.index', compact('pakets', 'pageTitle'));
    }

    // Menampilkan form tambah paket
    public function create()
    {
        $pageTitle = "Tambah Paket";
        return view('admin.paket.create', compact('pageTitle'));
    }

    // Menyimpan data paket baru
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->storeAs(
                'paket_foto',
                time() . '_' . $request->file('foto')->getClientOriginalName(),
                'public'
            );
        }

        Paket::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin-paket.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    // Menampilkan form edit paket
    public function edit($id)
    {
        $pageTitle = "Edit Paket";
        $paket = Paket::findOrFail($id);
        return view('admin.paket.edit', compact('paket', 'pageTitle'));
    }

    // Mengupdate data paket
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $paket = Paket::findOrFail($id);

        if ($request->hasFile('foto')) {
            if ($paket->foto) {
                Storage::disk('public')->delete($paket->foto);
            }
            $fotoPath = $request->file('foto')->store('paket_foto', 'public');
            $paket->foto = $fotoPath;
        }

        $paket->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'foto' => $paket->foto,
        ]);

        return redirect()->route('admin-paket.index')->with('success', 'Paket berhasil diperbarui.');
    }

    // Menghapus data paket
    public function destroy($id)
    {
        $paket = Paket::findOrFail($id);

        if ($paket->foto) {
            Storage::disk('public')->delete($paket->foto);
        }

        $paket->delete();

        return redirect()->route('admin-paket.index')->with('success', 'Paket berhasil dihapus.');
    }
}
