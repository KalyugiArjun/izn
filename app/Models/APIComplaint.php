<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class APIComplaint extends Model
{
    use HasFactory;

    protected $table = 'api_complaints';

    protected $fillable = [
        'complaint_num',
        'department_id',
        'station_id',
        'amenity_category_id',
        'description',
        'is_safety_critical',
        'timeline',
        'assigned_to',
        'controller_id',
        'images',
        'reported_before',
        'created_by',
        'updated_by',
        'status',
        'location_accuracy',
        'location_address',
        'longitude',
        'latitude',

        'rectifier_name',
        'rectifier_designation',
        'rectification_date',
        'rectification_remark',
        'rectification_images',
        'rectified_by',
        'rectified_at',

        'feedback_description',
        'feedback_images',
        'feedback_rating',
        'feedback_given_by',
        'feedback_given_at',

        'reopen_reason',
        'reopened_by',
        'reopened_at',

        'assigned_employee_id',
        'assigned_employee_at'
    ];

    protected $casts = [
        'images' => 'array',
        'rectification_images' => 'array',
        'feedback_images' => 'array',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function amenityCategory()
    {
        return $this->belongsTo(AmenityCategory::class, 'amenity_category_id');
    }

    public function assignedEmployee()
    {
        return $this->belongsTo(MasterEmployee::class, 'assigned_employee_id');
    }

    public function controller()
    {
        return $this->belongsTo(MasterEmployee::class, 'controller_id');
    }

    public function forwardedToDesignation()
    {
        return $this->belongsTo(Designation::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rectifiedByUser()
    {
        return $this->belongsTo(User::class, 'rectified_by');
    }

    public function reopenedByUser()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    public function transferredComplaints()
    {
        return $this->hasMany(TransferredComplaint::class, 'complaint_id')->latest();
    }

    public function latestTransfer()
    {
        return $this->hasOne(TransferredComplaint::class, 'complaint_id')->latestOfMany();
    }

    public function rectificationHistories()
    {
        return $this->hasMany(RectificationHistory::class, 'complaint_id')->latest();
    }
}
