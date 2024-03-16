<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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
