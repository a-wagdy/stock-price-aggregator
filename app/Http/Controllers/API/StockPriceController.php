<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockPriceChangeResource;
use App\Models\Quote;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockPriceController extends Controller
{
    public function report(): AnonymousResourceCollection
    {
        return StockPriceChangeResource::collection(Quote::paginate(25));
    }
}
