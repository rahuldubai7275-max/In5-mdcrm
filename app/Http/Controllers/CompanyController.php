<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyController extends Controller
{

    public function company(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        $adminAuth=\Auth::guard('admin')->user();
        $company=company::where('id',$adminAuth->company_id)->first();
        return view('/admin/company-profile', [
            'pageConfigs' => $pageConfigs,
            'company'=>$company
        ]);
    }

    public function apiStore(Request $request){
        $request->validate([
            'name'=>'required|string',
            'brand'=>'required|string',
            'sample'=>'required|string',
            'email'=>'required|string',
            'expiry_date'=>'required',
            'primary_user'=>'required',
            'employees_user'=>'required',
            'package'=>'required',
        ]);

        $i=0;
        while ($i==0){
            $api_key=Str::random(40);
            $company_api_key=Company::where('api_key',$api_key)->first();
            if(!$company_api_key)
                $i++;
        }

        $company=Company::create([
            'name'=>request('name'),
            'brand'=>request('brand'),
            'sample'=>request('sample'),
            'last_property_ref'=>0,
            'office_tel'=>request('phone_number'),
            'primary_email'=>request('email'),
            'website'=>request('website'),
            'expiry_date'=>request('expiry_date'),
            'primary_user'=>request('primary_user'),
            'employees_user'=>request('employees_user'),
            'package'=>request('package'),
            'api_key'=>$api_key,
            'md_token'=>request('md_token'),
            'pf_integrate'=>request('pf_integrate'),
            'bayut_integrate'=>request('bayut_integrate'),
            'pf_key'=>request('pf_key'),
            'pf_secret'=>request('pf_secret'),
            'bayut_key'=>request('bayut_key'),
            'private'=>( request('private') ) ? 1 : 0,
        ]);

        if($company){
            $Admin=Admin::create([
                'company_id'=>$company->id,
                'firstname'=>request('firstname'),
                'lastname'=>request('lastname'),
                'type'=>1,
                'main_super'=>1,
                'super'=>1,
                'email'=>request('email'),
                'office_tel'=>request('phone_number'),
                'password'=>Hash::make($request->password)
            ]);

            $url=env('MD_URL').'/api/admin/store';
            $data=[
                'in_crm_id'=>$Admin->id,
                'firstname'=>$request->firstname,
                'lastname'=>$request->lastname,
                'email'=>request('email'),
                'type'=>1,
                'main_super'=>1,
                'super'=>1,
                'office_tel'=>request('phone_number'),
                'password'=>$request->password
            ];

            Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);
        }

    }

    public function apiEdit(Request $request){
        $request->validate([
            'brand'=>'required|string',
            'sample'=>'required|string',
            'expiry_date'=>'required',
            'primary_user'=>'required',
            'employees_user'=>'required',
            'package'=>'required',
            'md_token'=>'required',
        ]);

        $Company = Company::where('md_token',request('md_token'))->first();

        if($Company) {
            $Company->brand = request('brand');
            $Company->sample = request('sample');
            $Company->expiry_date = request('expiry_date');
            $Company->primary_user = request('primary_user');
            $Company->employees_user = request('employees_user');
            $Company->package = request('package');
            $Company->pf_integrate = request('pf_integrate');
            $Company->bayut_integrate = request('bayut_integrate');
            $Company->pf_key = request('pf_key');
            $Company->pf_secret = request('pf_secret');
            $Company->bayut_key = request('bayut_key');
            $Company->private = (request('private')) ? 1 : 0;
            $Company->save();
        }
    }
    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);

        $adminAuth=\Auth::guard('admin')->user();

        $Company = Company::find($adminAuth->company_id);

        $path = "";
        if ($request->file('logo')) {
            $path = $request->file('logo')->store('public/images');
        }

        if ($path) {
            $logo = explode("/", $path);
            $logo = end($logo);

            Storage::delete('public/images/' . $Company->logo);
            $Company->logo = $logo;
        }

        $w_path = "";
        if ($request->file('watermark')) {
            $w_path = $request->file('watermark')->store('public/images');
        }

        if ($w_path) {
            $watermark = explode("/", $w_path);
            $watermark = end($watermark);

            Storage::delete('public/images/' . $Company->watermark);
            $Company->watermark = $watermark;
        }

        $Company->name = request('name');
        $Company->rera_orn=request('rera_orn');
        $Company->trn=request('trn');
        $Company->address=request('address');
        $Company->office_tel=request('office_tel');
        $Company->office_fax=request('office_fax');
        $Company->primary_email=request('primary_email');
        $Company->website=request('website');
        $Company->company_profile=request('company_profile');
        $Company->facebook=request('facebook');
        $Company->instagram=request('instagram');
        $Company->tiktok=request('tiktok');
        $Company->linkedin=request('linkedin');
        $Company->youtube=request('youtube');
        $Company->save();
        return redirect('/admin/company-profile');
    }

    public function Delete(){
        $Company = Company::find( request('Delete') );
        Storage::delete('public/images/'.$Company->picname);
        $Company->delete();
        return redirect('/admin/company-profile');
    }

}
