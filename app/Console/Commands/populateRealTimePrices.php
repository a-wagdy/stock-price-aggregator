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
    public function handle()
    {
        foreach (Quote::$symbols as $symbol) {
            $endpoint = $this->alphaVantageAPI->getQuoteEndpoint($symbol);
            $payload = $this->alphaVantageAPI->getContents($endpoint);
            $currentPrice = isset($payload['Global Quote']) ? $payload['Global Quote']['05. price'] * 100 : mt_rand(1, 400);

            $cacheKey = md5($symbol);
            $previousPrice = Cache::get($cacheKey);

            if ($currentPrice === $previousPrice) {
                continue;
            }

            Cache::put($cacheKey, $currentPrice, now()->addDay());

            Quote::query()->where('symbol', $symbol)->update(['price' => $currentPrice]);
        }
    }
}
