<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Invoice;
use App\Models\MasterEmployee;
use App\Models\Order;
use App\Models\User;
use App\Models\EmployeePME;
use App\Models\EmployeeRefresherCourse;
use App\Models\EmployeeGradation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function orderData()
    {
        if (Auth::user()->can('manage order report')) {
            $responsibles = User::whereNotIn('type', ['customer', 'owner', 'super admin'])->where('parent_id', parentId())->pluck('name', 'id');
            $customers = User::where('type', 'customer')->where('parent_id', parentId())->pluck('name', 'id');
            $orders = Order::where('parent_id', parentId())->get();

            return view('report.order', compact('responsibles', 'customers', 'orders'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function generateOrderReport(Request $request)
    {
        if (Auth::user()->can('manage order report')) {

            $query = Order::where('parent_id', parentId());

            if ($request->filled('responsible')) {
                $query->where('responsible', $request->responsible);
            }

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);

                if (count($dates) === 2) {

                    $order_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                    $deadline_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                    $query->whereDate('order_date', '>=', $order_date)
                        ->whereDate('deadline_date', '<=', $deadline_date);
                }
            }

            $orders = $query->orderBy('id', 'desc')->get();
            $html = view('report.order_table', compact('orders'))->render();

            return response()->json(['html' => $html]);
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function incomeData()
    {
        if (Auth::user()->can('manage income report')) {
            $incomes = Invoice::where('parent_id', parentId())->whereIn('status', ['paid', 'partial_paid'])->get();
            $customers = User::where('type', 'customer')->where('parent_id', parentId())->pluck('name', 'id');
            return view('report.income', compact('incomes', 'customers'));
        } else {
            return redirect()->back()->with('error', 'Permission deneid');
        }
    }

    public function generateIncomeReport(Request $request)
    {
        if (Auth::user()->can('manage income report')) {
            $query = Invoice::where('parent_id', parentId())->whereIn('status', ['partial_paid', 'paid']);

            if ($request->filled('customer_id')) {
                $query->where('customer_id', $request->customer_id);
            }

            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);

                if (count($dates) === 2) {

                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                    $query->whereDate('invoice_date', '>=', $start_date)
                        ->whereDate('invoice_date', '<=', $end_date);
                }
            }

            $incomes = $query->orderBy('id', 'desc')->get();
            $totalAmount = $incomes->sum(function ($invoice) {
                return $invoice->getInvoiceTotalAmount();
            });
            $formattedTotal = priceFormat($totalAmount);
            $html = view('report.income_table', compact('incomes'))->render();

            return response()->json(['html' => $html, 'total_amount' => $formattedTotal]);
        } else {
            return redirect()->back()->with('error', 'Permission deneid');
        }
    }
    public function expenseData()
    {
        if (Auth::user()->can('manage expense report')) {
            $expenses = Expense::where('parent_id', parentId())->get();
            $categories = ExpenseCategory::where('parent_id', parentId())->pluck('name', 'id');
            $subCategories = ExpenseSubCategory::where('parent_id', parentId())->pluck('name', 'id');

            return view('report.expense', compact('expenses', 'categories', 'subCategories'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function generateExpenseReport(Request $request)
    {
        if (Auth::user()->can('manage expense report')) {
            $query = Expense::where('parent_id', parentId());

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('sub_category_id')) {
                $query->where('sub_category_id', $request->sub_category_id);
            }

            if ($request->filled('date_range')) {
                $dates = explode(' - ', $request->date_range);

                if (count($dates) === 2) {

                    $start_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                    $end_date = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                    $query->whereDate('date', '>=', $start_date)
                        ->whereDate('date', '<=', $end_date);
                }
            }

            $expenses = $query->orderBy('id', 'desc')->get();
            $totalAmount = $expenses->sum('amount');
            $formattedTotal = priceFormat($totalAmount);
            $html = view('report.expense_table', compact('expenses'))->render();

            return response()->json(['html' => $html, 'total_amount' => $formattedTotal]);
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function yearlyProfitLoss()
    {
        if (Auth::user()->can('manage profit loss report')) {
            return view('report.yearly_profit_loss');
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function generateProfitLossReport(Request $request)
    {
        if (Auth::user()->can('manage profit loss report')) {
            $year = $request->input('year', now()->year);

            $expenses = Expense::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('SUM(amount) as total_expense')
            )
                ->whereYear('date', $year)
                ->groupBy(DB::raw('MONTH(date)'))
                ->orderBy('month')
                ->get()
                ->keyBy('month');

            $invoiceGroups = Invoice::whereYear('invoice_date', $year)
                ->whereIn('status', ['paid', 'partial_paid'])
                ->get()
                ->groupBy(function ($invoice) {
                    return (int) \Carbon\Carbon::parse($invoice->invoice_date)->format('m');
                });

            $incomes = collect();

            foreach ($invoiceGroups as $month => $monthInvoices) {
                $incomes[$month] = (object) [
                    'month' => $month,
                    'total_income' => $monthInvoices->sum(function ($invoice) {
                        return $invoice->getInvoiceTotalAmount();
                    }),
                ];
            }

            $report = [];
            $total_income = 0;
            $total_expense = 0;

            for ($month = 1; $month <= 12; $month++) {
                $income = $incomes[$month]->total_income ?? 0;
                $expense = $expenses[$month]->total_expense ?? 0;
                $profit = $income - $expense;

                $report[] = [
                    'month' => date('F', mktime(0, 0, 0, $month, 10)),
                    'income' => $income,
                    'expense' => $expense,
                    'profit' => $profit,
                ];

                $total_income += $income;
                $total_expense += $expense;
            }

            $settings = settings();
            $currency_symbol = $settings['CURRENCY_SYMBOL'] ?? '$';

            $html = view('report.yearly_summary_table', compact('report'))->render();

            return response()->json([
                'html' => $html,
                'report' => $report,
                'total_income' => priceFormat(number_format($total_income, 2)),
                'total_expense' => priceFormat(number_format($total_expense, 2)),
                'total_profit' => priceFormat(number_format($total_income - $total_expense, 2)),
                'currency_symbol' => $currency_symbol,
            ]);
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }


    public function master_employee_report(Request $request)
    {
        if($request->isMethod('post')){

        // dd($request->all());

            if (Auth::user()->type == 'owner') {


                $data = MasterEmployee::with(['department', 'designation', 'stations']);

                if($request->search_type == 'date_of_birth'){

                    $data->whereBetween('date_of_birth', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                } elseif($request->search_type == 'date_of_appointment') {

                    $data->whereBetween('date_of_appointment', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                } elseif($request->search_type == 'date_of_retirement') {

                    $data->whereBetween('date_of_retirement', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                }

                $data = $data->latest()->get();

            } else {

                $user = Auth::user();

                $data = MasterEmployee::with(['department', 'designation', 'stations'])->where('department_id', $user->department_id);

                if($request->search_type == 'date_of_birth'){

                    $data->whereBetween('date_of_birth', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                } elseif($request->search_type == 'date_of_appointment') {

                    $data->whereBetween('date_of_appointment', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                } elseif($request->search_type == 'date_of_retirement') {

                    $data->whereBetween('date_of_retirement', [
                                            $request->from_date,
                                            $request->to_date
                                        ]);

                }

                $data = $data->latest()->get();

            }
        } else {
            $data = [];
        }

        return view('report.employee_report', compact('data'));
    }

    public function employee_safety_record_list(Request $request)
    {
        return app(\App\Http\Controllers\Admin\Report\EmployeeSafetyRecordReportController::class)->index($request);
    }

    public function employee_gradation($id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $employee = MasterEmployee::findOrFail($real_id);
        $history = EmployeeGradation::where('master_employee_id', $real_id)
            ->orderBy('gradation_date', 'desc')
            ->latest()
            ->get();

        return view('report.employee_safety.gradation', compact('employee', 'history'));
    }

    public function employee_gradation_store(Request $request, $id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $request->validate([
            'gradation_date' => 'required|date',
            'total_marks'    => 'required|numeric|min:0|max:100',
            'grade'          => 'required|string|max:50',
            'remarks'        => 'nullable|string',
        ]);

        EmployeeGradation::create([
            'master_employee_id' => $real_id,
            'gradation_date'     => $request->gradation_date,
            'total_marks'        => $request->total_marks,
            'grade'              => $request->grade,
            'remarks'            => $request->remarks,
            'status'             => 1,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return redirect()->back()->with('success', __('Gradation record saved successfully.'));
    }

    public function employee_pme($id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $employee = MasterEmployee::findOrFail($real_id);
        $history = EmployeePME::where('master_employee_id', $real_id)
            ->orderBy('last_pme_date', 'desc')
            ->latest()
            ->get();

        return view('report.employee_safety.pme', compact('employee', 'history'));
    }

    public function employee_pme_store(Request $request, $id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $request->validate([
            'last_pme_date'  => 'required|date',
            'certificate_no' => 'required|string|max:100',
            'next_due_date'  => 'required|date',
            'remarks'        => 'nullable|string',
        ]);

        EmployeePME::create([
            'master_employee_id' => $real_id,
            'last_pme_date'      => $request->last_pme_date,
            'certificate_no'     => $request->certificate_no,
            'next_due_date'      => $request->next_due_date,
            'remarks'            => $request->remarks,
            'status'             => 1,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return redirect()->back()->with('success', __('PME record saved successfully.'));
    }

    public function employee_refresher($id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $employee = MasterEmployee::findOrFail($real_id);
        $history = EmployeeRefresherCourse::where('master_employee_id', $real_id)
            ->orderBy('last_refresher_date', 'desc')
            ->latest()
            ->get();

        return view('report.employee_safety.refresher', compact('employee', 'history'));
    }

    public function employee_refresher_store(Request $request, $id)
    {
        try {
            $real_id = decrypt($id);
        } catch (\Exception $e) {
            $real_id = $id;
        }

        $request->validate([
            'last_refresher_date' => 'required|date',
            'certificate_no'      => 'required|string|max:100',
            'institution_name'    => 'required|string|max:150',
            'next_due_date'       => 'required|date',
            'remarks'             => 'nullable|string',
        ]);

        EmployeeRefresherCourse::create([
            'master_employee_id'  => $real_id,
            'last_refresher_date' => $request->last_refresher_date,
            'certificate_no'      => $request->certificate_no,
            'institution_name'    => $request->institution_name,
            'next_due_date'       => $request->next_due_date,
            'remarks'             => $request->remarks,
            'status'              => 1,
            'created_by'          => auth()->id(),
            'updated_by'          => auth()->id(),
        ]);

        return redirect()->back()->with('success', __('Refresher Course record saved successfully.'));
    }

    // Gradation Edit/Update/Delete
    public function employee_gradation_edit($id)
    {
        $record = EmployeeGradation::findOrFail(decrypt($id));
        return response()->json($record);
    }

    public function employee_gradation_update(Request $request, $id)
    {
        $record = EmployeeGradation::findOrFail(decrypt($id));
        $request->validate([
            'gradation_date' => 'required|date',
            'total_marks'    => 'required|numeric|min:0|max:100',
            'grade'          => 'required|string|max:50',
            'remarks'        => 'nullable|string',
        ]);
        $record->update([
            'gradation_date' => $request->gradation_date,
            'total_marks'    => $request->total_marks,
            'grade'          => $request->grade,
            'remarks'        => $request->remarks,
            'updated_by'     => auth()->id(),
        ]);
        return redirect()->back()->with('success', __('Gradation record updated successfully.'));
    }

    public function employee_gradation_delete($id)
    {
        $record = EmployeeGradation::findOrFail(decrypt($id));
        $record->delete();
        return redirect()->back()->with('success', __('Gradation record deleted successfully.'));
    }

    // PME Edit/Update/Delete
    public function employee_pme_edit($id)
    {
        $record = EmployeePME::findOrFail(decrypt($id));
        return response()->json($record);
    }

    public function employee_pme_update(Request $request, $id)
    {
        $record = EmployeePME::findOrFail(decrypt($id));
        $request->validate([
            'last_pme_date'  => 'required|date',
            'certificate_no' => 'required|string|max:100',
            'next_due_date'  => 'required|date',
            'remarks'        => 'nullable|string',
        ]);
        $record->update([
            'last_pme_date'  => $request->last_pme_date,
            'certificate_no' => $request->certificate_no,
            'next_due_date'  => $request->next_due_date,
            'remarks'        => $request->remarks,
            'updated_by'     => auth()->id(),
        ]);
        return redirect()->back()->with('success', __('PME record updated successfully.'));
    }

    public function employee_pme_delete($id)
    {
        $record = EmployeePME::findOrFail(decrypt($id));
        $record->delete();
        return redirect()->back()->with('success', __('PME record deleted successfully.'));
    }

    // Refresher Edit/Update/Delete
    public function employee_refresher_edit($id)
    {
        $record = EmployeeRefresherCourse::findOrFail(decrypt($id));
        return response()->json($record);
    }

    public function employee_refresher_update(Request $request, $id)
    {
        $record = EmployeeRefresherCourse::findOrFail(decrypt($id));
        $request->validate([
            'last_refresher_date' => 'required|date',
            'certificate_no'      => 'required|string|max:100',
            'institution_name'    => 'required|string|max:150',
            'next_due_date'       => 'required|date',
            'remarks'             => 'nullable|string',
        ]);
        $record->update([
            'last_refresher_date' => $request->last_refresher_date,
            'certificate_no'      => $request->certificate_no,
            'institution_name'    => $request->institution_name,
            'next_due_date'       => $request->next_due_date,
            'remarks'             => $request->remarks,
            'updated_by'          => auth()->id(),
        ]);
        return redirect()->back()->with('success', __('Refresher Course record updated successfully.'));
    }

    public function employee_refresher_delete($id)
    {
        $record = EmployeeRefresherCourse::findOrFail(decrypt($id));
        $record->delete();
        return redirect()->back()->with('success', __('Refresher Course record deleted successfully.'));
    }

    public function inspectionReport(Request $request)
    {
        $query = \App\Models\Inspection::query();

        // Filter by logged-in user: only show reports submitted by the current user
        $authUser = Auth::user();
        if ($authUser && !in_array($authUser->type, ['owner', 'super admin'])) {
            // Find the MasterEmployee record linked to the logged-in user
            $employee = MasterEmployee::where('mobile', $authUser->phone_number)->first();
            if ($employee) {
                $query->where('created_by', $employee->id);
            } else {
                // No employee record found, show nothing
                $query->whereRaw('1 = 0');
            }
        }

        // Filter by Movement Type
        if ($request->filled('movement')) {
            $query->where('movement', $request->movement);
        }

        // Filter by Station Yard
        if ($request->filled('station')) {
            $query->where('station_yard', $request->station);
        }

        // Filter by Date Range
        if ($request->filled('start_date')) {
            $query->where('date_of_inspection', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date_of_inspection', '<=', $request->end_date);
        }

        $inspections = $query->with('employee')->latest()->get();

        // Get movement options from master movements table + inspections table
        $movements = [];
        try {
            $movements = \App\Models\Movement::orderBy('name')->pluck('name', 'name')->toArray();
        } catch (\Exception $e) {
            // If model not available
        }
        // Also pull unique movements from actual inspection records
        $existingMovements = \App\Models\Inspection::whereNotNull('movement')
            ->distinct()
            ->pluck('movement', 'movement')
            ->toArray();
        foreach ($existingMovements as $em) {
            if (!isset($movements[$em])) {
                $movements[$em] = $em;
            }
        }
        asort($movements);

        // Get station options from stations master table
        $stationsList = [];
        try {
            $stations = \App\Models\Station::orderBy('name')->get();
            foreach ($stations as $st) {
                $stationsList[$st->name] = strtoupper($st->name) . ' (' . strtoupper($st->code) . ')';
            }
        } catch (\Exception $e) {
            // fallback
        }
        // Also pull unique station_yard values from actual inspection records
        $existingStations = \App\Models\Inspection::whereNotNull('station_yard')
            ->distinct()
            ->pluck('station_yard', 'station_yard')
            ->toArray();
        foreach ($existingStations as $es) {
            if (!isset($stationsList[$es])) {
                $stationsList[$es] = strtoupper($es);
            }
        }
        ksort($stationsList);

        return view('report.inspection_report', compact('inspections', 'movements', 'stationsList'));
    }
}

