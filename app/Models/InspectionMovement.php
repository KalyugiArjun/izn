<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InspectionMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'movement_id',
        'inspection_date',
        'designation',
        'officer_name',
        'gps_location',
        
        'safety_drive_id',
        'goods_passenger',
        'train_no',
        'block_section',
        'station_id',
        'reason',
        'type_of_leave_id',
        'attend_block_id',

        'created_by',
        'updated_by',

        'joint_note_pdf',
        'survey_pdf'
    ];

    public function employee()
    {
        return $this->belongsTo(MasterEmployee::class, 'employee_id');
    }

    public function movement()
    {
        return $this->belongsTo(Movement::class, 'movement_id');
    }

    public function regularInspection()
    {
        return $this->hasOne(RegularInspection::class, 'inspection_id');
    }

    public function ambushInspection()
    {
        return $this->hasOne(AmbushInspection::class, 'inspection_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function safetyDrive()
    {
        return $this->belongsTo(SafetyDrive::class, 'safety_drive_id');
    }

    public function attendBlock()
    {
        return $this->belongsTo(AttendBlock::class, 'attend_block_id');
    }

    public function typeOfLeave()
    {
        return $this->belongsTo(TypeOfLeave::class, 'type_of_leave_id');
    }
}
