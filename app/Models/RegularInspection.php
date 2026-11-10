<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegularInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'day_night',
        'type_of_inspection_id',
        'station_id',
        'station_lc_gate',
        'gate_no',
        'block_section',
        'engg_traffic',
        'goods_passenger',
        'train_no',
        'location_id',
        'major_deficiency',
        'write_deficiency',
    ];

    public function inspection()
    {
        return $this->belongsTo(InspectionMovement::class, 'inspection_id');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }

    public function type()
    {
        return $this->belongsTo(TypeOfInspection::class, 'type_of_inspection_id');
    }

    public function dieselLobby()
    {
        return $this->belongsTo(Diesellobby::class, 'location_id');
    }

    public function runningRoom()
    {
        return $this->belongsTo(RunningRoomLocation::class, 'location_id');
    }
}
