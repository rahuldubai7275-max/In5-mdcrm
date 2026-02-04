<?php

namespace App\Http\Controllers;

use App\Exports\ExportLead;
use App\Imports\LeadImports;
use App\Models\Admin;
use App\Models\ClusterStreet;
use App\Models\Community;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactSource;
use App\Models\LeadNote;
use App\Models\MasterProject;
use App\Models\Portal;
use App\Models\PortalProperty;
use App\Models\Property;
use App\Models\Survey;
use App\Models\VillaType;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class LeadController extends Controller
{
    public function Leads(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/leads', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    public function Leads_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/leads-sm', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    public function Lead(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/leads",'name'=>"Leads"], ['name'=>"Lead"]
        ];

        $adminAuth=\Auth::guard('admin')->user();

        $leadMax=0;
        $lead='';
        $offPlanProject='';
        $route=route('lead.add');
        if(request('id')){
            $lead=Lead::find(request('id'));
            if(!$lead || $lead->company_id!=$adminAuth->company_id){
                return abort(404);
            }else{
                if($lead->admin_id!=$adminAuth->id && $adminAuth->type>2){//$lead->private==1 &&
                    return abort(404);
                }
                /*if($lead->private==0 && $adminAuth->type>2){
                    return abort(404);
                }*/
            }

            if($lead->off_plan_project_id){
                $company=Company::find($adminAuth->company_id);

                $url=env('MD_URL').'/api/off-plan-project/detail';

                $data=['id'=>$lead->off_plan_project_id];
                $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

                $offPlanProject=json_decode($response);
            }
            $route=route('lead.edit');
        }else{
            $leadMax=Lead::max('id');
            $leadMax++;
        }

        return view('/admin/lead', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'leadMax' => $leadMax,
            'lead' => $lead,
            'offPlanProject' => $offPlanProject,
            'route' => $route,
        ]);
    }

    public function exportLeads(Request $request){
        return Excel::download(new ExportLead, 'Leads.xlsx');
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $assign_to=request('assign_to');
        $private=0;
        if($adminAuth->type==3 || $adminAuth->type==4){
            $assign_to=$adminAuth->id;
        }

        if($company->private==1 && $assign_to==$adminAuth->id){
            $private=1;
        }

        $lead=Lead::create([
            //'property_id'=>request('property'),
            'company_id'=>$adminAuth->company_id,
            'type'=>'manual',
            'admin_id'=>$adminAuth->id,
            'private'=>$private,
            'name'=>request('name'),
            'mobile_number'=>request('mobile_number'),
            'mobile_number_2'=>request('mobile_number_2'),
            'email'=>request('email'),
            'contact_category'=>request('contact_category'),
            'source'=>request('source'),
            'referrer_id'=>request('referrer'),
            'looking_for'=>request('looking_for'),
            'developer_id'=>request('developer'),
            'emirate_id'=>request('emirate'),
            'master_project_id'=>request('master_project'),
            'community_id'=>request('community'),
            'off_plan_project_id'=>request('off_plan_project'),
            'budget'=>(request('budget')) ? str_replace(',','',request('budget')) : null,
            'job_title_id'=>request('job_title'),
            'assign_to'=>$assign_to,
            'assign_time'=>date('Y-m-d H:i:s'),
            'open_date'=>date('Y-m-d H:i:s'),
            'seen'=>($private==1) ? 1:0,
        ]);
        if(request('note')) {
            LeadNote::create([
                'company_id' => $adminAuth->company_id,
                'lead_id' => $lead->id,
                'note_subject'=>4,
                'admin_id' => $adminAuth->id,
                'note' => request('note')
            ]);
        }
        return redirect('/admin/leads');
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');

        $file_name=$request->file('file')->getClientOriginalName();

        $path="";
        $excel_name=null;
        if ($request->file('file')) {
            $imagePath = $request->file('file');

            $path = $request->file('file')->store('public/images');
        }

        if( $path ){
            $excel_name=explode("/",$path);
            $excel_name=end ($excel_name);
        }

        // Process the Excel file
        Excel::import(new LeadImports, $file);

        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }

    public function edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $assign_to=request('assign_to');

        if($adminAuth->type==3 || $adminAuth->type==4){
            $assign_to=$adminAuth->id;
        }

        $lead=Lead::find(request('_id'));

        if(!$lead || $lead->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $developer_id=null;
        $off_plan_project_id=null;
        $master_project_id=null;
        $community_id=null;
        $looking_for=request('looking_for');
        if($looking_for=='1'){
            $master_project_id=request('master_project');
            $community_id=request('community');
        }
        if($looking_for=='2'){
            $developer_id=request('developer');
            $off_plan_project_id=request('off_plan_project');
        }

        $lead->name=request('name');
        $lead->mobile_number=request('mobile_number');
        $lead->mobile_number_2=request('mobile_number_2');
        $lead->email=request('email');
        $lead->contact_category=request('contact_category');
        $lead->source=request('source');
        $lead->referrer_id=request('referrer');
        $lead->looking_for=$looking_for;
        $lead->developer_id=$developer_id;
        $lead->emirate_id=request('emirate');
        $lead->master_project_id=$master_project_id;
        $lead->community_id=$community_id;
        $lead->off_plan_project_id=$off_plan_project_id;
        $lead->budget=(request('budget')) ? str_replace(',','',request('budget')) : null;
        $lead->job_title_id=request('job_title');
        if($lead->assign_to != $assign_to) {
            $lead->assign_to = $assign_to;
            $lead->assign_time = date('Y-m-d H:i:s');
            $lead->open_date = date('Y-m-d H:i:s');
        }

        $lead->save();

        return redirect('/admin/lead/view/'.$lead->id);
    }

    public function closeLead(Request $request){
        $request->validate([
            'lead'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $lead=Lead::find(request('lead'));

        if(!$lead || $lead->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $lead->status=2;
        $lead->result_specifier=$adminAuth->id;
        $lead->colse_reason=request('colse_reason');
        $lead->result_date=date('Y-m-d H:i:s');
        $lead->seen=1;
        $lead->save();

        return redirect('/admin/leads');
    }

    public function getLeads(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' ORDER BY created_at DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $where='';
        $status_0=0;
        $lead_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_lead')->first();
        $lead_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$lead_setting->id)->where('admin_id','!=',$adminAuth->id)->pluck('admin_id')->toArray();

        if($lead_setting->status==1 && $adminAuth->type>1 && $lead_setting_admin_id) {
            //if($lead_setting_admin_id)
            $where .= ' AND assign_to NOT IN ('.join(',',$lead_setting_admin_id).') ';
        }

        if($adminAuth->type>2){
            $now=date('Y-m-d H:i:s');
            if($lead_setting->status==1) {
                if ($lead_setting->time_type == 1) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " minutes"));
                }

                if ($lead_setting->time_type == 2) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " hours"));
                }

                if ($lead_setting->time_type == 3) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " days"));
                }

                $where.="AND (assign_to=".$adminAuth->id." OR leads.open_date<'".$date_time."' )";//$where.="AND (assign_to=".$adminAuth->id." OR leads.assign_time<'".$date_time."' )";
            }else{
                $where.="AND assign_to=".$adminAuth->id;//$date_time=date('Y-m-d H:i:s',strtotime($now. " - 1 days") );
            }
        }

        if( request('leads') ){
            $where.=' AND leads.seen=0';
            $where .= ' AND leads.assign_to="' . $adminAuth->id . '"';
        }else{
            if( request('client_manager') ){
                if(request('client_manager') && request('status')=='1') {
                    $where .= ' AND leads.result_specifier=' . request('client_manager');
                }else {
                    if(request('client_manager')=='null')
                        $where .= ' AND leads.assign_to IS NULL';
                    else
                        $where .= ' AND leads.assign_to="' . request('client_manager') . '"';
                }
                $status_0++;
            }
        }

        if( request('contact_category') ){
            $where.=' AND leads.contact_category="'.request('contact_category').'"';
            $status_0++;
        }

        if( request('type') ){
            $where.=' AND leads.type="'.request('type').'"';
            $status_0++;
        }

        if( request('portal') ){
            $where.=' AND leads.portal="'.request('portal').'"';
            $status_0++;
        }

        if( request('private') ){
            $where.=' AND leads.private="'.request('private').'"';
            $where .= ' AND leads.admin_id=' . $adminAuth->id;
            $status_0++;
        }else{
            $where.=' AND leads.private=0 ';
        }

        if( request('source') ){
            $where.=' AND leads.source="'.request('source').'"';
            $status_0++;
        }

        if( request('referrer') ){
            $where.=' AND leads.referrer_id ="'.request('referrer').'"';
            $status_0++;
        }

        if( request('job_title') ){
            $where.=' AND leads.job_title_id ="'.request('job_title').'"';
            $status_0++;
        }

        if( request('developer') ){
            $where.=' AND leads.developer_id ="'.request('developer').'"';
            $status_0++;
        }

        if( request('emirate') ){
            $where.=' AND leads.emirate_id ="'.request('emirate').'"';
            $status_0++;
        }

        if( request('master_project') ){
            $where.=' AND leads.master_project_id ="'.request('master_project').'"';
            $status_0++;
        }

        if( request('off_plan_project') ){
            $where.=' AND leads.off_plan_project_id ="'.request('off_plan_project').'"';
            $status_0++;
        }

        if( request('reason') ){
            $where.=' AND leads.colse_reason ="'.request('reason').'"';
            $status_0++;
        }

        if( request('ref_number') ){
            $where.=' AND leads.id ="'.request('ref_number').'"';$status_0++;
        }

        if( request('ref_number_property') ){
            $where.=' AND leads.property_id ="'.request('ref_number_property').'"';
            $status_0++;
        }

        if( request('mobile_number') ){
            $where.=' AND leads.mobile_number LIKE "%'.request('mobile_number').'%"';
            $status_0++;
        }

        if( request('name') ){
            $where.=' AND leads.name LIKE "%'.request('name').'%"';
            $status_0++;
        }

        if( request('email') ){
            $where.=' AND leads.email LIKE "%'.request('email').'%"';
            $status_0++;
        }

        if( request('from_budget') )
            $where.=' AND leads.budget >='.str_replace(',','',request('from_budget'));

        if( request('to_budget') )
            $where.=' AND leads.budget <='.str_replace(',','',request('to_budget'));

        if( request('from_date') ){
            $where.=' AND leads.created_at >="'.request('from_date').' 00:00:00"';
            $status_0++;
        }

        if( request('to_date') ) {
            $where .= ' AND leads.created_at <="' . request('to_date') . ' 23:59:59"';
            $status_0++;
        }

        if( request('status') || request('status')=='0' ) {
            $where .= ' AND leads.status =' . request('status');
            if($adminAuth->type>2 && request('status')=='1'){
                $where.=' AND leads.result_specifier='.$adminAuth->id;
            }
        }else {
            if( request('client_manager') ){
                if($adminAuth->id!=request('client_manager') && $adminAuth->type>=3){
                    $where .= ' AND leads.status !=1';
                }
            }else{
                if(!request('status')){
                    $where .= ' AND leads.status !=1';
                }
            }

            if($status_0==0)
                $where .= ' AND leads.status=0';
            //else
            //    $where .= ' AND leads.status!=3';
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `leads` WHERE company_id=".$adminAuth->company_id." ".( (request('status')==3) ? ' AND status=3' : ' AND status!=3' ) );
        $totalRecords=$totalRecords[0]->countAll;

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM leads WHERE company_id=".$adminAuth->company_id." ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM leads WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy);
        }else{
            $Records=DB::select("SELECT * FROM leads WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy." limit ".$start.",".$rowperpage);
        }

        $Status=[0=>'Open',1=>'Added To Contact',2=>'Closed',3=>'Deleted'];
        $StatusColor=[0=>'primary',1=>'success',2=>'warning',3=>'danger'];

        $obj=[];

        foreach($Records as $row){
            $property=Property::where('id',$row->property_id)->first();
            $admin=Admin::where('id',$row->assign_to)->first();
            $result_specifier=Admin::where('id',$row->result_specifier)->first();
            $creator=Admin::where('id',$row->admin_id)->first();
            $source=ContactSource::where('id',$row->source)->first();
            $master_project=MasterProject::where('id',$row->master_project_id)->first();
            $portal=Portal::where('id',$row->portal)->first();
            $checkbox='';
            if($adminAuth->type<3 || $row->status==2 || ($adminAuth->id==$row->assign_to && ($row->status==2 || $row->status==0))){
                if($row->private==0 || $adminAuth->type==1) {
                    $checkbox = '<div class="d-inline-block checkbox">
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" value="' . $row->id . '" name="lead[]">
                                        </label>
                                    </fieldset>
                                </div>';
                }
            }

            $action='';
            if($row->status != 1) {
                if ( $row->admin_id == $adminAuth->id || ($row->private == 0 && $adminAuth->type <= 2)) {
                    $action = '<a target="_blank" title="Edit" href="/admin/lead/' . $row->id . '"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>';
                }
                //$action .= (($row->private == 1 && $row->admin_id == $adminAuth->id) || $adminAuth->type == 1) ? '<a href="javascript:void(0)" class="ajax-delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '';
            }
            $mobile_number=$row->mobile_number;
            $email=$row->email;

            $isNumberInClosed=Lead::where('mobile_number',$row->mobile_number)->where('status',2)->where('id','!=',$row->id)->orderBy('id', 'ASC')->first();
            if($isNumberInClosed)
                $mobile_number='<div class="is-link"><div><a target="_blank" title="Phone number is closed" href="/admin/lead/view/'.$isNumberInClosed->id.'">'.$row->mobile_number.'</a></div></div>';

            $isNumber=Contact::where('main_number',$row->mobile_number)->orWhere('number_two',$row->mobile_number)->first();
            if($isNumber)
                $mobile_number='<div class="is-link"><div><a target="_blank" title="Phone number is existing" href="/admin/contact/view/'.$isNumber->id.'">'.$row->mobile_number.'</a></div></div>';

            if($email) {
                $isEmailInClosed = Lead::where('email', $row->email)->where('status', 2)->where('id', '!=', $row->id)->orderBy('id', 'ASC')->first();
                if ($isEmailInClosed)
                    $email = '<div class="is-link"><div><a target="_blank" title="Email is existing" href="/admin/lead/view/' . $isEmailInClosed->id . '">' . $isEmailInClosed->email . '</a></div></div>';

                $isEmail = Contact::where('email', $row->email)->orWhere('email_two', $row->email)->first();
                if ($isEmail)
                    $email = '<div class="is-link"><div><a target="_blank" title="Email is existing" href="/admin/contact/view/' . $isEmail->id . '">' . $isEmail->email . '</a></div></div>';
            }
            $obj['checkbox']=$checkbox;

            $obj['id']=$row->id;
            $obj['portal']=($portal) ? $portal->name : 'N/A';
            $obj['type']=LeadType[$row->type];
            $obj['property_id']=($property) ? $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num : 'N/A';
            $obj['name']=$row->name;
            $obj['mobile_number']=$mobile_number;
            $obj['email']=$email;
            $obj['contact_category']=($row->contact_category) ? ucfirst($row->contact_category) : 'N/A';
            $obj['source']=($source) ? ($row->source==41) ? '<span class="w-100 badge badge-pill badge-light-primary">'.$source->name.'</span>':$source->name : 'N/A';
            $obj['master_project_id']=($master_project) ? $master_project->name : 'N/A';
            $obj['status']='<span class="w-100 badge badge-pill badge-light-'.$StatusColor[$row->status] .'">'.$Status[$row->status].'</span>';
            $obj['colse_reason']=($row->colse_reason) ? ((in_array($row->colse_reason, LeadClosedReason)) ? $row->colse_reason : '<a class="reason-view" data-target="#ViewModal" data-toggle="modal">'.$row->colse_reason.'</a>') : 'N/A';
            $obj['result_specifier']=($result_specifier) ? $result_specifier->firstname.' '.$result_specifier->lastname : 'N/A';
            $obj['assign_to']=($admin) ? $admin->firstname.' '.$admin->lastname : 'N/A';
            $obj['assign_time']=($row->assign_time && $row->admin_id) ? \Helper::changeDatetimeFormat($row->assign_time) : 'N/A';
            $obj['admin_id']=($creator) ? $creator->firstname.' '.$creator->lastname : 'N/A';
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);
            $obj['action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("lead.delete").'">
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

    public function getLeads_sm(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = 3;//request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = '';//request('search')['value']; // Search value

        $orderBy=' ORDER BY created_at DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();

        $where='';
        $status_0=0;
        $lead_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_lead')->first();
        $lead_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$lead_setting->id)->where('admin_id','!=',$adminAuth->id)->pluck('admin_id')->toArray();

        if($lead_setting->status==1 && $adminAuth->type>1 && $lead_setting_admin_id) {
            //if($lead_setting_admin_id)
            $where .= ' AND assign_to NOT IN ('.join(',',$lead_setting_admin_id).') ';
        }

        if($adminAuth->type>2){
            $now=date('Y-m-d H:i:s');
            if($lead_setting->status==1) {
                if ($lead_setting->time_type == 1) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " minutes"));
                }

                if ($lead_setting->time_type == 2) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " hours"));
                }

                if ($lead_setting->time_type == 3) {
                    $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " days"));
                }

                $where.="AND (assign_to=".$adminAuth->id." OR leads.open_date<'".$date_time."' )";//$where.="AND (assign_to=".$adminAuth->id." OR leads.assign_time<'".$date_time."' )";
            }else{
                $where.="AND assign_to=".$adminAuth->id;//$date_time=date('Y-m-d H:i:s',strtotime($now. " - 1 days") );
            }
        }

        if( request('leads') ){
            $where.=' AND leads.seen=0';
            $where .= ' AND leads.assign_to="' . $adminAuth->id . '"';
        }else{
            if( request('client_manager') ){
                if(request('client_manager') && request('status')=='1') {
                    $where .= ' AND leads.result_specifier=' . request('client_manager');
                }else {
                    if(request('client_manager')=='null')
                        $where .= ' AND leads.assign_to IS NULL';
                    else
                        $where .= ' AND leads.assign_to="' . request('client_manager') . '"';
                }
                $status_0++;
            }
        }

        if( request('contact_category') ){
            $where.=' AND leads.contact_category="'.request('contact_category').'"';
            $status_0++;
        }

        if( request('type') ){
            $where.=' AND leads.type="'.request('type').'"';
            $status_0++;
        }

        if( request('portal') ){
            $where.=' AND leads.portal="'.request('portal').'"';
            $status_0++;
        }

        if( request('private') ){
            $where.=' AND leads.private="'.request('private').'"';
            $where .= ' AND leads.admin_id=' . $adminAuth->id;
            $status_0++;
        }else{
            $where.=' AND leads.private=0 ';
        }

        if( request('source') ){
            $where.=' AND leads.source="'.request('source').'"';
            $status_0++;
        }

        if( request('master_project') ){
            $where.=' AND leads.master_project_id ="'.request('master_project').'"';
            $status_0++;
        }

        if( request('reason') ){
            $where.=' AND leads.colse_reason ="'.request('reason').'"';
            $status_0++;
        }

        if( request('ref_number') ){
            $where.=' AND leads.id ="'.request('ref_number').'"';$status_0++;
        }

        if( request('ref_number_property') ){
            $where.=' AND leads.property_id ="'.request('ref_number_property').'"';
            $status_0++;
        }

        if( request('mobile_number') ){
            $where.=' AND leads.mobile_number LIKE "%'.request('mobile_number').'%"';
            $status_0++;
        }

        if( request('name') ){
            $where.=' AND leads.name LIKE "%'.request('name').'%"';
            $status_0++;
        }

        if( request('email') ){
            $where.=' AND leads.email LIKE "%'.request('email').'%"';
            $status_0++;
        }

        if( request('from_date') ){
            $where.=' AND leads.created_at >="'.request('from_date').' 00:00:00"';
            $status_0++;
        }

        if( request('to_date') ) {
            $where .= ' AND leads.created_at <="' . request('to_date') . ' 23:59:59"';
            $status_0++;
        }

        if( request('status') || request('status')=='0' ) {
            $where .= ' AND leads.status =' . request('status');
            if($adminAuth->type>2 && request('status')=='1'){
                $where.=' AND leads.result_specifier='.$adminAuth->id;
            }
        }else {
            if( request('client_manager') ){
                if($adminAuth->id!=request('client_manager') && $adminAuth->type>=3){
                    $where .= ' AND leads.status !=1';
                }
            }

            if($status_0==0)
                $where .= ' AND leads.status=0';
//            else
//                $where .= ' AND leads.status!=3';
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `leads` WHERE company_id=".$adminAuth->company_id." ".( (request('status')==3) ? ' AND status=3' : ' AND status!=3' ) );
        $totalRecords=$totalRecords[0]->countAll;

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM leads WHERE company_id=".$adminAuth->company_id." ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        //$data = array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM leads WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy);
        }else{
            $Records=DB::select("SELECT * FROM leads WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy." limit ".$start.",".$rowperpage);
        }

        $data='';

        foreach($Records as $row){
            $property=Property::where('id',$row->property_id)->first();
            $admin=Admin::where('id',$row->assign_to)->first();
            $source=ContactSource::where('id',$row->source)->first();
            $checkbox='';
            if($adminAuth->type<3 || $row->status==2 || ($adminAuth->id==$row->assign_to && ($row->status==2 || $row->status==0))){
                if($row->private==0)
                    $checkbox='<input type="checkbox" class="d-none" value="'.$row->id.'" name="lead[]">';
            }

            $img = '';//'<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/images/Default.png"></div>';
            if($property) {
                $pictures = explode(',', $property->pictures);
                if ($property->pictures)
                    $img = '<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/storage/' . $pictures[0] . '"></div>';
            }

            $data.='<div class="card mb-2 hold-box" data-id="'.$row->id.'">
                    '.$checkbox.'
                    <div class="card-body p-1">
                        <div class="d-flex">
                            <div>
                            '.$img.'
                            </div>
                            <div class="pl-1">
                                <p class="m-0">'.$row->id.'</p>
                                <p class="m-0">'.(($source) ? ($row->source==41) ? '<span class=" badge badge-pill badge-light-primary">'.$source->name.'</span>':$source->name : 'N/A').'</p>
                                <p class="m-0">'.(($admin) ? $admin->firstname.' '.$admin->lastname : '').'</p>
                            </div>
                        </div>
                    </div>
               </div>';
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

    public function view(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/leads",'name'=>"Lead"], ['name'=>"View"]
        ];

        $id=request('id');

        $Lead = Lead::find($id);

        $adminAuth=\Auth::guard('admin')->user();
        if(!$Lead || $Lead->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $LeadNote=DB::select("SELECT 'lead' as type,lead_notes.id, admin_id, property_id,  null as off_plan_project_id, note_subject, lead_id, note, date_at, time_at,lead_notes.status, lead_notes.created_at,firstname,lastname FROM lead_notes,admins WHERE lead_notes.admin_id=admins.id AND lead_id=".$Lead->id." ORDER BY created_at desc");
        //UNION
        //                SELECT 'property' as type,property_note.id, admin_id, property_id, null as off_plan_project_id, note_subject, contact_id, note, date_at, time_at,property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND lead_id=".$Lead->id."

        if(request('reminder')){
            $rLeadNote=LeadNote::find(request('reminder'));
            $rLeadNote->seen=1;
            $rLeadNote->save();
        }

        $offPlanProject='';
        if($Lead->off_plan_project_id){
            $company=Company::find($adminAuth->company_id);

            $url=env('MD_URL').'/api/off-plan-project/detail';

            $data=['id'=>$Lead->off_plan_project_id];
            $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

            $offPlanProject=json_decode($response);
        }

        $recording='';
        if($Lead->portal==2 && $Lead->type=='call_logs') {
            $recording=$Lead->download_url;
        }
        if($Lead->portal==1 && $Lead->type=='call_logs') {

            $token_response = Http::withBody(json_encode(['apiKey'=>env('PF_KEY'),'apiSecret'=>env('PF_SECRET')]),'application/json')->
            post('https://atlas.propertyfinder.com/v1/auth/token');
            $token_response= json_decode($token_response);
            $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/leads?id='.$Lead->pf_lead_id);//createdAtFrom=2025-08-27T08:39:16Z
            $response= json_decode($response);


            foreach ($response->data as $lead) {
                $recording=$lead->call->recordFile;
            }
        }

        $adminAuth = \Auth::guard('admin')->user();

        if ($adminAuth->id == $Lead->assign_to){
            $Lead->seen = 1;
            $Lead->save();
            $Lead = Lead::find($id);
        }

        return view('/admin/lead-view', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'lead' => $Lead,
            'offPlanProject' => $offPlanProject,
            'LeadNote' => $LeadNote,
            'recording' => $recording,
        ]);


    }

    public function leadProperty(){
        $property_id=request('property');
        $property = Property::find( $property_id );
        $owner = Contact::find( $property->contact_id );
        $company=Company::find( $property->company_id );

        $pictures=explode(',', $property->pictures);
        $img_src='';
        if($property->pictures)
            $img_src=$pictures[0];

        $mc=0;
        $expected_price=0;
        if($property->expected_price){
            $mc= $property->bua==0 ? 0 : ($property->expected_price/$property->bua) ;
        }

        if($property->listing_type_id==2){
            if($property->yearly){
                $expected_price=$property->yearly;
            }else if($property->monthly){
                $expected_price=$property->monthly;
            }else if($property->weekly){
                $expected_price=$property->weekly;
            }else{
                $expected_price=$property->daily;
            }
        }

        $MasterProject=MasterProject::where('id',$property->master_project_id)->first();
        $Community=Community::find($property->community_id);
        $ClusterStreet=ClusterStreet::find($property->cluster_street_id);
        $VillaType=VillaType::where('id',$property->villa_type_id)->first();

        echo '<img class="mr-2 rounded" width="70" height="70" src="/storage/'.$img_src.'">
            <div class="text-xl">
                <small>
                    <P class="mb-0">Reference: '.$company->sample.'-'.(($property->listing_type_id==1) ? "S" : "R").'-'.$property->ref_num.'</P>
                    <P class="mb-0">
                    '.(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' '.$Community->name : ''). (  (($ClusterStreet) ? ' '.$ClusterStreet->name : '').(($property->villa_number) ? ' '.'No '.$property->villa_number : '') ).' | AED '.number_format($expected_price).'
                    </P>
                    <hr class="my-0" style="border: 1px solid gray;">
                    <P class="mb-0">'.$owner->firstname.' '.$owner->lastname.'</P>
                    <P class="mb-0">'.$company->sample.'-'.$owner->id.'</P>
                    <P class="mb-0">'.ucfirst($owner->contact_category).'</P>
                    <P class="mb-0">'.$owner->main_number.'</P>
                </small>
            </div>
        ';

    }

    public function action(Request $request){
        if ( request('lead') ){
            foreach (request('lead') as $id) {

                $Lead = Lead::find($id);

                $adminAuth = \Auth::guard('admin')->user();

                if($Lead->company_id==$adminAuth->company_id) {
                    $assign_to = request('AssignTo');

                    if ($assign_to) {
                        $today = date('Y-m-d H:i:s');

                        $Lead->admin_id = $adminAuth->id;
                        $Lead->assign_to = $assign_to;
                        $Lead->status = '0';
                        $Lead->assign_time = $today;
                        $Lead->open_date = $today;
                        $Lead->seen = 0;
                        $Lead->private = 0;
                    }

                    $Lead->save();
                }

            }
        }
        //return redirect('/admin/leads');
    }

    public function assign(Request $request){
        if ( request('_id') ){

            $id=request('_id');
            $Lead = Lead::find($id);

            $adminAuth = \Auth::guard('admin')->user();
            if(!$Lead || $Lead->company_id!=$adminAuth->company_id){
                return abort(404);
            }

            $assign_to = request('assign_to');

            if ($assign_to){
                $Lead->admin_id = $adminAuth->id;
                $Lead->assign_to = $assign_to;
                $Lead->status = '0';
                $Lead->assign_time = date('Y-m-d H:i:s');
                $Lead->open_date = date('Y-m-d H:i:s');
                $Lead->seen = 0;
                $Lead->private = 0;
            }

            $Lead->save();

        }
        return redirect('/admin/leads');
    }

    public function insertLeadsFB(){
        $now=strtotime( date('Y-m-d H:i:s').' - 10 minute' );
        $date_time=date('Y-m-d H:i:s',$now);
        $type='leads';
        $token='EAAEZC1fQ1QgUBO3QQEea7ZBuOtOTg1en3ImdrZAHzZB0aGjNdoWNN2d7aPT37MLzquEn0iu3pZBhYXZAZB9Di6kAnelnZBiDNnkLJf1R7LZCUl8unVCI8J7ZCa1ZAt46nbpCY47RLazckPRQNwCCZCSGRj3ROoHlnHNybzYYhbcvCEmlcKexRkjr89kmr2Ox92VZAXsvcVBjXZAHfVXTGrFrMyYr6AYPHvAXGLsFOhiwZDZD';
        $url='https://graph.facebook.com/v20.0/1979817849106174/leads?access_token='.$token.'&fields=created_time,id,ad_id,form_id,field_data';

        $assign_time=date('Y-m-d H:i:s');

        $response = Http::get($url);

        $response=json_decode($response);

        $fb_leads=$response->data;
        foreach ($fb_leads as $row){
            $pf_lead_check=Lead::where('pf_lead_id',$row->id)->first();
            if(!$pf_lead_check){
                $created_at=explode('+',$row->created_time);
                $created_at=str_replace('T',' ',$created_at[0]);
                $name='';
                $mobile='';
                $email='';
                foreach ($row->field_data as $fd){
                    if($fd->name=='phone_number'){
                        $mobile=$fd->values[0];
                    }
                    if($fd->name=='full_name'){
                        $name=$fd->values[0];
                    }
                    if($fd->name=='email'){
                        $email=$fd->values[0];
                    }
                }

                Lead::create([
                    'portal'=>5,//Property Finder
                    'source'=>21,//Property Finder
                    'type'=>'leads',
                    'name'=>$name,
                    'mobile_number'=>$mobile,
                    'email'=>$email,
                    'pf_lead_id'=>$row->id,
                    'created_at'=>$created_at
                ]);
            }
        }
    }
    public function insertLeadsPF(){
        $assign_time = date('Y-m-d H:i:s');
        date_default_timezone_set("UTC");
        $companys=DB::select('select * from company WHERE pf_integrate=1 AND pf_key IS NOT NULL');
        foreach($companys as $company) {
            $token_response = Http::withBody(json_encode(['apiKey' => $company->pf_key, 'apiSecret' => $company->pf_secret]), 'application/json')->
            post('https://atlas.propertyfinder.com/v1/auth/token');
            $token_response = json_decode($token_response);
            $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/leads?createdAtFrom=' . date('Y-m-d') . 'T00:00:00Z');
            $response = json_decode($response);

            $pf_lead_type = ['email' => 'leads', 'whatsapp' => 'whatsapp_leads', 'call' => 'call_logs'];
            if ($response) {
                $pf_leads = $response->data;
                foreach ($pf_leads as $row) {
                    $created_at = $row->createdAt;
                    $created_at = str_replace('Z', '', $created_at);
                    $created_at = str_replace('T', ' ', $created_at);
                    $created_at = date('Y-m-d H:i:s', strtotime($created_at . "+ 4 hours"));

                    $pf_lead_check = Lead::where('pf_lead_id', $row->id)->first();
                    if (!$pf_lead_check) {

                        $property = null;
                        if (isset($row->listing))
                            $property = Property::where('pf_id', $row->listing->id)->first();


                        $call_total_duration = null;
                        $call_connected_duration = null;
                        if (isset($row->call)) {
                            $call_total_duration = gmdate("H:i:s", $row->call->waitTime);
                            $call_connected_duration = gmdate("H:i:s", $row->call->talkTime);
                        }

                        $mobile_number = null;
                        $email = null;
                        if (isset($row->sender->contacts)) {
                            foreach ($row->sender->contacts as $contact) {
                                if ($contact->type == 'email')
                                    $email = $contact->value;

                                if ($contact->type == 'phone')
                                    $mobile_number = $contact->phone = str_replace('-', '', filter_var($contact->value, FILTER_SANITIZE_NUMBER_INT));
                            }
                        }

                        $admin = null;
                        if (isset($row->publicProfile->id)) {
                            $admin = Admin::where('pf_user_id', $row->publicProfile->id)->first();
                        }

                        Lead::create([
                            'company_id' => $company->id,
                            'property_id' => ($property) ? $property->id : null,
                            'emirate_id' => ($property) ? $property->emirate_id : null,
                            'master_project_id' => ($property) ? $property->master_project_id : null,
                            'community_id' => ($property) ? $property->community_id : null,
                            'portal' => 1,//Property Finder
                            'source' => 32,//Property Finder
                            'type' => $pf_lead_type[$row->channel],
                            'name' => (isset($row->sender->name)) ? $row->sender->name : null,
                            'mobile_number' => $mobile_number,
                            'email' => $email,
                            'pf_lead_id' => $row->id,

                            'call_status' => $row->status,
                            'call_total_duration' => $call_total_duration,
                            'call_connected_duration' => $call_connected_duration,

                            'created_at' => $created_at,
                            'assign_to' => ($admin) ? $admin->id : null,
                            'assign_time' => $assign_time,
                        ]);
                    }
                }
            }
        }

    }

    public function insertLeadsBayut(){
        $now = strtotime(date('Y-m-d H:i:s') . ' - 10 minute');
        $date_time = date('Y-m-d H:i:s', $now);
        $companys=DB::select('select * from company WHERE bayut_integrate=1 AND bayut_key IS NOT NULL');
        foreach($companys as $company) {
            $type = 'leads';
            $url = 'https://www.bayut.com/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $token = $company->bayut_key;//env('BAYUT_TOKEN');

            $assign_time = date('Y-m-d H:i:s');

            $response = Http::withToken($token)->get($url);

            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {
                    $p_ref = explode('-', $row->property_reference);
                    //$property = Property::find(end($p_ref));
                    $property = Property::where('company_id',$company->id)->where('ref_num',end($p_ref))->first();
                    Lead::create([
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'emirate_id' => ($property) ? $property->emirate_id : null,
                        'master_project_id' => ($property) ? $property->master_project_id : null,
                        'community_id' => ($property) ? $property->community_id : null,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'name' => $row->client_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->client_phone, FILTER_SANITIZE_NUMBER_INT)),
                        'email' => $row->client_email,
                        'message' => $row->message,
                        'created_at' => $row->date_time,
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ]);

                }
            }

            $type = 'call_logs';
            $url = 'https://www.bayut.com/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $response = Http::withToken($token)->get($url);

            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {

                    $receiver_numbers = explode(',', $row->receiver_number);
                    $admin = '';
                    foreach ($receiver_numbers as $number) {
                        $admin = Admin::Where('main_number', 'like', '%' . $number . '%')->orWhere('mobile_number', 'like', '%' . $number . '%')->first();
                        if ($admin) {
                            break;
                        }
                    }

                    Lead::create([
                        'company_id' => $company->id,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'mobile_number' => str_replace('-', '', filter_var($row->caller_number, FILTER_SANITIZE_NUMBER_INT)),
                        'receiver_number' => $row->receiver_number,
                        'call_status' => $row->call_status,
                        'call_total_duration' => $row->call_total_duration,
                        'call_connected_duration' => $row->call_connected_duration,
                        'download_url' => $row->call_recordingurl,
                        'caller_location' => $row->caller_location,
                        'created_at' => $row->date . ' ' . $row->time,
                        'assign_to' => ($admin) ? $admin->id : null,
                        'assign_time' => $assign_time,
                    ]);
                }
            }

            $type = 'whatsapp_leads';
            $url = 'https://www.bayut.com/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $response = Http::withToken($token)->get($url);

            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {
                    $p_ref = explode('-', $row->listing_reference);
                    //$property = Property::find(end($p_ref));
                    $property = Property::where('company_id',$company->id)->where('ref_num',end($p_ref))->first();
                    Lead::create([
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'emirate_id' => ($property) ? $property->emirate_id : null,
                        'master_project_id' => ($property) ? $property->master_project_id : null,
                        'community_id' => ($property) ? $property->community_id : null,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'name' => $row->detail->actor_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->detail->cell, FILTER_SANITIZE_NUMBER_INT)),
                        'message' => $row->detail->message,
                        'created_at' => $row->date_time,
                        'delivery_notifications' => json_encode($row->delivery_notifications),
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ]);

                }
            }

        }

    }

    public function insertLeadsDubizzle(){
        $now = strtotime(date('Y-m-d H:i:s') . ' - 10 minute');
        $date_time = date('Y-m-d H:i:s', $now);
        $companys=DB::select('select * from company WHERE bayut_integrate=1 AND bayut_key IS NOT NULL');
        foreach($companys as $company) {
            $token = $company->bayut_key;

            $assign_time = date('Y-m-d H:i:s');

            $date_time = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 1 days'));
            $type = 'leads';
            $url = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $response = Http::withToken($token)->get($url);
            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {
                    $pf_lead_check = Lead::where('pf_lead_id', $row->lead_id)->first();
                    if (!$pf_lead_check) {
                        $p_ref = explode('-', $row->property_reference);
                        $property = Property::where('company_id', $company->id)->where('ref_num', end($p_ref))->first();

                        Lead::create([
                            'company_id' => $company->id,
                            'property_id' => ($property) ? $property->id : null,
                            'emirate_id' => ($property) ? $property->emirate_id : null,
                            'master_project_id' => ($property) ? $property->master_project_id : null,
                            'community_id' => ($property) ? $property->community_id : null,
                            'portal' => 3,//Dubizzle
                            'source' => 34,//Dubizzle
                            'pf_lead_id' => $row->lead_id,
                            'type' => $type,
                            'name' => $row->client_name,
                            'mobile_number' => str_replace('-', '', filter_var($row->client_phone, FILTER_SANITIZE_NUMBER_INT)),
                            'email' => $row->client_email,
                            'message' => $row->message,
                            'created_at' => $row->date_time,
                            'assign_to' => ($property) ? $property->client_manager_id : null,
                            'assign_time' => $assign_time,
                        ]);
                    }

                }
            }

            $type = 'call_logs';
            $url = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $response = Http::withToken($token)->get($url);
            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {
                    $pf_lead_check = Lead::where('pf_lead_id', $row->lead_id)->first();
                    if (!$pf_lead_check) {
                        $p_ref = explode('-', $row->listing_reference);
                        $property = Property::where('company_id', $company->id)->where('ref_num', end($p_ref))->first();

                        $receiver_numbers = explode(',', $row->receiver_number);
                        $admin = '';
                        foreach ($receiver_numbers as $number) {
                            $admin = Admin::Where('main_number', 'like', '%' . $number . '%')->orWhere('mobile_number', 'like', '%' . $number . '%')->first();
                            if ($admin) {
                                break;
                            }
                        }

                        Lead::create([
                            'company_id' => $company->id,
                            'property_id' => ($property) ? $property->id : null,
                            'emirate_id' => ($property) ? $property->emirate_id : null,
                            'master_project_id' => ($property) ? $property->master_project_id : null,
                            'community_id' => ($property) ? $property->community_id : null,
                            'portal' => 3,//Dubizzle
                            'source' => 34,//Dubizzle
                            'pf_lead_id' => $row->lead_id,
                            'type' => $type,
                            'mobile_number' => str_replace('-', '', filter_var($row->caller_number, FILTER_SANITIZE_NUMBER_INT)),
                            'receiver_number' => $row->receiver_number,
                            'call_status' => $row->call_status,
                            'call_total_duration' => $row->call_total_duration,
                            'call_connected_duration' => $row->call_connected_duration,
                            'download_url' => $row->call_recordingurl,
                            'caller_location' => $row->caller_location,
                            'created_at' => $row->date . ' ' . $row->time,
                            'assign_to' => ($admin) ? $admin->id : null,
                            'assign_time' => $assign_time,
                        ]);
                    }
                }
            }

            $type = 'whatsapp_leads';
            $url = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
            $response = Http::withToken($token)->get($url);
            $response = json_decode($response);
            if ($response) {
                foreach ($response as $row) {
                    $pf_lead_check = Lead::where('pf_lead_id', $row->lead_id)->first();
                    if (!$pf_lead_check) {
                        $p_ref = explode('-', $row->listing_reference);
                        $property = Property::where('company_id', $company->id)->where('ref_num', end($p_ref))->first();

                        Lead::create([
                            'company_id' => $company->id,
                            'property_id' => ($property) ? $property->id : null,
                            'emirate_id' => ($property) ? $property->emirate_id : null,
                            'master_project_id' => ($property) ? $property->master_project_id : null,
                            'community_id' => ($property) ? $property->community_id : null,
                            'portal' => 3,//Dubizzle
                            'source' => 34,//Dubizzle
                            'pf_lead_id' => $row->lead_id,
                            'type' => $type,
                            'name' => $row->detail->actor_name,
                            'mobile_number' => str_replace('-', '', filter_var($row->detail->cell, FILTER_SANITIZE_NUMBER_INT)),
                            'message' => $row->detail->message,
                            'created_at' => $row->date_time,
                            'delivery_notifications' => json_encode($row->delivery_notifications),
                            'assign_to' => ($property) ? $property->client_manager_id : null,
                            'assign_time' => $assign_time,
                        ]);
                    }
                }
            }

        }
    }

    public function pfLead(){
        $adminAuth = \Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $assign_time=date('Y-m-d H:i:s');
        date_default_timezone_set("UTC");
        $token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);
        $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/leads?page=1&createdAtFrom=2025-10-01T00:00:00Z&createdAtTo=2025-10-30T23:59:59Z');//?createdAtFrom='.date('Y-m-d').'T00:00:00Z'
        //$response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/leads');//?createdAtFrom='.date('Y-m-d').'T00:00:00Z'
        return$response= json_decode($response);

        $pf_lead_type=['email'=>'leads','whatsapp'=>'whatsapp_leads','call'=>'call_logs'];

        $data=[];
        if($response){
            $pf_leads=$response->data;
            foreach ($pf_leads as $row) {
                $created_at = $row->createdAt;
                $created_at = str_replace('Z', '', $created_at);
                $created_at = str_replace('T', ' ', $created_at);
                $created_at=date('Y-m-d H:i:s',strtotime($created_at. "+ 4 hours") );

                $pf_lead_check = Lead::where('pf_lead_id', $row->id)->first();
                if (!$pf_lead_check) {

                    $property=null;
                    if(isset($row->listing))
                        $property = Property::where('pf_id',$row->listing->id)->first();


                    $call_total_duration=null;
                    $call_connected_duration=null;
                    if(isset($row->call)) {
                        $call_total_duration=gmdate("H:i:s", $row->call->waitTime);
                        $call_connected_duration=gmdate("H:i:s", $row->call->talkTime);
                    }

                    $mobile_number=null;
                    $email=null;
                    if(isset($row->sender->contacts)){
                        foreach ($row->sender->contacts as $contact) {
                            if($contact->type=='email')
                                $email=$contact->value;

                            if($contact->type=='phone')
                                $mobile_number=$contact->phone = str_replace('-', '', filter_var($contact->value, FILTER_SANITIZE_NUMBER_INT));
                        }
                    }

                    $admin=null;
                    if(isset($row->publicProfile->id)) {
                        $admin = Admin::where('pf_user_id', $row->publicProfile->id)->first();
                    }

                    Lead::create([
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 1,//Property Finder
                        'source' => 32,//Property Finder
                        'type' => $pf_lead_type[$row->channel],
                        'name' => (isset($row->sender->name)) ? $row->sender->name : null,
                        'mobile_number' => $mobile_number,
                        'email' => $email,
                        'pf_lead_id' => $row->id,

                        'call_status'=>$row->status,
                        'call_total_duration'=>$call_total_duration,
                        'call_connected_duration'=>$call_connected_duration,

                        'created_at' => $created_at,
                        'assign_to' => ($admin) ? $admin->id : null,
                        'assign_time' => $assign_time,
                    ]);
                    /*$data[]=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 1,//Property Finder
                        'source' => 32,//Property Finder
                        'type' => $pf_lead_type[$row->channel],
                        'name' => (isset($row->sender->name)) ? $row->sender->name : null,
                        'mobile_number' => $mobile_number,
                        'email' => $email,
                        'pf_lead_id' => $row->id,

                        'call_status'=>$row->status,
                        'call_total_duration'=>$call_total_duration,
                        'call_connected_duration'=>$call_connected_duration,

                        'created_at' => $created_at,
                        'assign_to' => ($admin) ? $admin->id : null,
                        'assign_time' => $assign_time,
                    ];*/
                }
            }
        }
        //return $data;

    }
    public function bayutLead(){
        $adminAuth = \Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $now = strtotime(date('Y-m-d H:i:s') . ' - 10 minute');
        $date_time = '2026-02-01 00:00:00';date('Y-m-d H:i:s', $now);

        //$type = 'leads';
        //$type = 'call_logs';
        //$type = 'whatsapp_leads';

        $type = request('type');
        $url = 'https://www.bayut.com/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
        $token = $company->bayut_key;//env('BAYUT_TOKEN');

        $assign_time = date('Y-m-d H:i:s');

        return $response = Http::withToken($token)->get($url);

        $datas=[];
        $response = json_decode($response);
        if ($response) {
            if ($type == 'leads') {

                foreach ($response as $row) {
                    $p_ref = explode('-', $row->property_reference);
                    //$property = Property::find(end($p_ref));
                    $property = Property::where('company_id', $company->id)->where('ref_num', end($p_ref))->first();
                    $data=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'name' => $row->client_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->client_phone, FILTER_SANITIZE_NUMBER_INT)),
                        'email' => $row->client_email,
                        'message' => $row->message,
                        'created_at' => $row->date_time,
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);

                }

            }

            if ($type == 'call_logs') {
                foreach ($response as $row) {

                    $receiver_numbers = explode(',', $row->receiver_number);
                    $admin = '';
                    foreach ($receiver_numbers as $number) {
                        $admin = Admin::Where('main_number', 'like', '%' . $number . '%')->orWhere('mobile_number', 'like', '%' . $number . '%')->first();
                        if ($admin) {
                            break;
                        }
                    }
                    $data=[
                        'company_id' => $company->id,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'mobile_number' => str_replace('-', '', filter_var($row->caller_number, FILTER_SANITIZE_NUMBER_INT)),
                        'receiver_number' => $row->receiver_number,
                        'call_status' => $row->call_status,
                        'call_total_duration' => $row->call_total_duration,
                        'call_connected_duration' => $row->call_connected_duration,
                        'download_url' => $row->call_recordingurl,
                        'caller_location' => $row->caller_location,
                        'created_at' => $row->date . ' ' . $row->time,
                        'assign_to' => ($admin) ? $admin->id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);
                }
            }

            if($type=='whatsapp_leads'){
                foreach ($response as $row) {
                    $p_ref = explode('-', $row->listing_reference);
                    //$property = Property::find(end($p_ref));
                    $property = Property::where('company_id',$company->id)->where('ref_num',end($p_ref))->first();
                    $data=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 2,//Bayut
                        'source' => 33,//Bayut
                        'type' => $type,
                        'name' => $row->detail->actor_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->detail->cell, FILTER_SANITIZE_NUMBER_INT)),
                        'message' => $row->detail->message,
                        'created_at' => $row->date_time,
                        'delivery_notifications' => json_encode($row->delivery_notifications),
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);

                }
            }

        }
        return $datas;

    }

    public function insertDubizzleLeads(Request $request){
        /*$date_time=date('Y-m-d').' 00:00:00';//date()
        $type='whatsapp_leads';

        $assign_time=date('Y-m-d H:i:s');

        $p_ref=explode('-',$request->listing['reference']);
        $property=Property::find( end($p_ref) );
        Lead::create([
            'property_id'=>($property) ? end($p_ref) : null,
            'portal'=>3,//Dubizzle
            'source'=>34,//Dubizzle
            'type'=>$type,
            'name'=>$request->enquirer['name'],
            'mobile_number'=>$request->enquirer['phone_number'],
            'created_at'=>$request->received_at,
            'assign_to'=>($property) ? $property->client_manager_id : null,
            'assign_time'=>$assign_time,
        ]);*/

        $adminAuth = \Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $now = strtotime(date('Y-m-d H:i:s') . ' - 10 minute');
        $date_time = '2026-01-25 00:00:00';date('Y-m-d H:i:s', $now);

        //$type = 'leads';
        //$type = 'call_logs';
        //$type = 'whatsapp_leads';

        $type = request('type');
        $url = 'https://dubizzle.com/profolio/api-v7/stats/website-client-leads?type=' . $type . '&timestamp=' . $date_time;
        $token = $company->bayut_key;//env('BAYUT_TOKEN');

        $assign_time = date('Y-m-d H:i:s');

        return $response = Http::withToken($token)->get($url);

        $datas=[];
        $response = json_decode($response);
        if ($response) {
            if ($type == 'leads') {

                foreach ($response as $row) {
                    $p_ref = explode('-', $row->property_reference);
                    //$property = Property::find(end($p_ref));
                    $property = Property::where('company_id', $company->id)->where('ref_num', end($p_ref))->first();
                    $data=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 3,//Dubizzle
                        'source' => 34,//Dubizzle
                        'type' => $type,
                        'name' => $row->client_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->client_phone, FILTER_SANITIZE_NUMBER_INT)),
                        'email' => $row->client_email,
                        'message' => $row->message,
                        'created_at' => $row->date_time,
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);

                }

            }

            if ($type == 'call_logs') {
                foreach ($response as $row) {
                    $p_ref = explode('-', $row->listing_reference);
                    $property = Property::where('company_id',$company->id)->where('ref_num',end($p_ref))->first();

                    $receiver_numbers = explode(',', $row->receiver_number);
                    $admin = '';
                    foreach ($receiver_numbers as $number) {
                        $admin = Admin::Where('main_number', 'like', '%' . $number . '%')->orWhere('mobile_number', 'like', '%' . $number . '%')->first();
                        if ($admin) {
                            break;
                        }
                    }
                    $data=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 3,//Dubizzle
                        'source' => 34,//Dubizzle
                        'type' => $type,
                        'mobile_number' => str_replace('-', '', filter_var($row->caller_number, FILTER_SANITIZE_NUMBER_INT)),
                        'receiver_number' => $row->receiver_number,
                        'call_status' => $row->call_status,
                        'call_total_duration' => $row->call_total_duration,
                        'call_connected_duration' => $row->call_connected_duration,
                        'download_url' => $row->call_recordingurl,
                        'caller_location' => $row->caller_location,
                        'created_at' => $row->date . ' ' . $row->time,
                        'assign_to' => ($admin) ? $admin->id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);
                }
            }

            if($type=='whatsapp_leads'){
                foreach ($response as $row) {
                    $p_ref = explode('-', $row->listing_reference);
                    $property = Property::where('company_id',$company->id)->where('ref_num',end($p_ref))->first();
                    $data=[
                        'company_id' => $company->id,
                        'property_id' => ($property) ? $property->id : null,
                        'portal' => 3,//Dubizzle
                        'source' => 34,//Dubizzle
                        'type' => $type,
                        'name' => $row->detail->actor_name,
                        'mobile_number' => str_replace('-', '', filter_var($row->detail->cell, FILTER_SANITIZE_NUMBER_INT)),
                        'message' => $row->detail->message,
                        'created_at' => $row->date_time,
                        'delivery_notifications' => json_encode($row->delivery_notifications),
                        'assign_to' => ($property) ? $property->client_manager_id : null,
                        'assign_time' => $assign_time,
                    ];
                    $datas[]=$data;
                    //Lead::create($data);

                }
            }

        }
        return $datas;
    }

    public function Delete(){
        $id=request('Delete');
        $Contact=Contact::where('lead_id',$id)->first();
        if($Contact) {
            return ['r'=>'0','msg'=>'The lead has been added to the contact'];
        }

        $Lead = Lead::find( $id );

        $LeadNote=LeadNote::where('lead_id', $Lead->id)->count();

        if(request('activities')!='delete') {
            if ($LeadNote > 0) {
                return ['r' => '-1',
                    'msg' => 'By deleting this lead, all activities related to this lead will be deleted.'];
            }
        }

        $adminAuth=\Auth::guard('admin')->user();
        if($Lead->company_id==$adminAuth->company_id) {
            Survey::where('model', 'Lead_Appointment')->where('model_id', $Lead->id)->delete();
            Survey::where('model', 'Lead_Viewing')->where('model_id', $Lead->id)->delete();
            LeadNote::where('lead_id', $Lead->id)->delete();
            $Lead->delete();

            return ['r' => '1', 'msg' => ''];
        }
    }

    public function apiStore(Request $request){
        $api_key= $request->bearerToken();
        if(!$api_key)
            return response(["message"=>"Unauthorized"],'401');

        $Company=Company::where('api_key',$api_key)->first();

        if($Company) {
            $request->validate([
                //'in_crm_ids' => 'required',
                'access' => 'required'
            ]);

            $lead=Lead::create([
                'company_id'=>$Company->id,
                'type'=>'website',
                'name'=>request('name'),
                'mobile_number'=>request('mobile_number'),
                'email'=>request('email'),
                'message'=>request('message'),
                'source'=>44,
            ]);

            return response(["message"=>"done"],'200');
        }
    }

}
