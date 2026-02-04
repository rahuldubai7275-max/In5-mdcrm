<?php

namespace App\Http\Controllers;

use App\Exports\ExportProperty;
use App\Mail\SendMail;
use App\Models\CommunityParent;
use App\Models\ContactNote;
use App\Models\DataCenter;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\PortalProperty;
use App\Models\Survey;
use App\Models\View;
use App\Models\Bathroom;
use App\Models\Bedroom;
use App\Models\ClusterStreet;
use App\Models\Community;
use App\Models\Contact;
use App\Models\ContactSource;
use App\Models\Emirate;
use App\Models\Features;
use App\Models\PropertyFeature;
use App\Models\MasterProject;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\PropertyType;
use App\Models\VaastuOrientation;
use App\Models\VendorMotivation;
use App\Models\VillaType;
use App\Models\Admin;
use App\Models\Notification;
use App\Models\History;
use App\Models\PropertyStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Image;
use Maatwebsite\Excel\Facades\Excel;

class PropertiesController_old extends Controller
{
    public function Properties(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
        $PropertyTypes=PropertyType::get();
        $MasterProjects=MasterProject::get();
        $Communitys=Community::get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();

        return view('/admin/properties', [
            'pageConfigs' => $pageConfigs,
            'PropertyTypes'=>$PropertyTypes,
            'ClientManagers'=>$ClientManagers,
            'MasterProjects'=>$MasterProjects,
            'Communitys'=>$Communitys,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
        ]);
    }

    public function Properties_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
        $PropertyTypes=PropertyType::get();
        $MasterProjects=MasterProject::get();
        $Communitys=Community::get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();

