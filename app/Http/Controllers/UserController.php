<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\LoggedHistory;
use App\Models\MasterEmployee;
use App\Models\Notification;
use App\Models\PackageTransaction;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage user')) {
            if (\Auth::user()->type == 'super admin') {
                $users = User::where('parent_id', parentId())->where('type', 'owner')->orderBy('id', 'desc')->get();
                return view('user.index', compact('users'));
            } else {

                $users = User::with('departmentData', 'designationData', 'masterEmployee')
                    ->where('parent_id', parentId())
                    ->whereNotIn('type', ['customer'])
                    ->orderBy('id', 'desc')
                    ->get();

                // dd($users);

                // foreach ($users as $user) {
                //     dd($user->departmentData->name);
                // }
                return view('user.index', compact('users'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        $departments  = Department::where('status', 1)->pluck('name', 'id');
        $designations = Designation::where('status', 1)->pluck('name', 'id');

        $userRoles = Role::where('parent_id', parentId())->whereNotIn('name', ['customer'])->get()->pluck('name', 'id');
        return view('user.create', compact('userRoles', 'departments', 'designations'));
    }

    public function filterEmployees(Request $request)
    {
        $query = MasterEmployee::where('status', 1);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        if ($request->filled('station_id')) {
            $query->whereHas('stations', function ($q) use ($request) {
                $q->where('stations.id', $request->station_id);
            });
        }

        return $query->get(['id', 'name', 'email', 'mobile']);
    }

    public function store(Request $request)
    {
        // dd($request);
        if (\Auth::user()->can('create user')) {
            if (\Auth::user()->type == 'super admin') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:6',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $user = new User();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = \Hash::make($request->password);
                $user->phone_number = $request->phone_number;
                $user->type = 'owner';
                $user->profile = 'avatar.png';
                $user->lang = 'english';
                $user->subscription = 1;
                $user->parent_id = parentId();
                $user->email_verified_at = now();
                $user->save();
                $userRole = Role::findByName('owner');
                $user->assignRole($userRole);
                defaultTemplate($user->id);
                defaultCustomerCreate($user->id);
                defaultEmployeeCreate($user->id);

                if (!empty($request->profile)) {
                    $tenantFilenameWithExt = $request->file('profile')->getClientOriginalName();
                    $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                    $tenantExtension = $request->file('profile')->getClientOriginalExtension();
                    $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                    $dir = storage_path('upload/profile');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('profile')->storeAs('upload/profile/', $tenantFileName);
                    $user->profile = $tenantFileName;
                    $user->save();
                }

                $module = 'owner_create';
                $setting = settings();
                $errorMessage = '';
                if (!empty($user)) {
                    $data['subject'] = 'New User Created';
                    $data['module'] = $module;
                    $data['password'] = $request->password;
                    $data['name'] = $request->name;
                    $data['email'] = $request->email;
                    $data['url'] = env('APP_URL');
                    $data['logo'] = $setting['company_logo'];
                    $to = $user->email;
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                return redirect()->route('users.index')->with('success', __('User successfully created.') . $errorMessage);
            } else {

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'email' => 'required|email|unique:users',
                        'password' => 'required|min:6',
                        'role' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $pricing_feature_settings = getSettingsValByIdName(1, 'pricing_feature');
                if ($pricing_feature_settings == 'on') {
                    $ids = parentId();
                    $authUser = User::find($ids);
                    $totalUser = $authUser->totalUser();
                    $subscription = Subscription::find($authUser->subscription);
                    if ($totalUser >= $subscription->user_limit && $subscription->user_limit != 0) {
                        return redirect()->back()->with('error', __('Your user limit is over, please upgrade your subscription.'));
                    }
                }

                $authUser = User::find($ids);
                $totalEmployee = $authUser->totalEmployee();
                $subscription = Subscription::find($authUser->subscription);
                if ($totalEmployee >= $subscription->employee_limit && $subscription->employee_limit != 0) {
                    return redirect()->back()->with('error', __('Your employee limit is over, please upgrade your subscription.'));
                }
                $userRole = Role::findById($request->role);
                $emp = MasterEmployee::find($request->employee_id);
                $user = new User();
                $user->name = $emp ? $emp->name : $request->employee_id;
                $user->email = $request->email;
                $user->phone_number = !empty($request->phone_number) ? $request->phone_number : ($emp ? $emp->mobile : null);
                $user->designation = !empty($request->designation_id) ? $request->designation_id : ($emp ? $emp->designation_id : null);
                $user->department_id = !empty($request->department_id) ? $request->department_id : ($emp ? $emp->department_id : null);
                $user->password = \Hash::make($request->password);
                $user->type = $userRole->name;
                $user->email_verified_at = now();
                $user->profile = 'avatar.png';
                $user->lang = 'english';
                $user->parent_id = parentId();

                // dd($user);
                $user->save();
                $user->assignRole($userRole);

                if (!empty($request->profile)) {
                    $tenantFilenameWithExt = $request->file('profile')->getClientOriginalName();
                    $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                    $tenantExtension = $request->file('profile')->getClientOriginalExtension();
                    $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                    $dir = storage_path('upload/profile');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('profile')->storeAs('upload/profile/', $tenantFileName);
                    $user->profile = $tenantFileName;
                    $user->save();
                }

                $module = 'user_create';
                $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
                $notification->password = $request->password;
                $setting = settings();
                $errorMessage = '';
                if (!empty($notification) && $notification->enabled_email == 1) {
                    $notification_responce = MessageReplace($notification, $user->id);
                    $data['subject'] = $notification_responce['subject'];
                    $data['message'] = $notification_responce['message'];
                    $data['module'] = $module;
                    $data['password'] = $request->password;
                    $data['logo'] = $setting['company_logo'];
                    $to = $user->email;

                    if (!empty($notification) && $notification->enabled_email == 1) {
                        $response = commonEmailSend($to, $data);
                        if ($response['status'] == 'error') {
                            $errorMessage = $response['message'];
                        }
                    }

                    if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                        $twilio_sid = getSettingsValByName('twilio_sid');
                        if (!empty($twilio_sid)) {
                            send_twilio_msg($request->phone_number, $notification_responce['sms_message']);
                        }
                    }
                }

                return redirect()->route('users.index')->with('success', __('User successfully created.') . '</br>' . $errorMessage);
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }


    public function show(User $user)
    {
        if (!\Auth::user()->can('show user')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        } else {
            $settings = settings();
            $transactions = PackageTransaction::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();
            $subscriptions = Subscription::get();
            return view('user.show', compact('user', 'transactions', 'settings', 'subscriptions'));
        }
    }

    public function edit($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        $userRoles = Role::where('parent_id', '=', parentId())->whereNotIn('name', ['customer'])->get()->pluck('name', 'id');
        $departments  = Department::where('status', 1)->pluck('name', 'id');
        $designations = Designation::where('status', 1)->pluck('name', 'id');

        return view('user.edit', compact('user', 'userRoles', 'departments', 'designations'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        if (\Auth::user()->can('edit user')) {
            if (\Auth::user()->type == 'super admin') {
                $user = User::findOrFail($id);

                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'email' => 'required|email|unique:users,email,' . $id,
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $userData = $request->all();
                $user->fill($userData)->save();

                if ($request->profile != '') {
                    $tenantFilenameWithExt = $request->file('profile')->getClientOriginalName();
                    $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                    $tenantExtension = $request->file('profile')->getClientOriginalExtension();
                    $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                    $dir = storage_path('upload/profile');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('profile')->storeAs('upload/profile/', $tenantFileName);
                    $user->profile = $tenantFileName;
                    $user->save();
                }

                return redirect()->route('users.index')->with('success', 'User successfully updated.');
            } else {


                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'email' => 'required|email|unique:users,email,' . $id,
                        'role' => 'required',
                        'is_active' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                // ✅ Handle is_active (IMPORTANT)
                $isActive = (int) $request->is_active;

                // dd($isActive);

                $userRole = Role::findById($request->role);
                $user = User::findOrFail($id);
                $emp = MasterEmployee::find($request->employee_id);
                $user->name = $emp ? $emp->name : $request->employee_id;
                $user->email = $request->email;
                $user->phone_number = !empty($request->phone_number) ? $request->phone_number : ($emp ? $emp->mobile : $user->phone_number);
                $user->type = $userRole->name;
                $user->is_active = $isActive;
                $user->designation = !empty($request->designation_id) ? $request->designation_id : ($emp ? $emp->designation_id : $user->designation);
                $user->department_id = !empty($request->department_id) ? $request->department_id : ($emp ? $emp->department_id : $user->department_id);

                // dd($user);
                $user->save();

                if ($request->profile != '') {
                    $tenantFilenameWithExt = $request->file('profile')->getClientOriginalName();
                    $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                    $tenantExtension = $request->file('profile')->getClientOriginalExtension();
                    $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                    $dir = storage_path('upload/profile');
                    if (!file_exists($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $request->file('profile')->storeAs('upload/profile/', $tenantFileName);
                    $user->profile = $tenantFileName;
                    $user->save();
                }

                $user->roles()->sync($userRole);
                return redirect()->route('users.index')->with('success', 'User successfully updated.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {

        if (\Auth::user()->can('delete user')) {
            $id = decrypt($id);
            $user = User::find($id);
            $user->delete();

            return redirect()->route('users.index')->with('success', __('User successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function loggedHistory()
    {
        $ids = parentId();
        $authUser = User::find($ids);
        $subscription = Subscription::find($authUser->subscription);

        if (\Auth::user()->can('manage logged history') && $subscription->enabled_logged_history == 1) {
            $histories = LoggedHistory::where('parent_id', parentId())->get();
            return view('logged_history.index', compact('histories'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function loggedHistoryShow($id)
    {
        if (\Auth::user()->can('manage logged history')) {
            $histories = LoggedHistory::find($id);
            return view('logged_history.show', compact('histories'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function loggedHistoryDestroy($id)
    {
        if (\Auth::user()->can('delete logged history')) {
            $histories = LoggedHistory::find($id);
            $histories->delete();
            return redirect()->back()->with('success', 'Logged history succefully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }
}
