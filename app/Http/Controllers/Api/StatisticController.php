<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contact;
use App\Models\MessageLog;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function getStatistics()
    {
        $groomAdmin = Admin::where('role', 'groom')->first();
        $brideAdmin = Admin::where('role', 'bride')->first();

        $groomContactCount = $groomAdmin ? $groomAdmin->contacts()->count() : 0;
        $brideContactCount = $brideAdmin ? $brideAdmin->contacts()->count() : 0;

        $groomSentCount = $groomAdmin ? MessageLog::where('admin_id', $groomAdmin->id)
            ->where('status', 'sent')
            ->count() : 0;

        $brideSentCount = $brideAdmin ? MessageLog::where('admin_id', $brideAdmin->id)
            ->where('status', 'sent')
            ->count() : 0;

        return response()->json([
            'status' => 'success',
            'data' => [
                'groom' => [
                    'contact_count' => $groomContactCount,
                    'sent_count' => $groomSentCount,
                ],
                'bride' => [
                    'contact_count' => $brideContactCount,
                    'sent_count' => $brideSentCount,
                ],
                'total' => [
                    'contact_count' => $groomContactCount + $brideContactCount,
                    'sent_count' => $groomSentCount + $brideSentCount,
                ],
            ],
        ]);
    }
}
