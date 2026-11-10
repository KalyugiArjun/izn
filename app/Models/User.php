<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;
    use Impersonate;
    use HasApiTokens;


    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'phone_number',
        'profile',
        'lang',
        'subscription',
        'subscription_expire_date',
        'parent_id',
        'is_active',
        'twofa_secret',
        'code',
        'last_login_at',
        'user_agent',
        'platform',
        'browser',
        'device_name',
        'device_type',
        'ip_address',
        'app_version',
        'device_id',
        'department_id',
        'designation'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function canImpersonate()
    {
        // Example: Only admins can impersonate others
        return $this->type == 'super admin';
    }

    public function departmentData()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designationData()
    {
        return $this->belongsTo(Designation::class, 'designation');
    }

    /**
     * Get the resolved designation ID of the user.
     */
    public function getDesignationId(): int
    {
        if (!empty($this->designation)) {
            return (int) $this->designation;
        }

        if (!empty($this->phone_number)) {
            $desigId = MasterEmployee::where('mobile', $this->phone_number)->value('designation_id');
            if ($desigId) {
                return (int) $desigId;
            }
        }

        return 0;
    }

    /**
     * Get the resolved department ID of the user.
     */
    public function getDepartmentId(): int
    {
        if (!empty($this->department_id)) {
            return (int) $this->department_id;
        }

        if (!empty($this->phone_number)) {
            $deptId = MasterEmployee::where('mobile', $this->phone_number)->value('department_id');
            if ($deptId) {
                return (int) $deptId;
            }
        }

        return 0;
    }

    /**
     * Check if user's designation matches TI Planning Work (Designation 72).
     * Has access to: New Work, Paperwork Timeline, Variation.
     */
    public function isPlanningWork(): bool
    {
        $desigId = $this->getDesignationId();
        if ($desigId === 72) {
            return true;
        }

        $desigName = '';
        if ($this->designationData) {
            $desigName = strtoupper($this->designationData->name);
        } elseif ($this->masterEmployeeName && $this->masterEmployeeName->designation) {
            $desigName = strtoupper($this->masterEmployeeName->designation->name);
        }

        return str_contains($desigName, 'PLANNING WORK');
    }

    /**
     * Check if user's designation matches PH 16 Work Authority table (ph16_tis).
     * Has access to: Inspection Log.
     * Returns boolean flag (true / false).
     */
    public function isPh16Authorized(): bool
    {
        $desigId = $this->getDesignationId();
        if (!$desigId) {
            return false;
        }

        $query = Ph16Ti::where('designation_id', $desigId);
        $deptId = $this->getDepartmentId();

        if ($deptId) {
            $hasDeptMatch = (clone $query)->where('department_id', $deptId)->exists();
            if ($hasDeptMatch) {
                return true;
            }
        }

        return $query->exists();
    }

    /**
     * Check if user is TI Safety (Designation ID: 78).
     */
    public function isTiSafety(): bool
    {
        $desigId = $this->getDesignationId();
        if ($desigId === 78) {
            return true;
        }

        $desigName = '';
        if ($this->designationData) {
            $desigName = strtoupper($this->designationData->name);
        } elseif ($this->resolved_employee && $this->resolved_employee->designation) {
            $desigName = strtoupper($this->resolved_employee->designation->name);
        }

        return str_contains($desigName, 'TI SAFETY');
    }

    /**
     * Check if user is Sr. DOM (Designation ID: 7).
     */
    public function isSrDom(): bool
    {
        $desigId = $this->getDesignationId();
        if ($desigId === 7) {
            return true;
        }

        $desigName = '';
        if ($this->designationData) {
            $desigName = strtoupper($this->designationData->name);
        } elseif ($this->resolved_employee && $this->resolved_employee->designation) {
            $desigName = strtoupper($this->resolved_employee->designation->name);
        }

        return str_contains($desigName, 'SR DOM') || str_contains($desigName, 'SR. DOM') || str_contains($desigName, 'SR.DOM');
    }

    /**
     * Check if user is authorized to create / manage Award Nominations.
     * Matches user's designation against designations assigned by admin in `auth_award_nominations`.
     */
    public function isAwardNominationAuthorized(): bool
    {
        $type = strtolower($this->type ?? '');
        if (in_array($type, ['super admin', 'owner', 'admin'])) {
            return true;
        }

        // TI Safety and Sr DOM always have workflow access
        if ($this->isTiSafety() || $this->isSrDom()) {
            return true;
        }

        $desigId = $this->getDesignationId();
        $deptId  = $this->getDepartmentId();
        $empId   = $this->resolved_employee?->id;

        if (!$desigId && !$deptId && !$empId) {
            return false;
        }

        return \App\Models\AuthAwardNomination::where(function ($q) use ($desigId, $deptId, $empId) {
            if ($empId) {
                $q->orWhere('employee_id', $empId);
            }

            if ($desigId) {
                $q->orWhere(function ($sub) use ($desigId, $deptId) {
                    $sub->where('designation_id', $desigId);
                    if ($deptId) {
                        $sub->where(function ($dSub) use ($deptId) {
                            $dSub->where('department_id', $deptId)
                                 ->orWhereNull('department_id');
                        });
                    }
                });
            }

            if ($deptId) {
                $q->orWhere(function ($sub) use ($deptId) {
                    $sub->where('department_id', $deptId)
                        ->whereNull('designation_id')
                        ->whereNull('employee_id');
                });
            }
        })->exists();
    }

    public function masterEmployee()
    {
        return $this->hasOne(MasterEmployee::class, 'mobile', 'phone_number');
    }

    public function masterEmployeeName()
    {
        return $this->hasOne(MasterEmployee::class, 'mobile', 'phone_number');
    }

    public function employee()
    {
        return $this->hasOne(MasterEmployee::class, 'mobile', 'phone_number');
    }

    /**
     * Resiliently resolve the linked MasterEmployee record.
     */
    public function getResolvedEmployeeAttribute()
    {
        if ($this->relationLoaded('masterEmployee') && $this->masterEmployee) {
            return $this->masterEmployee;
        }

        if (!empty($this->phone_number)) {
            $emp = MasterEmployee::where('mobile', $this->phone_number)->first();
            if ($emp) {
                return $emp;
            }
        }

        if (is_numeric($this->name)) {
            $emp = MasterEmployee::find($this->name);
            if ($emp) {
                return $emp;
            }
        }

        if (!empty($this->email)) {
            $emp = MasterEmployee::where('email', $this->email)->first();
            if ($emp) {
                return $emp;
            }
        }

        if (!empty($this->name)) {
            $emp = MasterEmployee::where('name', $this->name)->first();
            if ($emp) {
                return $emp;
            }
        }

        return null;
    }
    public function totalUser()
    {
        return User::whereNotIn('type', ['customer', 'employee'])->where('parent_id', $this->id)->count();
    }
    public function totalCustomer()
    {
        return User::whereIn('type', ['customer'])->where('parent_id', $this->id)->count();
    }
    public function totalEmployee()
    {
        return User::whereIn('type', ['employee'])->where('parent_id', $this->id)->count();
    }
    public function totalClothType()
    {
        return ClothType::where('parent_id', $this->id)->count();
    }
    public function totalContact()
    {
        return Contact::where('parent_id', '=', parentId())->count();
    }

    public function roleWiseUserCount($role)
    {
        return User::where('type', $role)->where('parent_id', parentId())->count();
    }

    public static function getDevice($user)
    {
        $mobileType = '/(?:phone|windows\s+phone|ipod|blackberry|(?:android|bb\d+|meego|silk|googlebot) .+? mobile|palm|windows\s+ce|opera mini|avantgo|mobilesafari|docomo)/i';
        $tabletType = '/(?:ipad|playbook|(?:android|bb\d+|meego|silk)(?! .+? mobile))/i';
        if (preg_match_all($mobileType, $user)) {
            return 'mobile';
        } else {
            if (preg_match_all($tabletType, $user)) {
                return 'tablet';
            } else {
                return 'desktop';
            }
        }
    }

    public function subscriptions()
    {
        return $this->hasOne('App\Models\Subscription', 'id', 'subscription');
    }

    public function customers()
    {
        return $this->hasOne('App\Models\Customer', 'user_id', 'id');
    }

    public static $systemModules = [
        'user',
        'customer',
        'measurement',
        'order',
        'invoice',
        'expense',
        'contact',
        'note',
        'cloth type',
        'notification',
        'measurement unit',
        'tax',
        'logged history',
        'settings',
        'profit loss',
        'income',

    ];

    public function SubscriptionLeftDay()
    {
        $Subscription = Subscription::find($this->subscription);
        if ($Subscription->interval == 'Unlimited') {
            $return = '<span class="text-success">' . __('Unlimited Days Left') . '</span>';
        } else {
            $date1 = date_create(date('Y-m-d'));
            $date2 = date_create($this->subscription_expire_date);
            $diff = date_diff($date1, $date2);
            $days = $diff->format("%R%a");
            if ($days > 0) {
                $return = '<span class="text-success">' . $days . __(' Days Left') . '</span>';
            } else {
                $return = '<span class="text-danger">' . $days . __(' Days Left') . '</span>';
            }
        }


        return $return;
    }

    /**
     * Get the user initials for text avatar.
     */
    public function getInitials()
    {
        $name = '';
        if ($this->masterEmployee && !empty($this->masterEmployee->name)) {
            $name = $this->masterEmployee->name;
        } elseif ($this->masterEmployeeName && !empty($this->masterEmployeeName->name)) {
            $name = $this->masterEmployeeName->name;
        } elseif (!empty($this->name)) {
            $name = $this->name;
        } else {
            $name = $this->email ?? 'User';
        }

        // Strip prefix titles (case-insensitive) like Shri, Smt, Dr, Mr, Mrs, Ms
        $name = preg_replace('/^(shri|smt\.?|dr\.?|mr\.?|mrs\.?|ms\.?)\s+/i', '', trim($name));

        $words = explode(' ', trim($name));
        $initials = '';

        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        } elseif (count($words) == 1 && !empty($words[0])) {
            $initials = strtoupper(substr($words[0], 0, 2));
        } else {
            $initials = 'US';
        }

        return $initials;
    }
}
