<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * @param $status_code
     * @param $message
     *
     * @return JsonResponse
     */
    protected function responseWithError($status_code, $message): JsonResponse
    {
        return response()->json([
            'error' => [
                'status' => $status_code,
                'message' => $message,
            ],
        ], $status_code);
    }
}