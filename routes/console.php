<?php

declare(strict_types=1);

use App\Jobs\PopulateRealTimePrices;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new PopulateRealTimePrices())->everyMinute()->withoutOverlapping();
