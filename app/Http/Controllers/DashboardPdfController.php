<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\APIComplaint;
use Illuminate\Support\Facades\Storage;

class DashboardPdfController extends Controller
{
    /**
     * Export the Charts Dashboard as a downloadable PDF.
     *
     * Route:  GET /dashboard/export-pdf
     * Usage:  return redirect()->route('dashboard.export.pdf');
     *
     * Requires:  composer require barryvdh/laravel-dompdf
     */
    public function saveChart(Request $request)
    {
        // dd($request->daily_chart);
        if (!Storage::exists('charts')) {
            Storage::makeDirectory('charts');
        }

        // 🔵 Status Chart
        if ($request->status_chart) {
            $img1 = str_replace('data:image/png;base64,', '', $request->status_chart);
            $img1 = str_replace(' ', '+', $img1);
            Storage::put('charts/status_chart.png', base64_decode($img1));
        }

        // 🟢 Daily Chart
        if ($request->daily_chart) {
            $img2 = str_replace('data:image/png;base64,', '', $request->daily_chart);
            $img2 = str_replace(' ', '+', $img2);
            Storage::put('charts/daily_chart.png', base64_decode($img2));
        }

        return response()->json(['success' => true]);
    }
    public function exportPdf()
    {
        // ── 1. KPI Counters ──────────────────────────────────────────────────
        $result['totalComplaints']    = APIComplaint::count();
        $result['pendingComplaints']  = APIComplaint::where('status', 0)->count();
        $result['resolvedComplaints'] = APIComplaint::where('status', 1)->count();
        $result['reopenedComplaints'] = APIComplaint::where('status', 2)->count();
        $result['closedComplaints']   = APIComplaint::where('status', 3)->count();
        $result['safetyComplaints']   = APIComplaint::where('is_safety_critical', 1)->count();

        // ── 2. Time-based KPIs ───────────────────────────────────────────────
        $result['todayComplaints'] = APIComplaint::whereDate('created_at', now()->toDateString())->count();

        $result['yesterdayComplaints'] = APIComplaint::whereDate('created_at', now()->subDay()->toDateString())->count();

        $result['lastMonthComplaints'] = APIComplaint::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        $result['delayComplaints'] = APIComplaint::whereDate('timeline', '<', now())
            ->whereNotIn('status', [1, 3])
            ->count();

        // ── 3. Monthly Trend (last 6 months) ─────────────────────────────────
        $result['organizationByMonth'] = [
            'label' => collect(range(5, 0))->map(
                fn($i) => now()->subMonths($i)->format('M Y')
            )->values()->toArray(),

            'data' => collect(range(5, 0))->map(
                fn($i) => APIComplaint::whereYear('created_at',  now()->subMonths($i)->year)
                    ->whereMonth('created_at', now()->subMonths($i)->month)
                    ->count()
            )->values()->toArray(),
        ];

        $result['paymentByMonth'] = [
            'label' => $result['organizationByMonth']['label'],

            'data' => collect(range(5, 0))->map(
                fn($i) => APIComplaint::whereYear('created_at',  now()->subMonths($i)->year)
                    ->whereMonth('created_at', now()->subMonths($i)->month)
                    ->where('status', 1)
                    ->count()
            )->values()->toArray(),
        ];

        // ── 4. Department Breakdown ───────────────────────────────────────────
        $result['byDepartment'] = APIComplaint::query()
            ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
            ->selectRaw("
                departments.id,
                departments.name                        AS department_name,
                COUNT(*)                                AS total,
                SUM(api_complaints.is_safety_critical)  AS critical,
                SUM(api_complaints.status = 0)          AS pending,
                SUM(api_complaints.status = 1)          AS rectified,
                SUM(api_complaints.status = 2)          AS reopened,
                SUM(api_complaints.status = 3)          AS closed
            ")
            ->groupBy('departments.id', 'departments.name')
            ->orderByDesc('total')
            ->get();

        // ── 5. Station Breakdown (top 6) ──────────────────────────────────────
        $result['byStation'] = APIComplaint::query()
            ->join('stations', 'api_complaints.station_id', '=', 'stations.id')
            ->selectRaw("
                stations.id,
                stations.name                           AS station_name,
                stations.code                           AS station_code,
                COUNT(*)                                AS total,
                SUM(api_complaints.status = 0)          AS pending,
                SUM(api_complaints.status = 1)          AS rectified,
                SUM(api_complaints.status = 2)          AS reopened,
                SUM(api_complaints.status = 3)          AS closed
            ")
            ->groupBy('stations.id', 'stations.name', 'stations.code')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        // ── 6. Daily Trend (last 14 days) ─────────────────────────────────────
        $result['dailyTrend'] = APIComplaint::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── 7. Recent Complaints ──────────────────────────────────────────────
        $result['recentComplaints'] = APIComplaint::with(['department', 'station', 'amenityCategory'])
            ->latest()
            ->take(10)
            ->get();

        // ── 8. Computed helpers ───────────────────────────────────────────────
        $total = $result['totalComplaints'] ?: 1;
        $result['resolutionRate'] = round(
            (($result['resolvedComplaints'] + $result['closedComplaints']) / $total) * 100
        );
        $result['criticalRate'] = round(($result['safetyComplaints'] / $total) * 100);
        $result['generatedAt']  = now()->format('d M Y, H:i');

        $chartPath = storage_path('charts/status_chart.png');

        // $statusChartPath = storage_path('app/charts/status_chart.png');
        $dailyChartPath  = storage_path('charts/daily_chart.png');

        // dd($chartPath);

        // ── 9. Render PDF ─────────────────────────────────────────────────────
        $pdf = Pdf::loadView('dashboard.pdf-report', compact('result', 'chartPath' , 'dailyChartPath'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 150,
            ]);

        $filename = 'complaints-dashboard-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}
