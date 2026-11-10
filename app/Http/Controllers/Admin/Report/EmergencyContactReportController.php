<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmergencyContactReportController extends Controller
{
    public function index()
    {
        $departmentsQuery = DB::table('departments')->select('id', 'name')->orderBy('name');

        // Restricted (emp type) user ko sirf uska khud ka department dikhana hai
        if ($this->isRestrictedUser()) {
            $empId = $this->ownEmployeeId();

            $departmentsQuery->whereIn('id', function ($q) use ($empId) {
                $q->select('department_id')
                    ->from('master_employees')
                    ->where('id', $empId);
            });
        }

        $departments = $departmentsQuery->get();
        //    dd($departments); // <-- ye line temporarily daalo aur page reload karo

        return view('admin.report.emergency-contact-report.index', compact('departments'));
    }

    /**
     * Department select hote hi uske designations laao (AJAX)
     * designations table me department_id column hai
     */
    public function getDesignations(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
        ]);

        $designations = DB::table('designations')
            ->where('department_id', $request->department_id)
            ->orderBy('name')
            ->get(['id', 'name']);

            // dd($designations);

        return response()->json($designations);
    }

    /**
     * Department + Designation select hote hi employees laao (AJAX)
     * master_employees me department_id aur designation_id dono columns hai
     */
    public function getEmployees(Request $request)
    {
        $request->validate([
            'department_id'  => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $query = MasterEmployee::where('department_id', $request->department_id)
            ->where('designation_id', $request->designation_id);

        if ($this->isRestrictedUser()) {
            $query->where('id', $this->ownEmployeeId());
        }

        $employees = $query->orderBy('name')->get(['id', 'name']);

        return response()->json($employees);
    }

    /**
     * Employee select + Search click -> uske emergency contacts laao (AJAX)
     */
    public function search(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:master_employees,id',
        ]);

        if ($this->isRestrictedUser() && (int) $request->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $records = EmergencyContact::with('employee.department', 'employee.designation')
            ->where('employee_id', $request->employee_id)
            ->latest()
            ->get();

        return view('admin.report.emergency-contact-report.partials.result-table', compact('records'))->render();
    }


    /**
     * Restricted (emp) user ka apna employee record dhundo — users table ke email/mobile
     * ko master_employees ke email/mobile se match karke
     */
    private function isRestrictedUser(): bool
    {
        $type = strtolower(Auth::user()->type ?? '');

        return $type === 'emp';
    }

    private ?int $ownEmployeeId = null;
    private bool $ownEmployeeResolved = false;

    private function ownEmployeeId(): ?int
    {
        if ($this->ownEmployeeResolved) {
            return $this->ownEmployeeId;
        }

        $user = Auth::user();

        $employee = MasterEmployee::where(function ($q) use ($user) {
            if (!empty($user->email)) {
                $q->orWhere('email', $user->email);
            }
            if (!empty($user->phone)) {
                $q->orWhere('mobile', $user->phone);
            }
        })->first();

        $this->ownEmployeeId       = $employee?->id;
        $this->ownEmployeeResolved = true;

        return $this->ownEmployeeId;
    }
}
