<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Station;
use App\Models\User;
use App\Models\MasterEmployee;
use App\Models\AmenityCategory;
use App\Models\Designation;
use App\Models\Rectify;
use App\Models\Diesellobby;
use App\Models\AuthLoadStableClearance;
use App\Models\AuthMyMovement;
use App\Models\AuthAmbushCheck;
use App\Models\AuthAwardNomination;
use App\Models\Ph16Ti;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserTabsController extends Controller
{
    
public function index(Request $request)
{
    $user = Auth::user();

    $employee = MasterEmployee::with('designation')
        ->where('mobile', $user->phone_number)
        ->first();

    if (!$employee) {
        return response()->json([
            'status' => false,
            'message' => 'Employee not found'
        ]);
    }

    $designationId = $employee->designation_id;

    $stable_clearance = AuthLoadStableClearance::where('designation_id', $designationId)->exists();
    $my_movement = AuthMyMovement::where('designation_id', $designationId)->exists();
    $ambush = AuthAmbushCheck::where('designation_id', $designationId)->exists();
    $ph16Tb = Ph16Ti::where('designation_id', $designationId)->exists();

    $award = AuthAwardNomination::where('designation_id', $designationId)
        ->orWhere('employee_id', $employee->id)
        ->orWhere(function ($q) use ($employee) {
            $q->where('department_id', $employee->department_id)->whereNull('designation_id')->whereNull('employee_id');
        })
        ->exists();

    return response()->json([
        'status' => true,
        'data' => [
            'stable_clearance' => $stable_clearance,
            'ambush' => $ambush,
            'my_movement' => $my_movement,
            'award'  => $award,
            'ph16Ti' => $ph16Tb,
            
        ]
    ]);
}
}
