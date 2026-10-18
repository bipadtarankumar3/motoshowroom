<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PlatformAnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    /**
     * Display SaaS platform-wide executive metrics and MRR/ARR oversight.
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', '30d');

        if (! in_array($period, ['7d', '30d', '90d', '365d', 'all'])) {
            $period = '30d';
        }

        $analytics = $this->analyticsService->getPlatformAnalytics($period);

        return view('admin.analytics.index', compact('analytics', 'period'));
    }
}
