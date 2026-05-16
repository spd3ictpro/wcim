<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsageLog extends Model
{
    protected $fillable = [
        'product_id', 'old_stock', 'new_stock', 'change', 'note', 'type',
    ];

    protected $casts = [
        'old_stock' => 'integer',
        'new_stock' => 'integer',
        'change' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
