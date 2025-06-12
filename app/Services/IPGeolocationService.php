<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IPGeolocationService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.ipgeolocation.io/v2/ipgeo';

    public function __construct()
    {
        $this->apiKey = config('services.ipgeolocation.api_key');
    }

    /**
     * Get geolocation data for an IP address
     *
     * @param string $ipAddress
     * @param string $userAgent
     * @return array|null
     */
    public function getLocationData($ipAddress, $userAgent = null)
    {
        try {
            $params = [
                'apiKey' => $this->apiKey,
                'ip' => $ipAddress,
                'fields' => 'location,user_agent,currency,time_zone',
            ];

            // Add user agent if provided
            if ($userAgent) {
                $params['user_agent'] = $userAgent;
            }

            $response = Http::timeout(10)->get($this->baseUrl, $params);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('IPGeolocation API error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'ip' => $ipAddress
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('IPGeolocation API exception', [
                'message' => $e->getMessage(),
                'ip' => $ipAddress
            ]);

            return null;
        }
    }

    /**
     * Parse the API response and extract relevant data
     *
     * @param array $data
     * @return array
     */
    public function parseLocationData($data)
    {
        if (!$data) {
            return $this->getDefaultData();
        }

        return [
            'country' => $data['location']['country_name'] ?? null,
            'city' => $data['location']['city'] ?? null,
            'region' => $data['location']['state_prov'] ?? null,
            'continent' => $data['location']['continent_name'] ?? null,
            'latitude' => $data['location']['latitude'] ?? null,
            'longitude' => $data['location']['longitude'] ?? null,
            'zipcode' => $data['location']['zipcode'] ?? null,
            'country_emoji' => $data['location']['country_emoji'] ?? null,
            'device_name' => $data['user_agent']['device']['name'] ?? null,
            'device_type' => $data['user_agent']['device']['type'] ?? null,
            'device_brand' => $data['user_agent']['device']['brand'] ?? null,
            'os_name' => $data['user_agent']['operating_system']['name'] ?? null,
            'browser_name' => $data['user_agent']['name'] ?? null,
        ];
    }

    /**
     * Get default data when API fails
     *
     * @return array
     */
    private function getDefaultData()
    {
        return [
            'country' => null,
            'city' => null,
            'region' => null,
            'continent' => null,
            'latitude' => null,
            'longitude' => null,
            'zipcode' => null,
            'country_emoji' => null,
            'device_name' => null,
            'device_type' => null,
            'device_brand' => null,
            'os_name' => null,
            'browser_name' => null,
        ];
    }
}