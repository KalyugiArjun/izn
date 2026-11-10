<?php

use App\Http\Controllers\Admin\Authority\CreateComplainController;
use App\Http\Controllers\Admin\Authority\CreateRectifyController;
use App\Http\Controllers\Admin\Authority\OfficerInspectionNotesController;
use App\Http\Controllers\Admin\Authority\AwardNominationController;
use App\Http\Controllers\Admin\Authority\MyMovementController;
use App\Http\Controllers\Admin\Authority\AmbushCheckController;
use App\Http\Controllers\Admin\Authority\LoadStableClearanceController;
use App\Http\Controllers\Admin\Authority\StaffCounsellingController;
use App\Http\Controllers\Admin\Authority\PmeRefresherGradationController;
use App\Http\Controllers\Admin\StaffVisibility\StaffCounsellingOnlineController;
use App\Http\Controllers\Admin\StaffVisibility\StaffCounsellingOfflineController;
use App\Http\Controllers\Admin\StaffVisibility\AmbushCheckShuntingController;
use App\Http\Controllers\Admin\StaffVisibility\AmbushCheckBaTestController;
use App\Http\Controllers\Admin\StaffVisibility\MedicalController;
use App\Http\Controllers\Admin\StaffVisibility\RefresherController;
use App\Http\Controllers\Admin\StaffVisibility\GradationController;
use App\Http\Controllers\Admin\LoadStable\LoadStableNewEntryController;
use App\Http\Controllers\Admin\LoadStable\LoadStableForwardedToController;
use App\Http\Controllers\Admin\LoadStable\LoadStableFinalizByController;
use App\Http\Controllers\Admin\LoadStable\LoadClearanceForwardSmToController;
use App\Http\Controllers\Admin\LoadStable\LoadClearanceEntryByController;
use App\Http\Controllers\Admin\LoadStable\LoadClearanceForwardPointsmanToController;
use App\Http\Controllers\Admin\LoadStable\LoadClearanceFinalizByController;
use App\Http\Controllers\Admin\Master\AmenityCategoryController;
use App\Http\Controllers\Admin\Master\AttendBlockController;
use App\Http\Controllers\Admin\Master\DepartmentController;
use App\Http\Controllers\Admin\Master\DesignationController;
use App\Http\Controllers\Admin\Master\DivisionController;
use App\Http\Controllers\Admin\Master\LineNoController;
use App\Http\Controllers\Admin\Master\MasterEmployeeController;
use App\Http\Controllers\Admin\Master\MessageController;
use App\Http\Controllers\Admin\Master\SliderController;
use App\Http\Controllers\Admin\Master\StationController;
use App\Http\Controllers\Admin\Master\DiesellobbyController;
use App\Http\Controllers\Admin\Master\EOEmployeeController;
use App\Http\Controllers\Admin\Master\JointSignalSightingController;
use App\Http\Controllers\Admin\Master\LoadTypeController;
use App\Http\Controllers\Admin\Master\MovementController;
use App\Http\Controllers\Admin\Master\RunningRoomLocationController;
use App\Http\Controllers\Admin\Master\SafetyDriveController;
use App\Http\Controllers\Admin\Master\TypeOfAmbushController;
use App\Http\Controllers\Admin\Master\TypeOfInspectionController;
use App\Http\Controllers\Admin\Master\TypeOfLeaveController;
use App\Http\Controllers\Admin\Master\WorkingAreaController;
use App\Http\Controllers\Admin\Project\ProjectController;
use App\Http\Controllers\Admin\Project\ScopeOfWorkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\AuthPageController;
use App\Http\Controllers\EmployeeProfileController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseSubCategoryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\NoticeBoardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Models\User;


use App\Http\Controllers\Admin\Master\SafetyCircularController;
use App\Http\Controllers\Admin\Master\OperatingSafetyCircularController;
use App\Http\Controllers\Admin\Master\JpoController;
use App\Http\Controllers\Admin\Master\OfficerInspectionNoteController;
use App\Http\Controllers\Admin\Master\TrafficInspectorInspectionNoteController;

use App\Http\Controllers\Admin\Master\StationWorkingRuleOriginalController;
use App\Http\Controllers\Admin\Master\StationWorkingRuleCorrectionController;
use App\Http\Controllers\Admin\Master\GsrOriginalController;
use App\Http\Controllers\Admin\Master\GsrCorrectionController;
use App\Http\Controllers\Admin\Master\OperatingManualOriginalController;
use App\Http\Controllers\Admin\Master\OperatingManualCorrectionController;
use App\Http\Controllers\Admin\Master\AccidentManualOriginalController;
use App\Http\Controllers\Admin\Master\AccidentManualCorrectionController;
use App\Http\Controllers\Admin\Master\AreaOfProficiencyController;
use App\Http\Controllers\Admin\Master\BackgroundLoginController;
use App\Http\Controllers\Admin\Master\MiscellaneousLettersController;
use App\Http\Controllers\Admin\Master\OperatingSafetyAlertController;
use App\Http\Controllers\Admin\Master\OperatingSafetyEpisodeController;
use App\Http\Controllers\Admin\Master\OperatingSafetyDriveController;
use App\Http\Controllers\Admin\Master\CaseStudyController;
use App\Http\Controllers\Admin\Master\CoachingRakeTypeController;
use App\Http\Controllers\Admin\Master\EmergencyContactController;
use App\Http\Controllers\Admin\Master\GoodsRakeTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\MeasurementUnitController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ClothTypeController;
use App\Http\Controllers\DashboardPdfController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use GuzzleHttp\Promise\Create;
use App\Http\Controllers\Admin\Master\PopupController;

use App\Http\Controllers\Admin\Master\OperatingSafetyDocumentsController;
use App\Http\Controllers\Admin\Report\EmergencyContactReportController;
use App\Http\Controllers\Admin\Report\Ph16ReportController;
use App\Http\Controllers\Admin\Report\ComplaintsReportController;
use App\Http\Controllers\Admin\Report\AmbushCheckReportController;
use App\Http\Controllers\Admin\Report\MyMovementReportController;
use App\Http\Controllers\Admin\Report\StaffCounselingReportController;
use App\Http\Controllers\Admin\Report\AwardRecordReportController;
use App\Http\Controllers\Admin\Report\EmployeeSafetyRecordReportController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\ApprovalListingController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\EspSipController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\EstimateController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\ExecutionController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\ExecutionendController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\FinanceVettingSanctionController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\IdentifyNewWorkController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\InspectionLogController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\JointSurveyController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\ReviewEstimateController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\TenderingController;
use App\Http\Controllers\Admin\WorksPaperworkProgressRegister\VariationController;
use App\Http\Controllers\Admin\Authority\Ph16TiController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Artisan;

Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage link created!';
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});

require __DIR__ . '/auth.php';

Route::get('/', [HomeController::class, 'index'])->middleware(
    [

        'XSS',
    ]
);
Route::get('home', [HomeController::class, 'index'])->name('home')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware(
    [

        'XSS',
    ]
);

Route::get('check-ph16-flag', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Please login first.',
        ], 401);
    }

    $desigId = $user->getDesignationId();
    $deptId = $user->getDepartmentId();
    $isPlanning = $user->isPlanningWork();
    $isAuth = $user->isPh16Authorized();

    $sections = [];
    if ($isPlanning) {
        $sections = array_merge($sections, ['New Work', 'Paperwork Timeline', 'Variation']);
    }
    if ($isAuth) {
        $sections[] = 'Inspection Log';
    }

    return response()->json([
        'user_id' => $user->id,
        'user_name' => $user->name,
        'phone_number' => $user->phone_number,
        'type' => $user->type,
        'resolved_designation_id' => $desigId,
        'resolved_department_id' => $deptId,
        'is_planning_work' => $isPlanning,
        'is_ph16_authority' => $isAuth,
        'visible_sections' => $sections,
        'summary' => $isPlanning
            ? ($isAuth ? 'TI Planning Work + PH 16 Authority (All 4 Sections)' : 'TI Planning Work (3 Sections: New Work, Paperwork Timeline, Variation)')
            : ($isAuth ? 'PH 16 Authority / TI Safety (Only Inspection Log)' : 'Normal Employee (No PH 16 Sections)'),
    ]);
})->name('check-ph16-flag')->middleware(['auth', 'XSS']);
Route::get('charts-dashboard', [HomeController::class, 'chartDashboard'])->name('charts-dashboard')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard-by-department', [HomeController::class, 'departmentDashboard'])->name('dashboard-by-department')->middleware(
    [

        'XSS',
    ]
);

Route::get('department-dashboard-data-view/{department_id}/{status}', [HomeController::class, 'departmentDashboard_data_view'])->name('department.dashboard.data.view')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard-by-compliments', [HomeController::class, 'stationDashboard'])->name('dashboard-by-compliments')->middleware(
    [

        'XSS',
    ]
);
Route::get('station-dashboard-data-view/{station_id}/{status}', [HomeController::class, 'stationDashboard_data_view'])->name('station.dashboard.data.view')->middleware(
    [

        'XSS',
    ]
);
Route::get('dashboard-by-calendar', [HomeController::class, 'calendarDashboard'])->name('dashboard-by-calendar')->middleware(
    [

        'XSS',
    ]
);

Route::middleware(['XSS'])->group(function () {

    // Existing
    Route::get('all-complaints', [HomeController::class, 'allComplaints'])
        ->name('all-complaints');

    // View full complaint detail
    Route::get('complaints/{id}/view', [HomeController::class, 'viewComplaint'])
        ->name('complaints.view');

    // Show assign page
    Route::get('complaints/{id}/assign', [HomeController::class, 'assignComplaint'])
        ->name('complaints.assign');

    // Store the assignment
    Route::post('complaints/{id}/assign', [HomeController::class, 'storeAssignComplaint'])
        ->name('complaints.assign.store');

    // web.php
    Route::get('complaints/{id}/transfer', [HomeController::class, 'transferPage'])->name('complaints.transfer');
    Route::post('complaints/{id}/transfer', [HomeController::class, 'transfer'])->name('complaints.transfer.submit');
});

//-------------------------------User-------------------------------------------

Route::resource('users', UserController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::get('/employees/filter', [UserController::class, 'filterEmployees'])->name('employees.filter');

Route::get('login/otp', [OTPController::class, 'show'])->name('otp.show')->middleware(
    [

        'XSS',
    ]
);
Route::post('login/otp', [OTPController::class, 'check'])->name('otp.check')->middleware(
    [

        'XSS',
    ]
);
Route::get('login/2fa/disable', [OTPController::class, 'disable'])->name('2fa.disable')->middleware(['XSS',]);

//-------------------------------Subscription-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::resource('subscriptions', SubscriptionController::class);
        Route::get('coupons/history', [CouponController::class, 'history'])->name('coupons.history');
        Route::delete('coupons/history/{id}/destroy', [CouponController::class, 'historyDestroy'])->name('coupons.history.destroy');
        Route::get('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
        Route::resource('coupons', CouponController::class);
        Route::get('subscription/transaction', [SubscriptionController::class, 'transaction'])->name('subscription.transaction');
    }
);

//-------------------------------Subscription Payment-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::post('subscription/{id}/stripe/payment', [SubscriptionController::class, 'stripePayment'])->name('subscription.stripe.payment');
    }
);
//-------------------------------Settings-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('settings', [SettingController::class, 'index'])->name('setting.index');

        Route::post('settings/account', [SettingController::class, 'accountData'])->name('setting.account');
        Route::delete('settings/account/delete', [SettingController::class, 'accountDelete'])->name('setting.account.delete');
        Route::post('settings/password', [SettingController::class, 'passwordData'])->name('setting.password');
        Route::post('settings/general', [SettingController::class, 'generalData'])->name('setting.general');
        Route::post('settings/smtp', [SettingController::class, 'smtpData'])->name('setting.smtp');
        Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
        Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
        Route::post('settings/payment', [SettingController::class, 'paymentData'])->name('setting.payment');
        Route::post('settings/site-seo', [SettingController::class, 'siteSEOData'])->name('setting.site.seo');
        Route::post('settings/google-recaptcha', [SettingController::class, 'googleRecaptchaData'])->name('setting.google.recaptcha');
        Route::post('settings/company', [SettingController::class, 'companyData'])->name('setting.company');
        Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');

        Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
        Route::post('settings/footer', [SettingController::class, 'footerData'])->name('setting.footer');

        Route::get('language/{lang}', [SettingController::class, 'lanquageChange'])->name('language.change');
        Route::post('theme/settings', [SettingController::class, 'themeSettings'])->name('theme.settings');

        Route::post('settings/twilio', [SettingController::class, 'twilio'])->name('setting.twilio');
    }
);


