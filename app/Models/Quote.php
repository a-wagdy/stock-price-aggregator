<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    public static array $symbols = [
        'IBM',
        'AAPL',
        'MSFT',
        'AMZN',
        'TSLA',
        'AMD',
        'NVDA',
        'ADBE',
        'ACT',
        'ADD',
    ];

    protected $fillable = [
        'symbol',
        'price',
    ];

    protected function casts(): array
    {
        return [
            'symbol' => 'string',
            'price' => 'integer',
        ];
    }
}