        return view('/admin/properties-sm', [
            'pageConfigs' => $pageConfigs,
            'PropertyTypes'=>$PropertyTypes,
            'ClientManagers'=>$ClientManagers,
            'MasterProjects'=>$MasterProjects,
            'Communitys'=>$Communitys,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
        ]);
    }

    public function GetProperties(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' ORDER BY property.id DESC';
        if($columnIndex){
            $orderBy=" ORDER BY property.".$columnName." ".$columnSortOrder;
        }

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `property` WHERE 1");
        $totalRecords=$totalRecords[0]->countAll;

        $adminAuth=\Auth::guard('admin')->user();

        // $dataWhere=[];
        $addTable='';
        $where=' WHERE 1 ';

        if(request('property')=='new_listing'){
            $d30before= date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") );
            $where=',property_status_history WHERE property.id=property_status_history.property_id AND property_status_history.status=1 AND property_status_history.created_at>="'.$d30before.'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='rfl'){
            $where.=' AND property.status=11';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='unlisted'){
            $where.=' AND property.status=2';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='listing'){
            $where.=' AND property.status=1';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='ma'){
            $where.=' AND property.status=4';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='30-15'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 15 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 30 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='15-7'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 7 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 15 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='7-0'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.$today.'"  AND "'.date('Y-m-d',strtotime($today. "+ 7 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='RL'){
            $where.=' AND property.status=11';
        }

        if($searchValue)
            $where.=' AND (title LIKE "%'.$searchValue.'%" OR description LIKE "%'.$searchValue.'%")';

        $rowperpage=0;
        if( request('status') || request('property_type') || request('client_manager') || request('master_project') || request('community') || request('bedrooms') || request('id')
            || request('bathrooms') || request('rent_price') || request('from_date') || request('to_date') || request('status2') || request('unit_villa_number') || request('from_price') || request('to_price') || request('listing') || request('property')!='')
            $rowperpage = request('length');

        $adminAuth=\Auth::guard('admin')->user();

        if( request('listing') )
            $where.=' AND listing_type_id="'.request('listing').'"';

        if( request('status') ){
            $listing_status=request('status');
            if (str_contains($listing_status, 'pf_error,')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
            }
            if (str_contains($listing_status, ',pf_error')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
            }

            if (str_contains($listing_status, 'pf_error')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
            }

            if($listing_status)
                $where.=' AND status IN ('.$listing_status.')';
        }

        if( request('type') )
            $where.=' AND property.type = '.request('type');

        if( request('property_type') )
            $where.=' AND property_type_id IN ('.request('property_type').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('client_manager') )
            $where.=' AND client_manager_id IN ('.request('client_manager').')';

        if( request('client_manager_2') )
            $where.=' AND client_manager2_id IN ('.request('client_manager_2').')';

        if( request('emirate') )
            $where.=' AND emirate_id='.request('emirate');

        if( request('master_project') )
            $where.=' AND master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND community_id IN ('.request('community').')';

        if( request('bedrooms') )
            $where.=' AND bedroom_id IN ('.request('bedrooms').')';

        if( request('bathrooms') )
            $where.=' AND bathroom_id IN ('.request('bathrooms').')';

        if( request('status2') )
            $where.=' AND status2 IN ('.request('status2').')';

        if( request('off_plan') )
            $where.=' AND off_plan="'.request('off_plan').'"';

        if( request('unit_villa_number') )
            $where.=' AND villa_number="'.request('unit_villa_number').'"';

        if( request('property_management') )
            $where.=' AND property_management="'.request('property_management').'"';

        if( request('rera_permit') )
            $where.=' AND rera_permit="'.request('rera_permit').'"';

        if(request('rent_price')) {
            $rent_price_field = request('rent_price');
            $where.=' AND '.$rent_price_field.' IS NOT NULL ';
            $where.=' AND '.$rent_price_field.' >0 ';
        }

        if(request('listing')==1 && $columnName=='expected_price'){
            if($columnName=='expected_price'){
                $columnName='expected_price';
            }
        }

        if(request('listing')==2 && $columnName=='expected_price'){
            if(request('rent_price')){
                $columnName=request('rent_price');
            }else{
                $columnName='yearly';
            }
        }
        if(request('listing')!=2){
            if( request('from_price') )
                $where.=' AND expected_price >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND expected_price <='.str_replace(',','',request('to_price'));
        }else{
            if(request('rent_price')){
                $rent_price_field=request('rent_price');
            }else{
                $rent_price_field='yearly';
            }

            if( request('from_price') )
                $where.=' AND '.$rent_price_field.' >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND '.$rent_price_field.' <='.str_replace(',','',request('to_price'));
        }

        if( request('from_date') )
            $where.=' AND property.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND property.created_at <="'.request('to_date').' 23:59:59"';

        if( request('id') )
            $where.=' AND property.id ='.request('id');

        if( request('portal') ){
            $addTable.=' ,portal_property';
            $where.=' AND property.id=portal_property.property_id AND portal_property.portal_id ='.request('portal');
        }


        if($where==' WHERE 1 '  && request('property')=='properties') {
            // $where .= ' AND status IN (1,2)';
            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id .')';
        }

        if( request('select_color') ){
            $today = date('Y-m-d');

            $activity_contact_setting_2=\App\Models\Setting::where('id',8)->first();
            $activity_contact_setting_3=\App\Models\Setting::where('id',9)->first();

            $color=request('select_color');
            if($color=="Red"){
                $where.= ' AND last_activity <="'.date('Y-m-d',strtotime($today. "- $activity_contact_setting_3->time days") ).'"';
            }

            if($color=="Yellow"){
                $where.= ' AND last_activity BETWEEN "'.date('Y-m-d',strtotime($today. "- $activity_contact_setting_3->time days") ).'" AND "'.date('Y-m-d',strtotime($today. "- 15 days") ).'"';
            }

            if($color=="Green"){
                $where.= ' AND last_activity >="'.date('Y-m-d',strtotime($today. "- ".$activity_contact_setting_2->time." days") ).'"';
            }
        }

        $where=$addTable.$where;

        $totalRecordwithFilter=0;
        //if($where!=' WHERE 1 ') {
            $totalRecordwithFilter = DB::select("SELECT COUNT(*) as countAll FROM `property` " . $where);
            $totalRecordwithFilter = $totalRecordwithFilter[0]->countAll;
        //}

        $data = array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where.$orderBy." , id DESC");

            Session::put('property_where', $where.$orderBy." , property.id DESC");
        }else{
            Session::put('property_where', $where.$orderBy." , property.id DESC ");
            $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where.$orderBy." , property.id DESC limit ".$start.",".$rowperpage);
        }

        $ma_setting=\App\Models\Setting::where('id',2)->first();

        $obj=[];
        foreach($Records as $row){
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $ClusterStreet=ClusterStreet::find($row->cluster_street_id);
            $VillaType=VillaType::find($row->villa_type_id);
            $Bedroom=Bedroom::find($row->bedroom_id);
            $ClientManager=Admin::find( $row->client_manager_id );
            $ClientManager2=Admin::find( $row->client_manager2_id );
            $creator=Admin::find( $row->admin_id );
            if($creator->status==2)
                $creator=Admin::where( 'type',1 )->first();
            $Note=PropertyNote::where('property_id','=',$row->id)->latest('created_at', 'desc')->first();

            $status='';
            if($row->status==1)
                $status='<span class="badge badge-success" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="Listed"></span>';// Listed

            if($row->status==2)
                $status='<span class="badge badge-yellow" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="'.Status[2].'"></span>';// Unlisted

            if($row->status==4)
                $status='<span class="badge badge-danger" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="MA"></span>';// MA

            if($row->status==11) {
                $PropertyStatusHistory = PropertyStatusHistory::where('property_id',$row->id)->orderBy('id','DESC')->first();
                if($PropertyStatusHistory->rfl_status=='0')
                    $status = '<span class="badge badge-primary" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="Request For Listing"></span>';// Request For Listing
                else
                    $status = '<span class="badge badge-warning" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="Request For Listing (Rejected)"></span>';
            }

            $obj['checkbox']=($adminAuth->type<=2 || $row->client_manager_id==$adminAuth->id) ? '<div class="d-inline-block checkbox">
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" value="'.$row->id.'" name="property[]">
                                        </label>
                                    </fieldset>
                                </div>' : '';

            $pictures=explode(',', $row->pictures);
            $img='';
            if($row->pictures)
                $img='<div style="display: block;width: 50px"><img style="max-width: 50px;" src="/storage/'.$pictures[0].'"></div>';

            $expected_price=($row->expected_price) ? number_format($row->expected_price) : '';

            if($row->listing_type_id==2){

                $rp_count='0';
                if($row->yearly){
                    $rent_price=$row->yearly;
                }else if($row->monthly){
                    $rent_price=$row->monthly;
                }else if($row->weekly){
                    $rent_price=$row->weekly;
                }else{
                    $rent_price=$row->daily;
                }

                if($row->yearly){
                    $rp_count++;
                }
                if($row->monthly){
                    $rp_count++;
                }
                if($row->weekly){
                    $rp_count++;
                }
                if($row->daily){
                    $rp_count++;
                }

                if($rp_count>1) {
                    $expected_price = '<a class="rent-price" href="#rentPriceModal" data-toggle="modal"
                    data-daily="' . (($row->daily) ? number_format($row->daily) : '') . '"
                    data-weekly="' . (($row->weekly) ? number_format($row->weekly) : '') . '"
                    data-monthly="' . (($row->monthly) ? number_format($row->monthly) : '') . '"
                    data-yearly="' . (($row->yearly) ? number_format($row->yearly) : '') . '"
                    >' . number_format($rent_price) . '</a>';
                }else{
                    $expected_price=number_format($rent_price);
                }
            }

            $editAction='';
            $copeAction='<a href="javascript:void(0);" class="copy-property" title="Copy"><i class="users-edit-icon feather icon-copy mr-50"></i></a>';
            $historyAction='<a href="javascript:void(0);" class="show-history" title="History" data-toggle="modal" data-target="#historyModal"><i class="users-edit-icon feather icon-calendar mr-50"></i></a>';

//            if( ($adminAuth->type>2 || $row->status==1) ||
//                ($adminAuth->id!=$row->client_manager_id && $adminAuth->id!=$row->client_manager2_id) ) {
//                $editAction = '';
//            }

            if($adminAuth->type>2) {
                if (
                    ($adminAuth->id == $row->client_manager_id || $adminAuth->id == $row->client_manager2_id)
                    && $row->status!=1

                ) {
                    $editAction='<a href="/admin/property-edit/'.$row->id.'" target="_blank" class="edit-field-study" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>';
                }
            }else{
                $editAction='<a href="/admin/property-edit/'.$row->id.'" target="_blank" class="edit-field-study" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>';
            }

            if( $adminAuth->type>2 && ($adminAuth->id!=$row->client_manager_id && $adminAuth->id!=$row->client_manager2_id) ) {
                $historyAction='';
                $copeAction='';
            }

            $cluster_street='';
            $villa_number='';
            if($ma_setting->status==1 && ($row->status==2 || $row->status==3 || $row->status==4) && $adminAuth->type==4 &&
                !($row->client_manager_id == $adminAuth->id || $row->client_manager2_id == $adminAuth->id)){
                //$ma_setting_admin=\App\Models\SettingAdmin::where('setting_id',$ma_setting->id)->where('admin_id',$row->client_manager_id)->first();
                $ma_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$row->client_manager_id,$row->client_manager2_id])->where('setting_id',$ma_setting->id)->first();
                if(!$ma_setting_admin) {

                    $last_activity=$row->created_at;

                    $propertyNoteLast=PropertyNote::whereIn('admin_id', [$row->client_manager_id,$row->client_manager2_id])->where('property_id',$row->id)->orderBy('id','DESC')->first();
                    if($propertyNoteLast){//$row->last_activity
                        $last_activity=$propertyNoteLast->created_at;//$row->last_activity;
                    }
                    $today = \Carbon\Carbon::now();
                    $today = $today->format('Y/n/j H:i:s');
                    $date_two = \Carbon\Carbon::parse($last_activity);
                    $minutes = $date_two->diffInMinutes($today);
                    $hours = $date_two->diffInHours($today);
                    $days = $date_two->diffInDays($today);

                    if($ma_setting->time_type==1 && $minutes >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }

                    if($ma_setting->time_type==2 && $hours >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }

                    if($ma_setting->time_type==3 && $days >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }
                }
            }else {
                if ($adminAuth->type != 4 || $row->client_manager_id == $adminAuth->id || $row->client_manager2_id == $adminAuth->id) {
                    $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                    $villa_number = $row->villa_number;
                }
            }
            $obj['id']=SAMPLE.'-'.(($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->id;
            $obj['img']=$img;
            $obj['status']=$status;
            $obj['status2']=($row->status2) ? Status2[$row->status2] : 'N/A';
            $obj['master_project_id']=($MasterProject) ? $MasterProject->name : 'N/A';
            $obj['community_id']=($Community) ? $Community->name : 'N/A';
            $obj['cluster_street_id']=$cluster_street;
            $obj['villa_type_id']=(($VillaType) ? $VillaType->name : 'N/A');
            $obj['villa_number']=$villa_number;
            $obj['bedroom_id']=($Bedroom) ? $Bedroom->name : 'N/A';
            $obj['expected_price']=$expected_price;
            $obj['client_manager_id']=(($row->client_manager_id == $row->admin_id) ? '' : '<i class="fa fa-exchange text-primary mr-1" title="'.$creator->firstname.' '.$creator->lastname.'"></i>').(($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '');
            $obj['client_manager2_id']=($ClientManager2) ? $ClientManager2->firstname.' '.$ClientManager2->lastname : 'N/A';
            $obj['expiration_date']=($row->expiration_date) ? date('d-m-Y',strtotime($row->expiration_date)) : 'N/A';
            $obj['last_activity']=($Note) ? date('d-m-Y',strtotime($Note->created_at)) : 'N/A';
            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);
            $obj['Action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("property.delete").'" data-copy-action="'.route("property.copy").'">
                            '.$editAction.$historyAction.$copeAction.'
                            '.(($adminAuth->type==1) ? '<a href="javascript:void(0)" class="ajax-delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '' ).'
                          </div>';
            //'.(($adminAuth->type==1) ? '<a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '' ).'
            $obj['action_view']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" ></div>';
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

    public function GetProperties_sm(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = 3;//request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        //$searchValue = request('search')['value']; // Search value

        $orderBy=' ORDER BY property.id DESC';
        if($columnIndex){
            $orderBy=" ORDER BY property.".$columnName." ".$columnSortOrder;
        }

        $PropertyCount= new Property();
        $PropertyData=new Property();

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `property` WHERE 1");
        $totalRecords=$totalRecords[0]->countAll;

        $adminAuth=\Auth::guard('admin')->user();

        // $dataWhere=[];
        $addTable='';
        $where=' WHERE 1 ';

        if(request('property')=='new_listing'){
            $d30before= date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") );
            $where=',property_status_history WHERE property.id=property_status_history.property_id AND property_status_history.status=1 AND property_status_history.created_at>="'.$d30before.'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='rfl'){
            $where.=' AND property.status=11';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='unlisted'){
            $where.=' AND property.status=2';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='listing'){
            $where.=' AND property.status=1';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='ma'){
            $where.=' AND property.status=4';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='30-15'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 15 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 30 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='15-7'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.date('Y-m-d',strtotime($today. "+ 7 days")).'"  AND "'.date('Y-m-d',strtotime($today. "+ 15 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        $today = date('Y-m-d');
        if(request('property')=='7-0'){
            $where.=' AND property.status=1 AND property.expiration_date BETWEEN "'.$today.'"  AND "'.date('Y-m-d',strtotime($today. "+ 7 days")).'" ';

            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id.' )';
        }

        if(request('property')=='RL'){
            $where.=' AND property.status=11';
        }

        $adminAuth=\Auth::guard('admin')->user();

        if( request('listing') )
            $where.=' AND listing_type_id="'.request('listing').'"';

        if( request('status') )
            $where.=' AND status IN ('.request('status').')';

        if( request('type') )
            $where.=' AND property.type = '.request('type');

        if( request('property_type') )
            $where.=' AND property_type_id IN ('.request('property_type').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('client_manager') )
            $where.=' AND client_manager_id IN ('.request('client_manager').')';

        if( request('client_manager_2') )
            $where.=' AND client_manager2_id IN ('.request('client_manager_2').')';

        if( request('master_project') )
            $where.=' AND master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND community_id IN ('.request('community').')';

        if( request('bedrooms') )
            $where.=' AND bedroom_id IN ('.request('bedrooms').')';

        if( request('bathrooms') )
            $where.=' AND bathroom_id IN ('.request('bathrooms').')';

        if( request('status2') )
            $where.=' AND status2 IN ('.request('status2').')';

        if( request('off_plan') )
            $where.=' AND off_plan="'.request('off_plan').'"';

        if( request('unit_villa_number') )
            $where.=' AND villa_number="'.request('unit_villa_number').'"';

        if( request('property_management') )
            $where.=' AND property_management="'.request('property_management').'"';

        if( request('rera_permit') )
            $where.=' AND rera_permit="'.request('rera_permit').'"';

        if(request('rent_price')) {
            $rent_price_field = request('rent_price');
            $where.=' AND '.$rent_price_field.' IS NOT NULL ';
            $where.=' AND '.$rent_price_field.' >0 ';
        }

        if(request('listing')==1 && $columnName=='expected_price'){
            if($columnName=='expected_price'){
                $columnName='expected_price';
            }
        }

        if(request('listing')==2 && $columnName=='expected_price'){
            if(request('rent_price')){
                $columnName=request('rent_price');
            }else{
                $columnName='yearly';
            }
        }
        if(request('listing')!=2){
            if( request('from_price') )
                $where.=' AND expected_price >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND expected_price <='.str_replace(',','',request('to_price'));
        }else{
            if(request('rent_price')){
                $rent_price_field=request('rent_price');
            }else{
                $rent_price_field='yearly';
            }

            if( request('from_price') )
                $where.=' AND '.$rent_price_field.' >='.str_replace(',','',request('from_price'));

            if( request('to_price') )
                $where.=' AND '.$rent_price_field.' <='.str_replace(',','',request('to_price'));
        }

        if( request('from_date') )
            $where.=' AND property.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND property.created_at <="'.request('to_date').' 23:59:59"';

        if( request('id') )
            $where.=' AND property.id ='.request('id');

        if( request('portal') ){
            $addTable.=' ,portal_property';
            $where.=' AND property.id=portal_property.property_id AND portal_property.portal_id ='.request('portal');
        }


        if($where==' WHERE 1 '  && request('property')=='properties') {
            //$where .= ' AND status IN (1,2)';
            if($adminAuth->type>2)
                $where.=' AND (client_manager_id='.$adminAuth->id.' OR client_manager2_id='.$adminAuth->id .')';
        }

        if( request('select_color') ){
            $today = date('Y-m-d');

            $activity_contact_setting_2=\App\Models\Setting::where('id',8)->first();
            $activity_contact_setting_3=\App\Models\Setting::where('id',9)->first();

            $color=request('select_color');
            if($color=="Red"){
                $where.= ' AND last_activity <="'.date('Y-m-d',strtotime($today. "- $activity_contact_setting_3->time days") ).'"';
            }

            if($color=="Yellow"){
                $where.= ' AND last_activity BETWEEN "'.date('Y-m-d',strtotime($today. "- $activity_contact_setting_3->time days") ).'" AND "'.date('Y-m-d',strtotime($today. "- 15 days") ).'"';
            }

            if($color=="Green"){
                $where.= ' AND last_activity >="'.date('Y-m-d',strtotime($today. "- ".$activity_contact_setting_2->time." days") ).'"';
            }
        }

        $where=$addTable.$where;

        $totalRecordwithFilter=0;
        //if($where!=' WHERE 1 ') {
            $totalRecordwithFilter = DB::select("SELECT COUNT(*) as countAll FROM `property` " . $where);
            $totalRecordwithFilter = $totalRecordwithFilter[0]->countAll;
        //}

        $data = '';//array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where.$orderBy." , id DESC");

            Session::put('property_where', $where.$orderBy." , property.id DESC");
        }else{
            Session::put('property_where', $where.$orderBy." , property.id DESC ");
            $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where.$orderBy." , property.id DESC limit ".$start.",".$rowperpage);
        }

        $ma_setting=\App\Models\Setting::where('id',2)->first();

        $obj=[];
        foreach($Records as $row){
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $ClusterStreet=ClusterStreet::find($row->cluster_street_id);
            $ClientManager=Admin::find( $row->client_manager_id );

            $status='';
            if($row->status==1)
                $status='<span class="badge badge-success mr-1" style="border-radius:50%;display: inline-block;width: 15px;height: 15px;" title="Listed"></span>';// Listed

            if($row->status==2)
                $status='<span class="badge badge-yellow mr-1" style="border-radius:50%;display: inline-block;width: 15px;height: 15px;" title="'.Status[2].'"></span>';// Unlisted

            if($row->status==4)
                $status='<span class="badge badge-danger mr-1" style="border-radius:50%;display: inline-block;width: 15px;height: 15px;" title="MA"></span>';// MA

            if($row->status==11) {
                $PropertyStatusHistory = PropertyStatusHistory::where('property_id',$row->id)->orderBy('id','DESC')->first();
                if($PropertyStatusHistory->rfl_status=='0')
                    $status = '<span class="badge badge-primary mr-1" style="border-radius:50%;display: inline-block;width: 15px;height: 15px;" title="Request For Listing"></span>';// Request For Listing
                else
                    $status = '<span class="badge badge-warning mr-1" style="border-radius:50%;display: inline-block;width: 15px;height: 15px;" title="Request For Listing (Rejected)"></span>';
            }

            $checkbox='';
            if($adminAuth->type<=2 || ($row->client_manager_id==$adminAuth->id || $row->client_manager2_id==$adminAuth->id))
                 $checkbox='<input type="checkbox" class="d-none" value="'.$row->id.'" name="property[]">';

            $pictures=explode(',', $row->pictures);

            $img = '<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/images/Default.png"></div>';
            if($row->pictures)
                $img='<div style="width: 100px" class="d-flex h-100 align-items-center"><img style="height: auto;max-width: 100%" src="/storage/'.$pictures[0].'"></div>';

            $cluster_street='';
            $villa_number='';
            if($ma_setting->status==1 && ($row->status==2 || $row->status==3 || $row->status==4) && $adminAuth->type==4 &&
                !($row->client_manager_id == $adminAuth->id || $row->client_manager2_id == $adminAuth->id)){
                //$ma_setting_admin=\App\Models\SettingAdmin::where('setting_id',$ma_setting->id)->where('admin_id',$row->client_manager_id)->first();
                $ma_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$row->client_manager_id,$row->client_manager2_id])->where('setting_id',$ma_setting->id)->first();
                if(!$ma_setting_admin) {

                    $last_activity=$row->created_at;

                    $propertyNoteLast=PropertyNote::whereIn('admin_id', [$row->client_manager_id,$row->client_manager2_id])->where('property_id',$row->id)->orderBy('id','DESC')->first();
                    if($propertyNoteLast){//$row->last_activity
                        $last_activity=$propertyNoteLast->created_at;//$row->last_activity;
                    }
                    $today = \Carbon\Carbon::now();
                    $today = $today->format('Y/n/j H:i:s');
                    $date_two = \Carbon\Carbon::parse($last_activity);
                    $minutes = $date_two->diffInMinutes($today);
                    $hours = $date_two->diffInHours($today);
                    $days = $date_two->diffInDays($today);

                    if($ma_setting->time_type==1 && $minutes >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }

                    if($ma_setting->time_type==2 && $hours >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }

                    if($ma_setting->time_type==3 && $days >= $ma_setting->time){
                        $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                        $villa_number = $row->villa_number;
                    }
                }
            }else {
                if ($adminAuth->type != 4 || $row->client_manager_id == $adminAuth->id || $row->client_manager2_id == $adminAuth->id) {
                    $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : 'N/A';
                    $villa_number = $row->villa_number;
                }
            }

            $data.='<div class="card mb-2 hold-box" data-id="'.$row->id.'">
                    '.$checkbox.'
                    <div class="card-body p-1">
                        <div class="d-flex">
                            <div>
                            '.$img.'
                            </div>
                            <div class="pl-1">
                                <p class="m-0">'.$status.SAMPLE.'-'.(($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->id.'</p>
                                <p class="m-0">'.(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' | '.$Community->name : '').$cluster_street.(($villa_number) ? ' | '.'No '.$villa_number : '').'</p>
                                <p class="m-0">'.(($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '').'</p>
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

    public function GetPropertiesApi(){

        $where=' WHERE 1 ';

        $portal='';
        if( request('portal') ) {
            $portal=request('portal');
            if($portal=='pf')
                $where=' ,portal_property WHERE property.id=portal_property.property_id and portal_property.portal_id=1 and status =1';
            if($portal=='site')
                $where=' ,portal_property WHERE property.id=portal_property.property_id and portal_property.portal_id=4 and status =1';
        }else{
            $where=' ,portal_property WHERE property.id=portal_property.property_id and portal_property.portal_id in (2,3) and status =1';
        }

        if($where==' WHERE 1 ')
            $where .= ' AND status =1';


        $data = array();

        $Records=DB::select("SELECT DISTINCT property.* FROM `property` ".$where." ORDER BY property.id DESC");

        $obj=[];
        foreach($Records as $row){
            $PropertyType=PropertyType::find($row->property_type_id);
            $Emirate=Emirate::find($row->emirate_id);
            $MasterProject=MasterProject::find($row->master_project_id);
            $Community=Community::find($row->community_id);
            $CommunityParent=CommunityParent::find($Community->parent_id);
            //$ClusterStreet=ClusterStreet::find($row->cluster_street_id);
            $VillaType=VillaType::find($row->villa_type_id);
            $Bedroom=Bedroom::find($row->bedroom_id);
            $Bathroom=Bathroom::find($row->bathroom_id);
            $View=View::find($row->view);
            $ClientManager=Admin::find( $row->client_manager_id );
            //$ClientManager2=Admin::find( $row->client_manager2_id );
            //$Note=PropertyNote::where('property_id','=',$row->id)->latest('created_at', 'desc')->first();

            $bedroomText='Bedrooms';
            if($Bedroom && $Bedroom->name=='1')
                $bedroomText='Bedroom';

            $bathroomText='Bathrooms';
            if($Bathroom && $Bathroom->name=='1')
                $bathroomText='Bathroom';

            $description=nl2br($row->description);
            $description.='<br>';
            $description.=($VillaType) ? '<br>* Type: '.$VillaType->name : '';
            $description.=($row->bua) ? '<br>* BUA: '.number_format($row->bua).' Sq Ft' : '';
            $description.=($row->plot_sqft) ? '<br>* Plot: '.number_format($row->plot_sqft).' Sq Ft' : '';
            $description.=($Bedroom) ? '<br>* '.$Bedroom->name.( ($Bedroom->name != 'Studio') ? ' '.$bedroomText : '' ) : '';
            $description.=($Bathroom && $Bathroom->name!='0') ? '<br>* '.$Bathroom->name.' '.$bathroomText : '';
            $description.=($row->maid=='Yes') ? "<br>* Maid's Room" : '';
            $description.=($row->study=='Yes') ? '<br>* Study Room' : '';
            $description.=($row->storage=='Yes') ? '<br>* Storage Room' : '';
            $description.=($View) ? '<br>* '.$View->name : '';
            $description.=($row->furnished && ($row->property_type_id!=19 && $row->property_type_id!=29)) ? '<br>* '.$row->furnished : '';
            $description.=($row->parking && $row->parking!='0') ? '<br>* '.$row->parking.' Parking' : '';
            $description.=($row->usp) ? '<br>* '.$row->usp : '';
            $description.=($row->usp2) ? '<br>* '.$row->usp2 : '';
            $description.=($row->usp3) ? '<br>* '.$row->usp3 : '';
            $description.=($row->status2 && ($row->property_type_id!=19 && $row->property_type_id!=29)) ? '<br>* '.Status2[$row->status2] : '';

            $pictures=explode(',', $row->pictures);
            $img='';
            if($row->pictures)
                $img='<div style="width: 50px;"><img style="max-width: 50px;" src="/storage/'.$pictures[0].'"></div>';

            $expected_price=$row->expected_price;
            $Rent_Frequency='';
            if($row->listing_type_id==2){

                if($row->yearly){
                    $expected_price=$row->yearly;
                    $Rent_Frequency='Yearly';
                }else if($row->monthly){
                    $expected_price=$row->monthly;
                    $Rent_Frequency='Monthly';
                }else if($row->weekly){
                    $expected_price=$row->weekly;
                    $Rent_Frequency='Weekly';
                }else{
                    $expected_price=$row->daily;
                    $Rent_Frequency='Daily';
                }
            }

            $obj['id']=SAMPLE.'-'.(($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->id;
            $obj['only_id']=$row->id;
            $obj['dtcm_number']=$row->dtcm_number;
            $obj['offering_type']=(($row->type==1) ? 'R' : 'C').(($row->listing_type_id==1) ? 'S' : 'R');
            $obj['listing_type']=ListingType_XML[$row->listing_type_id];
            $obj['property_type']=($PropertyType) ? $PropertyType->bayut_title : '';
            $obj['property_type_site']=($PropertyType) ? $PropertyType->name : '';
            $obj['property_type_pf']=($PropertyType) ? $PropertyType->abbreviation : '';
            $obj['price_on_application']=$row->ask_for_price;

//            if($row->ask_for_price=='No')
            if($row->listing_type_id==1){
                $obj['price']=$row->expected_price;
            }else{
                $rent_price='';
                $rent_price.=(($row->yearly) ? '<yearly>'.$row->yearly.'</yearly>' : '');
                $rent_price.=(($row->monthly) ? '<monthly>'.$row->monthly.'</monthly>' : '');
                $rent_price.=(($row->weekly) ? '<weekly>'.$row->weekly.'</weekly>' : '');
                $rent_price.=(($row->daily) ? '<daily>'.$row->daily.'</daily>' : '');

                $obj['price']=$rent_price;
            }
//            else
//                $obj['price']='';

            $obj['Price_BD']=$expected_price;

            $obj['service_charge']='';

            $obj['img']=$img;
            $obj['status']='live';//Status[$row->status];
            $obj['city']=($Emirate) ? $Emirate->name : '';
            $obj['locality']=($MasterProject) ? $MasterProject->name : '';

            $communityName=(($Community->bayut_name) ? $Community->bayut_name : $Community->name);
            if($portal=='pf'){
                $communityName=$Community->name;
            }

            $obj['sub_locality']=($CommunityParent) ? $CommunityParent->name : $communityName;
            $obj['tower_name']=($CommunityParent) ? $communityName : '';

            $obj['Property_Title']=$row->title;
            $obj['Property_Title_AR']='';
            $obj['Website_Title']=$row->website_title;
            $obj['Property_Description']=$description;
            $obj['Property_Description_AR']='';
            $obj['Property_Size']=$row->bua;
            $obj['Property_Size_Unit']='SQFT';
            $obj['Bedrooms']=($Bedroom) ? $Bedroom->name : '';
            $obj['Bathroom']=($Bathroom) ? $Bathroom->name : '';
            $obj['view']=($View) ? $View->name : '';
            $obj['Listing_Agent_id']=($ClientManager) ? $ClientManager->id : '';
            $obj['Listing_Agent']=($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '';
            $obj['Listing_Agent_Phone']=($ClientManager) ? $ClientManager->main_number : '';
            $obj['Listing_Agent_Photo']=($ClientManager && $ClientManager->pic_name) ? request()->getSchemeAndHttpHost().'/storage/'.$ClientManager->pic_name  : '';
            $obj['Listing_Agent_Email']=($ClientManager) ? $ClientManager->email : '';
            $obj['license_no']=($ClientManager) ? $ClientManager->rera_brn : '';
            $obj['info']= '';
            $obj['stories']='';
            $obj['parking']=$row->parking;
            $obj['number_cheques']=$row->number_cheques;

            $Furnished=['Furnished'=>'Yes','Unfurnished'=>'No','Semi furnished'=>'Partly'];

            $obj['furnished']=($row->furnished) ? $Furnished[$row->furnished] : '';
            $obj['view360']=$row->video_360_degrees;

            $PropertyFeatures=PropertyFeature::where('property_id', $row->id)->get();
            $Features='';
            $Facilities='';
            $PF_Features=[];
            foreach ($PropertyFeatures as $PF) {
                $Feature=Features::find($PF->feature_id);

                $Features.= '<Feature><![CDATA['.$Feature->name.']]></Feature>';
                $Facilities.= '<facility>'.$Feature->name.'</facility>';
                $PF_Features[]=$Feature->abbreviation;
            }

            $obj['Features']=$Features;
            $obj['Facilities']=$Facilities;
            $obj['PF_Features']=join(',',$PF_Features);
            $obj['commercial_amenities']='';
            $obj['plot_size']=$row->plot_sqft;

            $pictures=explode(',', $row->pictures);
            $img='';
            $img_site='';
            $photo='';
            if($row->pictures) {
                foreach ($pictures as $pic) {
                    $img .= '<Image><![CDATA[' . request()->getSchemeAndHttpHost().'/storage/'.$pic . ']]></Image>';
                    $img_site.= '<image>' . request()->getSchemeAndHttpHost().'/storage/'.$pic . '</image>';
                    $photo .= '<url last_updated="'.$row->updated_at.'">' . request()->getSchemeAndHttpHost().'/storage/'.$pic . '</url>';
                }
            }
            $obj['Images']=$img;
            $obj['land_department_qr']=($row->land_department_qr) ? request()->getSchemeAndHttpHost().'/storage/'.$row->land_department_qr:'';
            $obj['img_site']=$img_site;
            $obj['photo']=$photo;
            $obj['Video']=$row->video_link;
            $obj['build_year']='';
            $obj['floor']='';
            $obj['floor_plan']='';
            $obj['geopoints']='';
            $obj['title_deed']=$row->title_deed_no;
            $obj['availability_date']=$row->available_from;
            $obj['Developer']='';
            $obj['project_name']='';
            $obj['completion_status']=$row->off_plan;
            $obj['Last_Updated']=$row->updated_at;
            $obj['Listing_Date']=$row->updated_at;
            $obj['Permit_Number']=$row->rera_permit;
            $obj['Rent_Frequency']=$Rent_Frequency;
            $obj['status2']=($row->status2) ? Status2[$row->status2] : 'N/A';
            $obj['Off_Plan']=($row->off_plan=='completed' || $row->off_plan=='completed_primary' || $row->listing_type_id==2) ? 'No' : 'Yes';
            $obj['updated_at']=$row->updated_at;
            $obj['PreviewLink']=request()->getSchemeAndHttpHost().'/property/brochure/'.\Helper::idCode($row->id);


            $data[] = $obj;
            $obj=[];
        }
//        return $data;
        if(request('portal')=='pf') {
            $last_updated=Property::orderBy('updated_at','DESC')->first();
            return response()->view('admin/xml-propertyfinder', [
                'response' => $data,
                'listing_count' => count($Records),
                'last_updated' => ($last_updated) ? $last_updated->updated_at : ''
            ])->header('Content-Type', 'text/xml');
        }elseif (request('portal')=='site') {
            return response()->view('admin/xml-site', [
                'response' => $data
            ])->header('Content-Type', 'text/xml');
        }else{
            return response()->view('admin/xml', [
                'response' => $data
            ])->header('Content-Type', 'text/xml');
        }
    }

    public function exportProperties(Request $request){
        return Excel::download(new ExportProperty, 'Properties.xlsx');
    }

    public function SelectAjax(Request $request){
        $search=request('q');
        $properties=Property::join('master_project', 'master_project.id', '=', 'property.master_project_id')
            ->join('community', 'community.id', '=', 'property.community_id')
            ->select('property.*', 'master_project.name as master_name', 'community.name as community_name')
            ->where('property.id','LIKE','%'.$search.'%')
            ->orWhere('master_project.name','LIKE','%'.$search.'%')
            ->orWhere('community.name','LIKE','%'.$search.'%')
            ->limit(30)->get();
        $json = [];
        foreach($properties as $row){
            $json[] = ['id'=>$row->id, 'address'=>$row->master_name." | ".$row->community_name,
                'ref'=>SAMPLE.'-'.( (($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->id )];
        }
        return json_encode($json);
    }

    public function propertyAjax(Request $request){
        $property=property::find($request->property);

        return $property;//json_encode($json);
    }

    public function Property(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/properties",'name'=>"Properties"], ['name'=>"Property"]
        ];
        $ClusterStreets=ClusterStreet::get();
        $Communitys=Community::get();
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $PropertyTypes=PropertyType::get();
        $Emirates=Emirate::orderBy('name','ASC')->get();
        $MasterProjects=MasterProject::orderBy('name','ASC')->get();
        $VendorMotivations=VendorMotivation::get();
        $Views=View::orderBy('name','ASC')->get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();
        $VaastuOrientations=VaastuOrientation::get();
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();//ClientManager::get();
        $Features=Features::where('type','=','Features')->get();
        $Amenities=Features::where('type','=','Amenities')->get();
        $propertyMax=Property::max('id');
        $Property='';
        return view('/admin/property', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'ClusterStreets'=>$ClusterStreets,
            'Communitys'=>$Communitys,
            'ContactSources'=>$ContactSources,
            'PropertyTypes'=>$PropertyTypes,
            'Emirates'=>$Emirates,
            'MasterProjects'=>$MasterProjects,
            'VendorMotivations'=>$VendorMotivations,
            'Views'=>$Views,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
            'VaastuOrientations'=>$VaastuOrientations,
            'ClientManagers'=>$ClientManagers,
            'Features'=>$Features,
            'Amenities'=>$Amenities,
            'Property'=>$Property,
            'propertyMax'=>($propertyMax+1),
        ]);
    }

    public function view(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/properties",'name'=>"Properties"], ['name'=>"View"]
        ];
        $Property=Property::find(request('id'));
        $PropertyNote=DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.status, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND property_id=".$Property->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND property_id=".$Property->id." ORDER BY created_at desc");

        if(request('reminder')){
            $rPropertyNote=PropertyNote::find(request('reminder'));
            $rPropertyNote->seen=1;
            $rPropertyNote->save();
        }

        if(request('rfl')){
            $PropertyStatusHistory=PropertyStatusHistory::find(request('rfl'));
            $PropertyStatusHistory->seen=1;
            $PropertyStatusHistory->save();
        }

        $Previous='';
        $Next='';
        if(Session::exists('property_where')) {
            $Properties = DB::select("SELECT property.id FROM `property` " . Session::get('property_where'));
            if($Properties) {
                $PropertiesArray = [];
                foreach ($Properties as $row) {
                    $PropertiesArray[] = $row->id;
                }

                // return Session::get('property_where');
                $array_index = array_search($Property->id, $PropertiesArray);
                $countArray = count($PropertiesArray);
                $countArray--;

                $Previous = ($array_index == 0) ? '' : $PropertiesArray[$array_index - 1];
                $Next = ($array_index == $countArray) ? '' : $PropertiesArray[$array_index + 1];
            }
        }
        if ($Property) {
            return view('/admin/property-view', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'Property' => $Property,
                'PropertyNote'=>$PropertyNote,
                'Previous'=>$Previous,
                'Next'=>$Next,
            ]);
        }else{
            return redirect('/admin/properties');
        }

    }

    public function brochure(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $id=request('id');
        $idDecode= \Helper::idDecode($id);
        $Property=Property::find($idDecode);

        $agent_id=\Helper::idDecode(request('a'));
        $agent=Admin::where('id',$agent_id)->first();

        if (!$Property || ($agent_id && !$agent)) {
            return view('/admin/property-brochure-not-found', [
                'pageConfigs' => $pageConfigs,
                'Property' => $Property,
            ]);
        }else{
            return view('/admin/property-brochure', [
                'pageConfigs' => $pageConfigs,
                'Property' => $Property,
            ]);
        }

    }

    public function pfListed($l_id,$action='listed'){
        $token=env('PF_TOKEN');

        $token_response = Http::withToken(base64_encode($token),'Basic')
            ->post('https://auth.propertyfinder.com/auth/oauth/v1/token',[
                "scope"=> "openid",
                "grant_type"=> "client_credentials",
            ]);
        $token_response= json_decode($token_response);

        //$property=Property::find($l_id);

        //$pf_id=$property->pf_id;

        //LUL-S-11269  Update Client manager
        //LUL-R-12474  Update Location
        //LUL-S-12463  update title
        //LUL-S-11413  Modification not listed
        //LUL-S-12515  update price sho in api
        //-----------------------------------------------------
        //LUL-R-12501 isLive false



        //return $publish_response = Http::withToken($token_response->access_token)->post('https://atlas.propertyfinder.com/v1/listings/01K2EVHHKMZWR1QRMB2M6M1HB0/publish');
        return $publish_response = Http::withToken($token_response->access_token)->post('https://atlas.propertyfinder.com/v1/listings/01K2EVHHKMZWR1QRMB2M6M1HB0');

        $reference='VSP-R-32';//SAMPLE.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->id;
        return $pf_property=Http::withToken($token_response->access_token)->get('https://atlas.propertyfinder.com/v1/listings?filter[reference]='.$reference);

        if(!$pf_id){
            $pf_property=Http::withToken($token_response->access_token)->get('https://atlas.propertyfinder.com/v1/listings?filter[reference]='.$reference);

            if($pf_property->status()==200){
                $pf_property= json_decode($pf_property);
                $pf_id=$pf_property->results[0]->id;
            }
        }

        if($action=='delete'){
            Http::withToken($token_response->access_token)->delete('https://atlas.propertyfinder.com/v1/listings/'.$pf_id);
            $property->pf_id=null;
            $property->save();

            return ;
        }

        $PropertyType=PropertyType::find($property->property_type_id);
        $Emirate=Emirate::find($property->emirate_id);
        $MasterProject=MasterProject::find($property->master_project_id);
        $Community=Community::find($property->community_id);
        $CommunityParent=CommunityParent::find($Community->parent_id);
        //$ClusterStreet=ClusterStreet::find($row->cluster_street_id);
        $VillaType=VillaType::find($property->villa_type_id);
        $Bedroom=Bedroom::find($property->bedroom_id);
        $Bathroom=Bathroom::find($property->bathroom_id);
        $View=View::find($property->view);
        $ClientManager=Admin::find( $property->client_manager_id );

        $agent_pf_info = Http::withToken($token_response->access_token)
            ->get('https://atlas.propertyfinder.com/v1/users?search='.$ClientManager->email);
        $agent_pf_info= json_decode($agent_pf_info);

        $assignedTo_id= $agent_pf_info->data[0]->publicProfile->id;

        $PropertyFeatures=PropertyFeature::where('property_id', $property->id)->get();
        $amenities=[];
        foreach ($PropertyFeatures as $PF) {
            $Feature=Features::find($PF->feature_id);
            if($Feature->abbreviation)
                $amenities[]= $Feature->abbreviation;
        }
        //$amenities=rtrim($amenities,',');

        $pictures=explode(',', $property->pictures);
        $img=[];
        if($property->pictures) {
            foreach ($pictures as $pic) {
                $img[]=["original"=>["url"=> request()->getSchemeAndHttpHost().'/storage/'.$pic]];
            }
        }
        //$img=rtrim($img,',');

        $Furnished=['Furnished'=>'furnished',
            'Unfurnished'=>'unfurnished',
            'Semi furnished'=>'semi-furnished'];
        $furnished=($property->furnished) ? $Furnished[$property->furnished] : '';

        $rent_type='';

        $amounts=[];
        if($property->listing_type_id==1){
            $amounts['sale']=$property->expected_price;
        }else{
            $rent_type=(($property->monthly) ? 'monthly' : '');
            $rent_type=(($property->yearly) ? 'yearly' : $rent_type);


            ($property->yearly) ? ($amounts['yearly']=$property->yearly) : '';
            ($property->monthly) ? ($amounts['monthly']=$property->monthly) : '';
            ($property->weekly) ? ($amounts['weekly']=$property->weekly) : '';
            ($property->daily) ? ($amounts['daily']=$property->daily) : '';
        }

        $listingAdvertisementNumber='';
        $compliance_type='';
        if($property->emirate_id==2){
            if($property->dtcm_number){
                $listingAdvertisementNumber=$property->dtcm_number;
                $compliance_type='dtcm';
            }

            if($property->rera_permit){
                $listingAdvertisementNumber=$property->rera_permit;
                $compliance_type='rera';
            }
        }

        if($property->emirate_id==7){
            if($property->dtcm_number){
                $listingAdvertisementNumber=$property->dtcm_number;
                $compliance_type='dtcm';
            }
        }

        $json=[];
        $json['amenities']=$amenities;

        $json['assignedTo']=['id'=>$assignedTo_id];

        $json['availableFrom']=$property->available_from;

        $json['bathrooms']=( ($Bathroom) ? $Bathroom->name : '' );

        $json['bedrooms']=( ($Bedroom) ? strtolower($Bedroom->name) : '' );

        $json['category']=strtolower( PropertyType[$property->type] );

        $json['compliance']=['listingAdvertisementNumber'=>$listingAdvertisementNumber,'type'=>$compliance_type];

        $json['description']=['en'=>$property->description];

        $json['furnishingType']=$furnished;

        $json['location']=['id'=>$property->pf_location_id];

        $json['media']=['images'=>$img
            ,'videos'=>['default'=>(($property->video_link) ? $property->video_link : "" ),'view360'=>(($property->video_360_degrees) ? $property->video_360_degrees : "" )]
        ];

        $json['parkingSlots']=$property->parking;

        $json['plotSize']=(int)$property->plot_sqft;

        $json['price']=['amounts'=>$amounts,
            'numberOfCheques'=>(($property->number_cheques)?:0),'type'=>( ($property->listing_type_id==1) ? 'sale' : $rent_type )];

        $json['reference']=$reference;

        $json['size']=(int)$property->bua;

        $json['title']=['en'=>$property->title];

        $json['type']=strtolower($PropertyType->name);

        $json['uaeEmirate']=strtolower($Emirate->name);

        $json['updatedAt']=str_replace(' ','T',$property->updated_at);

        //return $json;

        //return $response = Http::withToken($token_response->access_token)->get('https://atlas.propertyfinder.com/v1/listings?draft=true&filter[reference]='.$reference);

        if($pf_id)
            $listed_response = Http::withBody(json_encode($json),'application/json')->withToken($token_response->access_token)->put('https://atlas.propertyfinder.com/v1/listings/'.$property->pf_id);
        else
            $listed_response = Http::withBody(json_encode($json),'application/json')->withToken($token_response->access_token)->post('https://atlas.propertyfinder.com/v1/listings');

        if($listed_response->status()==200){
            $listed_response= json_decode($listed_response);

            $property->status=1;
            $property->pf_id=$listed_response->id;
            $property->save();

            $publish_response = Http::withToken($token_response->access_token)->post('https://atlas.propertyfinder.com/v1/listings/'.$listed_response->id.'/publish');

            //if($publish_response->status()==200)
            //    return redirect('/admin/property/view/'.$property->id);

        }

    }

    public function Store(Request $request)
    {
        $pictures=null;
        if ((request('InputAttachFile'))){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                $this->resizeImage($image);

                //if( request( current( explode('.',$image) ) ) ){
                $this->watermark($image);
                //}

            }
            $pictures=rtrim($pictures,',');
        }

        $duplicateProperty=Property::where('listing_type_id', request('ListingType'))->
        where('master_project_id',request('MasterProject'))->
        where('community_id',request('Community'))->
        where('villa_number',request('VillaNumber'))->first();
        if($duplicateProperty){
            Session::flash('error','This property is already registered');
            return redirect()->back()->withInput($request->all());
        }

        $path="";
        $land_department_qr_name=null;
        if ($request->file('land_department_qr')) {
            $path = $request->file('land_department_qr')->store('public/images');
        }

        if( $path ){
            $land_department_qr_name=explode("/",$path);
            $land_department_qr_name=end ($land_department_qr_name);

            //Storage::delete('public/images/'.$Admin->pic_name);
            //$Admin->pic_name = $pic_name;
        }

        $adminAuth=\Auth::guard('admin')->user();

        $clientManager=($adminAuth->type>2) ? $adminAuth->id :request('ClientManager');

        $status=request('Status');
        $Property=Property::create([
            'admin_id'=>$adminAuth->id,
            //'created_at'=>request('DateEntered'),
            'contact_id'=>request('contact'),
            'data_center_id'=>request('data_center_id')?: null ,
            'listing_type_id'=>request('ListingType'),
            'status'=>$status,
            'title'=>request('Title'),
            'website_title'=>request('WebsiteTitle'),
            'contact_source_id'=>request('ContactSource'),
            'vendor_motivation_id'=>request('VendorMotivation'),
            'description'=>request('Description'),
            'type'=>request('Type'),
            'property_type_id'=>request('PropertyType'),
            'villa_number'=>request('VillaNumber'),
            'emirate_id'=>request('Emirate'),
            'master_project_id'=>request('MasterProject'),
            'community_id'=>request('Community'),
            'cluster_street_id'=>(request('ClusterStreet')=='0')? null :request('ClusterStreet'),
            'villa_type_id'=>(request('VillaType')=='0')? null :request('VillaType'),
            'bedroom_id'=>request('Bedrooms'),
            'bathroom_id'=>request('Bathrooms'),
            'plot_sqft'=>(request('PlotSQFT')) ? str_replace(',','',request('PlotSQFT')) : 0,
            'vaastu_orientation_id'=>request('VaastuOrientation'),
            'expected_price'=>(request('ExpectedPrice')) ? str_replace(',','',request('ExpectedPrice')) : 0,
            'client_manager_id'=>$clientManager,
            'client_manager2_id'=>request('ClientManager2'),
            'off_plan'=>request('OffPlan'),
            'offplanDetails_saleType'=>request('OffPlanDetailsSaleType'),
            'offplanDetails_dldWaiver'=>request('OffPlanDetailsDldWaiver'),
            'offplanDetails_originalPrice'=>(request('OffPlanDetailsOriginalPrice')) ? str_replace(',','',request('OffPlanDetailsOriginalPrice')) : 0,
            'offplanDetails_amountPaid'=>(request('OffPlanDetailsAmountPaid')) ? str_replace(',','',request('OffPlanDetailsAmountPaid')) : 0,
            'completion_date'=>request('CompletionDate'),
            'bua'=>(request('BUA')) ? str_replace(',','',request('BUA')) : 0,
            'view'=>request('View'),
            'usp'=>request('USP'),
            'usp2'=>request('USP2'),
            'usp3'=>request('USP3'),
            'usp4'=>request('USP4'),
            'parking'=>request('Parking'),
            'occupancy_status'=>request('OccupancyStatus'),
            'passport'=>request('Passport'),
            'title_deed'=>request('TitleDeed'),
            'form_a'=>request('FormA'),
            'eid_front'=>request('EIDFront'),
            'eid_back'=>request('EIDBack'),
            'power_of_attorney'=>request('PowerOfAttorney'),
            'visa'=>request('Visa'),
            'other_doc'=>request('OtherDoc'),
            'rera_permit'=>request('ReraPermit'),
            'exclusive'=>request('Exclusive'),
            'published'=>request('Published'),
            'featured'=>request('Featured'),
            'ask_for_price'=>request('AskForPrice'),
            'pictures'=>$pictures,
            'land_department_qr'=>$land_department_qr_name,
            'maid'=>request('Maid'),
            'video_link'=>request('video_link'),
            'video_360_degrees'=>request('video_360_degrees'),
            'driver'=>request('Driver'),
            'study'=>request('Study'),
            'storage'=>request('Storage'),
            'furnished'=>request('Furnished'),
            'status2'=>request('Status2'),
            'rented_for'=>(request('RentedFor')) ? str_replace(',','',request('RentedFor')) : 0,
            'rented_from'=>request('RentedFrom'),
            'rented_until'=>request('RentedUntil'),
            'vacating_notice'=>request('VacatingNotice'),
            'available_from'=>request('AvailableFrom'),
            'number_cheques'=>request('NumberCheques'),
            'frequency'=>request('Frequency'),
            'title_deed_no'=>request('TitleDeedNo'),
            'expiration_date'=>request('ExpirationDate'),
            'next_availability'=>request('NextAvailability'),
            'dtcm_number'=>request('DTCMNumber'),
            'starting_date'=>request('StartingDate'),
            'daily'=>(request('DailyPrice')) ? str_replace(',','',request('DailyPrice')) : 0,
            'weekly'=>(request('WeeklyPrice')) ? str_replace(',','',request('WeeklyPrice')) : 0,
            'monthly'=>(request('MonthlyPrice')) ? str_replace(',','',request('MonthlyPrice')) : 0,
            'yearly'=>(request('YearlyPrice')) ? str_replace(',','',request('YearlyPrice')) : 0,
            'property_management'=>request('property_management'),
            'viewing_arrangement'=>request('ViewingArrangement'),

        ]);

        if(request('note')){
            $data=PropertyNote::create([
                'admin_id'=>$adminAuth->id,
                'property_id'=>$Property->id,
                'note'=>request('note')
            ]);
        }

        if(request('FeaturesCheck')){
            foreach ( request('FeaturesCheck') as $features_id){
                PropertyFeature::create([
                    'property_id'=>$Property->id,
                    'feature_id'=>$features_id
                ]);
            }
        }

//        if(request('PortalCheck')){
        $Portals=Portal::get();
        foreach ( $Portals as $portal){
            PortalProperty::create([
                'portal_id'=>$portal->id,
                'property_id'=>$Property->id
            ]);
        }
//        }

        if($status=='11'){
            Notification::create([
                'type'=>'Request Listing',
                'parent'=>'Property',
                'admin_id'=>$adminAuth->id,
                'parent_id'=>$Property->id
            ]);
        }

        if(request('data_center_id')){
            $DataCenter=DataCenter::find(request('data_center_id'));
            $DataCenter->added_to_property=$Property->id;
            $DataCenter->added_to_property_date=date('Y-m-d H:i:s');
            $DataCenter->added_to_property_admin=$adminAuth->id;
            $DataCenter->save();
        }

        PropertyStatusHistory::create([
            'property_id'=>$Property->id,
            'h_admin_id' => $adminAuth->id,
            'status'=>$status,
            'rfl_status'=>0,
            'ma_first'=>1
        ]);

        return redirect('/admin/property/view/'.$Property->id);
    }

    public function copyStore(Request $request)
    {
        $adminAuth=\Auth::guard('admin')->user();

        $clientManager=($adminAuth->type>2) ? $adminAuth->id :request('ClientManager');

        $parent_id=request('CopyProperty');
        $parent_property=Property::find($parent_id);
        $status=11;//$parent_property->status;

        $pictures=explode(',', $parent_property->pictures);
        $pictures_new=$parent_property->pictures;
        if($parent_property->pictures) {
            $pictures_new='';
            foreach ($pictures as $pic) {
                $picture=explode('.',$pic);
                $picture_new=md5($picture[0] . microtime()).'.'.$picture[1];
                Storage::copy('public/images/' . $pic, 'public/images/'.$picture_new);
                $pictures_new .= $picture_new.',';
            }
            $pictures_new=rtrim($pictures_new,',');
        }

        $passport_new=$parent_property->passport;
        if($parent_property->passport) {
            $passport=explode('.',$parent_property->passport);
            $passport_new=md5($passport[0] . microtime()).'.'.$passport[1];
            Storage::copy('public/images/' . $parent_property->passport, 'public/images/'.$passport_new);
        }

        $title_deed_new=$parent_property->title_deed;
        if($parent_property->title_deed) {
            $title_deed=explode('.',$parent_property->title_deed);
            $title_deed_new=md5($title_deed[0] . microtime()).'.'.$title_deed[1];
            Storage::copy('public/images/' . $parent_property->title_deed, 'public/images/'.$title_deed_new);
        }

        $form_a_new=$parent_property->form_a;
        if($parent_property->form_a) {
            $form_a=explode('.',$parent_property->form_a);
            $form_a_new=md5($form_a[0] . microtime()).'.'.$form_a[1];
            Storage::copy('public/images/' . $parent_property->form_a, 'public/images/'.$form_a_new);
        }

        $eid_front_new=$parent_property->eid_front;
        if($parent_property->eid_front) {
            $eid_front=explode('.',$parent_property->eid_front);
            $eid_front_new=md5($eid_front[0] . microtime()).'.'.$eid_front[1];
            Storage::copy('public/images/' . $parent_property->eid_front, 'public/images/'.$eid_front_new);
        }

        $eid_back_new=$parent_property->eid_back;
        if($parent_property->eid_back) {
            $eid_back=explode('.',$parent_property->eid_back);
            $eid_back_new=md5($eid_back[0] . microtime()).'.'.$eid_back[1];
            Storage::copy('public/images/' . $parent_property->eid_back, 'public/images/'.$eid_back_new);
        }

        $power_of_attorney_new=$parent_property->power_of_attorney;
        if($parent_property->power_of_attorney) {
            $power_of_attorney=explode('.',$parent_property->power_of_attorney);
            $power_of_attorney_new=md5($power_of_attorney[0] . microtime()).'.'.$power_of_attorney[1];
            Storage::copy('public/images/' . $parent_property->power_of_attorney, 'public/images/'.$power_of_attorney_new);
        }

        $visa_new=$parent_property->visa;
        if($parent_property->visa) {
            $visa=explode('.',$parent_property->visa);
            $visa_new=md5($visa[0] . microtime()).'.'.$visa[1];
            Storage::copy('public/images/' . $parent_property->visa, 'public/images/'.$visa_new);
        }

        $other_doc_new=$parent_property->other_doc;
        if($parent_property->other_doc) {
            $other_doc=explode('.',$parent_property->other_doc);
            $other_doc_new=md5($other_doc[0] . microtime()).'.'.$other_doc[1];
            Storage::copy('public/images/' . $parent_property->other_doc, 'public/images/'.$other_doc_new);
        }

        $land_department_qr_new=$parent_property->land_department_qr;
        if($parent_property->land_department_qr) {
            $land_department_qr=explode('.',$parent_property->land_department_qr);
            $land_department_qr_new=md5($land_department_qr[0] . microtime()).'.'.$land_department_qr[1];
            Storage::copy('public/images/' . $parent_property->land_department_qr, 'public/images/'.$land_department_qr_new);
        }
        $Property=Property::create([
            'parent_id'=>$parent_id,
            'admin_id'=>$adminAuth->id,
            'contact_id'=>$parent_property->contact_id,
            'listing_type_id'=>$parent_property->listing_type_id,
            'status'=>$status,
            'title'=>$parent_property->title,
            'website_title'=>$parent_property->website_title,
            'contact_source_id'=>$parent_property->contact_source_id,
            'vendor_motivation_id'=>$parent_property->vendor_motivation_id,
            'description'=>$parent_property->description,
            'type'=>$parent_property->type,
            'property_type_id'=>$parent_property->property_type_id,
            'villa_number'=>$parent_property->villa_number,
            'emirate_id'=>$parent_property->emirate_id,
            'master_project_id'=>$parent_property->master_project_id,
            'community_id'=>$parent_property->community_id,
            'cluster_street_id'=>$parent_property->cluster_street_id,
            'villa_type_id'=>$parent_property->villa_type_id,
            'bedroom_id'=>$parent_property->bedroom_id,
            'bathroom_id'=>$parent_property->bathroom_id,
            'plot_sqft'=>$parent_property->plot_sqft,
            'vaastu_orientation_id'=>$parent_property->vaastu_orientation_id,
            'expected_price'=>$parent_property->expected_price,
            'client_manager_id'=>$parent_property->client_manager_id,
            'client_manager2_id'=>$parent_property->client_manager2_id,
            'off_plan'=>$parent_property->off_plan,
            'offplanDetails_saleType'=>$parent_property->offplanDetails_saleType,
            'offplanDetails_dldWaiver'=>$parent_property->offplanDetails_dldWaiver,
            'offplanDetails_originalPrice'=>$parent_property->offplanDetails_originalPrice,
            'offplanDetails_amountPaid'=>$parent_property->offplanDetails_amountPaid,
            'completion_date'=>$parent_property->completion_date,
            'bua'=>$parent_property->bua,
            'view'=>$parent_property->view,
            'usp'=>$parent_property->usp,
            'usp2'=>$parent_property->usp2,
            'usp3'=>$parent_property->usp3,
            'usp4'=>$parent_property->usp4,
            'parking'=>$parent_property->parking,
            'occupancy_status'=>$parent_property->occupancy_status,
            'passport'=>$passport_new,
            'title_deed'=>$title_deed_new,
            'form_a'=>$form_a_new,
            'eid_front'=>$eid_front_new,
            'eid_back'=>$eid_back_new,
            'power_of_attorney'=>$power_of_attorney_new,
            'visa'=>$visa_new,
            'other_doc'=>$other_doc_new,
            'rera_permit'=>$parent_property->rera_permit,
            'exclusive'=>$parent_property->exclusive,
            'published'=>$parent_property->published,
            'featured'=>$parent_property->featured,
            'ask_for_price'=>$parent_property->ask_for_price,
            'pictures'=>$pictures_new,
            'land_department_qr'=>$land_department_qr_new,
            'maid'=>$parent_property->maid,
            'video_link'=>$parent_property->video_link,
            'video_360_degrees'=>$parent_property->video_360_degrees,
            'driver'=>$parent_property->driver,
            'study'=>$parent_property->study,
            'storage'=>$parent_property->storage,
            'furnished'=>$parent_property->furnished,
            'status2'=>$parent_property->status2,
            'rented_from'=>$parent_property->rented_from,
            'rented_until'=>$parent_property->rented_until,
            'vacating_notice'=>$parent_property->vacating_notice,
            'available_from'=>$parent_property->available_from,
            'number_cheques'=>$parent_property->number_cheques,
            'frequency'=>$parent_property->frequency,
            'title_deed_no'=>$parent_property->title_deed_no,
            'expiration_date'=>$parent_property->expiration_date,
            'next_availability'=>$parent_property->next_availability,
            'dtcm_number'=>$parent_property->dtcm_number,
            'starting_date'=>$parent_property->starting_date,
            'daily'=>$parent_property->daily,
            'weekly'=>$parent_property->weekly,
            'monthly'=>$parent_property->monthly,
            'yearly'=>$parent_property->yearly,
            'property_management'=>$parent_property->property_management,
            'viewing_arrangement'=>$parent_property->viewing_arrangement,

        ]);

        $PropertyFeature=PropertyFeature::where('property_id',$parent_property->id)->get();
        foreach ( $PropertyFeature as $features){
            PropertyFeature::create([
                'property_id'=>$Property->id,
                'feature_id'=>$features->feature_id
            ]);
        }

        $Portals=Portal::get();
        foreach ( $Portals as $portal){
            PortalProperty::create([
                'portal_id'=>$portal->id,
                'property_id'=>$Property->id
            ]);
        }

        if($status=='11'){
            Notification::create([
                'type'=>'Request Listing',
                'parent'=>'Property',
                'admin_id'=>$adminAuth->id,
                'parent_id'=>$Property->id
            ]);
        }

        PropertyStatusHistory::create([
            'property_id'=>$Property->id,
            'h_admin_id' => $adminAuth->id,
            'status'=>$status,
            'rfl_status'=>0,
            'ma_first'=>1
        ]);

        return redirect('/admin/property-edit/'.$Property->id);
    }

    public function PropertyDetails(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/properties",'name'=>"Properties"], ['name'=>"View"]
        ];

        $adminAuth=\Auth::guard('admin')->user();

        $Property=Property::find(request('id'));

        if($adminAuth->type>2 && $Property->status==1)
            return 'not this permission';

        $ClusterStreets=ClusterStreet::get();
        $Communitys=Community::get();
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $PropertyTypes=PropertyType::get();
        $Emirates=Emirate::orderBy('name','ASC')->get();
        $MasterProjects=MasterProject::get();
        $VendorMotivations=VendorMotivation::get();
        $Views=View::orderBy('name','ASC')->get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();
        $VaastuOrientations=VaastuOrientation::get();
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();//ClientManager::get();
        $Features=Features::where('type','=','Features')->get();
        $Amenities=Features::where('type','=','Amenities')->get();
        $PropertyNote=PropertyNote::with('Admin')->where('property_id','=',$Property->id)->orderBy('id', 'desc')->get();

        $pf_l_id='';
        $pf_name_address='';
        if($Property->pf_location_id){
            $token=env('PF_TOKEN');

            $token_response = Http::withToken(base64_encode($token),'Basic')
                ->post('https://auth.propertyfinder.com/auth/oauth/v1/token',[
                    "scope"=> "openid",
                    "grant_type"=> "client_credentials",
                ]);
            $token_response= json_decode($token_response);

            $response = Http::withToken($token_response->access_token)->get('https://atlas.propertyfinder.com/v1/locations?filter[id]='.$Property->pf_location_id);
            $response=json_decode($response);
            $pf_l_id=$response->data[0]->id;

            $pf_l_name=[];
            foreach ($response->data[0]->tree as $value) {
                $pf_l_name[]=$value->name;
            }

            $pf_name_address=join(' - ',array_reverse($pf_l_name));
        }

        if ($Property) {
            return view('/admin/property', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'Property' => $Property,
                'ClusterStreets'=>$ClusterStreets,
                'Communitys'=>$Communitys,
                'ContactSources'=>$ContactSources,
                'PropertyTypes'=>$PropertyTypes,
                'Emirates'=>$Emirates,
                'MasterProjects'=>$MasterProjects,
                'VendorMotivations'=>$VendorMotivations,
                'Views'=>$Views,
                'Bedrooms'=>$Bedrooms,
                'Bathrooms'=>$Bathrooms,
                'VaastuOrientations'=>$VaastuOrientations,
                'ClientManagers'=>$ClientManagers,
                'Features'=>$Features,
                'Amenities'=>$Amenities,
                'PropertyNote'=>$PropertyNote,
                'propertyMax'=>'',

                'pf_l_id'=>$pf_l_id,
                'pf_name_address'=>$pf_name_address
            ]);
        }else{
            return redirect('/admin/properties');
        }

    }

    public function action(Request $request){
        if ( request('property') ){

            $adminAuth=\Auth::guard('admin')->user();
            $array_not_assign=[];
            $array_assigned=[];
            foreach (request('property') as $id) {

                $afterProperty = Property::find($id);

                $Property = Property::find($id);
                if($Property->client_manager_id==$adminAuth->id || $adminAuth->type<3) {

                    $adminAuth = \Auth::guard('admin')->user();

                    $cm_2 = request('AssignTo');
                    $cm = request('ClientManager');

                    $afterContact = Contact::find($Property->contact_id);
                    $Contact = Contact::find($Property->contact_id);

                    if ($cm_2) {
                        //$Contact->client_manager_tow = ($cm_2=='null')? null : $cm_2;
                        $Property->client_manager2_id = ($cm_2 == 'null') ? null : $cm_2;
                        $cm_email=$cm_2;
                    }

                    if ($cm) {
                        $Contact->client_manager = $cm;
                        $Contact->save();
                        $Property->client_manager_id = $cm;
                        $cm_email = $cm;
                    }

                    $Property->save();

                    $beforeProperty = Property::find($id);
                    $beforeProperty = json_encode($beforeProperty);
                    $beforeProperty = json_decode($beforeProperty);
                    foreach ($beforeProperty as $key => $value) {
                        if ($key != 'created_at' && $key != 'updated_at' && $key != 'last_activity') {
                            if ($afterProperty->$key != $value) {
                                History::create([
                                    'admin_id' => $adminAuth->id,
                                    'model' => 'Property',
                                    'model_id' => $id,
                                    'action' => 'Update',
                                    'title' => $key,
                                    'after_value' => $afterProperty->$key,
                                    'before_value' => $value,
                                ]);
                            }
                        }
                    }

                    $beforeContact = Contact::find($Property->contact_id);
                    $beforeContact = json_encode($beforeContact);
                    $beforeContact = json_decode($beforeContact);
                    foreach ($beforeContact as $key => $value) {
                        if ($key != 'created_at' && $key != 'updated_at' && $key!='last_activity') {
                            if ($afterContact->$key != $value) {
                                $adminAuth = \Auth::guard('admin')->user();
                                History::create([
                                    'admin_id' => $adminAuth->id,
                                    'model' => 'Contact',
                                    'model_id' => $id,
                                    'action' => 'Update',
                                    'title' => $key,
                                    'after_value' => $afterContact->$key,
                                    'before_value' => $value,
                                ]);
                            }
                        }
                    }
                    $array_assigned[]=SAMPLE.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->id;
                }else{
                    $array_not_assign[]=SAMPLE.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->id;
                }
            }
            if($array_assigned){
                $assignedTo=Admin::find($cm_email);

                $body='Dear '.$assignedTo->firstname.' '.$assignedTo->lastname.'<br><br><br>The reference numbers listed below have been assigned to you.<br><br>';
                $body.=join('<br>',$array_assigned);
                $details = [
                    'subject' => 'Assigned Property',
                    'body' => $body
                ];

                try {
                    Mail::to($assignedTo->email)->send(new SendMail($details));
                }catch (\Exception $e){

                }
            }
            if($array_not_assign){
                //Session::flash('error',join(', ',$array_not_assign).'<br>'.'You can not assign the mentioned reference number as you are CM2 for them');
                return ['r'=>'0','msg'=>join(', ',$array_not_assign).'<br>'.'You can not assign the mentioned reference number as you are CM2 for them'];
            }
            return ['r'=>'1','msg'=>''];
        }
        return redirect('/admin/properties');
    }

    public function EditProperty(Request $request){

        $pictures=null;
        if (request('InputAttachFile')){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                $this->resizeImage($image);

                if( request( current( explode('.',$image) ) ) ){
                    $this->watermark($image);
                }

            }
            $pictures=rtrim($pictures,',');
        }

        $id=request('_id');

        $afterProperty = Property::find($id);

        $Property = Property::find($id);

        $adminAuth=\Auth::guard('admin')->user();

        $path="";
        $land_department_qr_name=null;
        if ($request->file('land_department_qr')) {
            $path = $request->file('land_department_qr')->store('public/images');
        }

        if( $path ){
            $land_department_qr_name=explode("/",$path);
            $land_department_qr_name=end ($land_department_qr_name);

            Storage::delete('public/images/'.$Property->land_department_qr);
            $Property->land_department_qr = $land_department_qr_name;
        }

        $status=request('Status');

        $Property->contact_id=request('contact');
        $Property->listing_type_id=request('ListingType');

        $Property->contact_source_id=request('ContactSource');
        $Property->vendor_motivation_id=request('VendorMotivation');

        $Property->type=request('Type');
        $Property->property_type_id=request('PropertyType');
        $Property->villa_number=request('VillaNumber');
        $Property->emirate_id=request('Emirate');
        $Property->master_project_id=request('MasterProject');
        $Property->community_id=request('Community');
        $Property->cluster_street_id=(request('ClusterStreet')=='0')? null :request('ClusterStreet');
        $Property->villa_type_id=(request('VillaType')=='0')? null :request('VillaType');
        $Property->bedroom_id=request('Bedrooms');
        $Property->bathroom_id=request('Bathrooms');
        $Property->plot_sqft=(request('PlotSQFT')) ? str_replace(',','',request('PlotSQFT')) : 0;
        $Property->vaastu_orientation_id=request('VaastuOrientation');
        //$Property->client_manager_id=request('ClientManager');
        //$Property->client_manager2_id=request('ClientManager2');
        $Property->off_plan=request('OffPlan');
        $Property->offplanDetails_saleType=request('OffPlanDetailsSaleType');
        $Property->offplanDetails_dldWaiver=request('OffPlanDetailsDldWaiver');
        $Property->offplanDetails_originalPrice=(request('OffPlanDetailsOriginalPrice')) ? str_replace(',','',request('OffPlanDetailsOriginalPrice')) : 0;
        $Property->offplanDetails_amountPaid=(request('OffPlanDetailsAmountPaid')) ? str_replace(',','',request('OffPlanDetailsAmountPaid')) : 0;
        $Property->completion_date=request('CompletionDate');
        $Property->bua=(request('BUA')) ? str_replace(',','',request('BUA')) : 0;
        $Property->view=request('View');
        $Property->usp=request('USP');
        $Property->usp2=request('USP2');
        $Property->usp3=request('USP3');
        $Property->usp4=request('USP4');
        $Property->parking=request('Parking');
        $Property->occupancy_status=request('OccupancyStatus');
        $Property->passport=request('Passport');
        $Property->title_deed=request('TitleDeed');
        $Property->form_a=request('FormA');
        $Property->eid_front=request('EIDFront');
        $Property->eid_back=request('EIDBack');
        $Property->power_of_attorney=request('PowerOfAttorney');
        $Property->visa=request('Visa');
        $Property->other_doc=request('OtherDoc');
        $Property->published=request('Published');


        $Property->maid=request('Maid');
        $Property->driver=request('Driver');
        $Property->study=request('Study');
        $Property->storage=request('Storage');
        $Property->furnished=request('Furnished');
        $Property->status2=request('Status2');

        $Property->rented_for=(request('RentedFor')) ? str_replace(',','',request('RentedFor')) : 0;
        $Property->rented_from=request('RentedFrom');
        $Property->rented_until=request('RentedUntil');
        $Property->vacating_notice=request('VacatingNotice');
        $Property->available_from=request('AvailableFrom');
        $Property->number_cheques=request('NumberCheques');
        $Property->frequency=request('Frequency');
        $Property->next_availability=request('NextAvailability');


        $Property->daily=(request('DailyPrice')) ? str_replace(',','',request('DailyPrice')) : 0;
        $Property->weekly=(request('WeeklyPrice')) ? str_replace(',','',request('WeeklyPrice')) : 0;
        $Property->monthly=(request('MonthlyPrice')) ? str_replace(',','',request('MonthlyPrice')) : 0;
        $Property->yearly=(request('YearlyPrice')) ? str_replace(',','',request('YearlyPrice')) : 0;
        $Property->property_management=request('property_management');

        if($adminAuth->type<=2 ){
            $Property->title_deed_no=request('TitleDeedNo');
            $Property->rera_permit=request('ReraPermit');
            $Property->dtcm_number=request('DTCMNumber');
            $Property->starting_date=request('StartingDate');
            $Property->expiration_date=request('ExpirationDate');
        }
        $Property->exclusive=request('Exclusive');
        $Property->featured=request('Featured');
        $Property->ask_for_price=request('AskForPrice');
        $Property->status=$status;
        $Property->title=request('Title');
        $Property->website_title=request('WebsiteTitle');
        $Property->description=request('Description');
        $Property->expected_price=(request('ExpectedPrice')) ? str_replace(',','',request('ExpectedPrice')) : 0;
        $Property->pictures=$pictures;
        $Property->video_link=request('VideoLink');
        $Property->video_360_degrees=request('Video_360_Degrees');
        $Property->viewing_arrangement=request('ViewingArrangement');

        $Property->save();

        if($afterProperty->status!=$status){
            $rfl_status=0;
            $ma_first=1;
            if($status==4){
                $PropertyStatusHistory=PropertyStatusHistory::where('property_id',$Property->id)->where('status',$status)->first();
                if($PropertyStatusHistory){
                    $ma_first=0;
                }
            }

            if($status==1){
                $PropertyStatusHistory = PropertyStatusHistory::where('property_id', $Property->id)->where('status', 11)->orderBy('id', 'DESC')->first();
                if ($PropertyStatusHistory) {
                    $PropertyStatusHistory->rfl_status=1;
                    $PropertyStatusHistory->accept_date=date('Y-m-d H:i:s');
                    $PropertyStatusHistory->save();
                }else{
                    PropertyStatusHistory::create([
                        'property_id'=>$Property->id,
                        'h_admin_id' => $adminAuth->id,
                        'status'=>11,
                        'rfl_status'=>1,
                        'accept_date'=>date('Y-m-d H:i:s'),
                        'seen'=>1
                    ]);
                }
            }

            if($afterProperty->status==11 && $status!=1){
                if($status!=11 && request('psh_id')){
                    $PropertyStatusHistory = PropertyStatusHistory::where('id', request('psh_id'))->first();
                    $PropertyStatusHistory->rfl_status=3;
                    $PropertyStatusHistory->seen=1;
                    $PropertyStatusHistory->save();
                }
            }

            PropertyStatusHistory::create([
                'property_id' => $Property->id,
                'h_admin_id' => $adminAuth->id,
                'status' => $status,
                'rfl_status' => $rfl_status,
                'ma_first' => $ma_first
            ]);
        }else{
            if($status==11 && request('psh_id')){
                $PropertyStatusHistory = PropertyStatusHistory::where('id', request('psh_id'))->first();
                $PropertyStatusHistory->rfl_status=0;
                $PropertyStatusHistory->seen=1;
                $PropertyStatusHistory->save();
            }
        }

        PropertyFeature::where('property_id', $Property->id)->delete();
        if(request('FeaturesCheck')){
            foreach ( request('FeaturesCheck') as $features_id){
                PropertyFeature::create([
                    'property_id'=>$Property->id,
                    'feature_id'=>$features_id
                ]);
            }
        }
        if($adminAuth->type<=2 ){
            PortalProperty::where('property_id', $Property->id)->delete();
            if(request('PortalCheck')){
                foreach ( request('PortalCheck') as $portal_id){
                    PortalProperty::create([
                        'portal_id'=>$portal_id,
                        'property_id'=>$Property->id
                    ]);
                }
            }
        }
        $Notification = Notification::where('parent','Property')->where('parent_id',$Property->id)->orderBy('id', 'desc')->first();
        if($status=='1' && $Notification){
            $Notification->seen=1;
            $Notification->save();
        }

        if($status=='11'){
            Notification::create([
                'type'=>'Request Listing',
                'parent'=>'Property',
                'admin_id'=>$adminAuth->id,
                'parent_id'=>$Property->id
            ]);
        }

        $beforeProperty = Property::find($id);
        $beforeProperty=json_encode($beforeProperty);
        $beforeProperty=json_decode($beforeProperty);
        foreach ( $beforeProperty as $key => $value){
            if( $key!='created_at' && $key!='updated_at' && $key!='last_activity' ){
                if($afterProperty->$key!=$value){
                    $adminAuth=\Auth::guard('admin')->user();
                    History::create([
                        'admin_id'=>$adminAuth->id,
                        'model'=>'Property',
                        'model_id'=>$id,
                        'action'=>'Update',
                        'title'=>$key,
                        'after_value'=>$afterProperty->$key,
                        'before_value'=>$value,
                    ]);
                }
            }
        }

        return redirect('/admin/property/view/'.$Property->id);
    }

    public function getHistory(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $HistoryCount= new History();
        $HistoryData=new History();

        $totalRecords = History::count();

        $dataWhere=[];

        $dataWhere[]=['model','=','Property'];
        $dataWhere[]=['model_id','=',request('property')];

        $totalRecordwithFilter=$HistoryCount->count();
        $data = array();

        #record number with filter
        if($rowperpage=='-1')
            $Records=$HistoryData->where($dataWhere)->orderBy($columnName,$columnSortOrder)->get();
        else
            $Records=$HistoryData->where($dataWhere)->orderBy($columnName,$columnSortOrder) ->skip($start)->take($rowperpage)->get();


        $obj=[];
        foreach($Records as $row){
            $admin=Admin::where('id',$row->admin_id)->first();
            $obj['admin_id']=$admin->firstname.' '.$admin->lastname;
            $obj['title']=$row->title;
            $obj['value']='<a href="javascript:void(0);" class="history-value" data-id="'.$row->id.'"
                            data-toggle="modal" data-target="#historyValueModal"><i class="feather icon-file-text"></i></a>';
            $obj['action']=$row->action;
            $obj['created_at']=$row->created_at;
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

    public function getHistoryValue(){
        $history=History::where('id',request('id'))->first();
        echo '<div class="row m-0">
                <div class="col-sm-6">
                    <h5>After</h5>
                    <div>
                    '.$history->after_value.'
                    </div>
                </div>
                <div class="col-sm-6">
                    <h5>Before</h5>
                    <div>
                    '.$history->before_value.'
                    </div>
                </div>
        </div>';
    }

    public function resizeImage($image){
        $path = storage_path('app/public/images/' . $image);

        // open an image file
        $img = Image::make($path);

        // now you are able to resize the instance
        //$img->resize(800, 600);befor
        $img->resize(1312, 894);

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
        $Property = Property::find($request->id);

        $pictures=str_replace($request->image_name.',','',$Property->pictures);
        $pictures=str_replace($request->image_name,'',$pictures);
        $pictures=rtrim($pictures,',');
        $Property->pictures=$pictures;
        $Property->save();
    }

    public function rfl_reject(Request $request){
        $request->validate([
            'psh_id'=>'required',
        ]);
        $PropertyStatusHistory = PropertyStatusHistory::find($request->psh_id);
        $PropertyStatusHistory->rfl_status=2;
        $PropertyStatusHistory->reason=request('reject_reason');
        $PropertyStatusHistory->save();

        return redirect('/admin/property/view/'.$PropertyStatusHistory->property_id);
    }

    public function duplicatePropertyCheck(Request $request){
        //$Property=Property::where('property_id', $Property->id);
        $request->validate([
            //'emirate'=>'required',
            'listing'=>'required',
            'master_project'=>'required',
            'project'=>'required',
            'vila_unit_number'=>'required',
        ]);

        //where('emirate_id',$request->emirate)->

        if($request->id=='null') {
            $property = Property::where('listing_type_id', $request->listing)->
            where('master_project_id', $request->master_project)->
            where('community_id', $request->project)->
            where('villa_number', $request->vila_unit_number)->first();
        }else{
            $propertyEdit= Property::find($request->id);
            if($propertyEdit->parent_id) {
                $property = Property::whereNotIn('id', [$request->id, $propertyEdit->parent_id])->
                whereNull('parent_id')->
                where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('villa_number', $request->vila_unit_number)->first();
            }else{
                $property = Property::where('id', '!=', $request->id)->
                whereNull('parent_id')->
                where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('villa_number', $request->vila_unit_number)->first();
            }
        }
        return $property;
    }

    public function Delete(){
        $Property = Property::find( request('Delete') );

        $PropertyNote=PropertyNote::where('property_id', $Property->id)->count();
        $ContactNote=ContactNote::where('property_id', $Property->id)->count();
        $Lead=Lead::where('property_id', $Property->id)->count();

        if($Lead>0){
            return ['r'=>'0',
                'msg'=>'Removing this property is not possible, This property has certain leads.'];
        }

        if(request('activities')!='delete') {
            if ($PropertyNote > 0 || $ContactNote > 0) {
                return ['r' => '-1',
                    'msg' => 'By deleting this property, all activities related to this property will be deleted.'];
            }
        }

        Survey::where('model', 'Property_Appointment')->where('model_id', $Property->id)->delete();
        Survey::where('model', 'Property_Viewing')->where('model_id', $Property->id)->delete();
        PropertyNote::where('property_id', $Property->id)->delete();
        ContactNote::where('property_id', $Property->id)->delete();
        History::where('model', 'Property')->where('model_id', $Property->id)->delete();
        Notification::where('parent', 'Property')->where('parent_id', $Property->id)->delete();
        $Property->delete();

        return ['r'=>'1','msg'=>''];
    }

    function change_master_project(){
        /*$old_location=DB::select('SELECT DISTINCT community.master_project_id,community_id,name FROM property,community WHERE property.community_id=community.id;');
        foreach ($old_location as $row) {
            $old_master_project = MasterProject::find($row->master_project_id);


            $new_master_project = DB::select('SELECT * FROM new_master_project WHERE emirate_id=2 AND name="' . $old_master_project->name . '"');

            echo 'old==' . $old_master_project->name . '--' . $row->name . '<br>';

            $error=0;
            if($new_master_project){

                $new_community = DB::select('SELECT * FROM new_community WHERE master_project_id=' . $new_master_project[0]->id . ' AND name="' . $row->name . '"');
                if($new_community) {
                    echo 'new==' . $new_master_project[0]->name . '--' . $new_community[0]->name . '<br><br>';
                }else{
                    echo '<span style="color:red">no project</span><br><br>';
                    $error++;
                }
            }else{
                echo '<span style="color:red">no  master project</span><br><br>';
                $error++;
            }

            if($error==0) {
                DB::select('UPDATE property SET master_project_id='.$new_master_project[0]->id.', community_id='.$new_community[0]->id.' WHERE community_id='.$row->community_id);
                echo '<span style="color:blue">update</span><br><br>';
            }
        }*/

        $old_location=DB::select('SELECT DISTINCT community.master_project_id,community_id,name FROM master_project_ol,community WHERE property.community_id=community.id;');

    }
}

