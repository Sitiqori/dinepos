<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $images = [
            'Dimsum Mentai Keju' => 'products/dimsum-mentai-keju.jpg',
            'Dimsum Ayam'        => 'products/dimsum-ayam.jpg',
            'Dimsum Mozarella'   => 'products/dimsum-mozarella.jpg',
            'Dimsum Udang'       => 'products/dimsum-udang.jpg',
            'Es Teh Manis'       => 'products/es-teh-manis.jpg',
            'Jus Alpukat'        => 'products/jus-alpukat.jpg',
            'Nasi Goreng'        => 'products/nasi-goreng.jpg',
            'Keripik Singkong'   => 'products/keripik-singkong.jpg',
        ];

        foreach ($images as $name => $path) {
            DB::table('products')
                ->where('name', $name)
                ->whereNull('image')
                ->update(['image' => $path]);
        }
    }

    public function down(): void
    {
        $names = [
            'Dimsum Mentai Keju', 'Dimsum Ayam', 'Dimsum Mozarella', 'Dimsum Udang',
            'Es Teh Manis', 'Jus Alpukat', 'Nasi Goreng', 'Keripik Singkong',
        ];

        DB::table('products')
            ->whereIn('name', $names)
            ->update(['image' => null]);
    }
};
