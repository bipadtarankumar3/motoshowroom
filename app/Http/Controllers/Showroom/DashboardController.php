<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the authenticated showroom operational back-office dashboard.
     */
    public function index(Showroom $showroom): View
    {
        $showroom->load(['subscription.plan', 'memberships.user']);

        $metrics = [
            'bikes_count' => 0,
            'active_enquiries' => 0,
            'today_services' => 0,
            'open_jobs' => 0,
            'active_campaigns' => 0,
            'staff_count' => $showroom->memberships->count(),
        ];

        $recentActivities = AuditLog::where('tenant_id', $showroom->id)
            ->with('user')
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('showrooms.manage', compact('showroom', 'metrics', 'recentActivities'));
    }
}
