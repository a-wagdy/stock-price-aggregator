<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Quote;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ids = Quote::query()->select(['id'])->pluck('id');

        $data = [];
        foreach ($ids as $id) {
            $data[] = [
                'quote_id' => $id,
                'price' => mt_rand(1,500) / 10,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('prices')->insert($data);
    }
}
