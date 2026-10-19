<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'description',
        'quantity',
        'unit_price',
        'vat_rate',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function total(): int
    {
        return $this->quantity * $this->unit_price;
    }

    public function vatAmount(): int
    {
        return (int) round($this->total() * $this->vat_rate / 100);
    }
}