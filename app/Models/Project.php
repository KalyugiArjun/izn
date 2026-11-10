<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

     use HasFactory;

    protected $fillable = [
        'project_id',
        'cost',
        'working_agencies',
        'sanction_date',
        'tdc_date',
        'financial_year',
        'plan_head',
        'department_id',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'sanction_date' => 'date',
        'tdc_date'      => 'date',
        'cost'          => 'decimal:2',
    ];

    /* ── Relationships ── */

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ── Auto-generate Project ID ── */

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            $latest = static::latest('id')->first();
            $next   = $latest ? ((int) substr($latest->project_id, 4)) + 1 : 1;
            $project->project_id = 'PRJ-' . str_pad($next, 5, '0', STR_PAD_LEFT);
        });
    }
}
