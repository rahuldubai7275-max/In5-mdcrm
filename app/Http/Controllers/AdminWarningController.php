<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\Admin;
use App\Models\AdminWarning;
use App\Models\WarningType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminWarningController extends Controller
{
    public function warnings(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/admin-warnings', [
            'pageConfigs' => $pageConfigs,
        ]);
    }
    public function getWarning(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $where='';

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM admin_warnings,admins,warnings_type WHERE admin_warnings.admin_id=admins.id AND admin_warnings.warning_id=warnings_type.id ");
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if(request('warning')){
            $where .= ' AND warning_id=' . request('warning');
        }

        if(request('admin')){
            $where .= ' AND admin_id=' . request('admin');
        }

        if( request('from_date') )
            $where.=' AND admin_warnings.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND admin_warnings.created_at <="'.request('to_date').' 23:59:59"';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM admin_warnings,admins,warnings_type WHERE admin_warnings.admin_id=admins.id AND admin_warnings.warning_id=warnings_type.id ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT admin_warnings.*,admins.firstname,admins.lastname, warnings_type.name FROM admin_warnings,admins,warnings_type WHERE admin_warnings.admin_id=admins.id AND admin_warnings.warning_id=warnings_type.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT admin_warnings.*,admins.firstname,admins.lastname, warnings_type.name FROM admin_warnings,admins,warnings_type WHERE admin_warnings.admin_id=admins.id AND admin_warnings.warning_id=warnings_type.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['name']=$row->name;
            $obj['reason']=$row->reason;
            $obj['status']=($row->status==1) ? 'Seen' : 'Sent';
            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("warning.delete").'">
                                '.(($adminAuth->type==1) ? '<a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>':'' ).'
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

    public function store(Request $request){
        $request->validate([
            'admin'=>'required',
            'warning'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $Admin=Admin::find(request('admin'));
        $warning=WarningType::find(request('warning'));
        AdminWarning::create([
            'creator_id'=>$adminAuth->id,
            'admin_id'=>request('admin'),
            'warning_id'=>request('warning'),
            'reason'=>request('reason'),
            'description'=>request('description')
        ]);

        $body='Dear '.$Admin->firstname.' '.$Admin->lastname.'<br><br><br>You have a warning letter<br><br><br><b>Date: </b>'
            .\Helper::changeDatetimeFormat( date('Y-m-d H:i:s'))
        .'<br><br><b>Warning Type: </b>'.$warning->name.'<br><br><b>Reason: </b>'.request('reason').'<br><br><br>'.request('description');

        $details = [
            'subject' => 'Warning Letter',
            'body' => $body
        ];

        try {
            Mail::to($Admin->email)->send(new SendMail($details));
        }catch (\Exception $e){

        }
        return redirect('/admin/warnings');
    }

    public function details(Request $request){
        $request->validate([
            'warning'=>'required',
        ]);

        $AdminWarning=AdminWarning::find(request('warning'));
        $WarningType=WarningType::find($AdminWarning->warning_id);
        $Admin=Admin::find($AdminWarning->admin_id);
        return '<p><b>Date:</b> '.\Helper::changeDatetimeFormat( $AdminWarning->created_at).'</p>
                <p><b>Client Manager:</b> '.$Admin->firstname.' '.$Admin->lastname.'</p>
                <p><b>Warning Type:</b> '.$WarningType->name.'</p>
                <p><b>Reason:</b> '.$AdminWarning->reason.'</p>
                <p><b>Notice:</b> '.$AdminWarning->description.'</p>';
    }

    public function acknowledge(Request $request){
        $request->validate([
            'WarningAcknowledge_id'=>'required',
        ]);

        $AdminWarning=AdminWarning::find(request('WarningAcknowledge_id'));
        $WarningType=WarningType::find($AdminWarning->warning_id);
        $Admin=Admin::find($AdminWarning->admin_id);
        $Creator=Admin::find($AdminWarning->creator_id);
        $AdminWarning->status=1;
        $AdminWarning->save();

        $body='Dear '.$Creator->firstname.' '.$Creator->lastname.'<br><br><br>I acknowledge receipt of the warning letter dated '.\Helper::changeDatetimeFormat( date('Y-m-d H:i:s')).'. I understand the concerns.'
            .'<br><br><br>Thank you!<br><br><br>Best regards,<br>'.$Admin->firstname.' '.$Admin->lastname;

        $details = [
            'subject' => 'Acknowledgment of Warning Letter',
            'body' => $body
        ];

        try {
            Mail::to($Creator->email)->send(new SendMail($details));
        }catch (\Exception $e){

        }
        return back()->withInput();

    }


    public function Delete(){
        $AdminWarning = AdminWarning::find( request('Delete') );

        $WarningType=WarningType::find($AdminWarning->warning_id);
        $Admin=Admin::find($AdminWarning->admin_id);
        $Creator=Admin::find($AdminWarning->creator_id);
        $AdminWarning->status=1;
        $AdminWarning->save();

        $body='Dear '.$Admin->firstname.' '.$Admin->lastname.'<br><br><br>The past warning letter issued on '.\Helper::changeDatetimeFormat( date('Y-m-d H:i:s')).' was a mistake. Please disregard it, as it will be removed from your profile.'
            .'<br><br><br>Thank you!<br><br><br>Best regards,<br>'.$Creator->firstname.' '.$Creator->lastname;

        $details = [
            'subject' => 'Clarification on Warning Letter',
            'body' => $body
        ];

        try {
            Mail::to($Admin->email)->send(new SendMail($details));
        }catch (\Exception $e){

        }

        $AdminWarning->delete();


        return redirect('/admin/warnings');
    }

}
