<?php

namespace App\Http\Controllers\Auth;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Models\Admin;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'type' => ['required', 'number', 'max:1'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(Request $request)//create(array $data)
    {
        $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'AdminType' => ['required'],//, 'number', 'max:1'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:6'],//, 'confirmed'
        ],
            [
                'email.required' => 'Email is required',
                'email.unique' => 'Email is already existing',
            ]);

        $path="";
        $pic_name=null;
        if ($request->file('PicName')) {
            $imagePath = $request->file('PicName');

            $path = $request->file('PicName')->store('public/images');
        }

        if( $path ){
            $pic_name=explode("/",$path);
            $pic_name=end ($pic_name);
        }

        $adminAuth = \Auth::guard('admin')->user();

        if(request('status')==1){
            $check=(new AdminController() )->company_info( (($request->AdminType==8) ?  'other' : 'primary') );
            if( $check==0 ){
                return redirect('/admin/admins');
            }
        }

        $Admin=Admin::create([
            'company_id'=>$adminAuth->company_id,
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
            'supervisor_id'=>$request->supervisor,
            'office_tel'=>$request->OfficeTel,
            'emergency_contact_name'=>$request->EmergencyContactName,
            'emergency_contact_number'=>$request->EmergencyContactNumber,
            'emergency_contact_email'=>$request->EmergencyContactEmail,
            'emergency_contact_relation'=>$request->EmergencyContactRelation,
            'emergency_contact_address'=>$request->EmergencyContactAddress,
            'address'=>$request->Address,
            'password'=>Hash::make($request->password),
            'basic_salary'=>(request('BasicSalary')) ? str_replace(',','',request('BasicSalary')) : null,
            'allowance_salary'=>(request('AllowanceSalary')) ? str_replace(',','',request('AllowanceSalary')) : null,
            'commission'=>request('Commission'),
            'payment_method'=>request('PaymentMethod'),
            'bank_id'=>request('Bank'),
            'account_number'=>request('AccountNumber'),
            'iban_number'=>request('IBANNumber'),
            'labour_personal_id'=>request('LabourPersonalId'),
            'bank_routing_code'=>request('BankRoutingCode'),
            'leave_days'=>request('LeaveDays'),
            'use_annual_current_year'=>request('UseAnnualCurrentYear'),
            'use_annual_previous_year'=>request('UseAnnualPreviousYear'),
            'visa_expiration_date'=>request('VisaExpirationDate'),
            'insurance_expiration_date'=>request('InsuranceExpirationDate'),
            'labour_card_expiration_date'=>request('LabourCardExpirationDate'),
            'rera_card_expiration_date'=>request('ReraCardExpirationDate'),
            'passport'=>request('passport'),
            'emirates_id'=>request('emirates_id'),
            'labour_contract'=>request('labour_contract'),
            'labour_card'=>request('labour_card'),
            'residents_visa'=>request('residents_visa'),
            'insurance'=>request('insurance'),
            'rera_card'=>request('rera_card'),
            'job_offer'=>request('job_offer'),
            'contract_of_employment'=>request('contract_of_employment'),
            'cancellation'=>request('cancellation'),
            'other'=>request('other'),
        ]);

        $url=env('MD_URL').'/api/admin/store';
        $company=Company::find($adminAuth->company_id);
        $data=[
            'in_crm_id'=>$Admin->id,
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
            'address'=>$request->Address,
            'password'=>$request->password
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        return redirect('admin/admins');
    }
    protected function hfhfhfhgfgg(Request $request)
    {
        $Admin=Admin::create([
            'firstname'=>'Test',
            'lastname'=>'Account',
            'date_birth'=>'1992-08-01',
            'date_joined'=>date('Y-m-d'),
            'gender'=>'1',
            'type'=>'1',
            'email'=>'testaccount@MDcrms.com',
            'personal_email'=>'',
            'main_number'=>$request->MainNumber,
            'mobile_number'=>$request->MobileNumber,
            'password'=>Hash::make('123456')
        ]);

        return redirect('admin/admins');
    }
}
