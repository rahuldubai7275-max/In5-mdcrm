<?php

namespace App\Http\Controllers;

use App\Exports\ExportProperty;
use App\Mail\SendMail;
use App\Models\CommunityParent;
use App\Models\Company;
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

class PropertiesController extends Controller
{
    public function Properties(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();

        return view('/admin/properties', [
            'pageConfigs' => $pageConfigs,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
        ]);
    }

    public function Properties_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ClientManagers=Admin::where('status','1')->orderBy('firstname','ASC')->get();
        $Bedrooms=Bedroom::get();

        return view('/admin/properties-sm', [
            'pageConfigs' => $pageConfigs,
            'ClientManagers'=>$ClientManagers,
            'Bedrooms'=>$Bedrooms,
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

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $rent_price_field='';

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `property` WHERE company_id=".$adminAuth->company_id);
        $totalRecords=$totalRecords[0]->countAll;

        // $dataWhere=[];
        $addTable='';
        $where=' WHERE property.company_id='.$adminAuth->company_id.' ';

        if(request('property')=='new_listing'){
            $d30before= date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") );
            $where=',property_status_history WHERE property.id=property_status_history.property_id AND property.company_id='.$adminAuth->company_id.' AND property_status_history.status=1 AND property_status_history.created_at>="'.$d30before.'" ';

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

        if( request('listing') )
            $where.=' AND listing_type_id="'.request('listing').'"';

        if( request('status') ){
            $listing_status=request('status');
            $pf_error_select=0;
            if (str_contains($listing_status, 'pf_error,')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
                $pf_error_select++;
            }
            if (str_contains($listing_status, ',pf_error')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
                $pf_error_select++;
            }

            if (str_contains($listing_status, 'pf_error')) {
                $where.=' AND pf_error IS NOT NULL';
                $listing_status=str_contains($listing_status, 'pf_error,');
                $pf_error_select++;
            }

            if($pf_error_select==0){
                if($listing_status)
                    $where.=' AND status IN ('.$listing_status.')';
            }else{
                $where.=' AND status = "1"';
            }
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
                $orderBy=str_replace('expected_price',$rent_price_field,$orderBy);
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
            $where.=' AND property.ref_num ='.request('id');

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

            $activity_contact_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
            $activity_contact_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

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

        $ma_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_ma')->first();

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
            if($creator && $creator->status==2)
                $creator=Admin::where( 'type',1 )->first();
            $Note=PropertyNote::where('property_id','=',$row->id)->latest('created_at', 'desc')->first();

            $status='';
            if($row->status==1){
                $status='<span class="badge badge-success pf-error" data-type="2" data-pid="'.$row->id.'" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="Listed"  data-target="#ViewModal"  data-toggle="modal"></span>';// Listed

                if($row->pf_score==100)
                    $status='<span class="badge badge-white pf-error p-0" data-type="2" data-pid="'.$row->id.'" style="border-radius:50%;display: block;width: 15px;height: 15px;" title="Listed"  data-target="#ViewModal"  data-toggle="modal"><i class="fa fa-check-circle text-success" style="font-size: 18px;"></i></span>';// Listed

                if($row->pf_error!=null){
                    $status='<span class="badge badge-success pf-error" data-type="1" data-pid="'.$row->id.'" style="background-color: #95fbc3 !important;border-radius:50%;display: block;width: 15px;height: 15px;" title="Listed" data-target="#ViewModal"  data-toggle="modal"></span>';
                }
            }

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
                    >'. number_format( (($rent_price_field=='') ? $rent_price : $row->$rent_price_field) ) . '</a>';
                }else{
                    $expected_price=number_format($rent_price);
                }
            }

            $editAction='';
            $copeAction='<a href="javascript:void(0);" class="copy-property" title="Copy"><i class="users-edit-icon feather icon-copy mr-50"></i></a>';
            $historyAction='<a href="javascript:void(0);" class="show-history" title="History" data-toggle="modal" data-target="#historyModal"><i class="users-edit-icon feather icon-calendar mr-50"></i></a>';

            if( ($adminAuth->type>2 || $row->status==1) ||
                ($adminAuth->id!=$row->client_manager_id && $adminAuth->id!=$row->client_manager2_id) ) {
                $editAction = '';
            }

            if($adminAuth->type>2) {
                if ( ($adminAuth->id == $row->client_manager_id || $adminAuth->id == $row->client_manager2_id) && $row->status!=1 ) {
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
            $obj['id']=$company->sample.'-'.(($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->ref_num;
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

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `property` WHERE company_id=".$adminAuth->company_id);
        $totalRecords=$totalRecords[0]->countAll;

        // $dataWhere=[];
        $addTable='';
        $where=' WHERE property.company_id='.$adminAuth->company_id.' ';

        if(request('property')=='new_listing'){
            $d30before= date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") );
            $where=',property_status_history WHERE property.id=property_status_history.property_id AND property.company_id='.$adminAuth->company_id.' AND property_status_history.status=1 AND property_status_history.created_at>="'.$d30before.'" ';

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
            $where.=' AND property.ref_num ='.request('id');

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

            $activity_contact_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
            $activity_contact_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

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

        $ma_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_ma')->first();

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

            $img = '<img class="card-img-top" style="height: auto;max-width: 100%" src="/images/Default.png">';
            if($row->pictures)
                $img='<img class="card-img-top" style="height: auto;max-width: 100%" src="/storage/'.$pictures[0].'">';

            $cluster_street='';
            $villa_number='';
            if($ma_setting->status==1 && ($row->status==2 || $row->status==3 || $row->status==4) && $adminAuth->type==4 &&
                !($row->client_manager_id == $adminAuth->id || $row->client_manager2_id == $adminAuth->id)){
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
                    <div class="position-relative">
                    '.$img.'
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">'.$status.$company->sample.'-'.(($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->ref_num.'</h5>
                        <p class="m-0">'.(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' | '.$Community->name : '').$cluster_street.(($villa_number) ? ' | '.'No '.$villa_number : '').'</p>
                        <p class="m-0">'.(($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '').'</p>
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

        $id_key=explode('_',request('id_key'));
        $company_id=$id_key[0];
        $company_key=$id_key[1];
        $company=Company::where('id',$company_id)->where('api_key',$company_key)->first();

        if($company) {
            $portal = '';
            if (request('portal')) {
                $portal = request('portal');
                if ($portal == 'bd')
                    $where = ' ,portal_property WHERE property.id=portal_property.property_id AND company_id='.$company->id.' AND portal_property.portal_id in (2,3) and status =1';

                if ($portal == 'site')
                    $where = ' ,portal_property WHERE property.id=portal_property.property_id AND company_id='.$company->id.' AND portal_property.portal_id=4 and status =1';
            }


            if ($where == ' WHERE 1 ')
                $where .= ' AND status =1';


            $data = array();

            $Records = DB::select("SELECT DISTINCT property.* FROM `property` " . $where . " ORDER BY property.id DESC");

            $obj = [];
            foreach ($Records as $row) {
                $PropertyType = PropertyType::find($row->property_type_id);
                $Emirate = Emirate::find($row->emirate_id);
                $MasterProject = MasterProject::find($row->master_project_id);
                $Community = Community::find($row->community_id);
                $CommunityParent = CommunityParent::find($Community->parent_id);
                $VillaType = VillaType::find($row->villa_type_id);
                $Bedroom = Bedroom::find($row->bedroom_id);
                $Bathroom = Bathroom::find($row->bathroom_id);
                $View = View::find($row->view);
                $ClientManager = Admin::find($row->client_manager_id);

                $bedroomText = 'Bedrooms';
                if ($Bedroom && $Bedroom->name == '1')
                    $bedroomText = 'Bedroom';

                $bathroomText = 'Bathrooms';
                if ($Bathroom && $Bathroom->name == '1')
                    $bathroomText = 'Bathroom';

                $description = nl2br($row->description);
                $description .= '<br>';
                $description .= ($VillaType) ? '<br>* Type: ' . $VillaType->name : '';
                $description .= ($row->bua) ? '<br>* BUA: ' . number_format($row->bua) . ' Sq Ft' : '';
                $description .= ($row->plot_sqft) ? '<br>* Plot: ' . number_format($row->plot_sqft) . ' Sq Ft' : '';
                $description .= ($Bedroom) ? '<br>* ' . $Bedroom->name . (($Bedroom->name != 'Studio') ? ' ' . $bedroomText : '') : '';
                $description .= ($Bathroom && $Bathroom->name != '0') ? '<br>* ' . $Bathroom->name . ' ' . $bathroomText : '';
                $description .= ($row->maid == 'Yes') ? "<br>* Maid's Room" : '';
                $description .= ($row->study == 'Yes') ? '<br>* Study Room' : '';
                $description .= ($row->storage == 'Yes') ? '<br>* Storage Room' : '';
                $description .= ($View) ? '<br>* ' . $View->name : '';
                $description .= ($row->furnished && ($row->property_type_id != 19 && $row->property_type_id != 29)) ? '<br>* ' . $row->furnished : '';
                $description .= ($row->parking && $row->parking != '0') ? '<br>* ' . $row->parking . ' Parking' : '';
                $description .= ($row->usp) ? '<br>* ' . $row->usp : '';
                $description .= ($row->usp2) ? '<br>* ' . $row->usp2 : '';
                $description .= ($row->usp3) ? '<br>* ' . $row->usp3 : '';
                $description .= ($row->status2 && ($row->property_type_id != 19 && $row->property_type_id != 29)) ? '<br>* ' . Status2[$row->status2] : '';

                $pictures = explode(',', $row->pictures);
                $img = '';
                if ($row->pictures)
                    $img = '<div style="width: 50px;"><img style="max-width: 50px;" src="/storage/' . $pictures[0] . '"></div>';

                $expected_price = $row->expected_price;
                $Rent_Frequency = '';
                if ($row->listing_type_id == 2) {

                    if ($row->yearly) {
                        $expected_price = $row->yearly;
                        $Rent_Frequency = 'Yearly';
                    } else if ($row->monthly) {
                        $expected_price = $row->monthly;
                        $Rent_Frequency = 'Monthly';
                    } else if ($row->weekly) {
                        $expected_price = $row->weekly;
                        $Rent_Frequency = 'Weekly';
                    } else {
                        $expected_price = $row->daily;
                        $Rent_Frequency = 'Daily';
                    }
                }

                $obj['id'] = $company->sample . '-' . (($row->listing_type_id == 1) ? 'S' : 'R') . '-' . $row->ref_num;
                $obj['only_id'] = $row->id;
                $obj['dtcm_number'] = $row->dtcm_number;
                $obj['offering_type'] = (($row->type == 1) ? 'R' : 'C') . (($row->listing_type_id == 1) ? 'S' : 'R');
                $obj['listing_type'] = ListingType_XML[$row->listing_type_id];
                $obj['property_type'] = ($PropertyType) ? $PropertyType->bayut_title : '';
                $obj['property_type_site'] = ($PropertyType) ? $PropertyType->name : '';
                $obj['property_type_pf'] = ($PropertyType) ? $PropertyType->abbreviation : '';
                $obj['price_on_application'] = $row->ask_for_price;

//            if($row->ask_for_price=='No')
                if ($row->listing_type_id == 1) {
                    $obj['price'] = $row->expected_price;
                } else {
                    $rent_price = '';
                    $rent_price .= (($row->yearly) ? '<yearly>' . $row->yearly . '</yearly>' : '');
                    $rent_price .= (($row->monthly) ? '<monthly>' . $row->monthly . '</monthly>' : '');
                    $rent_price .= (($row->weekly) ? '<weekly>' . $row->weekly . '</weekly>' : '');
                    $rent_price .= (($row->daily) ? '<daily>' . $row->daily . '</daily>' : '');

                    $obj['price'] = $rent_price;
                }
//            else
//                $obj['price']='';

                $obj['Price_BD'] = $expected_price;

                $obj['service_charge'] = '';

                $obj['img'] = $img;
                $obj['status'] = 'live';//Status[$row->status];
                $obj['city'] = ($Emirate) ? $Emirate->name : '';
                $obj['locality'] = ($MasterProject) ? $MasterProject->name : '';

                $communityName = (($Community->bayut_name) ? $Community->bayut_name : $Community->name);
                if ($portal == 'pf') {
                    $communityName = $Community->name;
                }

                $obj['sub_locality'] = ($CommunityParent) ? $CommunityParent->name : $communityName;
                $obj['tower_name'] = ($CommunityParent) ? $communityName : '';

                $obj['Property_Title'] = $row->title;
                $obj['Property_Title_AR'] = '';
                $obj['Website_Title'] = $row->website_title;
                $obj['Property_Description'] = $description;
                $obj['Property_Description_AR'] = '';
                $obj['Property_Size'] = $row->bua;
                $obj['Property_Size_Unit'] = 'SQFT';
                $obj['Bedrooms'] = ($Bedroom) ? $Bedroom->name : '';
                $obj['Bathroom'] = ($Bathroom) ? $Bathroom->name : '';
                $obj['view'] = ($View) ? $View->name : '';
                $obj['Listing_Agent_id'] = ($ClientManager) ? $ClientManager->id : '';
                $obj['Listing_Agent'] = ($ClientManager) ? $ClientManager->firstname . ' ' . $ClientManager->lastname : '';
                $obj['Listing_Agent_Phone'] = ($ClientManager) ? $ClientManager->main_number : '';
                $obj['Listing_Agent_Photo'] = ($ClientManager && $ClientManager->pic_name) ? request()->getSchemeAndHttpHost() . '/storage/' . $ClientManager->pic_name : '';
                $obj['Listing_Agent_Email'] = ($ClientManager) ? $ClientManager->email : '';
                $obj['license_no'] = ($ClientManager) ? $ClientManager->rera_brn : '';
                $obj['info'] = '';
                $obj['stories'] = '';
                $obj['parking'] = $row->parking;
                $obj['number_cheques'] = $row->number_cheques;

                $Furnished = ['Furnished' => 'Yes', 'Unfurnished' => 'No', 'Semi furnished' => 'Partly'];

                $obj['furnished'] = ($row->furnished) ? $Furnished[$row->furnished] : '';
                $obj['view360'] = $row->video_360_degrees;

                $PropertyFeatures = PropertyFeature::where('property_id', $row->id)->get();
                $Features = '';
                $Facilities = '';
                $PF_Features = [];
                foreach ($PropertyFeatures as $PF) {
                    $Feature = Features::find($PF->feature_id);

                    $Features .= '<Feature><![CDATA[' . $Feature->name . ']]></Feature>';
                    $Facilities .= '<facility>' . $Feature->name . '</facility>';
                    $PF_Features[] = $Feature->abbreviation;
                }

                $obj['Features'] = $Features;
                $obj['Facilities'] = $Facilities;
                $obj['PF_Features'] = join(',', $PF_Features);
                $obj['commercial_amenities'] = '';
                $obj['plot_size'] = $row->plot_sqft;

                $pictures = explode(',', $row->pictures);
                $img = '';
                $img_site = '';
                $photo = '';
                if ($row->pictures) {
                    foreach ($pictures as $pic) {
                        $img .= '<Image><![CDATA[' . request()->getSchemeAndHttpHost() . '/storage/' . $pic . ']]></Image>';
                        $img_site .= '<image>' . request()->getSchemeAndHttpHost() . '/storage/' . $pic . '</image>';
                        $photo .= '<url last_updated="' . $row->updated_at . '">' . request()->getSchemeAndHttpHost() . '/storage/' . $pic . '</url>';
                    }
                }
                $obj['Images'] = $img;
                $obj['land_department_qr'] = ($row->land_department_qr) ? request()->getSchemeAndHttpHost() . '/storage/' . $row->land_department_qr : '';
                $obj['img_site'] = $img_site;
                $obj['photo'] = $photo;
                $obj['Video'] = $row->video_link;
                $obj['build_year'] = '';
                $obj['floor'] = '';
                $obj['floor_plan'] = '';
                $obj['geopoints'] = '';
                $obj['title_deed'] = $row->title_deed_no;
                $obj['availability_date'] = $row->available_from;
                $obj['Developer'] = '';
                $obj['project_name'] = '';
                $obj['completion_status'] = $row->off_plan;
                $obj['Last_Updated'] = $row->updated_at;
                $obj['Listing_Date'] = $row->updated_at;
                $obj['Permit_Number'] = $row->rera_permit;
                $obj['Rent_Frequency'] = $Rent_Frequency;
                $obj['status2'] = ($row->status2) ? Status2[$row->status2] : 'N/A';
                $obj['Off_Plan'] = ($row->off_plan == 'completed' || $row->off_plan == 'completed_primary' || $row->listing_type_id == 2) ? 'No' : 'Yes';
                $obj['updated_at'] = $row->updated_at;
                $obj['PreviewLink'] = request()->getSchemeAndHttpHost() . '/property/brochure/' . \Helper::idCode($row->id);


                $data[] = $obj;
                $obj = [];
            }

            if (request('portal') == 'bd') {
                return response()->view('admin/xml', [
                    'response' => $data
                ])->header('Content-Type', 'text/xml');
            } elseif (request('portal') == 'site') {
                return response()->view('admin/xml-site', [
                    'response' => $data
                ])->header('Content-Type', 'text/xml');
            }
        }
        return abort(404);
    }

    public function exportProperties(Request $request){
        return Excel::download(new ExportProperty, 'Properties.xlsx');
    }

    public function SelectAjax(Request $request){
        $search=request('q');
        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        /*$properties=Property::join('master_project', 'master_project.id', '=', 'property.master_project_id')
            ->join('community', 'community.id', '=', 'property.community_id')
            ->select('property.*', 'master_project.name as master_name', 'community.name as community_name')
            ->where('property.company_id',$adminAuth->company_id)
            ->where('property.id','LIKE','%'.$search.'%')
            ->orWhere('master_project.name','LIKE','%'.$search.'%')
            ->orWhere('community.name','LIKE','%'.$search.'%')
            ->limit(30)->get();*/
        $properties=DB::select('SELECT property.*, master_project.name as master_name, community.name as community_name FROM property, master_project, community WHERE property.master_project_id=master_project.id AND property.community_id=community.id AND property.company_id='.$adminAuth->company_id.' AND (property.id LIKE "%'.$search.'%" OR master_project.name LIKE "%'.$search.'%" OR community.name LIKE "%'.$search.'%") LIMIT 30');
        $json = [];
        foreach($properties as $row){
            $json[] = ['id'=>$row->id, 'address'=>$row->master_name." | ".$row->community_name,
                'ref'=>$company->sample.'-'.( (($row->listing_type_id==1) ? 'S' : 'R').'-'.$row->ref_num )];
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
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $Emirates=Emirate::orderBy('name','ASC')->get();
        $VendorMotivations=VendorMotivation::get();
        $Views=View::orderBy('name','ASC')->get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();
        $VaastuOrientations=VaastuOrientation::get();
        $Features=Features::where('type','=','Features')->get();
        $Amenities=Features::where('type','=','Amenities')->get();

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $propertyMax=$company->last_property_ref;
        $Property='';
        return view('/admin/property', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'ContactSources'=>$ContactSources,
            'Emirates'=>$Emirates,
            'VendorMotivations'=>$VendorMotivations,
            'Views'=>$Views,
            'Bedrooms'=>$Bedrooms,
            'Bathrooms'=>$Bathrooms,
            'VaastuOrientations'=>$VaastuOrientations,
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

        $adminAuth=\Auth::guard('admin')->user();
        if(!$Property || $Property->company_id!=$adminAuth->company_id){
            return abort(404);
        }

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
        $adminAuth=\Auth::guard('admin')->user();

        $id=request('id');
        $idDecode= \Helper::idDecode($id);
        $Property=Property::find($idDecode);

        $agent_id=\Helper::idDecode(request('a'));
        $agent=Admin::where('id',$agent_id)->first();

        if(!$adminAuth) {
            if ($agent->status == 2) {
                return abort(404);
            }
        }

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
        $property=Property::find($l_id);
        $company=Company::find($property->company_id);

        if(!$company->pf_integrate || !$company->pf_key || !$company->pf_secret)
            return 1;

        $token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);


        $pf_id=$property->pf_id;
        $reference=($property->pf_ref)? $property->pf_ref : $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num;

        if(!$pf_id){
            $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?filter[reference]='.$reference);

            if($pf_property->status()==200){
                $pf_property= json_decode($pf_property);
                if($pf_property->results)
                    $pf_id=$pf_property->results[0]->id;
            }
        }else{
            $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?filter[ids]='.$pf_id);
            $pf_property= json_decode($pf_property);

            if($pf_property->results){
                $reference= $pf_property->results[0]->reference;
            }

        }

        if($action=='delete'){
            Http::withToken($token_response->accessToken)->delete('https://atlas.propertyfinder.com/v1/listings/'.$pf_id);
            $property->pf_id=null;
            $property->pf_ref=null;
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

        $assignedTo_id=$ClientManager->pf_user_id;
        if(!$assignedTo_id){
            $agent_pf_info = Http::withToken($token_response->accessToken)
                ->get('https://atlas.propertyfinder.com/v1/users?search='.$ClientManager->email);
            $agent_pf_info= json_decode($agent_pf_info);

            $assignedTo_id= $agent_pf_info->data[0]->publicProfile->id;

            $ClientManager->pf_user_id=$assignedTo_id;
            $ClientManager->save();
        }

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
            $amounts['sale']=(int)$property->expected_price;
        }else{
            $rent_type=(($property->monthly) ? 'monthly' : '');
            $rent_type=(($property->yearly) ? 'yearly' : $rent_type);


            ($property->yearly) ? ($amounts['yearly']=(int)$property->yearly) : '';
            ($property->monthly) ? ($amounts['monthly']=(int)$property->monthly) : '';
            ($property->weekly) ? ($amounts['weekly']=(int)$property->weekly) : '';
            ($property->daily) ? ($amounts['daily']=(int)$property->daily) : '';
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


        $bedroomText='Bedrooms';
        if($Bedroom && $Bedroom->name=='1')
            $bedroomText='Bedroom';

        $bathroomText='Bathrooms';
        if($Bathroom && $Bathroom->name=='1')
            $bathroomText='Bathroom';

        $description=nl2br($property->description);
        $description.='<br />';
        $description.=($VillaType) ? '<br />* Type: '.$VillaType->name : '';
        $description.=($property->bua) ? '<br />* BUA: '.number_format($property->bua).' Sq Ft' : '';
        $description.=($property->plot_sqft) ? '<br />* Plot: '.number_format($property->plot_sqft).' Sq Ft' : '';
        $description.=($Bedroom) ? '<br />* '.$Bedroom->name.( ($Bedroom->name != 'Studio') ? ' '.$bedroomText : '' ) : '';
        $description.=($Bathroom && $Bathroom->name!='0') ? '<br />* '.$Bathroom->name.' '.$bathroomText : '';
        $description.=($property->maid=='Yes') ? "<br />* Maid's Room" : '';
        $description.=($property->study=='Yes') ? '<br />* Study Room' : '';
        $description.=($property->storage=='Yes') ? '<br />* Storage Room' : '';
        $description.=($View) ? '<br />* '.$View->name : '';
        $description.=($property->furnished && ($property->property_type_id!=19 && $property->property_type_id!=29)) ? '<br />* '.$property->furnished : '';
        $description.=($property->parking && $property->parking!='0') ? '<br />* '.$property->parking.' Parking' : '';
        $description.=($property->usp) ? '<br />* '.$property->usp : '';
        $description.=($property->usp2) ? '<br />* '.$property->usp2 : '';
        $description.=($property->usp3) ? '<br />* '.$property->usp3 : '';
        $description.=($property->status2 && ($property->property_type_id!=19 && $property->property_type_id!=29)) ? '<br />* '.Status2[$property->status2] : '';

        $json=[];
        $json['amenities']=$amenities;

        $json['assignedTo']=['id'=>(int)$assignedTo_id];

        $json['createdBy']=['id'=>(int)$assignedTo_id];

        $json['availableFrom']=$property->available_from;

        $json['bathrooms']=( ($Bathroom) ? (($Bathroom->name==0)? 'none': $Bathroom->name) : 'none' );

        if($property->type=='1' && $Bedroom)
            $json['bedrooms']=( ($PropertyType->id==14 || $PropertyType->id==28) ? '20' : ( ($Bedroom) ? strtolower($Bedroom->name) : '' ) );

        $json['category']=strtolower( PropertyType[$property->type] );

        $json['compliance']=['listingAdvertisementNumber'=>$listingAdvertisementNumber,'type'=>$compliance_type];

        $json['description']=['en'=>$description];

        if($furnished)
            $json['furnishingType']=$furnished;

        $json['location']=['id'=>(int)$property->pf_location_id];

        $json['media']=['images'=>$img
            ,'videos'=>['default'=>(($property->video_link) ? $property->video_link : "" ),'view360'=>(($property->video_360_degrees) ? $property->video_360_degrees : "" )]
        ];

        if($property->parking)
            $json['parkingSlots']=(int)$property->parking;

        $json['plotSize']=(int)$property->plot_sqft;

        $json['price']=['amounts'=>$amounts,
            'numberOfCheques'=>(int)(($property->number_cheques)?:0),'type'=>( ($property->listing_type_id==1) ? 'sale' : $rent_type )];

        $json['reference']=$reference;

        $json['size']=($property->size_for_portals==1) ? (int)$property->plot_sqft : (int)$property->bua;

        $json['title']=['en'=>$property->title];

        $json['type']=$PropertyType->abbreviation;

        $json['projectStatus']=$property->off_plan;

        $json['uaeEmirate']=strtolower($Emirate->name);

        $json['updatedAt']=str_replace(' ','T',$property->updated_at);

        //return $json;

        //return $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?draft=true&filter[reference]='.$reference);

        if($pf_id){
            $listed_response = Http::withBody(json_encode($json),'application/json')->withToken($token_response->accessToken)->put('https://atlas.propertyfinder.com/v1/listings/'.$pf_id);
        }else{
            $listed_response = Http::withBody(json_encode($json),'application/json')->withToken($token_response->accessToken)->post('https://atlas.propertyfinder.com/v1/listings');
        }

        if($listed_response->status()==200){
            $listed_response= json_decode($listed_response); //echo json_encode($listed_response);exit();

            $pf_error=null;

            $property->pf_id=$listed_response->id;
            $property->pf_score=(isset($listed_response->qualityScore->value))? $listed_response->qualityScore->value : null;

            if($listed_response->state->stage!='live') {
                $publish_response = Http::withToken($token_response->accessToken)->post('https://atlas.propertyfinder.com/v1/listings/'.$listed_response->id.'/publish');

                if($publish_response->status()==200)
                    $property->pf_error=$publish_response;
                else
                    $pf_error=$publish_response;
            }
            $property->pf_error=$pf_error;
            $property->save();

        }else{
            $property->pf_error=$listed_response;
            $property->save();
        }

    }

    public function pfFetch(){
        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);

        $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?page=4');//?filter[state]=live

        return $response= json_decode($response);

        $results=$response->results;

        $data=[];
        $ref_num=$company->last_property_ref;
        foreach($results as $row){
            $ref_num++;
            $cm_1=Admin::where('pf_user_id',$row->assignedTo->id)->first();

            $category=($row->category=='residential') ? 1 : 2;
            $PropertyType=PropertyType::where('type',$category)->where('abbreviation',$row->type)->first();

            $Bedroom=null;
            if(isset($row->bedrooms))
                $Bedroom=Bedroom::where('name',$row->bedrooms)->first();

            $Bathroom=null;
            if(isset($row->bathrooms))
                $Bathroom=Bathroom::where('name',$row->bathrooms)->first();

            $status=1;
            $listing_type_id=($row->price->type=='sale') ? 1 : 2;

            $Furnished=['furnished'=>'Furnished',
                'unfurnished'=>'Unfurnished',
                'semi-furnished'=>'Semi furnished'];
            $furnished=$Furnished[(isset($row->furnishingType)) ? $row->furnishingType : 'unfurnished' ];

            $reference=$row->reference;//explode('-',$row->reference);
            $created_at=explode('.',$row->createdAt);
            $created_at = str_replace('T', ' ', $created_at[0]);

            /*$data[]=[
                'ref_num'=>$ref_num,
                'company_id'=>$company->id,
                'admin_id' => ($cm_1)? $cm_1->id : null,
                'created_at' => $created_at,
                'listing_type_id' => $listing_type_id,
                'status' => $status,
                'title' => $row->title->en,
                'description' => $row->description->en,
                'type' => $category,
                'property_type_id' => $PropertyType->id,
                'villa_number' => (isset($row->unitNumber)) ? $row->unitNumber : null,
                'pf_id' => $row->id,
                'pf_ref' => $reference,
                'pf_location_id' => $row->location->id,
                'pf_score' => $row->qualityScore->value,
                'bedroom_id' => ($Bedroom) ? $Bedroom->id : null,
                'bathroom_id' => ($Bathroom) ? $Bathroom->id : null,
                'expected_price' => ($listing_type_id == 1) ? $row->price->amounts->sale : null,
                'client_manager_id' => ($cm_1)? $cm_1->id : null,
                'off_plan' => (isset($row->projectStatus)) ? (($row->projectStatus == 'off_plan') ? 'off_plan_primary' : 'completed') : 'completed',
                'bua' => $row->size,
                'parking' => (isset($row->parkingSlots)) ? $row->parkingSlots : null,
                'rera_permit' => ($row->compliance->type == 'rera_listing') ? $row->compliance->listingAdvertisementNumber : 0,
                'video_link' => (isset($row->media->default)) ? $row->media->default : null,
                'furnished' => $furnished,
                'available_from' => (isset($row->availableFrom)) ? $row->availableFrom : null,
                'daily' => ($row->price->type == 'daily') ? $row->price->amounts->daily : 0,
                'weekly' => ($row->price->type == 'weekly') ? $row->price->amounts->weekly : 0,
                'monthly' => ($row->price->type == 'monthly') ? $row->price->amounts->monthly : 0,
                'yearly' => ($row->price->type == 'yearly') ? $row->price->amounts->yearly : 0,
            ];*/
            //$checkProperty=Property::find( filter_var(end($reference), FILTER_SANITIZE_NUMBER_INT) );
            $checkProperty=Property::where('pf_ref',$reference)->first();

            if(!$checkProperty) {
                $Property = Property::create([
                    'ref_num'=>$ref_num,
                    'company_id'=>$company->id,
                    'admin_id' => ($cm_1)? $cm_1->id : null,
                    'created_at' => $created_at,
                    'listing_type_id' => $listing_type_id,
                    'status' => $status,
                    'title' => $row->title->en,
                    'description' => $row->description->en,
                    'type' => $category,
                    'property_type_id' => $PropertyType->id,
                    'villa_number' => (isset($row->unitNumber)) ? $row->unitNumber : null,
                    'pf_id' => $row->id,
                    'pf_ref' => $reference,
                    'pf_location_id' => $row->location->id,
                    'pf_score' => $row->qualityScore->value,
                    'bedroom_id' => ($Bedroom) ? $Bedroom->id : null,
                    'bathroom_id' => ($Bathroom) ? $Bathroom->id : null,
                    'expected_price' => ($listing_type_id == 1) ? $row->price->amounts->sale : null,
                    'client_manager_id' => ($cm_1)? $cm_1->id : null,
                    'off_plan' => (isset($row->projectStatus)) ? (($row->projectStatus == 'off_plan') ? 'off_plan_primary' : 'completed') : 'completed',
                    'bua' => $row->size,
                    'parking' => (isset($row->parkingSlots)) ? $row->parkingSlots : null,
                    'rera_permit' => ($row->compliance->type == 'rera_listing') ? $row->compliance->listingAdvertisementNumber : 0,
                    'video_link' => (isset($row->media->default)) ? $row->media->default : null,
                    'furnished' => $furnished,
                    'available_from' => (isset($row->availableFrom)) ? $row->availableFrom : null,
                    'daily' => ($row->price->type == 'daily') ? $row->price->amounts->daily : 0,
                    'weekly' => ($row->price->type == 'weekly') ? $row->price->amounts->weekly : 0,
                    'monthly' => ($row->price->type == 'monthly') ? $row->price->amounts->monthly : 0,
                    'yearly' => ($row->price->type == 'yearly') ? $row->price->amounts->yearly : 0,
                ]);

                if(isset($row->amenities)) {
                    foreach ($row->amenities as $amenitie) {
                        $Features = Features::where('abbreviation', $amenitie)->first();
                        if ($Features) {
                            PropertyFeature::create([
                                'property_id' => $Property->id,
                                'feature_id' => $Features->id
                            ]);
                        }
                    }
                }

                PortalProperty::create([
                    'portal_id' => 1,
                    'property_id' => $Property->id
                ]);

                PropertyStatusHistory::create([
                    'property_id' => $Property->id,
                    'h_admin_id' => 20,
                    'status' => 1,
                    'rfl_status' => 0,
                    'ma_first' => 1
                ]);

                $company->last_property_ref=$ref_num;
                $company->save();
            }

        }
        return $data;
    }

    public function getPFerror(Request $request){

        $property=Property::find(request('id'));
        $company=Company::find($property->company_id);
        $reference=($property->pf_ref)? $property->pf_ref : $company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num;

        if(request('type')=='1'){
            return $property->pf_error;
        }

        if(request('type')=='2'){
            $token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
            post('https://atlas.propertyfinder.com/v1/auth/token');
            $token_response= json_decode($token_response);

            $filter_name='reference';
            $filter_value=$reference;
            if($property->pf_id) {
                $filter_name = 'ids';
                $filter_value = $property->pf_id;
            }

            $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?filter['.$filter_name.']='.$filter_value);
            $pf_property= json_decode($pf_property);
            if(!$pf_property->results){
                $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?draft=true&filter['.$filter_name.']='.$filter_value);
                $pf_property= json_decode($pf_property);
            }

            $input='';
            $status='';
            $statusColor='success';
            if($pf_property->results){
                $status.='<b>Status:</b> '.$pf_property->results[0]->state->type.'<br><br>';
                if($pf_property->results[0]->state->type!='live'){
                    $statusColor='danger';
                    if(isset($pf_property->results[0]->state->reasons)){
                        $status.='<b>reasons:</b><br>';
                        foreach($pf_property->results[0]->state->reasons as $row){
                            $status.=$row->en.'<br>';
                        }
                    }
                }

                $input.='<div class="alert alert-'.$statusColor.'">
                            <ul class="m-0">
                                <li>'.$status.'</li>
                            </ul>
                        </div>';

                $score=$pf_property->results[0]->qualityScore;

                $property->pf_score=$score->value;
                $property->save();

                $scoreColor=['green'=>'4dbd43','orange'=>'#FF9F43','red'=>'#ea3934'];

                $input.='<div class="d-flex justify-content-between mb-25">
                            <div class="browser-info">
                                <p class="mb-25">Score</p>
                                <h4>'.$score->value.'</h4>
                            </div>
                            <div class="stastics-info text-right">
                                <span>100</span>
                            </div>
                        </div>
                        <div class="progress progress-bar-primary mb-2">
                            <div class="progress-bar" role="progressbar" aria-valuenow="'.$score->value.'" aria-valuemin="'.$score->value.'" aria-valuemax="100"
                                 style="width:'.$score->value.'%"></div>
                        </div>';

                $input.='<div class="table-responsive"> <table class="table">
                <thead>
                <tr>
                <th>Title</th>
                <th>Color</th>
                <th>Group</th>
                <th>Help</th>
                <th>Tag</th>
                <th>Value</th>
                <th>Weight</th>
                </tr>
                </thed>
                ';
                foreach($score->details as $key=>$value){
                    $input.='<tr>';
                    $input.='<td><b>'.$key.'</b></td>';

                    foreach($score->details->$key as $d_key=>$d_value){

                        if($d_key=='helpAr' || $d_key=='tagAr')
                            continue;

                        if($d_key=='color')
                            $input.='<td><span class="badge badge-success" style="background-color: '.$scoreColor[$score->details->$key->$d_key].' !important;border-radius:50%;display: block;width: 15px;height: 15px;"></span></td>';
                        else
                            $input.='<td>'.$score->details->$key->$d_key.'</td>';
                    }
                    $input.='</tr>';
                }
                $input.='</table></div>';
            }
            return $input;
        }

    }

    public function Store(Request $request)
    {
        $adminAuth=\Auth::guard('admin')->user();

        $company=Company::find($adminAuth->company_id);
        $pictures=null;
        if ((request('InputAttachFile'))){
            foreach (request('InputAttachFile') as $image) {

                $pictures .= $image . ',';

                $this->resizeImage($image);

                if( $company->watermark ){
                    $this->watermark($image,$company->watermark);
                }

            }
            $pictures=rtrim($pictures,',');
        }

        if(request('ClusterStreet')) {
            $duplicateProperty=Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
            where('master_project_id',request('MasterProject'))->
            where('community_id',request('Community'))->
            where('cluster_street_id', request('ClusterStreet'))->
            where('villa_number',request('VillaNumber'))->first();
        }else{
            $duplicateProperty=Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
            where('master_project_id',request('MasterProject'))->
            where('community_id',request('Community'))->
            where('villa_number',request('VillaNumber'))->first();
        }

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

        $clientManager=($adminAuth->type>2) ? $adminAuth->id :request('ClientManager');

        $status=request('Status');
        $ref_num=$company->last_property_ref;
        $ref_num++;
        $Property=Property::create([
            'ref_num'=>$ref_num,
            'company_id'=>$adminAuth->company_id,
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
            'pf_location_id'=>request('PFLocation'),
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
            'size_for_portals'=>request('ForPortals'),
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

        $company->last_property_ref=$ref_num;
        $company->save();

        $owner=Contact::find($Property->contact_id);
        $owner->last_activity=$Property->created_at;
        $owner->save();

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

        /*//if(request('PortalCheck')){
        $Portals=Portal::get();
        foreach ( $Portals as $portal){
            PortalProperty::create([
                'portal_id'=>$portal->id,
                'property_id'=>$Property->id
            ]);
        }
        //}*/

        if(request('PortalCheck')){
            foreach ( request('PortalCheck') as $portal_id){
                PortalProperty::create([
                    'portal_id'=>$portal_id,
                    'property_id'=>$Property->id
                ]);
            }
        }

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

        if($status==1){
            if( in_array( "1", request('PortalCheck') ) ){
                $this->pfListed($Property->id,'listed');
            }
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

        $company=Company::find($adminAuth->company_id);
        $ref_num=($company->last_property_ref+1);
        $Property=Property::create([
            'parent_id'=>$parent_id,
            'company_id'=>$parent_property->company_id,
            'admin_id'=>$adminAuth->id,
            'ref_num'=>$ref_num,
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
            'pf_location_id'=>$parent_property->pf_location_id,
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

        $company->last_property_ref=$ref_num;
        $company->save();

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

        if(!$Property || $Property->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        if($adminAuth->type>2 && $Property->status==1)
            return 'not this permission';

        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $Emirates=Emirate::orderBy('name','ASC')->get();
        $VendorMotivations=VendorMotivation::get();
        $Views=View::orderBy('name','ASC')->get();
        $Bedrooms=Bedroom::get();
        $Bathrooms=Bathroom::get();
        $VaastuOrientations=VaastuOrientation::get();
        $Features=Features::where('type','=','Features')->get();
        $Amenities=Features::where('type','=','Amenities')->get();

        $pf_l_id='';
        $pf_name_address='';
        if($Property->pf_location_id){
            $token_response = Http::withBody(json_encode(['apiKey'=>env('PF_KEY'),'apiSecret'=>env('PF_SECRET')]),'application/json')->
            post('https://atlas.propertyfinder.com/v1/auth/token');
            $token_response= json_decode($token_response);

            $response = Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/locations?filter[id]='.$Property->pf_location_id);
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
                'ContactSources'=>$ContactSources,
                'Emirates'=>$Emirates,
                'VendorMotivations'=>$VendorMotivations,
                'Views'=>$Views,
                'Bedrooms'=>$Bedrooms,
                'Bathrooms'=>$Bathrooms,
                'VaastuOrientations'=>$VaastuOrientations,
                'Features'=>$Features,
                'Amenities'=>$Amenities,
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
            $company=Company::find($adminAuth->company_id);
            $array_not_assign=[];
            $array_assigned=[];
            foreach (request('property') as $id) {

                $afterProperty = Property::find($id);

                $Property = Property::find($id);
                if($Property || $Property->company_id==$adminAuth->company_id){
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

                        if ($cm) {
                            if($Property->status==1){
                                $PortalProperty=PortalProperty::where('property_id', $Property->id)->where('portal_id',1)->first();
                                if( $PortalProperty ){
                                    $this->pfListed($Property->id,'listed');
                                }
                            }
                        }

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
                        $array_assigned[]=$company->sample.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num;
                    }else{
                        $array_not_assign[]=$company->sample.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num;
                    }
                }
            }
            if($array_assigned){
                $assignedTo=Admin::find($cm_email);
                if($assignedTo) {
                    $body = 'Dear ' . $assignedTo->firstname . ' ' . $assignedTo->lastname . '<br><br><br>The reference numbers listed below have been assigned to you.<br><br>';
                    $body .= join('<br>', $array_assigned);
                    $details = [
                        'subject' => 'Assigned Property',
                        'body' => $body
                    ];

                    try {
                        Mail::to($assignedTo->email)->send(new SendMail($details));
                    } catch (\Exception $e) {

                    }
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

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $pictures=null;
        if (request('InputAttachFile')){
            foreach (request('InputAttachFile') as $image) {
                $image_name = $image;
                $this->resizeImage($image);

                if( request( current( explode('.',$image) ) ) ){
                    if($company->watermark)
                        $this->watermark($image,$company->watermark);

                    $img_name=explode('.',$image);
                    $new_img_name=$img_name[0].'-'.strtotime(date('Y-m-d H:i:s')).'.'.$img_name[1];
                    Storage::move('public/images/'.$image, 'public/images/'.$new_img_name);

                    $image_name=$new_img_name;
                }

                $pictures .= $image_name . ',';
            }
            $pictures=rtrim($pictures,',');
        }

        $id=request('_id');

        $afterProperty = Property::find($id);

        $Property = Property::find($id);

        if(!$Property || $Property->company_id!=$adminAuth->company_id){
            return abort(404);
        }

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
        $Property->pf_location_id=request('PFLocation');
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
        $Property->size_for_portals=request('ForPortals');
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

        if($status==1){
            //if( in_array( "1", request('PortalCheck') ) ){
            $checkPortalProperty=PortalProperty::where('property_id', $Property->id)->where('portal_id',"1")->first();
            if($checkPortalProperty){
                $this->pfListed($Property->id,'listed');
            }
        }

        if($afterProperty->status==1 && $status!=1){
            $this->pfListed($Property->id,'delete');
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

    public function watermark($image,$watermark){
        $watermarkImage=storage_path('app/public/images/' . $watermark);//'images/watermark-logo.png';
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

        $adminAuth=\Auth::guard('admin')->user();
        if(!$Property || $Property->company_id!=$adminAuth->company_id){
            return abort(404);
        }

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

        $adminAuth=\Auth::guard('admin')->user();

        if($request->id=='null') {
            if(request('cluster_street')) {
                $property = Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('cluster_street_id', $request->cluster_street)->
                where('villa_number', $request->vila_unit_number)->first();
            }else{
                $property = Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('villa_number', $request->vila_unit_number)->first();
            }
        }else{
            $propertyEdit= Property::find($request->id);
            if($propertyEdit->parent_id) {
                $property = Property::whereNotIn('id', [$request->id, $propertyEdit->parent_id])->
                whereNull('parent_id')->
                where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('villa_number', $request->vila_unit_number)->first();
            }else{
                $property = Property::where('id', '!=', $request->id)->
                whereNull('parent_id')->
                where('company_id', $adminAuth->company_id)->where('listing_type_id', $request->listing)->
                where('master_project_id', $request->master_project)->
                where('community_id', $request->project)->
                where('villa_number', $request->vila_unit_number)->first();
            }
        }
        return $property;
    }

    public function Delete(){
        $Property = Property::find( request('Delete') );

        $adminAuth=\Auth::guard('admin')->user();
        if(!$Property || $Property->company_id!=$adminAuth->company_id){
            return abort(404);
        }

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
        /*//$old_location=DB::select("SELECT DISTINCT master_project_old.emirate_id, master_project_old.name as 'master_project_name', community_old.name as 'community_name', community_old.master_project_id ,cluster_street.community_id  FROM cluster_street, community_old, master_project_old WHERE cluster_street.community_id=community_old.id AND community_old.master_project_id=master_project_old.id");

        $old_location=DB::select("SELECT DISTINCT master_project_old.emirate_id, master_project_old.name as 'master_project_name', community_old.name as 'community_name', community_old.master_project_id ,villa_type.community_id  FROM villa_type, community_old, master_project_old WHERE villa_type.community_id=community_old.id AND community_old.master_project_id=master_project_old.id");
        foreach ($old_location as $row) {

            echo 'old=='.$row->community_id.'--master_project_name->' . $row->master_project_name . '--' . $row->community_name.'<br>';

            $new_master_project = MasterProject::where('emirate_id',$row->emirate_id)->where('name',$row->master_project_name)->first();
            $new_community = Community::where('master_project_id',$new_master_project->id)->where('name',$row->community_name)->first();

            echo 'new=='.$new_community->id.'--master_project_name->' . $new_master_project->name . '--' . $new_community->name.'<br><br>';

            //DB::select('UPDATE cluster_street SET community_id='.$new_community->id.' WHERE community_id='.$row->community_id);//cluster_street


            DB::select('UPDATE villa_type SET community_id='.$new_community->id.' WHERE community_id='.$row->community_id);//villa_type
        }*/

    }

    public function pfChech(){

        $property=Property::find(request('id'));
        $company=Company::find($property->company_id);

        //$token_response = Http::withBody(json_encode(['apiKey'=>$company->pf_key,'apiSecret'=>$company->pf_secret]),'application/json')->
        $token_response = Http::withBody(json_encode(['apiKey'=>'ySwgr.hqlnQzLQ5oRNuAzSEhMNKgCGbI6JIJVi2d','apiSecret'=>'qcj4LMI8Y90t3xOkE77b7M3SmRuHyNly']),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);

        $pf_id=$property->pf_id;
        $reference='OlympicPark-70k';//$company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num;


        $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?filter[reference]='.$reference);
        $pf_property= json_decode($pf_property);
        if(!$pf_property->results){
            $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?draft=true&filter[reference]='.$reference);
            $pf_property= json_decode($pf_property);
        }

        //return Http::withToken($token_response->accessToken)->delete('https://atlas.propertyfinder.com/v1/listings/01K9VW6RYKSBZRJEWV4QZC5NQJ');

        return $pf_property;
    }

    public function pfStatus(){
        $token_response = Http::withBody(json_encode(['apiKey'=>env('PF_KEY'),'apiSecret'=>env('PF_SECRET')]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);

        $draft_archived_filter='';

        if(request('type')=='draft')
            $draft_archived_filter='draft=true&';

        if(request('type')=='archived')
            $draft_archived_filter='archived=true&';

        $pf_property=Http::withToken($token_response->accessToken)->get('https://atlas.propertyfinder.com/v1/listings?'.$draft_archived_filter.'filter[state]='.request('type'));
        $pf_property= json_decode($pf_property);
        if($pf_property->results) {
            $results=$pf_property->results;
            $input='<ul class="list-group list-group-flush">';
            foreach ($results as $row) {
                $p_ref = explode('-', $row->reference);
                $property = Property::find(end($p_ref));
                if ($property) {
                    $input .= '<li class="list-group-item">' . $row->reference . '<br>';
                    if (isset($row->state->reasons)) {
                        $input .= '<b>reasons:</b><br>';
                        foreach ($row->state->reasons as $reason) {
                            $input .= $reason->en . '<br>';
                        }
                    }
                    $input .= '</li>';
                }
                $input .= '</ul>';
            }
        }else{
            $input='<p class="text-center">No Data</p>';
        }
        return $input;
    }
}

