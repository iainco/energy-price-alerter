<?php

namespace App\Actions\Octopus;

use App\ApiServices\OctopusApiService;

class GetAccount
{
    public function __construct(protected OctopusApiService $service) {}

    public function execute(): ?array
    {
        return $this->service->getAccount();
    }
}
