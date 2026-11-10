<?php

namespace App\Http\Controllers;

use App\Models\AccidentManualCorrection;
use App\Models\AccidentManualOriginal;
use App\Models\APIComplaint;
use App\Models\CaseStudy;
use App\Models\ClothType;
use App\Models\Contact;
use App\Models\Custom;
use App\Models\Department;
use App\Models\Expense;
use App\Models\FAQ;
use App\Models\GsrCorrection;
use App\Models\GsrOriginal;
use App\Models\HomePage;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\MasterEmployee;
use App\Models\Measurement;
use App\Models\NoticeBoard;
use App\Models\Order;
use App\Models\PackageTransaction;
use App\Models\Page;
use App\Models\RectificationHistory;
use App\Models\Rectify;
use App\Models\Station;
use App\Models\Subscription;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use App\Models\MiscellaneousLetters;
use App\Models\OperatingManualCorrection;
use App\Models\OperatingManualOriginal;

use App\Models\OperatingSafetyAlert;
use App\Models\OperatingSafetyEpisode;
use App\Models\OperatingSafetyDrive;

use App\Models\SafetyCircular;
use App\Models\OperatingSafetyCircular;
use App\Models\Jpo;
use App\Models\OfficerInspectionNote;
use App\Models\TrafficInspectorInspectionNote;
use App\Models\Popup;
class HomeController extends Controller
{
    public function index()
    {

        if (Auth::check()) {
            if (Auth::user()->type == 'owner') {
                $result['totalOrganization'] = User::where('type', 'owner')->count();
                $result['totalSubscription'] = Subscription::count();
                $result['totalTransaction'] = PackageTransaction::count();
                $result['totalIncome'] = PackageTransaction::sum('amount');
                $result['totalNote'] = NoticeBoard::where('parent_id', parentId())->count();
                $result['totalContact'] = Contact::where('parent_id', parentId())->count();

                $result['totalComplaints']        = APIComplaint::count();
                $result['pendingComplaints']         = APIComplaint::where('status', 0)->count();
                $result['resolvedComplaints']     = APIComplaint::where('status', 1)->count();
                $result['reopenedComplaints']     = APIComplaint::where('status', 2)->count();
                $result['closedComplaints']     = APIComplaint::where('status', 3)->count();
                $result['safetyComplaints']       = APIComplaint::where('is_safety_critical', 1)->count();
                $result['recentComplaints'] = APIComplaint::with(['department', 'station', 'amenityCategory'])
                    ->latest()
                    ->take(10)
                    ->get();


                $result['byDepartment'] = APIComplaint::query()
                    ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                    ->selectRaw('departments.name as department_name,
                 COUNT(*) as total,
                 SUM(api_complaints.is_safety_critical) as critical')
                    ->groupBy('departments.id', 'departments.name')
                    ->orderByDesc('total')
                    ->get();

                // ── 2. Daily Complaints – last 14 days (area chart) ───────────
                $result['dailyTrend'] = APIComplaint::query()
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // Today Complaints
                $result['todayComplaints'] = APIComplaint::whereDate('created_at', now()->toDateString())->count();

                // Yesterday Complaints
                $result['yesterdayComplaints'] = APIComplaint::whereDate('created_at', now()->subDay()->toDateString())->count();

                // Last Month Complaints
                $result['lastMonthComplaints'] = APIComplaint::whereYear('created_at', now()->subMonth()->year)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->count();

                // Delay Complaints (timeline < current date AND not closed)
                $result['delayComplaints'] = APIComplaint::whereDate('timeline', '<', now())
                    ->whereNotIn('status', [1, 3]) // not resolved or closed
                    ->count();

                // ── 3. Monthly overview (already used by existing blade) ──────
                // Keep your existing $result['organizationByMonth'] and
                // $result['paymentByMonth'] — the blade uses them for the
                // Monthly Trend line chart.
                //
                // If you don't have those yet, here's a sample implementation:

                $result['organizationByMonth'] = [
                    'label' => collect(range(5, 0))->map(
                        fn($i) =>
                        now()->subMonths($i)->format('M Y')
                    )->values()->toArray(),

                    'data' => collect(range(5, 0))->map(
                        fn($i) =>
                        APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                            ->whereMonth('created_at', now()->subMonths($i)->month)
                            ->count()
                    )->values()->toArray(),
                ];

                $result['paymentByMonth'] = [
                    'label' => $result['organizationByMonth']['label'],

                    'data' => collect(range(5, 0))->map(
                        fn($i) =>
                        APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                            ->whereMonth('created_at', now()->subMonths($i)->month)
                            ->where('status', 1)   // Rectified = "resolved"
                            ->count()
                    )->values()->toArray(),
                ];
                $result['organizationByMonth'] = $this->organizationByMonth();
                $result['paymentByMonth'] = $this->paymentByMonth();

                // dd($result);

                $today = Carbon::today();

                // Birthday List
                $birthdays_employees = MasterEmployee::with(['department', 'designation', 'stations'])
                    ->whereMonth('date_of_birth', $today->month)
                    ->whereDay('date_of_birth', $today->day)
                    ->get();

                // Appointment Anniversary List
                $appointments_employees = MasterEmployee::with(['department', 'designation', 'stations'])
                    ->whereMonth('date_of_appointment', $today->month)
                    ->whereDay('date_of_appointment', $today->day)
                    ->get();

                // Retirement Today List (Exact Date Match)
                $retirements_employees = MasterEmployee::with(['department', 'designation', 'stations'])
                    ->whereDate('date_of_retirement', $today)
                    ->get();

                $result['stationWorkingRuleStations'] = DB::table('station_working_rule_originals')
                    ->where('status', 1)
                    ->distinct()
                    ->count('station_id');

                $result['stationWorkingRuleSlips'] = DB::table('station_working_rule_corrections')
                    ->where('status', 1)
                    ->count();
                $result['totalMiscellaneousLetters'] = MiscellaneousLetters::where('status', 1)->count();

                $result['gsrOriginals'] = GsrOriginal::where('status', 1)->count();
                $result['gsrSlips']     = GsrCorrection::where('status', 1)->count();

                $result['operatingManualOriginals'] = OperatingManualOriginal::where('status', 1)->count();
                $result['operatingManualSlips']     = OperatingManualCorrection::where('status', 1)->count();

                $result['accidentManualOriginals'] = AccidentManualOriginal::where('status', 1)->count();
                $result['accidentManualSlips']     = AccidentManualCorrection::where('status', 1)->count();


                $result['operatingSafetyAlerts']   = OperatingSafetyAlert::where('status', 1)->count();
                $result['operatingSafetyEpisodes'] = OperatingSafetyEpisode::where('status', 1)->count();
                $result['operatingSafetyDrives']   = OperatingSafetyDrive::where('status', 1)->count();
                $result['totalCaseStudy'] = CaseStudy::where('status', 1)->count();

                $result['totalSafetyCircular'] = SafetyCircular::where('status', 1)->count();
                $result['totalOperatingSafetyCircular'] = OperatingSafetyCircular::where('status', 1)->count();
                $result['totalJpo'] = Jpo::where('status', 1)->count();
                $result['totalOfficerInspectionNotes'] = OfficerInspectionNote::where('status', 1)->count();
                $result['totalTrafficInspectorInspectionNotes'] = TrafficInspectorInspectionNote::where('status', 1)->count();


                return view('dashboard.super_admin', compact('result', 'birthdays_employees', 'appointments_employees', 'retirements_employees'));
            } elseif (Auth::user()->type == 'employee_tailor') {
                $result['totalCustomer'] = User::where('parent_id', parentId())->where('type', 'customer')->count();
                $result['totalMeasurement'] = Measurement::where('responsible', Auth::user()->id)->count();
                $result['totalOrder'] = Order::where('responsible', Auth::user()->id)->count();
                $result['totalTodayOrder'] = Order::where('responsible', Auth::user()->id)->where('order_date', today())->count();
                $result['totalOrderStatus'] = $this->totalOrderStatus();

                $result['notifyOrder'] = $this->getnotify();
                return view('dashboard.index', compact('result'));
            } elseif (Auth::user()->type == 'customer') {
                $result['totalMeasurement'] = Measurement::where('customer', Auth::user()->id)->count();
                $result['totalOrder'] = Order::where('customer_id', Auth::user()->id)->count();
                $result['totalPaidAmount'] = DB::table('invoices')
                    ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('taxes', 'invoice_items.tax', '=', 'taxes.id')
                    ->where('invoices.customer_id', Auth::user()->id)
                    ->where('invoices.status', 'paid')
                    ->selectRaw('
                            SUM(invoice_items.amount * invoice_items.quantity)
                            + SUM((invoice_items.amount * invoice_items.quantity) * taxes.rate / 100) as total
                        ')
                    ->value('total');

                $result['totalUnpaidAmount'] = DB::table('invoices')
                    ->join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('taxes', 'invoice_items.tax', '=', 'taxes.id')
                    ->where('invoices.customer_id', Auth::user()->id)
                    ->where('invoices.status', 'unpaid')
                    ->selectRaw('SUM(invoice_items.amount * invoice_items.quantity) + SUM((invoice_items.amount * invoice_items.quantity) * taxes.rate / 100) as total')
                    ->value('total');
                $result['notifyOrder'] = $this->getnotify();
                return view('dashboard.index', compact('result'));
            } else {

                // dd("testing");
                $user = Auth::user();

                // Base query filtered by department_id AND assigned_to (designation)
                $baseQuery = APIComplaint::where('department_id', $user->department_id)
                    ->where('assigned_to', $user->designation);

                $result['totalComplaints']    = (clone $baseQuery)->count();
                $result['pendingComplaints']  = (clone $baseQuery)->where('status', 0)->count();
                $result['resolvedComplaints'] = (clone $baseQuery)->where('status', 1)->count();
                $result['reopenedComplaints'] = (clone $baseQuery)->where('status', 2)->count();
                $result['closedComplaints']   = (clone $baseQuery)->where('status', 3)->count();
                $result['safetyComplaints']   = (clone $baseQuery)->where('is_safety_critical', 1)->count();

                $result['recentComplaints'] = (clone $baseQuery)
                    ->with(['department', 'station', 'amenityCategory'])
                    ->latest()
                    ->take(10)
                    ->get();

                // Today Complaints
                $result['todayComplaints'] = (clone $baseQuery)
                    ->whereDate('created_at', now()->toDateString())
                    ->count();

                // Yesterday Complaints
                $result['yesterdayComplaints'] = (clone $baseQuery)
                    ->whereDate('created_at', now()->subDay()->toDateString())
                    ->count();

                // Last Month Complaints
                $result['lastMonthComplaints'] = (clone $baseQuery)
                    ->whereYear('created_at', now()->subMonth()->year)
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->count();

                // By Station
                $result['byStation'] = (clone $baseQuery)
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

                // Top 3 reporters per station
                $result['topReportersByStation'] = (clone $baseQuery)
                    ->selectRaw("
            station_id,
            created_by,
            COUNT(*) AS report_count
        ")
                    ->whereIn('station_id', $result['byStation']->pluck('id'))
                    ->groupBy('station_id', 'created_by')
                    ->orderByDesc('report_count')
                    ->get()
                    ->groupBy('station_id')
                    ->map(fn($rows) => $rows->take(3));

                // Delay Complaints
                $result['delayComplaints'] = (clone $baseQuery)
                    ->whereDate('timeline', '<', now())
                    ->whereNotIn('status', [1, 3])
                    ->count();

                $result['organizationByMonth'] = $this->organizationByMonth($user->department_id, $user->designation);
                $result['paymentByMonth']      = $this->paymentByMonth($user->department_id, $user->designation);

                return view('deparment-dashboard.index', compact('result'));
            }
        } else {
            if (!file_exists(setup())) {
                header('location:install');
                die;
            } else {

               
$landingPage = getSettingsValByName('landing_page');

// Homepage data
$subscriptions = Subscription::get();
$menus = Page::where('enabled', 1)->get();
$FAQs = FAQ::where('enabled', 1)->get();

// Get the latest active popup within its schedule
$popup = \App\Models\Popup::where('status', 1)
    ->where(function ($query) {
        $query->whereNull('starts_at')
              ->orWhere('starts_at', '<=', now());
    })
    ->where(function ($query) {
        $query->whereNull('ends_at')
              ->orWhere('ends_at', '>=', now());
    })
    ->latest('id')
    ->first();

// Pass popup to landing page
return view('layouts.landing', compact(
    'subscriptions',
    'menus',
    'FAQs',
    'popup'
));

                // } else {
                //     return redirect()->route('login');
                // }
            }
        }
    }
    public function  chartDashboard()
    {

        if (Auth::user()->type == 'owner') {
            // dd('testing');
            $result['totalCustomer'] = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType'] = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome'] = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense'] = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings'] = settings();

            $result['totalComplaints']        = APIComplaint::count();
            $result['pendingComplaints']         = APIComplaint::where('status', 0)->count();
            $result['resolvedComplaints']     = APIComplaint::where('status', 1)->count();
            $result['reopenedComplaints']     = APIComplaint::where('status', 2)->count();
            $result['closedComplaints']     = APIComplaint::where('status', 3)->count();
            $result['safetyComplaints']       = APIComplaint::where('is_safety_critical', 1)->count();
            $result['recentComplaints'] = APIComplaint::with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();
            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw('departments.name as department_name,
                 COUNT(*) as total,
                 SUM(api_complaints.is_safety_critical) as critical')
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── 2. Daily Complaints – last 14 days (area chart) ───────────
            $result['dailyTrend'] = APIComplaint::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Today Complaints
            $result['todayComplaints'] = APIComplaint::whereDate('created_at', now()->toDateString())->count();

            // Yesterday Complaints
            $result['yesterdayComplaints'] = APIComplaint::whereDate('created_at', now()->subDay()->toDateString())->count();

            // Last Month Complaints
            $result['lastMonthComplaints'] = APIComplaint::whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
                            departments.id,
                            departments.name        AS department_name,
                            COUNT(*)                AS total,
                            SUM(api_complaints.status = 0)  AS pending,
                            SUM(api_complaints.status = 1)  AS rectified,
                            SUM(api_complaints.status = 2)  AS reopened,
                            SUM(api_complaints.status = 3)  AS closed
                        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();


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
                ->take(6)   // top 6 stations; remove for all
                ->get();

            // Top 3 reporters per station
            $result['topReportersByStation'] = APIComplaint::query()
                ->selectRaw("
                            station_id,
                            created_by,
                            COUNT(*) AS report_count
                        ")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // Delay Complaints (timeline < current date AND not closed)
            $result['delayComplaints'] = APIComplaint::whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3]) // not resolved or closed
                ->count();

            $result['organizationByMonth'] = [
                'label' => collect(range(5, 0))->map(
                    fn($i) =>
                    now()->subMonths($i)->format('M Y')
                )->values()->toArray(),

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->count()
                )->values()->toArray(),
            ];

            $result['paymentByMonth'] = [
                'label' => $result['organizationByMonth']['label'],

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->where('status', 1)   // Rectified = "resolved"
                        ->count()
                )->values()->toArray(),
            ];
            $result['organizationByMonth'] = $this->organizationByMonth();
            $result['paymentByMonth'] = $this->paymentByMonth();


            return view('dashboard.chart-dashboard', compact('result'));
        } else {
            $user = Auth::user();

            $deptId = $user->department_id ?: ($user->resolved_employee?->department_id ?? null);
            $desigName = $user->designation ?: ($user->resolved_employee?->designation?->name ?? null);

            // ── Base query scoped to logged-in Department Admin / Sr DOM ──────────────
            $base = APIComplaint::query();
            if ($deptId && !$user->isSrDom() && !in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin'])) {
                $base->where('department_id', $deptId);
            }
            if ($desigName && !$user->isSrDom() && !in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin'])) {
                $base->where('assigned_to', $desigName);
            }

            // ── Existing non-complaint stats (unchanged) ─────────────────────
            $result['totalCustomer']        = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType']       = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome']          = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense']         = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings']             = settings();

            // ── Complaint Counts ─────────────────────────────────────────────
            $result['totalComplaints']    = (clone $base)->count();
            $result['pendingComplaints']  = (clone $base)->where('status', 0)->count();
            $result['resolvedComplaints'] = (clone $base)->where('status', 1)->count();
            $result['reopenedComplaints'] = (clone $base)->where('status', 2)->count();
            $result['closedComplaints']   = (clone $base)->where('status', 3)->count();
            $result['safetyComplaints']   = (clone $base)->where('is_safety_critical', 1)->count();

            // ── Recent Complaints ────────────────────────────────────────────
            $result['recentComplaints'] = (clone $base)
                ->with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();

            // ── Daily Trend – last 14 days ───────────────────────────────────
            $result['dailyTrend'] = (clone $base)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // ── Today / Yesterday / Last Month ──────────────────────────────
            $result['todayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            $result['yesterdayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->count();

            $result['lastMonthComplaints'] = (clone $base)
                ->whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            // ── By Department (only their own department) ────────────────────
            $result['byDepartment'] = (clone $base)
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
            departments.id,
            departments.name                        AS department_name,
            COUNT(*)                                AS total,
            SUM(api_complaints.status = 0)          AS pending,
            SUM(api_complaints.status = 1)          AS rectified,
            SUM(api_complaints.status = 2)          AS reopened,
            SUM(api_complaints.status = 3)          AS closed
        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── By Station (top 6 within their scope) ───────────────────────
            $result['byStation'] = (clone $base)
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

            // ── Top 3 Reporters per Station ──────────────────────────────────
            $result['topReportersByStation'] = (clone $base)
                ->selectRaw("
            station_id,
            created_by,
            COUNT(*) AS report_count
        ")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // ── Delayed Complaints ───────────────────────────────────────────
            $result['delayComplaints'] = (clone $base)
                ->whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3])
                ->count();

            // ── Monthly Charts (pass user scope to private methods) ──────────
            $result['organizationByMonth'] = $this->organizationByMonth(
                $user->department_id,
                $user->designation
            );

            $result['paymentByMonth'] = $this->paymentByMonth(
                $user->department_id,
                $user->designation
            );

            return view('dashboard.chart-dashboard', compact('result'));
        }
    }
    public function  departmentDashboard()
    {
        if (Auth::user()->type == 'owner') {
            // dd('testing');
            $result['totalCustomer'] = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType'] = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome'] = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense'] = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings'] = settings();

            $result['totalComplaints']        = APIComplaint::count();
            $result['pendingComplaints']         = APIComplaint::where('status', 0)->count();
            $result['resolvedComplaints']     = APIComplaint::where('status', 1)->count();
            $result['reopenedComplaints']     = APIComplaint::where('status', 2)->count();
            $result['closedComplaints']     = APIComplaint::where('status', 3)->count();
            $result['safetyComplaints']       = APIComplaint::where('is_safety_critical', 1)->count();
            $result['recentComplaints'] = APIComplaint::with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();
            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw('departments.name as department_name,
                 COUNT(*) as total,
                 SUM(api_complaints.is_safety_critical) as critical')
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── 2. Daily Complaints – last 14 days (area chart) ───────────
            $result['dailyTrend'] = APIComplaint::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Today Complaints
            $result['todayComplaints'] = APIComplaint::whereDate('created_at', now()->toDateString())->count();

            // Yesterday Complaints
            $result['yesterdayComplaints'] = APIComplaint::whereDate('created_at', now()->subDay()->toDateString())->count();

            // Last Month Complaints
            $result['lastMonthComplaints'] = APIComplaint::whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
                            departments.id,
                            departments.name        AS department_name,
                            COUNT(*)                AS total,
                            SUM(api_complaints.status = 0)  AS pending,
                            SUM(api_complaints.status = 1)  AS rectified,
                            SUM(api_complaints.status = 2)  AS reopened,
                            SUM(api_complaints.status = 3)  AS closed
                        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();


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
                ->take(6)   // top 6 stations; remove for all
                ->get();

            // Top 3 reporters per station
            $result['topReportersByStation'] = APIComplaint::query()
                ->selectRaw("
                            station_id,
                            created_by,
                            COUNT(*) AS report_count
                        ")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // Delay Complaints (timeline < current date AND not closed)
            $result['delayComplaints'] = APIComplaint::whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3]) // not resolved or closed
                ->count();

            $result['organizationByMonth'] = [
                'label' => collect(range(5, 0))->map(
                    fn($i) =>
                    now()->subMonths($i)->format('M Y')
                )->values()->toArray(),

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->count()
                )->values()->toArray(),
            ];

            $result['paymentByMonth'] = [
                'label' => $result['organizationByMonth']['label'],

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->where('status', 1)   // Rectified = "resolved"
                        ->count()
                )->values()->toArray(),
            ];
            $result['organizationByMonth'] = $this->organizationByMonth();
            $result['paymentByMonth'] = $this->paymentByMonth();


            return view('dashboard.department-dashboard', compact('result'));
        } else {
            $user = Auth::user();

            $deptId = $user->department_id ?: ($user->resolved_employee?->department_id ?? null);
            $desigName = $user->designation ?: ($user->resolved_employee?->designation?->name ?? null);

            // ── Base query scoped to logged-in Department Admin / Sr DOM ──────────────
            $base = APIComplaint::query();
            if ($deptId && !$user->isSrDom() && !in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin'])) {
                $base->where('department_id', $deptId);
            }
            if ($desigName && !$user->isSrDom() && !in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin'])) {
                $base->where('assigned_to', $desigName);
            }

            // ── Existing non-complaint stats (unchanged) ─────────────────────
            $result['totalCustomer']        = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType']       = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome']          = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense']         = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings']             = settings();

            // ── Complaint Counts ─────────────────────────────────────────────
            $result['totalComplaints']    = (clone $base)->count();
            $result['pendingComplaints']  = (clone $base)->where('status', 0)->count();
            $result['resolvedComplaints'] = (clone $base)->where('status', 1)->count();
            $result['reopenedComplaints'] = (clone $base)->where('status', 2)->count();
            $result['closedComplaints']   = (clone $base)->where('status', 3)->count();
            $result['safetyComplaints']   = (clone $base)->where('is_safety_critical', 1)->count();

            // ── Recent Complaints ────────────────────────────────────────────
            $result['recentComplaints'] = (clone $base)
                ->with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();

            // ── Daily Trend – last 14 days ───────────────────────────────────
            $result['dailyTrend'] = (clone $base)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // ── Today / Yesterday / Last Month ──────────────────────────────
            $result['todayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            $result['yesterdayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->count();

            $result['lastMonthComplaints'] = (clone $base)
                ->whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            // ── By Department (only their own) ───────────────────────────────
            $result['byDepartment'] = (clone $base)
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
            departments.id,
            departments.name                        AS department_name,
            COUNT(*)                                AS total,
            SUM(api_complaints.status = 0)          AS pending,
            SUM(api_complaints.status = 1)          AS rectified,
            SUM(api_complaints.status = 2)          AS reopened,
            SUM(api_complaints.status = 3)          AS closed
        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── By Station (top 6) ───────────────────────────────────────────
            $result['byStation'] = (clone $base)
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

            // ── Top 3 Reporters per Station ──────────────────────────────────
            $result['topReportersByStation'] = (clone $base)
                ->selectRaw("station_id, created_by, COUNT(*) AS report_count")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // ── Delayed Complaints ───────────────────────────────────────────
            $result['delayComplaints'] = (clone $base)
                ->whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3])
                ->count();

            // ── Monthly Charts ───────────────────────────────────────────────
            $result['organizationByMonth'] = $this->organizationByMonth($user->department_id, $user->designation);
            $result['paymentByMonth']      = $this->paymentByMonth($user->department_id, $user->designation);

            return view('dashboard.department-dashboard', compact('result'));
        }
    }

    public function  departmentDashboard_data_view($department_id, $status)
    {

        if ($status == 'pending') {
            $status_code = 0;
            $status_view = 'Pending';
        } elseif ($status == 'rectified') {
            $status_code = 1;
            $status_view = 'Resolved';
        } elseif ($status == 'reopened') {
            $status_code = 2;
            $status_view = 'Re-raised';
        } elseif ($status == 'closed') {
            $status_code = 3;
            $status_view = 'Closed';
        } else {
            $status_code = null;
            $status_view = 'All';
        }

        $department_data = Department::find($department_id);
        $department_name = $department_data ? $department_data->name : '';



        $data = APIComplaint::leftJoin('stations', 'api_complaints.station_id', '=', 'stations.id')
            ->leftJoin('departments', 'api_complaints.department_id', '=', 'departments.id')
            ->leftJoin('amenity_categories', 'api_complaints.amenity_category_id', '=', 'amenity_categories.id')
            ->leftJoin('designations', 'api_complaints.assigned_to', '=', 'designations.id')
            ->select('api_complaints.*', 'stations.name as station_name', 'departments.name as department_name', 'amenity_categories.name as amenity_category_name', 'designations.name as assigned_to_name')
            ->where('api_complaints.department_id', $department_id)
            ->where('api_complaints.status', $status_code)
            ->latest()
            ->get();

        // dd($status, $department_id, $data);

        return view('dashboard.department-dashboard-data-view', compact('data', 'status_view', 'department_name'));
    }

    public function  stationDashboard()
    {

        if (Auth::user()->type == 'owner') {
            // dd('testing');
            $result['totalCustomer'] = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType'] = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome'] = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense'] = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings'] = settings();

            $result['totalComplaints']        = APIComplaint::count();
            $result['pendingComplaints']         = APIComplaint::where('status', 0)->count();
            $result['resolvedComplaints']     = APIComplaint::where('status', 1)->count();
            $result['reopenedComplaints']     = APIComplaint::where('status', 2)->count();
            $result['closedComplaints']     = APIComplaint::where('status', 3)->count();
            $result['safetyComplaints']       = APIComplaint::where('is_safety_critical', 1)->count();
            $result['recentComplaints'] = APIComplaint::with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();
            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw('departments.name as department_name,
                 COUNT(*) as total,
                 SUM(api_complaints.is_safety_critical) as critical')
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── 2. Daily Complaints – last 14 days (area chart) ───────────
            $result['dailyTrend'] = APIComplaint::query()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Today Complaints
            $result['todayComplaints'] = APIComplaint::whereDate('created_at', now()->toDateString())->count();

            // Yesterday Complaints
            $result['yesterdayComplaints'] = APIComplaint::whereDate('created_at', now()->subDay()->toDateString())->count();

            // Last Month Complaints
            $result['lastMonthComplaints'] = APIComplaint::whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            $result['byDepartment'] = APIComplaint::query()
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
                            departments.id,
                            departments.name        AS department_name,
                            COUNT(*)                AS total,
                            SUM(api_complaints.status = 0)  AS pending,
                            SUM(api_complaints.status = 1)  AS rectified,
                            SUM(api_complaints.status = 2)  AS reopened,
                            SUM(api_complaints.status = 3)  AS closed
                        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();


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
                ->take(6)   // top 6 stations; remove for all
                ->get();

            // Top 3 reporters per station
            $result['topReportersByStation'] = APIComplaint::query()
                ->selectRaw("
                            station_id,
                            created_by,
                            COUNT(*) AS report_count
                        ")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // Delay Complaints (timeline < current date AND not closed)
            $result['delayComplaints'] = APIComplaint::whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3]) // not resolved or closed
                ->count();

            $result['organizationByMonth'] = [
                'label' => collect(range(5, 0))->map(
                    fn($i) =>
                    now()->subMonths($i)->format('M Y')
                )->values()->toArray(),

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->count()
                )->values()->toArray(),
            ];

            $result['paymentByMonth'] = [
                'label' => $result['organizationByMonth']['label'],

                'data' => collect(range(5, 0))->map(
                    fn($i) =>
                    APIComplaint::whereYear('created_at', now()->subMonths($i)->year)
                        ->whereMonth('created_at', now()->subMonths($i)->month)
                        ->where('status', 1)   // Rectified = "resolved"
                        ->count()
                )->values()->toArray(),
            ];
            $result['organizationByMonth'] = $this->organizationByMonth();
            $result['paymentByMonth'] = $this->paymentByMonth();


            return view('dashboard.complaint-dashboard', compact('result'));
        } else {
            $user = Auth::user();

            // ── For Station Admin: filter by station_id instead ─────────────
            // If station admin has station_id on user model, use that.
            // Otherwise fall back to department_id + designation like above.
            $base = APIComplaint::where('station_id', $user->station_id);

            // ── Existing non-complaint stats (unchanged) ─────────────────────
            $result['totalCustomer']        = User::where('parent_id', parentId())->where('type', 'customer')->count();
            $result['totalClothType']       = ClothType::where('parent_id', parentId())->count();
            $result['totalIncome']          = InvoicePayment::where('parent_id', parentId())->sum('amount');
            $result['totalExpense']         = Expense::where('parent_id', parentId())->sum('amount');
            $result['incomeExpenseByMonth'] = $this->incomeByMonth();
            $result['settings']             = settings();

            // ── Complaint Counts ─────────────────────────────────────────────
            $result['totalComplaints']    = (clone $base)->count();
            $result['pendingComplaints']  = (clone $base)->where('status', 0)->count();
            $result['resolvedComplaints'] = (clone $base)->where('status', 1)->count();
            $result['reopenedComplaints'] = (clone $base)->where('status', 2)->count();
            $result['closedComplaints']   = (clone $base)->where('status', 3)->count();
            $result['safetyComplaints']   = (clone $base)->where('is_safety_critical', 1)->count();

            // ── Recent Complaints ────────────────────────────────────────────
            $result['recentComplaints'] = (clone $base)
                ->with(['department', 'station', 'amenityCategory'])
                ->latest()
                ->take(10)
                ->get();

            // ── Daily Trend – last 14 days ───────────────────────────────────
            $result['dailyTrend'] = (clone $base)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(13)->startOfDay())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // ── Today / Yesterday / Last Month ──────────────────────────────
            $result['todayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            $result['yesterdayComplaints'] = (clone $base)
                ->whereDate('created_at', now()->subDay()->toDateString())
                ->count();

            $result['lastMonthComplaints'] = (clone $base)
                ->whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count();

            // ── By Department (within their station) ─────────────────────────
            $result['byDepartment'] = (clone $base)
                ->join('departments', 'api_complaints.department_id', '=', 'departments.id')
                ->selectRaw("
            departments.id,
            departments.name                        AS department_name,
            COUNT(*)                                AS total,
            SUM(api_complaints.status = 0)          AS pending,
            SUM(api_complaints.status = 1)          AS rectified,
            SUM(api_complaints.status = 2)          AS reopened,
            SUM(api_complaints.status = 3)          AS closed
        ")
                ->groupBy('departments.id', 'departments.name')
                ->orderByDesc('total')
                ->get();

            // ── By Station (only their own station) ──────────────────────────
            $result['byStation'] = (clone $base)
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

            // ── Top 3 Reporters per Station ──────────────────────────────────
            $result['topReportersByStation'] = (clone $base)
                ->selectRaw("station_id, created_by, COUNT(*) AS report_count")
                ->whereIn('station_id', $result['byStation']->pluck('id'))
                ->groupBy('station_id', 'created_by')
                ->orderByDesc('report_count')
                ->get()
                ->groupBy('station_id')
                ->map(fn($rows) => $rows->take(3));

            // ── Delayed Complaints ───────────────────────────────────────────
            $result['delayComplaints'] = (clone $base)
                ->whereDate('timeline', '<', now())
                ->whereNotIn('status', [1, 3])
                ->count();

            // ── Monthly Charts ───────────────────────────────────────────────
            $result['organizationByMonth'] = $this->organizationByMonth(null, null, $user->station_id);
            $result['paymentByMonth']      = $this->paymentByMonth(null, null, $user->station_id);

            return view('dashboard.complaint-dashboard', compact('result'));
        }
    }

    public function  stationDashboard_data_view($station_id, $status)
    {

        if ($status == 'pending') {
            $status_code = 0;
            $status_view = 'Pending';
        } elseif ($status == 'rectified') {
            $status_code = 1;
            $status_view = 'Rectified';
        } elseif ($status == 'reopened') {
            $status_code = 2;
            $status_view = 'Re-raised';
        } elseif ($status == 'closed') {
            $status_code = 3;
            $status_view = 'Closed';
        } else {
            $status_code = null;
            $status_view = 'All';
        }

        $station_data = Station::find($station_id);
        $station_name = $station_data ? $station_data->name : '';
        $station_code = $station_data ? $station_data->code : '';



        $data = APIComplaint::leftJoin('stations', 'api_complaints.station_id', '=', 'stations.id')
            ->leftJoin('departments', 'api_complaints.department_id', '=', 'departments.id')
            ->leftJoin('amenity_categories', 'api_complaints.amenity_category_id', '=', 'amenity_categories.id')
            ->leftJoin('designations', 'api_complaints.assigned_to', '=', 'designations.id')
            ->select('api_complaints.*', 'stations.name as station_name', 'departments.name as department_name', 'amenity_categories.name as amenity_category_name', 'designations.name as assigned_to_name')
            ->where('api_complaints.station_id', $station_id);

        if ($status == 'all') {

            $data = $data->latest()->get();
        } else {
            $data = $data->where('api_complaints.status', $status_code)->latest()->get();
        }

        // dd($status, $station_id, $data);

        return view('dashboard.station-dashboard-data-view', compact('data', 'status_view', 'station_name', 'station_code'));
    }



    public function calendarDashboard()
    {
        if (Auth::user()->type == 'owner') {

            $complaints = APIComplaint::all();

            $eventData = [];

            $colors = [
                '#FF5733',
                '#33FF57',
                '#3357FF',
                '#FF33A8',
                '#A833FF',
                '#33FFF3',
                '#FF8F33',
                '#8FFF33',
                '#FF3333',
                '#33FF8F',
                '#8F33FF',
                '#FF3380',
                '#33A8FF',
                '#FFD433',
                '#33FFBD',
                '#FF6F61',
                '#6B5B95',
                '#88B04B',
                '#F7CAC9',
                '#92A8D1'
            ];

            // Group by date
            $grouped = $complaints->groupBy(function ($item) {
                return date('Y-m-d', strtotime($item->created_at));
            });

            foreach ($grouped as $date => $items) {

                $count = count($items);

                // 🎯 Random color
                $color = $colors[array_rand($colors)];

                $eventData[] = [
                    'title' => $count . ' Complaints',
                    'start' => $date,
                    'color' => $color,
                    'textColor' => '#fff',
                ];
            }

            return view('dashboard.dashboard-by-calendar', compact('eventData'));
        } else {
            $user = Auth::user();

            // ── Scope calendar to logged-in user's data ──────────────────────
            $complaints = APIComplaint::where('department_id', $user->department_id)
                ->where('assigned_to', $user->designation)
                ->get();

            $eventData = [];

            $colors = [
                '#FF5733',
                '#33FF57',
                '#3357FF',
                '#FF33A8',
                '#A833FF',
                '#33FFF3',
                '#FF8F33',
                '#8FFF33',
                '#FF3333',
                '#33FF8F',
                '#8F33FF',
                '#FF3380',
                '#33A8FF',
                '#FFD433',
                '#33FFBD',
                '#FF6F61',
                '#6B5B95',
                '#88B04B',
                '#F7CAC9',
                '#92A8D1',
            ];

            // ── Group by date ────────────────────────────────────────────────
            $grouped = $complaints->groupBy(fn($item) => date('Y-m-d', strtotime($item->created_at)));

            foreach ($grouped as $date => $items) {
                $eventData[] = [
                    'title'     => count($items) . ' Complaints',
                    'start'     => $date,
                    'color'     => $colors[array_rand($colors)],
                    'textColor' => '#fff',
                ];
            }

            return view('dashboard.dashboard-by-calendar', compact('eventData'));
        }
    }

    public function allComplaints()
    {

        $user = Auth::user();

        // Base query filtered by department_id AND assigned_to (designation)
        $baseQuery = APIComplaint::where('department_id', $user->department_id)
            ->where('assigned_to', $user->designation);

        $result['totalComplaints']    = (clone $baseQuery)->count();
        $result['pendingComplaints']  = (clone $baseQuery)->where('status', 0)->count();
        $result['resolvedComplaints'] = (clone $baseQuery)->where('status', 1)->count();
        $result['reopenedComplaints'] = (clone $baseQuery)->where('status', 2)->count();
        $result['closedComplaints']   = (clone $baseQuery)->where('status', 3)->count();
        $result['safetyComplaints']   = (clone $baseQuery)->where('is_safety_critical', 1)->count();

        $result['recentComplaints'] = (clone $baseQuery)
            ->with(['department', 'station', 'amenityCategory'])
            ->latest()
            ->get();

        // Today Complaints
        $result['todayComplaints'] = (clone $baseQuery)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Yesterday Complaints
        $result['yesterdayComplaints'] = (clone $baseQuery)
            ->whereDate('created_at', now()->subDay()->toDateString())
            ->count();

        // Last Month Complaints
        $result['lastMonthComplaints'] = (clone $baseQuery)
            ->whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->count();

        // By Station
        $result['byStation'] = (clone $baseQuery)
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

        // Top 3 reporters per station
        $result['topReportersByStation'] = (clone $baseQuery)
            ->selectRaw("
            station_id,
            created_by,
            COUNT(*) AS report_count
        ")
            ->whereIn('station_id', $result['byStation']->pluck('id'))
            ->groupBy('station_id', 'created_by')
            ->orderByDesc('report_count')
            ->get()
            ->groupBy('station_id')
            ->map(fn($rows) => $rows->take(3));

        // Delay Complaints
        $result['delayComplaints'] = (clone $baseQuery)
            ->whereDate('timeline', '<', now())
            ->whereNotIn('status', [1, 3])
            ->count();

        $result['organizationByMonth'] = $this->organizationByMonth($user->department_id, $user->designation);
        $result['paymentByMonth']      = $this->paymentByMonth($user->department_id, $user->designation);

        return view('deparment-dashboard.all-complaint', compact('result'));
    }

    public function transferPage($id)
    {
        $complaint = APIComplaint::with(['department', 'station', 'amenityCategory', 'assignedEmployee'])
            ->findOrFail($id);

        // If already transferred, block access
        if ($complaint->status == 6) {
            return redirect()->route('complaints.view', $id)
                ->with('error', 'This complaint has already been transferred.');
        }

        $departments = Department::where('status', 1)->get();

        return view('complaints.transfer', compact('complaint', 'departments'));
    }

    public function transfer(Request $request, $id)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'assigned_to'   => 'required|exists:designations,id',
        ], [
            'department_id.required' => 'Department is required.',
            'assigned_to.required'   => 'Designation is required.',
        ]);

        $complaint = APIComplaint::findOrFail($id);

        // Block double transfer
        if ($complaint->status == 6) {
            return redirect()->route('complaints.view', $id)
                ->with('error', 'This complaint has already been transferred.');
        }

        // Validate designation belongs to department via Rectify
        $isValid = Rectify::where('department_id', $request->department_id)
            ->where('designation_id', $request->assigned_to)
            ->exists();

        if (!$isValid) {
            return back()->withErrors(['assigned_to' => 'Invalid designation for the selected department.'])->withInput();
        }

        $oldDepartment = $complaint->department_id;
        $oldAssignedTo = $complaint->assigned_to;
        $oldStatus     = $complaint->status;

        $complaint->update([
            'department_id' => $request->department_id,
            'assigned_to'   => $request->assigned_to,
            'status'        => 6,
            'updated_by'    => auth()->id(),
        ]);

        RectificationHistory::create([
            'complaint_id'       => $complaint->id,
            'action'             => 'transferred',
            'old_status'         => $oldStatus,
            'new_status'         => 6,
            'old_department_id'  => $oldDepartment,
            'new_department_id'  => $request->department_id,
            'old_assigned_to'    => $oldAssignedTo,
            'new_assigned_to'    => $request->assigned_to,
            'action_by'          => auth()->id(),
        ]);

        return redirect()->route('all-complaints')
            ->with('success', 'Complaint transferred successfully.');
    }

    // ── 1. View Full Complaint ────────────────────────────────────────────────────
    public function viewComplaint($id)
    {
        $user = Auth::user();

        $complaint = APIComplaint::where('id', $id)
            ->where('department_id', $user->department_id)   // scoped to department
            ->where('assigned_to', $user->designation)        // scoped to designation
            ->with(['department', 'station', 'amenityCategory', 'assignedEmployee.designation'])
            ->firstOrFail();

        return view('deparment-dashboard.complaint-view', compact('complaint'));
    }

    // ── 2. Show Assign Page ───────────────────────────────────────────────────────
    public function assignComplaint($id)
    {
        $user = Auth::user();

        // Fetch complaint scoped to logged-in user's department & designation
        $complaint = APIComplaint::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('assigned_to', $user->designation)
            ->with(['department', 'station', 'amenityCategory', 'assignedEmployee'])
            ->firstOrFail();

        // Fetch only employees from same department
        $employees = MasterEmployee::where('department_id', $user->department_id)
            ->where('status', 1)                             // active employees only
            ->with('designation')
            ->orderBy('name')
            ->get();

        return view('deparment-dashboard.complaint-assign', compact('complaint', 'employees'));
    }

    // ── 3. Store Assignment ───────────────────────────────────────────────────────
    public function storeAssignComplaint(Request $request, $id)
    {

        // dd($request);
        $user = Auth::user();

        $request->validate([
            'employee_id' => 'required|exists:master_employees,id',
        ]);

        // Verify the chosen employee belongs to the same department (extra safety)
        $employee = MasterEmployee::where('id', $request->employee_id)
            ->where('department_id', $user->department_id)
            ->where('status', 1)
            ->firstOrFail();

        // dd($employee);

        // Fetch complaint scoped to logged-in user's department & designation
        $complaint = APIComplaint::where('id', $id)
            ->where('department_id', $user->department_id)
            ->where('assigned_to', $user->designation)
            ->firstOrFail();

        // dd($complaint);
        $oldStatus = $complaint->status;

        // ✅ Assign complaint
        $complaint->assigned_employee_id = $employee->id;
        $complaint->assigned_employee_at = now();

        // (Optional) update status if you use status flow
        $complaint->status = 4; // example: assigned

        $complaint->save();

        // 🔥 SAVE HISTORY
        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'assigned',
            'old_status' => $oldStatus,
            'new_status' => $complaint->status,

            'rectifier_name' => $employee->name,
            'rectifier_designation' => optional($employee->designation)->name,

            'rectification_date' => now(),

            'action_by' => $user->id,
        ]);


        return redirect()
            ->route('complaints.assign', $complaint->id)
            ->with('success', __('Complaint successfully assigned to ') . $employee->name . '.');
    }

    public function organizationByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $organization = [];
        while ($currentdate <= $end) {
            $organization['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $organization['data'][] = User::where('type', 'owner')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $currentdate = strtotime('+1 month', $currentdate);
        }


        return $organization;
    }

    public function paymentByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $payment = [];
        while ($currentdate <= $end) {
            $payment['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $payment['data'][] = PackageTransaction::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount');
            $currentdate = strtotime('+1 month', $currentdate);
        }

        return $payment;
    }

    public function incomeByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $payment = [];
        while ($currentdate <= $end) {
            $payment['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $payment['income'][] = InvoicePayment::where('parent_id', parentId())->whereMonth('payment_date', $month)->whereYear('payment_date', $year)->sum('amount');
            $payment['expense'][] = Expense::where('parent_id', parentId())->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
            $currentdate = strtotime('+1 month', $currentdate);
        }

        return $payment;
    }

    public function totalOrderStatus()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $currentDate = $start->copy();

        $orderData = [
            'label' => [],
            'completed' => [],
            'pending' => [],
        ];

        while ($currentDate <= $end) {
            $orderData['label'][] = $currentDate->format('d-M');

            $completed = Order::where('responsible', Auth::user()->id)
                ->where('status', 'completed')
                ->whereDate('order_date', $currentDate->toDateString())
                ->count();

            $pending = Order::where('responsible', Auth::user()->id)
                ->where('status', 'pending')
                ->whereDate('order_date', $currentDate->toDateString())
                ->count();

            $orderData['completed'][] = $completed;
            $orderData['pending'][] = $pending;

            $currentDate->addDay();
        }

        return $orderData;
    }

    public function getnotify()
    {
        if (Auth::user()->type == 'customer') {
            $orders = Order::where('customer_id', Auth::user()->id)
                ->where('status', '!=', 'delivered')
                ->get();
            return $orders;
        } elseif (Auth::user()->type == 'employee') {
            $today = now()->startOfDay();
            $nextWeek = now()->addDays(7)->endOfDay(); // get next 7 days

            $orders = Order::where('responsible', Auth::user()->id)
                ->whereBetween('deadline_date', [$today, $nextWeek])
                ->get();
            return $orders;
        }
    }
}
