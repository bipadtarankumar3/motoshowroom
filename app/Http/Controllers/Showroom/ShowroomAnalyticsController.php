<?php

namespace App\Http\Controllers\Showroom;

use App\Http\Controllers\Controller;
use App\Models\Showroom;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ShowroomAnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Display the dealership multi-dimensional analytics dashboard.
     */
    public function index(Request $request, Showroom $showroom): View
    {
        $hasFeature = $showroom->canAccessFeature('has_advanced_analytics');
        $period = $request->query('period', '30d');

        if (! in_array($period, ['7d', '30d', '90d', '365d', 'all'])) {
            $period = '30d';
        }

        $analytics = $hasFeature
            ? $this->analyticsService->getDealershipAnalytics($showroom, $period)
            : null;

        return view('showrooms.manage.analytics.index', compact('showroom', 'hasFeature', 'period', 'analytics'));
    }

    /**
     * Export structured analytical CSV reports for the showroom.
     */
    public function export(Request $request, Showroom $showroom): Response
    {
        if (! $showroom->canAccessFeature('has_advanced_analytics')) {
            abort(403, 'Your subscription plan does not include Advanced Analytics & CSV Export.');
        }

        $request->validate([
            'type' => ['required', 'string', 'in:revenue,enquiries,workshop'],
            'period' => ['nullable', 'string', 'in:7d,30d,90d,365d,all'],
        ]);

        $reportType = $request->input('type');
        $period = $request->input('period', '30d');

        $csvData = $this->analyticsService->exportCsv($showroom, $reportType, $period);
        $filename = "{$showroom->slug}-{$reportType}-report-".now()->format('YmdHis').'.csv';

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