//-------------------------------Role & Permissions-------------------------------------------
Route::resource('permission', PermissionController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

Route::resource('role', RoleController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Note-------------------------------------------
Route::resource('note', NoticeBoardController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Contact-------------------------------------------
Route::resource('contact', ContactController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------logged History-------------------------------------------

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('logged/history', [UserController::class, 'loggedHistory'])->name('logged.history');
        Route::get('logged/{id}/history/show', [UserController::class, 'loggedHistoryShow'])->name('logged.history.show');
        Route::delete('logged/{id}/history', [UserController::class, 'loggedHistoryDestroy'])->name('logged.history.destroy');
    }
);


//-------------------------------Plan Payment-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::post('subscription/{id}/bank-transfer', [PaymentController::class, 'subscriptionBankTransfer'])->name('subscription.bank.transfer');
        Route::get('subscription/{id}/bank-transfer/action/{status}', [PaymentController::class, 'subscriptionBankTransferAction'])->name('subscription.bank.transfer.action');
        Route::post('subscription/{id}/paypal', [PaymentController::class, 'subscriptionPaypal'])->name('subscription.paypal');
        Route::get('subscription/{id}/paypal/{status}', [PaymentController::class, 'subscriptionPaypalStatus'])->name('subscription.paypal.status');
        Route::post('subscription/{id}/{user_id}/manual-assign-package', [PaymentController::class, 'subscriptionManualAssignPackage'])->name('subscription.manual_assign_package');
        Route::get('subscription/flutterwave/{sid}/{tx_ref}', [PaymentController::class, 'subscriptionFlutterwave'])->name('subscription.flutterwave');
        Route::post('/subscription-pay-with-paystack', [PaymentController::class, 'subscriptionPayWithPaystack'])->name('subscription.pay.with.paystack')->middleware(['auth', 'XSS']);
        Route::get('/subscription/paystack/{pay_id}/{plan_id}', [PaymentController::class, 'getsubscriptionsPaymentStatus'])->name('subscription.paystack');
    }
);

//-------------------------------Notification-------------------------------------------
Route::resource('notification', NotificationController::class)->middleware(
    [
        'auth',
        'XSS',

    ]
);

Route::get('email-verification/{token}', [VerifyEmailController::class, 'verifyEmail'])->name('email-verification')->middleware(
    [
        'XSS',
    ]
);

//-------------------------------FAQ-------------------------------------------
Route::resource('FAQ', FAQController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Home Page-------------------------------------------
Route::resource('homepage', HomePageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------FAQ-------------------------------------------
Route::resource('pages', PageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Auth page-------------------------------------------
Route::resource('authPage', AuthPageController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Customer-------------------------------------------
Route::resource('customer', CustomerController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Expense-------------------------------------------
Route::resource('expense', ExpenseController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);

//-------------------------------Measurement Unit-------------------------------------------
Route::resource('measurement-unit', MeasurementUnitController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Tax-------------------------------------------
Route::resource('tax', TaxController::class)->middleware(
    [
        'auth',
        'XSS',
    ]
);
//-------------------------------Cloth Type-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::delete('cloth-type/measure/destroy', [ClothTypeController::class, 'clothTypeMeasureDestroy'])->name('cloth-type.measure.destroy');
        Route::resource('cloth-type', ClothTypeController::class);
    }
);

//-------------------------------Measurement-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('measurement/type', [MeasurementController::class, 'measurementType'])->name('measurement.type');
        Route::resource('measurement', MeasurementController::class);
    }
);
//-------------------------------Order-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('calendar', [OrderController::class, 'calendar'])->name('calendar');
        Route::get('order/today', [OrderController::class, 'todayOrder'])->name('order.today');
        Route::get('order/today/delivery', [OrderController::class, 'todayDelivery'])->name('order.today.delivery');
        Route::get('customers/measurement', [OrderController::class, 'customerMeasurement'])->name('customer.measurement');
        Route::resource('order', OrderController::class);
        Route::get('order-kanban', [OrderController::class, 'kanban'])->name('order.kanban');
        Route::post('order-status-update', [OrderController::class, 'statusUpdate'])->name('order.status.update');
    }
);

Route::post('/chart/save', [DashboardPdfController::class, 'saveChart'])->name('chart.save');
Route::get('/dashboard/export-pdf', [DashboardPdfController::class, 'exportPdf'])->name('dashboard.export.pdf');
//-------------------------------Invoice-------------------------------------------
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::get('invoice/{id}/item/create', [InvoiceController::class, 'invoiceItemCreate'])->name('invoice.item.create');
        Route::post('invoice/{id}/item/store', [InvoiceController::class, 'invoiceItemStore'])->name('invoice.item.store');
        Route::delete('invoice/{iid}/item/{id}/destroy', [InvoiceController::class, 'invoiceItemDestroy'])->name('invoice.item.destroy');
        Route::post('invoice/item/details', [InvoiceController::class, 'getItemDetails'])->name('get.invoice.item.details');

        Route::get('invoice/{id}/payment/create', [InvoiceController::class, 'invoicePaymentCreate'])->name('invoice.payment.create');
        Route::post('invoice/{id}/payment/store', [InvoiceController::class, 'invoicePaymentStore'])->name('invoice.payment.store');
        Route::delete('invoice/{id}/payment/{pid}/destroy', [InvoiceController::class, 'invoicePaymentDestroy'])->name('invoice.payment.destroy');
        Route::post('invoice/{id}/banktransfer/payment', [InvoiceController::class, 'banktransferPayment'])->name('invoice.banktransfer.payment');
        Route::get('invoice-payment-status/{id}/{status}', [InvoiceController::class, 'invoicePaymentStatus'])->name('invoice.bank.transfer.action');
        Route::post('invoice/{id}/stripe/payment', [InvoiceController::class, 'InvoiceStripePayment'])->name('invoice.stripe.payment');
        Route::post('invoice/{id}/paypal', [InvoiceController::class, 'invoicePaypal'])->name('invoice.paypal');
        Route::get('invoice/{id}/paypal/{status}', [InvoiceController::class, 'invoicePaypalStatus'])->name('invoice.paypal.status');
        Route::get('invoice/flutterwave/{id}/{tx_ref}', [InvoiceController::class, 'invoiceFlutterwave'])->name('invoice.flutterwave');
        Route::post('invoice/{id}/paystack/payment', [InvoiceController::class, 'invoicePaystack'])->name('invoice.paystack.payment');
        Route::get('/invoice/paystack/{pay_id}/{i_id}', [InvoiceController::class, 'invoicePaystackStatus'])->name('invoice.paystack');
        Route::get('orders/by-customer', [InvoiceController::class, 'byCustomer'])->name('orders.byCustomer');

        Route::resource('invoice', InvoiceController::class);
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('expense-category', ExpenseCategoryController::class);
        Route::get('subcategory-by-category', [ExpenseController::class, 'getSubcategory'])->name('subcategory.by.category');
        Route::resource('expense-sub-category', ExpenseSubCategoryController::class);
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::get('order-data', [ReportController::class, 'orderData'])->name('order.data');
        Route::post('generate-order-report', [ReportController::class, 'generateOrderReport'])->name('generate.order.report');
        Route::get('expense-data', [ReportController::class, 'expenseData'])->name('expense.data');
        Route::post('generate-expense-report', [ReportController::class, 'generateExpenseReport'])->name('generate.expense.report');
        Route::get('income-data', [ReportController::class, 'incomeData'])->name('income.data');
        Route::post('generate-income-report', [ReportController::class, 'generateIncomeReport'])->name('generate.income.report');
        Route::get('yearly-profit-loss', [ReportController::class, 'yearlyProfitLoss'])->name('yearly.profit.loss');
        Route::post('generate-profit-loss-report', [ReportController::class, 'generateProfitLossReport'])->name('generate.profit.loss.report');

        Route::get('master-employee-report', [ReportController::class, 'master_employee_report'])->name('report.master.employee');
        Route::post('master-employee-report-data', [ReportController::class, 'master_employee_report'])->name('report.master.employee.data');

        Route::get('employee-safety-record', [EmployeeSafetyRecordReportController::class, 'index'])->name('report.employee.safety');
        Route::get('employee-safety-record/{id}/gradation', [ReportController::class, 'employee_gradation'])->name('report.employee.safety.gradation');
        Route::post('employee-safety-record/{id}/gradation', [ReportController::class, 'employee_gradation_store'])->name('report.employee.safety.gradation.store');
        Route::get('employee-safety-record/gradation/{id}/edit', [ReportController::class, 'employee_gradation_edit'])->name('report.employee.safety.gradation.edit');
        Route::post('employee-safety-record/gradation/{id}/update', [ReportController::class, 'employee_gradation_update'])->name('report.employee.safety.gradation.update');
        Route::delete('employee-safety-record/gradation/{id}/delete', [ReportController::class, 'employee_gradation_delete'])->name('report.employee.safety.gradation.delete');

        Route::get('employee-safety-record/{id}/pme', [ReportController::class, 'employee_pme'])->name('report.employee.safety.pme');
        Route::post('employee-safety-record/{id}/pme', [ReportController::class, 'employee_pme_store'])->name('report.employee.safety.pme.store');
        Route::get('employee-safety-record/pme/{id}/edit', [ReportController::class, 'employee_pme_edit'])->name('report.employee.safety.pme.edit');
        Route::post('employee-safety-record/pme/{id}/update', [ReportController::class, 'employee_pme_update'])->name('report.employee.safety.pme.update');
        Route::delete('employee-safety-record/pme/{id}/delete', [ReportController::class, 'employee_pme_delete'])->name('report.employee.safety.pme.delete');

        Route::get('employee-safety-record/{id}/refresher', [ReportController::class, 'employee_refresher'])->name('report.employee.safety.refresher');
        Route::post('employee-safety-record/{id}/refresher', [ReportController::class, 'employee_refresher_store'])->name('report.employee.safety.refresher.store');
        Route::get('employee-safety-record/refresher/{id}/edit', [ReportController::class, 'employee_refresher_edit'])->name('report.employee.safety.refresher.edit');
        Route::post('employee-safety-record/refresher/{id}/update', [ReportController::class, 'employee_refresher_update'])->name('report.employee.safety.refresher.update');
        Route::delete('employee-safety-record/refresher/{id}/delete', [ReportController::class, 'employee_refresher_delete'])->name('report.employee.safety.refresher.delete');
    }
);

Route::get('page/{slug}', [PageController::class, 'page'])->name('page');

// Division
Route::get('master/division',              [DivisionController::class, 'index'])->name('master.division');
Route::get('master/division/create',       [DivisionController::class, 'create'])->name('master.division.create');
Route::post('master/division/store',       [DivisionController::class, 'store'])->name('master.division.store');
Route::get('master/division/edit/{id}',    [DivisionController::class, 'edit'])->name('master.division.edit');
Route::post('master/division/update/{id}', [DivisionController::class, 'update'])->name('master.division.update');
Route::delete('master/division/delete/{id}',  [DivisionController::class, 'destroy'])->name('master.division.destroy');

// Department
Route::get('master/department',              [DepartmentController::class, 'index'])->name('master.department');
Route::get('master/department/create',       [DepartmentController::class, 'create'])->name('master.department.create');
Route::post('master/department/store',       [DepartmentController::class, 'store'])->name('master.department.store');
Route::get('master/department/edit/{id}',    [DepartmentController::class, 'edit'])->name('master.department.edit');
Route::post('master/department/update/{id}', [DepartmentController::class, 'update'])->name('master.department.update');
Route::delete('master/department/delete/{id}',  [DepartmentController::class, 'destroy'])->name('master.department.destroy');

// Designation
Route::get('master/designation',              [DesignationController::class, 'index'])->name('master.designation');
Route::get('master/designation/create',       [DesignationController::class, 'create'])->name('master.designation.create');
Route::post('master/designation/store',       [DesignationController::class, 'store'])->name('master.designation.store');
Route::get('master/designation/edit/{id}',    [DesignationController::class, 'edit'])->name('master.designation.edit');
Route::post('master/designation/update/{id}', [DesignationController::class, 'update'])->name('master.designation.update');
Route::delete('master/designation/delete/{id}',  [DesignationController::class, 'destroy'])->name('master.designation.destroy');

// Station
Route::get('master/station',              [StationController::class, 'index'])->name('master.station');
Route::get('master/station/create',       [StationController::class, 'create'])->name('master.station.create');
Route::post('master/station/store',       [StationController::class, 'store'])->name('master.station.store');
Route::get('master/station/edit/{id}',    [StationController::class, 'edit'])->name('master.station.edit');
Route::post('master/station/update/{id}', [StationController::class, 'update'])->name('master.station.update');
Route::delete('master/station/delete/{id}',  [StationController::class, 'destroy'])->name('master.station.destroy');

// Diesel Lobby Location
Route::get('master/diesel_lobby',              [DiesellobbyController::class, 'index'])->name('master.diesel_lobby');
Route::get('master/diesel_lobby/create',       [DiesellobbyController::class, 'create'])->name('master.diesel_lobby.create');
Route::post('master/diesel_lobby/store',       [DiesellobbyController::class, 'store'])->name('master.diesel_lobby.store');
Route::get('master/diesel_lobby/edit/{id}',    [DiesellobbyController::class, 'edit'])->name('master.diesel_lobby.edit');
Route::post('master/diesel_lobby/update/{id}', [DiesellobbyController::class, 'update'])->name('master.diesel_lobby.update');
Route::delete('master/diesel_lobby/delete/{id}',  [DiesellobbyController::class, 'destroy'])->name('master.diesel_lobby.destroy');

