<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangItem extends Model
{
    use HasFactory;

    protected $table = 'keranjang';

      protected $fillable = [
        'user_id', 
        'produk_id', 
        'paket_id', 
        'jumlah', 
        'harga',  // Pastikan harga termasuk dalam daftar fillable
    ];
    // Relasi dengan tabel User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan tabel Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Relasi dengan tabel Paket
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

}
