<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterEmployee;
use App\Models\Rectify;
use Illuminate\Http\Request;

class CreateRectifyController extends Controller
{
     public function index()
    {
        $rectifies = Rectify::with(['department', 'designation'])->latest()->get();

        $employees = MasterEmployee::all();

        $rectifies->map(function ($rectify) use ($employees) {

            $matchedEmployees = $employees->filter(function ($emp) use ($rectify) {
                return $emp->department_id == $rectify->department_id
                    && $emp->designation_id == $rectify->designation_id;
            })->values();

            $rectify->matched_employees = $matchedEmployees;

            return $rectify;
        });

        return response()->json([
            'status' => true,
            'data' => $rectifies
        ]);
    }
}