// Running Room Location
Route::get('master/running_room_location',              [RunningRoomLocationController::class, 'index'])->name('master.running_room_location');
Route::get('master/running_room_location/create',       [RunningRoomLocationController::class, 'create'])->name('master.running_room_location.create');
Route::post('master/running_room_location/store',       [RunningRoomLocationController::class, 'store'])->name('master.running_room_location.store');
Route::get('master/running_room_location/edit/{id}',    [RunningRoomLocationController::class, 'edit'])->name('master.running_room_location.edit');
Route::post('master/running_room_location/update/{id}', [RunningRoomLocationController::class, 'update'])->name('master.running_room_location.update');
Route::delete('master/running_room_location/delete/{id}',  [RunningRoomLocationController::class, 'destroy'])->name('master.running_room_location.destroy');

// Movement
Route::get('master/movement',              [MovementController::class, 'index'])->name('master.movement');
Route::get('master/movement/create',       [MovementController::class, 'create'])->name('master.movement.create');
Route::post('master/movement/store',       [MovementController::class, 'store'])->name('master.movement.store');
Route::get('master/movement/edit/{id}',    [MovementController::class, 'edit'])->name('master.movement.edit');
Route::post('master/movement/update/{id}', [MovementController::class, 'update'])->name('master.movement.update');
Route::delete('master/movement/delete/{id}',  [MovementController::class, 'destroy'])->name('master.movement.destroy');

// Type of Inspection
Route::get('master/type_of_inspection',              [TypeOfInspectionController::class, 'index'])->name('master.type_of_inspection');
Route::get('master/type_of_inspection/create',       [TypeOfInspectionController::class, 'create'])->name('master.type_of_inspection.create');
Route::post('master/type_of_inspection/store',       [TypeOfInspectionController::class, 'store'])->name('master.type_of_inspection.store');
Route::get('master/type_of_inspection/edit/{id}',    [TypeOfInspectionController::class, 'edit'])->name('master.type_of_inspection.edit');
Route::post('master/type_of_inspection/update/{id}', [TypeOfInspectionController::class, 'update'])->name('master.type_of_inspection.update');
Route::delete('master/type_of_inspection/delete/{id}',  [TypeOfInspectionController::class, 'destroy'])->name('master.type_of_inspection.destroy');

// master.type_of_ambush

Route::get('master/type_of_ambush',              [TypeOfAmbushController::class, 'index'])->name('master.type_of_ambush');
Route::get('master/type_of_ambush/create',       [TypeOfAmbushController::class, 'create'])->name('master.type_of_ambush.create');
Route::post('master/type_of_ambush/store',       [TypeOfAmbushController::class, 'store'])->name('master.type_of_ambush.store');
Route::get('master/type_of_ambush/edit/{id}',    [TypeOfAmbushController::class, 'edit'])->name('master.type_of_ambush.edit');
Route::post('master/type_of_ambush/update/{id}', [TypeOfAmbushController::class, 'update'])->name('master.type_of_ambush.update');
Route::delete('master/type_of_ambush/delete/{id}',  [TypeOfAmbushController::class, 'destroy'])->name('master.type_of_ambush.destroy');

// Safety Drive
Route::get('master/safety_drive',              [SafetyDriveController::class, 'index'])->name('master.safety_drive');
Route::get('master/safety_drive/create',       [SafetyDriveController::class, 'create'])->name('master.safety_drive.create');
Route::post('master/safety_drive/store',       [SafetyDriveController::class, 'store'])->name('master.safety_drive.store');
Route::get('master/safety_drive/edit/{id}',    [SafetyDriveController::class, 'edit'])->name('master.safety_drive.edit');
Route::post('master/safety_drive/update/{id}', [SafetyDriveController::class, 'update'])->name('master.safety_drive.update');
Route::delete('master/safety_drive/delete/{id}',  [SafetyDriveController::class, 'destroy'])->name('master.safety_drive.destroy');

// Joint Signal Sighting
Route::get('master/joint_signal_sighting',              [JointSignalSightingController::class, 'index'])->name('master.joint_signal_sighting');
Route::get('master/joint_signal_sighting/create',       [JointSignalSightingController::class, 'create'])->name('master.joint_signal_sighting.create');
Route::post('master/joint_signal_sighting/store',       [JointSignalSightingController::class, 'store'])->name('master.joint_signal_sighting.store');
Route::get('master/joint_signal_sighting/edit/{id}',    [JointSignalSightingController::class, 'edit'])->name('master.joint_signal_sighting.edit');
Route::post('master/joint_signal_sighting/update/{id}', [JointSignalSightingController::class, 'update'])->name('master.joint_signal_sighting.update');
Route::delete('master/joint_signal_sighting/delete/{id}',  [JointSignalSightingController::class, 'destroy'])->name('master.joint_signal_sighting.destroy');

// Attend Block
Route::get('master/attend_block',              [AttendBlockController::class, 'index'])->name('master.attend_block');
Route::get('master/attend_block/create',       [AttendBlockController::class, 'create'])->name('master.attend_block.create');
Route::post('master/attend_block/store',       [AttendBlockController::class, 'store'])->name('master.attend_block.store');
Route::get('master/attend_block/edit/{id}',    [AttendBlockController::class, 'edit'])->name('master.attend_block.edit');
Route::post('master/attend_block/update/{id}', [AttendBlockController::class, 'update'])->name('master.attend_block.update');
Route::delete('master/attend_block/delete/{id}',  [AttendBlockController::class, 'destroy'])->name('master.attend_block.destroy');

// Type of Leave
Route::get('master/type_of_leave',              [TypeOfLeaveController::class, 'index'])->name('master.type_of_leave');
Route::get('master/type_of_leave/create',       [TypeOfLeaveController::class, 'create'])->name('master.type_of_leave.create');
Route::post('master/type_of_leave/store',       [TypeOfLeaveController::class, 'store'])->name('master.type_of_leave.store');
Route::get('master/type_of_leave/edit/{id}',    [TypeOfLeaveController::class, 'edit'])->name('master.type_of_leave.edit');
Route::post('master/type_of_leave/update/{id}', [TypeOfLeaveController::class, 'update'])->name('master.type_of_leave.update');
Route::delete('master/type_of_leave/delete/{id}',  [TypeOfLeaveController::class, 'destroy'])->name('master.type_of_leave.destroy');

// Load Type
Route::get('master/load_type',              [LoadTypeController::class, 'index'])->name('master.load_type');
Route::get('master/load_type/create',       [LoadTypeController::class, 'create'])->name('master.load_type.create');
Route::post('master/load_type/store',       [LoadTypeController::class, 'store'])->name('master.load_type.store');
Route::get('master/load_type/edit/{id}',    [LoadTypeController::class, 'edit'])->name('master.load_type.edit');
Route::post('master/load_type/update/{id}', [LoadTypeController::class, 'update'])->name('master.load_type.update');
Route::delete('master/load_type/delete/{id}',  [LoadTypeController::class, 'destroy'])->name('master.load_type.destroy');


Route::get('master/line-no',              [LineNoController::class, 'index'])->name('master.line-no');
Route::get('master/line-no/create',       [LineNoController::class, 'create'])->name('master.line-no.create');
Route::post('master/line-no/store',       [LineNoController::class, 'store'])->name('master.line-no.store');
Route::get('master/line-no/edit/{id}',    [LineNoController::class, 'edit'])->name('master.line-no.edit');
Route::post('master/line-no/update/{id}', [LineNoController::class, 'update'])->name('master.line-no.update');
Route::delete('master/line-no/delete/{id}',  [LineNoController::class, 'destroy'])->name('master.line-no.destroy');

Route::get('master/amenity-category',              [AmenityCategoryController::class, 'index'])->name('master.amenity-category');
Route::get('master/amenity-category/create',       [AmenityCategoryController::class, 'create'])->name('master.amenity-category.create');
Route::post('master/amenity-category/store',       [AmenityCategoryController::class, 'store'])->name('master.amenity-category.store');
Route::get('master/amenity-category/edit/{id}',    [AmenityCategoryController::class, 'edit'])->name('master.amenity-category.edit');
Route::post('master/amenity-category/update/{id}', [AmenityCategoryController::class, 'update'])->name('master.amenity-category.update');
Route::delete('master/amenity-category/delete/{id}',  [AmenityCategoryController::class, 'destroy'])->name('master.amenity-category.destroy');

Route::get('master/working-area',              [WorkingAreaController::class, 'index'])->name('master.working-area');
Route::get('master/working-area/create',       [WorkingAreaController::class, 'create'])->name('master.working-area.create');
Route::post('master/working-area/store',       [WorkingAreaController::class, 'store'])->name('master.working-area.store');
Route::get('master/working-area/edit/{id}',    [WorkingAreaController::class, 'edit'])->name('master.working-area.edit');
Route::post('master/working-area/update/{id}', [WorkingAreaController::class, 'update'])->name('master.working-area.update');
Route::delete('master/working-area/delete/{id}',  [WorkingAreaController::class, 'destroy'])->name('master.working-area.destroy');


// Coaching Rake Type
Route::get('master/coaching-rake-type', [CoachingRakeTypeController::class, 'index'])->name('coaching-rake-type.index');
Route::get('master/coaching-rake-type/create', [CoachingRakeTypeController::class, 'create'])->name('coaching-rake-type.create');
Route::post('master/coaching-rake-type', [CoachingRakeTypeController::class, 'store'])->name('coaching-rake-type.store');
Route::get('master/coaching-rake-type/{id}/edit', [CoachingRakeTypeController::class, 'edit'])->name('coaching-rake-type.edit');
Route::post('master/coaching-rake-type/{id}', [CoachingRakeTypeController::class, 'update'])->name('coaching-rake-type.update');
Route::delete('master/coaching-rake-type/{id}', [CoachingRakeTypeController::class, 'destroy'])->name('coaching-rake-type.destroy');

// Goods Rake Type
Route::get('master/goods-rake-type', [GoodsRakeTypeController::class, 'index'])->name('goods-rake-type.index');
Route::get('master/goods-rake-type/create', [GoodsRakeTypeController::class, 'create'])->name('goods-rake-type.create');
Route::post('master/goods-rake-type', [GoodsRakeTypeController::class, 'store'])->name('goods-rake-type.store');
Route::get('master/goods-rake-type/{id}/edit', [GoodsRakeTypeController::class, 'edit'])->name('goods-rake-type.edit');
Route::post('master/goods-rake-type/{id}', [GoodsRakeTypeController::class, 'update'])->name('goods-rake-type.update');
Route::delete('master/goods-rake-type/{id}', [GoodsRakeTypeController::class, 'destroy'])->name('goods-rake-type.destroy');


// Area of Proficiency
Route::get('master/area-of-proficiency', [AreaOfProficiencyController::class, 'index'])->name('area-of-proficiency.index');
Route::get('master/area-of-proficiency/create', [AreaOfProficiencyController::class, 'create'])->name('area-of-proficiency.create');
Route::post('master/area-of-proficiency', [AreaOfProficiencyController::class, 'store'])->name('area-of-proficiency.store');
Route::get('master/area-of-proficiency/{id}/edit', [AreaOfProficiencyController::class, 'edit'])->name('area-of-proficiency.edit');
Route::post('master/area-of-proficiency/{id}', [AreaOfProficiencyController::class, 'update'])->name('area-of-proficiency.update');
Route::delete('master/area-of-proficiency/{id}', [AreaOfProficiencyController::class, 'destroy'])->name('area-of-proficiency.destroy');


// emergency-contact
Route::get('emergency-contact', [EmergencyContactController::class, 'index'])->name('emergency-contact.index');
Route::get('emergency-contact/create', [EmergencyContactController::class, 'create'])->name('emergency-contact.create');
Route::post('emergency-contact', [EmergencyContactController::class, 'store'])->name('emergency-contact.store');
Route::get('emergency-contact/{id}/edit', [EmergencyContactController::class, 'edit'])->name('emergency-contact.edit');
Route::post('emergency-contact/{id}', [EmergencyContactController::class, 'update'])->name('emergency-contact.update');
Route::delete('emergency-contact/{id}', [EmergencyContactController::class, 'destroy'])->name('emergency-contact.destroy');


// employee-profile
Route::get('profile', [EmployeeProfileController::class, 'index'])->name('employee-profile.index');
Route::get('profile/emergency-contacts', [EmployeeProfileController::class, 'emergencyContacts'])->name('employee-profile.emergency-contacts');
Route::get('profile/{id}/edit/{section}', [EmployeeProfileController::class, 'edit'])->name('employee-profile.edit');
Route::post('profile/{id}/{section}', [EmployeeProfileController::class, 'update'])->name('employee-profile.update');
Route::get('profile/emergency-contact/create', [EmployeeProfileController::class, 'emergencyContactCreate'])->name('employee-profile.emergency-contact.create');
Route::post('profile/emergency-contact', [EmployeeProfileController::class, 'emergencyContactStore'])->name('employee-profile.emergency-contact.store');











