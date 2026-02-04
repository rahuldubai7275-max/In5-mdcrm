<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\AdminHrRequest;
use App\Models\HRRequest;
use App\Models\SettingAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminHrRequestController extends Controller
{
    public function requests(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        if(request('id')){
            $AdminHrRequest = AdminHrRequest::find( request('id') );
            $adminAuth=\Auth::guard('admin')->user();
            if($AdminHrRequest->admin_id==$adminAuth->id) {
                $AdminHrRequest->seen = 1;
                $AdminHrRequest->save();
            }
        }
        return view('/admin/admin-hr-requests', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'hr_request'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        AdminHrRequest::create([
            'admin_id'=>$adminAuth->id,
            'hr_request_id'=>request('hr_request'),
            'description'=>request('description')
        ]);
        return redirect()->back();
    }

    public function reply(Request $request){
        $request->validate([
            '_id'=>'required',
            'reply'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $AdminHrRequest=AdminHrRequest::find(request('_id'));
        $AdminHrRequest->hr_id=$adminAuth->id;
        $AdminHrRequest->reply=request('reply');
        $AdminHrRequest->reply_date=date('Y-m-d H:i:s');
        $AdminHrRequest->status='1';
        $AdminHrRequest->save();

        $admin=Admin::find($AdminHrRequest->admin_id);

        $details = [
            'subject' => 'Dont Reply (Request)',
            'body' => 'Dear '.$admin->firstname.' '.$admin->lastname.'<br><br><br>
            Your request has received , below you can find the details.<br><br>'.$AdminHrRequest->description.'<br><br><br><b>Reply:</b>'.$AdminHrRequest->reply,
        ];

        try {
            Mail::to($admin->email)->send(new SendMail($details));
        }catch (\Exception $e){

        }

        return redirect()->back();
    }

    public function GetRequest()
    {
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $adminAuth=\Auth::guard('admin')->user();

        $where = '';

        $hr_access=0;
        if($adminAuth->type=='6') {
            $hr_access = 1;
        }else {
            $SettingAdmin = SettingAdmin::where('setting_id', 16)->where('admin_id', $adminAuth->id)->first();
            if($SettingAdmin) {
                $hr_access = 1;
            }
        }

        if($hr_access==0 && $adminAuth->type!='1') {
            $where.= " AND admin_hr_requests.admin_id = ".$adminAuth->id;
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `admin_hr_requests` WHERE 1 ".$where);
        $totalRecords = $totalRecords[0]->countAll;

        if( request('profile') )
            $where.=' AND admin_id='.request('profile');

        if( request('admin') )
            $where.=' AND admin_id='.request('admin');

        if( request('request') )
            $where.=' AND hr_request_id ='.request('request');

        if( request('status') || request('status')=='0' )
            $where.=' AND admin_hr_requests.status="'.request('status').'"';


        if( request('from_date') )
            $where.=' AND admin_hr_requests.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND admin_hr_requests.created_at <="'.request('to_date').' 23:59:59"';


        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM admin_hr_requests WHERE 1 ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();


        if($columnName=='date_at'){
            $columnName="CONCAT(date_at,' ',time_at)";
        }

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT admin_hr_requests.*, firstname, lastname FROM admin_hr_requests,admins WHERE admin_hr_requests.admin_id=admins.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT admin_hr_requests.*, firstname, lastname FROM admin_hr_requests,admins WHERE admin_hr_requests.admin_id=admins.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $HRRequest=HRRequest::find($row->hr_request_id);
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['hr_request_id']=$HRRequest->title;
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);
            $obj['reply_date']=($row->reply_date) ? \Helper::changeDatetimeFormat($row->reply_date) : '';

            $obj['status']=($row->status=='0') ? '<span class="badge badge-pill badge-light-danger" style="width: 120px">New</span>' : '<span class="badge badge-pill badge-light-success" style="width: 120px">Replied</span>';

            $action='';

            //if($row->status==0 && $hr_access==1)
            //    $action= '<a href="#replyModal" data-toggle="modal" class="reply-request"><span class="btn btn-primary">Reply</span></a>';

            $obj['action']='<div class="action font-medium-3 d-flex" data-id="'.$row->id.'">
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

    public function Delete(){
        $AdminHrRequest = AdminHrRequest::find( request('Delete') );
        $AdminHrRequest->delete();

        return redirect()->back();
    }

    public function details(Request $request){
        $request->validate([
            'request'=>'required'
        ]);
        $AdminHrRequest = AdminHrRequest::find( request('request') );
        $admin=Admin::find($AdminHrRequest->admin_id);
        $HRRequest=HRRequest::find($AdminHrRequest->hr_request_id);

        $user=$admin->firstname.' '.$admin->lastname;

        $detail='<p><b>Request Type:</b> '.$HRRequest->title.'</p>';
        $detail.='<p><b>Request:</b> '.$AdminHrRequest->description.'</p>';

        if($AdminHrRequest->status==0){
            $detail.='<br><br>
                            <div class="form-group form-label-group">
                                <label for="reply">Reply</label>
                                <textarea class="form-control" id="description" name="reply" placeholder="Reply"></textarea>
                            </div>

                            <br>
                            <input type="hidden" value="'.$AdminHrRequest->id.'" id="_id" name="_id">
                            <div class="clearfix">
                                <button type="submit" class="btn btn-primary float-right">Reply</button>
                            </div>
                        ';
        }else{
            $detail.='<br><br><p><b>Reply:</b></p>';
            $detail.='<p>'.$AdminHrRequest->reply.'</p>';
        }

        return ['user'=>$user,'detail'=>$detail];

    }



}
