
@extends('layouts/contentLayoutMaster')

@section('title', 'Lead')

@section('vendor-style')
    <!-- vendor css files -->
	<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.css" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css" />

	<style>
        .slick-next {
            right: 0px;
        }

        .slick-prev {
            left: 0px;
            z-index:300;
        }

        .slick-slider {
            margin-bottom: 10px;
        }

        .slider-nav .slick-slide {
            padding: 0 5px;
        }

        .white_goverlay:before {
            background: rgb(255,255,255);
            background: linear-gradient(180deg, rgba(255,255,255, 0.2) 20%, rgba(255,255,255, 0.3) 30%, rgba(255,255,255, 0.8) 20%);
            bottom: 0;
            content: "";
            height: 90px;
            left: 0;
            position: absolute;
            right: 0;
            width: 100%;
            z-index: 1;
        }
        #preview-description , #dld-description{
            overflow: hidden;
        }
        .picker {
            min-width: 250px;
        }

        .rented-until-box .picker , .available-from-box .picker , .expiration-date-box .picker {
            right: 0;
        }

        .custom-switch .custom-control-label::before {
            height: 1rem !important;
            width: 2rem !important;
        }

        .custom-switch .custom-control-label::after {
            width: 0.8rem !important;
            height: 0.8rem !important;
        }

        .custom-switch .custom-control-label {
            height: 1rem;
            width: 2.1rem;
        }

        .img-fluid {
            width: 100%;
        }

        .bg-gray{
            background:#F7F7F7;
        }
        .text-gray{
            color:#ACACAC;
        }
	</style>
