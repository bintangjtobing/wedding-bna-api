<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\MessageLog;
use App\Models\ClickLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentAdmin = Auth::guard('admin')->user();
        $contactCount = $currentAdmin->contacts()->count();
        $messagesSent = MessageLog::where('admin_id', $currentAdmin->id)
            ->where('status', 'sent')
            ->count();

        // Tambahkan informasi status undangan
        $sentInvitations = $currentAdmin->contacts()->where('invitation_status', 'terkirim')->count();
        $pendingInvitations = $currentAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count();
        $failedInvitations = $currentAdmin->contacts()->where('invitation_status', 'gagal')->count();

        // Click Analytics untuk current admin
        $adminContactIds = $currentAdmin->contacts()->pluck('id');
        $clickLogs = ClickLog::whereIn('contact_id', $adminContactIds)->get();

        $clickAnalytics = [
            'total_clicks' => $clickLogs->count(),
            'unique_visitors' => $clickLogs->unique('ip_address')->count(),
            'countries_reached' => $clickLogs->whereNotNull('country')->unique('country')->count(),
            'cities_reached' => $clickLogs->whereNotNull('city')->unique('city')->count(),
            'recent_clicks' => $clickLogs->sortByDesc('clicked_at')->take(5),
            'top_countries' => $clickLogs->whereNotNull('country')
                ->groupBy('country')
                ->map->count()
                ->sortDesc()
                ->take(5),
            'device_breakdown' => $clickLogs->whereNotNull('device_type')
                ->groupBy('device_type')
                ->map->count()
                ->sortDesc(),
        ];

        $groomAdmin = Admin::where('role', 'groom')->first();
        $brideAdmin = Admin::where('role', 'bride')->first();
        $groomContactCount = $groomAdmin ? $groomAdmin->contacts()->count() : 0;
        $brideContactCount = $brideAdmin ? $brideAdmin->contacts()->count() : 0;

        // Status undangan untuk masing-masing admin
        $groomSentCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $groomPendingCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;
        $groomFailedCount = $groomAdmin ? $groomAdmin->contacts()->where('invitation_status', 'gagal')->count() : 0;

        $brideSentCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'terkirim')->count() : 0;
        $bridePendingCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'belum_dikirim')->count() : 0;
        $brideFailedCount = $brideAdmin ? $brideAdmin->contacts()->where('invitation_status', 'gagal')->count() : 0;

        // Overall Click Analytics
        $groomContactIds = $groomAdmin ? $groomAdmin->contacts()->pluck('id') : collect();
        $brideContactIds = $brideAdmin ? $brideAdmin->contacts()->pluck('id') : collect();

        $groomClicks = ClickLog::whereIn('contact_id', $groomContactIds)->count();
        $brideClicks = ClickLog::whereIn('contact_id', $brideContactIds)->count();
        $totalClicks = $groomClicks + $brideClicks;

        $groomUniqueVisitors = ClickLog::whereIn('contact_id', $groomContactIds)->distinct('ip_address')->count();
        $brideUniqueVisitors = ClickLog::whereIn('contact_id', $brideContactIds)->distinct('ip_address')->count();

        return view('dashboard.index', compact(
            'currentAdmin',
            'contactCount',
            'messagesSent',
            'sentInvitations',
            'pendingInvitations',
            'failedInvitations',
            'clickAnalytics',
            'groomContactCount',
            'brideContactCount',
            'groomSentCount',
            'groomPendingCount',
            'groomFailedCount',
            'brideSentCount',
            'bridePendingCount',
            'brideFailedCount',
            'groomClicks',
            'brideClicks',
            'totalClicks',
            'groomUniqueVisitors',
            'brideUniqueVisitors'
        ));
    }
}
