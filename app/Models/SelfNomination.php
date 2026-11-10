<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfNomination extends Model
{
    protected $table = 'self_nominations';

    protected $fillable = [
        'staff_id',
        'date',
        'time',
        'name',
        'designation_id',
        'proficiency_id',
        'description',
        'file',
        'file_type',
        'station_id',
        'ti_id',
        'ti_comment',
        'ti_forward_date',
        'ti_forward_time',
        'sr_dom_id',
        'dom_comment',
        'cert_proficiency',
        'checked_date',
        'checked_time',
        'certificate_no',
        'status',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staff()
    {
        return $this->belongsTo(MasterEmployee::class, 'staff_id');
    }
    public function ti()
    {
        return $this->belongsTo(MasterEmployee::class, 'ti_id');
    }
    public function srdom()
    {
        return $this->belongsTo(MasterEmployee::class, 'sr_dom_id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }
    public function proficiency()
    {
        return $this->belongsTo(AreaOfProficiency::class, 'proficiency_id');
    }
    public function proficiency_cert()
    {
        return $this->belongsTo(AreaOfProficiency::class, 'cert_proficiency');
    }
}