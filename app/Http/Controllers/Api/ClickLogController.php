<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClickLog;
use App\Models\Contact;
use App\Services\ClickLogService;
use App\Services\DeviceDetectionService;
use Illuminate\Http\Request;

class ClickLogController extends Controller
{
    protected $clickLogService;
    protected $deviceDetectionService;

    public function __construct(ClickLogService $clickLogService, DeviceDetectionService $deviceDetectionService)
    {
        $this->clickLogService = $clickLogService;
        $this->deviceDetectionService = $deviceDetectionService;
    }

    /**
     * Get click statistics for all contacts (Admin only)
     */
    public function getOverallStats()
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get stats for admin's contacts only
        $contactIds = $admin->contacts()->pluck('id');
        $logs = ClickLog::whereIn('contact_id', $contactIds)->get();

        $stats = [
            'total_clicks' => $logs->count(),
            'unique_visitors' => $logs->unique('ip_address')->count(),
            'countries_reached' => $logs->whereNotNull('country')->unique('country')->count(),
            'cities_reached' => $logs->whereNotNull('city')->unique('city')->count(),

            // Geographic stats
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

            // Device stats
            'device_types' => $logs->whereNotNull('device_type')
                ->groupBy('device_type')
                ->map->count()
                ->sortDesc()
                ->toArray(),
            'device_brands' => $logs->whereNotNull('device_brand')
                ->groupBy('device_brand')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
            'top_devices' => $logs->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // Browser & OS stats
            'browsers' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
            'operating_systems' => $logs->whereNotNull('os_name')
                ->groupBy('os_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),

            // Additional insights
            'mobile_vs_desktop' => [
                'mobile' => $logs->where('device_type', 'mobile')->count(),
                'tablet' => $logs->where('device_type', 'tablet')->count(),
                'desktop' => $logs->where('device_type', 'desktop')->count(),
                'robot' => $logs->where('device_type', 'robot')->count(),
            ],

            // Time-based stats
            'clicks_last_7_days' => $logs->where('clicked_at', '>=', now()->subDays(7))->count(),
            'clicks_last_30_days' => $logs->where('clicked_at', '>=', now()->subDays(30))->count(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }

    /**
     * Get click logs for a specific contact
     */
    public function getContactClickLogs($contactId)
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $contact = $admin->contacts()->find($contactId);

        if (!$contact) {
            return response()->json([
                'status' => 'error',
                'message' => 'Contact not found'
            ], 404);
        }

        $logs = ClickLog::where('contact_id', $contactId)
            ->orderBy('clicked_at', 'desc')
            ->paginate(20);

        $stats = $this->clickLogService->getClickStats($contact);

        // Enhanced stats with device information
        $contactLogs = ClickLog::where('contact_id', $contactId)->get();
        $enhancedStats = array_merge($stats, [
            'device_breakdown' => [
                'mobile' => $contactLogs->where('device_type', 'mobile')->count(),
                'tablet' => $contactLogs->where('device_type', 'tablet')->count(),
                'desktop' => $contactLogs->where('device_type', 'desktop')->count(),
                'robot' => $contactLogs->where('device_type', 'robot')->count(),
            ],
            'top_devices' => $contactLogs->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
            'top_browsers' => $contactLogs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(5)
                ->toArray(),
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'contact' => $contact,
                'statistics' => $enhancedStats,
                'logs' => $logs
            ]
        ]);
    }

    /**
     * Get recent click activities
     */
    public function getRecentActivities()
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $contactIds = $admin->contacts()->pluck('id');

        $recentLogs = ClickLog::whereIn('contact_id', $contactIds)
            ->with('contact:id,name,username')
            ->select([
                'id', 'contact_id', 'ip_address', 'country', 'city',
                'device_type', 'device_name', 'device_brand',
                'browser_name', 'os_name', 'clicked_at'
            ])
            ->orderBy('clicked_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $recentLogs
        ]);
    }

    /**
     * Get click analytics by date range
     */
    public function getAnalyticsByDateRange(Request $request)
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $contactIds = $admin->contacts()->pluck('id');

        $logs = ClickLog::whereIn('contact_id', $contactIds)
            ->whereBetween('clicked_at', [$request->start_date, $request->end_date])
            ->get();

        // Group by date with enhanced metrics
        $dailyStats = $logs->groupBy(function ($log) {
            return $log->clicked_at->format('Y-m-d');
        })->map(function ($dayLogs) {
            return [
                'total_clicks' => $dayLogs->count(),
                'unique_visitors' => $dayLogs->unique('ip_address')->count(),
                'countries' => $dayLogs->whereNotNull('country')->unique('country')->count(),
                'mobile_clicks' => $dayLogs->where('device_type', 'mobile')->count(),
                'desktop_clicks' => $dayLogs->where('device_type', 'desktop')->count(),
                'tablet_clicks' => $dayLogs->where('device_type', 'tablet')->count(),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'daily_statistics' => $dailyStats,
                'summary' => [
                    'total_clicks' => $logs->count(),
                    'unique_visitors' => $logs->unique('ip_address')->count(),
                    'countries_reached' => $logs->whereNotNull('country')->unique('country')->count(),
                    'device_breakdown' => [
                        'mobile' => $logs->where('device_type', 'mobile')->count(),
                        'tablet' => $logs->where('device_type', 'tablet')->count(),
                        'desktop' => $logs->where('device_type', 'desktop')->count(),
                        'robot' => $logs->where('device_type', 'robot')->count(),
                    ],
                    'date_range' => [
                        'start' => $request->start_date,
                        'end' => $request->end_date
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get device analytics
     */
    public function getDeviceAnalytics()
    {
        $admin = auth()->user();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $contactIds = $admin->contacts()->pluck('id');
        $logs = ClickLog::whereIn('contact_id', $contactIds)->get();

        $deviceAnalytics = [
            'device_types' => [
                'mobile' => $logs->where('device_type', 'mobile')->count(),
                'tablet' => $logs->where('device_type', 'tablet')->count(),
                'desktop' => $logs->where('device_type', 'desktop')->count(),
                'robot' => $logs->where('device_type', 'robot')->count(),
            ],
            'top_devices' => $logs->whereNotNull('device_name')
                ->groupBy('device_name')
                ->map->count()
                ->sortDesc()
                ->take(15)
                ->toArray(),
            'device_brands' => $logs->whereNotNull('device_brand')
                ->groupBy('device_brand')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
            'browsers' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
            'operating_systems' => $logs->whereNotNull('os_name')
                ->groupBy('os_name')
                ->map->count()
                ->sortDesc()
                ->take(10)
                ->toArray(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $deviceAnalytics
        ]);
    }

    /**
     * Test device detection from user agent
     */
    public function testDeviceDetection(Request $request)
    {
        $userAgent = $request->header('User-Agent') ?? $request->input('user_agent');

        if (!$userAgent) {
            return response()->json([
                'status' => 'error',
                'message' => 'No user agent provided'
            ], 400);
        }

        $deviceInfo = $this->deviceDetectionService->getDeviceInfo($userAgent);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user_agent' => $userAgent,
                'device_info' => $deviceInfo
            ]
        ]);
    }
}