@endsection
@section('content')
    <!-- Form wizard with step validation section start -->
    <div class="card">
        <!--<div class="card-header">
            <h4 class="card-title">Add New Property</h4>
        </div>-->
        <div class="card-content">
            <div class="card-body container">
                <div class="row">

                    <div class="col-sm-4">
                        @php
                            $adminAuth=\Auth::guard('admin')->user();
                            $company=\App\Models\Company::find($adminAuth->company_id);
                            $source=App\Models\ContactSource::where('id',$lead->source)->first();
                            $leadNote='';
                            $noteNotFeedback=DB::select("SELECT 'lead' as type,lead_notes.id, admin_id, property_id, note_subject, lead_id, note, date_at, time_at, lead_notes.created_at,firstname,lastname FROM lead_notes,admins WHERE lead_notes.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND lead_notes.status=1 AND `note` is null AND admin_id=".$adminAuth->id."
                                                        UNION
                                                        SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND contact_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id."
                                                        UNION
                                                        SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND property_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id." ORDER BY created_at desc");
                            if($lead->data_center_id!=''){
                                $leadNote=DB::select('SELECT admin_id, note, created_at, updated_at FROM data_center_note WHERE note_subject!=6 AND data_center_id='.$lead->data_center_id.'
                                                        UNION
                                                      SELECT admin_id, note, created_at, updated_at FROM lead_notes WHERE lead_id='.$lead->id.' ORDER BY `created_at` DESC');
                            }else{
                                $leadNote=App\Models\LeadNote::where('lead_id',$lead->id)->orderBy('id','DESC')->get();
                            }
                            $LeadMasterProject=App\Models\MasterProject::where('id',$lead->master_project_id)->first();
                            $LeadCommunity=App\Models\Community::where('id',$lead->community_id)->first();
                            $LeadDeveloper=App\Models\Developer::where('id',$lead->developer_id)->first();
                            $LeadJobTitle=App\Models\JobTitle::where('id',$lead->job_title_id)->first();
                            $LeadReferrer=App\Models\Referrer::where('id',$lead->referrer_id)->first();
                            $creator=App\Models\Admin::where('id',$lead->admin_id)->first();
                            $portal=App\Models\Portal::where('id',$lead->portal)->first();

                            $result_specifier=App\Models\Admin::find($lead->result_specifier);

                            if($lead->property_id){
                                $property=App\Models\Property::where('id',$lead->property_id)->first();
                                $owner = App\Models\Contact::find( $property->contact_id );

                                $pictures=explode(',', $property->pictures);
                                $img_src='';
                                if($property->pictures)
                                    $img_src=$pictures[0];

                                $expected_price=0;
                                if($property->expected_price){
                                    $expected_price=$property->expected_price ;
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

                                $MasterProject=App\Models\MasterProject::where('id',$property->master_project_id)->first();
                                $Community=App\Models\Community::find($property->community_id);
                                $ClusterStreet=App\Models\ClusterStreet::find($property->cluster_street_id);
                            }
                        @endphp
                        <h4 class="text-primary py-2">Lead Details</h4>
                        <p class="border-top m-0 py-1"><b>REF: </b> Lead-{{$lead->id}} </p>
                        {!! ($source) ? '<p class="border-top m-0 py-1"><b>Lead Source: </b> '.$source->name.' </p>' : '' !!}
{{--                        {!! ($portal) ? '<p class="border-top m-0 py-1"><b>Portal: </b> '.$portal->name.' </p>' : '' !!}--}}
                        {!! ($lead->type) ? '<p class="border-top m-0 py-1"><b>Type: </b> '.LeadType[$lead->type].' </p>' : '' !!}
                        {!! ($lead->property_id) ? '<p class="border-top m-0 py-1"><b>Property REF: </b> <a target="_blank" href="/admin/property/view/'.$property->id.'">'.$company->sample.'-'.((($property->listing_type_id==1) ? 'S' : 'R').'-'.$property->ref_num).'</a> </p>' : '' !!}
                        {!! ($lead->name) ? '<p class="border-top m-0 py-1"><b>Full Name: </b> '.$lead->name.' </p>' : '' !!}
                        {!! ($lead->mobile_number) ? '<p class="border-top m-0 py-1"><b>Mobile Number: </b> <a href="tel:'.$lead->mobile_number.'">'.$lead->mobile_number.'</a> </p>' : '' !!}
                        {!! ($lead->mobile_number_2) ? '<p class="border-top m-0 py-1"><b>Second Number: </b> <a href="tel:'.$lead->mobile_number_2.'">'.$lead->mobile_number_2.'</a> </p>' : '' !!}
                        {!! ($LeadJobTitle) ? '<p class="border-top m-0 py-1"><b>Job Title: </b>'.$LeadJobTitle->name.'</p>' : '' !!}
                        {!! ($lead->receiver_number) ? '<p class="border-top m-0 py-1"><b>Receiver Number: </b> '.$lead->receiver_number.' </p>' : '' !!}
                        {!! ($lead->call_status) ? '<p class="border-top m-0 py-1"><b>Call Status: </b> '.ucfirst($lead->call_status).' </p>' : '' !!}
                        {!! ($lead->call_total_duration) ? '<p class="border-top m-0 py-1"><b>Call Total Duration: </b> '.$lead->call_total_duration.' </p>' : '' !!}
                        {!! ($lead->call_connected_duration) ? '<p class="border-top m-0 py-1"><b>Call Connected Duration: </b> '.$lead->call_connected_duration.' </p>' : '' !!}
                        {!! ($lead->caller_location) ? '<p class="border-top m-0 py-1"><b>Caller Location: </b> '.$lead->caller_location.' </p>' : '' !!}
                        {!! ($lead->type=='call_logs') ? '<div class="border-top m-0 py-1">
                                <audio id="plyr-audio-player" class="audio-player" controls>
                                    <source src="'.$recording.'" />
                                </audio> </div>' : '' !!}
                        {!! ($lead->email) ? '<p class="border-top m-0 py-1"><b>Email: </b> <a href="mailto:'.$lead->email.'">'.$lead->email.'</a> </p>' : '' !!}
                        {!! ($lead->message) ? '<p class="border-top m-0 py-1"><b>Message: </b> '.$lead->message.' </p>' : '' !!}
                        {!! ($lead->budget) ? '<p class="border-top m-0 py-1"><b>Budget: </b>AED '.number_format($lead->budget).' </p>' : '' !!}

                        <p class="border-top m-0 py-1"><b>ENQ Date: </b> {{\Helper::changeDatetimeFormat($lead->created_at)}} </p>
                        {!! ($lead->contact_category) ? '<p class="border-top m-0 py-1"><b>Contact Categories: </b> '.ucfirst($lead->contact_category).' </p>' : '' !!}
                        {!! ($LeadReferrer) ? '<p class="border-top m-0 py-1"><b>Recommended From: </b>'.$LeadReferrer->name.'</p>' : '' !!}
                        {!! ($lead->looking_for) ? '<p class="border-top m-0 py-1"><b>'.(($lead->contact_category=='owner')? 'Available' : 'Looking For').': </b>'.BUYER_LOOKING_FOR[$lead->looking_for].'</p>' : '' !!}
                        {!! ($LeadDeveloper) ? '<p class="border-top m-0 py-1"><b>Developer: </b>'.$LeadDeveloper->name.'</p>' : '' !!}
                        {!! ($LeadMasterProject) ? '<p class="border-top m-0 py-1"><b>Master Project: </b>'.$LeadMasterProject->name.'</p>' : '' !!}
                        {!! ($LeadCommunity) ? '<p class="border-top m-0 py-1"><b>Project: </b>'.$LeadCommunity->name.'</p>' : '' !!}
                        {!! ($offPlanProject) ? '<p class="border-top m-0 py-1"><b>Project: </b>'.$offPlanProject->project_name.'</p>' : '' !!}
                        {!! ($creator) ? '<p class="border-top m-0 py-1"><b>From : </b>'.$creator->firstname.' '.$creator->lastname.'</p>' : '' !!}
                        {!! ($result_specifier && $lead->status==2) ? '<p class="border-top m-0 py-1"><b>Closed By : </b>'.$result_specifier->firstname.' '.$result_specifier->lastname.' <br> '.\Helper::changeDatetimeFormat($lead->result_date).'</p>' : '' !!}
                        {!! ($result_specifier && $lead->status==1) ? '<p class="border-top m-0 py-1"><b>Added BY : </b>'.$result_specifier->firstname.' '.$result_specifier->lastname.' <br> '.\Helper::changeDatetimeFormat($lead->result_date).'</p>' : '' !!}
                        {!! ($lead->colse_reason && $lead->status==2) ? '<p class="border-top m-0 py-1 text-danger"><b>Reason: </b>'.$lead->colse_reason.'</p>' : '' !!}
                        {!! ($lead->colse_reason && $lead->status==1) ? '<p class="border-top m-0 py-1 text-success"><b>Reason: </b>'.$lead->colse_reason.'</p>' : '' !!}

                        @if($lead->property_id)
                            <div class="d-flex deal-info-box"><img class="mr-2 rounded" width="70" height="70" src="/storage/{{$img_src}}">
                                <div class="text-xl">
                                    <small>
                                        <P class="mb-0">Reference: <a target="_blank" href="/admin/property/view/{{$property->id}}">{{$company->sample.'-'.(($property->listing_type_id==1) ? "S" : "R").'-'.$property->ref_num}}</a></P>
                                        <P class="mb-0">
                                            {{(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' '.$Community->name : '').' | AED '.number_format($expected_price)}}
                                        </P>
                                        @if($adminAuth->type<3 || $property->client_manager_id==$adminAuth->id || $property->client_manager2_id==$adminAuth->id)
                                            <hr class="my-0" style="border: 1px solid gray;">
                                            <P class="mb-0">{{$owner->firstname.' '.$owner->lastname}}</P>
                                            <P class="mb-0">{{$company->sample.'-'.$owner->id}}</P>
                                            <P class="mb-0">{{ucfirst($owner->contact_category)}}</P>
                                            <P class="mb-0">{{$owner->main_number}}</P>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endif

                        <div class="border-top m-0 py-1 justify-content-center d-flex">
                            {!! ($lead->mobile_number) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1 px-1" href="https://wa.me/'.str_replace(['+', '-'], '', filter_var($lead->mobile_number, FILTER_SANITIZE_NUMBER_INT)).'"><i class="font-medium-3 fa fa-whatsapp"></i></a><a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light" href="tel:'.str_replace(['+', '-'], '', filter_var($lead->mobile_number, FILTER_SANITIZE_NUMBER_INT)).'"><i class="font-medium-3 fa fa-phone"></i> Call</a>': '' !!}

{{--                            {!! ($lead->email ) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light" href="mailto:'.$lead->email.'"><i class="fa fa-envelope-o"></i> Email</a>' : '' !!}--}}
                        </div>
                    </div>
                    <div class="col-sm-8 pt-1">
                        @csrf
                        {{--<h4 class="text-primary py-2">Note</h4>
                        <div class="row custom-scrollbar pt-2 m-0" style="max-height: 300px;">
                            <ul class="list-group list-group-flush">
                                @foreach($leadNote as $note)
                                    @php
                                        $agent=App\Models\Admin::where('id',$note->admin_id)->first();
                                    @endphp
                                    <li class="list-group-item">
                                        <div><p>{{$agent->firstname .' '.$agent->lastname}} {{\Helper::changeDatetimeFormat($note->created_at)}}</p></div>
                                        <p class="m-0">{{$note->note}}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @if($lead->status==0)
                            <div class="mb-2 mt-2">
                                <fieldset class="form-group form-label-group mb-0">
                                    <textarea data-length="1500" class="form-control char-textarea" name="note" id="note" required rows="6" placeholder="Note"></textarea>
                                    <label>Note</label>
                                </fieldset>
                                <small class="counter-value float-right"><span class="char-count">0</span> / 1500 </small>
                            </div>
                            <div class="d-flex justify-content-center">
                                <input type="hidden" name="lead" value="{{$lead->id}}">
                                <button type="submit" name="submit" class="btn btn-primary waves-effect waves-light search-contact">Add Note</button>
                            </div>
                        @endif--}}
                        @if($lead->status==0 || $lead->status==2) <button type="button" style="width: 194px;margin-bottom: 14px;" class="d-block btn-activity btn mx-auto btn-outline-success waves-effect waves-light float-sm-left {{ ($noteNotFeedback) ? 'warning-feedback' : '' }}" {!! ($noteNotFeedback) ? '' : 'data-target="#ActivityModal" data-toggle="modal"' !!}>Activity</button>@endif
                        <div class="table-responsive custom-scrollbar pr-1" style="max-height: 600px;">
                            <table class="table table-striped truncate-table">
                                <thead style="height: 55px;">
                                <tr>
                                    <th></th>
                                    <th>Activity Type</th>
                                    <th>Property</th>
                                    <th>Feedback / Note</th>
                                    <th>CM</th>
                                    <th>Added Date</th>
                                </tr>
                                </thead>
                                <tbody id="div_notes_section">
                                @foreach($LeadNote as $note)
                                    @php
                                        $ActivityProperty=App\Models\Property::where('id',$note->property_id)->first();
                                        if($ActivityProperty){
                                            $ActivityMasterProject=App\Models\MasterProject::where('id',$ActivityProperty->master_project_id)->first();
                                            $ActivityCommunity=App\Models\Community::find($ActivityProperty->community_id);
                                        }

                                        $OffPlanProject='';//App\Models\OffPlanProject::where('id',$note->off_plan_project_id)->first();

                                    @endphp
                                    <tr class="note-description" data-title="{{NoteSubject[$note->note_subject]}}" data-desc="{{($note->status==2 && $note->note_subject==2) ? NoteSubject[$note->note_subject].' Cancelled' : $note->note}}">
                                        <td>
                                            <div class="action" data-id="{{$note->id}}" data-model="{{route('lead.note.cancel')}}">
                                                {!! (($note->note_subject==2 || $note->note_subject==3) && $note->type=="lead" && $note->status==1 && $note->note=='' && date('Y-m-d H:i:s') > $note->date_at.' '.$note->time_at) ? '<a href="#ActivityFeedbackModal" class="feedback-register" data-toggle="modal" data-note="'.$note->note.'"><span class="btn btn-primary" style="min-width:100%">Feedback</span></a>' : '' !!}
                                                {!! (($note->note_subject==2 || $note->note_subject==3) && $note->status==1 && $note->type=="lead" && date('Y-m-d H:i:s') < $note->date_at.' '.$note->time_at) ? '<a href="javascript:void(0);" class="disabled"><span class="btn btn-danger" style="min-width:100%">Cancel</span></a>' : '' !!}
                                            </div>
                                        </td>
                                        <td data-target="#ViewModal" data-toggle="modal">{{NoteSubject[$note->note_subject]}}</td>
                                        <td>
                                            @if($ActivityProperty || $OffPlanProject)
                                                {!! ($ActivityProperty) ? '<a href="/admin/property/view/'.$ActivityProperty->id.'">'.$company->sample.'-'.( (($ActivityProperty->listing_type_id==1) ? 'S' : 'R').'-'.$ActivityProperty->ref_num.'<br>'.( ($ActivityMasterProject) ? $ActivityMasterProject->name : '' ).' | '.( ($ActivityCommunity) ? $ActivityCommunity->name : '' ) ).'</a>' : '' !!}
                                                {!! ($OffPlanProject) ? '<a href="/off-plan/brochure/'.\Helper::idCode($OffPlanProject->md_crm_id). (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) .'" target="_blank">'.$OffPlanProject->project_name.' - '.$OffPlanProject->master_project_name.'</a>' : '' !!}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td data-target="#ViewModal" data-toggle="modal">
                                            {!! ( ($note->date_at) ? \Helper::changeDatetimeFormat( $note->date_at.' '.$note->time_at).'<br>' : '' ).
                                                '<span class="note{{$note->id}}">'.
                                                \Illuminate\Support\Str::limit(strip_tags($note->note),50)
                                                .( ( $note->status==2) ? (($note->note) ? '<br>':'').'<span class="text-danger">'.NoteSubject[$note->note_subject].' Cancelld</span>' : '' )
                                                 .'</span>' !!}
                                        </td>
                                        <td>{{$note->firstname.' '.$note->lastname}}</td>
                                        <td data-target="#ViewModal" data-toggle="modal">{{\Helper::changeDatetimeFormat($note->created_at)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        @if($lead->status==0 || $lead->status==2)
                            <a href="/admin/add-contacts?lead={{$lead->id}}" class="btn btn-primary waves-effect waves-light search-contact add-to-contact-btn float-md-right mb-1 mb-md-0">Add To Contact</a>
                            {{--@if($adminAuth->type<3 || $adminAuth->id==$lead->assign_to) <button type="button" data-toggle="modal" data-target="#assignModal" class="mx-md-2 btn btn-primary waves-effect waves-light search-contact float-right">Assign To</button> @endif--}}
                        @endif
                        @if($lead->status==0)
                            <button type="button" data-toggle="modal" data-target="#closeModal" class="d-block mx-auto btn btn-danger waves-effect waves-light search-contact float-sm-left">Close</button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Close -->
    <div class="modal fade text-left" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('lead.close')}}" novalidate class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Close</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <fieldset class="form-group form-label-group">
                                <label for="Reason">Reason</label>
                                <select class="form-control" id="Reason" name="Reason" required>
                                    <option value="">Select</option>
                                    @foreach(LeadClosedReason as $reason)
                                        <option value="{{$reason}}">{{$reason}}</option>
                                    @endforeach
                                    <option value="Other">Other</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 d-none">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control" id="colse_reason" name="colse_reason" placeholder="Reason" required>
                                <label>Reason</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="lead" value="{{$lead->id}}">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Assign -->
    <div class="modal fade text-left" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('lead.assign')}}" novalidate class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Assign To</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <fieldset class="form-group form-label-group">
                                <label for="select-agent">Assign To <span>*</span></label>
                                <select class="form-control select2" id="assign_to" name="assign_to" required>
                                    <option value="">Select</option>
                                    @php
                                        $Agents=\Helper::getCM_DropDown_list('1');
                                    @endphp
                                    @foreach($Agents as $agent)
                                        @if($agent->id==$adminAuth->id)
                                            @continue
                                        @endif
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_id" value="{{$lead->id}}">
                    <button type="submit" value="{{$lead->id}}" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Activity -->
    @include('admin/activity-modal')

@endsection

@section('vendor-script')
    <!-- vendor files -->

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>

@endsection
@section('page-script')
    <!-- Page js files -->
    <script>
        $('#Reason').change(function () {
            let val=$(this).val();
            $('#colse_reason').parent().parent().addClass('d-none');
            $('#colse_reason').val(val);
            if(val=='Other') {
                $('#colse_reason').parent().parent().removeClass('d-none');
                $('#colse_reason').val('');
            }
        });

        $('#mobile-back').click(function () {
            close_window();
        });
    </script>

    <script>
        $('body').on('click','.note-description td',function() {
            let html=$(this).children('.action').html();
            if (!html) {
                $('#ViewModal .modal-title').html( $(this).parent().data('title') );
                $('#ViewModal .modal-body').html( $(this).parent().data('desc') );
            }
        });

        $('#NoteSubject').change(function(){
            let val=$(this).val();
            $('#ActivityModal .error').removeClass('error');
            if(val==2 || val==3 || val==6)
                $('.data-at-box').removeClass('d-none');
            else
                $('.data-at-box').addClass('d-none').children('input').val('');

            $('.contact-property-box').addClass('d-none').children('select').val('');

            if(val==2) {
                $('.contact-property-box').removeClass('d-none');
                $('.activity-not-box').addClass('d-none');
            }else if(val==3){
                $('.activity-not-box').addClass('d-none');
            }else{
                $('.activity-not-box').removeClass('d-none');
            }
        });

    </script>

    <script>
        $('#AddPropertyNote').click(function () {
            let NoteSubject =$('#NoteSubject').val();
            let note =$('#Note').val();
            let date_at=$('#DateAt').val();
            let time_at=$('#TimeAt').val();
            let property =$('#ActivityContact').val();
            let error=0;

            if(NoteSubject == ''){
                $("#NoteSubject").parent().addClass('error');
                error=1
            }else{
                $("#NoteSubject").parent().removeClass('error');
            }

            if(NoteSubject!=2 &&  NoteSubject!=3) {
                if(note == ''){
                    $("#Note").parent().addClass('error');
                    error=1
                }else{
                    $("#Note").parent().removeClass('error');
                }
            }

            if(NoteSubject==2 || NoteSubject==3 || NoteSubject==6){
                if(date_at == ''){
                    $("#DateAt").parent().addClass('error');
                    error=1
                }else{
                    $("#DateAt").parent().removeClass('error');
                }
                if(time_at == ''){
                    $("#TimeAt").parent().addClass('error');
                    error=1
                }else{
                    $("#TimeAt").parent().removeClass('error');
                }
            }

            if(NoteSubject==2){
                if(property == '' || property == null){
                    $("#ActivityContact").parent().addClass('error');
                    error=1
                }else{
                    $("#ActivityContact").parent().removeClass('error');
                }
            }
            if(error==0) {
                $('#AddPropertyNote').html('Please wait...').attr('disabled','disabled');
                $.ajax({
                    url: "{{ route('lead.note.add') }}",
                    type: "POST",
                    data: {
                        _token: $('form input[name="_token"]').val(),
                        lead: "{{ ($lead) ? $lead->id : '' }}",
                        note_subject: NoteSubject,
                        property: property,
                        date_at: date_at,
                        time_at: time_at,
                        note: note
                    },
                    success: function (response) {
                        $('#NoteSubject').val('').change();
                        $('#DateAt , #TimeAt , #Note').val('');
                        $('#AddPropertyNote').html('Submit').removeAttr('disabled');
                        $('#div_notes_section').prepend(response);
                        $('.activity-box').removeClass('d-none');
                        $('#ActivityModal').modal('hide');
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        });

        $('body').on('click','.feedback-register',function() {
            let id=$(this).parent().data('id');
            $('#EditPropertyNote').val(id);
        });

        $('#EditPropertyNote').click(function () {
            let note =$('#FeedbackNote').val();
            let id =$('#EditPropertyNote').val();
            $('#FeedbackNote').val('');

            $.ajax({
                url:"{{ route('lead.note.edit') }}",
                type:"POST",
                data:{
                    _token:'{{csrf_token()}}',
                    id:id,
                    note:note
                },
                success:function (response) {
                    $('#note'+id).html(note);
                    $('#ActivityFeedbackModal').modal('hide');
                    location.reload();
                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });
    </script>

    <script>
        MemebrSelcet2();
        function MemebrSelcet2(SelectType=false) {
            // Loading remote data
            $(".select-2-user").select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple:SelectType,
                ajax: {
                    url: "{{ route('property.ajax.select') }}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Property Information',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function formatRepo (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="w-100 ml-1">
                                <div><b>${repo.ref}</b></div>
                                <div>${repo.address}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelection (repo) {
            return repo.ref ||  repo.text;

        }
    </script>

    <script>

        (function(window, document, $) {
            'use strict';

            $('.limit-format-picker').pickadate({
                format: 'yyyy-mm-dd',
                min:true
            });

            /*******    Pick-a-time Picker  *****/
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' +dd ;
            let date=today;

            var $input= $('.limit-timepicker').pickatime({
                format: 'HH:i',
                interval:10,    });

            var picker = $input.pickatime('picker');

            $('#DateAt').change(function(){
                date=$(this).val();
                $('#TimeAt').val('');
                if(today==date){
                    picker.set('min', true);
                }else{
                    picker.set('min', false);
                }
            });
        })(window, document, jQuery);
    </script>
@endsection
