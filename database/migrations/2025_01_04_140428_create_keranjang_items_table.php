<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('keranjang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Menghubungkan dengan tabel users
            $table->foreignId('produk_id')->nullable()->constrained('produk')->onDelete('set null'); // Menghubungkan dengan tabel produk
            $table->foreignId('paket_id')->nullable()->constrained('paket')->onDelete('set null'); // Menghubungkan dengan tabel paket
            $table->integer('jumlah'); // Jumlah produk/paket yang ditambahkan
            $table->decimal('harga', 15, 2); // Kolom harga untuk menyimpan harga total
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang');
    }
};
