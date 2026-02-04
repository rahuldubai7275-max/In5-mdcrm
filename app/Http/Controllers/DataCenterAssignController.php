<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\DataCenterAssign;
use App\Models\DataCenterAssignProject;
use App\Models\MasterProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataCenterAssignController extends Controller
{
    public function DataCenterAssign(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/data-center-assign', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'Admin'=>'required',
            'MasterProject'=>'required',
        ]);
        $admin=request('Admin');
        $masterProject=request('MasterProject');
        $all=(request('all'))? 1 : 0;
        $unmatched=(request('unmatched'))? 1 : 0;
        $projects=request('Projects');
        $DataCenterAssign=DataCenterAssign::where('admin_id',$admin)->where('master_project_id',$masterProject)->first();
        if(!$DataCenterAssign) {
            $DCA=DataCenterAssign::create([
                'admin_id' => $admin,
                'master_project_id' => $masterProject,
                'all' => $all,
                'unmatched' => $unmatched,
            ]);
            if($all==0 && $projects) {
                foreach ($projects as $project) {
                    DataCenterAssignProject::create([
                        'dca_id' => $DCA->id,
                        'project_id' => $project
                    ]);
                }
            }
        }
        return redirect('/admin/data-center-assign');
    }


    public function Edit(Request $request){
        $request->validate([
            'Admin'=>'required',
            'MasterProject'=>'required',
        ]);

        $admin=request('Admin');
        $masterProject=request('MasterProject');
        $all=(request('all'))? 1 : 0;
        $unmatched=(request('unmatched'))? 1 : 0;
        $projects=request('Projects');

        $DCA=DataCenterAssign::find(request('update'));

        $DCA->admin_id= $admin;
        $DCA->master_project_id= $masterProject;
        $DCA->all= $all;
        $DCA->unmatched= $unmatched;


        DataCenterAssignProject::where('dca_id',$DCA->id)->delete();
        if($all==0 && $projects) {
            foreach ($projects as $project) {
                DataCenterAssignProject::create([
                    'dca_id' => $DCA->id,
                    'project_id' => $project
                ]);
            }
        }

        $DCA->save();

        return redirect('/admin/data-center-assign');
    }

    public function getDC_Assign(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' id DESC';
        if($columnIndex){
            $orderBy=$columnName." ".$columnSortOrder;
        }

        $where='';

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM data_center_assign,admins,master_project WHERE data_center_assign.admin_id=admins.id AND data_center_assign.master_project_id=master_project.id ");
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if(request('master_project')){
            $where .= ' AND master_project_id=' . request('master_project');
        }

        if(request('admin')){
            $where .= ' AND admin_id=' . request('admin');
        }

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM data_center_assign,admins,master_project WHERE data_center_assign.admin_id=admins.id AND data_center_assign.master_project_id=master_project.id ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT data_center_assign.*,admins.firstname,admins.lastname,master_project.name as master_project_name FROM data_center_assign,admins,master_project WHERE data_center_assign.admin_id=admins.id AND data_center_assign.master_project_id=master_project.id ".$where." ORDER BY ".$orderBy);
        }else{
            $Records=DB::select("SELECT data_center_assign.*,admins.firstname,admins.lastname,master_project.name as master_project_name FROM data_center_assign,admins,master_project WHERE data_center_assign.admin_id=admins.id AND data_center_assign.master_project_id=master_project.id ".$where." ORDER BY ".$orderBy." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['master_project_name']=$row->master_project_name;

            $projects='';
            if($row->all==1){
                $projects='All';
            }else{
                $Project_ids=DataCenterAssignProject::where('dca_id', $row->id)->pluck('project_id')->toArray();
                $Community=Community::whereIn('id', $Project_ids)->pluck('name')->toArray();
                $projects=($Community) ? '<span class="projects" data-target="#ViewModal" data-toggle="modal" data-id="'.$row->id.'" data-title="'.$row->firstname.' '.$row->lastname.' - '.$row->master_project_name.'">'.join(', ',$Community).'</span>' : '';
            }
            $obj['unmatched']=($row->unmatched) ? 'Yes' : 'No';
            $obj['project']=$projects;

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("dc-assign.delete").'">
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
            'dca_id'=>'required',
        ]);
        $dca_id=request('dca_id');
        $Project_ids=DataCenterAssignProject::where('dca_id', $dca_id)->pluck('project_id')->toArray();
        $Community=Community::whereIn('id', $Project_ids)->pluck('name')->toArray();
        $output='<ul class="list-group list-group-flush">';
        foreach ($Community as $row){
            $output.='<li class="list-group-item">'.$row.'</li>';
        }
        return $output.='</ul>';
    }

    public function getDetails(Request $request){
        $request->validate([
            'dca_id'=>'required',
        ]);
        $dca_id=request('dca_id');
        $DataCenterAssign=DataCenterAssign::where('id', $dca_id)->first();
        $Project_ids=DataCenterAssignProject::where('dca_id', $dca_id)->pluck('project_id')->toArray();

        $response=array(
            "admin_id" => $DataCenterAssign->admin_id,
            "master_project_id" => $DataCenterAssign->master_project_id,
            "all" => $DataCenterAssign->all,
            "unmatched" => $DataCenterAssign->unmatched,
            "projects" => $Project_ids
        );
        return $response;
    }

    public function Delete(){
        $DataCenterAssign = DataCenterAssign::find( request('Delete') );
        $DataCenterAssign->delete();
        return redirect('/admin/data-center-assign');
    }

}

