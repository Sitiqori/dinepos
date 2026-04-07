<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_code',
        'amount',
        'payment_method',
        'payment_status',
        'paid_at',
        'change_amount',
        'qr_payload',
    ];

    protected $casts = [
        'amount'        => 'integer',
        'change_amount' => 'integer',
        'paid_at'       => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Auto-generate invoice_code if not provided.
     * Controller should always provide it, but this is a safety net.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tx) {
            if (empty($tx->invoice_code)) {
                $tx->invoice_code = 'INV-'.date('Ymd').'-'.strtoupper(Str::random(6));
            }
        });
    }
}
