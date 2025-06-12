<?php

namespace App\Services;

use App\Models\ClickLog;
use App\Models\Contact;
use Illuminate\Http\Request;

class ClickLogService
{
    protected $ipGeolocationService;

    public function __construct(IPGeolocationService $ipGeolocationService)
    {
        $this->ipGeolocationService = $ipGeolocationService;
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

            // Get geolocation data
            $geoData = $this->ipGeolocationService->getLocationData($ipAddress, $userAgent);
            $parsedData = $this->ipGeolocationService->parseLocationData($geoData);

            // Create click log
            $clickLog = ClickLog::create([
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'name' => $contact->name,
                'ip_address' => $ipAddress,
                'country' => $parsedData['country'],
                'city' => $parsedData['city'],
                'region' => $parsedData['region'],
                'continent' => $parsedData['continent'],
                'latitude' => $parsedData['latitude'],
                'longitude' => $parsedData['longitude'],
                'zipcode' => $parsedData['zipcode'],
                'country_emoji' => $parsedData['country_emoji'],
                'device_name' => $parsedData['device_name'],
                'device_type' => $parsedData['device_type'],
                'device_brand' => $parsedData['device_brand'],
                'os_name' => $parsedData['os_name'],
                'browser_name' => $parsedData['browser_name'],
                'clicked_at' => now(),
            ]);

            return $clickLog;
        } catch (\Exception $e) {
            \Log::error('Failed to log click', [
                'contact_id' => $contact->id,
                'username' => $contact->username,
                'error' => $e->getMessage()
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

                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Fallback to request IP
        return $request->ip();
    }

    /**
     * Get click statistics for a contact
     *
     * @param Contact $contact
     * @return array
     */
    public function getClickStats(Contact $contact)
    {
        $logs = ClickLog::where('contact_id', $contact->id)->get();

        return [
            'total_clicks' => $logs->count(),
            'unique_ips' => $logs->unique('ip_address')->count(),
            'countries' => $logs->whereNotNull('country')->unique('country')->count(),
            'cities' => $logs->whereNotNull('city')->unique('city')->count(),
            'first_click' => $logs->min('clicked_at'),
            'last_click' => $logs->max('clicked_at'),
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
        ];
    }
}
