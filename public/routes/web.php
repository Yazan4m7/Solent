<?php
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

Route::get('/', function () {
    return redirect('/home');

});
Route::get('/testme', function () {
    return view('enc');

});
Auth::routes();

Route::get('/new-case', [App\Http\Controllers\CaseController::class, 'create'])->name('new-case-view');
Route::get('/login-attempt', '\App\Http\Controllers\Auth\LoginController@authenticate')->name('login-attempt');
Route::get('/t-p', [App\Http\Controllers\CaseController::class, 'teethPopup'])->name('teeth-selecion-popup');
Route::get('/welcome-screen', [App\Http\Controllers\ReportsController::class, 'blankPage'])->name('blank-page');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::middleware('ViewPayments')->group(function (): void {
    Route::get('/payments/index', [App\Http\Controllers\ClientsController::class, 'paymentsIndex'])->name('payments-index');
});

Route::middleware(['web', 'auth'])->group(function (): void {

    Route::get('/oops', [App\Http\Controllers\SystemController::class, 'oopsScreen'])->name('oops-screen');

    Route::get('/home', [App\Http\Controllers\ReportsController::class, 'homeScreen'])->name('homeScreen');

    Route::get('/payments-with-collectors', [App\Http\Controllers\AccountantController::class, 'paymentsWithCollectors'])->name('payments-with-collectors');

    Route::get('/reset-case/{id}/{stage}', [App\Http\Controllers\CaseController::class, 'resetCaseToWaiting'])->name('reset-to-waiting');

    Route::get('/complete-by-admin/{id}/{stage}', [App\Http\Controllers\CaseController::class, 'completeByAdmin'])->name('complete-by-admin');

    Route::get('/QC/send-to-delivery', [App\Http\Controllers\CaseController::class, 'assignToDelivery'])->name('assign-to-delivery-person');

    Route::get('/lab-workflow', [App\Http\Controllers\CaseController::class, 'adminDashboard'])->name('admin-dashboard');

    Route::get('/operations-dashboard', [App\Http\Controllers\CaseController::class, 'adminDashboard_v2'])->name('admin-dashboard-v2');

    Route::get('search', [App\Http\Controllers\CaseController::class, 'globalSearch'])->name('global-search');

    Route::get('quick-access-ds', [App\Http\Controllers\ClientsController::class, 'quickAccessDS'])->name('quick-access-ds');

    Route::middleware('MainDashboard')->group(function (): void {
        Route::get('/main-dashboard', [App\Http\Controllers\ReportsController::class, 'adminHomeScreen'])->name('home');
    });

    Route::middleware('ViewAbutmentsDelivery')->group(function (): void {
    Route::get('/abutments-delivery/index', [App\Http\Controllers\AbutmentsController::class, 'abutmentsDeliveryIndex'])->name('abutments-delivery-index');
    });

    Route::middleware('ReceiveAbutments')->group(function (): void {
        Route::post('/abutments-delivery/receive', [App\Http\Controllers\AbutmentsController::class, 'receiveAbutment'])->name('receive-abutments');
    });

    Route::middleware('OrderAbutments')->group(function (): void {
        Route::get('/abutments-delivery/order{id}', [App\Http\Controllers\AbutmentsController::class, 'orderAbutment'])->name('order-abutments');
    });
    // Users Dashboards
    Route::middleware('Designer')->group(function (): void {
        Route::get('/design/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('designer-cases-list');
    });

    Route::middleware('Miller')->group(function (): void {
        Route::get('/milling/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Miller-cases-list');

        Route::post('/case/milled-externally/', [App\Http\Controllers\CaseController::class, 'externallyMilled'])->name('externally-milled');


    });

    Route::middleware('RejectedCases')->group(function (): void {
        Route::get('/cases/rejected-cases', [App\Http\Controllers\CaseController::class, 'rejectedCases'])->name('rejected-cases');
    });

    Route::middleware('Print3D')->group(function (): void {
        Route::get('/3d-printing/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Print3D-cases-list');
    });

    Route::middleware('SinterFurnace')->group(function (): void {
        Route::get('/Sintering/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('SinterFurnace-cases-list');
    });

    Route::middleware('PressFurnace')->group(function (): void {
        Route::get('/Pressing/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('PressFurnace-cases-list');
    });

    Route::middleware('Finishing')->group(function (): void {
        Route::get('/finishing/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Finishing-cases-list');
    });

    Route::middleware('QC')->group(function (): void {
        Route::get('/QC/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('QC-cases-list');


    });

    Route::middleware('Delivery')->group(function (): void {
        Route::get('/Delivery/{id}', [App\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Delivery-cases-list');
        Route::get('/delivery/accept/{id}', [App\Http\Controllers\CaseController::class, 'acceptCaseByDelivery'])->name('delivery-accept');
        Route::get('/my-collections', [App\Http\Controllers\DeliveryController::class, 'myCollections'])->name('my-collections');

    });

    Route::middleware('CreateCase')->group(function (): void {
        Route::get('/new-case', [App\Http\Controllers\CaseController::class, 'create'])->name('new-case-view');
        Route::post('/new-case-post', [App\Http\Controllers\CaseController::class, 'returnCreate'])->name('new-case-post');
    });

    Route::middleware('EditCases')->group(function (): void {
        Route::get('/case/view-for-editing/{id}', [App\Http\Controllers\CaseController::class, 'returnEdit'])->name('edit-case-view');
        Route::post('/case/edit', [App\Http\Controllers\CaseController::class, 'edit'])->name('edit-case');
    });

    Route::middleware('ViewCasesList')->group(function (): void {
        Route::get('/invoices', [App\Http\Controllers\CaseController::class, 'invoicesList'])->name('invoices-index');
     });

    Route::middleware('ViewVouchers')->group(function (): void {
        Route::get('/voucher/view/case/{id}', [App\Http\Controllers\CaseController::class, 'viewVoucher'])->name('view-voucher');
    });

    Route::middleware('ViewInvoices')->group(function (): void {
        Route::get('/invoices', [App\Http\Controllers\CaseController::class, 'invoicesList'])->name('invoices-index');
        // INVOICE ROUTES
        Route::get('/invoice/{id}', [App\Http\Controllers\CaseController::class, 'viewInvoice'])->name('view-invoice');

    });
    Route::middleware('admin')->group(function () {


        Route::get('/createDummyCase/{id?}/{amount?}', [App\Http\Controllers\CaseController::class, 'createDummyCase'])->name('createDummyCase');

            });
//// External Labs ROUTES
//        Route::post('/lab/new', [App\Http\Controllers\ExLabController::class, 'create'])->name('new-lab');
//        Route::get('/lab/edit/{id}', [App\Http\Controllers\ExLabController::class, 'returnUpdate'])->name('edit-lab-view');
//        Route::post('/lab/edit', [App\Http\Controllers\ExLabController::class, 'update'])->name('edit-lab');
//        Route::get('/lab/new-view', [App\Http\Controllers\ExLabController::class, 'returnCreate'])->name('new-lab-view');
//        Route::get('/external-labs', [App\Http\Controllers\ExLabController::class, 'index'])->name('labs-index');
//// MATERIAL ROUTES
//        Route::post('/material/new', [App\Http\Controllers\MaterialController::class, 'create'])->name('material-add-post');
//        Route::get('/material/edit/{id}', [App\Http\Controllers\MaterialController::class, 'returnUpdate'])->name('edit-material-view');
//        Route::post('/material/edit', [App\Http\Controllers\MaterialController::class, 'update'])->name('edit-material');
//        Route::get('/material/new-view', [App\Http\Controllers\MaterialController::class, 'returnCreate'])->name('material-add');
//        Route::get('/materials', [App\Http\Controllers\MaterialController::class, 'index'])->name('material-index');
//// JOB TYPES ROUTES
//        Route::get('/Job-type/index', [App\Http\Controllers\JobTypeController::class, 'index'])->name("job-type-index");
//        Route::get('/Job-type/new-view', [App\Http\Controllers\JobTypeController::class, 'returnCreate'])->name("new-job-type-view");
//        Route::post('Job-type/new-post', [App\Http\Controllers\JobTypeController::class, 'create'])->name('new-job-type');
//        Route::get('/Job-type/edit-view/{id}', [App\Http\Controllers\JobTypeController::class, 'returnUpdate'])->name("edit-job-type-view");
//        Route::post('Job-type/edit-post', [App\Http\Controllers\JobTypeController::class, 'update'])->name('edit-job-type');
//        Route::get('/users/index', [App\Http\Controllers\UserController::class, 'index'])->name('users-index');
//        Route::get('/users/new', [App\Http\Controllers\UserController::class, 'returnCreate'])->name('new-user-view');
//        Route::post('/users/new-post', [App\Http\Controllers\UserController::class, 'create'])->name('new-user');
//        Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('edit-user-view');
//        Route::post('/users/edit', [App\Http\Controllers\UserController::class, 'update'])->name('edit-user');
//// Clients Routes
//        Route::get('/dentists/new', [App\Http\Controllers\ClientsController::class, 'returnCreate'])->name('new-dentist-view');
//        Route::post('/dentists/new-post', [App\Http\Controllers\ClientsController::class, 'create'])->name('new-dentist');
//        Route::get('/payments/index', [App\Http\Controllers\ClientsController::class, 'paymentsIndex'])->name('payments-index');
//
//        Route::get('/doctors/statement/{id}', [App\Http\Controllers\ClientsController::class, 'statementOfAccount'])->name('client-statement');
//
//        Route::post('/doctors/new-payment', [App\Http\Controllers\ClientsController::class, 'newPayment'])->name('new-payment');
//        Route::post('/doctors/edit', [App\Http\Controllers\ClientsController::class, 'update'])->name('client-update');
//
//
//        Route::get('/doctors/edit/{id}', [App\Http\Controllers\ClientsController::class, 'view'])->name('client-view-edit');
//
//// RECEIPT VOUCHERS
//        Route::get('/voucher/view/case/{id}', [App\Http\Controllers\CaseController::class, 'viewVoucher'])->name('view-voucher');
//
//
//
//    });

    Route::middleware('TakePaymentsFromClients')->group(function (): void {
        Route::get('/delivery/docs/take-payments', [App\Http\Controllers\ClientsController::class, 'index'])->name('clients-index4payment');
        Route::post('/deliver/docs/new-payment', [App\Http\Controllers\ClientsController::class, 'newPayment'])->name('new-payment');
    });
    Route::middleware('DeliveryMonitor')->group(function (): void {
        Route::get('accountant/delivery-cases', [App\Http\Controllers\AccountantController::class, 'deliveryCases4Accountant'])->name('deli-cases-accountant-index');
        Route::get('accountant/receive-voucher/{id}', [App\Http\Controllers\AccountantController::class, 'receiveVoucher'])->name('receive-voucher');
        Route::get('accountant/receive-multiple-vouchers', [App\Http\Controllers\AccountantController::class, 'receiveMultipleVoucher'])->name('receive-multiple-vouchers');


    });
    Route::middleware('ReceivePayments')->group(function (): void {
        Route::get('accountant/receivable-payments', [App\Http\Controllers\AccountantController::class, 'receivablePayments'])->name('receivable-payments-index');
        Route::get('accountant/receive-payment/{id}', [App\Http\Controllers\AccountantController::class, 'receivePayment'])->name('receive-payment');
    });
    Route::middleware('ViewCasesList')->group(function (): void {
        Route::get('/cases', [App\Http\Controllers\CaseController::class, 'index'])->name('cases-index');
    });
    Route::middleware('LabWorkFlow')->group(function (): void {

    });
    Route::middleware('ViewDeliverySchedule')->group(function (): void {
        Route::get('/delivery-schedule', [App\Http\Controllers\CaseController::class, 'deliverySchedule'])->name('delivery-schedule');
        Route::post('/delivery-schedule/update-date', [App\Http\Controllers\CaseController::class, 'updateDeliveryDate'])->name('edit-delivery-date');
    });
    Route::middleware('ViewDoctors')->group(function (): void {
        Route::get('/doctors/index', [App\Http\Controllers\ClientsController::class, 'index'])->name('clients-index');
        Route::get('/clients/statement/{id?}', [App\Http\Controllers\ClientsController::class, 'statementOfAccount'])->name('client-statement-admin');

        Route::post('/doctors/edit', [App\Http\Controllers\ClientsController::class, 'update'])->name('client-update');
        Route::get('/doctors/edit/{id}', [App\Http\Controllers\ClientsController::class, 'view'])->name('client-view-edit');

    });
    Route::middleware('RejectCases')->group(function (): void {
        Route::get('/cases/reject/{id}', [App\Http\Controllers\FailuresController::class, 'rejectionView'])->name('reject-case-view');
        Route::post('/cases/reject', [App\Http\Controllers\FailuresController::class, 'rejectCase'])->name('reject-case');

    });
    Route::middleware('RepeatCases')->group(function (): void {
        Route::get('/cases/repeat/{id}', [App\Http\Controllers\FailuresController::class, 'repeatView'])->name('repeat-case-view');
        Route::post('/cases/repeat', [App\Http\Controllers\FailuresController::class, 'repeatCase'])->name('repeat-case');

    });
    Route::middleware('ModifyCases')->group(function (): void {
        Route::get('/cases/modify/{id}', [App\Http\Controllers\FailuresController::class, 'modifyView'])->name('modify-case-view');
        Route::post('/cases/modify', [App\Http\Controllers\FailuresController::class, 'modifyCase'])->name('modify-case');

    });
    Route::middleware('RedoCases')->group(function (): void {
        Route::get('/cases/redo/{id}', [App\Http\Controllers\FailuresController::class, 'redoView'])->name('redo-case-view');
        Route::post('/cases/redo', [App\Http\Controllers\FailuresController::class, 'redoCase'])->name('redo-case');

    });
    Route::middleware("LockUnlockCases")->group(function (): void{
        Route::get('/cases/lock/{id}', [App\Http\Controllers\CaseController::class, 'lockCase'])->name('lock-case');
        Route::get('/cases/unlock/{id}', [App\Http\Controllers\CaseController::class, 'unlockCase'])->name('unlock-case');

    });
    Route::middleware('admin')->group(function (): void {
        Route::get('/documentation/features', [App\Http\Controllers\DocumentationController::class, 'generatePDF'])->name('documentation.features');

        Route::get('/send-to-delivery', [App\Http\Controllers\CaseController::class, 'sendCaseToDelivery'])->name('send-case-to-delivery');

        Route::get('/mobile-access-stats', [App\Http\Controllers\AdminController::class, 'mobileAccessStats'])->name('mobile-stats-configs');

        Route::get('/intel-dashboard', [App\Http\Controllers\AdminController::class, 'intelDashboard'])->name('intel-dashboard');

        Route::get('/sys/config/update', [App\Http\Controllers\ConfigController::class, 'updateSystemConfig'])->name('update-sys-config');
        Route::get('/sys/config', [App\Http\Controllers\ConfigController::class, 'viewSystemConfig'])->name('sys-config');
         Route::get('/cases/trashed-cases', [App\Http\Controllers\CaseController::class, 'deletedCases'])->name('deleted-cases');
        Route::get('/cases/trashed-cases/restore/{id}', [App\Http\Controllers\CaseController::class, 'restoreDeletedCase'])->name('restore-case');

// DEVICES
        Route::get('/device/index', [App\Http\Controllers\DevicesController::class, 'index'])->name("devices-index");
        Route::get('/device/new-view', [App\Http\Controllers\DevicesController::class, 'returnCreate'])->name("new-device-view");
        Route::post('device/new-post', [App\Http\Controllers\DevicesController::class, 'create'])->name('new-device');
        Route::get('/device/edit-view/{id}', [App\Http\Controllers\DevicesController::class, 'returnUpdate'])->name('edit-device-view');
        Route::get('/device/edit-view/{id}', [App\Http\Controllers\DevicesController::class, 'returnUpdate'])->name('edit-device-view');
       // Route::post('device/edit-post', [App\Http\Controllers\DevicesController::class, 'update'])->name('edit-device');
        Route::post('/device/edit-post', [App\Http\Controllers\DevicesController::class, 'update'])->name('edit-device');
        Route::get('/device/toggle-visibility/{id}', [App\Http\Controllers\DevicesController::class, 'toggleVisibility'])->name('toggle-device-visibility');
        Route::get('/device/delete/{id}', [App\Http\Controllers\DevicesController::class, 'softDelete'])->name('soft-delete-device');

// Fail causes
        Route::get('/f-cause/index', [App\Http\Controllers\FailCausesController::class, 'index'])->name("f-causes-index");
        Route::get('/f-cause/new-view', [App\Http\Controllers\FailCausesController::class, 'returnCreate'])->name("new-f-cause-view");
        Route::post('f-cause/new-post', [App\Http\Controllers\FailCausesController::class, 'create'])->name('new-f-cause');
        Route::get('/f-cause/edit-view/{id}', [App\Http\Controllers\FailCausesController::class, 'returnUpdate'])->name('edit-f-cause-view');
        Route::post('f-cause/edit-post', [App\Http\Controllers\FailCausesController::class, 'update'])->name('edit-f-cause');

        Route::post('/admin/t-env/sendcase', [App\Http\Controllers\TestingController::class, 'createAndSendCaseTo'])->name('create-and-send-case-to');
// TAGS
        Route::get('/tags/index', [App\Http\Controllers\TagsController::class, 'index'])->name("tags-index");
        Route::get('/tags/new-view', [App\Http\Controllers\TagsController::class, 'returnCreate'])->name("new-tag-view");
        Route::post('tags/new-post', [App\Http\Controllers\TagsController::class, 'create'])->name('new-tag');
        Route::get('/tags/edit-view/{id}', [App\Http\Controllers\TagsController::class, 'returnUpdate'])->name('edit-tag-view');
        Route::post('tags/edit-post', [App\Http\Controllers\TagsController::class, 'update'])->name('edit-tag');

// External Labs ROUTES
        Route::post('/lab/new', [App\Http\Controllers\ExLabController::class, 'create'])->name('new-lab');
        Route::get('/lab/edit/{id}', [App\Http\Controllers\ExLabController::class, 'returnUpdate'])->name('edit-lab-view');
        Route::post('/lab/edit', [App\Http\Controllers\ExLabController::class, 'update'])->name('edit-lab');
        Route::get('/lab/new-view', [App\Http\Controllers\ExLabController::class, 'returnCreate'])->name('new-lab-view');
        Route::get('/external-labs', [App\Http\Controllers\ExLabController::class, 'index'])->name('labs-index');
// MATERIAL ROUTES
        Route::post('/material/new', [App\Http\Controllers\MaterialController::class, 'create'])->name('material-add-post');
        Route::get('/material/edit/{id}', [App\Http\Controllers\MaterialController::class, 'returnUpdate'])->name('edit-material-view');
        Route::post('/material/edit', [App\Http\Controllers\MaterialController::class, 'update'])->name('edit-material');
        Route::get('/material/new-view', [App\Http\Controllers\MaterialController::class, 'returnCreate'])->name('material-add');
        Route::get('/materials', [App\Http\Controllers\MaterialController::class, 'index'])->name('material-index');
// JOB TYPES ROUTES
        Route::get('/Job-type/index', [App\Http\Controllers\JobTypeController::class, 'index'])->name("job-type-index");
        Route::get('/Job-type/new-view', [App\Http\Controllers\JobTypeController::class, 'returnCreate'])->name("new-job-type-view");
        Route::post('Job-type/new-post', [App\Http\Controllers\JobTypeController::class, 'create'])->name('new-job-type');
        Route::get('/Job-type/edit-view/{id}', [App\Http\Controllers\JobTypeController::class, 'returnUpdate'])->name("edit-job-type-view");
        Route::post('Job-type/edit-post', [App\Http\Controllers\JobTypeController::class, 'update'])->name('edit-job-type');

        // USERS ROUTES
        Route::get('/users/index', [App\Http\Controllers\UserController::class, 'index'])->name('users-index');
        Route::get('/users/new', [App\Http\Controllers\UserController::class, 'returnCreate'])->name('new-user-view');
        Route::post('/users/new-post', [App\Http\Controllers\UserController::class, 'create'])->name('new-user');
        Route::get('/users/edit/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('edit-user-view');
        Route::post('/users/edit', [App\Http\Controllers\UserController::class, 'update'])->name('edit-user');
        Route::get('/users/delete/{id}', [App\Http\Controllers\UserController::class, 'softDelete'])->name('soft-delete-user');
// Clients Routes
        Route::get('/dentists/new', [App\Http\Controllers\ClientsController::class, 'returnCreate'])->name('new-dentist-view');
        Route::post('/dentists/new-post', [App\Http\Controllers\ClientsController::class, 'create'])->name('new-dentist');
        Route::post('/dentists/account-discount', [App\Http\Controllers\ClientsController::class, 'accountDiscount'])->name('account-discount');
        Route::get('/dentists/delete/{id}', [App\Http\Controllers\ClientsController::class, 'softDelete'])->name('soft-delete-client');


        Route::get('/system/switch_env', [App\Http\Controllers\SystemController::class, 'switchEnvironment'])->name('switch-env');

        Route::get('/dentist/invoices', [App\Http\Controllers\ClientsController::class, 'doctorInvoices'])->name('dentist-invoices');
        Route::get('/dentist/cases', [App\Http\Controllers\ClientsController::class, 'doctorCases'])->name('dentist-cases');
        Route::get('/dentist/payments', [App\Http\Controllers\ClientsController::class, 'doctorPayments'])->name('dentist-payments');
        Route::get('/payments/delete{id}', [App\Http\Controllers\ClientsController::class, 'deletePayment'])->name('delete-payment');

        // MEDIA ROUTES
        Route::get('/media/index', [App\Http\Controllers\MediaController::class, 'index'])->name('media-index');
        Route::get('/media/new', [App\Http\Controllers\MediaController::class, 'create'])->name("create-media");
        Route::post('media/new-post', [App\Http\Controllers\MediaController::class, 'createPost'])->name('create-media-post');
        Route::get('/media/edit/{id}', [App\Http\Controllers\MediaController::class, 'edit'])->name('edit-media');
        Route::post('media/edit-post', [App\Http\Controllers\MediaController::class, 'editPost'])->name('edit-media-post');
        Route::get('/media/delete{id}', [App\Http\Controllers\MediaController::class, 'delete'])->name('delete-media');

    // IMPLANTS ROUTES
        Route::get('/implants/index', [App\Http\Controllers\ImplantsController::class, 'index'])->name("implants-index");
        Route::get('/implants/new-view', [App\Http\Controllers\ImplantsController::class, 'returnCreate'])->name("new-implant-view");
        Route::post('implants/new-post', [App\Http\Controllers\ImplantsController::class, 'create'])->name('new-implant');
        Route::get('/implants/edit-view/{id}', [App\Http\Controllers\ImplantsController::class, 'returnUpdate'])->name("edit-implant-view");
        Route::post('implants/edit-post', [App\Http\Controllers\ImplantsController::class, 'update'])->name('edit-implant');

        // ABUTMENTS ROUTES
        Route::get('/abutment/index', [App\Http\Controllers\AbutmentsController::class, 'index'])->name("abutments-index");
        Route::get('/abutment/new-view', [App\Http\Controllers\AbutmentsController::class, 'returnCreate'])->name("new-abutment-view");
        Route::post('abutment/new-post', [App\Http\Controllers\AbutmentsController::class, 'create'])->name('new-abutment');
        Route::get('/abutment/edit-view/{id}', [App\Http\Controllers\AbutmentsController::class, 'returnUpdate'])->name("edit-abutment-view");
        Route::post('abutment/edit-post', [App\Http\Controllers\AbutmentsController::class, 'update'])->name('edit-abutment');

        Route::get('/se', [App\Http\Controllers\ConfigController::class, 'switchEnvironment'])->name('switch_env');

    });
    Route::middleware('ViewCasesMonitor')->group(function (): void {
        Route::get('/cases-monitor', [App\Http\Controllers\CaseController::class, 'viewSingleScreen'])->name('view-cases-monitor');
    });
    Route::middleware('Reports')->group(function (): void {
        Route::get('/reports/implants', [App\Http\Controllers\ReportsController::class, 'implantsReport'])->name('implants-report');
        Route::get('/reports/QC', [App\Http\Controllers\ReportsController::class, 'QCReport'])->name('QC-report');
        Route::get('/reports/job-types', [App\Http\Controllers\ReportsController::class, 'jobTypeReport'])->name('job-types-report');
        Route::get('/reports/num-of-units', [App\Http\Controllers\ReportsController::class, 'numOfUnitsReport'])->name('num-of-units-report');
        Route::get('/reports/repeats', [App\Http\Controllers\ReportsController::class, 'repeatsReport'])->name('repeats-report');
        Route::get('/reports/material', [App\Http\Controllers\ReportsController::class, 'materialReport'])->name('materials-report');

    });
    Route::post('/detect-new-job-stage', [App\Http\Controllers\CaseController::class, 'detectNewJobStage'])->name('detect-newJob-stage');

    // CASES ROUTES
    Route::get('/view/{id}/{stage?}', [App\Http\Controllers\CaseController::class, 'view'])->name('view-case');
    Route::get('/case/delete{id}', [App\Http\Controllers\CaseController::class, 'deleteCase'])->name('delete-case');

    // CASE FLOW ROUTES
    Route::get('/assign-case/{caseId}/{stage}', [App\Http\Controllers\CaseController::class, 'assignToMe'])->name('assign-to-me');
    Route::get('/finish-case/{caseId}/{stage}', [App\Http\Controllers\CaseController::class, 'finishCaseStage'])->name('finish-case');
    Route::get('/assign-and-finish-case/{caseId}/{stage}', [App\Http\Controllers\CaseController::class, 'assignAndFinish'])->name('assign-and-finish');
    Route::get('/finish-case/{caseId}', [App\Http\Controllers\CaseController::class, 'deliveredInBox'])->name('delivered-in-box');
    Route::post('/set-multiple-cases', [App\Http\Controllers\OperationsUpgrade::class, 'setOnDevice'])->name('set-multiple-cases');
    Route::post('/activate-multiple-cases', [App\Http\Controllers\OperationsUpgrade::class, 'activateMultipleCases'])->name('activate-multiple-cases');
    Route::post('/finish-multiple-cases', [App\Http\Controllers\OperationsUpgrade::class, 'finishMultipleCases'])->name('finish-multiple-cases');
    Route::post('/assign-deliveries', [App\Http\Controllers\OperationsUpgrade::class, 'assignCasesToDelivery'])->name('assign-multiple-deliveries');
    Route::post('/set-cases-on-printer', [App\Http\Controllers\OperationsUpgrade::class, 'setJobsOnDevice'])->name('set-cases-on-printer');

    // 3D Printing Build routes
    Route::post('/activate-3d-builds', [App\Http\Controllers\OperationsUpgrade::class, 'activate3DBuilds'])->name('activate-3d-builds');
    Route::post('/finish-3d-builds', [App\Http\Controllers\OperationsUpgrade::class, 'finish3DBuilds'])->name('finish-3d-builds');

    // Notes routes
    Route::post('/case/note', [App\Http\Controllers\CaseController::class, 'addNote'])->name('new-note');
    Route::get('/send-notification', [App\Http\Controllers\CaseController::class, 'sendNotification'])->name('new-notification');
    Route::get('/jwt', [App\Http\Controllers\CaseController::class, 'generateJWT'])->name('generate-jwt');
    Route::get('/sent-test-notification', [App\Http\Controllers\CaseController::class, 'testNotification'])->name('test-notificaion');
    Route::get('/tf/{x?}', [App\Http\Controllers\CaseController::class, 'testNotification'])->where('x', '.*')->name('test-notification-by-type');


    Route::get('/finish-case-completely/{caseId}', [App\Http\Controllers\CaseController::class, 'finishCaseCompletely'])->name('finish-case-completely');

});



