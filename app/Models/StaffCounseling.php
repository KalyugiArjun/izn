<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffCounseling extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'counseling_date',
        'counseling_time',
        'gps_location',
        'latitude',
        'longitude',
        'counselor_name',
        'counselor_designation',
        'counseling_mode',
        'calling_mode',
        'image',
        'counseling_location',
        'inspection_id',
        'station_id',
        'lc_gate',
        'running_room_id',
        'diesel_lobby_id',
        'train_no',
        'staff',
        'counseling_type',
        'subject_of_counseling',
    ];

    protected $casts = [
        'staff' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function dieselLobby()
    {
        return $this->belongsTo(Diesellobby::class, 'diesel_lobby_id');
    }

    public function counselorDesignation()
    {
        return $this->belongsTo(Designation::class, 'counselor_designation');
    }

    /**
     * Helper to get counselor's designation name (whether stored as ID or string).
     */
    public function getCounselorDesignationNameAttribute(): string
    {
        if (empty($this->counselor_designation)) {
            return '-';
        }

        if (is_numeric($this->counselor_designation)) {
            $desig = Designation::find($this->counselor_designation);
            return $desig ? $desig->name : ('Designation #' . $this->counselor_designation);
        }

        return (string) $this->counselor_designation;
    }

    /**
     * Helper to resolve all counseled staff members into employee & designation models.
     */
    public function getResolvedStaffListAttribute(): array
    {
        $rawStaff = $this->staff;
        if (is_string($rawStaff)) {
            $rawStaff = json_decode($rawStaff, true);
        }

        if (!is_array($rawStaff) || empty($rawStaff)) {
            return [];
        }

        $list = [];
        foreach ($rawStaff as $item) {
            $empId   = $item['staff_id'] ?? null;
            $desigId = $item['designation_id'] ?? null;
            $stnId   = $item['station_id'] ?? null;

            $emp = $empId ? MasterEmployee::find($empId) : null;
            $desig = $desigId ? Designation::find($desigId) : ($emp && $emp->designation ? $emp->designation : null);
            $station = $stnId ? Station::find($stnId) : ($emp && $emp->station ? $emp->station : null);

            $list[] = [
                'staff_id'         => $empId,
                'staff_name'       => $emp ? $emp->name : ('Staff #' . ($empId ?? '-')),
                'employee_code'    => $emp ? $emp->employee_code : '-',
                'designation_id'   => $desigId,
                'designation_name' => $desig ? $desig->name : '-',
                'station_name'     => $station ? ($station->name . ($station->code ? ' (' . $station->code . ')' : '')) : '-',
            ];
        }

        return $list;
    }
}
