<?php
define('SAMPLE','MD');
define('LOGO','M&D_new_logo.png');
define('ListingType',[1=>'Sale',2=>'Rent']);
define('ListingType_XML',[1=>'Buy',2=>'Rent']);
define('PropertyType',[1=>'Residential',2=>'Commercial']);
define('propertyCat',[1=>'Villa',2=>'Apartment',3=>'Commercial']);
define('AdminType',[1=>'Manager',2=>'Admin',3=>'Agent Pro',4=>'Agent',5=>'Team Leader',8=>'Other Employees']);
define('NoteSubject',[1=>'Phone call',2=>'Viewing',3=>'Appointment',4=>'Note',5=>'Email',6=>'Reminder']);
define('RecruitmentNoteSubject',[1=>'Interview',2=>'Phone Call',3=>'Reminder']);
define('Status',[11=>'Request for Listing',1=>'Listing',2=>'Pocket Listing',3=>'Withdrawn',4=>'MA',5=>'Sold By ',6=>'Sold By Other Company',7=>'Rented By ',8=>'Rented By Other Company']);
define('Status2',[1=>'Owner Occupied',2=>'Rented',3=>'Vacant',4=>'Vacant on Transfer',5=>'Under Construction']);//define('Status2',[1=>'Available',2=>'Moved in',3=>'Owner Occupied',4=>'Pending',5=>'renewed',6=>'Rented']);
define('TargetType',[1=>'No. of phone calls',2=>'No. of viewings',3=>'No. of MA',4=>'No. of Listings',5=>'Commission']);
define('CompanyDocCat',[1=>'Sales Form',2=>'Rental Form',3=>'Trade Licence',4=>'VAT Certificate',5=>'MOA',6=>'Emirates ID',7=>'Passport Copy',8=>'Establishment Card',9=>'ORN',10=>'BRN',11=>'Ejari',12=>'Others',]);
define('ContactCategory',['1'=>'buyer','2'=>'tenant','3'=>'agent','4'=>'owner','5'=>'developer']);
define('ADMIN_STATUS_NAME',['1'=>'Active','2'=>'Deactive']);
define('ADMIN_STATUS_COLOR',['1'=>'success','2'=>'danger']);
define('BUYER_LOOKING_FOR',['1'=>'Ready','2'=>'Off Plan']);
define('PAYMENT_METHOD',['1'=>'Cash','2'=>'Bank Transfer','3'=>'WPS']);
define('DealDocType',[
    "1"=>"Buyer Passport 1",
    "2"=>"Buyer Passport 2",
    "3"=>"Buyer Passport 3",
    "4"=>"Buyer Passport 4",
    "5"=>"Buyer EID 1",
    "6"=>"Buyer EID 2",
    "7"=>"Cheques Copy",
    "8"=>"Commission Form",
    "9"=>"Form A",
    "10"=>"Form B",
    "11"=>"Form F",
    "12"=>"New Title Deed",
    "13"=>"Old Title Deed",
    "14"=>"Owner EID 1",
    "15"=>"Owner EID 2",
    "16"=>"Owner EID 3",
    "17"=>"Owner EID 4",
    "18"=>"Owner Passport 1",
    "19"=>"Owner Passport 2",
    "20"=>"Owner Passport 3",
    "21"=>"Owner Passport 4",
    "22"=>"Receipts 1",
    "23"=>"Receipts 2",
    "24"=>"Receipts 3",
    "25"=>"Receipts 4",
    "26"=>"Rental Contract",
    "27"=>"Sale Contract",
    "28"=>"Tenant EID 1",
    "29"=>"Tenant EID 2",
    "30"=>"Tenant Passport 1",
    "31"=>"Tenant Passport 2",
    "32"=>"Other",
]);

define('EducationLevel',[
    '1'=>'School',
    '2'=>'High School',
    '3'=>'Diploma',
    '4'=>'Associate Degree',
    '5'=>'Bachelors Degree',
    '6'=>'Master Degree',
    '7'=>'Doctoral Degree',
]);

define('GENDER',[
    "1"=>"Male",
    "2"=>"Female",
]);

define('OffPlan',["completed"=>"Ready Secondary","off_plan"=>"Off-plan Secondary","completed_primary"=>"Ready Primary",
"off_plan_primary"=>"Off-plan Primary"]);

define('LeadType',['call_logs'=>'Call Logs','leads'=>'Email Leads','whatsapp_leads'=>'WhatsApp Leads','manual'=>'Manual Leads']);
define('LeadClosedReason',["Agent","Contact Already Exist","Developer","Du","Etisalat","Salesman","Scam","Spam","Valuation Company"]);
define('DataCenterClosedReason',["Sold","Rented","Owner Occupied","Not Interested","Wrong number"]);
define('DataCenterStatus',[1=>'Open',2=>'Assigned',3=>"Don't Disturb",4=>'Need To Follow']);
define('DataCenterStatusColor',[1=>'info',2=>'success',3=>'danger',4=>'primary']);

define('OffPlanProjectStatus',[1=>'available',2=>'Completed',3=>'Sold Out']);

define('Task_Type',[1=>['Confidential','#d6fbd2'],2=>['Immediate','#d3dcfa'],3=>['Important','#f4dbf7'],4=>['Normal','#f8f7c7']]);

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
