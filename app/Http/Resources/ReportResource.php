<?php

namespace App\Http\Resources;

use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Quote $this */

        $latestPrices = $this->getLatestPrices(2);

        $currentPrice = $previousPrice = 0;
        if ($latestPrices->isNotEmpty()) {
            $currentPrice = $latestPrices[0]['price'] ?? 0;
            $previousPrice = $latestPrices[1]['price'] ?? 0;
        }

        $percentage_change = $previousPrice ? (($currentPrice - $previousPrice) / $previousPrice) * 100 : 0;

        return [
            'symbol' => $this->symbol,
            'current_price' => $currentPrice,
            'previous_price' => $previousPrice,
            'percentage_change' => round($percentage_change, 2),
        ];
    }
}
