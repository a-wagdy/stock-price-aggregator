<?php

namespace Tests\Feature;

use App\API\AlphaVantageApiService;
use App\Jobs\PopulateRealTimePrices;
use App\Models\Quote;
use Exception;
use GuzzleHttp\Promise\RejectedPromise;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Request;
use Tests\TestCase;

class PriceUpdateTest extends TestCase
{
    use InteractsWithDatabase;

    public function testPriceUpdatesFailureForUnexpectedPayload()
    {
        $symbol = substr(md5(mt_rand()), 0, 7);
        $api = new AlphaVantageApiService();
        $url = $api->getQuoteEndpoint($symbol);

        // Mock response that throws exception during price fetching
        Http::fake([
            $url => fn (Request $request) => new RejectedPromise(new Exception('Unexpected API response')),
        ]);

        /** @var Quote $quote */
        $quote = Quote::factory()->create([
            'symbol' => $symbol,
        ]);

        Queue::fake();

        PopulateRealTimePrices::dispatch($api)->onQueue('default');

        // Assert that the job was dispatched
        Queue::assertPushedOn('default', PopulateRealTimePrices::class);

        // Assert that no price was stored (since fetching failed)
        $this->assertDatabaseMissing('prices', ['quote_id' => $quote->id]);

        // Execute the job again to test rate limiting
        PopulateRealTimePrices::dispatch($api);

        // Assert that the job was released due to rate limiting
        Queue::assertPushed(PopulateRealTimePrices::class);
    }

    public function testPriceUpdatesSuccess()
    {
        $price = mt_rand(1, 500);
        $symbol = substr(md5(mt_rand()), 0, 7);
        $api = new AlphaVantageApiService();
        $url = $api->getQuoteEndpoint($symbol);

        // Mock successful response for price fetching
        Http::fake([
            $url => Http::response([
                'Global Quote' => [
                    '05. price' => $price,
                ],
            ]),
        ]);

        /** @var Quote $quote */
        $quote = Quote::factory()->create([
            'symbol' => $symbol,
        ]);

        Queue::fake();

        PopulateRealTimePrices::dispatch($api)->onQueue('default');

        // Assert that the job was dispatched
        Queue::assertPushedOn('default', PopulateRealTimePrices::class);

        // Assert that the price has been stored correctly
        $this->assertDatabaseHas('prices', [
            'quote_id' => $quote->id,
            'price' => $price,
        ]);

        // Execute the job again to test rate limiting
        PopulateRealTimePrices::dispatch($api);

        // Assert that the job was released due to rate limiting
        Queue::assertPushed(PopulateRealTimePrices::class);
    }

}
