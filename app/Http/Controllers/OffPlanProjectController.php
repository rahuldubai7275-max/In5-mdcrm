<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Community;
use App\Models\Company;
use App\Models\ContactNote;
use App\Models\Developer;
use App\Models\MasterProject;
use App\Models\OffPlanProject;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Image;

class OffPlanProjectController extends Controller
{
    public function OffPlanProjects(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $data=['start'=>0,'length'=>18];

        $url=env('MD_URL').'/api/get-off-plan';

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        $response=json_decode($response);
        return view('/admin/off-plan-projects-card', [
            'pageConfigs' => $pageConfigs,
            'projects' => $response->aaData,
            'items' => $response->countDate,
        ]);
    }

    public function OffPlanProjectsAjax(){

        $start=request('start');

        $data=['start'=>$start,'length'=>18];

        if(request('emirate')){
            $data['emirate']=request('emirate');
        }

        if(request('master_project')){
            $data['master_project']=request('master_project');
        }

        if(request('community')){
            $data['community']=request('community');
        }

        if(request('developer')){
            $data['developer']=request('developer');
        }

        if(request('p_type')){
            $data['p_type']=request('p_type');
        }

        if(request('property_type')){
            $data['property_type']=request('property_type');
        }

        if(request('project_name')){
            $data['project_name']=request('project_name');
        }

        if(request('status')){
            $data['status']=request('status');
        }

        if(request('from_date_launch')){
            $data['from_date_launch']=request('from_date_launch');
        }

        if(request('to_date_launch')){
            $data['to_date_launch']=request('to_date_launch');
        }

        if(request('bedroom_from')){
            $data['bedroom_from']=request('bedroom_from');
        }

        if(request('bedroom_to')){
            $data['bedroom_to']=request('bedroom_to');
        }

        if(request('phpp')){
            $data['phpp']=request('phpp');
        }

        if(request('year')){
            $data['year']=request('year');
        }

        if(request('from_price')){
            $data['from_price']=str_replace(',','',request('from_price'));
        }

        if(request('to_price')){
            $data['to_price']=str_replace(',','',request('to_price'));
        }

        $adminAuth=\Auth::guard('admin')->user();

        $company=Company::find($adminAuth->company_id);

        $url=env('MD_URL').'/api/get-off-plan';

        $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        $response=json_decode($response);

        $aaData=$response->aaData;
        $output="";
        foreach($aaData as $row){
            $output.='<div class="col-sm-4 pb-2">
                <div class="card off-plan-project h-100 text-dark mb-0">
                    <div class="position-relative">
                        <a href="/off-plan/brochure/'.\Helper::idCode($row->id). (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) .'" target="_blank"><img class="card-img-top" src="'.$row->pictures.'"></a>
                        '.($row->edit=='true'?'<a class="off-plan-btn-edit" target="_blank" href="/admin/off-plan-project-edit/'.\Helper::idCode($row->id).'" ><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>':'').'
                        '.($row->content_status=='3'?'<span class="coming-soon-tag">'.$row->content_status_name.'</span>':'').'
                        '.($row->developer?'<span class="developer-tag">'.$row->developer.'</span>':'').'
                    </div>
                    <a href="/off-plan/brochure/'.\Helper::idCode($row->id). (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) .'" target="_blank" class="card-body text-dark">
                        <h5 class="card-title text-truncate">'.$row->project_name.'</h5>
                        <p class="card-text text-truncate mb-0">'.$row->master_project_name.'</p>
                        <div class="d-block mt-1">
                            <div class="float-left text-center">
                            <span>Completion Date</span><br>
                            <span>'.$row->date_of_completion.'</span>
                            </div>
                            <div class="float-right text-center">
                            <span>Starting Price</span><br>
                            <span>AED '.$row->starting_price.'</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>';
        }
        if(request('search')) {
            return ['output'=>$output,'items' => $response->countDate];
        }else{
            return $output;
        }

    }

    public function OffPlanProjects_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/off-plan-projects-sm', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function brochure(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $adminAuth=\Auth::guard('admin')->user();

        $id=request('id');
        $idDecode= \Helper::idDecode($id);

        $response = Http::get('https://mdcrms.com/api/get-off-plan-brochure/'.$idDecode);
        $response=json_decode($response);
        $OffPlanProject=$response;//OffPlanProject::find($idDecode);

        $agent_id=\Helper::idDecode(request('a'));
        $agent=Admin::where('id',$agent_id)->first();

        if(!$adminAuth) {
            if($agent->status==2){
                return abort(404);
            }
        }

