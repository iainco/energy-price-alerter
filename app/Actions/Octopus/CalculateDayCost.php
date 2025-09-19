<?php

namespace App\Actions\Octopus;

class CalculateDayCost
{
    public function execute(array $consumptionResponse): float
    {
        // TODO: since I'm on a fixed rate with Octopus I am using config vars below
        // but we should fetch pricing data from the API periodically (or all the time 
        // if not on a fixed rate)

        $total = 0.0;

        foreach ($consumptionResponse['results'] as $consumption) {
            $total += $consumption['consumption'] * config('octopus.standard_unit_rate');
        }

        return $total + config('octopus.standing_charge');
    }
}
