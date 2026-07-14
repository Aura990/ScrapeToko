<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComparisonHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop1_id',
        'shop2_id',
        'keyword',
        'sort',
        'shop1_product_count',
        'shop2_product_count',
        'shop1_min_price',
        'shop2_min_price',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shop1(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop1_id');
    }

    public function shop2(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop2_id');
    }

    /**
     * Toko mana yang punya harga termurah pada perbandingan ini.
     */
    public function getCheaperShopAttribute(): ?string
    {
        if (is_null($this->shop1_min_price) || is_null($this->shop2_min_price)) {
            return null;
        }

        if ($this->shop1_min_price === $this->shop2_min_price) {
            return 'sama';
        }

        return $this->shop1_min_price < $this->shop2_min_price ? 'shop1' : 'shop2';
    }
}
