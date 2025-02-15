<?php

namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage; // Import Storage

class ProdukController extends Controller
{
    // Menampilkan daftar produk
    public function index(Request $request)
    {
        $pageTitle = "Daftar Produk";
        $produks = Produk::all(); // Pastikan menggunakan paginate

        return view('admin.produk.index', compact('produks', 'pageTitle'));
    }

    // Menampilkan form untuk membuat produk baru (hanya untuk tampilan di web, bisa diabaikan jika API saja)
    public function create()
    {
        $pageTitle = "Tambah Produk";
        return view('admin.produk.create', compact('pageTitle')); // Tampilkan form create
    }

    // Menyimpan produk baru ke database
    // Menyimpan produk baru ke database
    public function store(Request $request)
    {
        // Validasi dan simpan produk baru
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->storeAs(
                'produk_foto',
                time() . '_' . $request->file('foto')->getClientOriginalName(),
                'public'
            );
        }

        Produk::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'foto' => $fotoPath,
        ]);

        // Kirim pesan sukses
        return redirect()->route('admin-produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    // Menampilkan produk berdasarkan ID
    public function show($id)
    {
        $produk = Produk::findOrFail($id);
        return view('admin.produk.show', compact('produk')); // Tampilkan produk di view
    }

    // Menampilkan form untuk mengedit produk (hanya untuk tampilan di web, bisa diabaikan jika API saja)
    public function edit($id)
    {
        $pageTitle = "Edit Produk";
        $produk = Produk::findOrFail($id);
        return view('admin.produk.edit', compact('produk', 'pageTitle')); // Return view edit produk
    }

    // Memperbarui produk berdasarkan ID
    // Memperbarui produk berdasarkan ID
    public function update(Request $request, $id)
    {
        // Validasi dan update produk
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $produk = Produk::findOrFail($id);

        if ($request->hasFile('foto')) {
            if ($produk->foto) {
                Storage::disk('public')->delete($produk->foto);
            }
            $fotoPath = $request->file('foto')->store('produk_foto', 'public');
            $produk->foto = $fotoPath;
        }

        $produk->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
            'foto' => $produk->foto,
        ]);

        // Kirim pesan sukses
        return redirect()->route('admin-produk.index')->with('success', 'Produk berhasil diperbarui!');
    }


    // Menghapus produk berdasarkan ID
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->foto) {
            Storage::disk('public')->delete($produk->foto);
        }

        $produk->delete();

        // Redirect ke halaman index produk setelah dihapus
        return redirect()->route('admin-produk.index')->with('success', 'Produk berhasil dihapus!');
    }
}
