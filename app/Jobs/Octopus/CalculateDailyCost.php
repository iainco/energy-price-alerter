<?php

namespace App\Jobs\Octopus;

use App\Actions\Octopus\CalculateDayCost;
use App\Actions\Octopus\CalculateProjection;
use App\Actions\Octopus\GetConsumption;
use App\Models\DayPrice;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class CalculateDailyCost implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function handle(
        GetConsumption $getConsumption,
        CalculateDayCost $calculateDayCost,
        CalculateProjection $calculateProjection,
    ): void {
        $mpan = config('octopus.mpan');
        $meterSerial = config('octopus.meter_serial');
        $yesterday = Carbon::yesterday()->toDateString();

        if (! DayPrice::query()->where('day', $yesterday)->exists()) {
            $consumption = $getConsumption->execute(
                $mpan,
                $meterSerial,
                "{$yesterday}T00:00Z",
                "{$yesterday}T23:30Z"
            );

            // TODO: if no consumption figures are available yet we should re-run the job
            // in a little while, and keep trying until we get the figures for $yesterday

            $dayPrice = number_format(
                $calculateDayCost->execute($consumption) / 100,
                2
            );

            DayPrice::query()->create([
                'day' => $yesterday,
                'price' => $dayPrice,
            ]);
        } else {
            $dayPrice = DayPrice::query()->firstWhere('day', $yesterday)->price;
        }

        $result = $calculateProjection->execute($yesterday);

        Http::post(config('services.discord.webhook_url'), [
            'content' => null,
            'embeds' => [[
                'title' => 'Electricity Usage Update',
                'description' => "Total for yesterday: £{$dayPrice}\n".
                    "Total for month so far: £{$result['totalForMonth']}\n".
                    "Daily average so far: £{$result['averageForMonth']}\n".
                    "Projected total for month: £{$result['projectionForMonth']}\n",
                'color' => 2450411,
            ]],
        ]);
    }
}
