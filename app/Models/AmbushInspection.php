<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AmbushInspection extends Model
{
    use HasFactory;

    protected $table = 'ambush_inspections';

    protected $fillable = [
        'inspection_id',
        'station_id',
        'from_time',
        'to_time',
        'day_night',
        'type_of_ambush_id',
        'lobby_id',
        'shunting_observation',
        't_806_no',
        'train_no',

        'shunting_staff_name',
        'shunting_staff',

        'load_type',
        'loco_self_proppeled',
        'track_machine',
        'total_number_of_load',
        'running_line',
        'stabling_line_no',
        'block_section',
        'block_taken_by',
        'date_of_voice_recording',
        'lc_gate_no',
        'voice_recording_time_train',
        'voice_recording_train',
        'voice_recording_time_lc',
        'voice_recording_lc',
        'actual_departure_time',
        'actual_arrival_time',
        'staff_name',
        'designation',
        'alcohol_test',
        'alcohol_percentage',
        'major_deficiency',
        'write_deficiency',
    ];

    protected $casts = [
        'shunting_staff' => 'array',
    ];

    public function inspection()
    {
        return $this->belongsTo(InspectionMovement::class, 'inspection_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function staffName()
    {
        return $this->belongsTo(MasterEmployee::class, 'staff_name');
    }

    public function ambushType()
    {
        return $this->belongsTo(TypeOfAmbush::class, 'type_of_ambush_id');
    }

    public function lobby()
    {
        return $this->belongsTo(Diesellobby::class, 'lobby_id');
    }

    /**
     * Decode and resolve shunting staff with employee name and designation name
     */
    public function getShuntingStaffDetailsAttribute()
    {
        if (empty($this->shunting_staff) || !is_array($this->shunting_staff)) {
            return collect();
        }

        $staffs = collect($this->shunting_staff);
        $empIds = $staffs->pluck('employee_id')->filter()->unique();
        $desigIds = $staffs->pluck('designation_id')->filter()->unique();

        $employees = MasterEmployee::whereIn('id', $empIds)->get()->keyBy('id');
        $designations = Designation::whereIn('id', $desigIds)->get()->keyBy('id');

        return $staffs->map(function ($s) use ($employees, $designations) {
            $emp = $employees->get($s['employee_id'] ?? null);
            $desig = $designations->get($s['designation_id'] ?? null);
            return [
                'employee_id' => $s['employee_id'] ?? null,
                'name' => $emp ? $emp->name : '-',
                'designation_id' => $s['designation_id'] ?? null,
                'designation' => $desig ? $desig->name : ($emp && $emp->designation ? $emp->designation->name : '-'),
            ];
        });
    }

    /**
     * Resolve designation name for BA Test staff
     */
    public function getBaStaffDesignationNameAttribute()
    {
        if (!empty($this->designation) && is_numeric($this->designation)) {
            $desig = Designation::find($this->designation);
            if ($desig) {
                return $desig->name;
            }
        }
        if (!empty($this->designation)) {
            return $this->designation;
        }
        if ($this->staffName && $this->staffName->designation) {
            return $this->staffName->designation->name;
        }
        return '-';
    }
}
