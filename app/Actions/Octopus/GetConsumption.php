<?php

namespace App\Actions\Octopus;

use App\ApiServices\OctopusApiService;

class GetConsumption
{
    public function __construct(protected OctopusApiService $service) {}

    public function execute(string $mpan, string $meterSerial, string $from, string $to): array
    {
        return $this->service->getConsumption($mpan, $meterSerial, $from, $to);
    }
}
