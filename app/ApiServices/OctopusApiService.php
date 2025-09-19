<?php

namespace App\ApiServices;

use Illuminate\Support\Facades\Http;
use Throwable;

class OctopusApiService
{
    private string $baseUrl = 'https://api.octopus.energy/v1';

    public function getAccount(): ?array
    {
        $endpoint = '/accounts/'.config('octopus.account_number');

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get electricity consumption for a given meter point and serial number.
     *
     * @param  string  $periodFrom  ISO8601 datetime
     * @param  string  $periodTo  ISO8601 datetime
     */
    public function getConsumption(string $mpan, string $meterSerial, string $periodFrom, string $periodTo): ?array
    {
        $endpoint = "/electricity-meter-points/{$mpan}/meters/{$meterSerial}/consumption";

        $params = [
            'page_size' => 100, // TODO: currently we always receive 48 records for a single day but we should handle pagination
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
            'order_by' => 'period',
        ];

        return $this->makeRequest('GET', $endpoint, $params);
    }

    /**
     * Get available products.
     */
    public function getProducts(): ?array
    {
        $endpoint = '/products';

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get product.
     */
    public function getProduct(string $productCode): ?array
    {
        $endpoint = "/products/{$productCode}";

        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Get standard unit rates for a given product and tariff.
     *
     * @param  string  $periodFrom  ISO8601 datetime
     * @param  string  $periodTo  ISO8601 datetime
     */
    public function getStandardUnitRates(string $productCode, string $tariffCode, string $periodFrom, string $periodTo): ?array
    {
        $endpoint = "/products/{$productCode}/electricity-tariffs/{$tariffCode}/standard-unit-rates";

        $params = [
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
        ];

        return $this->makeRequest('GET', $endpoint, $params);
    }

    /**
     * Get standing charges for a given product and tariff.
     *
     * @param  string  $periodFrom  ISO8601 datetime
     * @param  string  $periodTo  ISO8601 datetime
     */
    public function getStandingCharges(string $productCode, string $tariffCode, string $periodFrom, string $periodTo): ?array
    {
        $endpoint = "/products/{$productCode}/electricity-tariffs/{$tariffCode}/standing-charges";

        $params = [
            'period_from' => $periodFrom,
            'period_to' => $periodTo,
        ];

        return $this->makeRequest('GET', $endpoint, $params);
    }

    /**
     * Make an HTTP request.
     */
    private function makeRequest(string $method, string $endpoint, array $params = []): ?array
    {
        $url = "{$this->baseUrl}{$endpoint}";

        try {
            $response = Http::withBasicAuth(config('octopus.api_key'), '')
                ->{$method}($url, $params);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (Throwable $t) {
            // Log or handle error
        }

        return null;
    }
}
