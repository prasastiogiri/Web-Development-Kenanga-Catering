<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'user_id',
        'produk_id',
        'paket_id',
        'total_harga',
        'status',
        'event_date',
        'event_place',
        'jumlah',
        'payment_method',
        'status_pemesanan',
        'snap_token',
        'order_id'
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'event_date' => 'datetime'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }


    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }
    public function getItemsAttribute()
    {
        // If the order has a produk_id, return it as an item
        if ($this->produk_id) {
            return [[
                'produk' => $this->produk,
                'paket' => null,
                'jumlah' => $this->jumlah
            ]];
        }

        // If the order has a paket_id, return it as an item
        if ($this->paket_id) {
            return [[
                'produk' => null,
                'paket' => $this->paket,
                'jumlah' => $this->jumlah
            ]];
        }

        return [];
    }

    public function getCreatedAtAttribute($value)
{
    return \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta');
}

public function getUpdatedAtAttribute($value)
{
    return \Carbon\Carbon::parse($value)->timezone('Asia/Jakarta');
}
public function getEventDateFormattedAttribute()
{
    return $this->event_date->format('d-M-Y');
}
// App\Models\Pemesanan.php

public function scopeGetRevenue($query, $days = 7)
{
    return $query->where('status', 'paid')
        ->where('status_pemesanan', 'Selesai')
        ->where('created_at', '>=', now()->subDays($days))
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_harga) as total')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();
}

}
