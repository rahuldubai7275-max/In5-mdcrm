<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Company;
use App\Models\Setting;
use App\Models\SettingAdmin;
use App\Models\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SettingController extends Controller
{
    public function Settings(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->super!=1){
            return view('errors.403');
        }
        return view('/admin/settings', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function openLead(Request $request){
        $status=(request('lead_switch')) ? '1' : '0';
        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','open_lead')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'open_lead',
                'time_type'=>request('lead_time_type'),
                'time'=>request('lead_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;

        if($status==1){
            $Setting->time_type=request('lead_time_type');
            $Setting->time=request('lead_time');

            SettingAdmin::where('setting_id',$Setting->id)->delete();
            if(request('lead_user')) {
                foreach (request('lead_user') as $admin_id) {
                    SettingAdmin::create([
                        'setting_id' => $Setting->id,
                        'admin_id' => $admin_id
                    ]);
                }
            }
        }

        $Setting->save();

        return redirect('/admin/settings');
    }

    public function openMA(Request $request){
        $status=(request('ma_switch')) ? '1' : '0';

        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','open_ma')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'open_ma',
                'time_type'=>request('ma_time_type'),
                'time'=>request('ma_time'),
                'status'=>$status
            ]);
        }
        $setting_id=$Setting->id;
        $Setting->status = $status;

        if($status==1){
            $Setting->time_type=request('ma_time_type');
            $Setting->time=request('ma_time');

            SettingAdmin::where('setting_id',$setting_id)->delete();
            if(request('ma_user')) {
                foreach (request('ma_user') as $admin_id) {
                    SettingAdmin::create([
                        'setting_id' => $setting_id,
                        'admin_id' => $admin_id
                    ]);
                }
            }
        }

        $Setting->save();

        return redirect('/admin/settings');
    }

    public function openBuyerTenant(Request $request){
        $status=(request('buyer_tenant_switch')) ? '1' : '0';

        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','open_buyer_tenant')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'open_buyer_tenant',
                'time_type'=>request('buyer_tenant_time_type'),
                'time'=>request('buyer_tenant_time'),
                'status'=>$status
            ]);
        }
        $setting_id=$Setting->id;
        $Setting->status = $status;

        if($status==1){
            $Setting->time_type=request('buyer_tenant_time_type');
            $Setting->time=request('buyer_tenant_time');

            SettingAdmin::where('setting_id',$setting_id)->delete();
            if(request('buyer_tenant_user')) {
                foreach (request('buyer_tenant_user') as $admin_id) {
                    SettingAdmin::create([
                        'setting_id' => $setting_id,
                        'admin_id' => $admin_id
                    ]);
                }
            }
        }

        $Setting->save();

        return redirect('/admin/settings');
    }

    public function MainAccess(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        Admin::whereNull('main_super')->where('company_id',$adminAuth->company_id)->update(['super' => null]);
        if(request('main_access_user')) {
            foreach (request('main_access_user') as $admin_id) {
                $admin=Admin::find($admin_id);
                $admin->super=1;
                $admin->save();
            }
        }
        return redirect('/admin/settings');
    }

    public function HRAccess(Request $request){
        $setting_id=16;
        $Setting = Setting::find($setting_id);
        $Setting->status = 1;

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('hr_access_user')) {
            foreach (request('hr_access_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $Setting->save();

        $url=env('MD_URL').'/api/admin/access';
        $token=env('MD_TOKEN');
        $data=[
            'in_crm_ids'=>(request('hr_access_user')) ? join(',',request('hr_access_user')) : '',
            'access'=>'hr'
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('/admin/settings');
    }

    public function uploadContact(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','upload_contact')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'upload_contact',
                'status'=>1
            ]);
        }
        $setting_id=$Setting->id;
        $Setting->status = 1;

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('upload_contact_user')) {
            foreach (request('upload_contact_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $Setting->save();

        return redirect('/admin/settings');
    }

    public function taskAccess(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','all_task')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'all_task',
                'status'=>request('status')
            ]);
        }
        $setting_id=$Setting->id;
        $Setting->status = request('status');

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('task_access_user')) {
            foreach (request('task_access_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $url=env('MD_URL').'/api/admin/access';

        $company=Company::find($adminAuth->company_id);
        $token=$company->md_token;
        $data=[
            'in_crm_ids'=>(request('task_access_user')) ? join(',',request('task_access_user')) : '',
            'access'=>'task'
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        $Setting->save();

        return redirect('/admin/settings');
    }

    public function SurveyAccess(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','survey_access')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'survey_access',
                'time_type'=>'0',
                'status'=>1
            ]);
        }
        $setting_id=$Setting->id;
        $Setting->status = 1;

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('survey_access_user')) {
            foreach (request('survey_access_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $Setting->save();

        $url=env('MD_URL').'/api/admin/access';

        $company=Company::find($adminAuth->company_id);
        $token=$company->md_token;

        $data=[
            'in_crm_ids'=>(request('survey_access_user')) ? join(',',request('survey_access_user')) : '',
            'access'=>'survey'
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('/admin/settings');
    }

    public function RequestApprover(Request $request){
        $setting_id=22;
        $Setting = Setting::find($setting_id);
        $Setting->status = 1;

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('request_main_user')) {
            foreach (request('request_main_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $Setting->save();

        $url=env('MD_URL').'/api/admin/access';
        $token=env('MD_TOKEN');
        $data=[
            'in_crm_ids'=>(request('request_main_user')) ? join(',',request('request_main_user')) : '',
            'access'=>'decision_maker'
        ];
        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        $setting_id=17;
        $Setting = Setting::find($setting_id);
        $Setting->status = 1;

        SettingAdmin::where('setting_id',$setting_id)->delete();
        if(request('request_approver_user')) {
            foreach (request('request_approver_user') as $admin_id) {
                SettingAdmin::create([
                    'setting_id' => $setting_id,
                    'admin_id' => $admin_id
                ]);
            }
        }

        $Setting->save();

        $url=env('MD_URL').'/api/admin/access';
        $token=env('MD_TOKEN');
        $data=[
            'in_crm_ids'=>(request('request_approver_user')) ? join(',',request('request_approver_user')) : '',
            'access'=>'request_controller'
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('/admin/settings');
    }

    public function expirationProperty(Request $request){
        $status=1;

        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_1')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_property_1',
                'time_type'=>3,
                'time'=>request('first_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('first_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_2')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_property_2',
                'time_type'=>3,
                'time'=>request('second_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('second_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_3')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_property_3',
                'time_type'=>3,
                'time'=>request('third_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('third_warning_time');
        $Setting->save();

        return redirect('/admin/settings');
    }

    public function expirationUser(Request $request){
        $status=1;

        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_1')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_user_1',
                'time_type'=>3,
                'time'=>request('first_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('first_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_2')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_user_2',
                'time_type'=>3,
                'time'=>request('second_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('second_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_3')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'expiration_user_3',
                'time_type'=>3,
                'time'=>request('third_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('third_warning_time');
        $Setting->save();

        return redirect('/admin/settings');
    }

    public function contactLastActivity(Request $request){
        $adminAuth=\Auth::guard('admin')->user();
        $status=1;
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_1')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'contact_activity_1',
                'time_type'=>3,
                'time'=>request('first_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('first_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_2')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'contact_activity_2',
                'time_type'=>3,
                'time'=>request('second_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('second_warning_time');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_3')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'contact_activity_3',
                'time_type'=>3,
                'time'=>request('third_warning_time'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->time_type=3;
        $Setting->time=request('third_warning_time');
        $Setting->save();

        return redirect('/admin/settings');
    }

    public function calendarColor(Request $request){
        $status=1;
        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_viewing')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'calendar_viewing',
                'value'=>request('viewing'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('viewing');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_appointment')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'calendar_appointment',
                'value'=>request('appointment'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('appointment');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_reminder')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'calendar_reminder',
                'value'=>request('reminder'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('reminder');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_cancelled')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'calendar_cancelled',
                'value'=>request('cancelled'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('cancelled');
        $Setting->save();

        return redirect('/admin/settings');
    }

    public function brochureBG(Request $request){
        $status=1;

        $adminAuth=\Auth::guard('admin')->user();
        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','brochure_sale_bg')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'brochure_sale_bg',
                'value'=>request('bg_sale'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('bg_sale');
        $Setting->save();

        $Setting = Setting::where('company_id',$adminAuth->company_id)->where('title','brochure_rent_bg')->first();
        if(!$Setting){
            $Setting=Setting::create([
                'company_id'=>$adminAuth->company_id,
                'title'=>'brochure_rent_bg',
                'value'=>request('bg_rent'),
                'status'=>$status
            ]);
        }
        $Setting->status = $status;
        $Setting->value=request('bg_rent');
        $Setting->save();

        return redirect('/admin/settings');
    }

}
