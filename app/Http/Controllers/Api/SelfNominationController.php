<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SelfNomination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\MasterEmployee;
use App\Models\EmployeeStation;

class SelfNominationController extends Controller
{
    //List with Search & Pagination
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
            ], 404);
        }

        $search  = $request->search;
        $perPage = $request->per_page ?? 10;

        $query = SelfNomination::with([
                'proficiency:id,name',
                'designation:id,name',
                'proficiency_cert:id,name',
                'station:id,name',
                'srdom:id,name',
                'ti:id,name'
            ])
            ->where(function ($q) use ($employee) {
                $q->where('staff_id', $employee->id)
                    ->orWhere('ti_id', $employee->id)
                    ->orWhere('sr_dom_id', $employee->id);
            });

        /**
         * Search Filter
         */
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('proficiency', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /**
         * Status Filter
         * Supported:
         * status=1
         * status=1,2
         * status[]=1&status[]=2
         * status=["1","2"]      (JSON array string)
         * status=[1,2]          (JSON array string)
         */
        if ($request->filled('status')) {
            $statusInput = $request->status;

            // If it's a JSON-encoded array string like "[1,2]" or '["1","2"]', decode it
            if (is_string($statusInput)) {
                $trimmed = trim($statusInput);
                if (str_starts_with($trimmed, '[')) {
                    $decoded = json_decode($trimmed, true);
                    if (is_array($decoded)) {
                        $statusInput = $decoded;
                    }
                }
            }

            // If it's still a comma-separated string, explode it
            if (is_string($statusInput)) {
                $statusInput = explode(',', $statusInput);
            }

            $statuses = collect((array) $statusInput)
                ->map(function ($status) {
                    return (int) trim((string) $status);
                })
                ->unique()
                ->values()
                ->toArray();

            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        $data = $query->latest()->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Self Nomination List',
            'data'    => $data,
        ]);
    }


    public function trackYourForm(Request $request)
    {
        $user = Auth::user();

        $employee = MasterEmployee::with('designation')
            ->where('mobile', $user->phone_number)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $search  = $request->search;
        $perPage = $request->per_page ?? 10;

        $query = SelfNomination::query()
            ->with([
                'proficiency:id,name',
                'designation:id,name',
                'proficiency_cert:id,name',
                'station:id,name',
                'srdom:id,name',
                'ti:id,name'
            ])
            ->where(function ($q) use ($employee) {
                $q->where('staff_id', $employee->id);
                
            });

        // Search Filter
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereHas('proficiency', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status Filter
        // Support:
        // status=1
        // status=1,2
        // status=1,2,3
        // status[]=1&status[]=2
        if ($request->has('status') && $request->status !== null && $request->status !== '') {

            $statuses = is_array($request->status)
                ? $request->status
                : array_filter(array_map('trim', explode(',', $request->status)));

            $query->whereIn('status', $statuses);
        }

        $data = $query->latest()->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Self Nomination List',
            'data'    => $data,
        ]);
    }

    //Store
    public function store(Request $request)
    {
        $user = Auth::user();

        $employee = MasterEmployee::with('designation')
            ->where('mobile', $user->phone_number)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'station_id'     => 'required|integer',
            'designation_id' => 'required|integer',
            'date'           => 'required|date',
            'time'           => 'required',
            'name'           => 'required|string|max:150',
            'proficiency'    => 'required|string',
            'description'    => 'required|string|max:2000',
            'file'           => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {

            // TI Find By Station + Designation(78)
            $ti = MasterEmployee::where('designation_id', 78)
                ->whereHas('stations', function ($query) use ($request) {
                    $query->where('station_id', $request->station_id);
                })
                ->first();

            if (!$ti) {
                return response()->json([
                    'status' => false,
                    'message' => 'TI not found for selected station.'
                ], 404);
            }

            $filePath = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filePath = $file->store('nominations', 'public');
                $fileType = $file->getClientOriginalExtension(); // pdf, jpg, png
            }

            $nomination = SelfNomination::create([
                'staff_id'       => $employee->id,
                'station_id'     => $request->station_id,
                'designation_id' => $request->designation_id,
                'date'           => $request->date,
                'time'           => $request->time,
                'name'           => $request->name,
                'proficiency_id'    => $request->proficiency,
                'description'    => $request->description,
                'file'           => $filePath,
                'file_type'      => $fileType,
                'ti_id'          => $ti->id,
                'status'         => 0,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Self Nomination submitted successfully',
                'data'    => $nomination,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);

        }
    }

    // Get By ID
    public function show($id)
    {
        $nomination = SelfNomination::find($id);

        if (!$nomination) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $nomination
        ]);
    }


    // TI FORWARD TO SR.DOM and DOM AWARDED OR REJECTED
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $nomination = SelfNomination::find($id);

        if (!$nomination) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        $isTI = $nomination->ti_id == $employee->id;
        $isDOM = $nomination->sr_dom_id == $employee->id;

        if (!$isTI && !$isDOM) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to update this record'
            ], 403);
        }

        $updateData = [];

        // TI Update
        if ($isTI) {

            $updateData = $request->only([
                'ti_comment',
                'ti_forward_date',
                'ti_forward_time',
                'sr_dom_id',
                'status'
            ]);

            // TI only Pending -> TI Check / Reject
            if (isset($request->status) && !in_array($request->status, [1, 3])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status for TI'
                ], 422);
            }
        }

        // SR.DOM Update
        if ($isDOM) {

            $updateData = array_merge($updateData, $request->only([
                'dom_comment',
                'cert_proficiency',
                'checked_date',
                'checked_time',
                'status'
            ]));

            // Awarded
            if ($request->status == 2 && empty($nomination->certificate_no)) {

                $lastCertificate = SelfNomination::whereNotNull('certificate_no')
                    ->orderBy('id', 'desc')
                    ->first();

                $nextNumber = 1;

                if (
                    $lastCertificate &&
                    preg_match('/(\d+)$/', $lastCertificate->certificate_no, $matches)
                ) {
                    $nextNumber = (int) $matches[1] + 1;
                }

                $updateData['certificate_no'] =
                    'NER/IZN/OPTG/' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // DOM only Awarded / Rejected
            if (isset($request->status) && !in_array($request->status, [2, 3])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid status for SR.DOM'
                ], 422);
            }
        }

        $nomination->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'data' => $nomination->fresh()
        ]);
    }

    public function statusCount(Request $request)
    {
        $user = Auth::user();

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $counts = [
            'status_0' => 0,
            'status_1' => 0,
            'status_2' => 0,
            'status_3' => 0,
           
        ];

        $result = SelfNomination::where(function ($q) use ($employee) {
                $q->where('staff_id', $employee->id)
                    ->orWhere('ti_id', $employee->id)
                    ->orWhere('sr_dom_id', $employee->id);
            })
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        foreach ($result as $row) {
            $counts['status_' . $row->status] = $row->total;
        }

        return response()->json([
            'status' => true,
            'message' => 'Status Wise Count',
            'data' => $counts
        ]);
    }


}