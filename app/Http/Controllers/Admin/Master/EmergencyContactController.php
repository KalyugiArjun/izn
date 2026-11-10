<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\EmergencyContact;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactController extends Controller
{
    public function index()
    {
        $query = EmergencyContact::with('employee')->latest();

        if ($this->isRestrictedUser()) {
            $query->where('employee_id', $this->ownEmployeeId());
        }

        $records = $query->get();

        return view('admin.master.emergency-contact.index', compact('records'));
    }

    public function create()
    {
        $employees     = $this->getEmployees();
        $employeesData = $this->getEmployeesData($employees);

        return view('admin.master.emergency-contact.create', compact('employees', 'employeesData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:master_employees,id',
            'mobile_number'  => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'relation'       => 'required|string|max:100',
            'other_relation' => 'required_if:relation,Other|nullable|string|max:255',
        ]);

        if ($this->isRestrictedUser() && (int) $request->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $employee = MasterEmployee::findOrFail($request->employee_id);

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

        return redirect()->route('emergency-contact.index')
            ->with('success', __('Emergency Contact created successfully.'));
    }

    public function edit($id)
    {
        $record = EmergencyContact::findOrFail(decrypt($id));

        // 'emp' kisi aur ka record edit na kar paye (URL se id change karke bhi)
        if ($this->isRestrictedUser() && (int) $record->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $employees     = $this->getEmployees();
        $employeesData = $this->getEmployeesData($employees);

        return view('admin.master.emergency-contact.edit', compact('record', 'employees', 'employeesData'));
    }

    public function update(Request $request, $id)
    {
        $record = EmergencyContact::findOrFail(decrypt($id));

        if ($this->isRestrictedUser() && (int) $record->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $request->validate([
            'employee_id'    => 'required|exists:master_employees,id',
            'mobile_number'  => 'required|string|max:20',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string',
            'relation'       => 'required|string|max:100',
            'other_relation' => 'required_if:relation,Other|nullable|string|max:255',
        ]);

        if ($this->isRestrictedUser() && (int) $request->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $employee = MasterEmployee::findOrFail($request->employee_id);

        $record->update([
            'employee_id'    => $employee->id,
            'name'           => $employee->name,
            'mobile_number'  => $request->mobile_number,
            'email'          => $request->email,
            'address'        => $request->address,
            'relation'       => $request->relation,
            'other_relation' => $request->relation === 'Other' ? $request->other_relation : null,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->route('emergency-contact.index')
            ->with('success', __('Emergency Contact updated successfully.'));
    }

    public function destroy($id)
    {
        $record = EmergencyContact::findOrFail(decrypt($id));

        if ($this->isRestrictedUser() && (int) $record->employee_id !== (int) $this->ownEmployeeId()) {
            abort(403);
        }

        $record->delete();

        return redirect()->route('emergency-contact.index')
            ->with('success', __('Emergency Contact deleted successfully.'));
    }


    private function isRestrictedUser(): bool
    {
        $type = strtolower(Auth::user()->type ?? '');

        return $type === 'emp';
    }


    private ?int $ownEmployeeId = null;
    private bool $ownEmployeeResolved = false;

    private function ownEmployeeId(): ?int
    {
        if ($this->ownEmployeeResolved) {
            return $this->ownEmployeeId;
        }

        $user = Auth::user();

        $employee = MasterEmployee::where(function ($q) use ($user) {
            if (!empty($user->email)) {
                $q->orWhere('email', $user->email);
            }

            if (!empty($user->phone)) {
                $q->orWhere('mobile', $user->phone);
            }
        })->first();

        $this->ownEmployeeId       = $employee?->id;
        $this->ownEmployeeResolved = true;

        return $this->ownEmployeeId;
    }


    private function getEmployees()
    {
        $query = MasterEmployee::with(['department', 'designation']);

        if ($this->isRestrictedUser()) {
            $query->where('id', $this->ownEmployeeId());
        }

        return $query->get();
    }


    private function getEmployeesData($employees)
    {
        $data = [];

        foreach ($employees as $employee) {
            $data[$employee->id] = [
                'department'  => $employee->department->name ?? '',
                'designation' => $employee->designation->name ?? '',
            ];
        }

        return $data;
    }
}
