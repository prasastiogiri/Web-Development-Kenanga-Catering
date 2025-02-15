<?php

namespace App\Http\Controllers\User;

use App\Models\Produk; // Menggunakan model Produk
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdukUserController extends Controller
{
    // Menampilkan daftar produk
    public function index(Request $request)
    {
        $pageTitle = "Produk";

        // Ambil input pencarian nama produk
        $search = $request->input('search', '');

        // Cek apakah ada parameter sorting dari query string
        // Ubah default sorting menjadi 'nama_asc' (A-Z)
        $sortBy = $request->input('sort', 'nama_asc'); // Default sorting by 'nama_asc' (A-Z)
        $sortOrder = 'asc'; // Default ascending order

        // Tentukan urutan berdasarkan 'sort' parameter
        if ($sortBy == 'harga_asc') {
            $sortBy = 'harga';
            $sortOrder = 'asc';
        } elseif ($sortBy == 'harga_desc') {
            $sortBy = 'harga';
            $sortOrder = 'desc';
        } elseif ($sortBy == 'created_at') {
            $sortBy = 'created_at';
            $sortOrder = 'desc'; // Untuk mendapatkan yang terbaru, urutkan secara descending
        } elseif ($sortBy == 'nama_asc') {
            $sortBy = 'nama';
            $sortOrder = 'asc'; // Nama dari A-Z
        } elseif ($sortBy == 'nama_desc') {
            $sortBy = 'nama';
            $sortOrder = 'desc'; // Nama dari Z-A
        }

        // Query untuk mendapatkan produk sesuai dengan sorting dan pencarian, dengan pagination
        $produk = Produk::when($search, function ($query, $search) {
            return $query->where('nama', 'like', '%' . $search . '%');
        })
            ->orderBy($sortBy, $sortOrder)
            ->paginate(99); // Menambahkan pagination, 12 produk per halaman

        return view('user.produkUser.index', compact('produk', 'pageTitle', 'search'));
    }
}
