<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    // Table yang digunakan oleh model
    protected $table = 'paket';

    // Di Model Produk dan Paket
protected $casts = [
    'harga' => 'integer',
];
    // Kolom yang bisa diisi secara massal
    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
    ];

    // Kolom yang tidak bisa diisi secara massal
    protected $guarded = [];

    // Jika menggunakan timestamp
    public $timestamps = true;

    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class);
    }

    public function keranjangItems()
    {
        return $this->hasMany(KeranjangItem::class);
    }
}
