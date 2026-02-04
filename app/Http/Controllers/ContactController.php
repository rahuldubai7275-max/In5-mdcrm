<?php

namespace App\Http\Controllers;

use App\Exports\ExportContact;
use App\Imports\ContactImports;
use App\Mail\SendMail;
use App\Models\Bedroom;
use App\Models\Community;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactCategory;
use App\Models\ContactCommunity;
use App\Models\ContactNote;
use App\Models\ContactSource;
use App\Models\Admin;
use App\Models\DataCenter;
use App\Models\Deal;
use App\Models\Developer;
use App\Models\Emirate;
use App\Models\History;
use App\Models\Lead;
use App\Models\MasterProject;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\PropertyType;
use App\Models\ContactPropertyType;
use App\Models\ContactBedroom;
use App\Models\ContactMasterProject;
use App\Models\Setting;
use App\Models\Survey;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ContactController extends Controller
{
    public function Contacts(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
        return view('/admin/contact', [
            'pageConfigs' => $pageConfigs,
            'ContactSources' => $ContactSources,
            'ClientManagers' => $ClientManagers
        ]);
    }

    public function Contacts_sm(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
        return view('/admin/contact-sm', [
            'pageConfigs' => $pageConfigs,
            'ContactSources' => $ContactSources,
            'ClientManagers' => $ClientManagers
        ]);
    }

    public function GetContacts(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $orderBy=' id DESC';
        if($columnIndex){
            $orderBy=" ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $totalRecords = Contact::where('company_id',$adminAuth->company_id)->count();

        $data = array();
        // $dataWhere=[];
        $where=' WHERE contacts.company_id='.$adminAuth->company_id.' ';
        if($searchValue)
            $where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        $rowperpage=0;
        if( request('first_name') || request('client_manager') || request('client_manager_tow') || request('finance_status') || request('contact_number') || request('master_project') || request('community')
            || request('budget_from') || request('sale_budget') || request('contact_source') || request('contact_categories') || request('email_address') || request('property_type')
            || request('bedroom') || request('id') || request('deal_contact') || request('looking_for') || request('select_developer') || request('from_date') || request('to_date')
            || request('contact')=='contacts'
            || request('contact')=='buyers'
            || request('contact')=='tenants' )
            $rowperpage = request('length');

        if(request('contact')=='buyers'){
            $where.=' AND contacts.contact_category="buyer"';

            if($adminAuth->type>2)
                $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id.' )';
        }

        if(request('contact')=='tenants'){
            $where.=' AND contacts.contact_category="tenant"';

            if($adminAuth->type>2)
                $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id.' )';
        }

        if( request('status') || request('status')==0 )
            $where.=' AND contacts.status = "'.request('status').'"';

        if( request('deal_contact') )
            $where.=' AND contacts.id IN (SELECT contact_id FROM deal WHERE type='.request('deal_contact').')';

        if( request('first_name') )
            $where.=' AND CONCAT(firstname," ", lastname)  LIKE "%'.request('first_name').'%"';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('email_address') )
            $where.=' AND email LIKE "%'.request('email_address').'%"';

        if( request('contact_categories') ){
            $contact_categories=explode(',',request('contact_categories'));
            $c_cat='';
            $c_cat_arr=[];
            foreach($contact_categories as $category){
                $c_cat.='"'.$category.'",';
                $c_cat_arr[]=array_search ($category, ContactCategory);;
            }
            $where.=' AND (contact_category IN ('.rtrim($c_cat,",").') OR contacts.id IN (select contact_id FROM contact_category WHERE cat_id IN ('.join(',',$c_cat_arr).') ) )';
        }

        if( request('looking_for') ){
            $looking_for=request('looking_for');

            $where.=' AND (looking_for ='.$looking_for.' OR contacts.id IN (select contact_id FROM contact_category WHERE looking_for = '.$looking_for.' ) )';
        }

        if( request('private') ){
            $where.=' AND contacts.private="'.request('private').'"';
            $where .= ' AND contacts.admin_id=' . $adminAuth->id;
        }else{
            $where.=' AND contacts.private=0 ';
        }

        if( request('emirate') ){
            $emirate_id=request('emirate');

            $where.=' AND (emirate_id ='.$emirate_id.' OR contacts.id IN (select contact_id FROM contact_category WHERE emirate_id = '.$emirate_id.' ) )';
        }

        if( request('client_manager') )
            $where.=' AND client_manager IN ('.request('client_manager').')';

        if( request('client_manager_tow') )
            $where.=' AND client_manager_tow IN ('.request('client_manager_tow').')';

        if( request('select_developer') )
            $where.=' AND developer_id IN ('.request('select_developer').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('finance_status') )
            $where.=' AND buy_type = "'.request('finance_status').'"';

        if( request('contact_number') )
            $where.=' AND (main_number LIKE "%'.request('contact_number').'%" OR number_two LIKE "%'.request('contact_number').'%")';

//        if( request('p_type') )
//            $where.=' AND p_type = "'.request('p_type').'"';

        if( request('property_type') )
            $where.=' AND contact_property_type.property_type_id IN ('.request('property_type').')';

        if( request('master_project') )
            $where.=' AND contact_master_project.master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND contact_community.community_id IN ('.request('community').')';

        if( request('bedroom') )
            $where.=' AND contact_bedroom.bedroom_id IN ('.request('bedroom').')';

        if( request('budget_from') )
            $where.=' AND sale_budget >='.str_replace(',','',request('budget_from'));

        if( request('budget_to') )
            $where.=' AND sale_budget <='.str_replace(',','',request('budget_to'));

        if( request('contact_source') )
            $where.=' AND contact_source IN ('.request('contact_source').')';

        if( request('job_title') )
            $where.=' AND contacts.job_title_id ='.request('job_title');

        if( request('id') )
            $where.=' AND contacts.id ='.request('id');

        if( request('from_date') )
            $where.=' AND contacts.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND contacts.created_at <="'.request('to_date').' 23:59:59"';

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

        if($where==' WHERE 1 ' && request('contact')=='contacts') {
            //$where .= ' AND contacts.contact_category IN ("buyer","tenant")';

            if($adminAuth->type>2)
            $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id .')';
        }

        #record number with filter

        if($rowperpage=='-1'){
            $query="SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY contacts.".$orderBy;//FIELD(contacts.contact_category,'tenant','buyer') DESC ,
            $Records=DB::select($query);
            Session::put('contact_query', $query);
        }else{
            $query="SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY contacts.".$orderBy." limit ".$start.",".$rowperpage;//FIELD(contacts.contact_category,'tenant','buyer') DESC ,
            $Records=DB::select($query);
            Session::put('contact_query', "SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY contacts.".$orderBy);//FIELD(contacts.contact_category,'tenant','buyer') DESC ,
        }

        $totalRecordwithFilter=0;
        //if($where!=' WHERE 1 ')
            $totalRecordwithFilter=count(DB::select("SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where) );

        $b_t_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_buyer_tenant')->first();

        $obj=[];
        foreach($Records as $row){
            $ClientManager=Admin::find($row->client_manager);
            $ClientManagerTow=Admin::find($row->client_manager_tow);
            $Developer=Developer::find($row->developer_id);

            $creator=Admin::find( $row->admin_id );

            if($creator->status==2)
                $creator=Admin::where( 'type',1 )->first();

            $ContactSource=ContactSource::find($row->contact_source);
            $ContactPropertyType=ContactPropertyType::where('contact_id',$row->id)->get();
            $contactpt='';
            foreach($ContactPropertyType as $cptype){
                $PropertyType=PropertyType::find($cptype->property_type_id);
                $contactpt.=$PropertyType->name.',';
            }

            $editAction='';
            if($adminAuth->type<3 || ($row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id)) {
                $editAction = '<a target="_blank" title="Edit" href="/admin/contact-details/' . $row->id . '"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                            <a target="_blank" href="javascript:void(0);" class="show-history" title="History" data-toggle="modal" data-target="#historyModal"><i class="users-edit-icon feather icon-calendar mr-50"></i></a>';

                if($row->status==0){
                    $editAction.='<a href="javascript:void(0);" class="mr-50 contact-status" data-status="1" title="Archive">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="#626262" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                  <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                  <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" />
                                  <path d="M10 12l4 0" />
                                </svg>
                            </a>';
                }else{
                    $editAction.='<a href="javascript:void(0);" class="mr-50 contact-status" data-status="0" title="Active"> <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="#626262" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" >
                                  <path d="M8 4h11a2 2 0 1 1 0 4h-7m-4 0h-3a2 2 0 0 1 -.826 -3.822" />
                                  <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 1.824 -1.18m.176 -3.82v-7" />
                                  <path d="M10 12h2" />
                                  <path d="M3 3l18 18" />
                                </svg>
                            </a>';
                }
            }

            /*if($row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id)
                $editAction='<a href="/admin/contact-details/'.$row->id.'" title="Edit"><i class="users-edit-icon feather icon-edit-1 font-medium-3 mr-50"></i></a>
                             <a href="javascript:void(0);" class="show-history" title="History" data-toggle="modal" data-target="#historyModal"><i class="users-edit-icon feather icon-calendar mr-50"></i></a>';*/

            $Note=ContactNote::where('contact_id','=',$row->id)->latest('created_at', 'desc')->first();
            $obj['checkbox']=($adminAuth->type<=2 || ($row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id)) ? '<div class="d-inline-block checkbox">
                                    <fieldset>
                                        <label>
                                            <input type="checkbox" value="'.$row->id.'" name="contact[]">
                                        </label>
                                    </fieldset>
                                </div>' : '';

            $contactCategory=ContactCategory::where('contact_id',$row->id)->count();
            $contact_category=ucfirst($row->contact_category);
            if($contactCategory>0){
                $contact_category='<div class="preview-category"><a href="javascript:void(0);" data-id="'.$row->id.'" data-target="#contactCategoryShowModal" data-toggle="modal">'.ucfirst($row->contact_category).'</a></div>';
            }

            $full_name='';
            if(($row->contact_category=='buyer' || $row->contact_category=='tenant') &&
                $b_t_setting->status==1 && $adminAuth->type==4 &&
                !($row->client_manager == $adminAuth->id || $row->client_manager_tow == $adminAuth->id)){

                $b_t_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$row->client_manager,$row->client_manager_tow])->where('setting_id',$b_t_setting->id)->first();
                if(!$b_t_setting_admin) {


                    $last_activity=$row->created_at;

                    $contactNoteLast=ContactNote::whereIn('admin_id', [$row->client_manager,$row->client_manager_tow])->where('contact_id',$row->id)->orderBy('id','DESC')->first();
                    if($contactNoteLast){//$row->last_activity
                        $last_activity=$contactNoteLast->created_at;//$row->last_activity;
                    }

                    $today = \Carbon\Carbon::now();
                    $today = $today->format('Y/n/j H:i:s');
                    $date_two = \Carbon\Carbon::parse($last_activity);
                    $minutes = $date_two->diffInMinutes($today);
                    $hours = $date_two->diffInHours($today);
                    $days = $date_two->diffInDays($today);

                    if($b_t_setting->time_type==1 && $minutes >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                    }

                    if($b_t_setting->time_type==2 && $hours >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                    }

                    if($b_t_setting->time_type==3 && $days >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                    }
                }

            }else{
                if($adminAuth->type!=4 || $row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id){
                    $full_name=$row->firstname.' '.$row->lastname;
                }
            }

            $last_activity=$row->created_at;
            if($row->last_activity){
                $last_activity=$row->last_activity;
            }
            $today = \Carbon\Carbon::now();
            $today = $today->format('Y/n/j');
            $date_two = \Carbon\Carbon::parse($last_activity);
            $days = $date_two->diffInDays($today);

            $Deal=Deal::where('contact_id',$row->id)->first();

            $activity_contact_setting_2=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
            $activity_contact_setting_3=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

            if($days>$activity_contact_setting_3->time){//30
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-red'.(($Deal) ? '-deal' : '').'.png">';
            }

            if($days<=$activity_contact_setting_3->time){//30
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-yellow'.(($Deal) ? '-deal' : '').'.png">';
            }

            if($days<=$activity_contact_setting_2->time){//15
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-green'.(($Deal) ? '-deal' : '').'.png">';
            }



            $obj['id']=$img.$company->sample."-".$row->id;
            $obj['firstname']= $full_name;
            $obj['buy_type']=($row->buy_type)?:'N/A';
            $obj['contact_categories']=$contact_category;
            $obj['client_manager']=(($row->client_manager == $row->admin_id) ? '' : '<i class="fa fa-exchange text-primary mr-1" title="'. $creator->firstname.' '.$creator->lastname .'"></i>').(($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '');
            $obj['client_manager_tow']=($ClientManagerTow) ? $ClientManagerTow->firstname.' '.$ClientManagerTow->lastname : 'N/A';
            $obj['developer_id']=($Developer) ? $Developer->name : 'N/A';
            $obj['contact_source']=($ContactSource) ? $ContactSource->name : 'N/A';
            $obj['contact_property_type']=($contactpt) ? rtrim($contactpt,", ") : 'N/A';
            $obj['sale_budget']=($row->sale_budget) ? number_format($row->sale_budget) : 'N/A';
            $obj['last_activity']=($Note) ? date('d-m-Y',strtotime($Note->created_at)) : 'N/A';
            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);
            $obj['Action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="'.route("contact.delete").'">
                                '.$editAction.'
                                '.(($adminAuth->type==1) ? '<a href="javascript:void(0)" class="ajax-delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '' ).'
                            </div>';

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

    public function GetContacts_sm(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = 3;//request('length'); // Rows display per page
        $columnIndex = (request('order')) ? request('order')[0]['column'] : ''; // Column index
        $columnName = (request('order')) ? request('columns')[$columnIndex]['data'] : ''; // Column name
        $columnSortOrder = (request('order')) ? request('order')[0]['dir'] : ''; // asc or desc
        //$searchValue = request('search')['value']; // Search value

        $orderBy=' id DESC';
        if($columnIndex){
            $orderBy=" ".$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $totalRecords = Contact::where('company_id',$adminAuth->company_id)->count();

        $where=' WHERE company_id='.$adminAuth->company_id.' ';

        $adminAuth=\Auth::guard('admin')->user();

        if(request('contact')=='buyers'){
            $where.=' AND contacts.contact_category="buyer"';

            if($adminAuth->type>2)
                $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id.' )';
        }

        if( request('deal_contact') )
            $where.=' AND contacts.id IN (SELECT contact_id FROM deal WHERE type='.request('deal_contact').')';

        if( request('first_name') )
            $where.=' AND CONCAT(firstname," ", lastname)  LIKE "%'.request('first_name').'%"';

        if( request('last_name') )
            $where.=' AND lastname LIKE "%'.request('last_name').'%"';

        if( request('email_address') )
            $where.=' AND email LIKE "%'.request('email_address').'%"';

        if( request('contact_categories') ){
            $contact_categories=explode(',',request('contact_categories'));
            $c_cat='';
            $c_cat_arr=[];
            foreach($contact_categories as $category){
                $c_cat.='"'.$category.'",';
                $c_cat_arr[]=array_search ($category, ContactCategory);;
            }
            $where.=' AND (contact_category IN ('.rtrim($c_cat,",").') OR contacts.id IN (select contact_id FROM contact_category WHERE cat_id IN ('.join(',',$c_cat_arr).') ) )';
        }

        if( request('looking_for') ){
            $looking_for=request('looking_for');

            $where.=' AND (looking_for ='.$looking_for.' OR contacts.id IN (select contact_id FROM contact_category WHERE looking_for = '.$looking_for.' ) )';
        }

        if( request('private') ){
            $where.=' AND contacts.private="'.request('private').'"';
            $where .= ' AND contacts.admin_id=' . $adminAuth->id;
        }else{
            $where.=' AND contacts.private=0 ';
        }

        if( request('client_manager') )
            $where.=' AND client_manager IN ('.request('client_manager').')';

        if( request('client_manager_tow') )
            $where.=' AND client_manager_tow IN ('.request('client_manager_tow').')';

        if( request('select_developer') )
            $where.=' AND developer_id IN ('.request('select_developer').')';

        if( request('creator') )
            $where.=' AND admin_id IN ('.request('creator').')';

        if( request('finance_status') )
            $where.=' AND buy_type = "'.request('finance_status').'"';

        if( request('contact_number') )
            $where.=' AND (main_number LIKE "%'.request('contact_number').'%" OR number_two LIKE "%'.request('contact_number').'%")';

        if( request('property_type') )
            $where.=' AND contact_property_type.property_type_id IN ('.request('property_type').')';

        if( request('master_project') )
            $where.=' AND contact_master_project.master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND contact_community.community_id IN ('.request('community').')';

        if( request('bedroom') )
            $where.=' AND contact_bedroom.bedroom_id IN ('.request('bedroom').')';

        if( request('budget_from') )
            $where.=' AND sale_budget >='.str_replace(',','',request('budget_from'));

        if( request('budget_to') )
            $where.=' AND sale_budget <='.str_replace(',','',request('budget_to'));

        if( request('contact_source') )
            $where.=' AND contact_source IN ('.request('contact_source').')';

        if( request('job_title') )
            $where.=' AND contacts.job_title_id ='.request('job_title');

        if( request('id') )
            $where.=' AND contacts.id ='.request('id');

        if( request('from_date') )
            $where.=' AND contacts.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND contacts.created_at <="'.request('to_date').' 23:59:59"';

        if( request('select_color') ){
            $today = date('Y-m-d');

            $activity_contact_setting_2=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
            $activity_contact_setting_3=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

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

        if($where==' WHERE 1 ' && request('contact')=='contacts') {
            $where .= ' AND contacts.contact_category IN ("buyer","tenant")';

            if($adminAuth->type>2)
            $where.=' AND (client_manager='.$adminAuth->id.' OR client_manager_tow='.$adminAuth->id .')';
        }

        #record number with filter

        if($rowperpage=='-1'){
            $query="SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY FIELD(contacts.contact_category,'tenant','buyer') DESC , contacts.".$orderBy;
            $Records=DB::select($query);
            Session::put('contact_query', $query);
        }else{
            $query="SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY FIELD(contacts.contact_category,'tenant','buyer') DESC , contacts.".$orderBy." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
            Session::put('contact_query', "SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where." ORDER BY FIELD(contacts.contact_category,'tenant','buyer') DESC , contacts.".$orderBy);
        }

        $totalRecordwithFilter=0;
        if($where!=' WHERE 1 ')
            $totalRecordwithFilter=count(DB::select("SELECT DISTINCT contacts.* FROM ( ( ( ( contacts LEFT JOIN contact_property_type ON contacts.id = contact_property_type.contact_id ) LEFT JOIN contact_master_project ON contacts.id = contact_master_project.contact_id ) LEFT JOIN contact_community ON contacts.id = contact_community.contact_id ) LEFT JOIN contact_bedroom ON contacts.id = contact_bedroom.contact_id ) ".$where) );

        $b_t_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_buyer_tenant')->first();

        $data='';
        $obj=[];
        foreach($Records as $row){
            $ClientManager=Admin::find($row->client_manager);

            //$creator=Admin::where( 'type',1 )->first();

            $checkbox='';
            if($adminAuth->type<=2 || ($row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id))
                $checkbox='<input type="checkbox" class="d-none" value="'.$row->id.'" name="contact[]">';

            $contact_category=ucfirst($row->contact_category);

            $full_name='';
            $info_show=0;
            if(($row->contact_category=='buyer' || $row->contact_category=='tenant') &&
                $b_t_setting->status==1 && $adminAuth->type==4 &&
                !($row->client_manager == $adminAuth->id || $row->client_manager_tow == $adminAuth->id)){

                $b_t_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$row->client_manager,$row->client_manager_tow])->where('setting_id',$b_t_setting->id)->first();
                if(!$b_t_setting_admin) {


                    $last_activity=$row->created_at;

                    $contactNoteLast=ContactNote::whereIn('admin_id', [$row->client_manager,$row->client_manager_tow])->where('contact_id',$row->id)->orderBy('id','DESC')->first();
                    if($contactNoteLast){//$row->last_activity
                        $last_activity=$contactNoteLast->created_at;//$row->last_activity;
                    }

                    $today = \Carbon\Carbon::now();
                    $today = $today->format('Y/n/j H:i:s');
                    $date_two = \Carbon\Carbon::parse($last_activity);
                    $minutes = $date_two->diffInMinutes($today);
                    $hours = $date_two->diffInHours($today);
                    $days = $date_two->diffInDays($today);

                    if($b_t_setting->time_type==1 && $minutes >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                        $info_show=1;
                    }

                    if($b_t_setting->time_type==2 && $hours >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                        $info_show=1;
                    }

                    if($b_t_setting->time_type==3 && $days >= $b_t_setting->time){
                        $full_name=$row->firstname.' '.$row->lastname;
                        $info_show=1;
                    }
                }

            }else{
                if($adminAuth->type!=4 || $row->client_manager==$adminAuth->id || $row->client_manager_tow==$adminAuth->id){
                    $full_name=$row->firstname.' '.$row->lastname;
                    $info_show=1;
                }
            }

            $last_activity=$row->created_at;
            if($row->last_activity){
                $last_activity=$row->last_activity;
            }
            $today = \Carbon\Carbon::now();
            $today = $today->format('Y/n/j');
            $date_two = \Carbon\Carbon::parse($last_activity);
            $days = $date_two->diffInDays($today);

            $Deal=Deal::where('contact_id',$row->id)->first();

            $activity_contact_setting_2=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
            $activity_contact_setting_3=Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

            if($days>$activity_contact_setting_3->time){//30
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-red'.(($Deal) ? '-deal' : '').'.png">';
            }

            if($days<=$activity_contact_setting_3->time){//30
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-yellow'.(($Deal) ? '-deal' : '').'.png">';
            }

            if($days<=$activity_contact_setting_2->time){//15
                $img='<img style="width:25px; margin-right:3px" src="/images/imoji-green'.(($Deal) ? '-deal' : '').'.png">';
            }

            $whatsApp=($row->main_number!='+971' && $row->main_number!='' && $info_show==1) ? '<div class="col-6 text-center"><a style="padding: 6px;padding-bottom: 2px;border-radius: 3px;" data-ajax="false" class="bg-success text-white" href="https://wa.me/'.$row->main_number.'"><i class="font-medium-5 fa fa-whatsapp"></i></a></div><div class="col-6 text-center"><a class="text-success" href="tel:'.$row->main_number.'"><i class="font-medium-5 fa fa-phone"></i> Call</a></div>': '';
            $whatsApp.=(($row->main_number=='+971' || $row->main_number=='') && $row->number_two  && $info_show==1) ? '<div class="col-6 text-center"><a style="padding: 6px;padding-bottom: 2px;border-radius: 3px;" data-ajax="false" class="bg-success text-white" href="https://wa.me/'.$row->number_two.'"><i class="font-medium-5 fa fa-whatsapp"></i></a></div><div class="col-6 text-center"><a class="text-success" href="tel:/'.$row->number_two.'"><i class="font-medium-5 fa fa-phone"></i> Call</a></div>' : '';

            $data.='<div class="card mb-2 hold-box">
                    '.$checkbox.'
                    <div class="card-body p-1">
                        <div class="card-info" data-id="'.$row->id.'">
                            <p class="m-0"><b>Ref: </b>'.$img.$company->sample."-".$row->id.'</p>
                            '.(($full_name)? '<p class="m-0"><b>Name: </b>'.$full_name.'</p>' : '') .'
                            <p class="m-0"><b>Contact Category: </b>'.$contact_category.'</p>
                            <p><b>CM 1: </b>'.(($ClientManager) ? $ClientManager->firstname.' '.$ClientManager->lastname : '').'</p>
                        </div>
                        <div class="row m-0">
                            '.$whatsApp.'
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

    public function exportContacts(Request $request){
        return Excel::download(new ExportContact, 'Contacts.xlsx');
    }

    public function view(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/admin",'name'=>"Home"], ['link'=>"/admin/contacts",'name'=>"Contacts"], ['name'=>"View"]
        ];

        $Contact=Contact::find(request('id'));

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Contact || $Contact->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $ContactNote=DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, off_plan_project_id, note_subject, contact_id, note, date_at, time_at,contact_note.status, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND contact_id=".$Contact->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, null as off_plan_project_id, note_subject, contact_id, note, date_at, time_at,property_note.status, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND contact_id=".$Contact->id." ORDER BY created_at desc");//ContactNote::with('Admin')->where('contact_id','=',$Contact->id)->orderBy('id', 'desc')->get();

        if(request('reminder')){
            $rContactNote=ContactNote::find(request('reminder'));
            $rContactNote->seen=1;
            $rContactNote->save();
        }


        $Previous='';
        $Next='';
        if(Session::exists('contact_query')) {
            $Contacts = DB::select(Session::get('contact_query'));
            if ($Contacts) {
                $ContactsArray = [];
                foreach ($Contacts as $row) {
                    $ContactsArray[] = $row->id;
                }

                // return $ContactsArray;
                $array_index = array_search($Contact->id, $ContactsArray);
                $countArray = count($ContactsArray);
                $countArray--;

                $Previous = ($array_index == 0) ? '' : $ContactsArray[$array_index - 1];
                $Next = ($array_index == $countArray) ? '' : $ContactsArray[$array_index + 1];
            }
        }

        Session::put('property_where', "WHERE contact_id=".$Contact->id." ORDER BY id DESC");
        Session::get('property_where');
        if ($Contact) {
            return view('/admin/contact-view', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'Contact' => $Contact,
                'ContactNote'=>$ContactNote,
                'Previous'=>$Previous,
                'Next'=>$Next,
            ]);
        }else{
            return redirect('/admin/contacts');
        }

    }

    public function contactAjax(Request $request){
        $contact=Contact::find($request->contact);

        return $contact;//json_encode($json);
    }

    public function Register(Request $request){
//        $request->validate([
//            'Email'=>'required|unique:contacts',
//        ],
//        [
//            'Email.required' => 'Email is required',
//            'Email.unique' => 'Email is already existing',
//        ]);

        $mainNumberContact='';
        $mumberTwoContact='';
        $emailNumberContact='';
        $emailTwoContact='';

        $adminAuth=\Auth::guard('admin')->user();

        $MainNumber=request('MainNumber');
        if(request('ContactCategory')!='developer') {
            if ($MainNumber && $MainNumber != '+971')
                $mainNumberContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="' . $MainNumber . '" OR number_two="' . $MainNumber . '") LIMIT 0,1 ');
            else
                $MainNumber = null;

            if (request('NumberTwo'))
                $mumberTwoContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="' . request('NumberTwo') . '" OR number_two="' . request('NumberTwo') . '") LIMIT 0,1 ');

            if($adminAuth->type!=1){
                if ($mainNumberContact || $mumberTwoContact) {
                    Session::flash('error', 'Mobile Number is already existing');
                    return redirect()->back()->withInput($request->all());
                }
            }

            if (request('Email'))
                $emailNumberContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="' . request('Email') . '" OR email_two="' . request('Email') . '") LIMIT 0,1 ');

            if (request('EmailTwo'))
                $emailTwoContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="' . request('EmailTwo') . '" OR email_two="' . request('EmailTwo') . '") LIMIT 0,1 ');

            if ($emailNumberContact || $emailTwoContact) {
                Session::flash('error', 'Email is already existing');
                return redirect()->back()->withInput($request->all());
            }
        }

        $private=0;
        $lead_id=null;
        if(request('lead_id')) {
            $lead = Lead::find(request('lead_id'));
            $lead_id == $lead->id;
            if ($lead && $lead->private == 1) {
                $private = 1;
            }
        }

        $Contact=Contact::create([
        'lead_id'=>$lead_id ,
        'data_center_id'=>request('data_center_id')?: null ,
        'company_id'=>$adminAuth->company_id,
        'admin_id'=>$adminAuth->id,
        'private'=>$private,
        'looking_for'=>request('LookingFor'),
        'developer_id'=>request('Developer'),
        'off_plan_project_id'=>request('off_plan_project'),
        'emirate_id'=>request('Emirate'),
        'contact_category'=>request('ContactCategory'),
        'contact_source'=>request('ContactSource'),
        'client_manager'=>request('ClientManager'),
        'client_manager_tow'=>request('ClientManagerTwo'),
        'title'=>request('Title'),
        'firstname'=>request('FirstName'),
        'lastname'=>request('LastName'),
        'job_title_id'=>request('JobTitle'),
        'date_birth'=>request('DateBirth'),
        'main_number'=>$MainNumber,
        'number_two'=>request('NumberTwo'),
        'email'=>request('Email'),
        'email_two'=>request('EmailTwo'),
        'nationality'=>request('Nationalities'),
        'language'=>request('PreferredLanguage'),
        'country'=>request('Country'),
        'city'=>request('City'),
        'address'=>request('Address'),
        'sale_budget'=>(request('SaleBudget')) ? str_replace(',','',request('SaleBudget')) : null,
        'buy_type'=>request('BuyType'),
        'buyer_type'=>request('BuyerType'),
        'number_cheques'=>request('NumberCheques'),
        'move_in_day'=>request('MoveInDay'),
        'agency_name'=>request('AgencyName'),
        'p_type'=>request('P_Type'),
        'passport'=>request('Passport'),
        'eid_front'=>request('EIDFront'),
        'eid_back'=>request('EIDBack'),
        'other_doc'=>request('Other'),
        'last_activity'=>date('Y-m-d H:i:s'),
        ]);

        if(request('note')){
            $data=ContactNote::create([
                'admin_id'=>$adminAuth->id,
                'contact_id'=>$Contact->id,
                'note'=>request('note')
            ]);
        }

        if(request('PropertyType')){
            foreach ( request('PropertyType') as $property_type_id){
                ContactPropertyType::create([
                    'contact_id'=>$Contact->id,
                    'property_type_id'=>$property_type_id
                ]);
            }
        }

        if(request('MasterProject')){
            foreach ( request('MasterProject') as $master_project_id){
                ContactMasterProject::create([
                    'contact_id'=>$Contact->id,
                    'master_project_id'=>$master_project_id
                ]);
            }
        }

        if(request('Community')){
            foreach ( request('Community') as $project_id){
                ContactCommunity::create([
                    'contact_id'=>$Contact->id,
                    'community_id'=>$project_id
                ]);
            }
        }

        if(request('Bedroom')){
            foreach ( request('Bedroom') as $bedroom_id){
                ContactBedroom::create([
                    'contact_id'=>$Contact->id,
                    'bedroom_id'=>$bedroom_id
                ]);
            }
        }

        if(request('lead_id')){
            $Lead=Lead::find(request('lead_id'));
            $Lead->result_specifier=$adminAuth->id;
            //$Lead->colse_reason=request('add_reason');
            $Lead->result_date=date('Y-m-d H:i:s');
            $Lead->status=1;
            $Lead->seen=1;
            $Lead->save();
        }

        if(request('data_center_id')){
            $DataCenter=DataCenter::find(request('data_center_id'));
            $DataCenter->added_to_contact=$Contact->id;
            $DataCenter->added_to_contact_admin=$adminAuth->id;
            $DataCenter->added_to_contact_date=date('Y-m-d H:i:s');
            $DataCenter->save();
        }

        return redirect('/admin/contact/view/'.$Contact->id);
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
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
        Excel::import(new ContactImports, $file);

        return redirect()->back()->with('success', 'Excel file imported successfully!');
    }

    public function StoreByAjax(){
        // $data=Contact::create([
        //     'firstname'=>request('FirstName'),
        //     'lastname'=>request('LastName'),
        //     'main_number'=>request('MainNumber'),
        //     'email'=>request('Email'),
        // ]);

        // $request->validate([
        //     'Email'=>'required|unique:contacts',
        // ],
        // [
        //     'Email.required' => 'Email is required',
        //     'Email.unique' => '',
        // ]);

        $mainNumberContact='';
        $mumberTwoContact='';
        $emailNumberContact='';
        $emailTwoContact='';

        if(request('Email')) {
            $Contact = Contact::Where('email', request('Email'))->first();
            if ($Contact != '')
                return 'false';
        }

        $adminAuth=\Auth::guard('admin')->user();

        if ( (request('MainNumber')=='' || request('MainNumber')=='+971') && request('NumberTwo')=='' )
            return 'false1';

        $MainNumber=request('MainNumber');
        if($MainNumber && $MainNumber!='+971')
            $mainNumberContact=DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="' . $MainNumber . '" OR number_two="' . $MainNumber . '") LIMIT 0,1 ');//Contact::where('main_number',$MainNumber)->orWhere('number_two',$MainNumber)->first();
        else
            $MainNumber=null;

        if(request('NumberTwo'))
            $mumberTwoContact=DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="' . request('NumberTwo') . '" OR number_two="' . request('NumberTwo') . '") LIMIT 0,1 ');//Contact::where('main_number',request('NumberTwo'))->orWhere('number_two',request('NumberTwo'))->first();

        if($mainNumberContact || $mumberTwoContact){
            return 'false2';
        }

        if(request('Email'))
            $emailNumberContact=DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="' . request('Email') . '" OR email_two="' . request('Email') . '") LIMIT 0,1 ');//Contact::where('email',request('Email'))->orWhere('email_two',request('Email'))->first();

        if(request('EmailTwo'))
            $emailTwoContact=DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="' . request('EmailTwo') . '" OR email_two="' . request('EmailTwo') . '") LIMIT 0,1 ');//Contact::where('email',request('EmailTwo'))->orWhere('email_two',request('EmailTwo'))->first();

        if($emailNumberContact || $emailTwoContact){
            return 'false3';
        }
        $data=Contact::create([
            'admin_id'=>$adminAuth->id,
            'company_id'=>$adminAuth->company_id,
            'developer_id'=>request('Developer'),
            'contact_category'=>request('ContactCategory'),
            'contact_source'=>request('ContactSource'),
            'client_manager'=>request('ClientManager'),
            'client_manager_tow'=>request('ClientManagerTwo'),
            'title'=>request('Title'),
            'firstname'=>request('FirstName'),
            'lastname'=>request('LastName'),
            'main_number'=>request('MainNumber'),
            'number_two'=>request('NumberTwo'),
            'email'=>request('Email'),
            'email_two'=>request('EmailTwo'),
            'nationality'=>request('Nationalities'),
            'language'=>request('PreferredLanguage'),
            'country'=>request('Country'),
            'city'=>request('City'),
            'address'=>request('Address'),
            'sale_budget'=>(request('SaleBudget')) ? str_replace(',','',request('SaleBudget')) : null,
            'buy_type'=>request('BuyType'),
            'buyer_type'=>request('BuyerType'),
            'number_cheques'=>request('NumberCheques'),
            'move_in_day'=>request('MoveInDay'),
            'agency_name'=>request('AgencyName'),
            'last_activity'=>date('Y-m-d H:i:s'),
        ]);

        return $data->id;
    }

    public function SelectAjax(Request $request){
        $search=request('q');

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        if($adminAuth->type==4)
            $query="SELECT DISTINCT * FROM contacts WHERE company_id=".$adminAuth->company_id." AND (client_manager=".$adminAuth->id." OR client_manager_tow=".$adminAuth->id.") AND  (CONCAT(firstname,' ', lastname) LIKE '%$search%' OR email LIKE '%$search%' OR main_number LIKE '%$search%' OR number_two LIKE '%$search%' ) LIMIT 0,30";
        else
            $query="SELECT DISTINCT * FROM contacts WHERE company_id=".$adminAuth->company_id." AND (CONCAT(firstname,' ', lastname) LIKE '%$search%' OR email LIKE '%$search%' ) LIMIT 0,30";
        //$query="SELECT DISTINCT * FROM contacts WHERE (firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR email LIKE '%$search%' )";

        $contacts=DB::select($query);


        $json = [];
        foreach($contacts as $row){
            $picutre=\Helper::RetPhotoUser($row->photo);
            $main_number='';
            if($row->number_two){
                $main_number=$row->number_two;
            }
            if($row->main_number){
                $main_number=$row->main_number;
            }

            $email='';
            if($row->email_two){
                $email=$row->email_two;
            }
            if($row->email){
                $email=$row->email;
            }

            $json[] = ['ref'=>$company->sample.'-'.$row->id,'id'=>$row->id, 'fullname'=>$row->firstname." ".$row->lastname,
                'main_number'=>$main_number,'email'=>$email,'picutre'=>$picutre];

        }
        return json_encode($json);
    }

    public function SelectAjaxCM(Request $request){
        $search=request('q');

        $adminAuth=\Auth::guard('admin')->user();

        if($adminAuth->type==4)
            $query="SELECT DISTINCT * FROM contacts WHERE company_id=".$adminAuth->company_id." AND (client_manager=".$adminAuth->id." OR client_manager_tow=".$adminAuth->id." OR contact_category ='developer') AND  (CONCAT(firstname,' ', lastname) LIKE '%$search%' OR email LIKE '%$search%' ) LIMIT 0,30";
        else
            $query="SELECT DISTINCT * FROM contacts WHERE company_id=".$adminAuth->company_id." AND (CONCAT(firstname,' ', lastname) LIKE '%$search%' OR email LIKE '%$search%' ) LIMIT 0,30";

        $contacts=DB::select($query);

        $json = [];
        foreach($contacts as $row){
            $picutre=\Helper::RetPhotoUser($row->photo);
            $phone_number=$row->main_number;
            if($phone_number=='' || $phone_number=='+971'){
                $phone_number=$row->number_two;
            }
            $json[] = ['id'=>$row->id, 'fullname'=>$row->firstname." ".$row->lastname,
                'main_number'=>$phone_number,'email'=>$row->email,'picutre'=>$picutre];
        }
        return json_encode($json);
    }

    public function GetAjax(Request $request){
        $request->validate([
            'id'=>'required',
        ]);
        $contact=Contact::find(request('id'));
        $photo=\Helper::RetPhotoUser($contact->photo);
//        '<div class="avatar mr-1 avatar-lg">
//                    <img src="'.$photo.'">
//                </div>';

        $adminAuth=\Auth::guard('admin')->user();
        $remove='<a href="javascript:void(0)" class="remove-property-owner text-danger" title="Remove"><i class="feather icon-x"></i></a>';
        //if(request('type') && $adminAuth->type>1){
        //    $remove='';
        //}
        $outpout='<li class="list-group-item">
                <input type="hidden" name="contact" value="'.$contact->id.'">
            <div class="media">
                <div class="media-body message">
                    <span class="w-100 d-block"><b>'.$contact->firstname.' '.$contact->lastname.'</b></span>
                    <span class="w-100 d-block">'.$contact->email.'</span>
                    <div class="small d-block">'.$contact->main_number.'</div>
                </div>
                '.$remove.'
            </div>
        </li>';

        return $outpout;
    }

    public function ContactAdd(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/contacts",'name'=>"Contacts"], ['name'=>"Contact Details"]
        ];
        $Contact='';
        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        $ClientManagers=Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
        return view('/admin/contact-details', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'Contact' => $Contact,
            'ContactSources'=>$ContactSources,
            'ClientManagers'=>$ClientManagers
        ]);
    }

    public function ContactDetails(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/contacts",'name'=>"Contacts"], ['name'=>"Contact Details"]
        ];
        $Contact=Contact::find(request('id'));

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Contact || $Contact->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $offPlanProject='';
        if($Contact->off_plan_project_id){
            $company=Company::find($adminAuth->company_id);

            $url=env('MD_URL').'/api/off-plan-project/detail';

            $data=['id'=>$Contact->off_plan_project_id];
            $response=Http::withBody(json_encode($data),'application/json')->withToken($company->md_token)->post($url);

            $offPlanProject=json_decode($response);
        }

        $ContactSources=ContactSource::orderBy('name','ASC')->get();
        if ($Contact) {
            return view('/admin/contact-details', [
                'pageConfigs' => $pageConfigs,
                'breadcrumbs' => $breadcrumbs,
                'Contact' => $Contact,
                'ContactSources'=>$ContactSources,
                'offPlanProject'=>$offPlanProject,
            ]);
        }else{
            return redirect('/admin/contacts');
        }
    }

    public function action(Request $request){
        if ( request('contact') ){

            $adminAuth = \Auth::guard('admin')->user();
            $company=Company::find($adminAuth->company_id);
            $array_not_assign=[];
            $array_assigned=[];
            foreach (request('contact') as $id) {

                $afterContact = Contact::find($id);

                $Contact = Contact::find($id);
                if($Contact || $Contact->company_id==$adminAuth->company_id){
                    if($Contact->client_manager==$adminAuth->id || $adminAuth->type<3) {

                        $cm_2 = request('AssignTo');
                        $cm = request('ClientManager');
                        $cm_email='';
                        if ($cm_2) {
                            $Contact->client_manager_tow = ($cm_2 == 'null') ? null : $cm_2;
                            $cm_email=$cm_2;
                        }

                        if ($cm) {
                            $Contact->client_manager = $cm;
                            $cm_email=$cm;
                        }

                        $Contact->save();

                        $beforeContact = Contact::find($id);
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
                        $array_assigned[]=$company->sample.'-'.$Contact->id;
                    }else{
                        $array_not_assign[]=$company->sample.'-'.$Contact->id;
                    }
                }
            }
            if($array_assigned){
                $assignedTo=Admin::find($cm_email);
                if($assignedTo) {
                    $body = 'Dear ' . $assignedTo->firstname . ' ' . $assignedTo->lastname . '<br><br><br>The reference numbers listed below have been assigned to you.<br><br>';
                    $body .= join('<br>', $array_assigned);
                    $details = [
                        'subject' => 'Assigned Contact',
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
        return redirect('/admin/contacts');
    }

    public function status(Request $request){
        $adminAuth = \Auth::guard('admin')->user();
        $id = request('_id');
        $status = request('status');

        $afterContact = Contact::find($id);

        $Contact = Contact::find($id);
        $Contact->status = $status;
        $Contact->save();

        $beforeContact = Contact::find($id);
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

        return redirect('/admin/contacts');
    }

    public function Edit(Request $request){

        $request->validate([
            'ClientManager'=>'required'
        ]);

        $id=request('_id');
        $MainNumber=request('MainNumber');

        $mainNumberContact='';
        $mumberTwoContact='';
        $emailNumberContact='';
        $emailTwoContact='';

        $adminAuth=\Auth::guard('admin')->user();

        if(request('ContactCategory')!='developer') {

            if ($MainNumber && $MainNumber != '+971')
                $mainNumberContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND id!=' . $id . ' AND (main_number="' . $MainNumber . '" OR number_two="' . $MainNumber . '") LIMIT 0,1 ');
            else
                $MainNumber = null;

            if (request('NumberTwo'))
                $mumberTwoContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND id!=' . $id . ' AND (main_number="' . request('NumberTwo') . '" OR number_two="' . request('NumberTwo') . '") LIMIT 0,1 ');

            if($adminAuth->type!=1){
                if ($mainNumberContact || $mumberTwoContact) {
                    Session::flash('error', 'Mobile Number is already existing');
                    return redirect()->back()->withInput($request->all());
                }
            }

            if (request('Email'))
                $emailNumberContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND id!=' . $id . ' AND (email="' . request('Email') . '" OR email_two="' . request('Email') . '") LIMIT 0,1 ');

            if (request('EmailTwo'))
                $emailTwoContact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND id!=' . $id . ' AND (email="' . request('EmailTwo') . '" OR email_two="' . request('EmailTwo') . '") LIMIT 0,1 ');

            if ($emailNumberContact || $emailTwoContact) {
                Session::flash('error', 'Email is already existing');
                return redirect()->back()->withInput($request->all());
            }
        }

        $afterContact = Contact::find($id);

        $Contact = Contact::find($id);

        if(!$Contact || $Contact->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Contact->looking_for=request('LookingFor');
        $Contact->developer_id=request('Developer');
        $Contact->off_plan_project_id=request('off_plan_project');
        $Contact->emirate_id=request('Emirate');
        $Contact->contact_source=request('ContactSource');
        $Contact->contact_category=request('ContactCategory');
        //$Contact->client_manager=request('ClientManager');
        //$Contact->client_manager_tow=request('ClientManagerTwo');
        $Contact->title=request('Title');
        $Contact->firstname=request('FirstName');
        $Contact->lastname=request('LastName');
        $Contact->job_title_id=request('JobTitle');
        $Contact->date_birth=request('DateBirth');
        $Contact->main_number=request('MainNumber');
        $Contact->number_two=request('NumberTwo');
        $Contact->email=request('Email');
        $Contact->email_two=request('EmailTwo');
        $Contact->nationality=request('Nationalities');
        $Contact->language=request('PreferredLanguage');
        $Contact->country=request('Country');
        $Contact->city=request('City');
        $Contact->address=request('Address');
        $Contact->sale_budget=(request('SaleBudget')) ? str_replace(',','',request('SaleBudget')) : null;
        $Contact->buy_type=request('BuyType');
        $Contact->buyer_type=request('BuyerType');
        $Contact->number_cheques=request('NumberCheques');
        $Contact->move_in_day=request('MoveInDay');
        $Contact->agency_name=request('AgencyName');
        $Contact->p_type=request('P_Type');
        $Contact->passport=request('Passport');
        $Contact->eid_front=request('EIDFront');
        $Contact->eid_back=request('EIDBack');
        $Contact->other_doc=request('Other');

        $Contact->save();

        ContactPropertyType::where('contact_id', $Contact->id)->whereNull('cat_id')->delete();
        if(request('PropertyType')){
            foreach ( request('PropertyType') as $property_type_id){
                ContactPropertyType::create([
                    'contact_id'=>$Contact->id,
                    'property_type_id'=>$property_type_id
                ]);
            }
        }

        ContactMasterProject::where('contact_id', $Contact->id)->whereNull('cat_id')->delete();
        if(request('MasterProject')){
            foreach ( request('MasterProject') as $master_project_id){
                ContactMasterProject::create([
                    'contact_id'=>$Contact->id,
                    'master_project_id'=>$master_project_id
                ]);
            }
        }

        ContactCommunity::where('contact_id', $Contact->id)->whereNull('cat_id')->delete();
        if(request('Community')){
            foreach ( request('Community') as $project_id){
                ContactCommunity::create([
                    'contact_id'=>$Contact->id,
                    'community_id'=>$project_id
                ]);
            }
        }

        ContactBedroom::where('contact_id', $Contact->id)->whereNull('cat_id')->delete();
        if(request('Bedroom')){
            foreach ( request('Bedroom') as $bedroom_id){
                ContactBedroom::create([
                    'contact_id'=>$Contact->id,
                    'bedroom_id'=>$bedroom_id
                ]);
            }
        }

        $beforeContact = Contact::find($id);
        $beforeContact=json_encode($beforeContact);
        $beforeContact=json_decode($beforeContact);
        foreach ( $beforeContact as $key => $value){
            if( $key!='created_at' && $key!='updated_at' && $key!='last_activity' ){
                if($afterContact->$key!=$value){
                    $adminAuth=\Auth::guard('admin')->user();
                    History::create([
                        'admin_id'=>$adminAuth->id,
                        'model'=>'Contact',
                        'model_id'=>$id,
                        'action'=>'Update',
                        'title'=>$key,
                        'after_value'=>$afterContact->$key,
                        'before_value'=>$value,
                        ]);
                }
            }
        }
        return redirect('/admin/contact/view/'.$Contact->id);
    }

    public function getByMobileNumber(Request $request){

        $id=request('id');
        $main_number=request('main_number');
        $number_two=request('number_two');
        $email=request('email');
        $email_two=request('email_two');

        $adminAuth=\Auth::guard('admin')->user();

        if($main_number && $main_number!='+971') {
            $contact =DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="'.$main_number.'" OR number_two="'.$main_number.'") LIMIT 0,1 ');// Contact::where('main_number', $main_number)->orWhere('number_two', $main_number)->first();
            if( $contact && ($id=='' || $id!=$contact[0]->id))
                return $contact[0];
        }

        if($number_two) {
            $contact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (main_number="'.$number_two.'" OR number_two="'.$number_two.'") LIMIT 0,1 ');//Contact::where('main_number', $number_two)->orWhere('number_two', $number_two)->first();
            if( $contact && ($id=='' || $id!=$contact[0]->id))
                return $contact[0];
        }

        if($email) {
            $contact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="'.$email.'" OR email_two="'.$email.'") LIMIT 0,1 ');//Contact::where('email', $email)->orWhere('email_two', $email)->first();
            if( $contact && ($id=='' || $id!=$contact[0]->id))
                return $contact[0];
        }

        if($email_two) {
            $contact = DB::select('SELECT * FROM contacts WHERE company_id='.$adminAuth->company_id.' AND developer_id IS NULL AND (email="'.$email_two.'" OR email_two="'.$email_two.'") LIMIT 0,1 ');//Contact::where('email', $email_two)->orWhere('email_two', $email_two)->first();
            if( $contact && ($id=='' || $id!=$contact[0]->id))
                return $contact[0];
        }

    }

    public function getByEmail(Request $request){
        $request->validate([
            'email'=>'required',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $contact=Contact::where('company_id',$adminAuth->company_id)->where('email',$request->email)->first();
        return $contact;
    }

    public function getContactCatAjax(Request $request){
        $request->validate([
            'contact'=>'required',
        ]);
        $ContactCategories=ContactCategory::where('contact_id',$request->contact)->orderBy('id','DESC')->get();
        $categoryHTML='';
        foreach ($ContactCategories as $cat){
            $ContactPropertyType=ContactPropertyType::where('cat_id',$cat->id)->get();
            $contactpt='';
            foreach($ContactPropertyType as $row){
                $cPropertyType=PropertyType::find($row->property_type_id);
                $contactpt.=$cPropertyType->name.', ';
            }

            $Emirate=Emirate::find($cat->emirate_id);

            $ContactMasterProject=ContactMasterProject::where('cat_id',$cat->id)->get();
            $contactmp='';
            foreach($ContactMasterProject as $row){
                $cMasterProject=MasterProject::find($row->master_project_id);
                $contactmp.=$cMasterProject->name.', ';
            }

            $ContactCommunity=ContactCommunity::where('cat_id',$cat->id)->get();
            $contactcmun='';
            foreach($ContactCommunity as $row){
                $cCommunity=Community::find($row->community_id);
                $contactcmun.=$cCommunity->name.', ';
            }

            $ContactBedroom=ContactBedroom::where('cat_id',$cat->id)->get();
            $contactbed='';
            foreach($ContactBedroom as $row){
                $cBedroom=Bedroom::find($row->bedroom_id);
                $contactbed.=$cBedroom->name.', ';
            }

            $categoryHTML.='<li class="list-group-item">
              <p class="mb-0"><b>Created At:</b> '.\Helper::changeDatetimeFormat($cat->created_at).'</p>
              <p class="mb-0"><b>Contact Category:</b> '.ucfirst(ContactCategory[$cat->cat_id]).'</p>
              '.(($cat->looking_for) ? '<p class="mb-0"><b>Looking For:</b> '.BUYER_LOOKING_FOR[$cat->looking_for].'</p>' : '').'
              '.(($contactpt) ? '<p class="mb-0"><b>Property Type:</b> '.rtrim($contactpt,", ").'</p>' : '').'
              '.(($Emirate) ? '<p class="mb-0"><b>Emirate:</b> '.$Emirate->name.'</p>' : '').'
              '.(($contactmp) ? '<p class="mb-0"><b>Master Project:</b> '.rtrim($contactmp,", ").'</p>' : '').'
              '.(($contactcmun) ? '<p class="mb-0"><b>Project:</b> '.rtrim($contactcmun,", ").'</p>' : '').'
              '.(($contactbed) ? '<p class="mb-0"><b>Bedrooms:</b> '.rtrim($contactbed,", ").'</p>' : '').'
              '.(($cat->number_cheques) ? '<p class="mb-0"><b>No. of Cheques:</b> '.$cat->number_cheques.'</p>' : '').'
              '.(($cat->move_in_day) ? '<p class="mb-0"><b>Move in Date:</b> '.date('d-m-Y',strtotime($cat->move_in_day)).'</p>' : '').'
              '.(($cat->agency_name) ? '<p class="mb-0"><b>Agency Name:</b> '.$cat->agency_name.'</p>' : '').'
              '.(($cat->sale_budget) ? '<p class="mb-0"><b>Budget:</b> '.number_format($cat->sale_budget).'</p>' : '').'
              '.(($cat->buy_type) ? '<p class="mb-0"><b>Cash/Finance:</b> '.$cat->buy_type.'</p>' : '').'
              '.(($cat->buyer_type) ? '<p class="mb-0"><b>Investor/End-user:</b> '.$cat->buyer_type.'</p>' : '').'
             </li>';
        }

        return $categoryHTML;
    }

    public function Delete(){
        //$Contact = Contact::find( request('Delete') );
        $adminAuth=\Auth::guard('admin')->user();
        $Contact = Contact::where( 'company_id',$adminAuth->company_id )->where( 'id',request('Delete') )->first();


        $PropertyNote=PropertyNote::where('contact_id', $Contact->id)->count();
        $ContactNote=ContactNote::where('contact_id', $Contact->id)->count();
        $Property=Property::where('contact_id', $Contact->id)->count();

        if($Property>0){
            return ['r'=>'0',
                'msg'=>'Removing this contact is not possible, This contact has certain properties.'];
        }

        if(request('activities')!='delete') {
            if ($PropertyNote > 0 || $ContactNote > 0) {
                return ['r' => '-1',
                    'msg' => 'By deleting this contact, all activities related to this contact will be deleted.'];
            }
        }

        Survey::where('model', 'Contact_Appointment')->where('model_id', $Contact->id)->delete();
        Survey::where('model', 'Contact_Viewing')->where('model_id', $Contact->id)->delete();
        PropertyNote::where('contact_id', $Contact->id)->delete();
        ContactNote::where('contact_id', $Contact->id)->delete();
        History::where('model', 'Contact')->where('model_id', $Contact->id)->delete();
        Notification::where('parent', 'Contact')->where('parent_id', $Contact->id)->delete();
        $Contact->delete();

        return ['r'=>'1','msg'=>''];
    }
}

