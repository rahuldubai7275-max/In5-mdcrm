<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Bathroom;
use App\Models\Bedroom;
use App\Models\ClusterStreet;
use App\Models\Community;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactSource;
use App\Models\Emirate;
use App\Models\History;
use App\Models\MasterProject;
use App\Models\PropertyType;
use App\Models\VaastuOrientation;
use App\Models\VendorMotivation;
use App\Models\View;
use App\Models\VillaType;
use Illuminate\Http\Request;

class HistoryController extends Controller
{

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

        $dataWhere=[];

        $dataWhere[]=['model','=',request('model')];
        $dataWhere[]=['model_id','=',request('property')];
        if($searchValue)
            $dataWhere[]=['title','LIKE','%'.$searchValue.'%'];

        $totalRecords = History::where($dataWhere)->count();

        $totalRecordwithFilter=$HistoryCount->where($dataWhere)->count();
        $data = array();

        #record number with filter
        if($rowperpage=='-1')
            $Records=$HistoryData->where($dataWhere)->orderBy($columnName,$columnSortOrder)->get();
        else
            $Records=$HistoryData->where($dataWhere)->orderBy($columnName,$columnSortOrder) ->skip($start)->take($rowperpage)->get();

        $obj=[];
        $fieldTitle=['contact_id'=>'Contact',
            'listing_type_id'=>'Listing',
            'contact_source_id'=>'Contact Source',
            'vendor_motivation_id'=>'Vendor Motivation',
            'type'=>'Residential / Commercial',
            'property_type_id'=>'Property Type',
            'pf_id'=>'PF ID',
            'pf_ref'=>'PF Reference',
            'pf_error'=>'PF Error',
            'pf_location_id'=>'PF Location',
            'pf_score'=>'PF Score',
            'villa_number'=>'Villa / Unit Number',
            'emirate_id'=>'Emirate',
            'master_project_id'=>'Master Project',
            'community_id'=>'Project',
            'cluster_street_id'=>'Cluster / Street / Frond',
            'villa_type_id'=>'Type',
            'bedroom_id'=>'Bedrooms',
            'bathroom_id'=>'Bathrooms',
            'plot_sqft'=>'Plot',
            'vaastu_orientation_id'=>'Vaastu Orientation',
            'client_manager_id'=>'Client Manager',
            'client_manager2_id'=>'Client Manager 2',
            'off_plan'=>'Completion Status',
            'offplanDetails_saleType'=>'Off Plan Sale Type',
            'offplanDetails_dldWaiver'=>'Off Plan DLD Waiver',
            'offplanDetails_originalPrice'=>'Off Plan Original Price',
            'offplanDetails_amountPaid'=>'OffPlan Amount Paid',
            'completion_date'=>'Completion Date',
            'bua'=>'BUA',
            'size_for_portals'=>'For Portals',
            'view'=>'View',
            'usp'=>'USP',
            'usp2'=>'USP2',
            'usp3'=>'USP3',
            'usp4'=>'USP4',
            'parking'=>'parking',
            'occupancy_status'=>'Occupancy Status',
            'passport'=>'Passport',
            'title_deed'=>'Title Deed',
            'form_a'=>'Form A / Rental Form',
            'eid_front'=>'EID Front',
            'eid_back'=>'EID Back',
            'power_of_attorney'=>'Power of Attorney',
            'visa'=>'Visa',
            'rera_permit'=>'DLD Permit Number',
            'exclusive'=>'Exclusive',
            'published'=>'Published',
            'featured'=>'featured',
            'ask_for_price'=>'Ask For Price',
            'maid'=>"Maid's Room",
            'driver'=>"Driver Room",
            'study'=>'Study Room',
            'storage'=>'Storage Room',
            'furnished'=>'Furnished',
            'status2'=>'Status',
            'rented_for'=>'Rented For',
            'rented_from'=>'Rented From',
            'rented_until'=>'Rented Until',
            'vacating_notice'=>'Vacating Notice',
            'available_from'=>'Available From',
            'number_cheques'=>'Number Cheques',
            'frequency'=>'Frequency',
            'title_deed_no'=>'Title deed / Oqood no',
            'expiration_date'=>'Expiration Date',
            'next_availability'=>'Next Availability',
            'dtcm_number'=>'DTCM Number',
            'starting_date'=>'Starting Date',
            'daily'=>'Daily',
            'weekly'=>'Weekly',
            'monthly'=>'Monthly',
            'yearly'=>'Yearly',
            'property_management'=>'Property Management',
            'status'=>'Action',
            'title'=>'Title',
            'website_title'=>'Website Title',
            'description'=>'Description',
            'expected_price'=>'Expected Price',
            'video_link'=>'Video Link',
            'video_360_degrees'=>'Video 360 Degrees',
            'pictures'=>'Pictures',
            'land_department_qr'=>'Land Department QR code',
            'viewing_arrangement'=>'Viewing Arrangement',


            'contact_category'=>'Contact Category',
            'contact_source'=>'Contact Source',
            'client_manager'=>'Client Manager',
            'client_manager_tow'=>'Client Manager 2',
            'firstname'=>'First Name',
            'lastname'=>'Last Name',
            'date_birth'=>'Date Of Birth',
            'main_number'=>'Main Number',
            'number_two'=>'Number 2',
            'email'=>'Email',
            'email_two'=>'Email 2',
            'nationality'=>'Nationality',
            'language'=>'Language',
            'photo'=>'Photo',
            'country'=>'Country',
            'city'=>'City',
            'address'=>'Address',
            'sale_budget'=>'Budget',
            'buy_type'=>'Cash / Finance',
            'buyer_type'=>'Investor / End-user',
            'move_in_day'=>'Move In Day',
            'agency_name'=>'Agency Name',
            'p_type'=>'Residential / Commercial',
            'other_doc'=>'other document'];
        foreach($Records as $row){
            $admin=Admin::where('id',$row->admin_id)->first();

            $valueHtml='<a href="javascript:void(0);" class="history-value" data-id="'.$row->id.'"
                            data-toggle="modal" data-target="#historyValueModal"><i class="feather icon-file-text"></i></a>';
            if($row->title=='passport' || $row->title=='title_deed' || $row->title=='form_a' ||
                $row->title=='eid_front' || $row->title=='eid_back' || $row->title=='power_of_attorney' ||
                $row->title=='visa' || $row->title=='pictures' || $row->title=='photo' || $row->title=='other_doc' ){
                $row->action='Change';
                $valueHtml='';
            }
            $obj['admin_id']=$admin->firstname.' '.$admin->lastname;
            $obj['title']=$fieldTitle[$row->title];
            $obj['value']=$valueHtml;
            $obj['action']=$row->action;
            $obj['created_at']=date( 'd-m-Y H:i',strtotime($row->created_at) );
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
        $after_value=($history->after_value)?: 'N/A';
        $before_value=($history->before_value)?: 'N/A';

        if($history->title=='contact_id'){
            $contact_after_value=Contact::find($history->after_value);
            $after_value=($contact_after_value) ? $contact_after_value->firstname.' '.$contact_after_value->lastname : 'N/A';

            $contact_before_value=Contact::find($history->before_value);
            $before_value=($contact_before_value) ? $contact_before_value->firstname.' '.$contact_before_value->lastname : 'N/A';
        }

        if($history->title=='listing_type_id'){
            $after_value=ListingType[$history->after_value];
            $before_value=ListingType[$history->before_value];
        }

        if($history->title=='off_plan'){
            $after_value=OffPlan[$history->after_value];
            $before_value=OffPlan[$history->before_value];
        }

        if($history->title=='status'){
            $adminAuth=\Auth::guard('admin')->user();
            $company=Company::find($adminAuth->company_id);
            $after_value=Status[$history->after_value].( ($history->after_value==5 || $history->after_value==7)? $company->sample: '');
            $before_value=Status[$history->before_value].( ($history->after_value==5 || $history->after_value==7)? $company->sample: '');
        }

        if($history->title=='view'){
            $contact_after_value=View::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=View::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='property_management'){
            $after_value=($history->after_value) ? ($history->after_value==1) ? 'Yes' : 'No' : 'N/A';
            $before_value=($history->before_value) ? ($history->before_value==1) ? 'Yes' : 'No' : 'N/A';
        }

        if($history->title=='contact_source' || $history->title=='contact_source_id'){
            $contact_after_value=ContactSource::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=ContactSource::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='vendor_motivation_id'){
            $contact_after_value=VendorMotivation::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=VendorMotivation::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='property_type_id'){
            $contact_after_value=PropertyType::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=PropertyType::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='emirate_id'){
            $contact_after_value=Emirate::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=Emirate::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='master_project_id'){
            $contact_after_value=MasterProject::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=MasterProject::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='community_id'){
            $contact_after_value=Community::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=Community::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='cluster_street_id'){
            $contact_after_value=ClusterStreet::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=ClusterStreet::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='villa_type_id'){
            $contact_after_value=VillaType::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=VillaType::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='bedroom_id'){
            $contact_after_value=Bedroom::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=Bedroom::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='bathroom_id'){
            $contact_after_value=Bathroom::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=Bathroom::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='vaastu_orientation_id'){
            $contact_after_value=VaastuOrientation::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->name : 'N/A';

            $contact_before_value=VaastuOrientation::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->name : 'N/A';
        }

        if($history->title=='client_manager_id' ||
            $history->title=='client_manager2_id' ||
            $history->title=='client_manager' ||
            $history->title=='client_manager_tow'
        ){
            $contact_after_value=Admin::find($history->after_value);
            $after_value=($contact_after_value)? $contact_after_value->firstname.' '.$contact_after_value->lastname : 'N/A';

            $contact_before_value=Admin::find($history->before_value);
            $before_value=($contact_before_value)? $contact_before_value->firstname.' '.$contact_before_value->lastname : 'N/A';
        }
        echo '<div class="row m-0">
                <div class="col-sm-6">
                    <h5>Before</h5>
                    <div style="word-break: break-all;">
                    '.nl2br($after_value).'
                    </div>
                </div>
                <div class="col-sm-6" style="border-left:1px solid #4e4c4c">
                    <h5>After</h5>
                    <div style="word-break: break-all;">
                    '.nl2br($before_value).'
                    </div>
                </div>
        </div>';
    }

}

