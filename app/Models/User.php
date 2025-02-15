<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\KeranjangItem;  // Pastikan ini ada jika perlu

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'nama', 'email', 'password', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'foto',
    ];

    // Relasi ke CartItem
    public function keranjangItems()
    {
        return $this->hasMany(KeranjangItem::class, 'user_id');
    }
    /**
     * Relasi dengan tabel Pemesanan.
     */
    public function pemesanans()
    {
        return $this->hasMany(Pemesanan::class, 'user_id');
    }

}
