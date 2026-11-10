<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;

class CreateComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::with(['department', 'designation'])->latest()->get();
        $employees = MasterEmployee::all();

        $complaints->map(function ($complaint) use ($employees) {

            $matchedEmployees = $employees->filter(function ($emp) use ($complaint) {
                return $emp->department_id == $complaint->department_id
                    && $emp->designation_id == $complaint->designation_id;
            })->values();

            $complaint->matched_employees = $matchedEmployees;

            return $complaint;
        });

        return response()->json([
            'status' => true,
            'data' => $complaints
        ]);
    }
}
