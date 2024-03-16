<?php

declare(strict_types=1);

use App\API\AlphaVantageApiService;
use App\Jobs\PopulateRealTimePrices;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PopulateRealTimePrices(new AlphaVantageApiService()))->everyMinute()->withoutOverlapping();
