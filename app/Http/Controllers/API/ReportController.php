<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ReportResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ReportResource::collection(Quote::paginate(25));
    }

    /**
     * Display the specified resource.
     *
     * @param string $symbol
     * @return ReportResource|JsonResponse
     */
    public function show(string $symbol): ReportResource|JsonResponse
    {
        if (!$quote = Quote::query()->where('symbol', $symbol)->first()) {
            return $this->responseWithError(404, 'Quote not found');
        }
        return new ReportResource($quote);
    }
}
