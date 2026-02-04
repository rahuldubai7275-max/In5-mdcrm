<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use App\Models\ClusterStreet;
use App\Models\Community;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactSource;
use App\Models\Deal;
use App\Models\Admin;
use App\Models\DealAgent;
use App\Models\DealDocument;
use App\Models\DealModel;
use App\Models\DealTrackDefault;
use App\Models\DealTracking;
use App\Models\History;
use App\Models\MasterProject;
use App\Models\Property;
use App\Models\VillaType;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class DealController extends Controller
{
    public function deals(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $deals='';
        return view('/admin/deals', [
            'pageConfigs' => $pageConfigs,
            'contacts' => $deals
        ]);
    }

    public function getDeals(){
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
            $orderBy=$columnName." ".$columnSortOrder;
        }

        $adminAuth=\Auth::guard('admin')->user();

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `deal` WHERE company_id=".$adminAuth->company_id);
        $totalRecords=$totalRecords[0]->countAll;

        // $dataWhere=[];
        $where='AND deal.company_id='.$adminAuth->company_id.' ';
        $table_deal_agents='';
        if( request('agent') ) {
            $where .= ' AND deal.id=deal_agents.deal_id AND deal_agents.agent_id= "' . request('agent') . '"';
            $table_deal_agents=',deal_agents';
        }

        if($searchValue)
            $where.=' AND (title LIKE "%'.$searchValue.'%" OR description LIKE "%'.$searchValue.'%")';

        if( request('id') )
            $where.=' AND deal.id="'.request('id').'"';

        if( request('type') )
            $where.=' AND deal.type="'.request('type').'"';

        if( request('source') )
            $where.=' AND contacts.contact_source="'.request('source').'"';

        $today = date('Y-m-d');
        if( request('reminder') ) {
            $reminder=request('reminder');

            if($reminder==2)
                $days=1;
            elseif($reminder==3)
                $days=7;
            elseif($reminder==4)
                $days=30;
            elseif($reminder==5)
                $days=60;
            else
                $days=90;

            $where .= ' AND deal.acknowledge=0 AND deal.set_reminder="' . $reminder . '" AND deal.tenancy_renewal_date="' . date('Y-m-d', strtotime($today . "+ ".$days." days")) . '"';
        }
        if( request('emirate') )
            $where.=' AND property.emirate_id IN ('.request('emirate').')';

        if( request('master_project') )
            $where.=' AND property.master_project_id IN ('.request('master_project').')';

        if( request('community') )
            $where.=' AND property.community_id IN ('.request('community').')';

        if( request('unit_villa_number') )
            $where.=' AND property.villa_number="'.request('unit_villa_number').'"';

        if( request('property_management') )
            $where.=' AND deal.property_management="'.request('property_management').'"';

        if( request('from_price') )
            $where.=' AND deal_price >="'.str_replace(',','',request('from_price')).'"';

        if( request('to_price') )
            $where.=' AND deal_price <="'.str_replace(',','',request('to_price')).'"';

        if( request('from_commission') )
            $where.=' AND deal.commission >="'.str_replace(',','',request('from_commission')).'"';

        if( request('to_commission') )
            $where.=' AND deal.commission <="'.str_replace(',','',request('to_commission')).'"';

        if( request('buyer_tenant') )
            $where.=' AND CONCAT(contacts.firstname," ",contacts.firstname) LIKE "%'.request('buyer_tenant').'%"';

        if( request('to_commission') )
            $where.=' AND deal.commission <="'.str_replace(',','',request('to_commission')).'"';

        if( request('buyer_tenant') )
            $where.=' AND CONCAT(contacts.firstname," ",contacts.firstname) LIKE "%'.request('buyer_tenant').'%"';

        if( request('from_date') )
            $where.=' AND deal.created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND deal.created_at <="'.request('to_date').' 23:59:59"';

        if( request('status') )
            $where.=' AND deal.status ='.request('status');
        else
            $where.=' AND deal.status=1';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM deal,property,contacts".$table_deal_agents." WHERE deal.property_id=property.id AND deal.contact_id=contacts.id ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT deal.*,contacts.contact_source, property.listing_type_id, CONCAT(contacts.firstname,' ',contacts.lastname) AS buyer_Tenant, property.master_project_id, property.community_id, property.villa_number FROM deal,property,contacts".$table_deal_agents." WHERE deal.property_id=property.id AND deal.contact_id=contacts.id ".$where." ORDER BY ".$orderBy);
        }else{
            $Records=DB::select("SELECT deal.*,contacts.contact_source, property.listing_type_id, CONCAT(contacts.firstname,' ',contacts.lastname) AS buyer_Tenant, property.master_project_id, property.community_id, property.villa_number FROM deal,property,contacts".$table_deal_agents." WHERE deal.property_id=property.id AND deal.contact_id=contacts.id ".$where." ORDER BY ".$orderBy." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $MasterProject=MasterProject::where('id',$row->master_project_id)->first();
            $Community=Community::where('id',$row->community_id)->first();
            $ContactSource=ContactSource::where('id',$row->contact_source)->first();

            $dealAgents=DealAgent::where('deal_id',$row->id)->orderBy('id', 'asc')->get();
            $agentHtml='';
            $countDealAgents=count($dealAgents);
            $i=0;
            foreach ($dealAgents as $dAgent){
                $i++;
                $agent=Admin::where('id',$dAgent->agent_id)->first();
                $agentHtml.=$agent->firstname.' '.$agent->lastname.' / '.$dAgent->percent.'%';
                if($countDealAgents!=$i)
                    $agentHtml.='<br>';
            }
            if($row->set_reminder==2)
                $sr_days=1;
            elseif($row->set_reminder==3)
                $sr_days=7;
            elseif($row->set_reminder==4)
                $sr_days=30;
            elseif($row->set_reminder==5)
                $sr_days=60;
            elseif($row->set_reminder==6)
                $sr_days=90;
            else
                $sr_days=0;

            $obj['id']='D-'.$row->id;
            $obj['type']=($row->type==1) ? 'Rental' : 'Sales';
            $obj['property_address']=$MasterProject->name.' | '.$Community->name.' | '.$row->villa_number;
            $obj['buyer_Tenant']=$row->buyer_Tenant;
            $obj['deal_price']=number_format($row->deal_price);
            $obj['agent']=$agentHtml;
            $obj['deal_date']=($row->deal_date) ? date('d-m-Y',strtotime($row->deal_date)) : '';
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);
            $obj['commission']=number_format($row->commission);
            $obj['contact_source']=($ContactSource)? $ContactSource->name : '';
            $obj['action']='<div class="d-flex action font-medium-3" data-id="'.$row->id.'" data-model="'.route("deal.delete").'" data-acknowledge="'.route("deal.acknowledge").'">
                            <!--<a href="/admin/deal-edit/'.$row->id.'" target="_blank" class="edit-field-study" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>-->

                            '.(($adminAuth->type<2 && $row->status==1)? '<a href="#disabledDealModal" data-toggle="modal" class="deal-disabled" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>' : '').'
                          </div>';
            //( ($row->acknowledge==0 && $row->set_reminder>1 && $row->tenancy_renewal_date >= date('Y-m-d', strtotime($today . "+ ".$sr_days." days")) ) ? '<a href="javascript:void(0);" data-toggle="modal" class="btn btn-outline-primary waves-effect waves-light mx-1 font-medium-1 acknowledge">Acknowledge</a>' : '' )
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

    public function dealAdd(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/deals",'name'=>"Deals"], ['name'=>"Add Deal"]
        ];
        $deal='';

        $deal_access=2;
        if(env('DEAL_ACCESS')!='0')
            $deal_access=5;

        $adminAuth=\Auth::guard('admin')->user();
        if($adminAuth->type>$deal_access)
            return 'not this permission';


        return view('/admin/deal', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'deal' => $deal,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'contact'=>'required',
            'property'=>'required',
            'deal_price'=>'required'
        ]);

        $adminAuth=\Auth::guard('admin')->user();
        $commission=str_replace(',','',request('commission'));
        $deal_model_id=request('deal_model');
        $contact_id=request('contact');
        $property_id=request('property');
        $send_email=request('send_email')? 1:0;
        $type=request('type');
        $company_percent=request('company_commission_percent');
        $deal=Deal::create([
            'company_id'=>$adminAuth->company_id,
            'admin_id'=>$adminAuth->id,
            'property_id'=>$property_id,
            'contact_id'=>$contact_id,
            'type'=>$type,
            'deal_model_id'=>$deal_model_id,
            'tenancy_contract_start_date'=>request('tenancy_contract_start_date'),
            'tenancy_renewal_date'=>request('tenancy_renewal_date'),
            'cheques'=>request('cheques'),
            'set_reminder'=>request('set_reminder'),
            'deal_price'=>str_replace(',','',request('deal_price')),
            'commission'=>$commission,
            'company_percent'=>request('company_commission_percent'),
            'company_commission'=>( ($commission/100)*$company_percent ),
            'property_management'=>request('property_management'),
            'deal_date'=>request('deal_date'),
            'send_email'=>$send_email
        ]);

        for ( $i=1;$i<=3;$i++){
            if(request('agent_'.$i)) {
                $percent=request('agent_' . $i . '_commission_percent');
                DealAgent::create([
                    'deal_id' => $deal->id,
                    'agent_id' => request('agent_' . $i),
                    'percent' => $percent,
                    'commission' => ( ($commission/100)*$percent )
                ]);
            }
        }

        if(request('deal_doc')) {
            $i=0;
            foreach ( request('deal_doc') as $deal_doc) {
                DealDocument::create([
                    'deal_id' => $deal->id,
                    'type' => request('document_type')[$i],
                    'name' => request('document_name')[$i],
                    'docname' => $deal_doc
                ]);
                $i++;
            }
        }

        /*$DealTrackDefault = DealTrackDefault::where('deal_model_id',$deal_model_id)->orderBy('row','ASC')->get();
        $i=0;
        foreach ($DealTrackDefault as $row){
            $i++;
            DealTracking::create([
                'deal_id'=>$deal->id,
                'row'=>$i,
                'title'=>$row->title,
            ]);
        }

        $i++;
        $titleTracking=($type==1) ? 'Completed' : 'Transfer completed';
        DealTracking::create([
            'deal_id'=>$deal->id,
            'type'=>1,
            'row'=>$i,
            'title'=>$titleTracking,
        ]);

        if($send_email==1){
            $contact=Contact::find($contact_id);
            $dealModel=DealModel::find($deal_model_id);
            $company=Company::find(1);
            $TrackingLink='<a href="'.request()->getSchemeAndHttpHost().'/tracking/'.$deal->id.'" target="_blank">'.request()->getSchemeAndHttpHost().'/tracking/'.$deal->id.'</a>';
            $email_content=str_replace('$#TrackingLink',$TrackingLink,$dealModel->email_content);

            $details = [
                'subject' => 'Congratulation',
                'body' => nl2br($email_content),
                'from_email' => $company->primary_email,
                'from_name' => $company->name
            ];
            try {
                Mail::to($contact->email)->send(new SendMail($details));
            }catch (\Exception $e){

            }
            $property=Property::find($property_id);
            $owner=Contact::find($property->contact_id);
            try {
                Mail::to($owner->email)->send(new SendMail($details));
            }catch (\Exception $e){

            }
        }*/

        return redirect('/admin/deals');
    }

    public function dealDetails(){
        $pageConfigs = [
            'pageHeader' => true
        ];

        $breadcrumbs = [
            ['link'=>"/",'name'=>"Home"], ['link'=>"/admin/deals",'name'=>"Deals"], ['name'=>"View Deal"]
        ];

        $deal=Deal::find(request('id'));

        $adminAuth = \Auth::guard('admin')->user();
        if(!$deal || $deal->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        return view('/admin/deal-view', [
            'pageConfigs' => $pageConfigs,
            'breadcrumbs' => $breadcrumbs,
            'deal' => $deal,
        ]);
    }

    public function acknowledge(Request $request){
        $id=request('_id');

        $Deal = Deal::find($id);

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Deal || $Deal->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Deal->acknowledge=1;
        $Deal->save();
        return redirect('/admin/deals');
    }

    public function editDeal(Request $request){
        $id=request('update');

        //$afterDeal = Deal::find($id);

        $Deal = Deal::find($id);

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Deal || $Deal->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $commission=str_replace(',','',request('commission'));

        $Deal->property_id=request('property');
        $Deal->contact_id=request('contact');
        $Deal->type=request('type');
        $Deal->tenancy_contract_start_date=request('tenancy_contract_start_date');
        $Deal->tenancy_renewal_date=request('tenancy_renewal_date');
        $Deal->cheques=request('cheques');
        $Deal->set_reminder=request('set_reminder');
        $Deal->deal_price=str_replace(',','',request('deal_price'));
        $Deal->commission=$commission;
        $Deal->property_management=request('property_management');

        $Deal->save();
        return redirect('/admin/deals');
    }

    public function dealPropertyContact(){
        $property_id=request('property');
        $contact_id=request('contact');
        $property = Property::find( $property_id );
        $owner = Contact::find( $property->contact_id );
        $buyer_tenant = Contact::find( $contact_id );

        $company=Company::find( $property->company_id );

        $pictures=explode(',', $property->pictures);
        $img_src='';
        if($property->pictures)
            $img_src=$pictures[0];

        $mc=0;
        $expected_price=0;
        if($property->expected_price){
            $expected_price=$property->expected_price;
            $mc= $property->bua==0 ? 0 : ($property->expected_price/$property->bua) ;
        }

        if($property->listing_type_id==2){
            if($property->yearly){
                $expected_price=$property->yearly;
            }else if($property->monthly){
                $expected_price=$property->monthly;
            }else if($property->weekly){
                $expected_price=$property->weekly;
            }else{
                $expected_price=$property->daily;
            }
        }

        $MasterProject=MasterProject::where('id',$property->master_project_id)->first();
        $Community=Community::find($property->community_id);
        $ClusterStreet=ClusterStreet::find($property->cluster_street_id);
        $VillaType=VillaType::where('id',$property->villa_type_id)->first();

        echo '<img class="mr-2 rounded" width="70" height="70" src="/storage/'.$img_src.'">
            <div class="text-xl">
                <small>
                    <P class="mb-0">Reference: '.$company->sample.'-'.(($property->listing_type_id==1) ? "S" : "R").'-'.$property->ref_num.'</P>
                    <P class="mb-0">
                    '.(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' '.$Community->name : ''). (  (($ClusterStreet) ? ' '.$ClusterStreet->name : '').(($property->villa_number) ? ' '.'No '.$property->villa_number : '') ).' <br> AED '.number_format($expected_price).'
                    </P>
                    <hr class="my-0" style="border: 1px solid gray;">
                    <p class="mb-0"><b>Owner</b></p>
                    <p class="mb-0">'.$owner->firstname.' '.$owner->lastname.'</p>
                    <p class="mb-0">'.$company->sample.'-'.$owner->id.'</p>
                    <p class="mb-0">'.$owner->main_number.'</p>
                    <hr class="my-0" style="border: 1px solid gray;">
                    <p class="mb-0"><b>Contact</b></p>
                    <p class="mb-0">'.$buyer_tenant->firstname.' '.$buyer_tenant->lastname.'</p>
                    <p class="mb-0">'.$company->sample.'-'.$buyer_tenant->id.'</p>
                    <p class="mb-0">'.$buyer_tenant->main_number.'</p>
                </small>
            </div>
        ';

    }

    public function Delete(){
        $id=request('Delete');
        $Deal = Deal::find( $id );

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Deal || $Deal->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Deal->delete();
        return redirect('/admin/deals');
    }

    public function disabled(){
        $id=request('_id');

        $Deal = Deal::find( $id );

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Deal || $Deal->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Deal->status=2;
        $Deal->inactive_reason=request('reason');
        $Deal->save();
        return redirect('/admin/deals');
    }
}

