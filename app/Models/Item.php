<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'unit',
        'stock',
        'minimum_stock',
        'location',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'minimum_stock' => 'integer',
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function stockInDetails(): HasMany
    {
        return $this->hasMany(StockInDetail::class, 'item_id');
    }

    public function stockOutDetails(): HasMany
    {
        return $this->hasMany(StockOutDetail::class, 'item_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'item_id');
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }
        if ($this->stock <= $this->minimum_stock) {
            return 'menipis';
        }
        return 'aman';
    }

    public function getStockStatusLabelAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'Stok Habis',
            'menipis' => 'Stok Menipis',
            default => 'Stok Aman',
        };
    }

    public function getStockStatusBadgeClassAttribute(): string
    {
        return match ($this->stock_status) {
            'habis' => 'bg-danger',
            'menipis' => 'bg-warning text-dark',
            default => 'bg-success',
        };
    }
}
