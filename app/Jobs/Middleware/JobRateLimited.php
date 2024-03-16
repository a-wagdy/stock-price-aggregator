<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Contracts\Redis\LimiterTimeoutException;
use Illuminate\Support\Facades\Redis;

class JobRateLimited
{
    /**
     * Whatever uses this middleware, the job will be locked for 5 seconds
     * This is needed to avoid overlapping updates or race conditions
     *
     * @param object $job
     * @param Closure(object): void $next
     * @throws LimiterTimeoutException
     */
    public function handle(object $job, Closure $next): void
    {
        Redis::throttle('priceUpdates')
            ->block(0)->allow(1)->every(5)
            ->then(function () use ($job, $next) {

                    $next($job);
                }, function () use ($job) {

                    $job->release(5);
                }
            );
    }
}