        if (!$OffPlanProject || ($agent_id && !$agent)) {
            return view('/admin/property-brochure-not-found', [
                'pageConfigs' => $pageConfigs,
                'OffPlanProject' => $OffPlanProject,
            ]);
        }else{
            return view('/admin/off-plan-project-brochure', [
                'pageConfigs' => $pageConfigs,
                'OffPlanProject' => $OffPlanProject,
            ]);
        }

    }

    public function SelectAjax()
    {
        $adminAuth=\Auth::guard('admin')->user();

        $company=Company::find($adminAuth->company_id);

        $url=env('MD_URL').'/api/off-plan-project/search';

        $data=[
            'q'=>request('q'),
            'developer'=>request('developer'),
        ];

        $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        return $response;
    }

    public function SelectAjaxInside(Request $request){
        $adminAuth=\Auth::guard('admin')->user();

        $search = request('q');

        $query = "SELECT DISTINCT * FROM off_plan_project WHERE company_id=".$adminAuth->company_id." AND project_name LIKE '%$search%' LIMIT 0,30";

        $OffPlanProject= DB::select($query);

        $json = [];
        foreach ($OffPlanProject as $row) {

            $json[] = ['id' => $row->id, 'master_project'=>$row->master_project_name, 'project_name' => $row->project_name];
        }
        return json_encode($json);
    }

    public function OffPlanProject(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/off-plan-projects",'name'=>"Developer Projects"], ['name'=>"Add"]
        ];
        $route="off-plan-project.add";
        $offPlanProject='';
        return view('/admin/off-plan-project', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'offPlanProject'=>$offPlanProject,
            'route'=>$route
        ]);
    }

    public function details(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/off-plan-projects",'name'=>"Developer Projects"], ['name'=>"Edit"]
        ];

        $id=request('id');
        $idDecode= \Helper::idDecode($id);

        $url=env('MD_URL').'/api/off-plan-project/detail';
        $token=env('MD_TOKEN');

        $data=['id'=>$idDecode];
        $response=Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        $offPlanProject=json_decode($response);
        $route="off-plan-project.edit";

        if($offPlanProject){
            return view('/admin/off-plan-project', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'route'=>$route,
                'offPlanProject' => $offPlanProject,
            ]);
        }else{
            return redirect('/admin/off-plan-projects');
        }

    }

    /*public function Store(Request $request){
        $request->validate([
            'Developer'=>'required',
        ]);
        //$pictures=null;
        //if ((request('InputAttachFile'))){
        //    foreach (request('InputAttachFile') as $image) {
        //        $pictures .= $image . ',';
        //    }
        //    $pictures=rtrim($pictures,',');
        //}

        $pictures=null;
        if ((request('InputAttachFile'))){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                $this->resizeImage($image);

                if( request( current( explode('.',$image) ) ) ){
                    $this->watermark($image);
                }

            }
            $pictures=rtrim($pictures,',');
        }
        OffPlanProject::create([
            'master_project_id'=>request('MasterProject'),
            'community_id'=>request('Community'),
            'developer_id'=>request('Developer'),
            'type'=>request('Type'),
            'property_type_id'=>request('PropertyType'),
            'title'=>request('title'),
            'description'=>request('description'),
            'pictures'=>$pictures,
            'video_link'=>request('video_link'),
            'file'=>request('file'),
            'project_number'=>request('project_number'),
            'date_of_launch'=>request('date_of_launch'),
            'date_of_completion'=>request('date_of_completion'),
            'lng'=>request('lng'),
            'lat'=>request('lat'),
            'status'=>request('status')
        ]);
        return redirect('/admin/off-plan-projects');
    }*/

    public function Store(Request $request){
        $request->validate([
            'Developer'=>'required',
        ]);

        $MasterProject = MasterProject::find(request('MasterProject'));
        $community = Community::find(request('Community'));

        $starting_price=(request('starting_price')) ? str_replace(',','',request('starting_price')) : 0;

        $pictures=null;
        if ((request('InputAttachFile'))){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                //$this->resizeImage($image);

                //if( request( current( explode('.',$image) ) ) ){
                //    $this->watermark($image);
                //}

            }
            $pictures=rtrim($pictures,',');
        }

        $url=env('MD_URL').'/api/off-plan-project/store';
        $token=env('MD_TOKEN');
        $data=[
            'emirate_id'=>request('Emirate'),
            'master_project_id'=>request('MasterProject'),
            'community_id'=>request('Community'),
            'developer_id'=>request('Developer'),
            'type'=>request('Type'),
            'property_type_id'=>request('PropertyType'),
            'title'=>request('title'),
            'description'=>request('description'),
            'pictures'=>$pictures,
            'video_link'=>request('video_link'),
            'file'=>request('file'),
            'project_number'=>request('project_number'),
            'date_of_launch'=>request('date_of_launch'),
            'date_of_completion'=>request('date_of_completion'),
            'map'=>request('map'),
            'status'=>request('status'),
            'master_project_name'=>$MasterProject->name,
            'project_name'=>request('project_name'),
            'starting_price'=>$starting_price,
            'phpp'=>request('phpp'),
            'bedroom_from'=>request('bedroom_from'),
            'bedroom_to'=>request('bedroom_to'),
            'size_from'=>(request('size_from')) ? str_replace(',','',request('size_from')) : 0,
            'size_to'=>(request('size_to')) ? str_replace(',','',request('size_to')) : 0,
            'payment_plan'=>request('payment_plan'),
            'quarter'=>request('quarter'),
            'year'=>request('year'),
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('/admin/off-plan-projects');
    }

    public function insertOffPlan(Request $request){
        $request->validate([
            'Developer'=>'required',
        ]);
        OffPlanProject::create([
            'md_crm_id'=>request('md_crm_id'),
            'project_number'=>request('project_number'),
            'status'=>request('status')
        ]);
    }

    public function Edit(Request $request){
        $request->validate([
            'Developer'=>'required',
        ]);

        $MasterProject = MasterProject::find(request('MasterProject'));
        $community = Community::find(request('Community'));

        $starting_price=(request('starting_price')) ? str_replace(',','',request('starting_price')) : 0;

        $pictures=null;
        if ((request('InputAttachFile'))){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                //$this->resizeImage($image);

                //if( request( current( explode('.',$image) ) ) ){
                //    $this->watermark($image);
                //}

            }
            $pictures=rtrim($pictures,',');
        }

        $url=env('MD_URL').'/api/off-plan-project/edit';
        $token=env('MD_TOKEN');
        $data=[
            '_id'=>request('_id'),
            'emirate_id'=>request('Emirate'),
            'master_project_id'=>request('MasterProject'),
            'community_id'=>request('Community'),
            'developer_id'=>request('Developer'),
            'type'=>request('Type'),
            'property_type_id'=>request('PropertyType'),
            'title'=>request('title'),
            'description'=>request('description'),
            'pictures'=>$pictures,
            'video_link'=>request('video_link'),
            'file'=>request('file'),
            'project_number'=>request('project_number'),
            'date_of_launch'=>request('date_of_launch'),
            'date_of_completion'=>request('date_of_completion'),
            'map'=>request('map'),
            'status'=>request('status'),
            'master_project_name'=>$MasterProject->name,
            'project_name'=>request('project_name'),
            'starting_price'=>$starting_price,
            'phpp'=>request('phpp'),
            'bedroom_from'=>request('bedroom_from'),
            'bedroom_to'=>request('bedroom_to'),
            'size_from'=>(request('size_from')) ? str_replace(',','',request('size_from')) : 0,
            'size_to'=>(request('size_to')) ? str_replace(',','',request('size_to')) : 0,
            'payment_plan'=>request('payment_plan'),
            'quarter'=>request('quarter'),
            'year'=>request('year'),
        ];

        Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);

        return redirect('/admin/off-plan-projects');
    }

    public function getOffPlanProjects(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $where='';

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `off_plan_project` WHERE 1");
        $totalRecords=$totalRecords[0]->countAll;

        if($searchValue)
            $where.=' AND ( title  LIKE "%'.$searchValue.'%" OR description LIKE "%'.$searchValue.'%")';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('type') )
            $where.=' AND type ='.request('type');

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM off_plan_project WHERE 1 ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM off_plan_project WHERE 1 ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT * FROM off_plan_project WHERE 1 ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];

        foreach($Records as $row){

            $Developer=Developer::find($row->developer_id);
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $PropertyType=PropertyType::find($row->property_type_id);

            $obj['master_project_id']=($MasterProject) ? $MasterProject->name : '';
            $obj['community_id']=($Community) ? $Community->name : '';
            $obj['developer_id']=($Developer) ? $Developer->name : '';
            $obj['property_type_id']=($PropertyType) ? $PropertyType->name : '';
            $obj['title']=$row->title;
            $obj['status']=OffPlanProjectStatus[$row->status];
            $obj['date_of_launch']=($row->date_of_launch) ? date('d-m-Y',strtotime($row->date_of_launch)):'';
            $obj['date_of_completion']=($row->date_of_completion) ? date('d-m-Y',strtotime($row->date_of_completion)):'';
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);

            $action ='';
            if($adminAuth->type<3) {
                $action = '
                    <a target="_blank" href="/admin/off-plan-project-edit/' . $row->id . '"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                    <a href="javascript:void(0)" class="delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
                ';
            }

            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="'.route('off-plan-project.delete').'" data-brochure="'.\Helper::idCode($row->id).'">
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

    public function getOffPlanProjects_sm(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = 3;//request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        //$searchValue = '';//request('search')['value']; // Search value

        $where='';

        $orderBy=' ORDER BY created_at DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `off_plan_project` WHERE 1");
        $totalRecords=$totalRecords[0]->countAll;

        //if($searchValue)
        //    $where.=' AND ( title  LIKE "%'.$searchValue.'%" OR description LIKE "%'.$searchValue.'%")';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('type') )
            $where.=' AND type ='.request('type');

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM off_plan_project WHERE 1 ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = '';

        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM off_plan_project WHERE 1 ".$where.$orderBy);
        }else{
            $Records=DB::select("SELECT * FROM off_plan_project WHERE 1 ".$where.$orderBy." limit ".$start.",".$rowperpage);
        }

        $obj=[];

        foreach($Records as $row){

            $Developer=Developer::find($row->developer_id);
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $PropertyType=PropertyType::find($row->property_type_id);

            $pictures=explode(',', $row->pictures);

            $img = '<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/images/Default.png"></div>';
            if($row->pictures)
                $img='<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/storage/'.$pictures[0].'"></div>';

            $data.='<div class="card mb-2 hold-box" data-id="'.$row->id.'" data-brochure="'.\Helper::idCode($row->id).'">
                    <div class="card-body p-1">
                        <div class="d-flex">
                            <div>
                            '.$img.'
                            </div>
                            <div class="pl-1">
                                <p class="m-0">'.(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' | '.$Community->name : '').'</p>
                                <p class="m-0">'.(($Developer) ? $Developer->name : '').'</p>
                                <p class="m-0">'.OffPlanProjectStatus[$row->status].'</p>
                            </div>
                        </div>
                    </div>
                   </div>';

            $obj['master_project_id']=($MasterProject) ? $MasterProject->name : '';
            $obj['community_id']=($Community) ? $Community->name : '';
            $obj['developer_id']=($Developer) ? $Developer->name : '';
            $obj['property_type_id']=($PropertyType) ? $PropertyType->name : '';
            $obj['title']=$row->title;
            $obj['status']=OffPlanProjectStatus[$row->status];
            $obj['date_of_launch']=($row->date_of_launch) ? date('d-m-Y',strtotime($row->date_of_launch)):'';
            $obj['date_of_completion']=($row->date_of_completion) ? date('d-m-Y',strtotime($row->date_of_completion)):'';
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);

            $action ='';
            if($adminAuth->type<3) {
                $action = '
                    <a target="_blank" href="/admin/off-plan-project-edit/' . $row->id . '"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                    <a href="javascript:void(0)" class="delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
                ';
            }

            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="'.route('off-plan-project.delete').'" data-brochure="'.\Helper::idCode($row->id).'">
                            '.$action.'
                          </div>';

            //$data[] = $obj;
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

    public function resizeImage($image){
        $path = storage_path('app/public/images/' . $image);

        // open an image file
        $img = Image::make($path);

        // now you are able to resize the instance
        $img->resize(800, 600);
        //$img->resize(1312, 894);

        // finally we save the image as a new file
        $img->save(storage_path('app/public/images/' . $image));
    }

    public function watermark($image){
        $watermarkImage='images/watermark-logo.png';
        $path = storage_path('app/public/images/' . $image);

        // open an image file
        $img = Image::make($path);

        // and insert a watermark for example
        $img->insert($watermarkImage,'center');

        // finally we save the image as a new file
        $img->save(storage_path('app/public/images/' . $image));
    }

    public function pictureEdit(Request $request){
        $OffPlanProject = OffPlanProject::find($request->id);

        $pictures=str_replace($request->image_name.',','',$OffPlanProject->pictures);
        $pictures=str_replace($request->image_name,'',$pictures);
        $pictures=rtrim($pictures,',');
        $OffPlanProject->pictures=$pictures;
        $OffPlanProject->save();
    }

    public function Delete(){
        $OffPlanProject = OffPlanProject::find( request('Delete') );
        $countContactNote=ContactNote::where('off_plan_project_id',$OffPlanProject->id)->count();
        if($countContactNote>0) {
            Session::flash('error', 'The project can not be deleted, some emails has been sent to the contacts from this project.');
            return redirect('/admin/off-plan-projects');
        }
        $OffPlanProject->delete();
        return redirect('/admin/off-plan-projects');
    }

    public function mdcrmsMP(){

        $url=env('MD_URL').'/api/master-projects';

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $data=['emirate_id'=>request('Emirate')];
        $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

        $MasterProjects=json_decode($response);

        $output='<option value="">select</option>';
        foreach ($MasterProjects as $row){
            $output.='<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo  $output;
    }

}
