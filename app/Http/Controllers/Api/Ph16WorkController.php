<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\MasterEmployee;
use App\Models\IdentifyNewWork;
use App\Models\JointSurvey;
use App\Models\Estimate;
use App\Models\ApprovalListing;
use App\Models\FinanceVettingSanction;
use App\Models\Tendering;
use App\Models\ReviewEstimate;
use App\Models\Esp;
use App\Models\Sip;
use App\Models\Ph16Ti;
use App\Models\Execution;
use App\Models\Executionend;
use App\Models\Variation;
use App\Models\InspectionLog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class Ph16WorkController extends Controller
{

       public function Ph16Tidropdown()
    {
        $data = Ph16Ti::with(['designation:id,name', 'department:id,name'])
        ->select(
                'id',
                'department_id',
                'designation_id'
            )
            ->orderBy('id')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'TIS dropdown list fetched successfully',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'work_description_identification'=> 'required|string',
            'date_of_identification_of_work' => 'required|date',
            'concern_traffic_inspector'      => 'required|integer',
            'ph16_ti_id'                     => 'required|integer',
            'status'                         => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $work = IdentifyNewWork::create([
            'work_description_identification'=> $request->work_description_identification,
            'date_of_identification_of_work' => $request->date_of_identification_of_work,
            'concern_traffic_inspector'      => $request->concern_traffic_inspector,
            'ph16_ti_id'                     => $request->ph16_ti_id,
            'status'                         => $request->status,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Work created successfully',
            'data' => $work
        ], 201);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $search = $request->search;
        $perPage = $request->per_page ?? 50;

        $query = IdentifyNewWork::query()
        ->with([
            // 'ph16Ti:id,designation_id',
            'ph16Ti.designation:id,name',
        ]);

        // Agar employee wise data rakhte ho to uncomment karo
        // $query->where('employee_id', $employee->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('work_description_identification', 'like', "%{$search}%")
                ->orWhere('date_of_identification_of_work', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $works = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Work list fetched successfully',
            'data' => $works->items(),
            'pagination' => [
                'current_page' => $works->currentPage(),
                'last_page' => $works->lastPage(),
                'per_page' => $works->perPage(),
                'total' => $works->total(),
                'from' => $works->firstItem(),
                'to' => $works->lastItem(),
            ]
        ]);
    }


    public function checkJointSurvey(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = JointSurvey::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function jointSurveyStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = JointSurvey::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'joint-survey',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = JointSurvey::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function jointSurveyIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = JointSurvey::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }

    public function checkEstimate(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Estimate::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->department));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function EstimateStore(Request $request)
    {
        $user = Auth::user();
        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $request->validate([
            'identify_new_work_id' => 'required|integer',
            'department' => 'required|string',

            'estimate_called' => 'nullable|date',
            'estimate_received' => 'nullable|date',

            'call_letter' => 'nullable|file|mimes:pdf|max:10240',
            'received_estimate' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = $request->only([
            'identify_new_work_id',
            'department',
            'estimate_called',
            'estimate_received',
        ]);

        if ($request->hasFile('call_letter')) {
            $data['call_letter'] = $request->file('call_letter')
                ->store('estimates/call-letter', 'public');
        }

        if ($request->hasFile('received_estimate')) {
            $data['received_estimate'] = $request->file('received_estimate')
                ->store('estimates/received-estimate', 'public');
        }

        $estimate = Estimate::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Estimate created successfully',
            'data' => $estimate
        ]);
    }
    public function EstimateIndex(Request $request)
    {
        $user = Auth::user();
        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Estimate::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('department', 'like', "%{$search}%")
                ->orWhere('identify_new_work_id', 'like', "%{$search}%")
                ->orWhere('estimate_called', 'like', "%{$search}%")
                ->orWhere('estimate_received', 'like', "%{$search}%");
            });
        }

        $estimates = $query
            ->latest('id')
            ->paginate($perPage);

        $estimates->getCollection()->transform(function ($item) {

            $item->call_letter_url = $item->call_letter
                ? asset('storage/' . $item->call_letter)
                : null;

            $item->received_estimate_url = $item->received_estimate
                ? asset('storage/' . $item->received_estimate)
                : null;

            return $item;
        });

        return response()->json([
            'status' => true,
            'message' => 'Estimate list fetched successfully',
            'data' => $estimates->items(),
            'pagination' => [
                'current_page' => $estimates->currentPage(),
                'last_page' => $estimates->lastPage(),
                'per_page' => $estimates->perPage(),
                'total' => $estimates->total(),
            ]
        ]);
    }


    public function checkReviewEstimate(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = ReviewEstimate::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function ReviewEstimateStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'review_estimate_date' => 'required|date',
            'days_taken'           => 'required|integer',
            'previous_department'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = ReviewEstimate::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

    

        $jointSurvey = ReviewEstimate::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'review_estimate_date' => $request->review_estimate_date,
            'days_taken'           => $request->days_taken,
            'previous_department'  => $request->previous_department,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function ReviewEstimateIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = ReviewEstimate::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkApproval(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = ApprovalListing::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function ApprovalStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = ApprovalListing::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'approval-listing',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = ApprovalListing::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function ApprovalIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = ApprovalListing::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkFinanceVetting(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = FinanceVettingSanction::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function FinanceVettingStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = FinanceVettingSanction::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'finance-vetting-sanction',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = FinanceVettingSanction::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function FinanceVettingIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = FinanceVettingSanction::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkTendering(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Tendering::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function TenderingStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'department'           => 'required|string',
            'date'                 => 'required|date',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = Tendering::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'tendering',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Tendering::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'department'            => $request->department,
            'date'                 => $request->date,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function TenderingIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Tendering::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('department', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


   


    public function checkEsp(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Esp::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function EspStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = Esp::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'esp',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Esp::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'department'            => $request->department,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'esp saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function EspIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Esp::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkSip(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Sip::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function SipStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = Sip::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'esp',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Sip::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'department'            => $request->department,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'esp saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function SipIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Sip::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkExecution(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Execution::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function ExecutionStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'work_completion_date' => 'required|date',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = Execution::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'execution',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Execution::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'department'            => $request->department,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'work_completion_date' => $request->work_completion_date,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'esp saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function ExecutionIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Execution::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }

    
    public function checkExecutionEnd(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Executionend::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function ExecutionEndStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'milestone'            => 'required|string',
            'date'                 => 'required|date',
            'days_taken'           => 'required|integer',
            'work_completion_date' => 'required|date',
            'supporting_document'  => 'required|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Duplicate Check
        $exists = Execution::where('identify_new_work_id', $request->identify_new_work_id)
            ->where('milestone', $request->milestone)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'This milestone already exists for this work.',
            ], 422);
        }

        $filePath = null;

       if ($request->hasFile('supporting_document')) {
            $file = $request->file('supporting_document');

            $filePath = $file->storeAs(
                'execution-end',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Executionend::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'department'            => $request->department,
            'date'                 => $request->date,
            'days_taken'           => $request->days_taken,
            'work_completion_date' => $request->work_completion_date,
            'supporting_document'  => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'esp saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function ExecutionEndIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Executionend::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', "%{$search}%")
                ->orWhere('days_taken', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Joint Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }


    public function checkVariation(Request $request)
    {
        $searchMilestone = preg_replace('/\s+/', '', strtolower($request->milestone));

        $exists = Variation::where('identify_new_work_id', $request->identify_new_work_id)
            ->get()
            ->contains(function ($item) use ($searchMilestone) {
                $dbMilestone = preg_replace('/\s+/', '', strtolower($item->milestone));

                return $dbMilestone === $searchMilestone;
            });

        return response()->json([
            'status'  => $exists,
            'message' => $exists ? 'Record already exists' : 'Record not found'
        ]);
    }
    public function VariationStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id' => 'required|integer',
            'date_of_variation'    => 'required|date',
            'nature_of_variation'  => 'required|string',
            'revised_tdc'          => 'nullable|date',
            'file'                 => 'nullable|file|mimes:pdf|max:10240', // 10 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        
        $filePath = null;

       if ($request->hasFile('file')) {
            $file = $request->file('file');

            $filePath = $file->storeAs(
                'variation',
                $file->hashName(),
                'public'
            );
        }

        $jointSurvey = Variation::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'nature_of_variation'  => $request->nature_of_variation,
            'date_of_variation'    => $request->date_of_variation,
            'revised_tdc'          => $request->revised_tdc,
            'file'                 => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'esp saved successfully.',
            'data' => $jointSurvey,
        ]);
    }
    public function VariationIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = Variation::query()
        ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('nature_of_variation', 'like', "%{$search}%")
                ->orWhere('date_of_variation', 'like', "%{$search}%")
                ->orWhere('revised_tdc', 'like', "%{$search}%");
            });
        }

        $jointSurveys = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'variation Survey List',
            'data' => $jointSurveys->items(),
            'pagination' => [
                'current_page' => $jointSurveys->currentPage(),
                'last_page' => $jointSurveys->lastPage(),
                'per_page' => $jointSurveys->perPage(),
                'total' => $jointSurveys->total(),
            ]
        ]);
    }

    public function InspectionLogStore(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'identify_new_work_id'          => 'required|integer',
            'date_of_inspection'            => 'required|date',
            'department_inspected'          => 'required|string',
            'inspecting_authority'          => 'required|string',
            'name_of_inspecting_official'   => 'required|string',
            'work_done_detail'              => 'required|string',
            'photo'                         => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $filePath = null;

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            $filePath = $file->storeAs(
                'inspection-log',
                $file->hashName(),
                'public'
            );
        }

        $inspectionLog = InspectionLog::create([
            'identify_new_work_id'          => $request->identify_new_work_id,
            'date_of_inspection'            => $request->date_of_inspection,
            'department_inspected'          => $request->department_inspected,
            'inspecting_authority'          => $request->inspecting_authority,
            'name_of_inspecting_official'   => $request->name_of_inspecting_official,
            'work_done_detail'              => $request->work_done_detail,
            'photo'                         => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Inspection log saved successfully.',
            'data' => $inspectionLog,
        ]);
    }

    public function InspectionLogIndex(Request $request)
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $perPage = $request->per_page ?? 10;

        $query = InspectionLog::query()
            ->with(['identifyNewWork:id,work_description_identification']);

        if ($request->filled('identify_new_work_id')) {
            $query->where(
                'identify_new_work_id',
                $request->identify_new_work_id
            );
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name_of_inspecting_official', 'like', "%{$search}%")
                    ->orWhere('detail_of_work', 'like', "%{$search}%")
                    ->orWhere('department_inspected', 'like', "%{$search}%")
                    ->orWhere('inspecting_authority', 'like', "%{$search}%")
                    ->orWhere('date_of_inspection', 'like', "%{$search}%")
                    ->orWhere('revised_tdc', 'like', "%{$search}%");
            });
        }

        $inspectionLogs = $query
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Inspection Log List',
            'data' => $inspectionLogs->items(),
            'pagination' => [
                'current_page' => $inspectionLogs->currentPage(),
                'last_page'    => $inspectionLogs->lastPage(),
                'per_page'     => $inspectionLogs->perPage(),
                'total'        => $inspectionLogs->total(),
            ]
        ]);
    }


}