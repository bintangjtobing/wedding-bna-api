<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class IPGeolocationService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.ipgeolocation.io/v2/ipgeo';
    protected $deviceDetectionService;

    public function __construct(DeviceDetectionService $deviceDetectionService)
    {
        $this->apiKey = config('services.ipgeolocation.api_key');
        $this->deviceDetectionService = $deviceDetectionService;
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
                'fields' => 'location,currency,time_zone',
            ];

            $response = Http::timeout(10)->get($this->baseUrl, $params);

            if ($response->successful()) {
                $locationData = $response->json();

                // Get device info from user agent locally
                $deviceInfo = $this->deviceDetectionService->getDeviceInfo($userAgent);

                // Merge location and device data
                return array_merge($locationData, ['device_info' => $deviceInfo]);
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

        $deviceInfo = $data['device_info'] ?? [];

        return [
            // Location data from API
            'country' => $data['country_name'] ?? null,
            'city' => $data['city'] ?? null,
            'region' => $data['state_prov'] ?? null,
            'continent' => $data['continent_name'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'zipcode' => $data['zipcode'] ?? null,
            'country_emoji' => $data['country_emoji'] ?? null,

            // Device data from local detection (matching existing fields)
            'device_name' => $deviceInfo['device_name'] ?? null,
            'device_type' => $deviceInfo['device_type'] ?? null,
            'device_brand' => $deviceInfo['device_brand'] ?? null,
            'os_name' => $deviceInfo['os_name'] ?? null,
            'browser_name' => $deviceInfo['browser_name'] ?? null,
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

// ===== DEVICE DETECTION SERVICE =====

class DeviceDetectionService
{
    protected $agent;

    public function __construct()
    {
        $this->agent = new Agent();
    }

    /**
     * Get device information from user agent (matching existing DB fields)
     *
     * @param string|null $userAgent
     * @return array
     */
    public function getDeviceInfo($userAgent = null)
    {
        if ($userAgent) {
            $this->agent->setUserAgent($userAgent);
        }

        return [
            'device_name' => $this->getDeviceName(),
            'device_type' => $this->getDeviceType(),
            'device_brand' => $this->getDeviceBrand(),
            'os_name' => $this->getOperatingSystemWithVersion(),
            'browser_name' => $this->getBrowserWithVersion(),
        ];
    }

    /**
     * Get device type (mobile, tablet, desktop, robot)
     *
     * @return string
     */
    private function getDeviceType()
    {
        if ($this->agent->isRobot()) {
            return 'robot';
        } elseif ($this->agent->isMobile()) {
            return 'mobile';
        } elseif ($this->agent->isTablet()) {
            return 'tablet';
        } elseif ($this->agent->isDesktop()) {
            return 'desktop';
        }

        return 'unknown';
    }

    /**
     * Get device name (iPhone, Galaxy, etc.)
     *
     * @return string|null
     */
    private function getDeviceName()
    {
        $device = $this->agent->device();

        if ($device) {
            return $device;
        }

        // Enhanced device detection
        $userAgent = $this->agent->getUserAgent();

        // iPhone detection with model
        if (preg_match('/iPhone(\d+[,_]\d+)?/', $userAgent, $matches)) {
            $model = isset($matches[1]) ? $this->parseIPhoneModel($matches[1]) : '';
            return 'iPhone' . ($model ? ' ' . $model : '');
        }

        // iPad detection
        if (preg_match('/iPad(\d+[,_]\d+)?/', $userAgent, $matches)) {
            return 'iPad';
        }

        // Samsung Galaxy detection with model
        if (preg_match('/SM-([A-Z0-9]+)/', $userAgent, $matches)) {
            return 'Samsung ' . $this->parseSamsungModel($matches[1]);
        }

        // Google Pixel detection
        if (preg_match('/Pixel\s*(\d+[a-zA-Z]*)?/', $userAgent, $matches)) {
            $model = isset($matches[1]) ? $matches[1] : '';
            return 'Google Pixel' . ($model ? ' ' . $model : '');
        }

        // Generic Android device
        if (preg_match('/Android.*;\s*([^)]+)\)/', $userAgent, $matches)) {
            $deviceInfo = trim($matches[1]);
            if ($deviceInfo && $deviceInfo !== 'wv' && strlen($deviceInfo) < 50) {
                return $deviceInfo;
            }
        }

        return null;
    }

    /**
     * Parse iPhone model from identifier
     */
    private function parseIPhoneModel($identifier)
    {
        $models = [
            '15,2' => '14 Pro',
            '15,3' => '14 Pro Max',
            '14,7' => '14',
            '14,8' => '14 Plus',
            '13,2' => '12',
            '13,3' => '12 Pro',
            '13,4' => '12 Pro Max',
            '13,1' => '12 mini',
        ];

        $clean = str_replace(['_', ','], '', $identifier);
        return $models[$clean] ?? $identifier;
    }

    /**
     * Parse Samsung model from identifier
     */
    private function parseSamsungModel($identifier)
    {
        $models = [
            'G998B' => 'Galaxy S21 Ultra',
            'G996B' => 'Galaxy S21+',
            'G991B' => 'Galaxy S21',
            'N986B' => 'Galaxy Note 20 Ultra',
            'N981B' => 'Galaxy Note 20',
        ];

        return $models[$identifier] ?? $identifier;
    }

    /**
     * Get device brand
     *
     * @return string|null
     */
    private function getDeviceBrand()
    {
        $userAgent = $this->agent->getUserAgent();

        // Enhanced brand detection with more patterns
        $brands = [
            'Apple' => '/(?:iPhone|iPad|iPod|Mac|Safari.*Version)/i',
            'Samsung' => '/Samsung|SM-|Galaxy|GT-/i',
            'Google' => '/Pixel|Nexus/i',
            'Huawei' => '/Huawei|Honor|HW-|EVA-|VTR-|WAS-|LYA-/i',
            'Xiaomi' => '/Xiaomi|Mi\s|Redmi|POCO|M\d{4}/i',
            'OnePlus' => '/OnePlus|ONEPLUS/i',
            'LG' => '/LG-|LG\s|LG;/i',
            'Sony' => '/Sony|Xperia/i',
            'HTC' => '/HTC/i',
            'Motorola' => '/Motorola|Moto/i',
            'Nokia' => '/Nokia/i',
            'Oppo' => '/OPPO|CPH\d+/i',
            'Vivo' => '/vivo|V\d{4}/i',
            'Lenovo' => '/Lenovo/i',
            'Asus' => '/ASUS/i',
            'Microsoft' => '/Windows Phone|Lumia/i',
            'Realme' => '/Realme|RMX\d+/i',
        ];

        foreach ($brands as $brand => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Get operating system with version
     *
     * @return string|null
     */
    private function getOperatingSystemWithVersion()
    {
        $platform = $this->agent->platform();
        if (!$platform) {
            return null;
        }

        $version = $this->agent->version($platform);
        return $platform . ($version ? ' ' . $version : '');
    }

    /**
     * Get browser with version
     *
     * @return string|null
     */
    private function getBrowserWithVersion()
    {
        $browser = $this->agent->browser();
        if (!$browser) {
            return null;
        }

        $version = $this->agent->version($browser);
        return $browser . ($version ? ' ' . $version : '');
    }

    /**
     * Check if device is mobile
     *
     * @return bool
     */
    public function isMobile()
    {
        return $this->agent->isMobile();
    }

    /**
     * Check if device is tablet
     *
     * @return bool
     */
    public function isTablet()
    {
        return $this->agent->isTablet();
    }

    /**
     * Check if device is desktop
     *
     * @return bool
     */
    public function isDesktop()
    {
        return $this->agent->isDesktop();
    }

    /**
     * Check if device is robot/bot
     *
     * @return bool
     */
    public function isRobot()
    {
        return $this->agent->isRobot();
    }

    /**
     * Get robot name if user agent is a bot
     *
     * @return string|null
     */
    public function getRobotName()
    {
        if ($this->agent->isRobot()) {
            return $this->agent->robot();
        }
        return null;
    }
}