Route::get('authority/create-complain',  [CreateComplainController::class, 'index'])->name('authority.create.complain');
// Complaint routes
Route::prefix('admin/authority')->name('authority.')->middleware(['auth', 'XSS'])->group(function () {
    // PH16 TI routes
Route::get('ph16-ti', [Ph16TiController::class, 'index'])
    ->name('ph16-ti.index');

Route::get('ph16-ti/create', [Ph16TiController::class, 'create'])
    ->name('ph16-ti.create');

Route::post('ph16-ti', [Ph16TiController::class, 'store'])
    ->name('ph16-ti.store');

Route::get('ph16-ti/{id}/edit', [Ph16TiController::class, 'edit'])
    ->name('ph16-ti.edit');

Route::post('ph16-ti/{id}', [Ph16TiController::class, 'update'])
    ->name('ph16-ti.update');

Route::delete('ph16-ti/{id}/destroy', [Ph16TiController::class, 'destroy'])
    ->name('ph16-ti.destroy');

Route::get('ph16-ti/designations', [Ph16TiController::class, 'getDesignationsByDepartment'])
    ->name('ph16-ti.designations');

    // Officer Inspection Notes Authority routes
    Route::get('officer-inspection-notes', [OfficerInspectionNotesController::class, 'index'])
        ->name('officer-inspection-notes.index');
    Route::get('officer-inspection-notes/create', [OfficerInspectionNotesController::class, 'create'])
        ->name('officer-inspection-notes.create');
    Route::post('officer-inspection-notes', [OfficerInspectionNotesController::class, 'store'])
        ->name('officer-inspection-notes.store');
    Route::get('officer-inspection-notes/{id}/edit', [OfficerInspectionNotesController::class, 'edit'])
        ->name('officer-inspection-notes.edit');
    Route::post('officer-inspection-notes/{id}', [OfficerInspectionNotesController::class, 'update'])
        ->name('officer-inspection-notes.update');
    Route::delete('officer-inspection-notes/{id}/destroy', [OfficerInspectionNotesController::class, 'destroy'])
        ->name('officer-inspection-notes.destroy');
    Route::get('officer-inspection-notes/designations', [OfficerInspectionNotesController::class, 'getDesignationsByDepartment'])
        ->name('officer-inspection-notes.designations');

    // Award Nomination Authority routes
    Route::get('award-nomination', [AwardNominationController::class, 'index'])
        ->name('award-nomination.index');
    Route::get('award-nomination/create', [AwardNominationController::class, 'create'])
        ->name('award-nomination.create');
    Route::post('award-nomination', [AwardNominationController::class, 'store'])
        ->name('award-nomination.store');
    Route::get('award-nomination/{id}/edit', [AwardNominationController::class, 'edit'])
        ->name('award-nomination.edit');
    Route::post('award-nomination/{id}', [AwardNominationController::class, 'update'])
        ->name('award-nomination.update');
    Route::delete('award-nomination/{id}/destroy', [AwardNominationController::class, 'destroy'])
        ->name('award-nomination.destroy');
    Route::get('award-nomination/designations', [AwardNominationController::class, 'getDesignationsByDepartment'])
        ->name('award-nomination.designations');
    Route::get('award-nomination/employees', [AwardNominationController::class, 'getEmployeesByDepartment'])
        ->name('award-nomination.employees');

    // Self / Award Nomination creation, details, action and certificate routes
    Route::get('award-nomination/nominate/create', [AwardNominationController::class, 'createNomination'])
        ->name('award-nomination.nominate.create');
    Route::post('award-nomination/nominate/store', [AwardNominationController::class, 'storeNomination'])
        ->name('award-nomination.nominate.store');
    Route::get('award-nomination/nominate/{id}/show', [AwardNominationController::class, 'showNomination'])
        ->name('award-nomination.nominate.show');
    Route::get('award-nomination/nominate/{id}/action', [AwardNominationController::class, 'actionNomination'])
        ->name('award-nomination.nominate.action');
    Route::post('award-nomination/nominate/{id}/action', [AwardNominationController::class, 'updateNominationAction'])
        ->name('award-nomination.nominate.update-action');
    Route::delete('award-nomination/nominate/{id}/destroy', [AwardNominationController::class, 'destroyNomination'])
        ->name('award-nomination.nominate.destroy');
    Route::get('award-nomination/nominate/{id}/certificate', [AwardNominationController::class, 'certificate'])
        ->name('award-nomination.nominate.certificate');
    Route::get('award-nomination/employee-details/{id}', [AwardNominationController::class, 'getEmployeeDetails'])
        ->name('award-nomination.employee-details');

    // My Movement Authority routes
    Route::get('my-movement', [MyMovementController::class, 'index'])
        ->name('my-movement.index');
    Route::get('my-movement/create', [MyMovementController::class, 'create'])
        ->name('my-movement.create');
    Route::post('my-movement', [MyMovementController::class, 'store'])
        ->name('my-movement.store');
    Route::get('my-movement/{id}/edit', [MyMovementController::class, 'edit'])
        ->name('my-movement.edit');
    Route::post('my-movement/{id}', [MyMovementController::class, 'update'])
        ->name('my-movement.update');
    Route::delete('my-movement/{id}/destroy', [MyMovementController::class, 'destroy'])
        ->name('my-movement.destroy');
    Route::get('my-movement/designations', [MyMovementController::class, 'getDesignationsByDepartment'])
        ->name('my-movement.designations');

    // Ambush Check Authority routes
    Route::get('ambush-check', [AmbushCheckController::class, 'index'])
        ->name('ambush-check.index');
    Route::get('ambush-check/create', [AmbushCheckController::class, 'create'])
        ->name('ambush-check.create');
    Route::post('ambush-check', [AmbushCheckController::class, 'store'])
        ->name('ambush-check.store');
    Route::get('ambush-check/{id}/edit', [AmbushCheckController::class, 'edit'])
        ->name('ambush-check.edit');
    Route::post('ambush-check/{id}', [AmbushCheckController::class, 'update'])
        ->name('ambush-check.update');
    Route::delete('ambush-check/{id}/destroy', [AmbushCheckController::class, 'destroy'])
        ->name('ambush-check.destroy');
    Route::get('ambush-check/designations', [AmbushCheckController::class, 'getDesignationsByDepartment'])
        ->name('ambush-check.designations');

    // Load Stable & Clearance Authority routes
    Route::get('load-stable-clearance', [LoadStableClearanceController::class, 'index'])
        ->name('load-stable-clearance.index');
    Route::get('load-stable-clearance/create', [LoadStableClearanceController::class, 'create'])
        ->name('load-stable-clearance.create');
    Route::post('load-stable-clearance', [LoadStableClearanceController::class, 'store'])
        ->name('load-stable-clearance.store');
    Route::get('load-stable-clearance/{id}/edit', [LoadStableClearanceController::class, 'edit'])
        ->name('load-stable-clearance.edit');
    Route::post('load-stable-clearance/{id}', [LoadStableClearanceController::class, 'update'])
        ->name('load-stable-clearance.update');
    Route::delete('load-stable-clearance/{id}/destroy', [LoadStableClearanceController::class, 'destroy'])
        ->name('load-stable-clearance.destroy');
    Route::get('load-stable-clearance/designations', [LoadStableClearanceController::class, 'getDesignationsByDepartment'])
        ->name('load-stable-clearance.designations');

    // Staff Counselling Authority routes
    Route::get('staff-counselling', [StaffCounsellingController::class, 'index'])
        ->name('staff-counselling.index');
    Route::get('staff-counselling/create', [StaffCounsellingController::class, 'create'])
        ->name('staff-counselling.create');
    Route::post('staff-counselling', [StaffCounsellingController::class, 'store'])
        ->name('staff-counselling.store');
    Route::get('staff-counselling/{id}/edit', [StaffCounsellingController::class, 'edit'])
        ->name('staff-counselling.edit');
    Route::post('staff-counselling/{id}', [StaffCounsellingController::class, 'update'])
        ->name('staff-counselling.update');
    Route::delete('staff-counselling/{id}/destroy', [StaffCounsellingController::class, 'destroy'])
        ->name('staff-counselling.destroy');
    Route::get('staff-counselling/designations', [StaffCounsellingController::class, 'getDesignationsByDepartment'])
        ->name('staff-counselling.designations');

    // PME / REFRESHER / GRADATION Authority routes
    Route::get('pme-refresher-gradation', [PmeRefresherGradationController::class, 'index'])
        ->name('pme-refresher-gradation.index');
    Route::get('pme-refresher-gradation/create', [PmeRefresherGradationController::class, 'create'])
        ->name('pme-refresher-gradation.create');
    Route::post('pme-refresher-gradation', [PmeRefresherGradationController::class, 'store'])
        ->name('pme-refresher-gradation.store');
    Route::get('pme-refresher-gradation/{id}/edit', [PmeRefresherGradationController::class, 'edit'])
        ->name('pme-refresher-gradation.edit');
    Route::post('pme-refresher-gradation/{id}', [PmeRefresherGradationController::class, 'update'])
        ->name('pme-refresher-gradation.update');
    Route::delete('pme-refresher-gradation/{id}/destroy', [PmeRefresherGradationController::class, 'destroy'])
        ->name('pme-refresher-gradation.destroy');
    Route::get('pme-refresher-gradation/designations', [PmeRefresherGradationController::class, 'getDesignationsByDepartment'])
        ->name('pme-refresher-gradation.designations');
    

    // ── Complaint routes (existing) ──────────────────────────────────────
    Route::get('complain',              [CreateComplainController::class, 'index'])->name('complain.index');
    Route::get('complain/create',       [CreateComplainController::class, 'create'])->name('complain.create');
    Route::post('complain',             [CreateComplainController::class, 'store'])->name('complain.store');
    Route::get('complain/{id}/edit',    [CreateComplainController::class, 'edit'])->name('complain.edit');
    Route::post('complain/{id}',        [CreateComplainController::class, 'update'])->name('complain.update');
    Route::delete('complain/{id}/destroy', [CreateComplainController::class, 'destroy'])->name('complain.destroy');
    Route::get('complain/designations', [CreateComplainController::class, 'getDesignationsByDepartment'])->name('complain.designations');

    // ── Rectify routes (new) ─────────────────────────────────────────────
    Route::get('rectify',              [CreateRectifyController::class, 'index'])->name('rectify.index');
    Route::get('rectify/create',       [CreateRectifyController::class, 'create'])->name('rectify.create');
    Route::post('rectify',             [CreateRectifyController::class, 'store'])->name('rectify.store');
    Route::get('rectify/{id}/edit',    [CreateRectifyController::class, 'edit'])->name('rectify.edit');
    Route::post('rectify/{id}',        [CreateRectifyController::class, 'update'])->name('rectify.update');
    Route::delete('rectify/{id}/destroy', [CreateRectifyController::class, 'destroy'])->name('rectify.destroy');
    Route::get('rectify/designations', [CreateRectifyController::class, 'getDesignationsByDepartment'])->name('rectify.designations');
});

