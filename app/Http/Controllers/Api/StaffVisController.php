<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StaffVisCounsellingOffline;
use App\Models\StaffVisCounsellingOnline;

use App\Models\StaffVisAmbushBaTest;
use App\Models\StaffVisAmbushShunting;

use App\Models\StaffVisGradation;
use App\Models\StaffVisMedical;
use App\Models\StaffVisRefresher;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StaffVisController extends Controller
{
    public function StaffVisCounsellingOffline(Request $request)
    {
        $user = StaffVisCounsellingOffline::get();
        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }
    public function StaffVisCounsellingOnline(Request $request)
    {
        $user = StaffVisCounsellingOnline::get();
        return response()->json([
            'status' => true,
            'data' => $user
        ]);
    }


    public function StaffVisAmbushBaTest(Request $request)
    {
        $user = StaffVisAmbushBaTest::get();
        return response()->json([
            'status' => true,
            'message' => 'ambush-batest-staff',
            'data' => $user
        ]);
    }
    public function StaffVisAmbushShunting(Request $request)
    {
        $user = StaffVisAmbushShunting::get();
        return response()->json([
            'status' => true,
            'message' => 'Staff Vis Ambush Shunting',
            'data' => $user
        ]);
    }

    public function StaffVisMedical(Request $request)
    {
        $user = StaffVisMedical::get();
        return response()->json([
            'status' => true,
            'message' => 'Staff Vis Medical',
            'data' => $user
        ]);
    }
    public function StaffVisRefresher(Request $request)
    {
        $user = StaffVisRefresher::get();
        return response()->json([
            'status' => true,
            'message' => 'Staff Vis Refresher',
            'data' => $user
        ]);
    }
    public function StaffVisGradation(Request $request)
    {
        $user = StaffVisGradation::get();
        return response()->json([
            'status' => true,
            'message' => 'Staff Vis Gradation',
            'data' => $user
        ]);
    }
}