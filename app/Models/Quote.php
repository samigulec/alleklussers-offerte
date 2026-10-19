<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $quote_request_id
 * @property Carbon|null $valid_until
 * @property string|null $notes
 * @property-read QuoteRequest $quoteRequest
 * @property-read Collection<int, QuoteItem> $items
 */
class Quote extends Model
{
    protected $fillable = [
        'valid_until',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
        ];
    }

    /**
     * @return BelongsTo<QuoteRequest, $this>
     */
    public function quoteRequest(): BelongsTo
    {
        return $this->belongsTo(QuoteRequest::class);
    }

    /**
     * @return HasMany<QuoteItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function subtotal(): int
    {
        return (int) $this->items->sum(fn (QuoteItem $item): int => $item->total());
    }

    public function vatAmount(): int
    {
        return (int) $this->items->sum(fn (QuoteItem $item): int => $item->vatAmount());
    }

    public function total(): int
    {
        return $this->subtotal() + $this->vatAmount();
    }
}
