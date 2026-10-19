<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    protected $fillable = [
        'valid_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
        ];
    }

    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function subtotal(): int
    {
        return $this->items->sum(fn (QuoteItem $item) => $item->total());
    }

    public function vatAmount(): int
    {
        return $this->items->sum(fn (QuoteItem $item) => $item->vatAmount());
    }

    public function total(): int
    {
        return $this->subtotal() + $this->vatAmount();
    }
}