// ── Staff Visibility routes ──────────────────────────────────────────
Route::prefix('admin/staff-visibility')->name('staff-visibility.')->middleware(['auth', 'XSS'])->group(function () {
    // 1. Staff Counseling (Online Station)
    Route::get('counselling-online', [StaffCounsellingOnlineController::class, 'index'])->name('counselling-online.index');
    Route::get('counselling-online/create', [StaffCounsellingOnlineController::class, 'create'])->name('counselling-online.create');
    Route::post('counselling-online', [StaffCounsellingOnlineController::class, 'store'])->name('counselling-online.store');
    Route::get('counselling-online/{id}/edit', [StaffCounsellingOnlineController::class, 'edit'])->name('counselling-online.edit');
    Route::post('counselling-online/{id}', [StaffCounsellingOnlineController::class, 'update'])->name('counselling-online.update');
    Route::delete('counselling-online/{id}/destroy', [StaffCounsellingOnlineController::class, 'destroy'])->name('counselling-online.destroy');
    Route::get('counselling-online/designations', [StaffCounsellingOnlineController::class, 'getDesignationsByDepartment'])->name('counselling-online.designations');

    // 2. Staff Counseling (Offline Station)
    Route::get('counselling-offline', [StaffCounsellingOfflineController::class, 'index'])->name('counselling-offline.index');
    Route::get('counselling-offline/create', [StaffCounsellingOfflineController::class, 'create'])->name('counselling-offline.create');
    Route::post('counselling-offline', [StaffCounsellingOfflineController::class, 'store'])->name('counselling-offline.store');
    Route::get('counselling-offline/{id}/edit', [StaffCounsellingOfflineController::class, 'edit'])->name('counselling-offline.edit');
    Route::post('counselling-offline/{id}', [StaffCounsellingOfflineController::class, 'update'])->name('counselling-offline.update');
    Route::delete('counselling-offline/{id}/destroy', [StaffCounsellingOfflineController::class, 'destroy'])->name('counselling-offline.destroy');
    Route::get('counselling-offline/designations', [StaffCounsellingOfflineController::class, 'getDesignationsByDepartment'])->name('counselling-offline.designations');

    // 3. Ambush Check (Shunting)
    Route::get('ambush-shunting', [AmbushCheckShuntingController::class, 'index'])->name('ambush-shunting.index');
    Route::get('ambush-shunting/create', [AmbushCheckShuntingController::class, 'create'])->name('ambush-shunting.create');
    Route::post('ambush-shunting', [AmbushCheckShuntingController::class, 'store'])->name('ambush-shunting.store');
    Route::get('ambush-shunting/{id}/edit', [AmbushCheckShuntingController::class, 'edit'])->name('ambush-shunting.edit');
    Route::post('ambush-shunting/{id}', [AmbushCheckShuntingController::class, 'update'])->name('ambush-shunting.update');
    Route::delete('ambush-shunting/{id}/destroy', [AmbushCheckShuntingController::class, 'destroy'])->name('ambush-shunting.destroy');
    Route::get('ambush-shunting/designations', [AmbushCheckShuntingController::class, 'getDesignationsByDepartment'])->name('ambush-shunting.designations');

    // 4. Ambush Check (BA Test)
    Route::get('ambush-ba-test', [AmbushCheckBaTestController::class, 'index'])->name('ambush-ba-test.index');
    Route::get('ambush-ba-test/create', [AmbushCheckBaTestController::class, 'create'])->name('ambush-ba-test.create');
    Route::post('ambush-ba-test', [AmbushCheckBaTestController::class, 'store'])->name('ambush-ba-test.store');
    Route::get('ambush-ba-test/{id}/edit', [AmbushCheckBaTestController::class, 'edit'])->name('ambush-ba-test.edit');
    Route::post('ambush-ba-test/{id}', [AmbushCheckBaTestController::class, 'update'])->name('ambush-ba-test.update');
    Route::delete('ambush-ba-test/{id}/destroy', [AmbushCheckBaTestController::class, 'destroy'])->name('ambush-ba-test.destroy');
    Route::get('ambush-ba-test/designations', [AmbushCheckBaTestController::class, 'getDesignationsByDepartment'])->name('ambush-ba-test.designations');

    // 5. Medical
    Route::get('medical', [MedicalController::class, 'index'])->name('medical.index');
    Route::get('medical/create', [MedicalController::class, 'create'])->name('medical.create');
    Route::post('medical', [MedicalController::class, 'store'])->name('medical.store');
    Route::get('medical/{id}/edit', [MedicalController::class, 'edit'])->name('medical.edit');
    Route::post('medical/{id}', [MedicalController::class, 'update'])->name('medical.update');
    Route::delete('medical/{id}/destroy', [MedicalController::class, 'destroy'])->name('medical.destroy');
    Route::get('medical/designations', [MedicalController::class, 'getDesignationsByDepartment'])->name('medical.designations');

    // 6. Refresher
    Route::get('refresher', [RefresherController::class, 'index'])->name('refresher.index');
    Route::get('refresher/create', [RefresherController::class, 'create'])->name('refresher.create');
    Route::post('refresher', [RefresherController::class, 'store'])->name('refresher.store');
    Route::get('refresher/{id}/edit', [RefresherController::class, 'edit'])->name('refresher.edit');
    Route::post('refresher/{id}', [RefresherController::class, 'update'])->name('refresher.update');
    Route::delete('refresher/{id}/destroy', [RefresherController::class, 'destroy'])->name('refresher.destroy');
    Route::get('refresher/designations', [RefresherController::class, 'getDesignationsByDepartment'])->name('refresher.designations');

    // 7. Gradation
    Route::get('gradation', [GradationController::class, 'index'])->name('gradation.index');
    Route::get('gradation/create', [GradationController::class, 'create'])->name('gradation.create');
    Route::post('gradation', [GradationController::class, 'store'])->name('gradation.store');
    Route::get('gradation/{id}/edit', [GradationController::class, 'edit'])->name('gradation.edit');
    Route::post('gradation/{id}', [GradationController::class, 'update'])->name('gradation.update');
    Route::delete('gradation/{id}/destroy', [GradationController::class, 'destroy'])->name('gradation.destroy');
    Route::get('gradation/designations', [GradationController::class, 'getDesignationsByDepartment'])->name('gradation.designations');
});

// ── Load Stable routes ────────────────────────────────────────────────
Route::prefix('admin/load-stable')->name('load-stable.')->middleware(['auth', 'XSS'])->group(function () {
    // 1. New Entry
    Route::get('new-entry', [LoadStableNewEntryController::class, 'index'])->name('new-entry.index');
    Route::get('new-entry/create', [LoadStableNewEntryController::class, 'create'])->name('new-entry.create');
    Route::post('new-entry', [LoadStableNewEntryController::class, 'store'])->name('new-entry.store');
    Route::get('new-entry/{id}/edit', [LoadStableNewEntryController::class, 'edit'])->name('new-entry.edit');
    Route::post('new-entry/{id}', [LoadStableNewEntryController::class, 'update'])->name('new-entry.update');
    Route::delete('new-entry/{id}/destroy', [LoadStableNewEntryController::class, 'destroy'])->name('new-entry.destroy');
    Route::get('new-entry/designations', [LoadStableNewEntryController::class, 'getDesignationsByDepartment'])->name('new-entry.designations');

    // 2. New Entry Forwarded To
    Route::get('forwarded-to', [LoadStableForwardedToController::class, 'index'])->name('forwarded-to.index');
    Route::get('forwarded-to/create', [LoadStableForwardedToController::class, 'create'])->name('forwarded-to.create');
    Route::post('forwarded-to', [LoadStableForwardedToController::class, 'store'])->name('forwarded-to.store');
    Route::get('forwarded-to/{id}/edit', [LoadStableForwardedToController::class, 'edit'])->name('forwarded-to.edit');
    Route::post('forwarded-to/{id}', [LoadStableForwardedToController::class, 'update'])->name('forwarded-to.update');
    Route::delete('forwarded-to/{id}/destroy', [LoadStableForwardedToController::class, 'destroy'])->name('forwarded-to.destroy');
    Route::get('forwarded-to/designations', [LoadStableForwardedToController::class, 'getDesignationsByDepartment'])->name('forwarded-to.designations');

    // 3. New Entry Finaliz By
    Route::get('finaliz-by', [LoadStableFinalizByController::class, 'index'])->name('finaliz-by.index');
    Route::get('finaliz-by/create', [LoadStableFinalizByController::class, 'create'])->name('finaliz-by.create');
    Route::post('finaliz-by', [LoadStableFinalizByController::class, 'store'])->name('finaliz-by.store');
    Route::get('finaliz-by/{id}/edit', [LoadStableFinalizByController::class, 'edit'])->name('finaliz-by.edit');
    Route::post('finaliz-by/{id}', [LoadStableFinalizByController::class, 'update'])->name('finaliz-by.update');
    Route::delete('finaliz-by/{id}/destroy', [LoadStableFinalizByController::class, 'destroy'])->name('finaliz-by.destroy');
    Route::get('finaliz-by/designations', [LoadStableFinalizByController::class, 'getDesignationsByDepartment'])->name('finaliz-by.designations');

    // 4. Load Clearance Forward By SM To
    Route::get('clearance-forward-sm', [LoadClearanceForwardSmToController::class, 'index'])->name('clearance-forward-sm.index');
    Route::get('clearance-forward-sm/create', [LoadClearanceForwardSmToController::class, 'create'])->name('clearance-forward-sm.create');
    Route::post('clearance-forward-sm', [LoadClearanceForwardSmToController::class, 'store'])->name('clearance-forward-sm.store');
    Route::get('clearance-forward-sm/{id}/edit', [LoadClearanceForwardSmToController::class, 'edit'])->name('clearance-forward-sm.edit');
    Route::post('clearance-forward-sm/{id}', [LoadClearanceForwardSmToController::class, 'update'])->name('clearance-forward-sm.update');
    Route::delete('clearance-forward-sm/{id}/destroy', [LoadClearanceForwardSmToController::class, 'destroy'])->name('clearance-forward-sm.destroy');
    Route::get('clearance-forward-sm/designations', [LoadClearanceForwardSmToController::class, 'getDesignationsByDepartment'])->name('clearance-forward-sm.designations');

    // 5. Load Clearance Entry By
    Route::get('clearance-entry-by', [LoadClearanceEntryByController::class, 'index'])->name('clearance-entry-by.index');
    Route::get('clearance-entry-by/create', [LoadClearanceEntryByController::class, 'create'])->name('clearance-entry-by.create');
    Route::post('clearance-entry-by', [LoadClearanceEntryByController::class, 'store'])->name('clearance-entry-by.store');
    Route::get('clearance-entry-by/{id}/edit', [LoadClearanceEntryByController::class, 'edit'])->name('clearance-entry-by.edit');
    Route::post('clearance-entry-by/{id}', [LoadClearanceEntryByController::class, 'update'])->name('clearance-entry-by.update');
    Route::delete('clearance-entry-by/{id}/destroy', [LoadClearanceEntryByController::class, 'destroy'])->name('clearance-entry-by.destroy');
    Route::get('clearance-entry-by/designations', [LoadClearanceEntryByController::class, 'getDesignationsByDepartment'])->name('clearance-entry-by.designations');

    // 6. Load Clearance Forward By Pointsman To
    Route::get('clearance-forward-pointsman', [LoadClearanceForwardPointsmanToController::class, 'index'])->name('clearance-forward-pointsman.index');
    Route::get('clearance-forward-pointsman/create', [LoadClearanceForwardPointsmanToController::class, 'create'])->name('clearance-forward-pointsman.create');
    Route::post('clearance-forward-pointsman', [LoadClearanceForwardPointsmanToController::class, 'store'])->name('clearance-forward-pointsman.store');
    Route::get('clearance-forward-pointsman/{id}/edit', [LoadClearanceForwardPointsmanToController::class, 'edit'])->name('clearance-forward-pointsman.edit');
    Route::post('clearance-forward-pointsman/{id}', [LoadClearanceForwardPointsmanToController::class, 'update'])->name('clearance-forward-pointsman.update');
    Route::delete('clearance-forward-pointsman/{id}/destroy', [LoadClearanceForwardPointsmanToController::class, 'destroy'])->name('clearance-forward-pointsman.destroy');
    Route::get('clearance-forward-pointsman/designations', [LoadClearanceForwardPointsmanToController::class, 'getDesignationsByDepartment'])->name('clearance-forward-pointsman.designations');

    // 7. Load Clearance Finaliz By
    Route::get('clearance-finaliz-by', [LoadClearanceFinalizByController::class, 'index'])->name('clearance-finaliz-by.index');
    Route::get('clearance-finaliz-by/create', [LoadClearanceFinalizByController::class, 'create'])->name('clearance-finaliz-by.create');
    Route::post('clearance-finaliz-by', [LoadClearanceFinalizByController::class, 'store'])->name('clearance-finaliz-by.store');
    Route::get('clearance-finaliz-by/{id}/edit', [LoadClearanceFinalizByController::class, 'edit'])->name('clearance-finaliz-by.edit');
    Route::post('clearance-finaliz-by/{id}', [LoadClearanceFinalizByController::class, 'update'])->name('clearance-finaliz-by.update');
    Route::delete('clearance-finaliz-by/{id}/destroy', [LoadClearanceFinalizByController::class, 'destroy'])->name('clearance-finaliz-by.destroy');
    Route::get('clearance-finaliz-by/designations', [LoadClearanceFinalizByController::class, 'getDesignationsByDepartment'])->name('clearance-finaliz-by.designations');
});

Route::prefix('project-monitering-system')->name('project-monitering-system.')->group(function () {

    Route::get('project',              [ProjectController::class, 'index'])->name('project.index');
    Route::get('project/create',       [ProjectController::class, 'create'])->name('project.create');
    Route::post('project',             [ProjectController::class, 'store'])->name('project.store');
    Route::get('project/{id}/edit',    [ProjectController::class, 'edit'])->name('project.edit');
    Route::post('project/{id}',        [ProjectController::class, 'update'])->name('project.update');
    Route::delete('project/{id}/destroy', [ProjectController::class, 'destroy'])->name('project.destroy');

    // Scope of Work
    Route::get('scope-of-work',              [ScopeOfWorkController::class, 'index'])->name('scope_of_work.index');
    Route::get('scope-of-work/create',       [ScopeOfWorkController::class, 'create'])->name('scope_of_work.create');
    Route::post('scope-of-work',             [ScopeOfWorkController::class, 'store'])->name('scope_of_work.store');
    Route::get('scope-of-work/{id}/edit',    [ScopeOfWorkController::class, 'edit'])->name('scope_of_work.edit');
    Route::post('scope-of-work/{id}',        [ScopeOfWorkController::class, 'update'])->name('scope_of_work.update');
    Route::delete('scope-of-work/{id}/destroy', [ScopeOfWorkController::class, 'destroy'])->name('scope_of_work.destroy');
});

