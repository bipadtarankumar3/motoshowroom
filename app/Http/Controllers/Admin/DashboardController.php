<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Super Admin central platform dashboard.
     */
    public function index(): View
    {
        $stats = [
            'total_showrooms' => Showroom::count(),
            'active_showrooms' => Showroom::where('status', 'active')->count(),
            'trial_showrooms' => Showroom::where('status', 'trial')->count(),
            'suspended_showrooms' => Showroom::where('status', 'suspended')->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'total_audit_events' => AuditLog::count(),
        ];

        $recentShowrooms = Showroom::with('ownerMembership.user')
            ->latest()
            ->take(5)
            ->get();

        $recentAuditLogs = AuditLog::with(['user', 'showroom'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentShowrooms', 'recentAuditLogs'));
    }
}
