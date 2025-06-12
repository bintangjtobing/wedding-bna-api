<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClickLog;
use App\Models\Contact;
use App\Services\ClickLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    protected $clickLogService;

    public function __construct(ClickLogService $clickLogService)
    {
        $this->clickLogService = $clickLogService;
    }

    /**
     * Display analytics dashboard
     */
    public function index()
    {
        $currentAdmin = Auth::guard('admin')->user();
        $contactIds = $currentAdmin->contacts()->pluck('id');
        $logs = ClickLog::whereIn('contact_id', $contactIds)->get();

        $analytics = [
            'total_clicks' => $logs->count(),
            'unique_visitors' => $logs->unique('ip_address')->count(),
            'countries_reached' => $logs->whereNotNull('country')->unique('country')->count(),
            'cities_reached' => $logs->whereNotNull('city')->unique('city')->count(),
            'recent_activities' => $logs->sortByDesc('clicked_at')->take(10),
            'top_countries' => $logs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(10),
            'top_cities' => $logs->whereNotNull('city')
                ->groupBy('city')
                ->map->count()
                ->sortDesc()
                ->take(10),
            'device_breakdown' => $logs->whereNotNull('device_type')
                ->groupBy('device_type')
                ->map->count()
                ->sortDesc(),
            'browser_breakdown' => $logs->whereNotNull('browser_name')
                ->groupBy('browser_name')
                ->map->count()
                ->sortDesc()
                ->take(10),
            'os_breakdown' => $logs->whereNotNull('os_name')
                ->groupBy('os_name')
                ->map->count()
                ->sortDesc()
                ->take(10),
            'daily_clicks' => $logs->groupBy(function ($log) {
                return $log->clicked_at->format('Y-m-d');
            })->map->count()->sortKeys()->take(30),
            'hourly_pattern' => $logs->groupBy(function ($log) {
                return $log->clicked_at->format('H');
            })->map->count()->sortKeys(),
        ];

        // Get contact-specific analytics
        $contactAnalytics = $currentAdmin->contacts()
            ->with('clickLogs')
            ->get()
            ->map(function ($contact) {
                $clickCount = $contact->clickLogs->count();
                return [
                    'contact' => $contact,
                    'clicks' => $clickCount,
                    'unique_visitors' => $contact->clickLogs->unique('ip_address')->count(),
                    'last_click' => $contact->clickLogs->max('clicked_at'),
                ];
            })
            ->sortByDesc('clicks');

        return view('analytics.index', compact('analytics', 'contactAnalytics'));
    }

    /**
     * Display detailed analytics for a specific contact
     */
    public function contactDetail(Contact $contact)
    {
        $currentAdmin = Auth::guard('admin')->user();

        // Check if contact belongs to current admin
        if ($contact->admin_id !== $currentAdmin->id) {
            abort(403, 'Unauthorized access to this contact.');
        }

        $stats = $this->clickLogService->getClickStats($contact);
        $logs = $contact->clickLogs()->orderBy('clicked_at', 'desc')->paginate(50);

        // Additional analytics
        $dailyClicks = $contact->clickLogs()
            ->selectRaw('DATE(clicked_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->pluck('count', 'date');

        $locationBreakdown = $contact->clickLogs()
            ->whereNotNull('country')
            ->selectRaw('country, city, COUNT(*) as count')
            ->groupBy('country', 'city')
            ->orderBy('count', 'desc')
            ->get();

        return view('analytics.contact', compact('contact', 'stats', 'logs', 'dailyClicks', 'locationBreakdown'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $currentAdmin = Auth::guard('admin')->user();
        $contactIds = $currentAdmin->contacts()->pluck('id');

        $logs = ClickLog::whereIn('contact_id', $contactIds)
            ->with('contact')
            ->orderBy('clicked_at', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="click_analytics_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Write CSV header
            fputcsv($file, [
                'Contact Name',
                'Username',
                'IP Address',
                'Country',
                'City',
                'Region',
                'Latitude',
                'Longitude',
                'Device Name',
                'Device Type',
                'Device Brand',
                'OS Name',
                'Browser Name',
                'Click Time'
            ]);

            // Write data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->name,
                    $log->username,
                    $log->ip_address,
                    $log->country,
                    $log->city,
                    $log->region,
                    $log->latitude,
                    $log->longitude,
                    $log->device_name,
                    $log->device_type,
                    $log->device_brand,
                    $log->os_name,
                    $log->browser_name,
                    $log->clicked_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
