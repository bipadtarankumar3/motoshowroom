<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ServiceBooking;
use App\Models\Showroom;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class AnalyticsService
{
    /**
     * Compute comprehensive multi-dimensional dealership analytics.
     *
     * @return array<string, mixed>
     */
    public function getDealershipAnalytics(Showroom $showroom, string $period = '30d'): array
    {
        $startDate = $this->resolveStartDate($period);

        // 1. Financial & Revenue Metrics
        $paymentsQuery = $showroom->payments()
            ->where('status', 'captured');

        if ($startDate !== null) {
            $paymentsQuery->where('created_at', '>=', $startDate);
        }

        $capturedPayments = $paymentsQuery->get();
        $totalRevenue = (float) $capturedPayments->sum('amount');
        $totalTransactions = $capturedPayments->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0.0;

        $serviceRevenue = (float) $capturedPayments->where('payable_type', ServiceBooking::class)->sum('amount');
        $subscriptionRevenue = (float) $capturedPayments->where('payable_type', Subscription::class)->sum('amount');
        $otherRevenue = $totalRevenue - ($serviceRevenue + $subscriptionRevenue);

        // 2. Enquiries & Sales Conversion Funnel
        $enquiriesQuery = $showroom->enquiries();
        if ($startDate !== null) {
            $enquiriesQuery->where('created_at', '>=', $startDate);
        }
        $enquiries = $enquiriesQuery->with('bike')->get();
        $totalEnquiries = $enquiries->count();

        $getVal = fn ($prop) => $prop instanceof \BackedEnum ? $prop->value : (string) $prop;

        $enquiriesByType = [
            'purchase' => $enquiries->filter(fn ($e) => $getVal($e->type) === 'purchase')->count(),
            'test_ride' => $enquiries->filter(fn ($e) => $getVal($e->type) === 'test_ride')->count(),
            'general' => $enquiries->filter(fn ($e) => $getVal($e->type) === 'general')->count(),
        ];

        $funnelStages = [
            'new' => $enquiries->filter(fn ($e) => $getVal($e->status) === 'new')->count(),
            'contacted' => $enquiries->filter(fn ($e) => $getVal($e->status) === 'contacted')->count(),
            'test_ride_scheduled' => $enquiries->filter(fn ($e) => $getVal($e->status) === 'test_ride_scheduled')->count(),
            'negotiation' => $enquiries->filter(fn ($e) => $getVal($e->status) === 'negotiation')->count(),
            'completed' => $enquiries->filter(fn ($e) => in_array($getVal($e->status), ['completed', 'booked']))->count(),
        ];

        $closedWonCount = $funnelStages['completed'];
        $conversionRate = $totalEnquiries > 0 ? round(($closedWonCount / $totalEnquiries) * 100, 1) : 0.0;

        // Top Enquired Motorcycles
        $topBikes = $enquiries->filter(fn ($e) => $e->bike !== null)
            ->groupBy('bike_id')
            ->map(fn ($group) => [
                'name' => $group->first()->bike->name ?? 'Unknown Model',
                'brand' => $group->first()->bike->brand ?? 'Brand',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        // 3. Workshop Operations Metrics
        $bookingsQuery = $showroom->serviceBookings();
        if ($startDate !== null) {
            $bookingsQuery->where('created_at', '>=', $startDate);
        }
        $bookings = $bookingsQuery->get();
        $totalBookings = $bookings->count();

        $bookingsByStatus = [
            'pending' => $bookings->filter(fn ($b) => $getVal($b->status) === 'pending')->count(),
            'confirmed' => $bookings->filter(fn ($b) => $getVal($b->status) === 'confirmed')->count(),
            'in_service' => $bookings->filter(fn ($b) => $getVal($b->status) === 'in_service')->count(),
            'ready' => $bookings->filter(fn ($b) => in_array($getVal($b->status), ['ready_for_collection', 'ready_for_pickup']))->count(),
            'completed' => $bookings->filter(fn ($b) => $getVal($b->status) === 'completed')->count(),
            'cancelled' => $bookings->filter(fn ($b) => $getVal($b->status) === 'cancelled')->count(),
        ];

        $completedBookings = $bookingsByStatus['completed'];
        $workshopCompletionRate = $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0.0;

        // 4. Promotional Scratch Cards & Marketing
        $participationsQuery = $showroom->scratchCardParticipations();
        if ($startDate !== null) {
            $participationsQuery->where('created_at', '>=', $startDate);
        }
        $participations = $participationsQuery->get();
        $totalScratchCards = $participations->count();
        $scratchedCount = $participations->where('is_scratched', true)->count();
        $redeemedCount = $participations->where('is_redeemed', true)->count();
        $redemptionRate = $scratchedCount > 0 ? round(($redeemedCount / $scratchedCount) * 100, 1) : 0.0;

        // 5. AI Virtual Assistant Performance
        $aiConversationsQuery = $showroom->aiConversations();
        if ($startDate !== null) {
            $aiConversationsQuery->where('created_at', '>=', $startDate);
        }
        $aiConversations = $aiConversationsQuery->with('messages')->get();
        $totalAiChats = $aiConversations->count();
        $aiConvertedLeads = $aiConversations->where('lead_converted', true)->count();
        $aiLeadRate = $totalAiChats > 0 ? round(($aiConvertedLeads / $totalAiChats) * 100, 1) : 0.0;

        return [
            'period' => $period,
            'start_date' => $startDate?->toDateString(),
            'revenue' => [
                'total' => $totalRevenue,
                'formatted_total' => '₹'.number_format($totalRevenue, 2),
                'total_transactions' => $totalTransactions,
                'avg_transaction_value' => '₹'.number_format($avgTransactionValue, 2),
                'service_revenue' => $serviceRevenue,
                'subscription_revenue' => $subscriptionRevenue,
                'other_revenue' => $otherRevenue,
            ],
            'enquiries' => [
                'total' => $totalEnquiries,
                'by_type' => $enquiriesByType,
                'funnel' => $funnelStages,
                'closed_won' => $closedWonCount,
                'conversion_rate' => $conversionRate,
                'top_bikes' => $topBikes,
            ],
            'workshop' => [
                'total' => $totalBookings,
                'by_status' => $bookingsByStatus,
                'completed' => $completedBookings,
                'completion_rate' => $workshopCompletionRate,
            ],
            'marketing' => [
                'scratch_cards_total' => $totalScratchCards,
                'scratched_count' => $scratchedCount,
                'redeemed_count' => $redeemedCount,
                'redemption_rate' => $redemptionRate,
            ],
            'ai_assistant' => [
                'total_chats' => $totalAiChats,
                'converted_leads' => $aiConvertedLeads,
                'conversion_rate' => $aiLeadRate,
            ],
        ];
    }

    /**
     * Compute platform-wide metrics for Super Administrator oversight.
     *
     * @return array<string, mixed>
     */
    public function getPlatformAnalytics(string $period = '30d'): array
    {
        $startDate = $this->resolveStartDate($period);

        // Platform GMV & Payments
        $paymentsQuery = Payment::where('status', 'captured');
        if ($startDate !== null) {
            $paymentsQuery->where('created_at', '>=', $startDate);
        }
        $capturedPayments = $paymentsQuery->get();
        $platformGmv = (float) $capturedPayments->sum('amount');
        $totalTransactions = $capturedPayments->count();

        // Showrooms Breakdown
        $totalShowrooms = Showroom::count();
        $activeShowrooms = Showroom::where('status', 'active')->count();
        $trialShowrooms = Showroom::where('status', 'trial')->count();
        $suspendedShowrooms = Showroom::where('status', 'suspended')->count();

        // Subscriptions & MRR / ARR calculation
        $activeSubs = Subscription::where('status', 'active')->with('plan')->get();
        $mrr = 0.0;
        foreach ($activeSubs as $sub) {
            if ($sub->plan) {
                if ($sub->billing_interval === 'annual') {
                    $mrr += ((float) $sub->plan->annual_price) / 12;
                } else {
                    $mrr += (float) $sub->plan->monthly_price;
                }
            }
        }
        $arr = $mrr * 12;

        // Plan distribution
        $plansBreakdown = SubscriptionPlan::withCount(['subscriptions' => function ($q) {
            $q->where('status', 'active');
        }])->get()->map(fn ($p) => [
            'name' => $p->name,
            'slug' => $p->slug,
            'monthly_price' => $p->monthly_price,
            'active_subscribers' => $p->subscriptions_count,
        ]);

        return [
            'period' => $period,
            'platform_gmv' => $platformGmv,
            'formatted_gmv' => '₹'.number_format($platformGmv, 2),
            'total_transactions' => $totalTransactions,
            'mrr' => $mrr,
            'formatted_mrr' => '₹'.number_format($mrr, 2),
            'arr' => $arr,
            'formatted_arr' => '₹'.number_format($arr, 2),
            'showrooms' => [
                'total' => $totalShowrooms,
                'active' => $activeShowrooms,
                'trial' => $trialShowrooms,
                'suspended' => $suspendedShowrooms,
            ],
            'plans_breakdown' => $plansBreakdown,
        ];
    }

    /**
     * Generate structured CSV export data string for dealership reports.
     */
    public function exportCsv(Showroom $showroom, string $reportType, string $period = '30d'): string
    {
        $startDate = $this->resolveStartDate($period);
        $csv = fopen('php://temp', 'r+');

        if ($reportType === 'revenue') {
            fputcsv($csv, ['Payment ID', 'Reference', 'Type', 'Amount (INR)', 'Currency', 'Status', 'Method', 'Date']);
            $payments = $showroom->payments()->latest();
            if ($startDate) {
                $payments->where('created_at', '>=', $startDate);
            }
            foreach ($payments->get() as $p) {
                fputcsv($csv, [
                    $p->id,
                    $p->razorpay_payment_id ?? $p->gateway_payment_id ?? $p->order_id,
                    class_basename($p->payable_type ?? 'Standard'),
                    $p->amount,
                    $p->currency,
                    $p->status instanceof \BackedEnum ? $p->status->value : (string) $p->status,
                    $p->method ?? 'Razorpay',
                    $p->created_at->toDateTimeString(),
                ]);
            }
        } elseif ($reportType === 'enquiries') {
            fputcsv($csv, ['Enquiry #', 'Type', 'Customer Name', 'Phone', 'Email', 'Motorcycle', 'Status', 'Date']);
            $enquiries = $showroom->enquiries()->with('bike')->latest();
            if ($startDate) {
                $enquiries->where('created_at', '>=', $startDate);
            }
            foreach ($enquiries->get() as $e) {
                fputcsv($csv, [
                    $e->enquiry_number,
                    $e->type instanceof \BackedEnum ? $e->type->value : (string) $e->type,
                    $e->customer_name,
                    $e->customer_phone,
                    $e->customer_email,
                    $e->bike->name ?? 'General',
                    $e->status instanceof \BackedEnum ? $e->status->value : (string) $e->status,
                    $e->created_at->toDateTimeString(),
                ]);
            }
        } elseif ($reportType === 'workshop') {
            fputcsv($csv, ['Booking #', 'Service Package', 'Customer Name', 'Vehicle Reg', 'Cost (INR)', 'Status', 'Booking Date']);
            $bookings = $showroom->serviceBookings()->latest();
            if ($startDate) {
                $bookings->where('created_at', '>=', $startDate);
            }
            foreach ($bookings->get() as $b) {
                $servicePkg = $b->service_package ?? ($b->service_type instanceof \BackedEnum ? $b->service_type->value : (string) $b->service_type) ?? 'Standard Maintenance';
                $regNum = $b->registration_number ?? $b->registration_plate ?? $b->license_plate ?? 'N/A';
                fputcsv($csv, [
                    $b->booking_number ?? "#{$b->id}",
                    $servicePkg,
                    $b->customer_name,
                    $regNum,
                    $b->estimated_cost ?? 0.00,
                    $b->status instanceof \BackedEnum ? $b->status->value : (string) $b->status,
                    $b->booking_date ? (is_string($b->booking_date) ? $b->booking_date : $b->booking_date->toDateString()) : $b->created_at->toDateString(),
                ]);
            }
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $content ?: '';
    }

    /**
     * Resolve start date threshold from period string.
     */
    protected function resolveStartDate(string $period): ?Carbon
    {
        return match ($period) {
            '7d' => now()->subDays(7),
            '90d' => now()->subDays(90),
            '365d' => now()->subDays(365),
            'all' => null,
            default => now()->subDays(30), // Default 30d
        };
    }
}
