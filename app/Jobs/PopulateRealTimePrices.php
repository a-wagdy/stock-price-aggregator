<?php

namespace App\Jobs;

use App\Jobs\Middleware\JobRateLimited;
use App\Models\Quote;
use App\Stock\API\AlphaVantageAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateRealTimePrices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public int $maxExceptions = 1;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public int $tries = 1;

    private AlphaVantageAPI $alphaVantageAPI;

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new JobRateLimited];
    }

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->alphaVantageAPI = new AlphaVantageAPI();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $symbols = Quote::query()->select(['symbol'])->pluck('symbol');

        foreach ($symbols as $symbol) {

            $cacheKey = md5($symbol);

            if (Cache::has($cacheKey)) {
                continue;
            }

            try {
                $currentPrice = $this->alphaVantageAPI->getCurrentPriceForQuote($symbol);
                $previousPrice = Cache::get($cacheKey);

                if ($currentPrice === $previousPrice) {
                    continue;
                }

                Cache::put($cacheKey, $currentPrice, now()->addMinutes(15));

                DB::transaction(function () use ($symbol, $currentPrice) {
                    $quote = Quote::query()->select(['id', 'symbol'])->where('symbol', $symbol)->first();
                    $quote->prices()->create(['price' => $currentPrice]);
                });

            } catch (\Throwable $exception) {
                Log::error('PopulateRealTimePrices has failed: ' . $exception->getMessage());
            }
        }
    }

    public function fail($exception = null): void
    {
        $this->delete();
    }
}