<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeePME;
use App\Models\EmployeeRefresherCourse;
use App\Models\EmployeeGradation;
use App\Models\MasterEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeSafetyRecordController extends Controller
{
    /**
     * Check if logged-in user exists as MasterEmployee.
     * Returns employee instance or null.
     */
    private function checkUser()
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        return MasterEmployee::where('mobile', $user->phone_number)->first();
    }

    /**
     * Reusable JSON response for auth failure.
     */
    private function userNotFoundResponse()
    {
        return response()->json([
            'status'  => false,
            'message' => 'Employee not found for the logged-in user.',
        ], 404);
    }

    /* =========================================================
     |  PME (Pre-Medical Examination)
     ========================================================= */

    public function storePME(Request $request)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'master_employee_id' => 'required|exists:master_employees,id',
            'staff_id'           => 'required|exists:master_employees,id',
            'last_pme_date'      => 'required|date',
            'certificate_no'     => 'required|string|max:100',
            'next_due_date'      => 'required|date|after_or_equal:last_pme_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $record = EmployeePME::create([
            'master_employee_id' => $request->master_employee_id,
            'staff_id'           => $request->staff_id,
            'last_pme_date'      => $request->last_pme_date,
            'certificate_no'     => $request->certificate_no,
            'next_due_date'      => $request->next_due_date,
            'status'             => 1,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'PME record saved successfully.',
            'data'    => $record,
        ], 201);
    }

    /**
     * GET /api/pme/{master_employee_id}?search=&page=1&per_page=10
     */
    public function getPME(Request $request, $master_employee_id)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;
        $search  = trim($request->get('search', ''));

        $query = EmployeePME::with(['staff:id,name'])
        ->where('master_employee_id', $master_employee_id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_no', 'LIKE', "%{$search}%")
                  ->orWhere('staff_id', 'LIKE', "%{$search}%")
                  ->orWhereDate('last_pme_date', 'LIKE', "%{$search}%")
                  ->orWhereDate('next_due_date', 'LIKE', "%{$search}%");
            });
        }

        $records = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'PME records fetched successfully.',
            'data'    => $records->items(),
            'meta'    => [
                'current_page' => $records->currentPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
                'last_page'    => $records->lastPage(),
                'from'         => $records->firstItem(),
                'to'           => $records->lastItem(),
                'has_more'     => $records->hasMorePages(),
            ],
        ]);
    }

    /* =========================================================
     |  Refresher Course
     ========================================================= */

    public function storeRefresherCourse(Request $request)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'master_employee_id'  => 'required|exists:master_employees,id',
            'staff_id'           => 'required|exists:master_employees,id',
            'last_refresher_date' => 'required|date',
            'certificate_no'      => 'required|string|max:100',
            'institution_name'    => 'required|string|max:150',
            'next_due_date'       => 'required|date|after_or_equal:last_refresher_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $record = EmployeeRefresherCourse::create([
            'master_employee_id'  => $request->master_employee_id,
            'staff_id'            => $request->staff_id,
            'last_refresher_date' => $request->last_refresher_date,
            'certificate_no'      => $request->certificate_no,
            'institution_name'    => $request->institution_name,
            'next_due_date'       => $request->next_due_date,
            'status'              => 1,
            'created_by'          => auth()->id(),
            'updated_by'          => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Refresher Course record saved successfully.',
            'data'    => $record,
        ], 201);
    }

    /**
     * GET /api/refresher-course/{master_employee_id}?search=&page=1&per_page=10
     */
    public function getRefresherCourse(Request $request, $master_employee_id)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;
        $search  = trim($request->get('search', ''));

        $query = EmployeeRefresherCourse::with(['staff:id,name'])
        ->where('master_employee_id', $master_employee_id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('certificate_no', 'LIKE', "%{$search}%")
                  ->orWhere('staff_id', 'LIKE', "%{$search}%")
                  ->orWhere('institution_name', 'LIKE', "%{$search}%")
                  ->orWhereDate('last_refresher_date', 'LIKE', "%{$search}%")
                  ->orWhereDate('next_due_date', 'LIKE', "%{$search}%");
            });
        }

        $records = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Refresher Course records fetched successfully.',
            'data'    => $records->items(),
            'meta'    => [
                'current_page' => $records->currentPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
                'last_page'    => $records->lastPage(),
                'from'         => $records->firstItem(),
                'to'           => $records->lastItem(),
                'has_more'     => $records->hasMorePages(),
            ],
        ]);
    }

    /* =========================================================
     |  Staff Gradation
     ========================================================= */

    public function storeStaffGradation(Request $request)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'master_employee_id' => 'required|exists:master_employees,id',
            'staff_id'           => 'required|exists:master_employees,id',
            'gradation_date'     => 'required|date',
            'total_marks'        => 'required|numeric|min:0|max:100',
            'grade'              => 'required|in:A,B,C,D',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $record = EmployeeGradation::create([
            'master_employee_id' => $request->master_employee_id,
            'staff_id'           => $request->staff_id,
            'gradation_date'     => $request->gradation_date,
            'total_marks'        => $request->total_marks,
            'grade'              => $request->grade,
            'status'             => 1,
            'created_by'         => auth()->id(),
            'updated_by'         => auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Staff Gradation record saved successfully.',
            'data'    => $record,
        ], 201);
    }

    /**
     * GET /api/staff-gradation/{master_employee_id}?search=&page=1&per_page=10
     */
    public function getStaffGradation(Request $request, $master_employee_id)
    {
        if (!$this->checkUser()) {
            return $this->userNotFoundResponse();
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;
        $search  = trim($request->get('search', ''));

        $query = EmployeeGradation::with(['staff:id,name'])
        ->where('master_employee_id', $master_employee_id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'LIKE', "%{$search}%")
                  ->orWhere('grade', 'LIKE', "%{$search}%")
                  ->orWhere('total_marks', 'LIKE', "%{$search}%")
                  ->orWhereDate('gradation_date', 'LIKE', "%{$search}%");
            });
        }

        $records = $query->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Staff Gradation records fetched successfully.',
            'data'    => $records->items(),
            'meta'    => [
                'current_page' => $records->currentPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
                'last_page'    => $records->lastPage(),
                'from'         => $records->firstItem(),
                'to'           => $records->lastItem(),
                'has_more'     => $records->hasMorePages(),
            ],
        ]);
    }
}