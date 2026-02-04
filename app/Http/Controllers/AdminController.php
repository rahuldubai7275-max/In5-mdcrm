<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminRequest;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use function Symfony\Component\VarDumper\Dumper\esc;

class AdminController extends Controller
{
    // Admin - Table
    public function Admins(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $admin = \Auth::guard('admin')->user();

        //if($admin->type==1)
        $Admins=Admin::where('main_number','!=','+971502116655')->get();
        //else
        //$Admins=Admin::where('type','>',2)->get();


        $add_btn='<li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-light" href="'.route('admin.add.page').'">Add</a></li>';
        if( $this->company_info('total')==0 ){
            $add_btn='';
        }
        return view('/admin/admin', [
            'pageConfigs' => $pageConfigs,
            'Admins' => $Admins,
            'add_btn' => $add_btn,
        ]);
    }

    public function pfUserId(){
        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);

        $agent_pf_info = Http::withToken($token_response->accessToken)
            ->get('https://atlas.propertyfinder.com/v1/users');
        $agent_pf_info= json_decode($agent_pf_info);
        $i=0;
        foreach($agent_pf_info->data as $row){
            $i++;
            $ClientManager=Admin::where('company_id',$company->id)->where('email',$row->email)->first();
            echo $i.'-'.$row->firstName.' '.$row->lastName.'<br>';
            echo $row->email.'<br>';
            if($ClientManager) {
                echo $ClientManager->email . '<br><br>';


                /*if(isset($row->publicProfile->id)){
                    $assignedTo_id= $row->publicProfile->id;
                    $ClientManager->pf_user_id=$assignedTo_id;
                    $ClientManager->save();
                }*/

            }else{
                echo 'No Email<br><br>';
            }

        }
    }

    public function company_info($user_type){
        $adminAuth=\Auth::guard('admin')->user();
        $primary_user=Admin::where('company_id',$adminAuth->company_id)->where('status',1)->where('type','!=',8)->count();
        $employees_user=Admin::where('company_id',$adminAuth->company_id)->where('status',1)->where('type',8)->count();

        /*$txt_name= intval( str_replace('-','',date('Y-m-d') ) )*9;

        if($user_type=='total')
            Storage::delete('public/images/'.$txt_name.'.txt' );

        if( Storage::exists('public/images/'.$txt_name.'.txt') ) {
            $md_info = Crypt::decryptString( file_get_contents( storage_path( 'app/public/images/'.$txt_name.'.txt' ) ) );
        }else{
            $company=Company::find(1);

            $url=env('MD_URL').'/api/company';
            $token=env('MD_TOKEN');
            $data=[];

            $md_info=Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

            Storage::disk('local')->put('public/images/'.$txt_name.'.txt', Crypt::encryptString( $md_info  ) );
            Storage::delete('public/images/'.( intval( str_replace('-','',date('Y-m-d',strtotime("-1 days")) ) )*9 ).'.txt' );
        }*/

        $md_info=Company::find($adminAuth->company_id);

        $md_info=json_decode($md_info);

        if($md_info->expiry_date<date('Y-m-d'))
            return 0;

        if($user_type=='info'){
            if(($md_info->primary_user+$md_info->employees_user)< ($primary_user+$employees_user) ){
                return 0;
            }
            return 1;
        }

        if($user_type=='total'){
            if(($md_info->primary_user+$md_info->employees_user)<= ($primary_user+$employees_user) ){
                return 0;
            }
            return 1;
        }

        if($user_type=='primary'){
            if($md_info->primary_user<= $primary_user ){
                return 0;
            }
            return 1;
        }

        if($user_type=='other'){
            if($md_info->employees_user<= $employees_user ){
                return 0;
            }
            return 1;
        }
    }

    public function admin(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/admins",'name'=>"Users"], ['name'=>"User"]
        ];

        if( $this->company_info('total')==0 ){
            return;
        }

        $route="admin.add";
        $admin='';
        return view('/admin/admin-details', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'admin'=>$admin,
            'route'=>$route
        ]);
    }

    public function edit(Request $request)
    {

        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'AdminType' => ['required'],//, 'number', 'max:1'
            'email' => ['required', 'string', 'email', 'max:255'],
        ],
            [
                'email.required' => 'Email is required',
                'email.unique' => 'Email is already existing',
            ]);

        if (request('email')) {
            $checkEmail = Admin::where('email', request('email'))->where('id', '!=', request('_id'))->first();
            if ($checkEmail) {
                Session::flash('error', 'Email is already existing');
                return redirect()->back();
            }
        }

        $AdminType=request('AdminType');
        $Admin = Admin::find(request('_id'));
        $adminAuth = \Auth::guard('admin')->user();

        if(!$Admin || $Admin->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        if($Admin->status==2 && request('status')==1){

            if( $this->company_info( (($AdminType==8) ?  'other' : 'primary') )==0 ){

                return redirect('/admin/admins');
            }
        }

        $use_annual_current_year=$Admin->use_annual_current_year;
        if(request('UseAnnualCurrentYear') || request('UseAnnualCurrentYear')=='0')
            $use_annual_current_year = request('UseAnnualCurrentYear');

        $use_annual_previous_year=$Admin->use_annual_previous_year;
        if(request('UseAnnualPreviousYear') || request('UseAnnualPreviousYear')=='0')
            $use_annual_previous_year = request('UseAnnualPreviousYear');

        $path = "";
        $pic_name = null;
        if ($request->file('PicName')) {
            $imagePath = $request->file('PicName');

            $path = $request->file('PicName')->store('public/images');
        }

        if ($path) {
            $pic_name = explode("/", $path);
            $pic_name = end($pic_name);

            Storage::delete('public/images/' . $Admin->pic_name);
            $Admin->pic_name = $pic_name;
        }
        if (request('status'))
            $Admin->status = request('status');

        $Admin->firstname = request('firstname');
        $Admin->lastname = request('lastname');
        $Admin->date_birth = request('DateBirth');
        $Admin->date_joined = request('DateJoined');
        $Admin->gender = request('Gender');
        $Admin->type = $AdminType;
        $Admin->email = request('email');
        $Admin->personal_email = request('personal_email');
        $Admin->main_number = request('MainNumber');
        $Admin->mobile_number = request('MobileNumber');
        $Admin->job_title = request('JobTitle');
        $Admin->rera_brn = request('ReraBRN');
        $Admin->supervisor_id = request('supervisor');
        $Admin->office_tel = request('OfficeTel');
        $Admin->emergency_contact_name = request('EmergencyContactName');
        $Admin->emergency_contact_number = request('EmergencyContactNumber');
        $Admin->emergency_contact_email = request('EmergencyContactEmail');
        $Admin->emergency_contact_relation = request('EmergencyContactRelation');
        $Admin->emergency_contact_address = request('EmergencyContactAddress');
        $Admin->address = request('Address');
        $Admin->basic_salary = (request('BasicSalary')) ? str_replace(',', '', request('BasicSalary')) : null;
        $Admin->allowance_salary = (request('AllowanceSalary')) ? str_replace(',', '', request('AllowanceSalary')) : null;
        $Admin->commission = request('Commission');
        $Admin->payment_method = request('PaymentMethod');
        $Admin->bank_id = request('Bank');
        $Admin->account_number = request('AccountNumber');
        $Admin->iban_number = request('IBANNumber');
        $Admin->labour_personal_id = request('LabourPersonalId');
        $Admin->bank_routing_code = request('BankRoutingCode');
        $Admin->leave_days = request('LeaveDays');
        $Admin->use_annual_current_year = $use_annual_current_year;
        $Admin->use_annual_previous_year = $use_annual_previous_year;
        $Admin->visa_expiration_date = request('VisaExpirationDate');
        $Admin->insurance_expiration_date = request('InsuranceExpirationDate');
        $Admin->labour_card_expiration_date = request('LabourCardExpirationDate');
        $Admin->rera_card_expiration_date = request('ReraCardExpirationDate');
        $Admin->passport = request('passport');
        $Admin->emirates_id = request('emirates_id');
        $Admin->labour_contract = request('labour_contract');
        $Admin->labour_card = request('labour_card');
        $Admin->residents_visa = request('residents_visa');
        $Admin->insurance = request('insurance');
        $Admin->rera_card = request('rera_card');
        $Admin->job_offer = request('job_offer');
        $Admin->contract_of_employment = request('contract_of_employment');
        $Admin->cancellation = request('cancellation');
        $Admin->other = request('other');

        if ($adminAuth->super == 1 && $Admin->date_joined){
            $AdminRequestCount = AdminRequest::where('type', '0')->where('admin_id', $Admin->id)->count();
            //if($AdminRequestCount==0) {
            if ($use_annual_current_year>0 || $use_annual_previous_year > 0) {
                $newYear = date('Y') . '-' . date('m-d', strtotime($Admin->date_joined));
                $today = date('Y-m-d');
                if ($newYear > $today) {
                    $newYear = date('Y-m-d', strtotime($newYear . "- 1 years"));
                }
                $previousYear = date('Y-m-d', strtotime($newYear . "- 1 years"));

                if(request('UseAnnualCurrentYear')>0) {
                    $datetime_from = date('Y-m-d', strtotime($newYear . "+ 1 days"));
                    $datetime_to = date('Y-m-d', strtotime($datetime_from . "+ " . $use_annual_current_year . " days"));
                    AdminRequest::create([
                        'admin_id' => $Admin->id,
                        'type' => 0,
                        'request_id' => 7,
                        'datetime_from' => $datetime_from,
                        'datetime_to' => $datetime_to,
                        'number_days' => $use_annual_current_year,
                        'manager_admin' => $adminAuth->id,
                        'manager_status' => 1,
                        'result_seen' => 1,
                    ]);
                }

                if(request('UseAnnualPreviousYear')>0) {
                    $datetime_from = date('Y-m-d', strtotime($previousYear . "+ 1 days"));
                    $datetime_to = date('Y-m-d', strtotime($datetime_from . "+ " . $use_annual_previous_year . " days"));
                    AdminRequest::create([
                        'admin_id' => $Admin->id,
                        'type' => 0,
                        'request_id' => 7,
                        'datetime_from' => $datetime_from,
                        'datetime_to' => $datetime_to,
                        'number_days' => $use_annual_previous_year,
                        'manager_admin' => $adminAuth->id,
                        'manager_status' => 1,
                        'result_seen' => 1,
                    ]);
                }
            }
            //}
        }

        // $Admin->password=Hash::make(request('password'));
        $Admin->save();

        $url=env('MD_URL').'/api/admin/edit';
        $company=Company::find($adminAuth->company_id);
        $data=[
            'in_crm_id'=>$Admin->id,
            'status'=>$Admin->status,
            'firstname'=>$request->firstname,
            'lastname'=>$request->lastname,
            'date_birth'=>$request->DateBirth,
            'date_joined'=>$request->DateJoined,
            'gender'=>$request->Gender,
            'type'=>$request->AdminType,
            'email'=>$request->email,
            'personal_email'=>$request->personal_email,
            'pic_name'=>$pic_name,
            'main_number'=>$request->MainNumber,
            'mobile_number'=>$request->MobileNumber,
            'job_title'=>$request->JobTitle,
            'rera_brn'=>$request->ReraBRN,
            'office_tel'=>$request->OfficeTel,
            'emergency_contact_name'=>$request->EmergencyContactName,
            'emergency_contact_number'=>$request->EmergencyContactNumber,
            'emergency_contact_email'=>$request->EmergencyContactEmail,
            'emergency_contact_relation'=>$request->EmergencyContactRelation,
            'emergency_contact_address'=>$request->EmergencyContactAddress,
            'address'=>$request->Address
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        return redirect('/admin/admins');
    }

    public function AdminDetails(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/admins",'name'=>"Users"], ['name'=>"View"]
        ];
        $adminAuth=\Auth::guard('admin')->user();
        $admin=Admin::find(request('id'));
        $route="admin.edit";

        if(!$admin || $admin->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        //$hr_admin=\App\Models\Admin::where('type',6)->pluck('id')->first();
        //$hr_access=\App\Models\SettingAdmin::where('setting_id',16)->where('admin_id',$adminAuth->id)->first();
        $hr_access=[];
        $access=1;
        //if ($hr_admin){
        //    if($adminAuth->super!=1 && !in_array($adminAuth->id, $hr_admin))
        //        $access=0;
        //}else{
        //    if($adminAuth->super!=1 && !in_array($adminAuth->id, $hr_access))
        //        $access=0;
        //}

        /*if ($adminAuth->type!=6 && !$hr_access && $adminAuth->super!=1)
            $access=0;

        if ($admin->status==2 && $adminAuth->super!=1)
            $access=0;*/

        if ($adminAuth->type>=$admin->type && $adminAuth->super!=1)
            $access=0;

        if ($admin && ($adminAuth->type==1 || $access==1)) {
            return view('/admin/admin-details', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'route'=>$route,
                'admin' => $admin,
            ]);
        }else{
            return redirect('/admin/admins');
        }

    }

    public function GetAdmins(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $where='';

        $orderBy=' ORDER BY created_at DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `admins` WHERE company_id=".$adminAuth->company_id);
        $totalRecords=$totalRecords[0]->countAll;

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        $today = date('Y-m-d');

        $EU_setting_1=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_1')->first();
        $EU_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_2')->first();
        $EU_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_3')->first();

        if( request('first_name') )
            $where.=' AND firstname  LIKE "%'.request('first_name').'%"';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('type') )
            $where.=' AND type ='.request('type');

        if( request('status') )
            $where.=' AND status ='.request('status');
        else
            $where.=' AND status =1';

        if( request('status') ) {
            $where .= ' AND status =' . request('status');
        }

        if( request('visa_expiry') ){

            $color=request('visa_expiry');
            if($color=="Red"){
                $where.=' AND visa_expiration_date <= "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'" ';
            }

            if($color=="Yellow"){
                $where.=' AND visa_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'" ';
            }

            if($color=="Green"){
                $where.=' AND visa_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")).'" ';
            }
        }

        if( request('insurance_expiry') ){

            $color=request('insurance_expiry');
            if($color=="Red"){
                $where.=' AND insurance_expiration_date <= "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'" ';
            }

            if($color=="Yellow"){
                $where.=' AND insurance_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'" ';
            }

            if($color=="Green"){
                $where.=' AND insurance_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")).'" ';
            }
        }

        if( request('rera_card_expiry') ){

            $color=request('rera_card_expiry');
            if($color=="Red"){
                $where.=' AND rera_card_expiration_date <= "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'" ';
            }

            if($color=="Yellow"){
                $where.=' AND rera_card_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'" ';
            }

            if($color=="Green"){
                $where.=' AND rera_card_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")).'" ';
            }
        }

        if( request('labour_card_expiry') ){

            $color=request('labour_card_expiry');
            if($color=="Red"){
                $where.=' AND labour_card_expiration_date <= "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'" ';
            }

            if($color=="Yellow"){
                $where.=' AND labour_card_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'" ';
            }

            if($color=="Green"){
                $where.=' AND labour_card_expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")).'" ';
            }
        }

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM admins WHERE company_id=".$adminAuth->company_id.$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM admins WHERE company_id=".$adminAuth->company_id.$where.$orderBy);
        }else{
            $Records=DB::select("SELECT * FROM admins WHERE company_id=".$adminAuth->company_id.$where.$orderBy." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $obj['pic_name']='<div class="avatar m-0">
                                <img src="'.(($row->pic_name) ? '/storage/'.$row->pic_name : '/images/Defult2.jpg').'" alt="avtar img holder" height="32" width="32">
                            </div>';

            $obj['firstname']=$row->firstname;
            $obj['lastname']=$row->lastname;
            $obj['type']=AdminType[$row->type];
            $obj['email']=$row->email;
            $obj['status']='<span class="badge badge-pill badge-light-'.ADMIN_STATUS_COLOR[$row->status].'">'.ADMIN_STATUS_NAME[$row->status].'</span>';
            $obj['date_joined']=($row->date_joined) ? date('d-m-Y',strtotime($row->date_joined)):'';
            $date_two = \Carbon\Carbon::parse($today);

            $days = $date_two->diffInDays($row->visa_expiration_date);
            $visaColor='';
            if($row->visa_expiration_date<$today){
                $visaColor = 'danger';
            }else {
                if ($days < $EU_setting_1->time)
                    $visaColor = 'success';
                if ($days < $EU_setting_2->time)
                    $visaColor = 'warning';
                if ($days < $EU_setting_3->time)
                    $visaColor = 'danger';
            }
            $obj['visa_expiration_date']=($row->visa_expiration_date && $row->status==1) ? '<span class="text-'.$visaColor.'">'.date('d-m-Y',strtotime($row->visa_expiration_date)).'</span>':'';

            $days = $date_two->diffInDays($row->insurance_expiration_date);
            $insuranceColor='';
            if($row->insurance_expiration_date<$today){
                $insuranceColor = 'danger';
            }else {
                if ($days < $EU_setting_1->time)
                    $insuranceColor = 'success';
                if ($days < $EU_setting_2->time)
                    $insuranceColor = 'warning';
                if ($days < $EU_setting_3->time)
                    $insuranceColor = 'danger';
            }
            $obj['insurance_expiration_date']=($row->insurance_expiration_date && $row->status==1) ? '<span class="text-'.$insuranceColor.'">'.date('d-m-Y',strtotime($row->insurance_expiration_date)).'</span>':'';

            $days = $date_two->diffInDays($row->rera_card_expiration_date);
            $rera_cardColor='';
            if($row->rera_card_expiration_date<$today){
                $rera_cardColor = 'danger';
            }else {
                if ($days < $EU_setting_1->time)
                    $rera_cardColor = 'success';
                if ($days < $EU_setting_2->time)
                    $rera_cardColor = 'warning';
                if ($days < $EU_setting_3->time)
                    $rera_cardColor = 'danger';
            }
            $obj['rera_card_expiration_date']=($row->rera_card_expiration_date && $row->status==1) ? '<span class="text-'.$rera_cardColor.'">'.date('d-m-Y',strtotime($row->rera_card_expiration_date)).'</span>':'';

            $days = $date_two->diffInDays($row->labour_card_expiration_date);
            $labour_cardColor='';
            if($row->labour_card_expiration_date<$today){
                $labour_cardColor = 'danger';
            }else {
                if ($days < $EU_setting_1->time)
                    $labour_cardColor = 'success';
                if ($days < $EU_setting_2->time)
                    $labour_cardColor = 'warning';
                if ($days < $EU_setting_3->time)
                    $labour_cardColor = 'danger';
            }
            $obj['labour_card_expiration_date']=($row->labour_card_expiration_date && $row->status==1) ? '<span class="text-'.$labour_cardColor.'">'.date('d-m-Y',strtotime($row->labour_card_expiration_date)).'</span>':'';

            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);

            $action='';
            /*if($adminAuth->super==1 || $row->status==1)
                $action.='<a class="admin-edit" href="/admin/admin-edit/'.$row->id.'"><i class="feather icon-edit font-medium-3 mr-50"></i></a>';

            $action.='
                <a href="javascript:void(0)" class="change-password" title="Change Password" data-toggle="modal" data-target="#ModalChangePassword"><i class="users-edit-icon fa fa-lock font-medium-3 mr-50"></i></a>';*/

            if( $adminAuth->super==1 || ( $row->status==1 && $adminAuth->type<$row->type ) ){
                $action.='<a class="admin-edit" href="/admin/admin-edit/'.$row->id.'"><i class="feather icon-edit font-medium-3 mr-50"></i></a>';

                $action.='<a href="javascript:void(0)" class="change-password" title="Change Password" data-toggle="modal" data-target="#ModalChangePassword"><i class="users-edit-icon fa fa-lock font-medium-3 mr-50"></i></a>';
            }

            $obj['action']='<div class="action d-flex" data-id="'.$row->id.'" data-model="'.route('admin.delete').'" data-edit="'.route('admin.edit').'">
                            '.$action.'

                          </div>';
            $data[] = $obj;
            $obj=[];
        }
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return json_encode($response);
    }

    public function profile(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/admins",'name'=>"Users"], ['name'=>"Profile"]
        ];

        $adminAuth=\Auth::guard('admin')->user();

        $admin=Admin::find(request('id'));

        if(!$admin || $admin->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        return view('/admin/admin-profile', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'admin' => $admin,
        ]);
    }

    public function profileAuth(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['name'=>"Profile"]
        ];
        $admin=\Auth::guard('admin')->user();;

        return view('/admin/admin-profile', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'admin' => $admin,
        ]);

    }

    public function Delete(){
        $admin = Admin::find( request('Delete') );

        $adminAuth=\Auth::guard('admin')->user();
        if(!$admin || $admin->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $admin->delete();

        return redirect('admin/admins');
    }

    public function changePassword(Request $request){
        $request->validate([
            'password'=>'required',
            'changePassword'=>'required'
        ]);

        $admin = Admin::find(request('changePassword'));

        $adminAuth=\Auth::guard('admin')->user();
        if(!$admin || $admin->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $company=Company::find($admin->company_id);

        $admin->password=Hash::make(request('password'));
        $admin->save();

        $url=env('MD_URL').'/api/admin/change-password';
        $token=$company->md_token;
        $data=[
            'in_crm_id'=>$admin->id,
            'password'=>request('password')
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('admin/admins');
    }

    public function getInfo(Request $request){
        $request->validate([
            'admin'=>'required'
        ]);

        return $admin = Admin::find(request('admin'));
    }

    public function changePasswordOwn(Request $request){
        $request->validate([
            'oldPassword'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required',
        ]);

        $adminAuth=\Auth::guard('admin')->user();
        if (Hash::check(request('oldPassword'), $adminAuth->password)) {
            $admin = Admin::find($adminAuth->id);
            $admin->password = Hash::make(request('newPassword'));
            $admin->save();

            $company=Company::find($admin->company_id);

            $url = env('MD_URL') . '/api/admin/change-password';
            $token=$company->md_token;
            $data = [
                'in_crm_id' => $admin->id,
                'password' => request('password')
            ];

            Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);
        }else{
            Session::flash('error', 'The old password is incorrect.');
        }

        return redirect()->back();
    }

    public function eleven(){
        /*$client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.elevenlabs.io/v1/convai/agents', [
            'headers' => [
                'xi-api-key' => 'sk_be27008e49477621fed61c770897a25333a25e141467a30f',
            ],
        ]);
        echo $response->getBody();*/

        /*$client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://api.elevenlabs.io/v1/convai/agents/agent_7301k4sbbz9hevqvke70r2egaay2/link', [
            'headers' => [
                'xi-api-key' => 'sk_be27008e49477621fed61c770897a25333a25e141467a30f',
            ],
        ]);
        echo $response->getBody();*/


        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://api.elevenlabs.io/v1/text-to-speech/JBFqnCBsd6RMkjVDRZzb?output_format=mp3_44100_128', [
            'body' => '{
              "text": "The first move is what sets everything in motion.",
              "model_id": "eleven_multilingual_v2"
            }',
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'xi-api-key' => 'sk_be27008e49477621fed61c770897a25333a25e141467a30f',
                        ],
                    ]);
        echo $response->getBody();


    }

}

