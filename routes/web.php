<?php
Route::get('/', function () {
    return redirect('/home');

});
Route::get('/testme', function () {
    return view('enc');

});
Auth::routes(['register' => false]);

Route::get('/new-case', [App\Modules\Cases\Http\Controllers\CaseController::class, 'create'])->name('new-case-view');
Route::get('/login-attempt', '\App\Http\Controllers\Auth\LoginController@authenticate')->name('login-attempt');
Route::get('/t-p', [App\Modules\Cases\Http\Controllers\CaseController::class, 'teethPopup'])->name('teeth-selecion-popup');
Route::get('/welcome-screen', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'blankPage'])->name('blank-page');
Route::post('logout', '\App\Http\Controllers\Auth\LoginController@logout')->name('logout');

Route::middleware('ViewPayments')->group(function (): void {
    Route::get('/payments/index', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'paymentsIndex'])->name('payments-index');
});

Route::middleware(['web', 'auth'])->group(function (): void {

    Route::get('/oops', [App\Modules\System\Http\Controllers\SystemController::class, 'oopsScreen'])->name('oops-screen');

    Route::get('/home', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'homeScreen'])->name('homeScreen');

    Route::middleware('platform.admin')->prefix('system')->name('system.')->group(function (): void {
        Route::get('/tenants', [App\Modules\System\Http\Controllers\TenantController::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [App\Modules\System\Http\Controllers\TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants', [App\Modules\System\Http\Controllers\TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}', [App\Modules\System\Http\Controllers\TenantController::class, 'show'])->name('tenants.show');
    });

    Route::get('/payments-with-collectors', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'paymentsWithCollectors'])->name('payments-with-collectors');

    Route::get('/reset-case/{id}/{stage}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'resetCaseToWaiting'])->name('reset-to-waiting');

    Route::get('/complete-by-admin/{id}/{stage}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'completeByAdmin'])->name('complete-by-admin');

    Route::get('/QC/send-to-delivery', [App\Modules\Cases\Http\Controllers\CaseController::class, 'assignToDelivery'])->name('assign-to-delivery-person');

    Route::post('/assign-to-stage-employee', [App\Modules\Cases\Http\Controllers\CaseController::class, 'assignToStageEmployee'])->name('assign-to-stage-employee');

    Route::get('/lab-workflow', [App\Modules\Cases\Http\Controllers\CaseController::class, 'adminDashboard'])->name('admin-dashboard');

    Route::get('/operations-dashboard', [App\Modules\Cases\Http\Controllers\CaseController::class, 'adminDashboard_v2'])->name('admin-dashboard-v2');

    Route::get('search', [App\Modules\Cases\Http\Controllers\CaseController::class, 'globalSearch'])->name('global-search');

    Route::get('quick-access-ds', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'quickAccessDS'])->name('quick-access-ds');

    Route::middleware('MainDashboard')->group(function (): void {
        Route::get('/main-dashboard', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'adminHomeScreen'])->name('home');
    });

    // COMMENTED OUT: Abutments Delivery Feature - Can be reverted if needed
    // Route::middleware('ViewAbutmentsDelivery')->group(function (): void {
    // Route::get('/abutments-delivery/index', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'abutmentsDeliveryIndex'])->name('abutments-delivery-index');
    // });

    // Route::middleware('ReceiveAbutments')->group(function (): void {
    //     Route::post('/abutments-delivery/receive', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'receiveAbutment'])->name('receive-abutments');
    // });

    // Route::middleware('OrderAbutments')->group(function (): void {
    //     Route::get('/abutments-delivery/order{id}', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'orderAbutment'])->name('order-abutments');
    // });
    // Users Dashboards
    Route::middleware('Designer')->group(function (): void {
        Route::get('/design/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('designer-cases-list');
    });

    Route::middleware('Miller')->group(function (): void {
        Route::get('/milling/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Miller-cases-list');

        Route::post('/case/milled-externally/', [App\Modules\Cases\Http\Controllers\CaseController::class, 'externallyMilled'])->name('externally-milled');


    });

    Route::middleware('RejectedCases')->group(function (): void {
        Route::get('/cases/rejected-cases', [App\Modules\Cases\Http\Controllers\CaseController::class, 'rejectedCases'])->name('rejected-cases');
    });

    Route::middleware('Print3D')->group(function (): void {
        Route::get('/3d-printing/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Print3D-cases-list');
    });

    Route::middleware('SinterFurnace')->group(function (): void {
        Route::get('/Sintering/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('SinterFurnace-cases-list');
    });

    Route::middleware('PressFurnace')->group(function (): void {
        Route::get('/Pressing/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('PressFurnace-cases-list');
    });

    Route::middleware('Finishing')->group(function (): void {
        Route::get('/finishing/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Finishing-cases-list');
    });

    Route::middleware('QC')->group(function (): void {
        Route::get('/QC/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('QC-cases-list');


    });

    Route::middleware('Delivery')->group(function (): void {
        Route::get('/Delivery/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'employeeDashboard'])->name('Delivery-cases-list');
        Route::get('/delivery/accept/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'acceptCaseByDelivery'])->name('delivery-accept');
        // COMMENTED OUT: My Collections Feature - Can be reverted if needed
        // Route::get('/my-collections', [App\Modules\Delivery\Http\Controllers\DeliveryController::class, 'myCollections'])->name('my-collections');

    });

    Route::middleware('CreateCase')->group(function (): void {
        Route::get('/new-case', [App\Modules\Cases\Http\Controllers\CaseController::class, 'create'])->name('new-case-view');
        Route::post('/new-case-post', [App\Modules\Cases\Http\Controllers\CaseController::class, 'returnCreate'])->name('new-case-post');
    });

    Route::middleware('EditCases')->group(function (): void {
        Route::get('/case/view-for-editing/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'returnEdit'])->name('edit-case-view');
        Route::post('/case/edit', [App\Modules\Cases\Http\Controllers\CaseController::class, 'edit'])->name('edit-case');
        Route::delete('/attachment/delete/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'deleteAttachment'])->name('delete-attachment');
    });

    Route::middleware('ViewCasesList')->group(function (): void {
        Route::get('/invoices', [App\Modules\Cases\Http\Controllers\CaseController::class, 'invoicesList'])->name('invoices-index');
    });

    Route::middleware('ViewVouchers')->group(function (): void {
        Route::get('/voucher/view/case/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'viewVoucher'])->name('view-voucher');
    });

    Route::middleware('ViewInvoices')->group(function (): void {
        Route::get('/invoices', [App\Modules\Cases\Http\Controllers\CaseController::class, 'invoicesList'])->name('invoices-index');
        // INVOICE ROUTES
        Route::get('/invoice/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'viewInvoice'])->name('view-invoice');

    });
    Route::middleware('admin')->group(function () {


        Route::get('/createDummyCase/{id?}/{amount?}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'createDummyCase'])->name('createDummyCase');

    });
//// External Labs ROUTES
//        Route::post('/lab/new', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'create'])->name('new-lab');
//        Route::get('/lab/edit/{id}', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'returnUpdate'])->name('edit-lab-view');
//        Route::post('/lab/edit', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'update'])->name('edit-lab');
//        Route::get('/lab/new-view', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'returnCreate'])->name('new-lab-view');
//        Route::get('/external-labs', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'index'])->name('labs-index');
//// MATERIAL ROUTES
//        Route::post('/material/new', [App\Modules\Materials\Http\Controllers\MaterialController::class, 'create'])->name('material-add-post');
//        Route::get('/material/edit/{id}', [App\Modules\Materials\Http\Controllers\MaterialController::class, 'returnUpdate'])->name('edit-material-view');
//        Route::post('/material/edit', [App\Modules\Materials\Http\Controllers\MaterialController::class, 'update'])->name('edit-material');
//        Route::get('/material/new-view', [App\Modules\Materials\Http\Controllers\MaterialController::class, 'returnCreate'])->name('material-add');
//        Route::get('/materials', [App\Modules\Materials\Http\Controllers\MaterialController::class, 'index'])->name('material-index');
//// JOB TYPES ROUTES
//        Route::get('/Job-type/index', [App\Modules\JobTypes\Http\Controllers\JobTypeController::class, 'index'])->name("job-type-index");
//        Route::get('/Job-type/new-view', [App\Modules\JobTypes\Http\Controllers\JobTypeController::class, 'returnCreate'])->name("new-job-type-view");
//        Route::post('Job-type/new-post', [App\Modules\JobTypes\Http\Controllers\JobTypeController::class, 'create'])->name('new-job-type');
//        Route::get('/Job-type/edit-view/{id}', [App\Modules\JobTypes\Http\Controllers\JobTypeController::class, 'returnUpdate'])->name("edit-job-type-view");
//        Route::post('Job-type/edit-post', [App\Modules\JobTypes\Http\Controllers\JobTypeController::class, 'update'])->name('edit-job-type');
//        Route::get('/users/index', [App\Modules\Users\Http\Controllers\UserController::class, 'index'])->name('users-index');
//        Route::get('/users/new', [App\Modules\Users\Http\Controllers\UserController::class, 'returnCreate'])->name('new-user-view');
//        Route::post('/users/new-post', [App\Modules\Users\Http\Controllers\UserController::class, 'create'])->name('new-user');
//        Route::get('/users/edit/{id}', [App\Modules\Users\Http\Controllers\UserController::class, 'edit'])->name('edit-user-view');
//        Route::post('/users/edit', [App\Modules\Users\Http\Controllers\UserController::class, 'update'])->name('edit-user');
//// Clients Routes
//        Route::get('/dentists/new', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'returnCreate'])->name('new-dentist-view');
//        Route::post('/dentists/new-post', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'create'])->name('new-dentist');
//        Route::get('/payments/index', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'paymentsIndex'])->name('payments-index');
//
//        Route::get('/doctors/statement/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'statementOfAccount'])->name('client-statement');
//
        Route::post('/doctors/new-payment', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'newPayment'])->name('new-payment');
//        Route::post('/doctors/edit', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'update'])->name('client-update');
//
//
//        Route::get('/doctors/edit/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'view'])->name('client-view-edit');
//
//// RECEIPT VOUCHERS
//        Route::get('/voucher/view/case/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'viewVoucher'])->name('view-voucher');
//
//
//
//    });

    // COMMENTED OUT: Delivery Monitor Feature - Can be reverted if needed
    // Route::middleware('DeliveryMonitor')->group(function (): void {
    //     Route::get('accountant/delivery-cases', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'deliveryCases4Accountant'])->name('deli-cases-accountant-index');
    //     Route::get('accountant/receive-voucher/{id}', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'receiveVoucher'])->name('receive-voucher');
    //     Route::get('accountant/receive-multiple-vouchers', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'receiveMultipleVoucher'])->name('receive-multiple-vouchers');
    // });

    // COMMENTED OUT: Receive Payments Feature - Can be reverted if needed
     Route::middleware('ReceivePayments')->group(function (): void {
         Route::get('accountant/receivable-payments', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'receivablePayments'])->name('receivable-payments-index');
         Route::get('accountant/receive-payment/{id}', [App\Modules\Accountant\Http\Controllers\AccountantController::class, 'receivePayment'])->name('receive-payment');
     });
    Route::middleware('ViewCasesList')->group(function (): void {
        Route::get('/cases', [App\Modules\Cases\Http\Controllers\CaseController::class, 'index'])->name('cases-index');
    });
    Route::middleware('LabWorkFlow')->group(function (): void {

    });
    Route::middleware('ViewDeliverySchedule')->group(function (): void {
        Route::get('/delivery-schedule', [App\Modules\Cases\Http\Controllers\CaseController::class, 'deliverySchedule'])->name('delivery-schedule');
        Route::post('/delivery-schedule/update-date', [App\Modules\Cases\Http\Controllers\CaseController::class, 'updateDeliveryDate'])->name('edit-delivery-date');
    });
    Route::middleware('ViewDoctors')->group(function (): void {
        Route::get('/doctors/index', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'index'])->name('clients-index');
        Route::get('/clients/statement/{id?}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'statementOfAccount'])->name('client-statement-admin');
        Route::get('/clients/payment-invoice/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'paymentInvoice'])->name('client-payment-invoice');

        Route::post('/doctors/edit', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'update'])->name('client-update');
        Route::get('/doctors/edit/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'view'])->name('client-view-edit');
        Route::post('/clients/update-opening-balance', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'updateOpeningBalance'])->name('clients.update-opening-balance');
        Route::post('/clients/set-opening-balance', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'setOpeningBalance'])->name('clients.set-opening-balance');

    });
    Route::middleware('RejectCases')->group(function (): void {
        Route::get('/cases/reject/{id}', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'rejectionView'])->name('reject-case-view');
        Route::post('/cases/reject', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'rejectCase'])->name('reject-case');

    });
    Route::middleware('RepeatCases')->group(function (): void {
        Route::get('/cases/repeat/{id}', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'repeatView'])->name('repeat-case-view');
        Route::post('/cases/repeat', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'repeatCase'])->name('repeat-case');

    });
    Route::middleware('ModifyCases')->group(function (): void {
        Route::get('/cases/modify/{id}', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'modifyView'])->name('modify-case-view');
        Route::post('/cases/modify', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'modifyCase'])->name('modify-case');

    });
    Route::middleware('RedoCases')->group(function (): void {
        Route::get('/cases/redo/{id}', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'redoView'])->name('redo-case-view');
        Route::post('/cases/redo', [App\Modules\Failures\Http\Controllers\FailuresController::class, 'redoCase'])->name('redo-case');

    });
    Route::middleware("LockUnlockCases")->group(function (): void {
        Route::get('/cases/lock/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'lockCase'])->name('lock-case');
        Route::get('/cases/unlock/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'unlockCase'])->name('unlock-case');

    });
    Route::middleware('admin')->group(function (): void {
        Route::get('/documentation/features', [App\Modules\Documentation\Http\Controllers\DocumentationController::class, 'generatePDF'])->name('documentation.features');

        Route::get('/send-to-delivery', [App\Modules\Cases\Http\Controllers\CaseController::class, 'sendCaseToDelivery'])->name('send-case-to-delivery');

        Route::get('/mobile-access-stats', [App\Modules\Admin\Http\Controllers\AdminController::class, 'mobileAccessStats'])->name('mobile-stats-configs');

        Route::get('/intel-dashboard', [App\Modules\Admin\Http\Controllers\AdminController::class, 'intelDashboard'])->name('intel-dashboard');

        Route::get('/sys/config/update', [App\Modules\Config\Http\Controllers\ConfigController::class, 'updateSystemConfig'])->name('update-sys-config');
        Route::get('/sys/config', [App\Modules\Config\Http\Controllers\ConfigController::class, 'viewSystemConfig'])->name('sys-config');
        Route::get('/cases/trashed-cases', [App\Modules\Cases\Http\Controllers\CaseController::class, 'deletedCases'])->name('deleted-cases');
        Route::get('/cases/trashed-cases/restore/{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'restoreDeletedCase'])->name('restore-case');

// Fail causes
        Route::get('/f-cause/index', [App\Modules\Failures\Http\Controllers\FailCausesController::class, 'index'])->name("f-causes-index");
        Route::get('/f-cause/new-view', [App\Modules\Failures\Http\Controllers\FailCausesController::class, 'returnCreate'])->name("new-f-cause-view");
        Route::post('f-cause/new-post', [App\Modules\Failures\Http\Controllers\FailCausesController::class, 'create'])->name('new-f-cause');
        Route::get('/f-cause/edit-view/{id}', [App\Modules\Failures\Http\Controllers\FailCausesController::class, 'returnUpdate'])->name('edit-f-cause-view');
        Route::post('f-cause/edit-post', [App\Modules\Failures\Http\Controllers\FailCausesController::class, 'update'])->name('edit-f-cause');

        Route::post('/admin/t-env/sendcase', [App\Modules\Testing\Http\Controllers\TestingController::class, 'createAndSendCaseTo'])->name('create-and-send-case-to');
// TAGS
        Route::get('/tags/index', [App\Modules\Tags\Http\Controllers\TagsController::class, 'index'])->name("tags-index");
        Route::get('/tags/new-view', [App\Modules\Tags\Http\Controllers\TagsController::class, 'returnCreate'])->name("new-tag-view");
        Route::post('tags/new-post', [App\Modules\Tags\Http\Controllers\TagsController::class, 'create'])->name('new-tag');
        Route::get('/tags/edit-view/{id}', [App\Modules\Tags\Http\Controllers\TagsController::class, 'returnUpdate'])->name('edit-tag-view');
        Route::post('tags/edit-post', [App\Modules\Tags\Http\Controllers\TagsController::class, 'update'])->name('edit-tag');

// COMMENTED OUT: External Labs Feature - Can be reverted if needed
        // Route::post('/lab/new', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'create'])->name('new-lab');
        // Route::get('/lab/edit/{id}', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'returnUpdate'])->name('edit-lab-view');
        // Route::post('/lab/edit', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'update'])->name('edit-lab');
        // Route::get('/lab/new-view', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'returnCreate'])->name('new-lab-view');
        // Route::get('/external-labs', [App\Modules\Labs\Http\Controllers\ExLabController::class, 'index'])->name('labs-index');
// MATERIAL ROUTES -> moved to App\Modules\Materials\Routes\web.php
// JOB TYPES ROUTES -> moved to App\Modules\JobTypes\Routes\web.php

        // USERS ROUTES
        Route::get('/users/index', [App\Modules\Users\Http\Controllers\UserController::class, 'index'])->name('users-index');
        Route::get('/users/new', [App\Modules\Users\Http\Controllers\UserController::class, 'returnCreate'])->name('new-user-view');
        Route::post('/users/new-post', [App\Modules\Users\Http\Controllers\UserController::class, 'create'])->name('new-user');
        Route::get('/users/edit/{id}', [App\Modules\Users\Http\Controllers\UserController::class, 'edit'])->name('edit-user-view');
        Route::post('/users/edit', [App\Modules\Users\Http\Controllers\UserController::class, 'update'])->name('edit-user');
        Route::get('/users/delete/{id}', [App\Modules\Users\Http\Controllers\UserController::class, 'softDelete'])->name('soft-delete-user');
// Clients Routes
        Route::get('/dentists/new', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'returnCreate'])->name('new-dentist-view');
        Route::post('/dentists/new-post', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'create'])->name('new-dentist');
        Route::post('/dentists/account-discount', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'accountDiscount'])->name('account-discount');
        Route::get('/dentists/delete/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'softDelete'])->name('soft-delete-client');
        Route::post('/clients/delete', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'delete'])->name('client-delete');
        Route::post('/clients/disable', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'disable'])->name('client-disable');
        Route::post('/clients/enable', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'enable'])->name('client-enable');
        Route::get('/clients/toggle-active/{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'toggleActive'])->name('toggle-client-active');


        Route::get('/system/switch_env', [App\Modules\System\Http\Controllers\SystemController::class, 'switchEnvironment'])->name('switch-env');

        Route::get('/dentist/invoices', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'doctorInvoices'])->name('dentist-invoices');
        Route::get('/dentist/cases', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'doctorCases'])->name('dentist-cases');
        Route::get('/dentist/payments', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'doctorPayments'])->name('dentist-payments');
        Route::get('/payments/delete{id}', [App\Modules\Clients\Http\Controllers\ClientsController::class, 'deletePayment'])->name('delete-payment');


        // IMPLANTS ROUTES
        Route::get('/implants/index', [App\Modules\Implants\Http\Controllers\ImplantsController::class, 'index'])->name("implants-index");
        Route::get('/implants/new-view', [App\Modules\Implants\Http\Controllers\ImplantsController::class, 'returnCreate'])->name("new-implant-view");
        Route::post('implants/new-post', [App\Modules\Implants\Http\Controllers\ImplantsController::class, 'create'])->name('new-implant');
        Route::get('/implants/edit-view/{id}', [App\Modules\Implants\Http\Controllers\ImplantsController::class, 'returnUpdate'])->name("edit-implant-view");
        Route::post('implants/edit-post', [App\Modules\Implants\Http\Controllers\ImplantsController::class, 'update'])->name('edit-implant');

        // COMMENTED OUT: Abutments Configuration Feature - Can be reverted if needed
        // Route::get('/abutment/index', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'index'])->name("abutments-index");
        // Route::get('/abutment/new-view', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'returnCreate'])->name("new-abutment-view");
        // Route::post('abutment/new-post', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'create'])->name('new-abutment');
        // Route::get('/abutment/edit-view/{id}', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'returnUpdate'])->name("edit-abutment-view");
        // Route::post('abutment/edit-post', [App\Modules\Abutments\Http\Controllers\AbutmentsController::class, 'update'])->name('edit-abutment');

        Route::get('/se', [App\Modules\Config\Http\Controllers\ConfigController::class, 'switchEnvironment'])->name('switch_env');

    });
    Route::middleware('ViewCasesMonitor')->group(function (): void {
        Route::get('/cases-monitor', [App\Modules\Cases\Http\Controllers\CaseController::class, 'viewSingleScreen'])->name('view-cases-monitor');
    });
    Route::middleware('Reports')->group(function (): void {
        Route::get('/reports/implants', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'implantsReport'])->name('implants-report');
        Route::get('/reports/QC', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'QCReport'])->name('QC-report');
        Route::get('/reports/job-types', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'jobTypeReport'])->name('job-types-report');
        Route::get('/reports/num-of-units', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'numOfUnitsReport'])->name('num-of-units-report');
        Route::get('/reports/repeats', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'repeatsReport'])->name('repeats-report');
        Route::get('/reports/material', [App\Modules\Reports\Http\Controllers\ReportsController::class, 'materialReport'])->name('materials-report');

    });
    Route::post('/detect-new-job-stage', [App\Modules\Cases\Http\Controllers\CaseController::class, 'detectNewJobStage'])->name('detect-newJob-stage');

    // CASES ROUTES
    Route::get('view-case/{id}/{stage?}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'view'])->name('view-case');
    Route::get('case/{id}/print-invoice', [App\Modules\Cases\Http\Controllers\CaseController::class, 'printInvoice'])->name('print-invoice');
    Route::get('/case/delete{id}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'deleteCase'])->name('delete-case');

    // CASE FLOW ROUTES
    Route::get('/assign-case/{caseId}/{stage}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'assignToMe'])->name('assign-to-me');
    Route::get('/finish-case/{caseId}/{stage}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'finishCaseStage'])->name('finish-case');
    Route::get('/assign-and-finish-case/{caseId}/{stage}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'assignAndFinish'])->name('assign-and-finish');
    Route::get('/finish-case/{caseId}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'deliveredInBox'])->name('delivered-in-box');
    Route::post('/set-multiple-cases', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'setOnDevice'])->name('set-multiple-cases');
    Route::post('/activate-multiple-cases', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'activateMultipleCases'])->name('activate-multiple-cases');
    Route::post('/finish-multiple-cases', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'finishMultipleCases'])->name('finish-multiple-cases');
    Route::post('/assign-deliveries', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'assignCasesToDelivery'])->name('assign-multiple-deliveries');
    Route::post('/assign-employees', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'assignCasesToEmployee'])->name('assign-multiple-employees');
    Route::post('/set-cases-on-printer', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'setJobsOnDevice'])->name('set-cases-on-printer');

    Route::post('/bulk-assign-printing', [\App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'bulkAssignPrinting'])->name('bulk-assign-printing');
    Route::post('/bulk-complete-printing', [\App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'bulkCompletePrinting'])->name('bulk-complete-printing');

    // 3D Printing Build routes
    Route::post('/activate-3d-builds', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'activate3DBuilds'])->name('activate-3d-builds');
    Route::post('/finish-3d-builds', [App\Modules\Cases\Http\Controllers\OperationsUpgrade::class, 'finish3DBuilds'])->name('finish-3d-builds');

    // Notes routes
    Route::post('/case/note', [App\Modules\Cases\Http\Controllers\CaseController::class, 'addNote'])->name('new-note');
    Route::get('/send-notification', [App\Modules\Cases\Http\Controllers\CaseController::class, 'sendNotification'])->name('new-notification');
    Route::get('/jwt', [App\Modules\Cases\Http\Controllers\CaseController::class, 'generateJWT'])->name('generate-jwt');
    Route::get('/sent-test-notification', [App\Modules\Cases\Http\Controllers\CaseController::class, 'testNotification'])->name('test-notificaion');
    Route::get('/tf/{x?}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'testNotification'])->where('x', '.*')->name('test-notification-by-type');


    Route::get('/finish-case-completely/{caseId}', [App\Modules\Cases\Http\Controllers\CaseController::class, 'finishCaseCompletely'])->name('finish-case-completely');


    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/login', [App\Modules\Portal\Http\Controllers\PortalController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [App\Modules\Portal\Http\Controllers\PortalController::class, 'login']);
        Route::post('/logout', [App\Modules\Portal\Http\Controllers\PortalController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [App\Modules\Portal\Http\Controllers\PortalController::class, 'dashboard'])->name('dashboard')->middleware('auth:clients');
    });

Route::get('/create-invoice', [App\Modules\Cases\Http\Controllers\CaseController::class, 'createInvoice'])->name('create-invoice');
});

Route::fallback(function () {
    return response()->view('errors.generic', ['statusCode' => 404], 404);
});



