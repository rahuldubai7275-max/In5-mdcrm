<?php
use App\Http\Controllers\LanguageController;

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
Route::group(['prefix'=>'admin','middleware' => ['checkadmin']],function (){

    Route::get('/', 'DashboardController@dashboardAnalytics');

    Route::get('/properties', 'PropertiesController@Properties');
    Route::get('/property', 'PropertiesController@Property');
    Route::get('/property/listed', 'PropertiesController@pfListed')->name('property.listed');
    Route::get('/property/fetch', 'PropertiesController@pfFetch')->name('property.fetch');
    Route::post('/property/pf-error', 'PropertiesController@getPFerror')->name('property.pf-error');
    Route::post('/property', 'PropertiesController@Store')->name('property.add');
    Route::post('/property/copy', 'PropertiesController@copyStore')->name('property.copy');
    Route::post('/property/ajax/select', 'PropertiesController@SelectAjax')->name('property.ajax.select');
    Route::get('/property-add', 'PropertiesController@AddProperty');
    Route::get('/property-edit/{id}', 'PropertiesController@PropertyDetails');
    Route::post('/property-edit/', 'PropertiesController@EditProperty')->name('property.edit');
    Route::post('/property-get-datatable', 'PropertiesController@GetProperties')->name('property.get.datatable');
    Route::get('/properties-sm', 'PropertiesController@Properties_sm');
    Route::post('/property-get-data-sm', 'PropertiesController@GetProperties_sm')->name('property.get.data-sm');
    Route::post('/property/delete', 'PropertiesController@Delete')->name('property.delete');
    Route::post('/property/edit/image', 'PropertiesController@pictureEdit')->name('property.edit.image');
    Route::get('/property/view/{id}', 'PropertiesController@view')->name('property.view');
    //Route::get('/property/brochure/{id}', 'PropertiesController@brochure')->name('property.brochure');
    Route::post('/property/get-property-ajax/', 'PropertiesController@propertyAjax')->name('get-property-ajax');
    Route::post('/property/action/', 'PropertiesController@action')->name('property-action');
    Route::post('/property/rfl/reject/', 'PropertiesController@rfl_reject')->name('request-listed.reject');
    Route::post('/property/pf-status/', 'PropertiesController@pfStatus')->name('properties.pf-status');
    Route::post('/property/duplicatePropertyCheck/', 'PropertiesController@duplicatePropertyCheck')->name('property-duplicatePropertyCheck');

    Route::post('/property-note-add','PropertyNoteController@Store')->name('property.note.add');
    Route::post('/property-note-edit','PropertyNoteController@Edit')->name('property.note.edit');
    Route::post('/property-note-cancel','PropertyNoteController@cancel')->name('property.note.cancel');

    Route::get('/activity-report','ActivityController@activities');
    Route::post('/activities-get-datatable', 'ActivityController@GetActivities')->name('activities.get.datatable');

    Route::get('/leads', 'LeadController@Leads');
    Route::post('/leads-import', 'LeadController@import')->name('leads-import');
    Route::post('/leads-get-datatable', 'LeadController@getLeads')->name('leads.get.datatable');
    Route::get('/leads-sm', 'LeadController@Leads_sm');
    Route::post('/leads-get-data-sm', 'LeadController@getLeads_sm')->name('leads.get.data-sm');
    Route::get('/lead/view/{id}', 'LeadController@view')->name('lead.view');
    Route::get('/lead', 'LeadController@Lead');
    Route::post('/lead', 'LeadController@Store')->name('lead.add');
    Route::get('/lead/{id}', 'LeadController@Lead')->name('lead.detail');
    Route::post('/lead/edit', 'LeadController@edit')->name('lead.edit');
    Route::post('/lead/close', 'LeadController@closeLead')->name('lead.close');
    Route::post('/lead/delete', 'LeadController@Delete')->name('lead.delete');

    Route::post('/lead/action/', 'LeadController@action')->name('lead-action');
    Route::post('/lead/assign/', 'LeadController@assign')->name('lead.assign');

    Route::post('/lead/note', 'LeadNoteController@Store')->name('lead.note.add');
    Route::post('/lead-note-edit','LeadNoteController@Edit')->name('lead.note.edit');
    Route::post('/lead-note-cancel','LeadNoteController@cancel')->name('lead.note.cancel');

    Route::post('/lead/property', 'LeadController@leadProperty')->name('get-lead-info');


    Route::get('/contacts', 'ContactController@Contacts');
    Route::post('/contacts-import', 'ContactController@import')->name('contacts-import');
    Route::post('/contacts-get-datatable', 'ContactController@GetContacts')->name('contacts.get.datatable');
    Route::get('/contacts-sm', 'ContactController@Contacts_sm');
    Route::post('/contacts-get-data-sm', 'ContactController@GetContacts_sm')->name('contacts.get.data-sm');
    Route::get('/add-contacts', 'ContactController@ContactAdd');
    Route::post('/contacts', 'ContactController@Register')->name('contact.add');
    Route::post('/contacts/ajax/add', 'ContactController@StoreByAjax')->name('contact.add.ajax');
    Route::post('/contacts/ajax/select', 'ContactController@SelectAjax')->name('contact.ajax.select');
    Route::post('/contacts/ajax/select/cm', 'ContactController@SelectAjaxCM')->name('contact.ajax.select.cm');
    Route::post('/contacts/get/select', 'ContactController@GetAjax')->name('contact.ajax.get');
    Route::get('/contact-details/{id}', 'ContactController@ContactDetails');
    Route::post('/contact-profile-edit/', 'ContactController@Edit')->name('contact.edit');
    Route::post('/contact/delete', 'ContactController@Delete')->name('contact.delete');
    Route::get('/contact/view/{id}', 'ContactController@view')->name('contact.view');
    Route::post('/contact/get-contact-ajax/', 'ContactController@contactAjax')->name('get-contact-ajax');
    Route::post('/contact/get-contact-number-ajax/', 'ContactController@getByMobileNumber')->name('get-contact-number-ajax');
    Route::post('/contact/get-contact-email-ajax/', 'ContactController@getByEmail')->name('get-contact-email-ajax');
    Route::post('/contact/action/', 'ContactController@action')->name('contact-action');
    Route::post('/contact/status/', 'ContactController@status')->name('contact-status');
    Route::post('/contact/get-contact-category-ajax/', 'ContactController@getContactCatAjax')->name('get-contact-category-ajax');
    Route::post('/contact/add-contact-category/', 'ContactCategoryController@Store')->name('add-contact-category');

    Route::get('/deals', 'DealController@deals');
    Route::get('/add-deal', 'DealController@dealAdd');
    Route::post('/deal/add', 'DealController@Store')->name('deal.add');
    Route::post('/deals-get-datatable', 'DealController@getDeals')->name('deals.get.datatable');
    Route::get('/deal-edit/{id}', 'DealController@dealDetails');
    //Route::post('/deal-edit/', 'DealController@editDeal')->name('deal.edit');
    Route::get('/deal-view/{id}', 'DealController@dealDetails')->name('deal.view');
    Route::post('/deal/delete', 'DealController@Delete')->name('deal.delete');
    Route::post('/deal/disabled', 'DealController@disabled')->name('deal.disabled');
    Route::post('/deal/acknowledge', 'DealController@acknowledge')->name('deal.acknowledge');
    Route::post('/deal/property-contact', 'DealController@dealPropertyContact')->name('get-deal-info');

    Route::post('/deal-tracking/get', 'DealTrackingController@getDealTracking')->name('deal-tracking.get');
    Route::post('/deal-tracking/add', 'DealTrackingController@Store')->name('deal-tracking.add');
    Route::post('/deal-tracking/edit', 'DealTrackingController@edit')->name('deal-tracking.edit');
    Route::post('/deal-tracking/row', 'DealTrackingController@rowRefresh')->name('deal-tracking.row');
    Route::post('/deal-tracking/done', 'DealTrackingController@statusAction')->name('deal-tracking.done');
    Route::post('/deal-tracking/delete', 'DealTrackingController@delete')->name('deal-tracking.delete');

    Route::get('/deal-tracking-default', 'DealTrackDefaultController@DealTrackDefault');
    Route::post('/deal-tracking-default/get', 'DealTrackDefaultController@getDealTrackingDefault')->name('deal-tracking-default.get');
    Route::post('/deal-tracking-default/add', 'DealTrackDefaultController@Store')->name('deal-tracking-default.add');
    Route::post('/deal-tracking-default/edit', 'DealTrackDefaultController@edit')->name('deal-tracking-default.edit');
    Route::post('/deal-tracking-default/row', 'DealTrackDefaultController@rowRefresh')->name('deal-tracking-default.row');
    Route::post('/deal-tracking-default/delete', 'DealTrackDefaultController@delete')->name('deal-tracking-default.delete');


    Route::post('/deal-model/get', 'DealModelController@getEmailContent')->name('deal-model-email.get');
    Route::post('/deal-model/edit', 'DealModelController@edit')->name('deal-model-email.edit');

    Route::post('/history', 'HistoryController@getHistory')->name('history');
    Route::post('/history/value', 'HistoryController@getHistoryValue')->name('history.value');

    Route::post('/contact-note-add','ContactNoteController@Store')->name('contact.note.add');
    Route::post('/contact-note-edit','ContactNoteController@Edit')->name('contact.note.edit');
    Route::post('/contact-note-cancel','ContactNoteController@cancel')->name('contact.note.cancel');

    Route::get('/admins', 'AdminController@Admins');
    Route::get('/admin/pf-user', 'AdminController@pfUserId');
    Route::post('/admin-get-datatable', 'AdminController@GetAdmins')->name('admins.get.datatable');
    Route::get('/admins/admin', 'AdminController@admin')->name('admin.add.page');
    Route::post('/admins/add', 'Auth\RegisterController@create')->name('admin.add');
    Route::post('/admins/edit', 'AdminController@edit')->name('admin.edit');
    Route::get('/admin-edit/{id}', 'AdminController@AdminDetails');
    Route::get('/admin-profile/{id}', 'AdminController@profile');
    Route::get('/profile/', 'AdminController@profileAuth');
    Route::post('/admins/change-password', 'AdminController@changePassword')->name('admin.change.password');
    Route::post('/admins/own/change-password', 'AdminController@changePasswordOwn')->name('admin.own.change.password');
    Route::post('/admins/delete', 'AdminController@Delete')->name('admin.delete');
    Route::post('/admins/info', 'AdminController@getInfo')->name('admin.info');
    Route::get('/admins/eleven', 'AdminController@eleven')->name('admin.eleven');


    Route::get('/property-type', 'PropertyTypeController@PropertyTypes');
    Route::post('/property-type/get/ajax', 'PropertyTypeController@PropertyTypesAjax')->name('property-type.ajax.get');
    Route::post('/property-type/add', 'PropertyTypeController@Store')->name('property-type.add');
    Route::post('/property-type/delete', 'PropertyTypeController@Delete')->name('property-type.delete');
    Route::post('/property-type/edit', 'PropertyTypeController@Edit')->name('property-type.edit');

    Route::get('/calendar', 'CalendarController@calendar');
    Route::get('/calendar/get-json', 'CalendarController@getJson')->name('get-json.calendar');
    Route::post('/calendar/calendar-activity', 'CalendarController@calendarActivity')->name('calendar-activity.get.ajax');

    Route::post('/pf-location/get-ajax/', 'PFlocationController@GetAjax')->name('pf-location.get.ajax');

    Route::get('/master-project', 'MasterProjectController@MasterProjects');
    Route::post('/master-project/Get-Emirate-Ajax/', 'MasterProjectController@GetEmirateAjax')->name('master-project.get.ajax');
    Route::post('/master-project/add', 'MasterProjectController@Store')->name('master-project.add');
    Route::post('/master-project/delete', 'MasterProjectController@Delete')->name('master-project.delete');
    Route::post('/master-project/edit', 'MasterProjectController@Edit')->name('master-project.edit');

    Route::get('/contact-source', 'ContactSourceController@ContactSources');
    Route::post('/contact-source/add', 'ContactSourceController@Store')->name('contact-source.add');
    Route::post('/contact-source/delete', 'ContactSourceController@Delete')->name('contact-source.delete');
    Route::post('/contact-source/edit', 'ContactSourceController@Edit')->name('contact-source.edit');

    Route::get('/vendor-motivation', 'VendorMotivationController@VendorMotivations');
    Route::post('/vendor-motivation/add', 'VendorMotivationController@Store')->name('vendor-motivation.add');
    Route::post('/vendor-motivation/delete', 'VendorMotivationController@Delete')->name('vendor-motivation.delete');
    Route::post('/vendor-motivation/edit', 'VendorMotivationController@Edit')->name('vendor-motivation.edit');

    Route::get('/company-profile', 'CompanyController@company');
    // Route::post('/companys/add', 'CompanyController@Store')->name('companys.add');
    // Route::post('/companys/delete', 'CompanyController@Delete')->name('companys.delete');
    Route::post('/companys/edit', 'CompanyController@Edit')->name('companys.edit');

    Route::get('/emirates', 'EmirateController@Emirates');
    Route::post('/emirates/add', 'EmirateController@Store')->name('emirates.add');
    Route::post('/emirates/delete', 'EmirateController@Delete')->name('emirates.delete');
    Route::post('/emirates/edit', 'EmirateController@Edit')->name('emirates.edit');

    Route::get('/community', 'CommunityController@Communitys');
    Route::post('/community/Get-Master-Project-Ajax/', 'CommunityController@GetMasterProjectAjax')->name('community.get.ajax');
    Route::post('/community/Get-Master-Project-Ajax-data-center/', 'CommunityController@GetMasterProjectAjaxDataCenter')->name('community.get.ajax.data-center');
    Route::post('/community/add', 'CommunityController@Store')->name('community.add');
    Route::post('/community/delete', 'CommunityController@Delete')->name('community.delete');
    Route::post('/community/edit', 'CommunityController@Edit')->name('community.edit');

    Route::get('/cluster-street', 'ClusterStreetController@ClusterStreets');
    Route::post('/cluster-street/Get-Master-Project-Ajax/', 'ClusterStreetController@GetCommunityAjax')->name('cluster-street.get.ajax');
    Route::post('/cluster-street/add', 'ClusterStreetController@Store')->name('cluster-street.add');
    Route::post('/cluster-street/add/ajax', 'ClusterStreetController@StoreAjax')->name('cluster-street.add.ajax');
    Route::post('/cluster-street/delete', 'ClusterStreetController@Delete')->name('cluster-street.delete');
    Route::post('/cluster-street/edit', 'ClusterStreetController@Edit')->name('cluster-street.edit');
    Route::post('/cluster-street/confirm', 'ClusterStreetController@confirm')->name('cluster-street.confirm');

    Route::get('/type', 'VillaTypeController@VillaTypes');
    Route::post('/type/Get-Master-Project-Ajax', 'VillaTypeController@GetCommunityAjax')->name('type.get.ajax');
    Route::post('/type/add', 'VillaTypeController@Store')->name('villa-type.add');
    Route::post('/type/add/ajax', 'VillaTypeController@StoreAjax')->name('villa-type.add.ajax');
    Route::post('/type/delete', 'VillaTypeController@Delete')->name('villa-type.delete');
    Route::post('/type/edit', 'VillaTypeController@Edit')->name('villa-type.edit');
    Route::post('/type/confirm', 'VillaTypeController@confirm')->name('villa-type.confirm');

    Route::get('/views', 'ViewController@Views');
    Route::post('/views/add', 'ViewController@Store')->name('views.add');
    Route::post('/views/delete', 'ViewController@Delete')->name('views.delete');
    Route::post('/views/edit', 'ViewController@Edit')->name('views.edit');

    Route::get('/bedrooms', 'BedroomController@Bedrooms');
    Route::post('/bedrooms/add', 'BedroomController@Store')->name('bedrooms.add');
    Route::post('/bedrooms/delete', 'BedroomController@Delete')->name('bedrooms.delete');
    Route::post('/bedrooms/edit', 'BedroomController@Edit')->name('bedrooms.edit');

    Route::get('/bathrooms', 'BathroomController@Bathrooms');
    Route::post('/bathrooms/add', 'BathroomController@Store')->name('bathrooms.add');
    Route::post('/bathrooms/delete', 'BathroomController@Delete')->name('bathrooms.delete');
    Route::post('/bathrooms/edit', 'BathroomController@Edit')->name('bathrooms.edit');

    Route::get('/developers', 'DeveloperController@Developers');
    Route::post('/developers/add', 'DeveloperController@Store')->name('developers.add');
    Route::post('/developers/delete', 'DeveloperController@Delete')->name('developers.delete');
    Route::post('/developers/edit', 'DeveloperController@Edit')->name('developers.edit');

    Route::get('/vaastu-orientation', 'VaastuOrientationController@VaastuOrientations');
    Route::post('/vaastu-orientation/add', 'VaastuOrientationController@Store')->name('vaastu-orientation.add');
    Route::post('/vaastu-orientation/delete', 'VaastuOrientationController@Delete')->name('vaastu-orientation.delete');
    Route::post('/vaastu-orientation/edit', 'VaastuOrientationController@Edit')->name('vaastu-orientation.edit');

    Route::get('/target/{period}', 'TargetController@targets');
    Route::post('/target-get-datatable', 'TargetController@GetTargets')->name('targets.get.datatable');
    Route::post('/target/add', 'TargetController@Store')->name('target.add');
    Route::post('/target/delete', 'TargetController@Delete')->name('target.delete');
    Route::post('/target/edit', 'TargetController@Edit')->name('target.edit');
    Route::post('/target/dashboard', 'TargetController@ajaxDashboard')->name('target-ajax-dashboard');
    Route::post('/target/show', 'TargetController@show')->name('target-ajax-show');

    Route::get('/report', 'ReportController@repoets');
    Route::post('/report/filter', 'ReportController@reportFilters')->name('report.filter');

    Route::get('/dc-report', 'ReportController@telesalesRepoets');
    Route::post('/dc-report/filter', 'ReportController@telesalesReportFilters')->name('dc-report.filter');

    Route::get('/report-best', 'ReportController@repoetsBestAgent');
    Route::post('/report-best/filter', 'ReportController@repoetsBestAgent')->name('best.report.filter');
    Route::post('/report-best/list', 'ReportController@listBestAgent')->name('best.agent.list');

    Route::get('/business-timings', 'BusinessTimingController@businessTiming');
    Route::post('/business-timings/edit', 'BusinessTimingController@edit')->name('business-timings.edit');

    Route::get('/company-documents', 'CompanyDocumentController@companyDocuments');
    Route::get('/agent-forms', 'CompanyDocumentController@agentForms');
    Route::get('/agent-forms-sm', 'CompanyDocumentController@agentForms_sm');
    Route::post('/company-documents/add', 'CompanyDocumentController@Store')->name('company-document.add');
    Route::post('/company-documents/delete', 'CompanyDocumentController@Delete')->name('company-document.delete');

    Route::get('/campaigns', 'FBFormController@campaigns');

    Route::get('/settings', 'SettingController@settings');
    Route::post('/setting/ma', 'SettingController@openMA')->name('open-ma-setting');
    Route::post('/setting/lead', 'SettingController@openLead')->name('open-lead-setting');
    Route::post('/setting/hr-access', 'SettingController@HRAccess')->name('hr-access-setting');
    Route::post('/setting/main-access', 'SettingController@MainAccess')->name('main-access-setting');
    Route::post('/setting/survey-access', 'SettingController@SurveyAccess')->name('survey-access-setting');
    Route::post('/setting/request-approver', 'SettingController@RequestApprover')->name('request-approver-setting');
    Route::post('/setting/buyer-tenant', 'SettingController@openBuyerTenant')->name('open-buyer-tenant-setting');
    Route::post('/setting/expiration-property', 'SettingController@expirationProperty')->name('open-expiration-property-setting');
    Route::post('/setting/expiration-user', 'SettingController@expirationUser')->name('open-expiration-user-setting');
    Route::post('/setting/contact-activity', 'SettingController@contactLastActivity')->name('open-contact-activity-setting');
    Route::post('/setting/calendar-color', 'SettingController@calendarColor')->name('calendar-color-setting');
    Route::post('/setting/brochure-bg', 'SettingController@brochureBG')->name('brochure-bg-setting');
    Route::post('/setting/upload-contact', 'SettingController@uploadContact')->name('upload-contact-setting');
    Route::post('/setting/task-access', 'SettingController@taskAccess')->name('task-access-setting');

    Route::post('/upload/file', 'UploaderController@storeFile')->name('upload-file');
    Route::post('/upload/image', 'UploaderController@storeImage')->name('upload-image');
    Route::post('/delete/image', 'UploaderController@DeleteImage')->name('delete-image');

    Route::post('/theme-setting/edit', 'ThemeSettingController@Edit')->name('theme-setting.edit');

    Route::get('/properties-export', 'PropertiesController@exportProperties')->name('properties-export');
    Route::get('/contacts-export', 'ContactController@exportContacts')->name('contacts-export');
    Route::get('/leads-export', 'LeadController@exportLeads')->name('leads-export');

    Route::get('/requests', 'AdminRequestController@requests');
    Route::post('/request/add', 'AdminRequestController@store')->name('request.add');
    Route::post('/request/confirm', 'AdminRequestController@confirm')->name('request.confirm');
    Route::post('/request/details', 'AdminRequestController@details')->name('request.details');
    Route::post('/request/delete', 'AdminRequestController@Delete')->name('request.delete');
    Route::post('/request/cansel', 'AdminRequestController@cancelRequest')->name('request.cancel-request');
    Route::post('/request/cansel-action', 'AdminRequestController@cancelRequestAction')->name('request.cancel-request-action');
    Route::post('/requests-get-datatable', 'AdminRequestController@GetRequest')->name('requests.get.datatable');
    Route::get('/requests-sm', 'AdminRequestController@requests_sm');
    Route::post('/request-get-data-sm', 'AdminRequestController@GetRequest_sm')->name('request.get.data-sm');

    Route::get('/warnings', 'AdminWarningController@warnings');
    Route::post('/warnings/add', 'AdminWarningController@store')->name('warning.add');
    Route::post('/warnings/details', 'AdminWarningController@details')->name('warning.details');
    Route::post('/warnings/delete', 'AdminWarningController@Delete')->name('warning.delete');
    Route::post('/warnings/acknowledge', 'AdminWarningController@acknowledge')->name('warning.acknowledge');
    Route::post('/warnings-get-datatable', 'AdminWarningController@getWarning')->name('warnings.get.datatable');

    Route::get('/survey-question', 'SurveyQuestionController@surveyQuestions');
    Route::post('/survey-question/add', 'SurveyQuestionController@store')->name('survey-question.add');
    Route::post('/survey-question/edit', 'SurveyQuestionController@edit')->name('survey-question.edit');
    Route::post('/survey-question/delete', 'SurveyQuestionController@delete')->name('survey-question.delete');
    Route::post('/survey-question/status', 'SurveyQuestionController@statusAction')->name('survey-question.status');

    Route::get('/surveys', 'SurveyController@surveys');
    Route::post('/surveys-get-datatable', 'SurveyController@getSurveys')->name('surveys.get.datatable');
    Route::post('/surveys/details', 'SurveyController@details')->name('surveys.details');

    Route::get('/data-center-access', 'DataCenterAccessController@DataCenterAccess');
    Route::post('/data-center-access/store', 'DataCenterAccessController@store')->name('dc-access.add');
    Route::post('/data-center-access/edit', 'DataCenterAccessController@Edit')->name('dc-access.edit');
    Route::post('/data-center-access-get-datatable', 'DataCenterAccessController@getDC_Access')->name('dc-access.get.datatable');
    Route::post('/data-center-access/delete', 'DataCenterAccessController@delete')->name('dc-access.delete');
    Route::post('/data-center-access/get-projects', 'DataCenterAccessController@getProjects')->name('dc-access.get-projects');

    Route::get('/data-center-file', 'DataCenterFileController@DataCenterFile');
    Route::post('/data-center-file/delete', 'DataCenterFileController@delete')->name('dc-file.delete');

    Route::get('/data-center-assign', 'DataCenterAssignController@DataCenterAssign');
    Route::post('/data-center-assign/store', 'DataCenterAssignController@store')->name('dc-assign.add');
    Route::post('/data-center-assign/edit', 'DataCenterAssignController@Edit')->name('dc-assign.edit');
    Route::post('/data-center-assign-get-datatable', 'DataCenterAssignController@getDC_Assign')->name('dc-assign.get.datatable');
    Route::post('/data-center-assign/get-projects', 'DataCenterAssignController@getProjects')->name('dc-assign.get-projects');
    Route::post('/data-center-assign/get-details', 'DataCenterAssignController@getDetails')->name('dc-assign.get-details');
    Route::post('/data-center-assign/delete', 'DataCenterAssignController@delete')->name('dc-assign.delete');

    Route::get('/data-center', 'DataCenterController@DataCenter');
    Route::post('/data-center-import', 'DataCenterController@import')->name('data-center-import');
    Route::get('/data-export', 'DataCenterController@exportdata')->name('data-export');
    Route::post('/data-center-match-master-project', 'DataCenterController@matchMasterProject')->name('data-center-match-master-project');
    Route::post('/data-center-match-project', 'DataCenterController@matchProject')->name('data-center-match-project');
    Route::post('/data-center-assign', 'DataCenterController@assign')->name('data-center-assign');
    Route::post('/data-center-close', 'DataCenterController@close')->name('data-center-close');
    Route::post('/data-center-action', 'DataCenterController@action')->name('data-center-action');
    Route::post('/data-center-get-datatable', 'DataCenterController@getData')->name('data-center.get.datatable');
    Route::post('/data-center-agent-get-datatable', 'DataCenterController@getDataAgent')->name('data-center-agent.get.datatable');
    Route::get('/data-center-arranged', 'DataCenterController@DataCenterArranged');
    Route::get('/data-center-view/{id}', 'DataCenterController@view');
    Route::post('/data-center-agent-assign/agent-assign/', 'DataCenterController@agentAssign')->name('data-agent-action');

    Route::post('/data-center-note-add','DataCenterNoteController@Store')->name('data-center.note.add');
    Route::post('/dc-activities-get-datatable', 'DataCenterNoteController@GetActivities')->name('dc-activities.get.datatable');

    Route::get('/job-title/{poj}', 'JobTitleController@JobTitle');
    Route::post('/job-title/add', 'JobTitleController@Store')->name('job-title.add');
    Route::post('/job-title/delete', 'JobTitleController@Delete')->name('job-title.delete');
    Route::post('/job-title/edit', 'JobTitleController@Edit')->name('job-title.edit');

    Route::get('/language', 'LanguagesController@Languages');
    Route::post('/language/add', 'LanguagesController@Store')->name('language.add');
    Route::post('/language/delete', 'LanguagesController@Delete')->name('language.delete');
    Route::post('/language/edit', 'LanguagesController@Edit')->name('language.edit');

    Route::get('/recruitment', 'RecruitmentController@recruitments');
    Route::get('/recruitment/add', 'RecruitmentController@recruitment')->name('recruitment.add.page');
    Route::post('/recruitment/store', 'RecruitmentController@Store')->name('recruitment.add');
    Route::post('/recruitment/delete', 'RecruitmentController@delete')->name('recruitment.delete');
    Route::get('/recruitment-edit/{id}', 'RecruitmentController@details');
    Route::post('/recruitment/edit', 'RecruitmentController@edit')->name('recruitment.edit');
    Route::get('/recruitment-view/{id}', 'RecruitmentController@view')->name('recruitment.view');
    Route::post('/recruitment-get-datatable', 'RecruitmentController@GetRecruitment')->name('recruitment.get.datatable');

    Route::post('/recruitment-note-add','RecruitmentNoteController@Store')->name('recruitment.note.add');

    Route::get('/off-plan-projects', 'OffPlanProjectController@OffPlanProjects');
    Route::post('/off-plan-project-get-datatable', 'OffPlanProjectController@getOffPlanProjects')->name('off-plan-project.get.datatable');
    Route::post('/off-plan-project-get-ajax', 'OffPlanProjectController@OffPlanProjectsAjax')->name('off-plan-project.get.ajax');
    Route::post('/off-plan-project-get-ajax-mp', 'OffPlanProjectController@mdcrmsMP')->name('off-plan-project.get.ajax_mp');

    Route::get('/off-plan-projects-sm', 'OffPlanProjectController@OffPlanProjects_sm');
    Route::post('/off-plan-project-get-data-sm', 'OffPlanProjectController@GetOffPlanProjects_sm')->name('off-plan-project.get.data-sm');
    Route::get('/off-plan-project/add', 'OffPlanProjectController@OffPlanProject')->name('off-plan-project.add.page');
    Route::post('/off-plan-project/store', 'OffPlanProjectController@Store')->name('off-plan-project.add');
    Route::get('/off-plan-project-edit/{id}', 'OffPlanProjectController@details');
    Route::post('/off-plan-project/edit', 'OffPlanProjectController@edit')->name('off-plan-project.edit');
    Route::post('/off-plan-project/delete', 'OffPlanProjectController@delete')->name('off-plan-project.delete');
    Route::post('/off-plan-project/edit/image', 'OffPlanProjectController@pictureEdit')->name('off-plan-project.edit.image');
    Route::post('/off-plan-project/search', 'OffPlanProjectController@SelectAjax')->name('off-plan-project.ajax.select');
    Route::post('/off-plan-project-inside/search', 'OffPlanProjectController@SelectAjaxInside')->name('off-plan.ajax.select');

    Route::get('/hr-request', 'HRRequestController@HRRequests');
    Route::post('/hr-request/add', 'HRRequestController@store')->name('hr-request.add');
    Route::post('/hr-request/edit', 'HRRequestController@edit')->name('hr-request.edit');
    Route::post('/hr-request/delete', 'HRRequestController@delete')->name('hr-request.delete');

    Route::get('/requests-hr', 'AdminHrRequestController@requests');
    Route::post('/request-hr/add', 'AdminHrRequestController@store')->name('request-hr.add');
    Route::post('/request-hr/reply', 'AdminHrRequestController@reply')->name('request-hr.reply');
    Route::post('/request-hr/delete', 'AdminHrRequestController@Delete')->name('request-hr.delete');
    Route::post('/requests-hr-get-datatable', 'AdminHrRequestController@GetRequest')->name('requests-hr.get.datatable');
    Route::post('/request-hr/details', 'AdminHrRequestController@details')->name('hr-request.details');

    Route::get('/task-title', 'TaskTitleController@TaskTitles');
    Route::post('/task-title/add', 'TaskTitleController@store')->name('task-title.add');
    Route::post('/task-title/edit', 'TaskTitleController@edit')->name('task-title.edit');
    Route::post('/task-title/delete', 'TaskTitleController@delete')->name('task-title.delete');
    Route::post('/task-title/status', 'TaskTitleController@statusAction')->name('task-title.status');

    Route::get('/tasks', 'TaskController@Tasks');
    Route::post('/task/add', 'TaskController@store')->name('task.add');
    Route::post('/task/edit', 'TaskController@edit')->name('task.edit');
    Route::post('/task/delete', 'TaskController@delete')->name('task.delete');
    Route::post('/task/action', 'TaskController@action')->name('task.status');
    Route::post('/task/day', 'TaskController@ajaxDayTasks')->name('task.day');
    Route::post('/tasks-get-datatable', 'TaskController@getTasks')->name('tasks.get.datatable');

    Route::get('/referrers', 'ReferrerController@referrers');
    Route::post('/referrer/add', 'ReferrerController@store')->name('referrer.add');
    Route::post('/referrer/edit', 'ReferrerController@edit')->name('referrer.edit');
    Route::post('/referrer/delete', 'ReferrerController@delete')->name('referrer.delete');

});
Route::get('/admin/login','Auth\AdminLoginController@showLoginForm')->name('admin.login');
Route::post('/admin/login', 'Auth\AdminLoginController@login')->name('admin.login.submit');
Route::get('/admin/logout', 'Auth\AdminLoginController@logout')->name('admin.logout');


