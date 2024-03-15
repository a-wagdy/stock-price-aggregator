<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Quote;
use App\Stock\API\AlphaVantageAPI;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Cache;

/**
 * Use Isolatable to ensure that only one instance of a command can run at a time
 */
class populateRealTimePrices extends Command implements Isolatable
{
    public function __construct(
        private readonly AlphaVantageAPI $alphaVantageAPI,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-real-time-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate the database table with real-time prices';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (Quote::$symbols as $symbol) {

            $cacheKey = md5($symbol);
            if (Cache::has($cacheKey)) {
                continue;
            }

            try {
                // Lock the cache for 10 seconds to avoid race condition
                Cache::lock('priceUpdates', 10)->get(function () use ($symbol, $cacheKey) {
                    $currentPrice = $this->getCurrentPrice($symbol);
                    $previousPrice = Cache::get($cacheKey);

                    if ($currentPrice !== $previousPrice) {
                        Cache::put($cacheKey, $currentPrice, now()->addMinutes(15));

                        Quote::query()->where('symbol', $symbol)->update(['price' => $currentPrice]);
                    }
                });
            } catch (\Throwable $e) {
                // Log error to file
            }
        }
    }

    /**
     * @param string $symbol
     * @return int
     */
    function getCurrentPrice(string $symbol): int
    {
        $endpoint = $this->alphaVantageAPI->getQuoteEndpoint($symbol);
        $payload = $this->alphaVantageAPI->getContents($endpoint);

        if (isset($payload['Global Quote'])) {
            return ((int) $payload['Global Quote']['05. price'] * 100);
        }

        return mt_rand(1, 400);
    }
}