Route::prefix('master')->name('master.')->middleware(['auth', 'XSS'])->group(function () {

    // ... existing routes ...

    // Master Employee
    Route::get('employee',              [MasterEmployeeController::class, 'index'])->name('employee.index');
    Route::get('employee/create',       [MasterEmployeeController::class, 'create'])->name('employee.create');
    Route::post('employee',             [MasterEmployeeController::class, 'store'])->name('employee.store');
    Route::get('employee/{id}/edit',    [MasterEmployeeController::class, 'edit'])->name('employee.edit');
    Route::post('employee/{id}',        [MasterEmployeeController::class, 'update'])->name('employee.update');
    Route::delete('employee/{id}/destroy', [MasterEmployeeController::class, 'destroy'])->name('employee.destroy');
   // Popup Management
Route::get('popup', [PopupController::class, 'index'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.index');

Route::get('popup/create', [PopupController::class, 'create'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.create');

Route::post('popup', [PopupController::class, 'store'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.store');

Route::get('popup/{id}/edit', [PopupController::class, 'edit'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.edit');

Route::put('popup/{id}', [PopupController::class, 'update'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.update');

Route::delete('popup/{id}/destroy', [PopupController::class, 'destroy'])
    ->middleware(['auth', 'XSS'])
    ->name('popup.destroy');
    // EO Employee

    Route::get('eo-employee', [EOEmployeeController::class, 'index'])->name('eo-employee.index');
    Route::get('eo-employee/create', [EOEmployeeController::class, 'create'])
        ->name('eo-employee.create');
    Route::post('eo-employee', [EOEmployeeController::class, 'store'])
        ->name('eo-employee.store');
    Route::get('eo-employee/{id}/edit', [EOEmployeeController::class, 'edit'])
        ->name('eo-employee.edit');
    Route::post('eo-employee/{id}', [EOEmployeeController::class, 'update'])
        ->name('eo-employee.update');
    Route::delete('eo-employee/{id}/destroy', [EOEmployeeController::class, 'destroy'])
        ->name('eo-employee.destroy');


    Route::get('slider',              [SliderController::class, 'index'])->name('slider.index');
    Route::get('slider/create',       [SliderController::class, 'create'])->name('slider.create');
    Route::post('slider',             [SliderController::class, 'store'])->name('slider.store');
    Route::get('slider/{id}/edit',    [SliderController::class, 'edit'])->name('slider.edit');
    Route::post('slider/{id}',        [SliderController::class, 'update'])->name('slider.update');
    Route::delete('slider/{id}/destroy', [SliderController::class, 'destroy'])->name('slider.destroy');

    // Message
    Route::get('message',              [MessageController::class, 'index'])->name('message.index');
    Route::get('message/create',       [MessageController::class, 'create'])->name('message.create');
    Route::post('message',             [MessageController::class, 'store'])->name('message.store');
    Route::get('message/{id}/edit',    [MessageController::class, 'edit'])->name('message.edit');
    Route::post('message/{id}',        [MessageController::class, 'update'])->name('message.update');
    Route::delete('message/{id}/destroy', [MessageController::class, 'destroy'])->name('message.destroy');

    // Inspection List & Add Inspection Flow
    Route::get('inspection',              [\App\Http\Controllers\Admin\Master\InspectionController::class, 'index'])->name('inspection.index');
    Route::get('inspection/create',       [\App\Http\Controllers\Admin\Master\InspectionController::class, 'create'])->name('inspection.create');
    Route::post('inspection',             [\App\Http\Controllers\Admin\Master\InspectionController::class, 'store'])->name('inspection.store');
    Route::get('inspection/{id}',         [\App\Http\Controllers\Admin\Master\InspectionController::class, 'show'])->name('inspection.show');
    Route::get('inspection/{id}/edit',    [\App\Http\Controllers\Admin\Master\InspectionController::class, 'edit'])->name('inspection.edit');
    Route::post('inspection/{id}/update', [\App\Http\Controllers\Admin\Master\InspectionController::class, 'update'])->name('inspection.update');
    Route::delete('inspection/{id}/destroy', [\App\Http\Controllers\Admin\Master\InspectionController::class, 'destroy'])->name('inspection.destroy');

    // TI Daily Inspection Data
    Route::resource('ti-daily-inspection', \App\Http\Controllers\Admin\Master\TiDailyInspectionController::class)->names([
        'index'   => 'ti_daily_inspection.index',
        'create'  => 'ti_daily_inspection.create',
        'store'   => 'ti_daily_inspection.store',
        'show'    => 'ti_daily_inspection.show',
        'edit'    => 'ti_daily_inspection.edit',
        'update'  => 'ti_daily_inspection.update',
        'destroy' => 'ti_daily_inspection.destroy',
    ]);

    // Inspection / Movement Report
    Route::get('report/inspection', [\App\Http\Controllers\ReportController::class, 'inspectionReport'])->name('report.inspection');
});



Route::prefix('document-upload')->name('document-upload.')->group(function () {

    Route::get('safety-circular', [SafetyCircularController::class, 'index'])
        ->name('safety-circular.index');

    Route::get('safety-circular/create', [SafetyCircularController::class, 'create'])
        ->name('safety-circular.create');

    Route::post('safety-circular', [SafetyCircularController::class, 'store'])
        ->name('safety-circular.store');

    Route::get('safety-circular/{id}/edit', [SafetyCircularController::class, 'edit'])
        ->name('safety-circular.edit');

    Route::post('safety-circular/{id}', [SafetyCircularController::class, 'update'])
        ->name('safety-circular.update');

    Route::delete('safety-circular/{id}/destroy', [SafetyCircularController::class, 'destroy'])
        ->name('safety-circular.destroy');


    Route::get('operating-safety-circular', [OperatingSafetyCircularController::class, 'index'])
        ->name('operating-safety-circular.index');

    Route::get('operating-safety-circular/create', [OperatingSafetyCircularController::class, 'create'])
        ->name('operating-safety-circular.create');

    Route::post('operating-safety-circular', [OperatingSafetyCircularController::class, 'store'])
        ->name('operating-safety-circular.store');

    Route::get('operating-safety-circular/{id}/edit', [OperatingSafetyCircularController::class, 'edit'])
        ->name('operating-safety-circular.edit');

    Route::post('operating-safety-circular/{id}', [OperatingSafetyCircularController::class, 'update'])
        ->name('operating-safety-circular.update');

    Route::delete('operating-safety-circular/{id}/destroy', [OperatingSafetyCircularController::class, 'destroy'])
        ->name('operating-safety-circular.destroy');


    Route::get('jpo', [JpoController::class, 'index'])
        ->name('jpo.index');

    Route::get('jpo/create', [JpoController::class, 'create'])
        ->name('jpo.create');

    Route::post('jpo', [JpoController::class, 'store'])
        ->name('jpo.store');

    Route::get('jpo/{id}/edit', [JpoController::class, 'edit'])
        ->name('jpo.edit');

    Route::post('jpo/{id}', [JpoController::class, 'update'])
        ->name('jpo.update');

    Route::delete('jpo/{id}/destroy', [JpoController::class, 'destroy'])
        ->name('jpo.destroy');
});


Route::prefix('inspection-notes')->name('inspection-notes.')->group(function () {

    Route::get('officer', [OfficerInspectionNoteController::class, 'index'])
        ->name('officer.index');

    Route::get('officer/create', [OfficerInspectionNoteController::class, 'create'])
        ->name('officer.create');

    Route::post('officer', [OfficerInspectionNoteController::class, 'store'])
        ->name('officer.store');

    Route::get('officer/{id}/edit', [OfficerInspectionNoteController::class, 'edit'])
        ->name('officer.edit');

    Route::post('officer/{id}', [OfficerInspectionNoteController::class, 'update'])
        ->name('officer.update');

    Route::delete('officer/{id}/destroy', [OfficerInspectionNoteController::class, 'destroy'])
        ->name('officer.destroy');


    Route::get('traffic-inspector', [TrafficInspectorInspectionNoteController::class, 'index'])
        ->name('traffic-inspector.index');

    Route::get('traffic-inspector/create', [TrafficInspectorInspectionNoteController::class, 'create'])
        ->name('traffic-inspector.create');

    Route::post('traffic-inspector', [TrafficInspectorInspectionNoteController::class, 'store'])
        ->name('traffic-inspector.store');

    Route::get('traffic-inspector/{id}/edit', [TrafficInspectorInspectionNoteController::class, 'edit'])
        ->name('traffic-inspector.edit');

    Route::post('traffic-inspector/{id}', [TrafficInspectorInspectionNoteController::class, 'update'])
        ->name('traffic-inspector.update');

    Route::delete('traffic-inspector/{id}/destroy', [TrafficInspectorInspectionNoteController::class, 'destroy'])
        ->name('traffic-inspector.destroy');
});


// Operating & Safety Documents


Route::prefix('operating-safety-documents')->name('operating-safety-documents.')->group(function () {
    Route::get('station-working-rule/show', [OperatingSafetyDocumentsController::class, 'stationWorkingRuleStations'])
        ->name('station-working-rule.stations');

    Route::get('station-working-rule/{station}/show', [OperatingSafetyDocumentsController::class, 'stationWorkingRuleShow'])
        ->name('station-working-rule.show');

    Route::post('station-working-rule/{station}/original', [OperatingSafetyDocumentsController::class, 'stationWorkingRuleStoreOriginal'])
        ->name('station-working-rule.store-original');

    Route::post('station-working-rule/original/{original}/slip', [OperatingSafetyDocumentsController::class, 'stationWorkingRuleStoreSlip'])
        ->name('station-working-rule.store-slip');


    Route::get('gsr/show', [OperatingSafetyDocumentsController::class, 'gsrOriginals'])
        ->name('gsr.originals');
    Route::get('gsr/show/{original}', [OperatingSafetyDocumentsController::class, 'gsrShow'])
        ->name('gsr.show');

    // Operating Manual
    Route::get('operating-manual/show', [OperatingSafetyDocumentsController::class, 'operatingManualOriginals'])
        ->name('operating-manual.originals');
    Route::get('operating-manual/show/{original}', [OperatingSafetyDocumentsController::class, 'operatingManualShow'])
        ->name('operating-manual.show');

    // Accident Manual
    Route::get('accident-manual/show', [OperatingSafetyDocumentsController::class, 'accidentManualOriginals'])
        ->name('accident-manual.originals');
    Route::get('accident-manual/show/{original}', [OperatingSafetyDocumentsController::class, 'accidentManualShow'])
        ->name('accident-manual.show');


    Route::get('operating-safety-alert/show', [OperatingSafetyDocumentsController::class, 'alertShow'])
        ->name('alert.show');
    Route::get('operating-safety-alert/list/{filter}', [OperatingSafetyDocumentsController::class, 'alertList'])
        ->name('alert.list');

    // EPISODE
    Route::get('operating-safety-episode/show', [OperatingSafetyDocumentsController::class, 'episodeShow'])
        ->name('episode.show');
    Route::get('operating-safety-episode/list/{filter}', [OperatingSafetyDocumentsController::class, 'episodeList'])
        ->name('episode.list');

    // DRIVE
    Route::get('operating-safety-drive/show', [OperatingSafetyDocumentsController::class, 'driveShow'])
        ->name('drive.show');
    Route::get('operating-safety-drive/list/{filter}', [OperatingSafetyDocumentsController::class, 'driveList'])
        ->name('drive.list');

    Route::get('case-study/show', [OperatingSafetyDocumentsController::class, 'caseStudyShow'])
        ->name('case-study.show');

    Route::get('case-study/{id}/show', [OperatingSafetyDocumentsController::class, 'caseStudyDetail'])
        ->name('case-study.detail');

    // 

    Route::get('safety-circular/show', [OperatingSafetyDocumentsController::class, 'safetyCircularList'])
        ->name('safety-circular.show');

    Route::get('operating-safety-circular/show', [OperatingSafetyDocumentsController::class, 'operatingSafetyCircularList'])
        ->name('operating-safety-circular.show');

    Route::get('jpo/show', [OperatingSafetyDocumentsController::class, 'jpoList'])
        ->name('jpo.show');

    Route::get('officer-inspection-notes/show', [OperatingSafetyDocumentsController::class, 'officerInspectionNotesOfficers'])
        ->name('officer-inspection-notes.officers');

    Route::get('officer-inspection-notes/{officer}/types', [OperatingSafetyDocumentsController::class, 'officerInspectionNotesTypes'])
        ->name('officer-inspection-notes.types');

    Route::get('officer-inspection-notes/{officer}/types/{typeId}', [OperatingSafetyDocumentsController::class, 'officerInspectionNotesList'])
        ->name('officer-inspection-notes.list');



    Route::get('traffic-inspector-inspection-notes/show', [OperatingSafetyDocumentsController::class, 'trafficInspectorInspectionNotesInspectors'])
        ->name('traffic-inspector-inspection-notes.inspectors');

    Route::get('traffic-inspector-inspection-notes/{inspector}/types', [OperatingSafetyDocumentsController::class, 'trafficInspectorInspectionNotesTypes'])
        ->name('traffic-inspector-inspection-notes.types');

    Route::get('traffic-inspector-inspection-notes/{inspector}/types/{typeId}', [OperatingSafetyDocumentsController::class, 'trafficInspectorInspectionNotesList'])
        ->name('traffic-inspector-inspection-notes.list');



    Route::prefix('station-working-rule')
        ->name('station-working-rule.')
        ->group(function () {

            Route::prefix('original')
                ->name('original.')
                ->group(function () {

                    Route::get('/', [StationWorkingRuleOriginalController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [StationWorkingRuleOriginalController::class, 'create'])
                        ->name('create');

                    Route::post('/', [StationWorkingRuleOriginalController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [StationWorkingRuleOriginalController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [StationWorkingRuleOriginalController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [StationWorkingRuleOriginalController::class, 'destroy'])
                        ->name('destroy');
                });


            Route::prefix('correction')
                ->name('correction.')
                ->group(function () {

                    Route::get('/', [StationWorkingRuleCorrectionController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [StationWorkingRuleCorrectionController::class, 'create'])
                        ->name('create');

                    Route::post('/', [StationWorkingRuleCorrectionController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [StationWorkingRuleCorrectionController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [StationWorkingRuleCorrectionController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [StationWorkingRuleCorrectionController::class, 'destroy'])
                        ->name('destroy');

                    Route::get('/originals/{stationId}', [StationWorkingRuleCorrectionController::class, 'getOriginals'])
                        ->name('originals');

                    Route::get('/original/{id}', [StationWorkingRuleCorrectionController::class, 'getOriginal'])
                        ->name('original');
                });
        });


    /*
    |--------------------------------------------------------------------------
    | G&SR
    |--------------------------------------------------------------------------
    */

    Route::prefix('g-sr')->name('g-sr.')->group(function () {

        Route::prefix('original')
            ->name('original.')
            ->group(function () {

                Route::get('/', [GsrOriginalController::class, 'index'])
                    ->name('index');

                Route::get('/create', [GsrOriginalController::class, 'create'])
                    ->name('create');

                Route::post('/', [GsrOriginalController::class, 'store'])
                    ->name('store');

                Route::get('/{id}/edit', [GsrOriginalController::class, 'edit'])
                    ->name('edit');

                Route::post('/{id}', [GsrOriginalController::class, 'update'])
                    ->name('update');

                Route::delete('/{id}/destroy', [GsrOriginalController::class, 'destroy'])
                    ->name('destroy');
            });


        Route::prefix('correction')
            ->name('correction.')
            ->group(function () {

                Route::get('/', [GsrCorrectionController::class, 'index'])
                    ->name('index');

                Route::get('/create', [GsrCorrectionController::class, 'create'])
                    ->name('create');

                Route::post('/', [GsrCorrectionController::class, 'store'])
                    ->name('store');

                Route::get('/{id}/edit', [GsrCorrectionController::class, 'edit'])
                    ->name('edit');

                Route::post('/{id}', [GsrCorrectionController::class, 'update'])
                    ->name('update');

                Route::delete('/{id}/destroy', [GsrCorrectionController::class, 'destroy'])
                    ->name('destroy');

                Route::get('/original/{id}', [GsrCorrectionController::class, 'getOriginal'])
                    ->name('original');
            });
    });


    /*
    |--------------------------------------------------------------------------
    | OPERATING MANUAL
    |--------------------------------------------------------------------------
    */

    Route::prefix('operating-manual')
        ->name('operating-manual.')
        ->group(function () {

            Route::prefix('original')
                ->name('original.')
                ->group(function () {

                    Route::get('/', [OperatingManualOriginalController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [OperatingManualOriginalController::class, 'create'])
                        ->name('create');

                    Route::post('/', [OperatingManualOriginalController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [OperatingManualOriginalController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [OperatingManualOriginalController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [OperatingManualOriginalController::class, 'destroy'])
                        ->name('destroy');
                });


            Route::prefix('correction')
                ->name('correction.')
                ->group(function () {

                    Route::get('/', [OperatingManualCorrectionController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [OperatingManualCorrectionController::class, 'create'])
                        ->name('create');

                    Route::post('/', [OperatingManualCorrectionController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [OperatingManualCorrectionController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [OperatingManualCorrectionController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [OperatingManualCorrectionController::class, 'destroy'])
                        ->name('destroy');

                    Route::get('/original/{id}', [OperatingManualCorrectionController::class, 'getOriginal'])
                        ->name('original');
                });
        });


    /*
    |--------------------------------------------------------------------------
    | ACCIDENT MANUAL
    |--------------------------------------------------------------------------
    */

    Route::prefix('accident-manual')
        ->name('accident-manual.')
        ->group(function () {

            Route::prefix('original')
                ->name('original.')
                ->group(function () {

                    Route::get('/', [AccidentManualOriginalController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [AccidentManualOriginalController::class, 'create'])
                        ->name('create');

                    Route::post('/', [AccidentManualOriginalController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [AccidentManualOriginalController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [AccidentManualOriginalController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [AccidentManualOriginalController::class, 'destroy'])
                        ->name('destroy');
                });


            Route::prefix('correction')
                ->name('correction.')
                ->group(function () {

                    Route::get('/', [AccidentManualCorrectionController::class, 'index'])
                        ->name('index');

                    Route::get('/create', [AccidentManualCorrectionController::class, 'create'])
                        ->name('create');

                    Route::post('/', [AccidentManualCorrectionController::class, 'store'])
                        ->name('store');

                    Route::get('/{id}/edit', [AccidentManualCorrectionController::class, 'edit'])
                        ->name('edit');

                    Route::post('/{id}', [AccidentManualCorrectionController::class, 'update'])
                        ->name('update');

                    Route::delete('/{id}/destroy', [AccidentManualCorrectionController::class, 'destroy'])
                        ->name('destroy');

                    Route::get('/original/{id}', [AccidentManualCorrectionController::class, 'getOriginal'])
                        ->name('original');
                });
        });


    /*
    |--------------------------------------------------------------------------
    | MISCELLANEOUS LETTERS
    |--------------------------------------------------------------------------
    */

    Route::get('miscellaneous-letters', [MiscellaneousLettersController::class, 'index'])
        ->name('miscellaneous-letters.index');

    Route::get('miscellaneous-letters/create', [MiscellaneousLettersController::class, 'create'])
        ->name('miscellaneous-letters.create');

    Route::post('miscellaneous-letters', [MiscellaneousLettersController::class, 'store'])
        ->name('miscellaneous-letters.store');

    Route::get('miscellaneous-letters/{id}/edit', [MiscellaneousLettersController::class, 'edit'])
        ->name('miscellaneous-letters.edit');

    Route::post('miscellaneous-letters/{id}', [MiscellaneousLettersController::class, 'update'])
        ->name('miscellaneous-letters.update');

    Route::delete('miscellaneous-letters/{id}/destroy', [MiscellaneousLettersController::class, 'destroy'])
        ->name('miscellaneous-letters.destroy');

    Route::get('miscellaneous-letters/show', [MiscellaneousLettersController::class, 'letterList'])
        ->name('miscellaneous-letters.show');

    /*
    |--------------------------------------------------------------------------
    | OPERATING SAFETY ALERT
    |--------------------------------------------------------------------------
    */

    Route::get('operating-safety-alert', [OperatingSafetyAlertController::class, 'index'])
        ->name('operating-safety-alert.index');

    Route::get('operating-safety-alert/create', [OperatingSafetyAlertController::class, 'create'])
        ->name('operating-safety-alert.create');

    Route::post('operating-safety-alert', [OperatingSafetyAlertController::class, 'store'])
        ->name('operating-safety-alert.store');

    Route::get('operating-safety-alert/{id}/edit', [OperatingSafetyAlertController::class, 'edit'])
        ->name('operating-safety-alert.edit');

    Route::post('operating-safety-alert/{id}', [OperatingSafetyAlertController::class, 'update'])
        ->name('operating-safety-alert.update');

    Route::delete('operating-safety-alert/{id}/destroy', [OperatingSafetyAlertController::class, 'destroy'])
        ->name('operating-safety-alert.destroy');


    /*
    |--------------------------------------------------------------------------
    | OPERATING SAFETY EPISODE
    |--------------------------------------------------------------------------
    */

    Route::get('operating-safety-episode', [OperatingSafetyEpisodeController::class, 'index'])
        ->name('operating-safety-episode.index');

    Route::get('operating-safety-episode/create', [OperatingSafetyEpisodeController::class, 'create'])
        ->name('operating-safety-episode.create');

    Route::post('operating-safety-episode', [OperatingSafetyEpisodeController::class, 'store'])
        ->name('operating-safety-episode.store');

    Route::get('operating-safety-episode/{id}/edit', [OperatingSafetyEpisodeController::class, 'edit'])
        ->name('operating-safety-episode.edit');

    Route::post('operating-safety-episode/{id}', [OperatingSafetyEpisodeController::class, 'update'])
        ->name('operating-safety-episode.update');

    Route::delete('operating-safety-episode/{id}/destroy', [OperatingSafetyEpisodeController::class, 'destroy'])
        ->name('operating-safety-episode.destroy');


    /*
    |--------------------------------------------------------------------------
    | OPERATING SAFETY DRIVE
    |--------------------------------------------------------------------------
    */

    Route::get('operating-safety-drive', [OperatingSafetyDriveController::class, 'index'])
        ->name('operating-safety-drive.index');

    Route::get('operating-safety-drive/create', [OperatingSafetyDriveController::class, 'create'])
        ->name('operating-safety-drive.create');

    Route::post('operating-safety-drive', [OperatingSafetyDriveController::class, 'store'])
        ->name('operating-safety-drive.store');

    Route::get('operating-safety-drive/{id}/edit', [OperatingSafetyDriveController::class, 'edit'])
        ->name('operating-safety-drive.edit');

    Route::post('operating-safety-drive/{id}', [OperatingSafetyDriveController::class, 'update'])
        ->name('operating-safety-drive.update');

    Route::delete('operating-safety-drive/{id}/destroy', [OperatingSafetyDriveController::class, 'destroy'])
        ->name('operating-safety-drive.destroy');


    // 
    Route::get('case-study', [CaseStudyController::class, 'index'])
        ->name('case-study.index');

    Route::get('case-study/create', [CaseStudyController::class, 'create'])
        ->name('case-study.create');

    Route::post('case-study', [CaseStudyController::class, 'store'])
        ->name('case-study.store');

    Route::get('case-study/{id}/edit', [CaseStudyController::class, 'edit'])
        ->name('case-study.edit');

    Route::post('case-study/{id}', [CaseStudyController::class, 'update'])
        ->name('case-study.update');

    Route::delete('case-study/{id}/destroy', [CaseStudyController::class, 'destroy'])
        ->name('case-study.destroy');
});


Route::prefix('works-paperwork-progress-register')
    ->name('works-paperwork-progress-register.')
    ->group(function () {
        Route::get('identify-new-work', [IdentifyNewWorkController::class, 'index'])->name('identify-new-work.index');
        Route::get('identify-new-work/create', [IdentifyNewWorkController::class, 'create'])->name('identify-new-work.create');
        Route::post('identify-new-work', [IdentifyNewWorkController::class, 'store'])->name('identify-new-work.store');
        Route::get('identify-new-work/{id}/edit', [IdentifyNewWorkController::class, 'edit'])->name('identify-new-work.edit');
        Route::put('identify-new-work/{id}', [IdentifyNewWorkController::class, 'update'])->name('identify-new-work.update');
        Route::delete('identify-new-work/{id}/destroy', [IdentifyNewWorkController::class, 'destroy'])->name('identify-new-work.destroy');

        Route::get('joint-survey', [JointSurveyController::class, 'index'])->name('joint-survey.index');
        Route::get('joint-survey/create', [JointSurveyController::class, 'create'])->name('joint-survey.create');
        Route::post('joint-survey', [JointSurveyController::class, 'store'])->name('joint-survey.store');
        Route::get('joint-survey/{id}/edit', [JointSurveyController::class, 'edit'])->name('joint-survey.edit');
        Route::post('joint-survey/{id}', [JointSurveyController::class, 'update'])->name('joint-survey.update');
        Route::delete('joint-survey/{id}/destroy', [JointSurveyController::class, 'destroy'])->name('joint-survey.destroy');
        Route::get('joint-survey/milestone-status/{work_id}', [JointSurveyController::class, 'milestoneStatus'])->name('joint-survey.milestone-status');

        Route::get('estimates', [EstimateController::class, 'index'])->name('estimates.index');
        Route::get('estimates/create', [EstimateController::class, 'create'])->name('estimates.create');
        Route::post('estimates', [EstimateController::class, 'store'])->name('estimates.store');
        Route::get('estimates/{id}/edit', [EstimateController::class, 'edit'])->name('estimates.edit');
        Route::post('estimates/{id}', [EstimateController::class, 'update'])->name('estimates.update');
        Route::delete('estimates/{id}/destroy', [EstimateController::class, 'destroy'])->name('estimates.destroy');
        Route::get('estimates/department-status/{work_id}', [EstimateController::class, 'departmentStatus'])->name('estimates.department-status');

        Route::get('review-estimate', [ReviewEstimateController::class, 'index'])->name('review-estimate.index');
        Route::get('review-estimate/create', [ReviewEstimateController::class, 'create'])->name('review-estimate.create');
        Route::post('review-estimate', [ReviewEstimateController::class, 'store'])->name('review-estimate.store');
        Route::get('review-estimate/{id}/edit', [ReviewEstimateController::class, 'edit'])->name('review-estimate.edit');
        Route::post('review-estimate/{id}', [ReviewEstimateController::class, 'update'])->name('review-estimate.update');
        Route::delete('review-estimate/{id}/destroy', [ReviewEstimateController::class, 'destroy'])->name('review-estimate.destroy');

        Route::get('approval-listing', [ApprovalListingController::class, 'index'])->name('approval-listing.index');
        Route::get('approval-listing/create', [ApprovalListingController::class, 'create'])->name('approval-listing.create');
        Route::post('approval-listing', [ApprovalListingController::class, 'store'])->name('approval-listing.store');
        Route::get('approval-listing/{id}/edit', [ApprovalListingController::class, 'edit'])->name('approval-listing.edit');
        Route::post('approval-listing/{id}', [ApprovalListingController::class, 'update'])->name('approval-listing.update');
        Route::delete('approval-listing/{id}/destroy', [ApprovalListingController::class, 'destroy'])->name('approval-listing.destroy');
        Route::get('approval-listing/milestone-status/{work_id}', [ApprovalListingController::class, 'milestoneStatus'])->name('approval-listing.milestone-status');

        Route::get('finance-vetting-sanction', [FinanceVettingSanctionController::class, 'index'])->name('finance-vetting-sanction.index');
        Route::get('finance-vetting-sanction/create', [FinanceVettingSanctionController::class, 'create'])->name('finance-vetting-sanction.create');
        Route::post('finance-vetting-sanction', [FinanceVettingSanctionController::class, 'store'])->name('finance-vetting-sanction.store');
        Route::get('finance-vetting-sanction/{id}/edit', [FinanceVettingSanctionController::class, 'edit'])->name('finance-vetting-sanction.edit');
        Route::post('finance-vetting-sanction/{id}', [FinanceVettingSanctionController::class, 'update'])->name('finance-vetting-sanction.update');
        Route::delete('finance-vetting-sanction/{id}/destroy', [FinanceVettingSanctionController::class, 'destroy'])->name('finance-vetting-sanction.destroy');
        Route::get('finance-vetting-sanction/milestone-status/{work_id}', [FinanceVettingSanctionController::class, 'milestoneStatus'])->name('finance-vetting-sanction.milestone-status');

        Route::get('tendering', [TenderingController::class, 'index'])->name('tendering.index');
        Route::get('tendering/create', [TenderingController::class, 'create'])->name('tendering.create');
        Route::post('tendering', [TenderingController::class, 'store'])->name('tendering.store');
        Route::get('tendering/{id}/edit', [TenderingController::class, 'edit'])->name('tendering.edit');
        Route::post('tendering/{id}', [TenderingController::class, 'update'])->name('tendering.update');
        Route::delete('tendering/{id}/destroy', [TenderingController::class, 'destroy'])->name('tendering.destroy');
        Route::get('tendering/milestone-status/{work_id}', [TenderingController::class, 'milestoneStatus'])->name('tendering.milestone-status');

        // ESP
        Route::get('esp-sip/esp', [EspSipController::class, 'indexEsp'])->name('esp-sip.esp.index');
        Route::get('esp-sip/esp/create', [EspSipController::class, 'createEsp'])->name('esp-sip.esp.create');
        Route::post('esp-sip/esp', [EspSipController::class, 'storeEsp'])->name('esp-sip.esp.store');
        Route::get('esp-sip/esp/{id}/edit', [EspSipController::class, 'editEsp'])->name('esp-sip.esp.edit');
        Route::post('esp-sip/esp/{id}', [EspSipController::class, 'updateEsp'])->name('esp-sip.esp.update');
        Route::delete('esp-sip/esp/{id}/destroy', [EspSipController::class, 'destroyEsp'])->name('esp-sip.esp.destroy');
        Route::get('esp-sip/esp/milestone-status/{work_id}', [EspSipController::class, 'milestoneStatusEsp'])->name('esp-sip.esp.milestone-status');

        // SIP
        Route::get('esp-sip/sip', [EspSipController::class, 'indexSip'])->name('esp-sip.sip.index');
        Route::get('esp-sip/sip/create', [EspSipController::class, 'createSip'])->name('esp-sip.sip.create');
        Route::post('esp-sip/sip', [EspSipController::class, 'storeSip'])->name('esp-sip.sip.store');
        Route::get('esp-sip/sip/{id}/edit', [EspSipController::class, 'editSip'])->name('esp-sip.sip.edit');
        Route::post('esp-sip/sip/{id}', [EspSipController::class, 'updateSip'])->name('esp-sip.sip.update');
        Route::delete('esp-sip/sip/{id}/destroy', [EspSipController::class, 'destroySip'])->name('esp-sip.sip.destroy');
        Route::get('esp-sip/sip/milestone-status/{work_id}', [EspSipController::class, 'milestoneStatusSip'])->name('esp-sip.sip.milestone-status');



        Route::get('execution', [ExecutionController::class, 'index'])->name('execution.index');
        Route::get('execution/create', [ExecutionController::class, 'create'])->name('execution.create');
        Route::post('execution', [ExecutionController::class, 'store'])->name('execution.store');
        Route::get('execution/{id}/edit', [ExecutionController::class, 'edit'])->name('execution.edit');
        Route::post('execution/{id}', [ExecutionController::class, 'update'])->name('execution.update');
        Route::delete('execution/{id}/destroy', [ExecutionController::class, 'destroy'])->name('execution.destroy');
        Route::get('execution/milestone-status/{work_id}', [ExecutionController::class, 'milestoneStatus'])->name('execution.milestone-status');


        Route::get('variation', [VariationController::class, 'index'])->name('variation.index');
        Route::get('variation/create', [VariationController::class, 'create'])->name('variation.create');
        Route::post('variation', [VariationController::class, 'store'])->name('variation.store');
        Route::get('variation/{id}/edit', [VariationController::class, 'edit'])->name('variation.edit');
        Route::post('variation/{id}', [VariationController::class, 'update'])->name('variation.update');
        Route::delete('variation/{id}/destroy', [VariationController::class, 'destroy'])->name('variation.destroy');

        Route::get('inspection-log', [InspectionLogController::class, 'index'])->name('inspection-log.index');
        Route::get('inspection-log/create', [InspectionLogController::class, 'create'])->name('inspection-log.create');
        Route::post('inspection-log', [InspectionLogController::class, 'store'])->name('inspection-log.store');
        Route::get('inspection-log/{id}/edit', [InspectionLogController::class, 'edit'])->name('inspection-log.edit');
        Route::post('inspection-log/{id}', [InspectionLogController::class, 'update'])->name('inspection-log.update');
        Route::delete('inspection-log/{id}/destroy', [InspectionLogController::class, 'destroy'])->name('inspection-log.destroy');

        // Single photo delete (AJAX, edit screen se call hota hai)
        Route::get('inspection-log/photo/{id}/destroy', [InspectionLogController::class, 'destroyPhoto'])->name('inspection-log.photo.destroy');


        Route::prefix('executionend')->name('executionend.')->group(function () {
            Route::get('/', [ExecutionendController::class, 'index'])->name('index');
            Route::get('/create', [ExecutionendController::class, 'create'])->name('create');
            Route::post('/', [ExecutionendController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ExecutionendController::class, 'edit'])->name('edit');
            Route::post('/{id}', [ExecutionendController::class, 'update'])->name('update');
            Route::delete('/{id}', [ExecutionendController::class, 'destroy'])->name('destroy');
            Route::get('/milestone-status/{workId}', [ExecutionendController::class, 'milestoneStatus'])->name('milestone-status');
        });
    });




// Login frontend dynmic routes
Route::prefix('frontend-login')->name('frontend-login.')->group(function () {

    Route::get('background-login', [BackgroundLoginController::class, 'index'])->name('background-login.index');
    Route::get('background-login/create', [BackgroundLoginController::class, 'create'])->name('background-login.create');
    Route::post('background-login', [BackgroundLoginController::class, 'store'])->name('background-login.store');
    Route::get('background-login/{id}/edit', [BackgroundLoginController::class, 'edit'])->name('background-login.edit');
    Route::put('background-login/{id}', [BackgroundLoginController::class, 'update'])->name('background-login.update');
    Route::delete('background-login/{id}/destroy', [BackgroundLoginController::class, 'destroy'])->name('background-login.destroy');
});

Route::prefix('report/ph16-report')->name('report.ph16.')->group(function () {
    Route::get('/', [Ph16ReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [Ph16ReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [Ph16ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [Ph16ReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-work-pdf', [Ph16ReportController::class, 'exportWorkPdf'])->name('export.work-pdf');
});

Route::prefix('report/complaints-report')->name('report.complaints.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [ComplaintsReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [ComplaintsReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [ComplaintsReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [ComplaintsReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-complaint-pdf', [ComplaintsReportController::class, 'exportComplaintPdf'])->name('export.complaint-pdf');
});

Route::prefix('report/ambush-check-report')->name('report.ambush.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [AmbushCheckReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [AmbushCheckReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [AmbushCheckReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [AmbushCheckReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-ambush-pdf', [AmbushCheckReportController::class, 'exportAmbushPdf'])->name('export.ambush-pdf');
});

Route::prefix('report/my-movement-report')->name('report.my-movement.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [MyMovementReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [MyMovementReportController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [MyMovementReportController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [MyMovementReportController::class, 'update'])->name('update');
    Route::delete('/{id}/destroy', [MyMovementReportController::class, 'destroy'])->name('destroy');
    Route::get('/export-excel', [MyMovementReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [MyMovementReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-movement-pdf', [MyMovementReportController::class, 'exportMovementPdf'])->name('export.movement-pdf');
});

Route::prefix('report/staff-counseling-report')->name('report.staff-counseling.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [StaffCounselingReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [StaffCounselingReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [StaffCounselingReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [StaffCounselingReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-counseling-pdf', [StaffCounselingReportController::class, 'exportCounselingPdf'])->name('export.counseling-pdf');
});

Route::prefix('report/award-record-report')->name('report.award-record.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [AwardRecordReportController::class, 'index'])->name('index');
    Route::get('/{id}/show', [AwardRecordReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [AwardRecordReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [AwardRecordReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{id}/export-nomination-pdf', [AwardRecordReportController::class, 'exportNominationPdf'])->name('export.nomination-pdf');
    Route::get('/{id}/certificate', [AwardRecordReportController::class, 'certificate'])->name('certificate');
});

Route::prefix('report/employee-safety-record-report')->name('report.employee-safety.')->middleware(['auth', 'XSS'])->group(function () {
    Route::get('/', [EmployeeSafetyRecordReportController::class, 'index'])->name('index');
    Route::get('/{type}/{id}/show', [EmployeeSafetyRecordReportController::class, 'show'])->name('show');
    Route::get('/export-excel', [EmployeeSafetyRecordReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-pdf', [EmployeeSafetyRecordReportController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/{type}/{id}/export-pdf', [EmployeeSafetyRecordReportController::class, 'exportSinglePdf'])->name('export.single-pdf');
});


Route::prefix('emergency-contact-report')->name('emergency-contact-report.')->group(function () {
    Route::get('/', [EmergencyContactReportController::class, 'index'])->name('index');
    Route::get('/designations', [EmergencyContactReportController::class, 'getDesignations'])->name('designations');
    Route::get('/employees', [EmergencyContactReportController::class, 'getEmployees'])->name('employees');
    Route::get('/search', [EmergencyContactReportController::class, 'search'])->name('search');
});

// AJAX Routes for dependent dropdowns
Route::get('ajax/get-departments/{division_id}',   [WorkingAreaController::class, 'getDepartments'])->name('ajax.get-departments');
Route::get('ajax/get-designations/{department_id}', [WorkingAreaController::class, 'getDesignations'])->name('ajax.get-designations');
Route::get('ajax/get-designation-detail/{id}',     [WorkingAreaController::class, 'getDesignationDetail'])->name('ajax.get-designation-detail');
Route::get('/designations/show', [CreateComplainController::class, 'getDesignations'])
    ->name('authority.complain.designation');
//-------------------------------FAQ-------------------------------------------
Route::impersonate();
