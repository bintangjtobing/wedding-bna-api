<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClickLog;
use App\Models\Contact;
use App\Services\ClickLogService;
use Illuminate\Http\Request;

class ClickLogController extends Controller
{
    protected $clickLogService;

    public function __construct(ClickLogService $clickLogService)
    {
        $this->clickLogService = $clickLogService;
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
            'device_types' => $logs->whereNotNull('device_type')
                ->groupBy('device_type')
                ->map->count()
                ->sortDesc()
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

        return response()->json([
            'status' => 'success',
            'data' => [
                'contact' => $contact,
                'statistics' => $stats,
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

        // Group by date
        $dailyStats = $logs->groupBy(function ($log) {
            return $log->clicked_at->format('Y-m-d');
        })->map(function ($dayLogs) {
            return [
                'total_clicks' => $dayLogs->count(),
                'unique_visitors' => $dayLogs->unique('ip_address')->count(),
                'countries' => $dayLogs->whereNotNull('country')->unique('country')->count(),
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
                    'date_range' => [
                        'start' => $request->start_date,
                        'end' => $request->end_date
                    ]
                ]
            ]
        ]);
    }
}
