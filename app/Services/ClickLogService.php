<?php

namespace App\Services;

use App\Models\ClickLog;
use App\Models\Contact;
use Illuminate\Http\Request;

class ClickLogService
{
    protected $ipGeolocationService;
    protected $deviceDetectionService;

    public function __construct(
        IPGeolocationService $ipGeolocationService,
        DeviceDetectionService $deviceDetectionService
    ) {
        $this->ipGeolocationService = $ipGeolocationService;
        $this->deviceDetectionService = $deviceDetectionService;
    }

    /**
     * Log a click for a contact
     *
     * @param Contact $contact
     * @param Request $request
     * @return ClickLog|null
     */
    public function logClick(Contact $contact, Request $request)
    {
        try {
            $ipAddress = $this->getClientIP($request);
            $userAgent = $request->header('User-Agent');

            // Get geolocation data (location only)
            $geoData = $this->ipGeolocationService->getLocationData($ipAddress);
            $parsedGeoData = $this->ipGeolocationService->parseLocationData($geoData);

            // Get device data locally (more accurate)
            $deviceData = $this->deviceDetectionService->getDeviceInfo($userAgent);

            // Create click log with combined data
            $clickLog = ClickLog::create([
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'name' => $contact->name,
                'ip_address' => $ipAddress,

                // Location data from API
                'country' => $parsedGeoData['country'],
                'city' => $parsedGeoData['city'],
                'region' => $parsedGeoData['region'],
                'continent' => $parsedGeoData['continent'],
                'latitude' => $parsedGeoData['latitude'],
                'longitude' => $parsedGeoData['longitude'],
                'zipcode' => $parsedGeoData['zipcode'],
                'country_emoji' => $parsedGeoData['country_emoji'],

                // Device data from local detection (more accurate)
                'device_name' => $deviceData['device_name'],
                'device_type' => $deviceData['device_type'],
                'device_brand' => $deviceData['device_brand'],
                'os_name' => $deviceData['os_name'],
                'browser_name' => $deviceData['browser_name'],

                'clicked_at' => now(),
            ]);

            return $clickLog;
        } catch (\Exception $e) {
            \Log::error('Failed to log click', [
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'error' => $e->getMessage(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent')
            ]);

            return null;
        }
    }

    /**
     * Get the real client IP address
     *
     * @param Request $request
     * @return string
     */
    private function getClientIP(Request $request)
    {
        // Check for various headers that might contain the real IP
        $headers = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];

        foreach ($headers as $header) {
            if ($request->server($header)) {
                $ips = explode(',', $request->server($header));
                $ip = trim($ips[0]);

                // Validate IP and exclude private ranges
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to request IP
        return $request->ip();
    }

    /**
     * Get enhanced click statistics for a contact
     *
     * @param Contact $contact
     * @return array
     */
    public function getClickStats(Contact $contact)
    {
        $logs = ClickLog::where('contact_id', $contact->id)->get();

        return [
            // Basic statistics
            'total_clicks' => $logs->count(),
            'unique_ips' => $logs->unique('ip_address')->count(),
            'today_clicks' => $logs->whereDate('clicked_at', today())->count(),
            'this_week_clicks' => $logs->where('clicked_at', '>=', now()->startOfWeek())->count(),
            'this_month_clicks' => $logs->where('clicked_at', '>=', now()->startOfMonth())->count(),

            // Geographic statistics
            'countries' => $logs->whereNotNull('country')->unique('country')->count(),
            'cities' => $logs->whereNotNull('city')->unique('city')->count(),
            'top_countries' => $logs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
            'top_cities' => $logs->whereNotNull('city')
                ->groupBy('city')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // Device analytics (enhanced!)
            'device_breakdown' => [
                'mobile' => $logs->where('device_type', 'mobile')->count(),
                'tablet' => $logs->where('device_type', 'tablet')->count(),
                'desktop' => $logs->where('device_type', 'desktop')->count(),
                'robot' => $logs->where('device_type', 'robot')->count(),
                'unknown' => $logs->where('device_type', 'unknown')->count(),
            ],

            'mobile_percentage' => $logs->count() > 0 ?
                round(($logs->where('device_type', 'mobile')->count() / $logs->count()) * 100, 1) : 0,

            'top_devices' => $logs->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            'device_brands' => $logs->whereNotNull('device_brand')
                ->groupBy('device_brand')
                ->map->count()
                ->sortDesc()
                ->take(8)
                ->toArray(),

            // Browser & OS statistics
            'browsers' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(8)
                ->toArray(),

            'operating_systems' => $logs->whereNotNull('os_name')
                ->groupBy('os_name')
                ->map->count()
                ->sortDesc()
                ->take(8)
                ->toArray(),

            // Time-based analytics
            'first_click' => $logs->min('clicked_at'),
            'last_click' => $logs->max('clicked_at'),
            'peak_hour' => $logs->groupBy(function ($log) {
                return $log->clicked_at->format('H');
            })->map->count()->sortDesc()->keys()->first(),

            // Recent activity
            'recent_clicks' => $logs->where('clicked_at', '>=', now()->subHours(24))
                ->sortByDesc('clicked_at')
                ->take(10)
                ->map(function ($log) {
                    return [
                        'country' => $log->country,
                        'city' => $log->city,
                        'device_type' => $log->device_type,
                        'device_name' => $log->device_name,
                        'browser_name' => $log->browser_name,
                        'clicked_at' => $log->clicked_at->diffForHumans(),
                    ];
                })->values()->toArray(),
        ];
    }

    /**
     * Get device analytics for admin dashboard
     *
     * @param array|null $contactIds
     * @return array
     */
    public function getDeviceAnalytics($contactIds = null)
    {
        $query = ClickLog::query();

        if ($contactIds) {
            $query->whereIn('contact_id', $contactIds);
        }

        $logs = $query->get();

        return [
            'total_clicks' => $logs->count(),
            'unique_devices' => $logs->whereNotNull('device_name')->unique('device_name')->count(),

            // Device type distribution
            'device_types' => [
                'mobile' => $logs->where('device_type', 'mobile')->count(),
                'tablet' => $logs->where('device_type', 'tablet')->count(),
                'desktop' => $logs->where('device_type', 'desktop')->count(),
                'robot' => $logs->where('device_type', 'robot')->count(),
            ],

            // Market share percentages
            'market_share' => [
                'mobile_percentage' => $logs->count() > 0 ?
                    round(($logs->where('device_type', 'mobile')->count() / $logs->count()) * 100, 1) : 0,
                'desktop_percentage' => $logs->count() > 0 ?
                    round(($logs->where('device_type', 'desktop')->count() / $logs->count()) * 100, 1) : 0,
                'tablet_percentage' => $logs->count() > 0 ?
                    round(($logs->where('device_type', 'tablet')->count() / $logs->count()) * 100, 1) : 0,
            ],

            // Top devices and brands
            'top_mobile_devices' => $logs->where('device_type', 'mobile')
                ->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(15)
                ->toArray(),

            'brand_popularity' => $logs->whereNotNull('device_brand')
                ->groupBy('device_brand')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // Browser analytics
            'browser_usage' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // OS analytics
            'os_distribution' => $logs->whereNotNull('os_name')
                ->groupBy('os_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // Mobile vs Desktop trends (last 30 days)
            'device_trends' => $logs->where('clicked_at', '>=', now()->subDays(30))
                ->groupBy(function ($log) {
                    return $log->clicked_at->format('Y-m-d');
                })
                ->map(function ($dayLogs) {
                    return [
                        'mobile' => $dayLogs->where('device_type', 'mobile')->count(),
                        'desktop' => $dayLogs->where('device_type', 'desktop')->count(),
                        'tablet' => $dayLogs->where('device_type', 'tablet')->count(),
                    ];
                })
                ->toArray(),
        ];
    }

    /**
     * Get bot/robot analytics
     *
     * @param array|null $contactIds
     * @return array
     */
    public function getRobotAnalytics($contactIds = null)
    {
        $query = ClickLog::where('device_type', 'robot');

        if ($contactIds) {
            $query->whereIn('contact_id', $contactIds);
        }

        $robotLogs = $query->get();

        return [
            'total_robot_clicks' => $robotLogs->count(),
            'unique_robot_ips' => $robotLogs->unique('ip_address')->count(),
            'robot_percentage' => ClickLog::count() > 0 ?
                round(($robotLogs->count() / ClickLog::count()) * 100, 2) : 0,

            'robot_sources' => $robotLogs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            'robot_countries' => $robotLogs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
        ];
    }

    /**
     * Test device detection for debugging
     *
     * @param string $userAgent
     * @return array
     */
    public function testDeviceDetection($userAgent)
    {
        return $this->deviceDetectionService->getDeviceInfo($userAgent);
    }

    /**
     * Get click statistics by date range
     *
     * @param Contact $contact
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getClickStatsByDateRange(Contact $contact, $startDate, $endDate)
    {
        $logs = ClickLog::where('contact_id', $contact->id)
            ->whereBetween('clicked_at', [$startDate, $endDate])
            ->get();

        return [
            'period_clicks' => $logs->count(),
            'unique_visitors' => $logs->unique('ip_address')->count(),
            'daily_breakdown' => $logs->groupBy(function ($log) {
                return $log->clicked_at->format('Y-m-d');
            })->map(function ($dayLogs) {
                return [
                    'clicks' => $dayLogs->count(),
                    'unique_ips' => $dayLogs->unique('ip_address')->count(),
                    'mobile_clicks' => $dayLogs->where('device_type', 'mobile')->count(),
                    'desktop_clicks' => $dayLogs->where('device_type', 'desktop')->count(),
                ];
            })->toArray(),
        ];
    }
}
