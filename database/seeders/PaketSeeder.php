<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paket')->insert([
            [
                'nama' => 'Paket Classic 1',
                'deskripsi' => 'Nasi Putih, Nasi Goreng, Ayam Kecap Inggris, Sup sosis Jagung Manis, Bakmi Goreng, Rolade, Koloke, Sirup 2 Rasa, Water Cup ',
                'foto' => 'paket_foto/4mMY3QHFZUvYFIyHxoKKIPafKxXhQKc1kkHSKADT.jpg',
                'harga' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Classic 2 ',
                'deskripsi' => 'Nasi Putih, Nasi Goreng Hongkong, Olahan Ayam Teriyaki, Tofu Jamur, Sup Kimlo, Rolade, Garang Asem Patin, Sirup 2 Rasa, Water Cup ',
                'foto' => 'paket_foto/LacQl5iNs0jyNYPEODdmsaLrkC6gla9PAZ3vJXzg.jpg',
                'harga' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Special 1',
                'deskripsi' => 'Nasi Putih, Nasi Goreng Seafood, Chicken Cheese, Sup Shanghai, Capcay / Cah Baby Kol, Rolade, Oseng - oseng Kikil Pedas, Udang Teriyaki, Es Manado, Sirup 2 Rasa, Water Cup ',
                'foto' => 'paket_foto/GvCCnt6W3SGn3iILb2YSnqGQL9My8AHCYcwbJJI6.jpg',
                'harga' => 52500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Special 2',
                'deskripsi' => 'Nasi Putih, Nasi Goreng Seafood / Kambing, Chicken Cheese, Sup Kimlo / Sup Krim Jagung, Capcay / Cah Baby Kol, Rolade, Krengsengan Daging, Gurame Goreng Tepung, Mustafa Riang, Tongseng Ayam, Es Manado, Sirup 2 Rasa, Water Cup ',
                'foto' => 'paket_foto/IbFr8Px3REZA2J7h0YJwbXFnCVlu991lgYuTvVji.jpg',
                'harga' => 62500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Istimewa 1',
                'deskripsi' => 'Nasi Putih, Nasi Goreng Berselimut Dadar, Chicken Cheese, Sup Kimlo / Sup Krim Jagung, Capcay / Cah Baby Kol, Rolade, Krengsengan Daging, Gurame Goreng Tepung, Mustafa Riang, Olahan Ayam / Charsiu, Es Manado, Sirup 2 Rasa, Water Cup  ',
                'foto' => 'paket_foto/kcZFrVvZGCdznUJ8krkugwuy43gzdHbtADLITmAz.jpg',
                'harga' => 67500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Istimewa 2',
                'deskripsi' => 'Nasi Putih, Nasi Goreng Seafood / Kambing / Hongkong / Telur Puyuh Baldo, Chicken Cheese, Sup Kimlo / Sup Krim Jagung / Olahan Sup, Capcay / Cah Baby Kol, Rolade, Krengsengan Daging / Bistik Lidah, Gurame Goreng Tepung, Mustafa Riang, Olahan Ayam / Charsiu, Chicken Steak With Barbeque Sauce, Es Kombinasi, Sirup 2 Rasa, Water Cup  ',
                'foto' => 'paket_foto/37xCR38ZfAokz1eqYGMkfL5ARjJ8fXOW1YjoHUgZ.jpg',
                'harga' => 72500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Paket Majapahit',
                'deskripsi' => 'Nasi Putih, Nasi Jagung, Urap Sambal Terong Balado, Gudeg Komplit, Ayam Panggang, Teri Balado, Oseng Daun Singkong, Cantik Manis Patin, Tempe Goreng, Dadar Jagung, Ikan Asin, Es Dawet, Es Sinom, Bubur Sruntul ',
                'foto' => 'paket_foto/OnWtpR76ixBoP5whtxt17vD2O47PyCfmSf1KJcAZ.jpg',
                'harga' => 57000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
