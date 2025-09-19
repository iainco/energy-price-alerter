<?php

namespace App\Actions\Octopus;

use App\ApiServices\OctopusApiService;

class GetStandardUnitRates
{
    public function __construct(protected OctopusApiService $service) {}

    public function execute(string $productCode, string $tariffCode, string $periodFrom, string $periodTo): ?array
    {
        return $this->service->getStandardUnitRates($productCode, $tariffCode, $periodFrom, $periodTo);
    }
}
