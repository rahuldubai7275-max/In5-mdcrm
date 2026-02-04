<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\DataCenterAccess;
use App\Models\MasterProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataCenterAccessController extends Controller
{
    public function DataCenterAccess(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/data-center-access', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'Admin'=>'required|string',
            'MasterProject'=>'required|string',
        ]);

        $DataCenterAccess=DataCenterAccess::where('admin_id',request('Admin'))->where('master_project_id',request('MasterProject'))->first();
        if(!$DataCenterAccess) {
            //$unmatched=(request('unmatched'))? 1 : 0;
            DataCenterAccess::create([
                'admin_id' => request('Admin'),
                'master_project_id' => request('MasterProject'),
                'projects' => (request('Community')) ? join(',', request('Community')) : null,
                //'unmatched' => $unmatched,
            ]);
        }

        return redirect('/admin/data-center-access');
    }

    public function Edit(Request $request){
        $request->validate([
            'update'=>'required',
            'Admin'=>'required|string',
            'MasterProject'=>'required|string',
        ]);
        //$unmatched=(request('unmatched'))? 1 : 0;

        $DataCenterAccess=DataCenterAccess::find(request('update'));

        $DataCenterAccess->admin_id= request('Admin');
        $DataCenterAccess->master_project_id= request('MasterProject');
        $DataCenterAccess->projects= (request('Community')) ? join(',',request('Community')) : null;
        //$DataCenterAccess->unmatched= $unmatched;
        $DataCenterAccess->save();

        return redirect('/admin/data-center-access');
    }

    public function getDC_Access(){
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

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM data_center_access,admins,master_project WHERE data_center_access.admin_id=admins.id AND data_center_access.master_project_id=master_project.id ");
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if(request('master_project')){
            $where .= ' AND master_project_id=' . request('master_project');
        }

        if(request('admin')){
            $where .= ' AND admin_id=' . request('admin');
        }

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM data_center_access,admins,master_project WHERE data_center_access.admin_id=admins.id AND data_center_access.master_project_id=master_project.id ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT data_center_access.*,admins.firstname,admins.lastname,master_project.name as master_project_name FROM data_center_access,admins,master_project WHERE data_center_access.admin_id=admins.id AND data_center_access.master_project_id=master_project.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT data_center_access.*,admins.firstname,admins.lastname,master_project.name as master_project_name FROM data_center_access,admins,master_project WHERE data_center_access.admin_id=admins.id AND data_center_access.master_project_id=master_project.id ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $Community=Community::whereIn('id', explode(',',$row->projects))->pluck('name')->toArray();
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['master_project_name']=$row->master_project_name;
            $obj['projects']=($Community) ? '<span class="projects" data-target="#ViewModal" data-toggle="modal" data-id="'.$row->id.'" data-title="'.$row->firstname.' '.$row->lastname.' - '.$row->master_project_name.'">'.join(', ',$Community).'</span>' : '';
            //$obj['unmatched']=($row->unmatched) ? 'Yes' : 'No';
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("dc-access.delete").'"
                            data-admin="'.$row->admin_id.'"
                            data-masterproject="'.$row->master_project_id.'"
                            data-unmatched="'.$row->unmatched.'"
                            data-project="'.$row->projects.'">
                            <a class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                            <a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
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

    public function getProjects(Request $request){
        $request->validate([
            'id'=>'required',
        ]);
        $id=request('id');
        $DataCenterAccess=DataCenterAccess::where('id', $id)->first();
        $Community=Community::whereIn('id', explode(',',$DataCenterAccess->projects))->pluck('name')->toArray();
        $output='<ul class="list-group list-group-flush">';
        foreach ($Community as $row){
            $output.='<li class="list-group-item">'.$row.'</li>';
        }
        return $output.='</ul>';
    }

    public function Delete(){
        $DataCenterAccess = DataCenterAccess::find( request('Delete') );
        $DataCenterAccess->delete();
        return redirect('/admin/data-center-access');
    }

}

