<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // type: 'stok' | 'expiry' | 'expired' | 'pesanan'
            $table->string('type', 30);
            // reference_id: product id atau order id
            $table->unsignedBigInteger('reference_id');
            $table->timestamp('read_at')->useCurrent();

            $table->unique(['user_id', 'type', 'reference_id']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
    }
};