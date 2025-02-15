<?php

namespace App\Http\Controllers\User;

use App\Models\KeranjangItem;
use App\Models\Paket;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class KeranjangController extends Controller
{
    private $specialProducts = [
        "Kambing Guling",
        "Sate Kambing 500 Tusuk + Gulai Kambing 1 Panci"
    ];

    public function index()
    {
        $pageTitle = "Keranjang";
        return view('user.keranjang.index', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu.',
            ], 401);
        }

        $request->validate([
            'paket_id' => 'nullable|exists:paket,id',
            'produk_id' => 'nullable|exists:produk,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        try {
            $user = Auth::user();
            $existingItem = null;

            // Validasi produk khusus
            if ($request->produk_id) {
                $produk = Produk::find($request->produk_id);

                if ($produk && in_array($produk->nama, $this->specialProducts)) {
                    $currentQuantity = $user->keranjangItems()
                        ->where('produk_id', $produk->id)
                        ->sum('jumlah');

                    if (($currentQuantity + $request->jumlah) > 5) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Maaf, maksimal pemesanan 5 porsi untuk produk ini.'
                        ], 422);
                    }
                }
            }

            // Cari item yang sudah ada
            if ($request->produk_id) {
                $existingItem = $user->keranjangItems()
                    ->where('produk_id', $request->produk_id)
                    ->first();
            } elseif ($request->paket_id) {
                $existingItem = $user->keranjangItems()
                    ->where('paket_id', $request->paket_id)
                    ->first();
            }

            if ($existingItem) {
                // Update existing item
                $existingItem->jumlah += $request->jumlah;
                $hargaPerItem = $existingItem->paket ?
                    $existingItem->paket->harga :
                    $existingItem->produk->harga;
                $existingItem->harga = $existingItem->jumlah * $hargaPerItem;
                $existingItem->save();
            } else {
                // Create new item
                $paket = $request->paket_id ? Paket::find($request->paket_id) : null;
                $produk = $request->produk_id ? Produk::find($request->produk_id) : null;
                $harga = $paket ? $paket->harga : ($produk ? $produk->harga : 0);
                $totalHarga = $harga * $request->jumlah;

                KeranjangItem::create([
                    'user_id' => $user->id,
                    'produk_id' => $produk?->id,
                    'paket_id' => $paket?->id,
                    'jumlah' => $request->jumlah,
                    'harga' => $totalHarga,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil ditambahkan ke keranjang.',
                'cartItemCount' => $user->keranjangItems()->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        $item = auth()->user()->keranjangItems()->findOrFail($id);

        if ($item) {
            $item->delete();
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus.',
                'cartItemCount' => auth()->user()->keranjangItems->count(),
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Item tidak ditemukan.'], 404);
    }

    public function getKeranjangItems()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }

    $items = $user->keranjangItems()->with(['paket', 'produk'])->get();

    $data = $items->map(function ($item) {
        $isPaket = $item->paket !== null;
        $harga = $isPaket ? $item->paket->harga : $item->produk->harga;
        $subtotal = $item->jumlah * $harga;

        return [
            'id' => $item->id,
            'nama' => $isPaket ? $item->paket->nama : $item->produk->nama,
            'jumlah' => $item->jumlah,
            'harga' => $harga,
            'subtotal' => $subtotal,
            'gambar' => asset('storage/' . ($isPaket ? $item->paket->foto : $item->produk->foto)),
        ];
    });

    return response()->json([
        'success' => true,
        'items' => $data,
        'totalHarga' => $data->sum('subtotal')
    ]);
}
public function updateJumlah(Request $request, $id)
{
    try {
        $item = KeranjangItem::findOrFail($id);

        // Validasi produk khusus
        if ($item->produk && in_array($item->produk->nama, $this->specialProducts)) {
            if ($request->jumlah > 5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maksimal 5 porsi untuk produk ini.'
                ], 422);
            }
        }

        // Validasi umum
        if ($request->jumlah <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah tidak valid.'
            ], 400);
        }

        // Update item
        $item->jumlah = $request->jumlah;
        $item->harga = $request->jumlah * ($item->paket ?
            $item->paket->harga :
            $item->produk->harga);
        $item->save();

        return response()->json([
            'success' => true,
            'totalHarga' => $item->harga,
            'cartItemCount' => Auth::user()->keranjangItems()->count(),
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan sistem.'
        ], 500);
    }
}
public function checkQuantity($produkId)
{
    $user = Auth::user();
    $produk = Produk::findOrFail($produkId);

    $currentQuantity = $user->keranjangItems()
        ->where('produk_id', $produkId)
        ->sum('jumlah');

    $remaining = 5 - $currentQuantity;

    return response()->json([
        'remainingQuantity' => max($remaining, 0)
    ]);
}
}
