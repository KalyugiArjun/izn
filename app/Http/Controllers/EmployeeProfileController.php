<?php

namespace App\Http\Controllers;

use App\Models\MasterEmployee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmergencyContact;

class EmployeeProfileController extends Controller
{

    public function index()
    {
        $employee = $this->resolveEmployeeForUser([
            'department',
            'designation',
            'stations',
            'emergencyContacts',
        ]);

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        return view('employee-profile.index', compact('employee'));
    }

    public function emergencyContacts()
    {
        $employee = $this->resolveEmployeeForUser(['emergencyContacts']);

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        return view('employee-profile.emergency-contacts', compact('employee'));
    }

    public function edit($id, $section)
    {
        $employee = MasterEmployee::findOrFail(decrypt($id));

        $this->authorizeEmployee($employee);

        $data = compact('employee', 'section');

        if ($section === 'personal') {
            $data['departments']  = Department::orderBy('name')->get();
            $data['designations'] = Designation::orderBy('name')->get();

            return view('employee-profile.edit-personal', $data);
        }

        if ($section === 'posting') {
            $data['stations'] = \App\Models\Station::select(
                'id',
                \Illuminate\Support\Facades\DB::raw("CONCAT(name, ' (', code, ')') as display_name")
            )->orderBy('name')->pluck('display_name', 'id');
            $data['selectedStations'] = $employee->stations->pluck('id')->toArray();

            return view('employee-profile.edit-posting', $data);
        }

        if ($section === 'contact') {
            return view('employee-profile.edit-contact', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id, $section)
    {
        $employee = MasterEmployee::findOrFail(decrypt($id));

        $this->authorizeEmployee($employee);

        if ($section === 'personal') {

            $request->validate([
                'name'                => 'required|string|max:255',
                'usmid'               => 'nullable|string|max:100',
                'department_id'       => 'nullable|exists:departments,id',
                'designation_id'      => 'nullable|exists:designations,id',
                'date_of_birth'       => 'nullable|date',
                'date_of_appointment' => 'nullable|date',
                'date_of_retirement'  => 'nullable|date',
                'level'               => 'nullable|string|max:100',
            ]);

            $employee->update($request->only([
                'name',
                'usmid',
                'department_id',
                'designation_id',
                'date_of_birth',
                'date_of_appointment',
                'date_of_retirement',
                'level',
            ]));

            // Sync with current user
            $user = Auth::user();
            if ($user) {
                $userUpdate = ['name' => $employee->name];
                if (!empty($employee->department_id)) {
                    $userUpdate['department_id'] = $employee->department_id;
                }
                if (!empty($employee->designation_id)) {
                    $userUpdate['designation'] = $employee->designation_id;
                }
                $user->update($userUpdate);
            }
        }

        if ($section === 'posting') {

            $request->validate([
                'jurisdiction_stations'           => 'nullable|string|max:255',
                'posting_present_station'         => 'nullable|string|max:255',
                'date_of_posting_present_station' => 'nullable|date',
                'posting'                         => 'nullable|string|max:255',
                'station_ids'                     => 'nullable|array',
                'station_ids.*'                   => 'exists:stations,id',
            ]);

            $employee->update($request->only([
                'posting_present_station',
                'date_of_posting_present_station',
            ]));

            if ($request->has('station_ids')) {
                $employee->stations()->sync($request->station_ids ?? []);
            }
        }

        if ($section === 'contact') {

            $request->validate([
                'mobile' => 'required|string|max:20',
                'email'  => 'required|email|max:255',
                'status' => 'required|in:1,2',
            ]);

            $employee->update($request->only([
                'mobile',
                'email',
                'status',
            ]));

            // Sync with current user
            $user = Auth::user();
            if ($user) {
                $userUpdate = ['phone_number' => $employee->mobile];
                $collision = \App\Models\User::where('email', $employee->email)->where('id', '!=', $user->id)->exists();
                if (!$collision) {
                    $userUpdate['email'] = $employee->email;
                }
                $user->update($userUpdate);
            }
        }

        return redirect()->route('employee-profile.index')
            ->with('success', __(ucfirst($section) . ' details updated successfully.'));
    }


    private function authorizeEmployee(MasterEmployee $employee)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $matchesEmail = !empty($user->email) && $employee->email === $user->email;
        $matchesPhone = (!empty($user->phone_number) && $employee->mobile === $user->phone_number) || (!empty($user->phone) && $employee->mobile === $user->phone);
        $matchesName  = (!empty($user->name) && $employee->name === $user->name);
        $isAdmin      = in_array(strtolower($user->type ?? ''), ['super admin', 'admin']);

        if (!$matchesEmail && !$matchesPhone && !$matchesName && !$isAdmin) {
            abort(403);
        }
    }

    public function emergencyContactCreate()
    {
        $employee = $this->resolveEmployeeForUser(['department', 'designation']);

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        return view('employee-profile.emergency-contact-create', compact('employee'));
    }


    public function emergencyContactStore(Request $request)
    {
        $employee = $this->resolveEmployeeForUser();

        if (!$employee) {
            abort(404, 'Employee profile not found.');
        }

        $request->validate([
            'mobile_number'  => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'relation'       => 'required|string|max:100',
            'other_relation' => 'required_if:relation,Other|nullable|string|max:255',
        ]);

        EmergencyContact::create([
            'employee_id'    => $employee->id,
            'name'           => $employee->name,
            'mobile_number'  => $request->mobile_number,
            'email'          => $request->email,
            'address'        => $request->address,
            'relation'       => $request->relation,
            'other_relation' => $request->relation === 'Other' ? $request->other_relation : null,
            'created_by'     => Auth::id(),
        ]);

        $redirectTo = $request->input('redirect_to', 'profile');
        if ($redirectTo === 'emergency-contacts') {
            return redirect()->route('employee-profile.emergency-contacts')
                ->with('success', __('Emergency Contact added successfully.'));
        }

        return redirect()->route('employee-profile.index')
            ->with('success', __('Emergency Contact added successfully.'));
    }

    private function resolveEmployeeForUser(array $with = [])
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $query = MasterEmployee::query();

        if (!empty($with)) {
            $query->with($with);
        }

        $employee = $query->where(function ($q) use ($user) {
            if (!empty($user->email)) {
                $q->orWhere('email', $user->email);
            }

            if (!empty($user->phone_number)) {
                $q->orWhere('mobile', $user->phone_number);
            }
            if (!empty($user->phone)) {
                $q->orWhere('mobile', $user->phone);
            }
        })->first();

        if ($employee) {
            return $employee;
        }

        if ($user->resolved_employee) {
            $resolvedId = $user->resolved_employee->id;
            return MasterEmployee::with($with)->find($resolvedId);
        }

        if (!empty($user->name)) {
            $byName = MasterEmployee::with($with)->where('name', $user->name)->first();
            if ($byName) {
                return $byName;
            }
        }

        return null;
    }
}
