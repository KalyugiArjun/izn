<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    protected $table = 'case_studies';

    protected $fillable = [
        'case_study_title',
        'incident_date',
        'location',
        'department_id',
        'type_of_irregularity',
        'risk_level',
        'key_learning',
        'pdf_file',
        'status',
        'created_by',
        'updated_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
