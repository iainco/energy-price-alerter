<?php

use App\Jobs\Octopus\CalculateDailyCost;
use Illuminate\Support\Facades\Artisan;

Artisan::command('octopus:calculate-cost', function () {
    CalculateDailyCost::dispatchSync();
    return self::SUCCESS;
});
