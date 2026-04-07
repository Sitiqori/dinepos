<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Safe migration: adds missing columns without dropping existing data.
 * Run: php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── orders table ─────────────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            // Add order_type if missing
            if (!Schema::hasColumn('orders', 'order_type')) {
                $table->enum('order_type', ['dine_in', 'take_away'])
                      ->default('dine_in')
                      ->after('payment_method');
            }

            // Fix payment_method enum to include 'tunai' if it only has 'cash'
            // SQLite doesn't support ALTER COLUMN, so we check the driver
            if (DB::getDriverName() !== 'sqlite') {
                // For MySQL/PostgreSQL: modify enum to support both cash and tunai
                try {
                    DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('tunai','qris','transfer','cash') NULL");
                } catch (\Throwable $e) {
                    // Column may already be correct, skip
                }
            }
        });

        // ── transactions table ───────────────────────────────────────────
        if (DB::getDriverName() !== 'sqlite') {
            try {
                DB::statement("ALTER TABLE transactions MODIFY payment_method ENUM('tunai','qris','transfer','cash') NOT NULL");
            } catch (\Throwable $e) {
                // Already correct, skip
            }
        }

        // ── products table ───────────────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('products', 'barcode')) {
                $table->string('barcode')->nullable()->after('sku');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit')->default('Pcs')->after('barcode');
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->integer('min_stock')->default(0)->after('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_type')) {
                $table->dropColumn('order_type');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            foreach (['sku', 'barcode', 'unit', 'min_stock'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
