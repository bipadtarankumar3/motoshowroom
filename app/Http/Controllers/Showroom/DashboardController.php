<?php

namespace App\Http\Controllers\Showroom;

use App\Enums\BookingStatus;
use App\Enums\EnquiryStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Showroom;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the authenticated showroom operational back-office dashboard with rich metrics, charts & tables.
     */
    public function index(Showroom $showroom): View
    {
        $showroom->load(['subscription.plan', 'memberships.user']);

        // 1. High-Level KPI Metric Counts
        $totalBikes = $showroom->bikes()->count();
        $publishedBikes = $showroom->publishedBikes()->count();
        $totalEnquiries = $showroom->enquiries()->count();
        $activeEnquiries = $showroom->activeEnquiries()->count();
        $todayServices = $showroom->todayServiceBookings()->active()->count();
        $totalServices = $showroom->serviceBookings()->count();
        $staffCount = $showroom->memberships()->count();
        $activeCampaigns = $showroom->activePromotionalCampaigns()->count();

        // Financial GMV calculation
        $grossRevenue = (float) $showroom->payments()
            ->where('status', PaymentStatus::Captured->value)
            ->sum('amount');

        $metrics = [
            'bikes_count' => $totalBikes,
            'published_bikes' => $publishedBikes,
            'total_enquiries' => $totalEnquiries,
            'active_enquiries' => $activeEnquiries,
            'today_services' => $todayServices,
            'total_services' => $totalServices,
            'open_jobs' => $showroom->activeJobOpenings()->count(),
            'active_campaigns' => $activeCampaigns,
            'staff_count' => $staffCount,
            'gross_revenue' => $grossRevenue,
            'formatted_revenue' => '₹'.number_format($grossRevenue, 2),
            'conversion_rate' => $totalEnquiries > 0
                ? round(($showroom->enquiries()->where('status', EnquiryStatus::BookingPending->value)->count() / $totalEnquiries) * 100, 1)
                : 0,
        ];

        // 2. Six-Month Trend Chart Data (Monthly Enquiries, Service Bookings & Revenue)
        $chartMonths = [];
        $enquiryTrend = [];
        $serviceTrend = [];
        $revenueTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $startOfMonth = $monthDate->copy()->startOfMonth();
            $endOfMonth = $monthDate->copy()->endOfMonth();

            $chartMonths[] = $monthDate->format('M');

            $enquiryTrend[] = $showroom->enquiries()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $serviceTrend[] = $showroom->serviceBookings()
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $revenueTrend[] = (float) $showroom->payments()
                ->where('status', PaymentStatus::Captured->value)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->sum('amount');
        }

        // 3. Enquiry Funnel Breakdown
        $enquiryFunnel = [
            'new' => $showroom->enquiries()->where('status', EnquiryStatus::New->value)->count(),
            'contacted' => $showroom->enquiries()->where('status', EnquiryStatus::Contacted->value)->count(),
            'test_ride' => $showroom->enquiries()->where('status', EnquiryStatus::TestRideScheduled->value)->count(),
            'negotiation' => $showroom->enquiries()->where('status', EnquiryStatus::Negotiation->value)->count(),
            'booked' => $showroom->enquiries()->where('status', EnquiryStatus::BookingPending->value)->count(),
        ];

        // 4. Workshop Service Status Distribution
        $serviceDistribution = [
            'pending' => $showroom->serviceBookings()->where('status', BookingStatus::Pending->value)->count(),
            'confirmed' => $showroom->serviceBookings()->where('status', BookingStatus::Confirmed->value)->count(),
            'in_service' => $showroom->serviceBookings()->where('status', BookingStatus::InService->value)->count(),
            'ready' => $showroom->serviceBookings()->where('status', BookingStatus::ReadyForCollection->value)->count(),
            'completed' => $showroom->serviceBookings()->where('status', BookingStatus::Completed->value)->count(),
        ];

        $chartData = [
            'months' => $chartMonths,
            'enquiries' => $enquiryTrend,
            'services' => $serviceTrend,
            'revenue' => $revenueTrend,
            'enquiry_funnel' => array_values($enquiryFunnel),
            'service_distribution' => array_values($serviceDistribution),
        ];

        // 5. Recent Enquiries & Leads (Table 1)
        $recentEnquiries = $showroom->enquiries()
            ->with(['bike', 'assignedStaff'])
            ->latest('created_at')
            ->take(5)
            ->get();

        // 6. Upcoming / Recent Service Appointments (Table 2)
        $recentBookings = $showroom->serviceBookings()
            ->with(['showroomService', 'user'])
            ->latest('booking_date')
            ->take(5)
            ->get();

        // 7. Recent Featured / Inventory Motorcycles (Table 3)
        $latestBikes = $showroom->bikes()
            ->latest('created_at')
            ->take(4)
            ->get();

        // 8. Recent System Activity Trail
        $recentActivities = AuditLog::where('tenant_id', $showroom->id)
            ->with('user')
            ->latest('created_at')
            ->take(6)
            ->get();

        return view('showrooms.manage', compact(
            'showroom',
            'metrics',
            'chartData',
            'recentEnquiries',
            'recentBookings',
            'latestBikes',
            'recentActivities'
        ));
    }
}
