<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\AdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminRequestController extends Controller
{
    public function requests(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        if(request('id')){
            $AdminRequest = AdminRequest::find( request('id') );
            $adminAuth=\Auth::guard('admin')->user();
            if($AdminRequest->admin_id==$adminAuth->id) {
                $AdminRequest->result_seen = 1;
                $AdminRequest->approve_cancel_seen = 2;
                $AdminRequest->save();
            }
        }
        return view('/admin/admin-requests', [
            'pageConfigs' => $pageConfigs,
        ]);
    }
    public function requests_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        if(request('id')){
            $AdminRequest = AdminRequest::find( request('id') );
            $adminAuth=\Auth::guard('admin')->user();
            if($AdminRequest->admin_id==$adminAuth->id) {
                $AdminRequest->result_seen = 1;
                $AdminRequest->approve_cancel_seen = 2;
                $AdminRequest->save();
            }
        }
        return view('/admin/admin-requests-sm', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function GetRequest(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $data = array();

        $adminAuth=\Auth::guard('admin')->user();
        $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();

        $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();
        $approver_access=0;
        if($request_main_admin_id){
            if(in_array($adminAuth->id, $request_main_admin_id))
                $approver_access=1;
        }else{
            if($adminAuth->super==1)
                $approver_access=1;
        }

        $where=' WHERE 1 ';

        $orderBy=' created_at DESC';

        if($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)){
            $orderBy=' datetime_from DESC';
        }

        if($columnIndex){
            $orderBy=$columnName." ".$columnSortOrder;
        }

        if(request('profile'))
            $totalRecords = AdminRequest::where('admin_id',request('profile'))->where('type',1)->count();
        else
            $totalRecords = AdminRequest::where('type',1)->count();

        $where.=' AND admin_requests.type=1';

        if( request('admin') )
            $where.=' AND admin_id='.request('admin');

        if( request('request') )
            $where.=' AND admin_requests.request_id ='.request('request');

        if( request('controller') )
            $where.=' AND admin_requests.hr_admin ='.request('controller');

        if( request('manager') )
            $where.=' AND admin_requests.manager_admin ='.request('manager');

        if( request('controller_status') || request('controller_status')=='0' ) {
            $where .= ' AND admin_requests.hr_status =' . request('controller_status');
            if(request('controller_status')=='0')
                $where.=' AND admin_requests.manager_status =0';
        }

        if( request('manager_status') || request('manager_status')=='0')
            $where.=' AND admin_requests.manager_status ='.request('manager_status');

        if( request('profile') )
            $where.=' AND admin_requests.admin_id ='.request('profile');

        if( request('from_date') )
            $where.=' AND admin_requests.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND admin_requests.created_at <="'.request('to_date').' 23:59:59"';

        #record number with filter
        $statusOrder='';
        if($approver_access==1 && !in_array($adminAuth->id, $request_approver_admin_id)){
            $statusOrder='admin_requests.manager_status ASC,';
        }

        if($rowperpage=='-1'){
            $query="SELECT * FROM admin_requests ".$where."  ORDER BY  ".$statusOrder.$orderBy;
            $Records=DB::select($query);
        }else{
            $query="SELECT * FROM admin_requests ".$where." ORDER BY  ".$statusOrder." admin_requests.".$orderBy." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
        }

        $totalRecordwithFilter=count(DB::select("SELECT * FROM admin_requests ".$where) );

        $obj=[];
        foreach($Records as $row){
            $admin=Admin::find($row->admin_id);
            $manager_admin=Admin::find($row->manager_admin);
            $hr_admin=Admin::find($row->hr_admin);
            $Request_=\App\Models\Request::find($row->request_id);

            $hr_status='';
            if( ($row->hr_status!=0 && $row->manager_status==0) &&
                ($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id))) {
                if ($row->hr_status == 1) {
                    $hr_status = '<span class="badge badge-pill badge-light-primary">Accepted</span>';
                }
                if ($row->hr_status == 2) {
                    $hr_status = '<span class="badge badge-pill badge-light-warning">Rejected</span>';
                }
            }

            $manager_status = '';
            if(request('controller_status')=='') {
                if ($row->manager_status == 1) {
                    $manager_status = '<span class="badge badge-pill badge-light-success">Accepted</span>';
                }
                if ($row->manager_status == 2) {
                    $manager_status = '<span class="badge badge-pill badge-light-danger">Rejected</span>';
                }
                if ($row->manager_status == 3) {
                    $manager_status = '<span class="badge badge-pill badge-light-secondary">Cancelled</span>';
                }
            }

            if($manager_status!='' && (!in_array($adminAuth->id, $request_approver_admin_id) || $approver_access!=1))
                $hr_status='';


            $obj['request_id']= $Request_->title;
            $obj['admin_id']= ($admin) ? $admin->firstname.' '.$admin->lastname : '';
            $obj['datetime_from']= ($row->datetime_from) ? date('d-m-Y',strtotime($row->datetime_from)) : 'N/A';
            $obj['datetime_to']= ($row->datetime_to) ? date('d-m-Y',strtotime($row->datetime_to)) : 'N/A';
            $obj['resumption_date']= ($row->resumption_date) ? date('d-m-Y',strtotime($row->resumption_date)) : 'N/A';
            $obj['number_days']= ($row->number_days)?:'N/A';
            $obj['manager_status']= $hr_status.$manager_status;
            $obj['manager_admin']= ($manager_admin && request('controller_status')=='') ? $manager_admin->firstname.' '.$manager_admin->lastname : ( ($hr_admin)? $hr_admin->firstname.' '.$hr_admin->lastname : '' );

            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);
            $delete='';
            $cancel='';
            $cancel_accept_reject='';
            if($row->manager_status != 2 && $row->datetime_from>date('Y-m-d') && $row->admin_id==$adminAuth->id && $row->cancel_request==0 && $row->manager_status == 1){
                $cancel='<a href="javascript:void(0);" class="lr-cancel"><span class="btn" style="background: #6c6b6b;color: #fff">Request</span></a>';
            }

            if($approver_access==1 && $row->cancel_request==1){
                $cancel_accept_reject= '<a href="javascript:void(0);" data-status="1" class="cancel-action"><span class="btn" style="background: #6c6b6b;color: #fff">Approve</span></a>';
            }

            if($row->admin_id==$adminAuth->id && $row->hr_status == 0 && $row->manager_status == 0){
                $delete= '<a href="javascript:void(0)" class="lr-delete"><span class="btn btn-danger">Delete</span></a>';
            }

            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'"  data-model="'.route('request.cancel-request').'"  data-model-cancel-action="'.route('request.cancel-request-action').'">
                                  '.$cancel.$cancel_accept_reject.$delete.'
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

    public function GetRequest_sm(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = 3;//request('length'); // Rows display per page

        $adminAuth=\Auth::guard('admin')->user();
        $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();

        $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();
        $approver_access=0;
        if($request_main_admin_id){
            if(in_array($adminAuth->id, $request_main_admin_id))
                $approver_access=1;
        }else{
            if($adminAuth->super==1)
                $approver_access=1;
        }

        $adminAuth=\Auth::guard('admin')->user();
        $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();

        $where=' WHERE 1 ';

        $orderBy=' created_at DESC';

        if($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)){
            $orderBy=' datetime_from DESC';
        }

        if(request('profile'))
            $totalRecords = AdminRequest::where('admin_id',request('profile'))->where('type',1)->count();
        else
            $totalRecords = AdminRequest::where('type',1)->count();

        $where.=' AND admin_requests.type=1';

        if( request('admin') )
            $where.=' AND admin_id='.request('admin');

        if( request('request') )
            $where.=' AND admin_requests.request_id ='.request('request');

        if( request('controller') )
            $where.=' AND admin_requests.hr_admin ='.request('controller');

        if( request('manager') )
            $where.=' AND admin_requests.manager_admin ='.request('manager');

        if( request('controller_status') || request('controller_status')=='0' ) {
            $where .= ' AND admin_requests.hr_status =' . request('controller_status');
            if(request('controller_status')=='0')
                $where.=' AND admin_requests.manager_status =0';
        }

        if( request('manager_status') || request('manager_status')=='0')
            $where.=' AND admin_requests.manager_status ='.request('manager_status');

        if( request('profile') )
            $where.=' AND admin_requests.admin_id ='.request('profile');

        if( request('from_date') )
            $where.=' AND admin_requests.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND admin_requests.created_at <="'.request('to_date').' 23:59:59"';

        #record number with filter
        $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();
        $statusOrder='';
        if($approver_access==1 && !in_array($adminAuth->id, $request_approver_admin_id)){
            $statusOrder='admin_requests.manager_status ASC,';
        }

        if($rowperpage=='-1'){
            $query="SELECT * FROM admin_requests ".$where."  ORDER BY  ".$statusOrder.$orderBy;
            $Records=DB::select($query);
        }else{
            $query="SELECT * FROM admin_requests ".$where." ORDER BY  ".$statusOrder." admin_requests.".$orderBy." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
        }

        //$totalRecordwithFilter=0;
        $totalRecordwithFilter=count(DB::select("SELECT * FROM admin_requests ".$where) );

        $data='';
        foreach($Records as $row){
            $admin=Admin::find($row->admin_id);
            $manager_admin=Admin::find($row->manager_admin);
            $hr_admin=Admin::find($row->hr_admin);
            $Request_=\App\Models\Request::find($row->request_id);

            $hr_status='';
            if( ($row->hr_status!=0 && $row->manager_status==0) ||
                ($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id))) {
                if ($row->hr_status == 1) {
                    $hr_status = '<span class="badge badge-pill badge-light-primary">Accepted</span>';
                }
                if ($row->hr_status == 2) {
                    $hr_status = '<span class="badge badge-pill badge-light-warning">Rejected</span>';
                }
            }

            $manager_status = '';
            if(request('controller_status')=='') {
                if ($row->manager_status == 1) {
                    $manager_status = '<span class="badge badge-pill badge-light-success">Accepted</span>';
                }
                if ($row->manager_status == 2) {
                    $manager_status = '<span class="badge badge-pill badge-light-danger">Rejected</span>';
                }
                if ($row->manager_status == 3) {
                    $manager_status = '<span class="badge badge-pill badge-light-secondary">Cancelled</span>';
                }
            }

            if($manager_status!='' && (!in_array($adminAuth->id, $request_approver_admin_id) || $adminAuth->super!=1))
                $hr_status='';

            $delete='';
            $cancel='';
            $cancel_accept_reject='';
            if($row->manager_status != 2 && $row->datetime_from>date('Y-m-d') && $row->admin_id==$adminAuth->id && $row->cancel_request==0 && $row->manager_status == 1){
                $cancel='<a href="javascript:void(0);" class="lr-cancel float-right"><span class="btn p-1 font-small-2" style="background: #6c6b6b;color: #fff">Request</span></a>';
            }

            if($approver_access==1 && $row->cancel_request==1){
                $cancel_accept_reject= '<a href="javascript:void(0);" data-status="1" class="cancel-action float-right"><span class="btn p-1 font-small-2" style="background: #6c6b6b;color: #fff">Approve</span></a>';
            }

            if($row->admin_id==$adminAuth->id && $row->hr_status == 0 && $row->manager_status == 0){
                $delete= '<a href="javascript:void(0)" class="lr-delete float-right"><span class="btn btn-danger p-1 font-small-2">Delete</span></a>';
            }

            $action='<div class="action" data-id="'.$row->id.'"  data-model="'.route('request.cancel-request').'"  data-model-cancel-action="'.route('request.cancel-request-action').'">
                                  '.$cancel.$cancel_accept_reject.$delete.'
                            </div>';

            $data.='<div class="card mb-2 hold-box" data-id="'.$row->id.'" data-toggle="modal" data-target="#requestDetail">
                    <div class="card-body p-1">
                        <div class="d-flex">
                            <div class="pl-1">
                                <p class="m-0">'.(($admin) ? $admin->firstname.' '.$admin->lastname : '').'</p>
                                <p class="m-0">'.$Request_->title.'</p>
                                <p class="m-0">'.$row->number_days.' day , '.(($row->datetime_from) ? date('d-m-Y',strtotime($row->datetime_from)) : '').(($row->datetime_to) ? ' - '.date('d-m-Y',strtotime($row->datetime_to)) : '').'</p>
                                <p class="m-0">'.$hr_status.$manager_status.'</p>
                            </div>
                        </div>
                        '.$action.'
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

    public function store(Request $request){
        $request->validate([
            'request'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        AdminRequest::create([
            'type'=>1,
            'admin_id'=>$adminAuth->id,
            'request_id'=>request('request'),
            'datetime_from'=>request('datetime_from'),
            'datetime_to'=>request('datetime_to'),
            'resumption_date'=>request('resumption_date'),
            'number_days'=>request('number_days'),
            'description'=>request('description'),
            'document'=>request('Document'),
        ]);
        return redirect()->back();
    }

    public function details(Request $request){
        $request->validate([
            'request'=>'required',
        ]);

        $id=request('request');
        $Arequest=AdminRequest::find($id);
        $Request_=\App\Models\Request::find($Arequest->request_id);
        $admin=Admin::find($Arequest->admin_id);

        $today = date('Y-m-d');
        $newYear=date('Y').'-'.date('m-d',strtotime($admin->date_joined));
        if($newYear>$today){
            $newYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
        }
        $takenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
        $halfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
        $sickTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=5 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
        $carryForwardTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=9 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
        $otherTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id IN (1,2,3,4,6,10) AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
        $adminAuth=\Auth::guard('admin')->user();

        $confirm='';
        $reject='';
        $approver='';
        $reasonInput='';
        $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();
        if($Arequest->admin_id!=$request_approver_admin_id) {
            if (in_array($adminAuth->id, $request_approver_admin_id) && $Arequest->hr_status == 0 && $Arequest->manager_status == 0) {
                $reasonInput = '<div class="col-sm-12 mt-3">
                                <div class="form-group form-label-group">
                                    <label for="hr_reason">Reason</label>
                                    <input type="text" id="hr_reason" name="hr_reason" class="form-control" placeholder="Reason" required>
                                </div>
                            </div>';
                $confirm = '<button type="button" value="1" class="btn-submit btn btn-outline-success mr-1 mb-1 waves-effect waves-light">Accept</button>';
                $reject = '<button  type="button" value="2" class="btn-submit btn btn-outline-danger mr-1 mb-1 waves-effect waves-light">Reject</button>';
                $approver = 'hr';
            }
        }

        $confirmed='';
        if($Arequest->hr_status!=0){
            $hr_admin=Admin::find($Arequest->hr_admin);
            if($Arequest->hr_status==1)
                $confirmed='<div class="text-primary">The request had been approved by '.$hr_admin->firstname.' '.$hr_admin->lastname.' <br> '.$Arequest->hr_reason.'</div>';
            else
                $confirmed='<div class="text-warning">The request has been rejected by '.$hr_admin->firstname.' '.$hr_admin->lastname.' <br> '.$Arequest->hr_reason.'</div>';
        }

        //if ($Arequest->manager_status==0 && $adminAuth->super==1){

        $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();
        $approver_access=0;
        if($request_main_admin_id){
            if(in_array($adminAuth->id, $request_main_admin_id))
                $approver_access=1;
        }else{
            if($adminAuth->super==1)
                $approver_access=1;
        }

        if ($Arequest->manager_status==0 && $approver_access==1){
            $confirm='<button type="button" value="1" class="btn-submit btn btn-outline-success mr-1 mb-1 waves-effect waves-light">Accept</button>';
            $reject='<button type="button" value="2" class="btn-submit btn btn-outline-danger mr-1 mb-1 waves-effect waves-light">Reject</button>';
            $approver='manager';
        }

        $action='';
        if($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)){
            $action=$confirmed.'

            <div class="text-right mt-2">
            '.$reasonInput.'
                <input type="hidden" name="request" value="'.$Arequest->id.'">
                <input type="hidden" id="request-status" name="status">
                <input type="hidden" name="approver" value="'.$approver.'">
                <button id="submit-status" type="submit" class="d-none"></button>
            '.$confirm.$reject;
        }

        $date_two = \Carbon\Carbon::parse($today);
        $years = $date_two->diffInYears($admin->date_joined);
        $carryForwardDays=0;
        if($years>0) {
            //$previousYear=date('Y').'-'.date('m-d',strtotime($admin->date_joined));
            $previousYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
            $beforeYearTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$admin->id);
            $beforeYearHalfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$admin->id);

            $carryForwardDays=$admin->leave_days-(($beforeYearTakenDays[0]->allDays+($beforeYearHalfTakenDays[0]->allDays/2))+$carryForwardTakenDays[0]->allDays);
        }

        $date_two = \Carbon\Carbon::parse($Arequest->datetime_from);
        $month = $date_two->diffInMonths($newYear);

        $leave=($admin->leave_days/12)*$month;
        //$leave=floor($leave);
        $takenDaysAll=$takenDays[0]->allDays+($halfTakenDays[0]->allDays/2);
        $Remaining=$leave-$takenDaysAll;
        $user='<div class="row">
                <div class="col-sm-6"> '.$admin->firstname.' '.$admin->lastname.'</div>
                 <div class="col-sm-6"><b>Date of Join: </b> '.(($admin->date_joined) ? date('d-m-Y',strtotime($admin->date_joined)) : '').' </div>
                 </div>';

        $detail='<div class="row mb-2">
                    <div class="col-sm-6">
                    <p><b>Annual Leave Days: </b> '.$admin->leave_days.' </p>
                    <p><b>Entitled Days: </b> '.$leave.' </p>
                    <p class="text-success"><b>Remaining Days: </b> '.$Remaining.' </p>
                    '.( ($carryForwardDays) ? '<p><b>Carry forward Days: </b> '.$carryForwardDays.' </p>' : '').'
                    </div>
                    <div class="col-sm-6">
                        <p><b>Annual Leave Taken Days:</b> '.$takenDaysAll.' </p>
                        <p><b>Sick Leave Taken Days:</b> '.(($sickTakenDays[0]->allDays)?:0).' </p>
                        <p><b>Other Leave Days:</b> '.(($otherTakenDays[0]->allDays)?:0).' </p>
                    </div>
                </div>';
                $detail.= '<div class="row" style="background-color: #f8f8f8;">
                    <div  class="col-sm-12">
                        <p><b>Request Type: </b> '.$Request_->title.' </p>
                        <p><b>From Date: </b> '.(($Arequest->datetime_from) ? date('d-m-Y',strtotime($Arequest->datetime_from)) : 'N/A').' </p>
                        <p><b>To date: </b> '.(($Arequest->datetime_to) ? date('d-m-Y',strtotime($Arequest->datetime_to)) : 'N/A').' </p>
                        <p><b>Number of Days: </b> '.$Arequest->number_days.'  </p>
                        <p><b>Resume date: </b> '.(($Arequest->resumption_date) ? date('d-m-Y',strtotime($Arequest->resumption_date)) : 'N/A').' </p>
                    </div>
                </div>
                <p class="mt-1"><b>Description: </b> '.$Arequest->description.' </p>
                         '.(($Arequest->document) ? '<p><b>File: </b><a href="/storage/'.$Arequest->document.'" target="_blank"><i class="feather icon-download"></i></a></p>' : '').'
                '.$action.'
                </div>
                ';

        return ['detail'=>$detail,'user'=>$user];
    }

    public function confirm(Request $request){
        $id=request('request');
        $status=request('status');
        $hr_reason=request('hr_reason');
        $approver=request('approver');
        $AdminRequest = AdminRequest::find( $id );

        $adminAuth=\Auth::guard('admin')->user();

        if($approver=='hr') {
            $AdminRequest->hr_status = $status;
            $AdminRequest->hr_reason = $hr_reason;
            $AdminRequest->hr_admin = $adminAuth->id;
        }
        if($approver=='manager') {
            $AdminRequest->manager_status = $status;
            $AdminRequest->manager_admin = $adminAuth->id;

            $admin=Admin::find($AdminRequest->admin_id);
            $Request=\App\Models\Request::find($AdminRequest->request_id);

            $statusText='';
            if($status==1){
                $statusText='approved';
            }
            if($status==2){
                $statusText='rejected';
            }
            $details = [
                'subject' => 'Dont Reply (Request)',
                'body' => 'Dear '.$admin->firstname.' '.$admin->lastname.'<br><br><br>

                    Your leave request ('.$Request->title.') for '.date('d/m/Y',strtotime($AdminRequest->datetime_from)).(($AdminRequest->datetime_to) ? ' to time '.date('d/m/Y',strtotime($AdminRequest->datetime_to)) : '').'.
                    Has been '.$statusText.'.',
                ];

            try {
                Mail::to($admin->email)->send(new SendMail($details));
            }catch (\Exception $e){

            }
        }
        $AdminRequest->save();


        return redirect()->back();
    }

    public function cancelRequest(Request $request){
        $request->validate([
            'disabled'=>'required',
        ]);
        $AdminRequest = AdminRequest::find(request('disabled'));
        $AdminRequest->cancel_request = 1;
        $AdminRequest->save();

        return redirect()->back();
    }

    public function cancelRequestAction(Request $request){
        $request->validate([
            '_id'=>'required',
            'CancelStatus'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $CancelStatus=request('CancelStatus');
        $AdminRequest = AdminRequest::find(request('_id'));
        if($CancelStatus==1){
            $AdminRequest->manager_status = 3;
            $AdminRequest->manager_admin = $adminAuth->id;
            $AdminRequest->cancel_request = 2;
            $AdminRequest->approve_cancel_seen = 1;
        }
        if($CancelStatus==2){
            $AdminRequest->cancel_request = 3;
        }

        $AdminRequest->save();

        return redirect()->back();
    }

    public function Delete(){
        $AdminRequest = AdminRequest::find( request('Delete') );
        $AdminRequest->delete();

        return redirect()->back();
    }

}
