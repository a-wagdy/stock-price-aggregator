<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Quote;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReportController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return ReportResource::collection(Quote::paginate(25));
    }
}
