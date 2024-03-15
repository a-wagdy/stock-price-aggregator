<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];

        foreach (Quote::$symbols as $symbol) {
            $data[] = [
                'symbol' => $symbol,
                'price' => mt_rand(1,500),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('quotes')->insert($data);
    }
}
