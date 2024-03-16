<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property string $symbol
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Price> $prices
 * @property-read int|null $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereSymbol($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Quote whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
    ];

    protected function casts(): array
    {
        return [
            'symbol' => 'string',
        ];
    }

    /**
     * Get the prices for the quote.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Get latest quote prices either from cache or from the DB
     *
     * @param int $limit
     * @return Collection
     */
    public function getLatestPrices(int $limit = 10): Collection
    {
        $cacheKey = md5($this->symbol);

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($limit) {
            return Price::query()
                ->where('quote_id', $this->id)
                ->orderBy('created_at', 'DESC')
                ->limit($limit)
                ->get()
            ;
        });
    }
}
