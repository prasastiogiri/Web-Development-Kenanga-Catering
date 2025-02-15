<?php

namespace App\Http\Controllers\User;

use App\Models\Paket;
use App\Models\Produk;
use App\Http\Controllers\Controller;


class HomepageController extends Controller
{
    public function index()
    {
        $pageTitle = "Home";
        // Ambil data paket dan produk
        $pakets = Paket::paginate(); 
        $produks = Produk::paginate(); 

        // Gabungkan paket dan produk dalam satu array atau koleksi untuk view
        $items = $pakets->merge($produks); 

        // Kirim data ke view
        return view('user.homepage.homepage', compact('items', 'pageTitle'));
    }
}

