<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $quote_id
 * @property string $description
 * @property int $quantity
 * @property int $unit_price
 * @property int $vat_rate
 * @property-read Quote $quote
 */
class QuoteItem extends Model
{
    protected $fillable = [
        'description',
        'quantity',
        'unit_price',
        'vat_rate',
    ];

    /**
     * @return BelongsTo<Quote, $this>
     */
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
