<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // Nama tabel (optional, kalo nama tabel beda sama nama model)
    protected $table = 'produk';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'nama',
        'deskripsi',
        'foto',
        'harga',
    ];
    // Di Model Produk dan Paket
protected $casts = [
    'harga' => 'integer',
];

    // Kolom yang tidak bisa diisi secara massal
    protected $guarded = [];

    // Jika menggunakan timestamp
    public $timestamps = true;

    // Format harga atau kolom lainnya jika diperlukan
    // protected $casts = [
    //     'harga' => 'decimal:2',
    // ];
      public function keranjangItems()
    {
        return $this->hasMany(KeranjangItem::class);
    }
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }
}
