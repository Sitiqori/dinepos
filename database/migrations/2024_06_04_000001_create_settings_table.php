<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        $defaults = [
            // Profil Toko
            'store_name'       => '',
            'store_address'    => '',
            'store_phone'      => '',
            'store_whatsapp'   => '',
            'store_npwp'       => '',
            'store_logo'       => '',
            // Struk & Pajak
            'ppn_enabled'      => '1',
            'max_discount'     => '50',
            'rounding'         => 'none',
            'invoice_format'   => 'DD-MM-YYYY',
            'invoice_reset'    => 'monthly',
            'printer_type'     => 'thermal_80',
            'printer_copies'   => '1',
            'auto_print'       => '1',
        ];

        foreach ($defaults as $key => $value) {
            \Illuminate\Support\Facades\DB::table('settings')->insert([
                'key'        => $key,
                'value'      => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
