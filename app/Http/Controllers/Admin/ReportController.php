<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        return view('admin.reports.index', [
            'from' => $from ?? now()->subDays(30)->toDateString(),
            'to' => $to ?? now()->toDateString(),
            'summary' => $this->reportService->summary($from, $to),
            'dailyRevenue' => $this->reportService->dailyRevenue($from, $to),
            'bookingsByStatus' => $this->reportService->bookingsByStatus($from, $to),
            'paymentsByMethod' => $this->reportService->paymentsByMethod($from, $to),
            'topRoutes' => $this->reportService->topRoutes($from, $to),
            'recentPayments' => $this->reportService->recentPayments($from, $to),
            'monthlyTrend' => $this->reportService->monthlyTrend(),
        ]);
    }
}
