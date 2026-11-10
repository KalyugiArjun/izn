<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Station;
use App\Models\MasterEmployee;

/**
 * LoadStabling — master record (Points-man Stage 1 entry)
 *
 * @property int    $id
 * @property string $pointsman_id
 * @property string $station
 * @property string $date_time
 * @property string $gps_location
 * @property string $train_number
 * @property string $arrival_time
 * @property string $fouling_mark
 * @property string $load_type
 * @property string $load_sub_type
 * @property string $stabling_line
 * @property string $total_load
 * @property string $all_vehicles_coupled
 * @property string $load_completely_stabled
 * @property array  $safety_check_data
 * @property string $photo_wedges
 * @property string $photo_chains
 * @property string $status
 */
class LoadStabling extends Model
{
    // protected $fillable = [
    //     'pointsman_id',
    //     'assign_to_sm',
    //     'pointsman_name',
    //     'station',
    //     'stable_station',
    //     'date_time',
    //     'gps_location',
    //     'train_number',
    //     'arrival_time',
    //     'fouling_mark',
    //     'load_type',
    //     'load_sub_type',
    //     'stabling_line',
    //     'running_line',
    //     'loco_number', 
    //     'type_of_machine',
    //     'total_load',
    //     'all_vehicles_coupled',
    //     'load_completely_stabled',
    //     'safety_check_data',
    //     'photo_wedges',
    //     'photo_chains',
    //     'status',
    // ];

    protected $fillable = [
    'pointsman_id',
    'old_pm_id',
    'assign_to_sm',
    'old_sm_id',
    'pointsman_name',
    'station',
    'stable_station',
    'date_time',
    'gps_location',
    'train_number',
    'arrival_time',
    'fouling_mark',
    'load_type',
    'load_sub_type',
    'coaching_sub_type',   // ✅ NEW
    'goods_sub_type',      // ✅ NEW
    'stabling_line',
    'running_line',
    'loco_number',
    'type_of_machine',
    'total_load',
    'all_vehicles_coupled',
    'load_completely_stabled',
    'tm_private_number',   // ✅ NEW
    'safety_check_data',
    'photo_wedges',
    'photo_chains',
    'status',
];

    protected $casts = [
        'type_of_machine' => 'array',
        // Automatically encode/decode as PHP array ↔ JSON string in DB
        'safety_check_data' => 'array',
    ];

    /* ── Relationships ────────────────────────────────────────── */

    public function smChecklist(): HasOne
    {
        return $this->hasOne(LoadStablingSmChecklist::class, 'load_stabling_id');
    }

    public function clearance(): HasOne
    {
        return $this->hasOne(LoadStablingClearance::class, 'load_stabling_id');
    }

    public function finalSubmission(): HasOne
    {
        return $this->hasOne(LoadStablingFinalSubmission::class, 'load_stabling_id');
    }
     
    public function stablestationData()
    {
        return $this->belongsTo(Station::class, 'stable_station', 'id');
    }
    public function stationData()
    {
        return $this->belongsTo(Station::class, 'station', 'id');
    }
    public function pointmanData()
    {
        return $this->belongsTo(MasterEmployee::class, 'pointsman_id', 'id');
    }
    public function assigntosmData()
    {
        return $this->belongsTo(MasterEmployee::class, 'assign_to_sm', 'id');
    }
    public function oldsmData()
    {
        return $this->belongsTo(MasterEmployee::class, 'old_sm_id', 'id');
    }
    public function oldpmData()
    {
        return $this->belongsTo(MasterEmployee::class, 'old_pm_id', 'id');
    }
}
