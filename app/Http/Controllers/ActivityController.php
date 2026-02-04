<?php

namespace App\Http\Controllers;

use App\Models\Activities;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Property;
use App\Models\Contact;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function activities(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/activity-reports', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function GetActivities(){
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $data = array();

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);
        $where=' WHERE company_id='.$adminAuth->company_id.' ';

        if($adminAuth->type>2 && $adminAuth->type!=5 && $adminAuth->type!=6)
            $totalRecords = Activities::where('admin_id',$adminAuth->id)->count();
        else
            $totalRecords = Activities::where('company_id',$adminAuth->company_id)->count();

        //if($searchValue)
            //$where.=' AND ( CONCAT(firstname," ", lastname)  LIKE "%'.$searchValue.'%" OR email LIKE "%'.$searchValue.'%")';

        if( request('type') )
            $where.=' AND type="'.request('type').'"';

        if( request('property') )
            $where.=' AND property_id='.request('property');

        if( request('off_plan_project') )
            $where.=' AND off_plan_project_id='.request('off_plan_project');

        if( request('contact') )
            $where.=' AND contact_id='.request('contact');

        if( request('admin') )
            $where.=' AND admin_id='.request('admin');

        if($adminAuth->type>2 && $adminAuth->type!=5 && $adminAuth->type!=6)
            $where.=' AND admin_id='.$adminAuth->id;

        if( request('note_subject') )
            $where.=' AND note_subject ='.request('note_subject');

        $now=date('Y-m-d H:i:s');
        if( request('cancelled') ) {
            $cancelled=request('cancelled');
            if($cancelled==2) {
                $where .= ' AND status =' . $cancelled;
            }

            if( !request('note_subject') )
                $where.=' AND note_subject IN (2,3)';

            if($cancelled==1) {
                $where.=' AND status =1 AND concat(date_at," ",time_at) <="'.$now.'"';
            }
            if($cancelled==3) {
                $where.=' AND status =1 AND concat(date_at," ",time_at) >="'.$now.'"';
            }
        }

        if( request('from_date_at') )
            $where.=' AND date_at >="'.request('from_date_at').' 00:00:00"';

        if( request('to_date_at') )
            $where.=' AND date_at <="'.request('to_date_at').' 23:59:59"';

        if( request('from_date') )
            $where.=' AND created_at >="'.request('from_date').' 00:00:00"';

        if( request('to_date') )
            $where.=' AND created_at <="'.request('to_date').' 23:59:59"';

        #record number with filter

        if($rowperpage=='-1'){
            $query="SELECT * FROM view_activities ".$where."  ORDER BY ".$columnName." ".$columnSortOrder;
            $Records=DB::select($query);
        }else{
            $query="SELECT * FROM view_activities ".$where." ORDER BY  view_activities.".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage;
            $Records=DB::select($query);
        }


        $totalRecordwithFilter=count(DB::select("SELECT * FROM view_activities ".$where) );

        $obj=[];

        foreach($Records as $row){

            $created_at_html='';
            $contactProperty='';
            if($row->type=='property'){
                $createdProperty=Property::find($row->property_id);
                if($adminAuth->type==6){
                    $created_at_html=$company->sample.'-'.(($createdProperty->listing_type_id==1) ? 'S' : 'R').'-'.$createdProperty->ref_num;
                }else{
                    $created_at_html='<a href="/admin/property/view/'.$createdProperty->id.'" target="_blank">'.$company->sample.'-'.(($createdProperty->listing_type_id==1) ? 'S' : 'R').'-'.$createdProperty->ref_num.'</a>';
                }

                $contact=Contact::find($row->contact_id);
                if($contact) {
                    if($adminAuth->type==6){
                        $contactProperty = $contact->firstname . ' ' . $contact->lastname;
                    }else{
                        $contactProperty = '<a href="/admin/contact/view/' . $contact->id . '" target="_blank">' . $contact->firstname . ' ' . $contact->lastname . '</a>';
                    }
                }
            }

            if($row->type=='contact'){
                $createdContact=Contact::find($row->contact_id);
                if($adminAuth->type==6){
                    $created_at_html=$createdContact->firstname.' '.$createdContact->lastname;
                }else{
                    $created_at_html='<a href="/admin/contact/view/'.$createdContact->id.'" target="_blank">'.$createdContact->firstname.' '.$createdContact->lastname.'</a>';
                }
                $property=Property::find($row->property_id);
                if($property) {
                    if($adminAuth->type==6){
                        $contactProperty =$company->sample . '-' . (($property->listing_type_id == 1) ? 'S' : 'R') . '-' . $property->ref_num;
                    }else{
                        $contactProperty = '<a href="/admin/property/view/' . $property->id . '" target="_blank">' . $company->sample . '-' . (($property->listing_type_id == 1) ? 'S' : 'R') . '-' . $property->ref_num . '</a>';
                    }
                }
            }

            if($row->type=='lead'){
                $createdLead=Lead::find($row->lead_id);
                if($adminAuth->type==6){
                    $created_at_html='Lead-'.$createdLead->id;
                }else{
                    $created_at_html='<a href="/admin/lead/view/'.$createdLead->id.'" target="_blank">Lead-'.$createdLead->id.'</a>';
                }
                $property=Property::find($row->property_id);
                if($property) {
                    if($adminAuth->type==6){
                        $contactProperty =$company->sample . '-' . (($property->listing_type_id == 1) ? 'S' : 'R') . '-' . $property->ref_num;
                    }else{
                        $contactProperty = '<a href="/admin/property/view/' . $property->id . '" target="_blank">' . $company->sample . '-' . (($property->listing_type_id == 1) ? 'S' : 'R') . '-' . $property->ref_num . '</a>';
                    }
                }
            }

            $status='';//'<span class="badge badge-pill badge-light-success" style="min-width: 100%">Active</span>';
            if($row->status==2){
                $status='<span class="badge badge-pill badge-light-danger" style="min-width: 100%">Cancelled</span>';
            }else{
                if($row->note_subject==2 || $row->note_subject==3) {
                    $datetime = $row->date_at . ' ' . $row->time_at;
                    if (strtotime($datetime) < strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-primary" style="min-width: 100%">Done</span>';
                    }
                    if (strtotime($datetime) > strtotime($now)) {
                        $status = '<span class="badge badge-pill badge-light-success" style="min-width: 100%">Upcoming</span>';
                    }
                }
            }

            $obj['type']= $row->type;
            $obj['note_subject']= NoteSubject[$row->note_subject];
            $obj['created_for']= $created_at_html;
            $obj['firstname']= $row->firstname.' '.$row->lastname;
            $obj['date_at']= ($row->date_at) ? \Helper::changeDatetimeFormat( $row->date_at.' '.$row->time_at) : '';

            $obj['contact_property']= $contactProperty;
            $obj['note']= ($row->note) ? '<span class="note" data-target="#ViewModal" data-toggle="modal" data-title="'.NoteSubject[$row->note_subject].'" data-desc="'.$row->note.'" >'.\Illuminate\Support\Str::limit(strip_tags($row->note),50).'</span>' : '';

            $obj['created_at']= \Helper::changeDatetimeFormat( $row->created_at);
            $obj['status']= $status;
            $obj['action']='<div class="action d-flex font-medium-3" data-id="'.$row->id.'" data-model="">

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
