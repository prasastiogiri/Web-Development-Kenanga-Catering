<?php

namespace App\Http\Controllers\User;

use App\Models\Paket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaketUserController extends Controller
{
    // Menampilkan daftar paket
    public function index(Request $request)
    {
        $pageTitle = "Paket";

        // Ambil input pencarian nama paket
        $search = $request->input('search', '');

        // Cek apakah ada parameter sorting dari query string
        $sortBy = $request->input('sort', 'nama_asc'); // Default sorting by 'nama_asc'
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

        // Query untuk mendapatkan paket sesuai dengan sorting dan pencarian
        $pakets = Paket::when($search, function ($query, $search) {
            return $query->where('nama', 'like', '%' . $search . '%');
        })
            ->orderBy($sortBy, $sortOrder)
            ->get();

        return view('user.paketUser.index', compact('pakets', 'pageTitle', 'search'));
    }
}
