<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MasterEmployee;
use App\Models\Station;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MasterEmployeeController extends Controller
{
    /**
     * Check if current user has unrestricted / admin access.
     */
    protected function isFullAccessUser(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        return in_array($user->type, ['owner', 'super admin', 'admin']) || empty($user->department_id);
    }

    /**
     * Resolve employee ID from encrypted or numeric string.
     */
    protected function resolveId($id)
    {
        try {
            return is_numeric($id) ? (int)$id : (int)decrypt($id);
        } catch (\Exception $e) {
            return is_numeric($id) ? (int)$id : null;
        }
    }

    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $user = Auth::user();
        $isFullAccess = $this->isFullAccessUser();

        $query = MasterEmployee::with([
            'department',
            'designation',
            'stations'
        ]);

        if (!$isFullAccess && !empty($user->department_id)) {
            $query->where('department_id', $user->department_id);
        }

        $employees = $query->latest()->get();

        return view('admin.master.employee.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $user = Auth::user();
        $isFullAccess = $this->isFullAccessUser();

        $deptQuery = Department::where('status', 1);
        if (!$isFullAccess && !empty($user->department_id)) {
            $deptQuery->where('id', $user->department_id);
        }
        $departments = $deptQuery->orderBy('name')->pluck('name', 'id');

        // Preload active designations
        $desigQuery = Designation::where('status', 1);
        if (!$isFullAccess && !empty($user->department_id)) {
            $desigQuery->where('department_id', $user->department_id);
        }
        $designations = $desigQuery->orderBy('name')->pluck('name', 'id');

        $stations = Station::select(
            'id',
            DB::raw("CONCAT(name, ' (', code, ')') as display_name")
        )->orderBy('name')->pluck('display_name', 'id');

        return view(
            'admin.master.employee.create',
            compact('departments', 'designations', 'stations')
        );
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                            => 'required|string|max:150',
            'usmid'                           => 'nullable|string|max:50|unique:master_employees,usmid',
            'department_id'                   => 'required|exists:departments,id',
            'designation_id'                  => 'required|exists:designations,id',
            'date_of_birth'                   => 'nullable|date',
            'date_of_appointment'             => 'nullable|date',
            'date_of_retirement'              => 'nullable|date',
            'date_of_posting_present_station' => 'nullable|date',
            'station_ids'                     => 'required|array|min:1',
            'station_ids.*'                   => 'exists:stations,id',
            'mobile'                          => 'required|string|max:15',
            'email'                           => 'nullable|email|max:150',
            'level'                           => 'required|string|max:20',
            'posting_present_station'         => 'required|string|max:255',
            'status'                          => 'nullable|in:0,1',
        ], [
            'name.required'                    => __('Please enter employee name.'),
            'department_id.required'           => __('Please select a department.'),
            'designation_id.required'          => __('Please select a designation.'),
            'station_ids.required'             => __('Please select at least one jurisdiction station.'),
            'mobile.required'                  => __('Please enter mobile number.'),
            'level.required'                   => __('Please select a level.'),
            'posting_present_station.required' => __('Please enter posting at present working place.'),
            'usmid.unique'                     => __('This HRMS ID is already assigned to another employee.'),
        ]);

        $usmid = !empty($request->usmid) ? trim($request->usmid) : null;

        $employee = MasterEmployee::create([
            'name'                            => trim($request->name),
            'usmid'                           => $usmid,
            'department_id'                   => $request->department_id,
            'designation_id'                  => $request->designation_id,
            'date_of_birth'                   => $request->date_of_birth ?: null,
            'date_of_appointment'             => $request->date_of_appointment ?: null,
            'date_of_retirement'              => $request->date_of_retirement ?: null,
            'date_of_posting_present_station' => $request->date_of_posting_present_station ?: null,
            'mobile'                          => trim($request->mobile),
            'email'                           => !empty($request->email) ? trim($request->email) : null,
            'posting_present_station'         => trim($request->posting_present_station),
            'level'                           => $request->level,
            'status'                          => isset($request->status) ? (int)$request->status : 1,
            'created_by'                      => auth()->id(),
            'updated_by'                      => auth()->id(),
        ]);

        // Sync stations (many-to-many)
        if (!empty($request->station_ids)) {
            $employee->stations()->sync($request->station_ids);
        }

        return redirect()->route('master.employee.index')->with(
            'success',
            __('Employee created successfully.')
        );
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($id)
    {
        $employeeId = $this->resolveId($id);
        if (!$employeeId) {
            return redirect()->route('master.employee.index')->with('error', __('Invalid employee record.'));
        }

        $employee = MasterEmployee::with('stations')->findOrFail($employeeId);
        $user = Auth::user();
        $isFullAccess = $this->isFullAccessUser();

        $deptQuery = Department::where('status', 1);
        if (!$isFullAccess && !empty($user->department_id)) {
            $deptQuery->where('id', $user->department_id);
        }
        $departments = $deptQuery->orderBy('name')->pluck('name', 'id');

        // Load designations: prioritize department of the employee, fallback to all active
        $desigQuery = Designation::where('status', 1);
        if ($employee->department_id) {
            $desigQuery->where('department_id', $employee->department_id);
        } elseif (!$isFullAccess && !empty($user->department_id)) {
            $desigQuery->where('department_id', $user->department_id);
        }
        $designations = $desigQuery->orderBy('name')->pluck('name', 'id');

        $stations = Station::select(
            'id',
            DB::raw("CONCAT(name, ' (', code, ')') as display_name")
        )->orderBy('name')->pluck('display_name', 'id');

        $selectedStations = $employee->stations
            ->pluck('id')
            ->toArray();

        return view(
            'admin.master.employee.edit',
            compact(
                'employee',
                'departments',
                'designations',
                'stations',
                'selectedStations'
            )
        );
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, $id)
    {
        $employeeId = $this->resolveId($id);
        if (!$employeeId) {
            return redirect()->route('master.employee.index')->with('error', __('Invalid employee record.'));
        }

        $employee = MasterEmployee::findOrFail($employeeId);

        $request->validate([
            'name'                            => 'required|string|max:150',
            'usmid'                           => 'nullable|string|max:50|unique:master_employees,usmid,' . $employee->id,
            'department_id'                   => 'required|exists:departments,id',
            'designation_id'                  => 'required|exists:designations,id',
            'date_of_birth'                   => 'nullable|date',
            'date_of_appointment'             => 'nullable|date',
            'date_of_retirement'              => 'nullable|date',
            'date_of_posting_present_station' => 'nullable|date',
            'station_ids'                     => 'required|array|min:1',
            'station_ids.*'                   => 'exists:stations,id',
            'mobile'                          => 'required|string|max:15',
            'email'                           => 'nullable|email|max:150',
            'posting_present_station'         => 'required|string|max:255',
            'level'                           => 'required|string|max:20',
            'status'                          => 'nullable|in:0,1',
        ], [
            'name.required'                    => __('Please enter employee name.'),
            'department_id.required'           => __('Please select a department.'),
            'designation_id.required'          => __('Please select a designation.'),
            'station_ids.required'             => __('Please select at least one jurisdiction station.'),
            'mobile.required'                  => __('Please enter mobile number.'),
            'level.required'                   => __('Please select a level.'),
            'posting_present_station.required' => __('Please enter posting at present working place.'),
            'usmid.unique'                     => __('This HRMS ID is already assigned to another employee.'),
        ]);

        $usmid = !empty($request->usmid) ? trim($request->usmid) : null;

        // Capture previous values before update for user and related synchronization
        $oldMobile = $employee->mobile;
        $oldEmail  = $employee->email;
        $oldName   = $employee->name;

        $employee->update([
            'name'                            => trim($request->name),
            'usmid'                           => $usmid,
            'department_id'                   => $request->department_id,
            'designation_id'                  => $request->designation_id,
            'date_of_birth'                   => $request->date_of_birth ?: null,
            'date_of_appointment'             => $request->date_of_appointment ?: null,
            'date_of_retirement'              => $request->date_of_retirement ?: null,
            'date_of_posting_present_station' => $request->date_of_posting_present_station ?: null,
            'mobile'                          => trim($request->mobile),
            'email'                           => !empty($request->email) ? trim($request->email) : null,
            'posting_present_station'         => trim($request->posting_present_station),
            'level'                           => $request->level,
            'status'                          => isset($request->status) ? (int)$request->status : 1,
            'updated_by'                      => auth()->id(),
        ]);

        // Sync stations — detaches old and attaches selected stations
        $employee->stations()->sync($request->station_ids ?? []);

        // Synchronize changes to users table and related tables
        $this->syncWithUsers($employee, $oldMobile, $oldEmail, $oldName);

        return redirect()->route('master.employee.index')->with(
            'success',
            __('Employee updated successfully.')
        );
    }

    /**
     * Synchronize employee updates to corresponding user(s) in users table and related records.
     */
    protected function syncWithUsers(MasterEmployee $employee, $oldMobile, $oldEmail, $oldName)
    {
        try {
            $query = User::query();

            $query->where(function ($q) use ($oldMobile, $oldEmail, $oldName, $employee) {
                if (!empty($oldMobile)) {
                    $q->orWhere('phone_number', $oldMobile);
                }
                if (!empty($employee->mobile)) {
                    $q->orWhere('phone_number', $employee->mobile);
                }
                if (!empty($oldEmail)) {
                    $q->orWhere('email', $oldEmail);
                }
                if (!empty($employee->email)) {
                    $q->orWhere('email', $employee->email);
                }
                $q->orWhere('name', (string)$employee->id);
                if (!empty($oldName)) {
                    $q->orWhere('name', $oldName);
                }
            });

            $users = $query->get();

            foreach ($users as $user) {
                // Never overwrite root super admin or owner accounts (ID 1 & 2)
                if (in_array(strtolower($user->type), ['super admin', 'owner']) && $user->id <= 2) {
                    continue;
                }

                $updatePayload = [];

                // Update name
                $updatePayload['name'] = $employee->name;

                // Update mobile / phone_number
                if (!empty($employee->mobile)) {
                    $updatePayload['phone_number'] = $employee->mobile;
                }

                // Update email if provided and not colliding with another user
                if (!empty($employee->email)) {
                    $collision = User::where('email', $employee->email)
                        ->where('id', '!=', $user->id)
                        ->exists();
                    if (!$collision) {
                        $updatePayload['email'] = $employee->email;
                    }
                }

                // Update department and designation
                if (!empty($employee->department_id)) {
                    $updatePayload['department_id'] = $employee->department_id;
                }
                if (!empty($employee->designation_id)) {
                    $updatePayload['designation'] = $employee->designation_id;
                }

                // Update status (is_active)
                if (isset($employee->status)) {
                    $updatePayload['is_active'] = (int)$employee->status;
                }

                if (!empty($updatePayload)) {
                    $user->update($updatePayload);
                }
            }

            // Also keep self_nominations (Module 5) name & designation in sync
            if (\Illuminate\Support\Facades\Schema::hasTable('self_nominations')) {
                \Illuminate\Support\Facades\DB::table('self_nominations')
                    ->where('staff_id', $employee->id)
                    ->update([
                        'name'           => $employee->name,
                        'designation_id' => $employee->designation_id,
                    ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to sync employee to users: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        $employeeId = $this->resolveId($id);
        if (!$employeeId) {
            return redirect()->route('master.employee.index')->with('error', __('Invalid employee record.'));
        }

        $employee = MasterEmployee::findOrFail($employeeId);

        // Detach all station pivot records first
        $employee->stations()->detach();
        $employee->delete();

        return redirect()->route('master.employee.index')->with(
            'success',
            __('Employee deleted successfully.')
        );
    }
}
