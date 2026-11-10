<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEmployee extends Model
{
    use HasFactory;

    protected $table = 'master_employees';

    protected $fillable = [
        'name',
        'usmid',
        'department_id',
        'designation_id',
        'date_of_birth',
        'date_of_appointment',
        'date_of_retirement',
        'date_of_posting_present_station',
        'employee_id',
        'mobile',
        'email',
        'posting_present_station',
        'level',
        'emergency_contact_number',
        'relation',
        'status',
        'created_by',
        'updated_by',
    ];

    /* ── Relationships ── */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function stations()
    {
        return $this->belongsToMany(
            Station::class,
            'employee_station',
            'master_employee_id',
            'station_id'
        );
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class, 'employee_id');
    }
}
