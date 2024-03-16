<?php

use App\Http\Controllers\API\StockPriceController;
use Illuminate\Support\Facades\Route;


Route::get('/quotes/report', [StockPriceController::class, 'report'])->middleware('api');