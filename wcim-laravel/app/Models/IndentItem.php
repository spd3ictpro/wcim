<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndentItem extends Model
{
    protected $fillable = [
        'indent_id', 'product_id', 'product_name', 'code', 'order_display',
    ];

    public function indent()
    {
        return $this->belongsTo(Indent::class);
    }
}
