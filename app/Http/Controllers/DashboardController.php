<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Contact;
use App\Models\MessageLog;
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

        return view('dashboard.index', compact(
            'currentAdmin',
            'contactCount',
            'messagesSent',
            'sentInvitations',
            'pendingInvitations',
            'failedInvitations',
            'groomContactCount',
            'brideContactCount',
            'groomSentCount',
            'groomPendingCount',
            'groomFailedCount',
            'brideSentCount',
            'bridePendingCount',
            'brideFailedCount'
        ));
    }
}
