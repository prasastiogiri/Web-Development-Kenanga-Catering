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
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Hubungkan dengan tabel users
            $table->foreignId('paket_id')->nullable()->constrained('paket')->onDelete('set null'); // Hubungkan dengan tabel paket
            $table->foreignId('produk_id')->nullable()->constrained('produk')->onDelete('set null'); // Hubungkan dengan tabel produk
            $table->decimal('total_harga', 10, 2); // Total harga pemesanan
            $table->string('status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('order_id')->nullable();
            $table->date('event_date'); // Tanggal acara
            $table->string('event_place', 255); // Tempat acara
            $table->integer('jumlah'); // Jumlah item yang dipesan
            $table->string('payment_method')->nullable();
            $table->string('status_pemesanan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('pemesanan');
    }
};
