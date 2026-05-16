<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category', 'type', 'product', 'brand', 'size',
        'price', 'unit', 'cost_per_unit', 'product_id', 'code',
        'requirement', 'stock', 'input_mode', 'expiry', 'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'requirement' => 'integer',
        'stock' => 'integer',
        'expiry' => 'date',
    ];

    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    public function getPerBoxAttribute()
    {
        if (preg_match('/(\d+)\/BOX/i', $this->unit, $matches)) {
            return (int) $matches[1];
        }
        return null;
    }

    public function getUnitDisplayAttribute()
    {
        if ($this->unit === 'Unit') return 'Unit';
        return str_replace('/box', '/Box', $this->unit);
    }

    public function getNeedsOrderAttribute()
    {
        return $this->requirement > $this->stock;
    }

    public function getOrderAmountAttribute()
    {
        if (!$this->needs_order) return 0;
        $diff = $this->requirement - $this->stock;
        $perBox = $this->per_box;
        if ($perBox) {
            return (int) ceil($diff / $perBox);
        }
        return $diff;
    }

    public function getOrderDisplayAttribute()
    {
        if (!$this->needs_order) return '-';
        $perBox = $this->per_box;
        if ($perBox) {
            return $this->order_amount . ' boxes';
        }
        return $this->order_amount . ' units';
    }
}
