<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScopeOfWork extends Model
{
    use HasFactory;

    use HasFactory;

    protected $table = 'scope_of_works';

    protected $fillable = [
        'project_id',
        'scope_of_work',
        'department_id',
        'status',
        'created_by',
        'updated_by',
    ];

    /* ── Relationships ── */

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
