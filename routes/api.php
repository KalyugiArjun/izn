<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\CreateComplaintController;
use App\Http\Controllers\Api\CreateRectifyController;
use App\Http\Controllers\Api\DropdownController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\LoadStablingController;
use App\Http\Controllers\Admin\Master\LineNoController;
use App\Http\Controllers\Api\WagonNumberController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\EmployeeSafetyRecordController;
use App\Http\Controllers\Api\CircularController;
use App\Http\Controllers\Api\StaffCounselingController;
use App\Http\Controllers\Api\InspectionNotesController;
use App\Http\Controllers\Api\SelfNominationController;
use App\Http\Controllers\Api\Ph16WorkController;
use App\Http\Controllers\Api\UserTabsController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware([
    'auth:sanctum',
    'inactive.token'
])->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test', [TestController::class, 'index']);
Route::post('/test', [TestController::class, 'store']);
Route::get('/test/{id}', [TestController::class, 'show']);

// Public Routes
Route::post('/check-phone', [AuthController::class, 'checkPhone']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/send-forgot-otp', [AuthController::class, 'sendForgotOtp']);
Route::get('/get-email/{phone}',[AuthController::class, 'getEmailByPhone']);
Route::post('/verify-forgot-otp', [AuthController::class, 'verifyForgotOtp']);

Route::get('/departments', [DropdownController::class, 'departments']);
Route::get('/departments/{id}', [DropdownController::class, 'departmentById']);
Route::get('/designations', [DropdownController::class, 'designations']);
Route::get('/designations/{id}', [DropdownController::class, 'designationsById']);

Route::get('/assign-to', [DropdownController::class, 'assignTo']);
Route::get('/departments/{id}/employees', [DropdownController::class, 'employeesByDepartment']);

Route::get('/amenity-categories', [DropdownController::class, 'amenityCategories']);
Route::get('/timinline', [DropdownController::class, 'timinline']);

Route::get('/all-emp-create-complaints', [CreateComplaintController::class, 'index']);
Route::get('/all-emp-rectify-complaints', [CreateRectifyController::class, 'index']);

Route::get('/all-stations', [DropdownController::class, 'allStations']);
 




    

Route::middleware([
    'auth:sanctum',
    'inactive.token'
])->group(function () {

   // allow mobile tabs for users
   Route::get('/allow-tabs', [UserTabsController::class, 'index']);

    // user
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::post('/logout-all', [AuthController::class, 'logoutAll']); 
    Route::get('/user-roles', [AuthController::class, 'checkUserRoles']);
    Route::post('/change-password',[AuthController::class, 'changePassword']);

    Route::get('/dashboard', [AuthController::class, 'dashboard']);
    Route::get('/new-dashboard', [AuthController::class, 'newDashboard']);
    Route::post('/emergency-contact', [AuthController::class, 'storeOrUpdateEmergencyContact']);
    Route::get('/profile-data', [AuthController::class, 'emergencyContactIndex']);
    Route::post('/profile-update', [AuthController::class, 'profileUpdate']);
    Route::post('/employee-station-update', [AuthController::class, 'updateEmployeeStation']);

    // posted employee stations
    Route::get('/stations', [DropdownController::class, 'stations']); // empployee station only
    Route::get('/station/{id}', [DropdownController::class, 'stationsById']); 
    Route::get('/station/{stationId}/employees', [DropdownController::class, 'employeesByStation']);
    Route::get('/employee/{id}/stations', [DropdownController::class, 'stationsByEmployee']);
    // employee get by id
    Route::get('/employees/{Id}', [DropdownController::class, 'employeesById']);
    // get employee by diesel lobby EO_EMPLOYEE
    Route::get('/eo-employees/{diesel_lobby_id}', [DropdownController::class, 'getEmployeeByDieselLobbyId']);
    
    
   

    // Complaints Routes
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::get('/complaints-searchable', [ComplaintController::class, 'searchPaginationList']);
    Route::get('/complaints-status', [ComplaintController::class, 'statusCount']);
    Route::get('/complaints/{id}', [ComplaintController::class, 'show']);
    Route::put('/complaints/{id}', [ComplaintController::class, 'update']);
    Route::delete('/complaints/{id}', [ComplaintController::class, 'destroy']);
    Route::post('/complaints/{id}/rectify', [ComplaintController::class, 'updateRectify']);
    Route::post('/complaints/{id}/feedback', [ComplaintController::class, 'submitFeedback']);
    Route::post('/complaints/{id}/close', [ComplaintController::class, 'submitClose']);
    Route::post('complaints/{id}/reopen', [ComplaintController::class, 'reopenComplaint']);
    Route::get('/complaints/{id}/history', [ComplaintController::class, 'history']);
    Route::post('/complaints/{id}/assign', [ComplaintController::class, 'assign']);
    Route::post('/complaint/transfer/{id}', [ComplaintController::class, 'transfer']);
    Route::get('/complaints/{id}/assign-data', [ComplaintController::class, 'getAssignComplaintData']);
    Route::post('/assign-complaints/{id}/assign', [ComplaintController::class, 'assignComplaintStore']);

        
    // line no. routes
    Route::prefix('line-no')->group(function () {
        Route::get('/', [LineNoController::class, 'apiIndex']);
        // Route::post('/store', [LineNoController::class, 'apiStore']);
        Route::get('/show/{id}', [LineNoController::class, 'apiShow']);
        // Route::post('/update/{id}', [LineNoController::class, 'apiUpdate']);
        // Route::delete('/delete/{id}', [LineNoController::class, 'apiDestroy']);
    });


    // wagon number routes - FIXED
    Route::post('/wagon/store', [WagonNumberController::class, 'store']);
    Route::get('/wagon/list', [WagonNumberController::class, 'index']);
    Route::get('/wagon/load-stabling/{id}', [WagonNumberController::class, 'getByLoadStabling']);
    Route::post('/wagon/update-by-load-stabling/{id}',[WagonNumberController::class, 'updateByLoadStabling']);
    Route::delete('/wagon/delete-by-load-stabling/{id}',[WagonNumberController::class, 'deleteByLoadStabling']);
    Route::post('/wagon/update/{id}', [WagonNumberController::class, 'update']); 
    Route::delete('/wagon/delete/{id}', [WagonNumberController::class, 'destroy']);



    // inspection TRAFFIC INSPECTOR HQ data routes
    Route::get('/inspection-movements', [InspectionController::class, 'index']);
    Route::post('/inspection-movements', [InspectionController::class, 'store']);
    Route::get('/inspection-movements/{id}', [InspectionController::class, 'show']);
    Route::post('/inspection-movements/{id}', [InspectionController::class, 'update']);
    Route::delete('/inspection-movements/{id}', [InspectionController::class, 'destroy']);

    // Regular inspection HQ
    Route::get('/regular-inspections', [InspectionController::class, 'regularIndex']);
    Route::post('/regular-inspections', [InspectionController::class, 'regularStore']);
    Route::get('/regular-inspections/{id}', [InspectionController::class, 'regularShow']);
    Route::post('/regular-inspections/{id}', [InspectionController::class, 'regularUpdate']);
    Route::delete('/regular-inspections/{id}', [InspectionController::class, 'regularDestroy']);

    // Ambush Check HQ
    Route::get('/ambush-check',[InspectionController::class, 'ambushIndex']);
    Route::post('/ambush-check',[InspectionController::class, 'ambushStore']);
    Route::get('/ambush-check/{id}',[InspectionController::class, 'ambushShow']);
    Route::post('/ambush-check/{id}',[InspectionController::class, 'ambushUpdate']);
    Route::delete('/ambush-check/{id}', [InspectionController::class, 'ambushDestroy']);

    // staff-counseling
    Route::get('/staff-counseling-list', [StaffCounselingController::class, 'staffCounselingIndex']);
    Route::post('/staff-counseling', [StaffCounselingController::class, 'staffCounselingStore']);
    Route::post('/staff-counseling/{id}', [StaffCounselingController::class, 'staffCounselingUpdate']);

    // self nomination award
    Route::get('/nomination', [SelfNominationController::class, 'index']);
    Route::get('/track-your-nomination', [SelfNominationController::class, 'trackYourForm']);
    Route::post('/nomination', [SelfNominationController::class, 'store']);
    Route::get('/nomination/{id}', [SelfNominationController::class, 'show']);
    Route::post('/nomination/{id}', [SelfNominationController::class, 'update']);
    Route::get('/nomination-status-count', [SelfNominationController::class, 'statusCount']);

    Route::get('/proficiency-type', [DropdownController::class, 'getProficiency']);



    // drop menu from inspection HQ
    Route::get('/movements', [InspectionController::class, 'movements']);
    Route::get('/types-of-inspections', [InspectionController::class, 'typeofinspections']);
    Route::get('/diesel-lobbies', [InspectionController::class, 'getDieselLobbies']);
    Route::get('/diesel-lobbies/{id}/employees', [InspectionController::class, 'employeesByDieselLobby']);
    Route::get('/running-room-locations', [InspectionController::class, 'getRunningRoomLocations']);
    Route::get('/running-room-locations/{id}/employees', [InspectionController::class, 'employeesByRunningRoomLocation']);
    Route::get('/type-of-ambushs', [InspectionController::class, 'typeOfAmbushs']);
    Route::get('/safety-drive', [InspectionController::class, 'safetyDrive']);
    Route::get('/type-of-leave', [InspectionController::class, 'typeOfLeave']);
    Route::get('/attend-block', [InspectionController::class, 'attendBlock']);



    // inspection data routes TRAFFIC INSPECTOR
    Route::post('/inspection/store', [InspectionController::class, 'storeTi']);
    Route::get('/inspection/list', [InspectionController::class, 'indexTi']);
    Route::get('/inspection/show/{id}', [InspectionController::class, 'showTi']);
    Route::post('/inspection/update/{id}', [InspectionController::class, 'updateTi']);
    Route::delete('/inspection/delete/{id}', [InspectionController::class, 'destroyTi']);



    // Employee Safety Record
    Route::post('/employee/pme', [EmployeeSafetyRecordController::class, 'storePME']);
    Route::post('/employee/refresher-course', [EmployeeSafetyRecordController::class, 'storeRefresherCourse']);
    Route::post('/employee/staff-gradation', [EmployeeSafetyRecordController::class, 'storeStaffGradation']);
    Route::get('/employee-pme/{master_employee_id}', [EmployeeSafetyRecordController::class, 'getPME']);
    
    Route::get('/employee-refresher-course/{master_employee_id}', [EmployeeSafetyRecordController::class, 'getRefresherCourse']);

    Route::get('/employee-staff-gradation/{master_employee_id}', [EmployeeSafetyRecordController::class, 'getStaffGradation']);


    // Circulars routes PDFs
    Route::get('/safety-circulars', [CircularController::class, 'safetyCircular']);
    Route::get('/safety-circulars/{id}', [CircularController::class, 'safetyShow']);

    Route::get('/operating-circulars', [CircularController::class, 'operatingSafetyCircular']);
    Route::get('/jpo-circulars', [CircularController::class, 'jpoIndex']);

    Route::get('/station-rule', [CircularController::class, 'stationWorkingRule']);
    
    Route::get('/operating-doc-details', [CircularController::class, 'operatingDocDetails']);
    Route::get('/gsr-original', [CircularController::class, 'GsrOriginal']);
    Route::get('/gsr-correction', [CircularController::class, 'GsrCorrection']);

    Route::get('/operating-manual-original', [CircularController::class, 'operatingManualOriginal']);
    Route::get('/operating-manual-correction', [CircularController::class, 'operatingManualCorrection']);
    Route::get('/accident-manual-original', [CircularController::class, 'accidentManualOriginal']);
    Route::get('/accident-manual-correction', [CircularController::class, 'accidentManualCorrection']);
    Route::get('/miscellaneous-letters', [CircularController::class, 'miscellaneousLetters']);
    Route::get('/operating-safety-alert', [CircularController::class, 'operatingSafetyAlert']);
    Route::get('/operating-safety-episode', [CircularController::class, 'operatingSafetyEpisode']);
    Route::get('/operating-safety-drive', [CircularController::class, 'operatingSafetyDrive']);
    Route::get('/case-study', [CircularController::class, 'caseStudy']);
    Route::get('/officer-inspection-note', [CircularController::class, 'OfficerInspectionNote']);
    Route::get('/officer-inspection-note-summary', [CircularController::class, 'OfficerInspectionNoteSummary']);
    Route::get('/traffic-inspection-note', [CircularController::class, 'TrafficInspectorInspectionNote']);
    Route::get('/traffic-inspection-note-summary', [CircularController::class, 'TrafficInspectorInspectionNoteSummary']);

    Route::get('/officer-note', [InspectionNotesController::class, 'officerIndex']);
    Route::get('/traffic-note', [InspectionNotesController::class, 'trafficIndex']);

    //ph 16 work
    Route::get('/identify-new-work', [Ph16WorkController::class, 'index']);
    Route::post('/identify-new-work', [Ph16WorkController::class, 'store']);
    Route::get('/ph16Ti', [Ph16WorkController::class, 'Ph16Tidropdown']);

    Route::post('/check-joint-survey', [Ph16WorkController::class, 'checkJointSurvey']);
    Route::post('/joint-survey', [Ph16WorkController::class, 'jointSurveyStore']);
    Route::get('/joint-survey', [Ph16WorkController::class, 'jointSurveyIndex']);

    Route::post('/check-estimate', [Ph16WorkController::class, 'checkEstimate']);
    Route::post('/estimate-store', [Ph16WorkController::class, 'EstimateStore']);
    Route::get('/estimate-list', [Ph16WorkController::class, 'EstimateIndex']);

    Route::post('/check-approval', [Ph16WorkController::class, 'checkApproval']);
    Route::post('/approval-store', [Ph16WorkController::class, 'ApprovalStore']);
    Route::get('/approval-list', [Ph16WorkController::class, 'ApprovalIndex']);
    
    Route::post('/check-finance-vetting', [Ph16WorkController::class, 'checkFinanceVetting']);
    Route::post('/finance-vetting', [Ph16WorkController::class, 'FinanceVettingStore']);
    Route::get('/finance-vetting', [Ph16WorkController::class, 'FinanceVettingIndex']);
    
    Route::post('/check-tendering', [Ph16WorkController::class, 'checkTendering']);
    Route::post('/tendering-store', [Ph16WorkController::class, 'TenderingStore']);
    Route::get('/tendering-list', [Ph16WorkController::class, 'TenderingIndex']);
   
    Route::post('/check-review-estimate', [Ph16WorkController::class, 'checkReviewEstimate']);
    Route::post('/review-estimate', [Ph16WorkController::class, 'ReviewEstimateStore']);
    Route::get('/review-estimate', [Ph16WorkController::class, 'ReviewEstimateIndex']);

    Route::post('/check-esp', [Ph16WorkController::class, 'checkEsp']);
    Route::post('/esp-store', [Ph16WorkController::class, 'EspStore']);
    Route::get('/esp-list', [Ph16WorkController::class, 'EspIndex']);
    Route::post('/check-sip', [Ph16WorkController::class, 'checkSip']);
    Route::post('/sip-store', [Ph16WorkController::class, 'SipStore']);
    Route::get('/sip-list', [Ph16WorkController::class, 'SipIndex']);

    Route::post('/check-execution', [Ph16WorkController::class, 'checkExecution']);
    Route::post('/execution-store', [Ph16WorkController::class, 'ExecutionStore']);
    Route::get('/execution-list', [Ph16WorkController::class, 'ExecutionIndex']);

    Route::post('/check-execution-end', [Ph16WorkController::class, 'checkExecutionEnd']);
    Route::post('/execution-end-store', [Ph16WorkController::class, 'ExecutionEndStore']);
    Route::get('/execution-end-list', [Ph16WorkController::class, 'ExecutionEndIndex']);

    Route::post('/check-variation', [Ph16WorkController::class, 'checkVariation']);
    Route::post('/variation-store', [Ph16WorkController::class, 'VariationStore']);
    Route::get('/variation-list', [Ph16WorkController::class, 'VariationIndex']);

    Route::post('/inspection-log-store', [Ph16WorkController::class, 'InspectionLogStore']);
    Route::get('/inspection-log-list', [Ph16WorkController::class, 'InspectionLogIndex']);

    
});


// Load Stabling Routes
Route::prefix('load-stabling')
    ->middleware([
    'auth:sanctum',
    'inactive.token'
])->group(function () {

    // STATIC ROUTES FIRST
    Route::get('/list', [LoadStablingController::class, 'index']);
    Route::get('/assigned-to-sm', [LoadStablingController::class, 'assignedToSm']);
    Route::get('/list-with-search', [LoadStablingController::class, 'listAndSearch']);
    Route::get('/assigned-clearance', [LoadStablingController::class, 'assignedClearance']);
    Route::get('/old-assigned', [LoadStablingController::class, 'listOldAssignments']);
    Route::post('/store', [LoadStablingController::class, 'store']);
    Route::post('/clearance/store', [LoadStablingController::class, 'storeClearance']);

    // ID ROUTES LAST
    Route::get('/{id}', [LoadStablingController::class, 'show'])->whereNumber('id');
    Route::put('/{id}', [LoadStablingController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [LoadStablingController::class, 'destroy'])->whereNumber('id');
    Route::post('/{id}/sm-checklist', [LoadStablingController::class, 'smChecklist'])->whereNumber('id');
    Route::post('clearance/{id}/update', [LoadStablingController::class, 'updateClearance']);
    Route::post('/{id}/forward-clearance', [LoadStablingController::class, 'forwardClearance'])->whereNumber('id');
    Route::post('/{id}/final-submit', [LoadStablingController::class, 'finalSubmit'])->whereNumber('id');

    // dropdown menu
    Route::get('/employees', [DropdownController::class, 'loadStablingEmployee']);
    Route::get('/coaching-rake', [DropdownController::class, 'coachingRake']);
    Route::get('/goods-rake', [DropdownController::class, 'goodsRake']);

});