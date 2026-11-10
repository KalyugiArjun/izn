<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WagonNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WagonNumberController extends Controller
{


    // =========================
    // GET ALL WAGON NUMBERS
    // =========================

    public function index()
    {

        // $wagons = WagonNumber::with('loadStabling')->latest()->get();

        $wagons = WagonNumber::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $wagons
        ]);
    }

    // =========================
    // CREATE
    // =========================

    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [

                'load_stabling_id' => 'required',

                'wagon_numbers' => 'required|array|min:1',

                'wagon_numbers.*' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $insertData = [];

            foreach ($request->wagon_numbers as $wagonNo) {

                $insertData[] = [
                    'load_stabling_id' => $request->load_stabling_id,
                    'wagon_no' => $wagonNo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

                // OLD DELETE
            WagonNumber::where(
                'load_stabling_id',
                $request->load_stabling_id
            )->delete();

            // NEW INSERT
            WagonNumber::insert($insertData);


            return response()->json([
                'status' => true,
                'message' => 'Wagon Numbers Added Successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    // =========================
    // GET BY LOAD STABLING ID
    // =========================

    public function getByLoadStabling($id)
    {
        try {

            $wagons = WagonNumber::where(
                'load_stabling_id',
                $id
            )->latest()->get();

            return response()->json([
                'status' => true,
                'data' => $wagons
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // UPDATE BY LOAD STABLING ID
    // =========================

    public function updateByLoadStabling(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [

                'wagon_numbers' => 'required|array|min:1',

                'wagon_numbers.*' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // OLD WAGONS DELETE
            WagonNumber::where(
                'load_stabling_id',
                $id
            )->delete();

            // NEW WAGONS INSERT
            $insertData = [];

            foreach ($request->wagon_numbers as $wagonNo) {

                $insertData[] = [
                    'load_stabling_id' => $id,
                    'wagon_no' => $wagonNo,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            WagonNumber::insert($insertData);

            return response()->json([
                'status' => true,
                'message' => 'Wagon Numbers Updated Successfully',
                'data' => $insertData
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================
    // DELETE BY LOAD STABLING ID
    // =========================

    public function deleteByLoadStabling($id)
    {
        try {

            WagonNumber::where(
                'load_stabling_id',
                $id
            )->delete();

            return response()->json([
                'status' => true,
                'message' => 'All Wagon Numbers Deleted Successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    // =========================
    // UPDATE
    // =========================

    public function update(Request $request, $id)
    {
        $wagon = WagonNumber::find($id);

        if (!$wagon) {

            return response()->json([
                'status' => false,
                'message' => 'Wagon Not Found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [

            'wagon_no' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $wagon->update([
            'wagon_no' => $request->wagon_no
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Wagon Updated Successfully',
            'data' => $wagon
        ]);
    }



    // =========================
    // DELETE
    // =========================

    public function destroy($id)
    {
        $wagon = WagonNumber::find($id);

        if (!$wagon) {

            return response()->json([
                'status' => false,
                'message' => 'Wagon Not Found'
            ], 404);
        }

        $wagon->delete();

        return response()->json([
            'status' => true,
            'message' => 'Wagon Deleted Successfully'
        ]);
    }
}