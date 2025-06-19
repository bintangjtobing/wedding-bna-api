<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ClickLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickLogService
{
    protected $deviceDetectionService;

    public function __construct(DeviceDetectionService $deviceDetectionService)
    {
        $this->deviceDetectionService = $deviceDetectionService;
    }

    /**
     * Log click activity for a contact
     *
     * @param Contact $contact
     * @param Request $request
     * @return ClickLog|null
     */
    public function logClick(Contact $contact, Request $request)
    {
        try {
            $ipAddress = $this->getClientIpAddress($request);
            $userAgent = $request->header('User-Agent');

            // Get geo location data from IPGeoLocation
            $geoData = $this->getGeoLocationData($ipAddress);

            // Get device information from User-Agent
            $deviceData = $this->deviceDetectionService->getDeviceInfo($userAgent);

            // Create click log entry
            $clickLog = ClickLog::create([
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'name' => $contact->name,
                'ip_address' => $ipAddress,

                // Geo data from IPGeoLocation
                'country' => $geoData['country'] ?? null,
                'city' => $geoData['city'] ?? null,
                'region' => $geoData['region'] ?? null,
                'continent' => $geoData['continent'] ?? null,
                'latitude' => $geoData['latitude'] ?? null,
                'longitude' => $geoData['longitude'] ?? null,
                'zipcode' => $geoData['zipcode'] ?? null,
                'country_emoji' => $geoData['country_emoji'] ?? null,

                // Device data from jenssegers/agent
                'device_name' => $deviceData['device_name'],
                'device_type' => $deviceData['device_type'],
                'device_brand' => $deviceData['device_brand'],
                'os_name' => $deviceData['os_name'],
                'browser_name' => $deviceData['browser_name'],

                'clicked_at' => now(),
            ]);

            Log::info('Click logged successfully', [
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'ip_address' => $ipAddress,
                'device_type' => $deviceData['device_type'],
                'browser' => $deviceData['browser_name'],
            ]);

            return $clickLog;

        } catch (\Exception $e) {
            Log::error('Failed to log click', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }

    /**
     * Get client IP address from request
     *
     * @param Request $request
     * @return string
     */
    protected function getClientIpAddress(Request $request)
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    /**
     * Get geo location data from IPGeoLocation API
     *
     * @param string $ipAddress
     * @return array
     */
    protected function getGeoLocationData($ipAddress)
    {
        try {
            $apiKey = config('services.ipgeolocation.api_key');

            if (!$apiKey) {
                Log::warning('IPGeoLocation API key not configured');
                return [];
            }

            $response = Http::timeout(10)->get('https://api.ipgeolocation.io/ipgeo', [
                'apiKey' => $apiKey,
                'ip' => $ipAddress,
                'fields' => 'country_code2,country_name,state_prov,city,latitude,longitude,zipcode,continent_code,continent_name,country_flag'
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'country' => $data['country_code2'] ?? null,
                    'city' => $data['city'] ?? null,
                    'region' => $data['state_prov'] ?? null,
                    'continent' => $data['continent_name'] ?? null,
                    'latitude' => isset($data['latitude']) ? (float) $data['latitude'] : null,
                    'longitude' => isset($data['longitude']) ? (float) $data['longitude'] : null,
                    'zipcode' => $data['zipcode'] ?? null,
                    'country_emoji' => $data['country_flag'] ?? null,
                ];
            }

            Log::warning('IPGeoLocation API request failed', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

        } catch (\Exception $e) {
            Log::error('IPGeoLocation API error', [
                'error' => $e->getMessage(),
                'ip' => $ipAddress
            ]);
        }

        return [];
    }

    /**
     * Get click statistics for a contact
     *
     * @param Contact $contact
     * @return array
     */
    public function getClickStats(Contact $contact)
    {
        $logs = $contact->clickLogs;

        if ($logs->isEmpty()) {
            return [
                'total_clicks' => 0,
                'unique_ips' => 0,
                'countries' => 0,
                'cities' => 0,
                'continents' => 0,
                'zip_codes' => 0,
                'avg_latitude' => 'N/A',
                'avg_longitude' => 'N/A',
                'first_click' => null,
                'last_click' => null,
                'today_clicks' => 0,
                'this_week_clicks' => 0,
                'this_month_clicks' => 0,
                'device_breakdown' => [
                    'mobile' => 0,
                    'desktop' => 0,
                    'tablet' => 0,
                    'robot' => 0,
                ],
                'mobile_percentage' => 0,
                'top_countries' => [],
                'top_cities' => [],
                'top_continents' => [],
                'top_regions' => [],
                'top_devices' => [],
                'browsers' => [],
            ];
        }

        // Device breakdown
        $deviceBreakdown = [
            'mobile' => $logs->where('device_type', 'mobile')->count(),
            'desktop' => $logs->where('device_type', 'desktop')->count(),
            'tablet' => $logs->where('device_type', 'tablet')->count(),
            'robot' => $logs->where('device_type', 'robot')->count(),
        ];

        $totalClicks = $logs->count();
        $mobilePercentage = $totalClicks > 0 ? round(($deviceBreakdown['mobile'] / $totalClicks) * 100, 1) : 0;

        // Time-based stats
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        return [
            'total_clicks' => $totalClicks,
            'unique_ips' => $logs->unique('ip_address')->count(),
            'countries' => $logs->whereNotNull('country')->unique('country')->count(),
            'cities' => $logs->whereNotNull('city')->unique('city')->count(),
            'continents' => $logs->whereNotNull('continent')->unique('continent')->count(),
            'zip_codes' => $logs->whereNotNull('zipcode')->unique('zipcode')->count(),
            'avg_latitude' => $logs->whereNotNull('latitude')->avg('latitude') ? number_format($logs->whereNotNull('latitude')->avg('latitude'), 4) : 'N/A',
            'avg_longitude' => $logs->whereNotNull('longitude')->avg('longitude') ? number_format($logs->whereNotNull('longitude')->avg('longitude'), 4) : 'N/A',
            'first_click' => $logs->min('clicked_at'),
            'last_click' => $logs->max('clicked_at'),
            'today_clicks' => $logs->where('clicked_at', '>=', $today)->count(),
            'this_week_clicks' => $logs->where('clicked_at', '>=', $thisWeek)->count(),
            'this_month_clicks' => $logs->where('clicked_at', '>=', $thisMonth)->count(),
            'device_breakdown' => $deviceBreakdown,
            'mobile_percentage' => $mobilePercentage,
            'top_countries' => $logs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'top_cities' => $logs->whereNotNull('city')
                ->groupBy('city')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'top_continents' => $logs->whereNotNull('continent')
                ->groupBy('continent')
                ->map->count()
                ->sortDesc()
                ->toArray(),
            'top_regions' => $logs->whereNotNull('region')
                ->groupBy('region')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'top_devices' => $logs->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'browsers' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
        ];
    }

    /**
     * Get recent click activities
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentActivities($limit = 20)
    {
        return ClickLog::with('contact:id,name,username')
            ->orderBy('clicked_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get click statistics by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @param Contact|null $contact
     * @return array
     */
    public function getStatsByDateRange($startDate, $endDate, Contact $contact = null)
    {
        $query = ClickLog::whereBetween('clicked_at', [$startDate, $endDate]);

        if ($contact) {
            $query->where('contact_id', $contact->id);
        }

        $logs = $query->get();

        return [
            'total_clicks' => $logs->count(),
            'unique_visitors' => $logs->unique('ip_address')->count(),
            'countries_reached' => $logs->whereNotNull('country')->unique('country')->count(),
            'device_breakdown' => [
                'mobile' => $logs->where('device_type', 'mobile')->count(),
                'desktop' => $logs->where('device_type', 'desktop')->count(),
                'tablet' => $logs->where('device_type', 'tablet')->count(),
                'robot' => $logs->where('device_type', 'robot')->count(),
            ],
            'daily_clicks' => $logs->groupBy(function ($log) {
                return $log->clicked_at->format('Y-m-d');
            })->map->count(),
        ];
    }
}