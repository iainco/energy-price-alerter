<?php

namespace App\Actions\Octopus;

use App\Models\DayPrice;
use Carbon\Carbon;

class CalculateProjection
{
    public function execute(string $day): array
    {
        $month = Carbon::parse($day)->format('Y-m');

        $totalForMonth = DayPrice::query()
            ->where('day', 'like', "$month%")
            ->sum('price');

        $existingCount = DayPrice::query()
            ->where('day', 'like', "$month%")
            ->count();

        $averageForMonth = $existingCount ? $totalForMonth / $existingCount : 0;
        $projection = $averageForMonth * Carbon::parse($day)->daysInMonth;

        return [
            'totalForMonth' => number_format($totalForMonth, 2),
            'averageForMonth' => number_format($averageForMonth, 2),
            'projectionForMonth' => number_format($projection, 2),
        ];
    }
}