Route::get('/',[\App\Http\Controllers\Site\IndexController::class,'index']);
Route::get('listing',[\App\Http\Controllers\Site\PropertyController::class,'ListingProperty']);
Route::get('property/{id}',[\App\Http\Controllers\Site\PropertyController::class,'Property']);

Route::get('storage/{filename}', function ($filename)
{

    $path = storage_path('app/public/images/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

// locale Route
//Route::get('lang/{locale}',[LanguageController::class,'swap']);

//Route::get('/linkImage', function () {
//    \Artisan::call('storage:link');
//    return \Artisan::output();
//});
//Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/phpinfo', function () {
    return phpinfo();
});

Route::post('/send-mail',[\App\Http\Controllers\MailController::class,'sendEmail'])->name('send-mail');
Route::get('/property/brochure/{id}', 'PropertiesController@brochure')->name('property.brochure');
Route::get('/off-plan/brochure/{id}', 'OffPlanProjectController@brochure')->name('off-plan-project.brochure');

Route::get('/property/pfcheck/{id}', 'PropertiesController@pfChech');

Route::get('/recruitment', 'RecruitmentController@recruitmentForm')->name('recruitment.form');
Route::post('/recruitment', 'RecruitmentController@storeForm')->name('recruitment.form.store');

Route::get('/save-target', 'TargetHistoryController@Store')->name('target-history.store');

Route::get('/lead/insert-lead', 'LeadController@insertLeads');
Route::get('/lead/insert-lead-fb', 'LeadController@insertLeadsFB');
Route::get('/lead/pf-lead', 'LeadController@pfLead');
Route::get('/lead/bayut-lead/{type}', 'LeadController@bayutLead');
Route::get('/lead/Dubizzle-lead/{type}', 'LeadController@insertDubizzleLeads');
Route::get('/lead/insert-form-fb', 'FBFormController@insertForm');


Route::get('/survey/{id}', 'SurveyController@survey');
Route::post('/survey/answered', 'SurveyController@answer')->name('survey.answer');

Route::get('/tracking/{deal_id}', 'DealTrackingController@tracking');

Route::get('/install-app', 'AppController@install');

Route::get('/data-center-import', 'DataCenterController@importToData');


//Route::get('/leads', 'LeadController@Leads');
