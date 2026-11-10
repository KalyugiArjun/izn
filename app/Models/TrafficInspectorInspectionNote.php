<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficInspectorInspectionNote extends Model
{
    protected $table = 'traffic_inspector_inspection_notes';

    protected $fillable = [
        'inspecting_traffic_inspector',
        'type_of_inspection',
        'place_of_inspection',
        'date_of_inspection',
        'inspection_note_pdf',
        'status',
        'created_by',
        'updated_by',
    ];
    
    public function inspection()
    {
        return $this->belongsTo(Inspection::class);
    }
    
    public function inspectionType()
    {
        return $this->belongsTo(
            TypeOfInspection::class,
            'type_of_inspection',
            'id'
        );
    }
}
