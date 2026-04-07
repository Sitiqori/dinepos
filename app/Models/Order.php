<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',  // nama pelanggan (opsional)
        'order_code',
        'total',
        'status',
        'payment_method',
        'order_type',     // may not exist in older DB schema — controller checks before setting
        'notes',
        'table_number',
    ];

    protected $casts = [
        'total' => 'integer',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function items()      { return $this->hasMany(OrderItem::class); }
    public function transaction() { return $this->hasOne(Transaction::class); }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => '<span class="badge badge-orange">Pending</span>',
            'processing' => '<span class="badge badge-navy">Diproses</span>',
            'completed'  => '<span class="badge badge-green">Selesai</span>',
            'cancelled'  => '<span class="badge badge-red">Batal</span>',
            default      => '<span class="badge">'.ucfirst($this->status).'</span>',
        };
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'ORD-'.strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }
}
