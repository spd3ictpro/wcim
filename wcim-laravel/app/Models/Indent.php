<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indent extends Model
{
    protected $fillable = [
        'indent_date', 'total_items', 'notes',
    ];

    protected $casts = [
        'indent_date' => 'date',
        'total_items' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(IndentItem::class);
    }

    public function getMonthLabelAttribute()
    {
        return $this->indent_date->format('F Y');
    }
}
