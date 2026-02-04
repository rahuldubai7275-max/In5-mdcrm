<?php

namespace App\Http\Controllers;

use App\Exports\ExportDataCenter;
use App\Imports\DataCenterImports;
use App\Models\Admin;
use App\Models\Community;
use App\Models\Company;
use App\Models\Contact;
use App\Models\DataCenter;
use App\Models\DataCenterAccess;
use App\Models\DataCenterFile;
use App\Models\DataCenterNote;
use App\Models\DataCenterUpload;
use App\Models\Lead;
use App\Models\MasterProject;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Caster\DateCaster;
use Symfony\Component\VarDumper\Cloner\Data;

class DataCenterController extends Controller
{
    public function DataCenter(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/data-center', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'MasterProject' => 'required',
            'file' => 'required|mimes:xlsx,xls',
        ]);

        //ini_set('max_execution_time', 10000);

        //$isError = false;
        // Get the uploaded file
        $file = $request->file('file');

        $file_name=$request->file('file')->getClientOriginalName();

        /*    Excel::load($file, function($reader) use (&$isError) {

                $firstrow = $reader->first()->toArray();

                if (isset($firstrow['Master Project']) &&
                    isset($firstrow['Project']) &&
                    isset($firstrow['ST.CL.FR']) &&
                    isset($firstrow['Villa/Unit Number']) &&
                    isset($firstrow['Bedrooms']) &&
                    isset($firstrow['Size']) &&
                    isset($firstrow['Plot']) &&
                    isset($firstrow['Name']) &&
                    isset($firstrow['Phone Number']) &&
                    isset($firstrow['Phone Number 2']) &&
                    isset($firstrow['Email']) &&
                    isset($firstrow['Nationality'])) {
                    $rows = $reader->all();
                    foreach ($rows as $row) {
                        echo $row->Size.' '.$row->Plot.' '.$row->Name."<br />";
                    }
                }
                else {
                    $isError = true;

                }

            });
            if ($isError) {
                return View::make('error');
            }*/

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
        Excel::import(new DataCenterImports, $file);

        $arr_file_name=explode('.',$file_name);
        $file_format=end($arr_file_name);
        $file_name=str_replace('.'.$file_format,'',$file_name);
        $master_project_id=request('MasterProject');

        $DataCenterFile=DataCenterFile::where('name',$file_name)->first();
        if(!$DataCenterFile) {
            $DataCenterFile=DataCenterFile::create(['master_project_id' => $master_project_id,'name' => $file_name,'file_name' => $excel_name]);
        }

        //update master_project_id adn file_name
        DB::select("UPDATE data_center_upload SET master_project_id='".$master_project_id."' WHERE file_id IS NULL");

        $projectsText=DataCenterUpload::where('master_project_id',$master_project_id)->select('project')->distinct()->get();

        foreach($projectsText as $row){
            //$Community=Community::where('master_project_id',$master_project_id)->where('name',trim($row->project))->first();
            //if($Community) {
            //    DB::select("UPDATE data_center SET project_id=".$Community->id." WHERE master_project_id='".$master_project_id."' AND project='".$row->project."'");
            //}

            $project_have_parent=DB::select("SELECT * from (SELECT community.id, community.name as 'project', community_parent.name as 'parent',concat(community.name,' - ',community_parent.name) as full_name FROM `community` LEFT JOIN community_parent ON community.parent_id=community_parent.id WHERE community.master_project_id=".$master_project_id.") a WHERE a.full_name='".trim($row->project)."'");
            \Log::info("SELECT * from (SELECT community.id, community.name as 'project', community_parent.name as 'parent',concat(community.name,' - ',community_parent.name) as full_name FROM `community` LEFT JOIN community_parent ON community.parent_id=community_parent.id WHERE community.master_project_id=".$master_project_id.") a WHERE a.full_name='".trim($row->project)."'");
            if($project_have_parent) {
                DB::select("UPDATE data_center_upload SET project_id=" . $project_have_parent[0]->id . " WHERE master_project_id='" . $master_project_id . "' AND project='" . $row->project . "'");
            }else{
                $Community=DB::select("SELECT * from (SELECT community.id, community.name as 'project', community_parent.name as 'parent',concat(community.name,' - ',community_parent.name) as full_name FROM `community` LEFT JOIN community_parent ON community.parent_id=community_parent.id WHERE community.master_project_id=".$master_project_id.") a WHERE a.project='".trim($row->project)."'");
                \Log::info("SELECT * from (SELECT community.id, community.name as 'project', community_parent.name as 'parent',concat(community.name,' - ',community_parent.name) as full_name FROM `community` LEFT JOIN community_parent ON community.parent_id=community_parent.id WHERE community.master_project_id=".$master_project_id.") a WHERE a.project='".trim($row->project)."'");
                if($Community) {
                    DB::select("UPDATE data_center_upload SET project_id=".$Community[0]->id." WHERE master_project_id='".$master_project_id."' AND project='".$row->project."'");
                }
            }
        }

        DB::select("UPDATE data_center_upload SET file_id='".$DataCenterFile->id."' WHERE file_id IS NULL");


        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }

    public function importToData()
    {
        $dataCenter=DB::select("SELECT * FROM `data_center_upload` WHERE file_id IS NOT NULL ORDER BY id ASC LIMIT 0,500");
        if(count($dataCenter)>0) {
            $sql = 'INSERT INTO `data_center`(`master_project`, `master_project_id`, `project`, `project_id`, `st_cl_fr`, `villa_unit_no`, `bedrooms`, `size`, `name`, `phone_number`, `phone_number_2`, `email`, `nationality`, `file_id`, `plot_size`, `created_at`, `updated_at`) VALUES ';
            $ids = '';

            $count = 0;
            foreach ($dataCenter as $row) {
                $Data = DataCenter::where('master_project', $row->master_project)->
                where('project', $row->project)->
                where('st_cl_fr', $row->st_cl_fr)->
                where('villa_unit_no', $row->villa_unit_no)->
                where('name', $row->name)->
                where('phone_number', $row->phone_number . '')->
                where('phone_number_2', $row->phone_number_2 . '')->
                where('email', $row->email)->first();

                if (!$Data) {
                    $sql .= '("' . $row->master_project . '", "' . $row->master_project_id . '", "' . $row->project . '", ' . (($row->project_id) ? $row->project_id : 'NULL') . ', "' . $row->st_cl_fr . '", "' . $row->villa_unit_no . '", "' . $row->bedrooms . '", "' . $row->size . '", "' . $row->name . '", "' . $row->phone_number . '", "' . $row->phone_number_2 . '", "' . $row->email . '", "' . $row->nationality . '", "' . $row->file_id . '", "' . $row->plot_size . '", "' . $row->created_at . '", "' . $row->updated_at . '"),';
                    $count++;
                }

                $ids .= $row->id . ',';
            }

            DB::statement('DELETE FROM `data_center_upload` WHERE id IN (' . rtrim($ids, ',') . ')');

            if($count>0)
                DB::statement(rtrim($sql, ','));

            $count = DataCenterUpload::count();
            if ($count == 0) {
                DB::statement('TRUNCATE data_center_upload');
            }
        }

    }

    public function DataCenterArranged(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/data-center-arranged', [
            'pageConfigs' => $pageConfigs,
        ]);
    }
    public function agentAssign(Request $request){
        if ( request('data') ){
            $assign_to = request('AssignTo');
            $data = request('data');
            foreach ($data as $id) {

                $DataCenter = DataCenter::find($id);

                $adminAuth = \Auth::guard('admin')->user();

                if ($assign_to){
                    $DataCenter->agent_assign_admin = $adminAuth->id;
                    $DataCenter->agent_assign = $assign_to;
                    $DataCenter->agent_assign_time =date('Y-m-d H:i:s');
                }else{
                    $DataCenter->agent_assign_admin = null;
                    $DataCenter->agent_assign = null;
                    $DataCenter->agent_assign_time =null;
                }

                $DataCenter->save();
            }
        }
    }
    public function view(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        if(request('id')) {
            $id=request('id');
            $adminAuth=\Auth::guard('admin')->user();

            if(request('reminder')){
                $rDataCenterNote=DataCenterNote::find(request('reminder'));
                $rDataCenterNote->seen=1;
                $rDataCenterNote->save();
            }

            if($adminAuth->type<3) {
                $data = DataCenter::find($id);
            }else {
                //$data = DB::select(str_replace('___',' AND a.id=' . $id,Session::get('data_center_where')));
                $firstWhere='';
                $projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNotNull('projects')->pluck('projects')->toArray();
                if ($projects) {
                    $firstWhere.=" OR project_id IN (".join(',', $projects).")";
                }
                $master_projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNull('projects')->pluck('master_project_id')->toArray();
                if ($master_projects) {
                    $firstWhere.=" OR master_project_id IN (".join(',', $master_projects).")";
                }
                $data = DB::select("SELECT * FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a WHERE 1 AND a.id=".$id);
                if($data)
                    $data=$data[0];
            }

            $Previous='';
            $Next='';
            if(Session::exists('data_center_where')) {
                if($adminAuth->type<3) {
                    $DataCenter = DB::select("SELECT data_center.id FROM `data_center` " . Session::get('data_center_where'));
                }else {
                    $DataCenter = DB::select("SELECT id FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a " . Session::get('data_center_where'));
                }
                if($DataCenter && $data) {
                    $dataArray = [];
                    foreach ($DataCenter as $row) {
                        $dataArray[] = $row->id;
                    }

                    $array_index = array_search($data->id, $dataArray);
                    $countArray = count($dataArray);
                    $countArray--;

                    $Previous = ($array_index == 0) ? '' : $dataArray[$array_index - 1];
                    $Next = ($array_index == $countArray) ? '' : $dataArray[$array_index + 1];
                }
            }

            if($data) {
                return view('/admin/data-center-view', [
                    'pageConfigs' => $pageConfigs,
                    'data' => $data,
                    'Previous'=>$Previous,
                    'Next'=>$Next,
                ]);
            }else{
                return "You don't have access to this data anymore";
            }
        }else{
            return 'page not found';
        }
    }
    public function matchMasterProject(){
        if ( request('data') ){

            $master_project=request('master_project');

            foreach (request('data') as $id) {

                $DataCenter = DataCenter::find($id);
                $DataCenter->master_project_id = $master_project;
                $DataCenter->save();

            }

            return ['r'=>'1','msg'=>''];
        }
    }

    public function matchProject(){
        if ( request('data') ){

            $project=request('project');

            foreach (request('data') as $id) {
                $DataCenter = DataCenter::find($id);
                $DataCenter->project_id = $project;
                $DataCenter->save();

            }

            return ['r'=>'1','msg'=>''];
        }
    }

    public function assign(Request $request){
        $request->validate([
            'dc'=>'required',
            'assign_to'=>'required',
            'contact_category'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $DataCenter=DataCenter::find(request('dc'));

        $source=($adminAuth->type==7) ? 41 : 35;
        $lead=Lead::create([
            'type'=>'manual',
            'telesales_id'=>$adminAuth->id,
            'admin_id'=>$adminAuth->id,
            'data_center_id'=>$DataCenter->id,
            'name'=>$DataCenter->name,
            'mobile_number'=>$DataCenter->phone_number,
            'email'=>($DataCenter->email && $DataCenter->email!='-') ? $DataCenter->email : '',
            'contact_category'=>request('contact_category'),//owner
            'source'=>$source,
            'master_project_id'=>$DataCenter->master_project_id,
            'assign_to'=>request('assign_to'),
            'assign_time'=>date('Y-m-d H:i:s'),

        ]);

        $DataCenter->status_by=$adminAuth->id;
        $DataCenter->status=2;//assign
        $DataCenter->assign_to=request('assign_to');
        $DataCenter->assign_date=date('Y-m-d H:i:s');
        $DataCenter->save();
        return redirect('/admin/data-center-view/'.$DataCenter->id);
    }

    public function close(Request $request){
        $request->validate([
            'dc'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $DataCenter=DataCenter::find(request('dc'));
        $DataCenter->status=3;
        $DataCenter->status_by=$adminAuth->id;
        $DataCenter->colse_reason=request('colse_reason');
        $DataCenter->result_date=date('Y-m-d H:i:s');
        $DataCenter->save();

        return redirect('/admin/data-center-view/'.$DataCenter->id);
    }

    public function action(Request $request){
        $request->validate([
            'dc'=>'required|string',
            'status'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $DataCenter=DataCenter::find(request('dc'));
        $DataCenter->status=request('status');
        $DataCenter->status_by=$adminAuth->id;
        $DataCenter->result_date=date('Y-m-d H:i:s');
        $DataCenter->save();

        return redirect('/admin/data-center-view/'.$DataCenter->id);
    }



    public function exportdata(Request $request){
        return Excel::download(new ExportDataCenter, 'data.xlsx');
    }

    public function getData(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' ORDER BY id DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $DCA_Count = DataCenterAccess::where('admin_id', $adminAuth->id)->count();
        if(request('data_center')=='0' || ($DCA_Count==0 && $adminAuth->type>2) ){
            return $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => []
            );
        }

        $where=' WHERE 1 ';

        //$where.=' AND master_project_id IS NOT NULL';

        if(request('unmatched')){
            $unmatched=request('unmatched');
            if($unmatched==1)
                $where .= ' AND project_id IS NOT NULL';
            if($unmatched==2)
                $where .= ' AND project_id IS NULL';
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `data_center` ".$where);
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if(request('master_project_text')){
            $where.=' AND master_project="'.request('master_project_text').'"';
        }

        if(request('project_text')){
            $where.=' AND project LIKE "%'.request('project_text').'%"';
        }

        if(request('nationality')){
            $where.=' AND nationality LIKE "%'.request('nationality').'%"';
        }

        if(request('name')){
            $where.=' AND name LIKE "%'.request('name').'%"';
        }

        if(request('phone_number')){
            $where.=' AND (phone_number LIKE "%'.request('phone_number').'%" OR phone_number_2 LIKE "%'.request('phone_number').'%")';
        }

        if(request('bedroom')){
            $where.=' AND bedrooms="'.request('bedroom').'"';
        }

        if(request('bua_from')){
            $where.=' AND size>='.request('bua_from');
        }

        if(request('bua_to')){
            $where.=' AND size<='.request('bua_to');
        }

        if(request('plot_from')){
            $where.=' AND plot_size>='.request('plot_from');
        }

        if(request('plot_to')){
            $where.=' AND plot_size<='.request('plot_to');
        }

        if(request('file')){
            $where.=' AND file_id='.request('file');
        }

        if(request('master_project_id')){
            $where .= ' AND master_project_id=' . request('master_project_id');
        }

        if(request('villa_unit_no')){
            $where.=' AND villa_unit_no LIKE "%'.request('villa_unit_no').'%"';
        }

        if(request('ref_id')){
            $where .= ' AND id=' . request('ref_id');
        }

        if(request('assigned_to')){
            $cm=0;
            if(request('status')==2) {
                $where .= ' AND status_by=' . request('assigned_to');
                $cm++;
            }
            if(request('assigned')) {
                $where .= ' AND agent_assign=' . request('assigned_to');
                $cm++;
            }

            if($cm==0){
                $where .= ' AND (status_by=' . request('assigned_to'). ' OR agent_assign=' . request('assigned_to').')';
            }

        }

        if(request('project_id')){
            $where.=' AND project_id IN ('.request('project_id').')';
        }

        if(request('status')){
            if(request('status')=='added_to_property') {
                $where .= ' AND added_to_property IS NOT NULL';
            }elseif(request('status')=='added_to_contact') {
                $where .= ' AND added_to_contact IS NOT NULL';
            }else {
                $where .= ' AND added_to_contact IS NULL AND added_to_property IS NULL AND status=' . request('status');
            }
        }

        if(request('assigned')){
            if(request('assigned')==1)
                $where.=' AND agent_assign IS NOT NULL';
            if(request('assigned')==2)
                $where.=' AND agent_assign IS NULL';
        }

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM data_center ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM data_center ".$where.$orderBy);
            Session::put('data_center_where', $where.$orderBy." , data_center.id DESC");
        }else{
            $Records=DB::select("SELECT * FROM data_center ".$where.$orderBy."  limit ".$start.",".$rowperpage);
            Session::put('data_center_where', $where.$orderBy." , data_center.id DESC ");
        }

        $obj=[];
        foreach($Records as $row){
            $checkbox='';
            if($row->status!=2 && $row->added_to_property=='' && $row->added_to_contact=='')
                $checkbox='<div class="d-inline-block checkbox">
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" value="'.$row->id.'" name="data[]">
                                        </label>
                                    </fieldset>
                                </div>';

            $obj['checkbox']=$checkbox;

            $master_project='';
            if($row->master_project_id){
                $MProject=MasterProject::find($row->master_project_id);
                $master_project=$MProject->name;
            }else{
                $master_project=$row->master_project;
            }

            $project='';
            if($row->project_id){
                $Community=Community::find($row->project_id);
                $project=$Community->name;
            }else{
                $project=$row->project;
            }

            if($row->agent_assign_admin)
                $agent_assign_admin=Admin::find($row->agent_assign_admin);
            if($row->status_by)
                $status_by=Admin::find($row->status_by);
            if($row->assign_to)
                $assign_to=Admin::find($row->assign_to);
            if($row->agent_assign)
                $agent_assign=Admin::find($row->agent_assign);
            if($row->assign_to)
                $assign_lead=Admin::find($row->assign_to);
            $obj['id']='DC-'.$row->id;
            $obj['master_project']=$master_project;
            $obj['project']=$project;
            $obj['st_cl_fr']=$row->st_cl_fr;
            $obj['villa_unit_no']=$row->villa_unit_no;
            $obj['bedrooms']=$row->bedrooms;
            $obj['size']=($row->size) ? number_format($row->size) : '';
            $obj['plot_size']=($row->plot_size) ? number_format($row->plot_size) : '';
            $obj['name']=$row->name;
            $obj['phone_number']=$row->phone_number;
            $obj['phone_number_2']=$row->phone_number_2;
            $obj['email']=$row->email;
            $obj['nationality']=$row->nationality;
            $obj['agent_assign_admin']=($row->agent_assign_admin) ? $agent_assign_admin->firstname.' '.$agent_assign_admin->lastname : '';
            $obj['agent_assign']=($row->agent_assign) ? $agent_assign->firstname.' '.$agent_assign->lastname : '';
            $obj['agent_assign_time']=($row->agent_assign_time) ? \Helper::changeDatetimeFormat($row->agent_assign_time) : '';
            $obj['assign_to']=($row->assign_to) ? $assign_to->firstname.' '.$assign_to->lastname : '';
            $obj['assign_date']=($row->assign_date && $row->status==2) ? \Helper::changeDatetimeFormat($row->assign_date) : '';
            $obj['status_by']=($row->assign_to && $row->status_by) ? $status_by->firstname.' '.$status_by->lastname : '';
            $colse_reason=($row->colse_reason && $row->status==3) ? $row->colse_reason : '';
            $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[$row->status] .'">'.(($colse_reason)? \Illuminate\Support\Str::limit($colse_reason,20) : DataCenterStatus[$row->status]).'</span>';

            $added_to_contact='';
            if($row->added_to_contact) {
                $Contact=Contact::where('id',$row->added_to_contact)->first();
                $added_to_contact='<a href="/admin/contact/view/'.$Contact->id.'">'.$Contact->firstname.' '.$Contact->lastname.'</a>';
                $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Contact</span>';
            }
            $obj['added_to_contact']=$added_to_contact;

            $added_to_property='';
            if($row->added_to_property) {
                $Property=Property::where('id',$row->added_to_property)->first();
                $added_to_property='<a href="/admin/property/view/'.$Property->id.'">'.$company->sample.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num.'</a>';
                $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Property</span>';
            }
            $obj['added_to_property']=$added_to_property;

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="">

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

    public function getDataAgent(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' ORDER BY id DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }
        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $DCA_Count = DataCenterAccess::where('admin_id', $adminAuth->id)->count();

        $where=' WHERE 1 ';

        //$where.=' AND master_project_id IS NOT NULL';

        if(request('unmatched')){
            $unmatched=request('unmatched');
            if($unmatched==1)
                $where .= ' AND project_id IS NOT NULL';
            if($unmatched==2)
                $where .= ' AND project_id IS NULL';
        }

        $firstWhere='';
        $projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNotNull('projects')->pluck('projects')->toArray();
        if ($projects) {
            $firstWhere.=" OR project_id IN (".join(',', $projects).")";
        }
        $master_projects = DataCenterAccess::where('admin_id', $adminAuth->id)->whereNull('projects')->pluck('master_project_id')->toArray();
        if ($master_projects) {
            $firstWhere.=" OR master_project_id IN (".join(',', $master_projects).")";
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a ");
        $totalRecords=$totalRecords[0]->countAll;//$TargetCount->where('period',$period)->count();

        if(request('name')){
            $where.=' AND name LIKE "%'.request('name').'%"';
        }

        if(request('phone_number')){
            $where.=' AND (a.phone_number LIKE "%'.request('phone_number').'%" OR a.phone_number_2 LIKE "%'.request('phone_number').'%")';
        }

        if(request('bedroom')){
            $where.=' AND a.bedrooms="'.request('bedroom').'"';
        }

        if(request('bua_from')){
            $where.=' AND a.size>='.request('bua_from');
        }

        if(request('bua_to')){
            $where.=' AND a.size<='.request('bua_to');
        }

        if(request('plot_from')){
            $where.=' AND a.plot_size>='.request('plot_from');
        }

        if(request('plot_to')){
            $where.=' AND a.plot_size<='.request('plot_to');
        }

        if(request('master_project_id')){
            $master_project_id=request('master_project_id');
            $where .= ' AND a.master_project_id=' . $master_project_id;
        }

        if(request('villa_unit_no')){
            $where.=' AND villa_unit_no LIKE "%'.request('villa_unit_no').'%"';
        }

        if(request('ref_id')){
            $where .= ' AND id=' . request('ref_id');
        }

        if(request('assigned_to')){
            $where.=' AND agent_assign='.request('assigned_to');
        }

        if(request('project_id')){
            $where.=' AND a.project_id IN ('.request('project_id').')';
        }

        if(request('status')){
            if(request('status')=='added_to_property') {
                $where .= ' AND added_to_property IS NOT NULL';
                //$where .= ' AND added_to_property_admin='.$adminAuth->id;
            }elseif(request('status')=='added_to_contact') {
                $where .= ' AND added_to_contact IS NOT NULL';
                //$where .= ' AND added_to_contact_admin='.$adminAuth->id;
            }else {
                $status=request('status');
                $where .= ' AND added_to_contact IS NULL AND added_to_property IS NULL AND status=' . $status;
                //if($status!=1 && $status!=4){
                //    $where .= ' AND status_by='.$adminAuth->id;
                //}
            }
        }else{
            $where .= ' AND added_to_contact IS NULL AND added_to_property IS NULL AND status IN (1,4)';
            //$where .= ' AND status_by='.$adminAuth->id;
        }

        if(request('assigned')){
            if(request('assigned')==1)
                $where.=' AND agent_assign IS NOT NULL';
            if(request('assigned')==2)
                $where.=' AND agent_assign IS NULL';
        }

        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a  ".$where.$orderBy);
            Session::put('data_center_where', $where.$orderBy);
            //Session::put('data_center_where', $where.$orderBy." , data_center.id DESC");
        }else{
            $Records=DB::select("SELECT * FROM (SELECT * FROM `data_center`  WHERE agent_assign=".$adminAuth->id.$firstWhere." ) a ".$where.$orderBy." limit ".$start.",".$rowperpage);
            Session::put('data_center_where', $where.$orderBy);
            //Session::put('data_center_where', $where.$orderBy." , data_center.id DESC ");
        }

        $obj=[];
        foreach($Records as $row){

            $master_project='';
            if($row->master_project_id){
                $MProject=MasterProject::find($row->master_project_id);
                $master_project=$MProject->name;
            }else{
                $master_project=$row->master_project;
            }

            $project='';
            if($row->project_id){
                $Community=Community::find($row->project_id);
                $project=$Community->name;
            }else{
                $project=$row->project;
            }

            $status_by=Admin::find($row->status_by);
            $assign_to=Admin::find($row->assign_to);
            $obj['id']='DC-'.$row->id;
            $obj['master_project']=$master_project;
            $obj['project']=$project;
            $obj['st_cl_fr']=$row->st_cl_fr;
            $obj['villa_unit_no']=$row->villa_unit_no;
            $obj['bedrooms']=$row->bedrooms;
            $obj['size']=($row->size) ? number_format($row->size) : '';
            $obj['plot_size']=($row->plot_size) ? number_format($row->plot_size) : '';
            $obj['name']=$row->name;
            $obj['phone_number']=$row->phone_number;
            $obj['phone_number_2']=$row->phone_number_2;
            $obj['email']=$row->email;
            $obj['nationality']=$row->nationality;
            $obj['assign_to']=($assign_to) ? $assign_to->firstname.' '.$assign_to->lastname : '';
            $obj['assign_date']=($row->assign_date) ? \Helper::changeDatetimeFormat($row->assign_date) : '';
            $obj['status_by']=($row->assign_to && $row->status_by) ? $status_by->firstname.' '.$status_by->lastname : '';
            $colse_reason=($row->colse_reason && $row->status==3) ? $row->colse_reason : '';
            $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[$row->status] .'">'.(($colse_reason)? \Illuminate\Support\Str::limit($colse_reason,20) : DataCenterStatus[$row->status]).'</span>';

            $added_to_contact='';
            if($row->added_to_contact) {
                $Contact=Contact::where('id',$row->added_to_contact)->first();
                $added_to_contact='<a target="_blank" href="/admin/contact/view/'.$Contact->id.'">'.$Contact->firstname.' '.$Contact->lastname.'</a>';
                $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Contact</span>';

            }
            $obj['added_to_contact']=$added_to_contact;

            $added_to_property='';
            if($row->added_to_property) {
                $Property=Property::where('id',$row->added_to_property)->first();
                $added_to_property='<a target="_blank" href="/admin/property/view/'.$Property->id.'">'.$company->sample.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num.'</a>';
                $obj['status']='<span class="badge w-100 badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Property</span>';

            }
            $obj['added_to_property']=$added_to_property;
            $obj['colse_reason']=($row->colse_reason) ? ((in_array($row->colse_reason, DataCenterClosedReason)) ? $row->colse_reason : '<a class="reason-view" data-target="#ViewModal" data-toggle="modal">'.$row->colse_reason.'</a>') : 'N/A';

            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="">

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

}

