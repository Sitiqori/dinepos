<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'sku', 'barcode', 'unit',
        'description', 'price', 'cost_price', 'stock', 'min_stock',
        'image', 'is_active',
    ];

    protected $casts = [
        'price'      => 'integer',
        'cost_price' => 'integer',
        'stock'      => 'integer',
        'min_stock'  => 'integer',
        'is_active'  => 'boolean',
    ];

    public function category()   { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }

    public function isLowStock(): bool
    {
        $min = $this->getAttribute('min_stock') ?? 0;
        return $this->stock <= (int) $min;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/'.$this->image)
            : asset('images/product-placeholder.png');
    }
}
