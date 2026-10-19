<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string $address
 * @property string $postal_code
 * @property string $city
 * @property string $service
 * @property string $description
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Quote|null $quote
 */
class QuoteRequest extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'service',
        'description',
    ];

    /**
     * @return HasOne<Quote, $this>
     */
    public function quote(): HasOne
    {
        return $this->hasOne(Quote::class);
    }
}
