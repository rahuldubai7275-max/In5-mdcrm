<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SurveyController extends Controller
{
    public function surveys(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/surveys', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function survey(Request $request){

        $survey=Survey::find(request('id'));

        if(!$survey){
            return 'page not found';
        }
        return view('/admin/survey', [
            'survey' => $survey,
        ]);
    }

    public function answer(Request $request){
        $request->validate([
            '_id'=>'required',
        ]);
        $Survey = Survey::find(request('_id'));
        if($Survey->status!=1) {
            $Survey->comment = request('comment');

            $total_rate = 0;
            $q_count = 0;
            foreach ($request->question as $sq_id) {
                $SurveyQuestion = SurveyQuestion::find($sq_id);
                if ($SurveyQuestion && $SurveyQuestion->status == 1) {
                    $rate = request('rate_' . $sq_id);
                    SurveyAnswer::create([
                        'survey_id' => $Survey->id,
                        'survey_question_id' => $sq_id,
                        'rate' => $rate
                    ]);
                    $total_rate += $rate;
                    $q_count++;
                }
            }

            $Survey->avg = ($total_rate / $q_count);
            $Survey->status = 1;
            $Survey->save();
        }
        Session::flash('success','Thank you survey has been successfully submitted');
        return redirect('/survey/'.$Survey->id);

    }

    public function getSurveys()
    {
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value

        $adminAuth=\Auth::guard('admin')->user();
        $company=Company::find($adminAuth->company_id);

        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `view_surveys` WHERE company_id=".$company->id);
        $totalRecords = $totalRecords[0]->countAll;

        // $dataWhere=[];
        $where = '';
        $now = date('Y-m-d H:i:s');
        $time = '';
        if (request('from_date')) {
            $from_date = request('from_date') . ' 00:00:00';
            if ($from_date < $now)
                $time = ' AND CONCAT(date_at," ",time_at)>="' . $from_date . '"';
        }

        if (request('to_date')) {
            $to_date = request('to_date') . ' 23:59:59';
            if ($to_date < $now) {
                $time .= ' AND CONCAT(date_at," ",time_at)<="' . $to_date . '"';
            }else {
                $time .= " AND CONCAT(date_at,' ',time_at) <= '" . $now . "'";
            }
        }else{
            $time.=" AND CONCAT(date_at,' ',time_at) <= '".$now."'";
        }

        $where.=$time;

        if( request('subject') ){
            $where.=' AND model in ("Property_'.request('subject').'" , "Contact_'.request('subject').'" )';
        }

        if( request('status') || request('status')=='0' )
            $where.=' AND status="'.request('status').'"';

        if( request('admin') )
            $where.=' AND admin_id="'.request('admin').'"';

        if( request('property') )
            $where.=' AND property_id="'.request('property').'"';

        if( request('contact') )
            $where.=' AND contact_id="'.request('contact').'"';

        if( request('avg') ) {
            $avg=explode('-',request('avg'));
            $avg_start =$avg[0];
            $avg_end =$avg[1];
            $where .= ' AND avg>=' . $avg_start;
            $where .= ' AND avg<=' . $avg_end ;
        }

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM view_surveys WHERE company_id=".$adminAuth->company_id." ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();


        if($columnName=='date_at'){
            $columnName="CONCAT(date_at,' ',time_at)";
        }

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM view_surveys WHERE company_id=".$adminAuth->company_id." ".$where." ORDER BY ".$columnName." ".$columnSortOrder);
        }else{
            $Records=DB::select("SELECT * FROM view_surveys WHERE company_id=".$adminAuth->company_id." ".$where." ORDER BY ".$columnName." ".$columnSortOrder." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){

            $property_id='';
            $contact_id='';

            if($row->model=='Contact_Viewing' || $row->model=='Contact_Appointment'){
                $ContactNote=ContactNote::where('id',$row->model_id)->first();
                $property_id=$ContactNote->property_id;
                $contact_id=$ContactNote->contact_id;
            }

            if($row->model=='Property_Viewing' || $row->model=='Property_Appointment'){
                $PropertyNote=PropertyNote::where('id',$row->model_id)->first();
                $property_id=$PropertyNote->property_id;
                $contact_id=$PropertyNote->contact_id;
            }

            $property=Property::where('id',$property_id)->first();
            $property_ref=($property) ? '<a href="/admin/property/view/'.$property->id.'" target="_blank">'.$company->sample.'-'.(($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num.'</a>' : '';

            $contact=Contact::where('id',$contact_id)->first();
            $contact_ref=($contact) ? '<a href="/admin/contact/view/'.$contact->id.'" target="_blank">'.$contact->firstname.' '.$contact->lastname.'</a>' : '';

            $status='<span class="badge badge-pill badge-light-success" style="min-width: 100%">Replied</span>';
            $copyBtn='';
            if($row->status==0) {
                $status = '<span class="badge badge-pill badge-light-danger" style="min-width: 100%">Not Replied</span>';
                $copyBtn = '<a href="javascript:void(0);" class="ml-50 copy-link"><i class="feather icon-copy"></i></a>';

            }

            $subject=explode('_',$row->model);
            $subject=end($subject);
            $obj['firstname']=$row->firstname.' '.$row->lastname;
            $obj['model']=$subject;
            $obj['property_id']=$property_ref;
            $obj['contact_id']=$contact_ref;
            $obj['avg']=($row->avg) ? '<div class="badge badge-primary badge-md"><span>'.$row->avg.'</span> <i class="feather icon-star"></i></div>' : '';
            $obj['status']=$status;
            $obj['date_at']=\Helper::changeDatetimeFormat($row->date_at.' '.$row->time_at);
            $obj['action']='<div class="action font-medium-3 d-flex" data-id="'.$row->id.'">
                                 <a href="#surveyDetails" data-toggle="modal" class="survey-detail"><i class="feather icon-eye"></i></a>
                                 '.$copyBtn.'
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

    public function details(Request $request){
        $request->validate([
            'survey'=>'required',
        ]);
        $Survey=Survey::find(request('survey'));

        $output='<ul class="list-group list-group-flush">';
        $SurveyAnswer=SurveyAnswer::where('survey_id',$Survey->id)->get();
        foreach ($SurveyAnswer as $row){
            $SurveyQuestion=SurveyQuestion::find($row->survey_question_id);

            $output.='<li class="list-group-item">
                <p><b>'.$SurveyQuestion->question.'</b></p>
                <div class="d-flex">
                    <div class="font-small-1">Very poor</div>
                    <div class="mx-1">
                        <span class="font-medium-1 rate-star d-flex">
                            <i class="fa fa-star text-'.( ($row->rate>=1) ? 'warning' : 'secondary' ).'"></i>
                            <i class="fa fa-star text-'.( ($row->rate>=2) ? 'warning' : 'secondary' ).'"></i>
                            <i class="fa fa-star text-'.( ($row->rate>=3) ? 'warning' : 'secondary' ).'"></i>
                            <i class="fa fa-star text-'.( ($row->rate>=4) ? 'warning' : 'secondary' ).'"></i>
                            <i class="fa fa-star text-'.( ($row->rate>=5) ? 'warning' : 'secondary' ).'"></i>
                        </span>
                    </div>
                    <div class="font-small-1">Very satisfied</div>
                </div>
            </li>';
        }
        $output.='</ul>';

        $output.='<p class="mt-2"><b>Comment: </b>'.$Survey->comment.'</p>
                <p><b>Avg: </b><span class="badge badge-primary badge-md"><span>'.$Survey->avg.'</span> <i class="feather icon-star"></i></span></p>';

        return $output;

    }

}
