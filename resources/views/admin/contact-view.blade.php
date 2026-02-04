
@extends('layouts/contentLayoutMaster')

@section('title', 'Contact')

@section('vendor-style')
    <!-- vendor css files -->
{{--	<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">--}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />


    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->

	<style>

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

        .bg-yellow{
            background:#ffff00;
        }

        .info-box .pb-1, .info-box .py-1 {
            padding-top: 0.7rem !important;
            padding-bottom: 0.7rem !important;
        }
	</style>
@endsection
@section('content')
    <!-- Form wizard with step validation section start -->

@php
    $adminAuth=\Auth::guard('admin')->user();
    $company=\App\Models\Company::find($adminAuth->company_id);
    $ClientManagers=\Helper::getCM_DropDown_list('1');
    $Developer=App\Models\Developer::where('id','=',$Contact->developer_id)->first();
    $Note=App\Models\ContactNote::where('contact_id','=',$Contact->id)->latest('created_at', 'desc')->first();
    $bg='';
    //$img='<img src="/images/imoji-green.png">';
    $last_activity=$Contact->created_at;
    if($Contact->last_activity){
        $last_activity=$Contact->last_activity;
    }
    $today = \Carbon\Carbon::now();
    $today = $today->format('Y/n/j H:i:s');
    $date_two = \Carbon\Carbon::parse($last_activity);
    $days = $date_two->diffInDays($today);

    $Deal=App\Models\Deal::where('contact_id',$Contact->id)->first();
    $deals=App\Models\Deal::where('contact_id',$Contact->id)->get();

    $activity_contact_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_2')->first();
    $activity_contact_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','contact_activity_3')->first();

    if($days>$activity_contact_setting_3->time){//30
        $bg='danger';
        $img='<img src="/images/imoji-red'.(($Deal) ? '-deal' : '').'.png">';
    }

    if($days<=$activity_contact_setting_3->time){//30
        $bg='yellow';
        $img='<img src="/images/imoji-yellow'.(($Deal) ? '-deal' : '').'.png">';
    }

    if($days<=$activity_contact_setting_2->time){//15
        $bg='success';
        $img='<img src="/images/imoji-green'.(($Deal) ? '-deal' : '').'.png">';
    }


  $contactmp_id='';
  $contactbed_id='';
    if($Contact){

        $ClientManager=App\Models\Admin::where('id',$Contact->client_manager)->first();
        $ClientManagerTwo=App\Models\Admin::where('id',$Contact->client_manager_tow)->first();

        $b_t_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_buyer_tenant')->first();
        $info_show=0;
        if(($Contact->contact_category=='buyer' || $Contact->contact_category=='tenant') &&
            $b_t_setting->status==1 && $adminAuth->type==4 &&
            !($Contact->client_manager == $adminAuth->id || $Contact->client_manager_tow == $adminAuth->id)){

            //$b_t_setting_admin=\App\Models\SettingAdmin::where('setting_id',$b_t_setting->id)->where('admin_id',$Contact->client_manager)->first();
            $b_t_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$Contact->client_manager,$Contact->client_manager_tow])->where('setting_id',$b_t_setting->id)->first();
            if(!$b_t_setting_admin) {

                $last_activity=$Contact->created_at;

                $contactNoteLast=\App\Models\ContactNote::whereIn('admin_id', [$Contact->client_manager,$Contact->client_manager_tow])->where('contact_id',$Contact->id)->orderBy('id','DESC')->first();
                if($contactNoteLast){//$Contact->last_activity
                    $last_activity=$contactNoteLast->created_at;//$Contact->last_activity;
                }

                $today = \Carbon\Carbon::now();
                $today = $today->format('Y/n/j');
                $date_two = \Carbon\Carbon::parse($last_activity);
                $minutes = $date_two->diffInMinutes($today);
                $hours = $date_two->diffInHours($today);
                $days = $date_two->diffInDays($today);

                if($b_t_setting->time_type==1 && $minutes >= $b_t_setting->time){
                    $info_show=1;
                }

                if($b_t_setting->time_type==2 && $hours >= $b_t_setting->time){
                    $info_show=1;
                }

                if($b_t_setting->time_type==3 && $days >= $b_t_setting->time){
                    $info_show=1;
                }
            }

        }else{
            if($adminAuth->type!=4 || $Contact->client_manager==$adminAuth->id || $Contact->client_manager_tow==$adminAuth->id){
                $info_show=1;
            }
        }

        $Properties=App\Models\Property::where('contact_id',$Contact->id)->get();

        $PropertyTypes=App\Models\ContactPropertyType::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
        $PropertyType='';
        foreach ($PropertyTypes as $row){
            $PType=App\Models\PropertyType::where('id',$row->property_type_id)->first();
            $PropertyType.=$PType->name.',';
        }

        $Bedrooms=App\Models\ContactBedroom::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
        $Bedroom='';
        foreach ($Bedrooms as $row){
            $Bed=App\Models\Bedroom::where('id',$row->bedroom_id)->first();
            $Bedroom.=$Bed->name.',';
            $contactbed_id.=$row->bedroom_id.', ';
        }

        $Emirate=\App\Models\Emirate::find($Contact->emirate_id);

        $MasterProjects=App\Models\ContactMasterProject::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
        $MasterProject='';
        foreach ($MasterProjects as $row){
            $MProject=App\Models\MasterProject::where('id',$row->master_project_id)->first();
            $MasterProject.=$MProject->name.', ';
            $contactmp_id.=$row->master_project_id.', ';
        }

        $ContactCommunity=\App\Models\ContactCommunity::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
        $Community='';
        foreach($ContactCommunity as $row){
            $comCommunity=\App\Models\Community::find($row->community_id);
            $Community.=$comCommunity->name.', ';
        }

        $noteNotFeedback=DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND contact_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND property_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id." ORDER BY created_at desc");
    }
    $contactCategories=\App\Models\ContactCategory::where('contact_id',$Contact->id)->orderBy('id','DESC')->get();
    $contactSource=\App\Models\ContactSource::find($Contact->contact_source);
@endphp
    <div class="card">
        <!--<div class="card-header">
            <h4 class="card-title">Add New Property</h4>
        </div>-->
        <div class="card-content">
            <div class="card-body container">
                <div class="row">
                    <div class="clearfix col-sm-12">
                        <div class="float-left d-flex align-items-center">
                            <div class="avatar my-0 ml-0 mr-1 avatar-xl bg-white" {!! ($Deal) ? 'href="#dealsModal" data-toggle="modal"' : '' !!}>
                                {!! $img ?? '' !!}
                            </div>
                            <p class="m-0"><b>Ref:</b><br> {{$company->sample}}-{{$Contact->id}}</p>
                        </div>
                        <div class="float-right">
                            <a href="/admin/contact/view/{{($Previous) ? $Previous : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Previous) ? '' : 'disabled'}}">
                                <span class="d-none d-sm-block">Previous</span>
                                <span class="d-block d-sm-none"><</span>
                            </a>
                            <a href="/admin/contact/view/{{($Next) ? $Next : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Next) ? '' : 'disabled'}}">
                                <span class="d-none d-sm-block">Next</span>
                                <span class="d-block d-sm-none">></span>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-12 mt-1">
                        <div class="row">
                            <div class="col-sm-8">
                                <div class="clearfix">
                                    <div>
                                        <div class="float-left">
                                            @if($info_show==1)
                                                <button type="button" style="padding-left: 7px;padding-right: 7px" class="font-mobile-small btn-activity btn btn-150 btn-outline-success waves-effect waves-light float-left {{ ($noteNotFeedback) ? 'warning-feedback' : '' }}" {!! ($noteNotFeedback) ? '' : 'data-target="#ActivityModal" data-toggle="modal"' !!}>Activity</button>
                                                @if($company->package!=1) <button type="button" style="margin-left: 7px;margin-right: 7px;padding-left: 7px;padding-right: 7px" class="font-mobile-small btn btn-150 btn-outline-success waves-effect waves-light float-left add-contact-cat-btn" data-target="#contactCategoryModal" data-toggle="modal">New Queries</button> @endif
                                            @endif
                                            @if($contactCategories->count()>0 && $company->package!=1)<button type="button" style="padding-left: 7px;padding-right: 7px" class="font-mobile-small btn btn-150 btn-outline-success waves-effect waves-light float-left" data-target="#contactCategoryShowModal" data-toggle="modal">Added Queries</button>@endif
                                        </div>
                                        @if(!request('match') && $company->package!=1)
                                        <div class="float-right">
                                            <a style="width:120px" href="#matchModal" data-toggle="modal" class="btn bg-gradient-info py-1 px-2 waves-effect mt-1 mt-md-0 waves-light modal-match">Match</a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-1 activity-box {{($ContactNote) ? '' : 'd-none'}}">
                                    <div class="table-responsive custom-scrollbar pr-1" style="max-height: 500px;">
                                        <table class="table table-striped truncate-table">
                                            <thead>
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
                                            @foreach($ContactNote as $note)
                                                @php
                                                    $ActivityProperty=App\Models\Property::where('id',$note->property_id)->first();
                                                    if($ActivityProperty){
                                                        $ActivityMasterProject=App\Models\MasterProject::where('id',$ActivityProperty->master_project_id)->first();
                                                        $ActivityCommunity=App\Models\Community::find($ActivityProperty->community_id);
                                                    }

                                                    $OffPlanProject=App\Models\OffPlanProject::where('id',$note->off_plan_project_id)->first();

                                                @endphp
                                                <tr class="note-description" data-title="{{NoteSubject[$note->note_subject]}}" data-desc="{{($note->status==2 && $note->note_subject==2) ? NoteSubject[$note->note_subject].' Cancelled' : $note->note}}">
                                                    <td>
                                                       <div class="action" data-id="{{$note->id}}" data-model="{{route('contact.note.cancel')}}">
                                                            {!! (($note->note_subject==2 || $note->note_subject==3) && $note->type=="contact" && $note->status==1 && $note->note=='' && date('Y-m-d H:i:s') > $note->date_at.' '.$note->time_at) ? '<a href="#ActivityFeedbackModal" class="feedback-register" data-toggle="modal" data-note="'.$note->note.'"><span class="btn btn-primary" style="min-width:100%">Feedback</span></a>' : '' !!}
                                                            {!! (($note->note_subject==2 || $note->note_subject==3) && $note->status==1 && $note->type=="contact" && date('Y-m-d H:i:s') < $note->date_at.' '.$note->time_at) ? '<a href="javascript:void(0);" class="disabled"><span class="btn btn-danger" style="min-width:100%">Cancel</span></a>' : '' !!}
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
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-gray h-100 px-1 info-box mt-1 mt-sm-0">
                                    <h1 class="text-center pb-1">{{ $company->brand }}</h1>

                                    {!! ($ClientManager) ? '<p class="border-bottom m-0 py-1"><b>CM 1: </b>'.$ClientManager->firstname.' '.$ClientManager->lastname.'</p>' : ''  !!}
                                    {!! ($ClientManagerTwo) ? '<p class="border-bottom m-0 py-1"><b>CM 2: </b>'.$ClientManagerTwo->firstname.' '.$ClientManagerTwo->lastname.'</p>' : ''  !!}
                                    {!! ($contactSource) ? '<p class="border-bottom m-0 py-1"><b>Contact Source: </b>'.$contactSource->name.'</p>' : ''  !!}
                                    <p class="border-bottom m-0 pb-1"><b>Category: </b>{{ ucfirst($Contact->contact_category).(($Developer) ? ' ('.$Developer->name.')':'') }}</p>
                                    {!! ($Contact->contact_category=='buyer' && $Contact->looking_for) ? '<p class="border-bottom m-0 py-1"><b>Looking For: </b>'.BUYER_LOOKING_FOR[$Contact->looking_for].'</p>' : ''  !!}

                                    {!! ($info_show==1) ? '<p class="border-bottom m-0 py-1"><b>Name: </b>'.$Contact->firstname.' '.$Contact->lastname.'</p>' : '' !!}
                                    {!! ($info_show==1 && ($Contact->main_number!='+971' && $Contact->main_number!='')) ? '<p class="border-bottom m-0 py-1"><b>UAE No: </b><a href="tel:'.$Contact->main_number.'">'.$Contact->main_number.'</a></p>': '' !!}
                                    {!! ($info_show==1 && $Contact->number_two) ? '<p class="border-bottom m-0 py-1"><b>Second No: </b><a href="tel:'.$Contact->number_two.'">'.$Contact->number_two.'</a></p>' : ''  !!}
                                    {!! ($info_show==1 && $Contact->email ) ? '<p class="border-bottom m-0 py-1"><b>Email: </b><a href="mailto:'.$Contact->email.'">'.$Contact->email.'</a></p>' : '' !!}
                                    {!! ($info_show==1 && $Contact->address!='') ? '<p class="border-bottom m-0 py-1"><b>Address: </b>'.$Contact->address.'</p>' : '' !!}
                                    {!! ($Contact->buy_type) ? '<p class="border-bottom m-0 py-1"><b>Cash or Finance: </b>'.( ($Contact) ? $Contact->buy_type : '' ).'</p>' : '' !!}
                                    {!! ($Contact->sale_budget) ? '<p class="border-bottom m-0 py-1"><b>Budget: </b>AED '.( ($Contact) ? number_format($Contact->sale_budget) : '').'</p>' : '' !!}
                                    {!! ($Contact->agency_name) ? '<p class="border-bottom m-0 py-1"><b>Agency Name: </b> '.$Contact->agency_name.'</p>' : '' !!}
                                    {!! ($PropertyType!='') ? '<p class="border-bottom m-0 py-1"><b>Property Type: </b>'.rtrim($PropertyType,', ').'</p>' : '' !!}
                                    {!! ($Emirate!='') ? '<p class="border-bottom m-0 py-1"><b>Emirate: </b>'.$Emirate->name.'</p>' : '' !!}
                                    {!! ($MasterProject!='') ? '<p class="border-bottom m-0 py-1"><b>Master Project: </b>'.rtrim($MasterProject,', ').'</p>' : '' !!}
                                    {!! ($Community!='') ? '<p class="border-bottom m-0 py-1"><b>Project: </b>'.rtrim($Community,', ').'</p>' : '' !!}
                                    {!! ($Bedroom!='') ? '<p class="border-bottom m-0 py-1"><b>No. of beds: </b>'.rtrim($Bedroom,', ') .'</p>' : '' !!}
                                    {!! ($Contact->number_cheques!='') ? '<p class="border-bottom m-0 py-1"><b>No. of Cheques: </b>'.$Contact->number_cheques .'</p>' : '' !!}
                                    {!! ($Contact->move_in_day!='') ? '<p class="border-bottom m-0 py-1"><b>Move in Date: </b>'.date('d-m-Y',strtotime($Contact->move_in_day)).'</p>' : '' !!}
                                    {!! (count($Properties)>0) ? '<p class="border-bottom m-0 py-1"><b><a href="#PropertiesAddressModal" data-toggle="modal">Properties owned by the contact</a></b></p>' : '' !!}

                                    @if($info_show==1)
                                    <div class="border-bottom m-0 py-1 justify-content-center d-flex">
                                    {!! ($Contact->main_number!='+971' && $Contact->main_number!='') ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1 px-1" href="https://wa.me/'.$Contact->main_number.'"><i class="font-medium-3 fa fa-whatsapp"></i></a><a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="tel:'.$Contact->main_number.'"><i class="font-medium-3 fa fa-phone"></i> Call</a>': '' !!}

                                    {!! (($Contact->main_number=='+971' || $Contact->main_number=='') && $Contact->number_two) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="https://wa.me/'.$Contact->number_two.'"><i class="font-medium-3 fa fa-whatsapp"></i></a><a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="tel:/'.$Contact->number_two.'"><i class="fa fa-phone"></i> Call</a>' : ''  !!}

                                    {{--{!! ($Contact->email ) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light" href="mailto:'.$Contact->email.'"><i class="fa fa-envelope-o"></i> Email</a>' : '' !!}--}}
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="PropertiesAddressModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-text-bold-600" id="cal-modal">Other Properties</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          <div class="modal-body pt-2">
            @if($Properties)
            <ul class="list-group list-group-flush">
            @foreach($Properties as $Property)
            @php
            $PMasterProject=App\Models\MasterProject::where('id',$Property->master_project_id)->first();
            $PCommunity=App\Models\Community::find($Property->community_id);
            $PClusterStreet=App\Models\ClusterStreet::find($Property->cluster_street_id);
            $PVillaType=App\Models\VillaType::where('id',$Property->villa_type_id)->first();

            $last_activity='Added Date: '.date('d-m-Y',strtotime($Property->created_at));

            $propertyNoteLast=\App\Models\PropertyNote::where('property_id',$Property->id)->orderBy('id','DESC')->first();
            if($propertyNoteLast){
                $last_activity='Last Activity: '.date('d-m-Y',strtotime($propertyNoteLast->created_at));
            }
            @endphp
                <li class="list-group-item">
                    <a href="/admin/property/view/{{$Property->id}}" target="_blank">
                        <p class="m-0"><b>{{$company->sample}}-{{(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num}}</b></p>
                        <p class="m-0">{{(($PMasterProject) ? $PMasterProject->name : '').(($PCommunity) ? ' | '.$PCommunity->name : ''). ( ($adminAuth->type<4 || $Property->admin_id==$adminAuth->id) ? (($PClusterStreet) ? ' | '.$PClusterStreet->name : '').(($PVillaType) ? ' | '.$PVillaType->name : '') : '' ) }}</p>
                        <p class="m-0">{{ $last_activity  }}</p>
                    </a>
                </li>
            @endforeach
            </ul>
            @endif
          </div>
          <!--<div class="modal-footer">
          </div>-->
          </div>
        </div>
    </div>

    <div class="modal fade text-left" id="contactCategoryModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-text-bold-600" id="cal-modal">New Queriy</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          <div class="modal-body pt-2">
            <form method="post" action="{{route('add-contact-category')}}" class="row">
                @csrf
                <div class="col-sm-12">
                    <div class="form-group form-label-group">
                        <label for="ContactCategory">Contact Category <span>*</span></label>
                        <select class="custom-select form-control" id="ContactCategory" name="ContactCategory" required>
                            <option value="">Select</option>
                            <option value="buyer">Buyer</option>
                            <option value="tenant">Tenant</option>
{{--                            <option value="agent">Agent</option>--}}
{{--                            <option value="owner">Owner</option>--}}
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 buyer tenant d-none">
                    <div class="form-group form-label-group">
                        <label>Residential / Commercial</label>
                        <select class="custom-select form-control p-type" id="P_Type" name="P_Type">
                            <option value="">Select</option>
                            @foreach(PropertyType as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 buyer tenant d-none">
                    <div class="form-group form-label-group buyer tenant">
                        <label>Property Type</label>
                        <select class="custom-select form-control modal-cc-select-2 property-type" multiple id="CC_PropertyType" name="PropertyType[]">
                        </select>
                    </div>
                </div>
                <div class="col-sm-12  buyer d-none">
                    <div class="form-group form-label-group buyer">
                        <label>Looking For</label>
                        <select class="custom-select form-control" id="CC_LookingFor" name="LookingFor">
                            <option value="">Select</option>
                            @foreach(BUYER_LOOKING_FOR as $kay=>$value)
                                <option value="{{$kay}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 buyer tenant">
                    <div class="form-group form-label-group">
                        <label for="Emirate">Emirate</label>
                        <select class="custom-select form-control" id="CC_Emirate" name="Emirate">
                            <option value="">Select</option>
                            @php
                                $Emirates=\App\Models\Emirate::get();
                            @endphp
                            @foreach($Emirates as $Emirate)
                                <option value="{{ $Emirate->id }}">{{ $Emirate->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 buyer tenant d-none">
                    <div class="form-group form-label-group buyer tenant">
                        <label for="CC_MasterProject">Master Project</label>
                        <select class="custom-select form-control modal-cc-select-2" multiple id="CC_MasterProject" name="MasterProject[]">

                        </select>
                    </div>
                </div>
                <div class="col-sm-12 buyer tenant">
                    <fieldset class="form-group form-label-group">
                        <label for="CC_Community">Project</label>
                        <select class="form-control  select2" multiple name="Community[]" id="CC_Community">
                            <option value="">Select</option>

                        </select>
                    </fieldset>
                </div>
                <div class="col-sm-6 buyer tenant d-none">
                    <div class="form-group form-label-group">
                        <label for="Bedrooms">Bedrooms</label>
                        <select class="custom-select form-control modal-cc-select-2" multiple id="CC_Bedroom" name="Bedroom[]">
                            @php
                                $Bedrooms=App\Models\Bedroom::get();
                            @endphp
                            @foreach($Bedrooms as $Bedroom)
                                <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 tenant d-none">
                    <div class="form-group form-label-group">
                        <label for="">No. of Cheques </label>
                        <select class="custom-select form-control" id="CC_NumberCheques" name="NumberCheques">
                            <option value="">Select</option>
                            @for ($i = 1; $i < 13; $i++)
                                <option value="{{$i}}">{{$i}}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-sm-6 tenant d-none">
                    <div class="form-group form-label-group">
                        <input type="date" class="form-control" id="CC_MoveInDay" name="MoveInDay" placeholder="Move in Date">
                        <label for="">Move in Date</label>
                    </div>
                </div>
                <div class="col-sm-12 agent d-none">
                    <div class="form-group form-label-group">
                        <input type="text" class="form-control" id="CC_AgencyName" name="AgencyName" placeholder="Agency Name">
                        <label for="">Agency Name</label>
                    </div>
                </div>

                <div class="col-sm-6 buyer tenant d-none">
                    <div class="form-group form-label-group">
                        <input type="text" id="CC_SaleBudget" name="SaleBudget" class="form-control number-format" placeholder="Budget">
                        <label>Budget (AED)</label>
                    </div>
                </div>

                <div class="col-sm-6 buyer d-none">
                    <fieldset class="form-group form-label-group">
                        <select class="form-control" id="CC_BuyType" name="BuyType">
                            <option value="">Select</option>
                            <option value="Cash Purchaser">Cash Purchaser</option>
                            <option value="Mortgage Purchase">Mortgage Purchase</option>
                        </select>
                        <label>Cash/Finance</label>
                    </fieldset>
                </div>
                <div class="col-sm-6 buyer d-none">
                    <fieldset class="form-group form-label-group">
                        <select class="form-control" id="CC_BuyerType" name="BuyerType">
                            <option value="">Select</option>
                            <option value="Investor">Investor</option>
                            <option value="End User">End User</option>
                        </select>
                        <label>Investor / End-user</label>
                    </fieldset>
                </div>

                <div class="col-12 mt-2">
                    <input type="hidden" name="contact" value="{{$Contact->id}}">
                    <button type="button" id="submit" class="btn  bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light float-right">Add</button>
                    <button type="submit" class="btn d-none bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light float-right">{{ ($Contact) ? 'Update' : 'Add' }}</button>
                </div>
            </form>
          </div>
          <!--<div class="modal-footer">
          </div>-->
          </div>
        </div>
    </div>

    @if($contactCategories->count()>0)
    <div class="modal fade text-left" id="contactCategoryShowModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-text-bold-600" id="cal-modal">Queries</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          <div class="modal-body pt-2">
              <ul class="list-group list-group-flush">
              @foreach($contactCategories as $cat)
                  @php
                      $ContactPropertyType=\App\Models\ContactPropertyType::where('cat_id',$cat->id)->get();
                      $contactpt='';
                      foreach($ContactPropertyType as $row){
                          $cPropertyType=\App\Models\PropertyType::find($row->property_type_id);
                          $contactpt.=$cPropertyType->name.', ';
                      }

                      $Emirate=\App\Models\Emirate::find($cat->emirate_id);

                      $ContactMasterProject=\App\Models\ContactMasterProject::where('cat_id',$cat->id)->get();
                      $contactmp='';
                      foreach($ContactMasterProject as $row){
                          $cMasterProject=\App\Models\MasterProject::find($row->master_project_id);
                          $contactmp.=$cMasterProject->name.', ';
                      }

                      $ContactCommunity=\App\Models\ContactCommunity::where('cat_id',$cat->id)->get();
                      $contactcmun='';
                      foreach($ContactCommunity as $row){
                          $cCommunity=\App\Models\Community::find($row->community_id);
                          $contactcmun.=$cCommunity->name.', ';
                      }

                      $ContactBedroom=\App\Models\ContactBedroom::where('cat_id',$cat->id)->get();
                      $contactbed='';
                      foreach($ContactBedroom as $row){
                          $cBedroom=\App\Models\Bedroom::find($row->bedroom_id);
                          $contactbed.=$cBedroom->name.', ';
                      }

                  @endphp
                      <li class="list-group-item">
                      <p class="mb-0"><b>Created At:</b> {{\Helper::changeDatetimeFormat($cat->created_at)}}</p>
                      <p class="mb-0"><b>Contact Category:</b> {{ucfirst(ContactCategory[$cat->cat_id])}}</p>
                      {!! ($cat->looking_for) ? '<p class="mb-0"><b>Looking For:</b> '.BUYER_LOOKING_FOR[$cat->looking_for].'</p>' : ''  !!}
                      {!! ($contactpt) ? '<p class="mb-0"><b>Property Type:</b> '.rtrim($contactpt,", ").'</p>' : ''  !!}
                      {!! ($Emirate) ? '<p class="mb-0"><b>Emirate:</b> '.$Emirate->name.'</p>' : ''  !!}
                      {!! ($contactmp) ? '<p class="mb-0"><b>Master Project:</b> '.rtrim($contactmp,", ").'</p>' : ''  !!}
                      {!! ($contactcmun) ? '<p class="mb-0"><b>Project:</b> '.rtrim($contactcmun,", ").'</p>' : ''  !!}
                      {!! ($contactbed) ? '<p class="mb-0"><b>Bedrooms:</b> '.rtrim($contactbed,", ").'</p>' : ''  !!}
                      {!! ($cat->number_cheques) ? '<p class="mb-0"><b>No. of Cheques:</b> '.$cat->number_cheques.'</p>' : ''  !!}
                      {!! ($cat->move_in_day) ? '<p class="mb-0"><b>Move in Date:</b> '.date('d-m-Y',strtotime($cat->move_in_day)).'</p>' : ''  !!}
                      {!! ($cat->agency_name) ? '<p class="mb-0"><b>Agency Name:</b> '.$cat->agency_name.'</p>' : ''  !!}
                      {!! ($cat->sale_budget) ? '<p class="mb-0"><b>Budget:</b> '.number_format($cat->sale_budget).'</p>' : ''  !!}
                      {!! ($cat->buy_type) ? '<p class="mb-0"><b>Cash/Finance:</b> '.$cat->buy_type.'</p>' : ''  !!}
                      {!! ($cat->buyer_type) ? '<p class="mb-0"><b>Investor/End-user:</b> '.$cat->buyer_type.'</p>' : ''  !!}
                      </li>
                  @endforeach
              </ul>
          </div>
          <!--<div class="modal-footer">
          </div>-->
          </div>
        </div>
    </div>
    @endif

    @if($adminAuth->type<3 && $deals)
    <div class="modal fade text-left" id="dealsModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title text-text-bold-600" id="cal-modal">Deal</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
            </div>
          <div class="modal-body pt-2">
              <ul class="list-group list-group-flush">
              @foreach($deals as $row)
                  <li class="list-group-item">
                      <a href="/admin/deal-view/{{$row->id}}" target="_blank">
                          <p class="mb-0"><b>Ref: </b> {{'D-'.$row->id}}</p>
                          <p class="mb-0"><b>Type: </b> {{($row->type==1) ? 'Rental' : 'Sales'}}</p>
                          {!! ($row->deal_price) ? '<p class="mb-0"><b>Price:</b> '.number_format($row->deal_price).'</p>' : ''  !!}
                          <p class="mb-0"><b>Deal Date:</b> {{date('d-m-Y',strtotime($row->deal_date))}}</p>
                      </a>
                  </li>
              @endforeach
              </ul>
          </div>
          <!--<div class="modal-footer">
          </div>-->
          </div>
        </div>
    </div>
    @endif

    <div class="modal fade" id="matchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Properties</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row my-1">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="client-manager">Client Manager</label>
                                <select class="form-control modal-select-2" multiple id="client-manager">
                                    <option value="">Select</option>
                                    @foreach($ClientManagers as $ClientManager)
                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="client-manager">Client Manager 2</label>
                                <select class="form-control select2" multiple id="client-manager-2" name="client_manager_2">
                                    <option value="">Select</option>
                                    @foreach($ClientManagers as $ClientManager)
                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="listing">Listing Type</label>
                                <select class="form-control" id="listing">
                                    <option value="">Select</option>
                                    <option value="1">Sales</option>
                                    <option value="2">Rent</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label>Residential / Commercial</label>
                                <select class="custom-select form-control p-type-match" id="P_Type" name="P_Type">
                                    <option value="">Select</option>
                                    @foreach(PropertyType as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="property-type">Property Type</label>
                                <select class="form-control select2 property-type-match" multiple  id="property-type">
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="status">Listing Status</label>
                                <select class="form-control modal-select-2" multiple id="status">
                                    <option value="">Select</option>
                                    @foreach(Status as $key => $value)
                                        <option value="{{ $key }}">{{ ( ($key==1) ? 'Listed':$value ).( ($key==5 || $key==7)? $company->sample: '') }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="master-project">Master Project</label>
                                <select class="form-control  modal-select-2" multiple  id="master-project">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="community">Project</label>
                                <select class="form-control  modal-select-2" multiple  id="community">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="bedrooms">Bedrooms</label>
                                <select class="form-control modal-select-2" multiple  id="bedrooms">
                                    <option value="">Select</option>
                                    @php
                                    $Bedrooms=App\Models\Bedroom::get();
                                    @endphp
                                    @foreach($Bedrooms as $Bedroom)
                                        <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="rent-price">Rental Type</label>
                                <select class="form-control" id="rent-price">
                                    <option value="">Select</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-color">Last Activity</label>
                                <select class="form-control" id="select-color" name="select_color">
                                    <option value="">Select</option>
                                    <option value="Green">Less than {{$activity_contact_setting_2->time}} days (Green)</option>
                                    <option value="Yellow">Between {{$activity_contact_setting_2->time}} to {{$activity_contact_setting_3->time}} days (Yellow)</option>
                                    <option value="Red">More than {{$activity_contact_setting_3->time}} days (Red)</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="off_plan">Completion Status</label>
                                <select class="custom-select form-control" id="off_plan" name="off_plan">
                                    <option value="">Select</option>
                                    @foreach(OffPlan as $key=>$value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="property_management">Property Management</label>
                                <select class="form-control" id="property_management" name="property_management">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="2">No</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="status2">Status</label>
                                <select class="custom-select form-control select2" multiple id="status2">
                                    <option value="">Select</option>
                                    @foreach(Status2 as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label for="portal">Portal</label>
                                <select class="custom-select form-control" id="portal" name="portal">
                                    <option value="">Select</option>
                                    @php
                                        $portals=\App\Models\Portal::get();
                                    @endphp
                                    @foreach($portals as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <input type="number" id="ref-number" autocomplete="off" class="form-control" placeholder="Ref Number">
                                <label for="ref-number">Ref Number</label>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="VillaNumber">Villa / Unit Number</label>
                                <input type="text" id="unit-villa-number" class="form-control" placeholder="Villa / Unit Number">
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control" id="rera-permit" placeholder="DLD Permit Number" aria-invalid="false">
                                <label>DLD Permit Number</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group validate">
                                        <label for="from-date">Added Date</label>
                                        <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group rented-until-box">
                                        <label for="to-date">Added Date</label>
                                        <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label>Price (AED)</label>
                                        <input type="text" id="from-price" class="form-control number-format" placeholder="From">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label>Price (AED)</label>
                                        <input type="text" id="to-price" class="form-control number-format" placeholder="To">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-12">
                            <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table truncate-table datatable1 table-striped order-column dataTable">
                                <thead>
                                <tr>

                                    <th>Ref</th>
                                    <th></th>
                                    <th></th>
                                    <th>Master Project</th>
                                    <th>Project</th>
                                    <th>Cluster / Street / Frond</th>
                                    <th>Unit/Villa Number</th>
                                    <th>Type</th>
                                    <th>Bedrooms</th>
                                    <th>Asking Price (AED)</th>
                                    <th>CM 1</th>
                                    <th>CM 2</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($Contact)

    <!-- Modal Activity -->
    @include('admin/activity-modal')

    @endif

@endsection

@section('vendor-script')
    <!-- vendor files -->
{{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>--}}
{{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>--}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script>
        $('.read-more').click(function(){
            if($('#preview-description').hasClass('white_goverlay')){
                $('#preview-description').removeClass('white_goverlay').css('height','unset');
                $(this).html('Read Less');
            }else{
                $('#preview-description').addClass('white_goverlay').css('height','250px');
                $(this).html('Read More');
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
                    url: "{{ route('contact.note.add') }}",
                    type: "POST",
                    data: {
                        _token: $('form input[name="_token"]').val(),
                        contact: "{{ ($Contact) ? $Contact->id : '' }}",
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
                url:"{{ route('contact.note.edit') }}",
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
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            fixedColumns: {
                start: 1
            },
            scrollX: true,
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            "info": false,
            'serverMethod': 'post',
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('property.get.datatable') }}',
                'data': function(data){
                    // alert($('#status').val());
                    // Append to data
                    data.FetchUser = 1;
                    data._token='{{csrf_token()}}';
                    data.listing=$('#listing').val();
                    data.status=$('#status').val().join(",");
                    data.type=$('#type').val();
                    data.property_type=$('#property-type').val().join(",");
                    data.client_manager=$('#client-manager').val().join(",");
                    data.client_manager_2=$('#client-manager-2').val().join(",");
                    data.master_project=$('#master-project').val().join(",");
                    data.community=$('#community').val().join(",");
                    data.bedrooms=$('#bedrooms').val().join(",");
                    data.off_plan=$('#off_plan').val();
                    data.unit_villa_number=$('#unit-villa-number').val();
                    data.rent_price=$('#rent-price').val();
                    data.status2=$('#status2').val().join(",");
                    data.from_price=$('#from-price').val();
                    data.to_price=$('#to-price').val();
                    data.id=$('#ref-number').val();
                    data.portal=$('#portal').val();
                    data.property_management=$('#property_management').val();
                    data.rera_permit=$('#rera-permit').val();
                    data.select_color=$('#select-color').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 1,13 ]}],
            'columns': [
                {data: 'id'},
                {data: 'img'},
                {data: 'status'},
                {data: 'master_project_id'},
                {data: 'community_id'},
                {data: 'cluster_street_id'},
                {data: 'villa_number'},
                {data: 'villa_type_id'},
                {data: 'bedroom_id'},
                {data: 'expected_price'},
                {data: 'client_manager_id'},
                {data: 'client_manager2_id'},
                {data: 'status2'},
                {data: 'action_view'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('#matchModal .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                window.open('/admin/property/view/'+id+'?c={{$Contact->id}}&match=true');
                // window.location.href ='/admin/property/view/'+id+'?c={{$Contact->id}}';
            }

        });
    </script>
    <script>
        getMasterProject('2');
        function getMasterProject(val){
            $.ajax({
                url:"{{ route('master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('#master-project').html(response);
                }
            });
        }

        $('#master-project').change(function () {
            let val=$(this).val();
            if(val.length<2){
                getCommunity(val);
                $('#community , #unit-villa-number').removeAttr('disabled');
            }else{
                getCommunity('');
                $('#community , #unit-villa-number').attr('disabled','disabled');
            }
            $('#Community').change();
        });

        function getCommunity(val){
            $.ajax({
                url:"{{ route('community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    $('#community').html(response);
                }
            });
        }

        $('#ActivityContactLabel').html('Property');

        @if(request('p'))
            let property='{{request('p')}}';

            $.ajax({
                url:"{{ route('get-property-ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    property:property
                },
                success:function (response) {
                    let txt='{{$company->sample}}-';
                    txt+=((response.listing_type_id==1) ? 'S' : 'R')+'-'+response.ref_num;

                    $('.select-2-user')
                        .empty()
                        .append('<option selected value="'+response.id+'">'+txt+'</option>');
                    $('.select-2-user').select2('data', {
                        id: response.id,
                        label:txt
                    });
                }
            });
        @endif
    </script>
    <script>
        $('#ContactCategory').change(function(){
            let val=$(this).val();
            $('.buyer , .tenant , .agent , .owner').addClass('d-none');

            $('.'+val).removeClass('d-none');

            $("#CC_SaleBudget , #CC_Emirate , #CC_MasterProject , #P_Type , #CC_PropertyType , #CC_LookingFor, #CC_Bedroom , #CC_BuyType , #CC_BuyerType").parent().removeClass('error').children('label').children('span').remove();
            if (val == 'buyer' || val == 'tenant') {
                $("#CC_SaleBudget , #CC_Emirate , #CC_MasterProject , #P_Type , #CC_PropertyType , #CC_LookingFor, #CC_BuyType , #CC_BuyerType").parent().children('label').append('<span>*</span>');
            }
        });

        $('#submit').click(function () {
            let error=0;

            let budget=$('#CC_SaleBudget').val();
            let category=$('#ContactCategory').val();

            let MasterProject=$('#CC_MasterProject').val();
            let PropertyType=$('#CC_PropertyType').val();
            let LookingFor=$('#CC_LookingFor').val();
            //let Bedroom=$('#Bedroom').val();
            let BuyType=$('#CC_BuyType').val();
            let BuyerType=$('#CC_BuyerType').val();

            if(category=='buyer' || category=='tenant'){
                if(MasterProject==''){
                    $("#CC_MasterProject").parent().addClass('error');
                    error=1
                }
                if(PropertyType==''){
                    $("#CC_PropertyType").parent().addClass('error');
                    error=1
                }
                // if(Bedroom==''){
                //     $("#Bedroom").parent().addClass('error');
                //     error=1
                // }

                if(budget==''){
                    $("#CC_SaleBudget").parent().addClass('error');
                    error=1
                }
                if(category=='buyer'){
                    if(LookingFor=='1' && BuyType==''){
                        $("#CC_BuyType").parent().addClass('error');
                        error=1
                    }

                    if(BuyerType==''){
                        $("#CC_BuyerType").parent().addClass('error');
                        error=1
                    }
                }
            }else{
                $("#CC_SaleBudget").parent().removeClass('error');
            }

            if(error==0){
                $('#contactCategoryModal button[type="submit"]').click();
            }
        });

        $('#CC_LookingFor').change(function(){
            let val=$(this).val();

            $("#CC_BuyType").val('');
            $("#CC_BuyType").parent().removeClass('error');
            $('#CC_BuyType').attr('disabled','disabled');
            $("#CC_BuyType").parent().children('label').children('span').remove();

            $('#CC_Community').parent().removeClass('d-none');
            if(val=='1') {
                $('#CC_BuyType').parent().children('label').append('<span>*</span>');
                $('#CC_BuyType').removeAttr('disabled');
            }
            if(val=='2') {
                $('#CC_Community').parent().addClass('d-none');
            }
        });
    </script>

    <script>
        $('.p-type').change(function(){
            let type=$(this).val();

            $("#CC_Bedroom").removeAttr('disabled').val('');
            if (type == '2') {
                $("#CC_Bedroom").attr('disabled','disabled').val('');
            }
            getPropertyType(type);
        });

        function getPropertyType(type){
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('.property-type').html(response);
                }
            });
        }

        $('.p-type-match').change(function(){
            let type=$(this).val();
            getPropertyTypeMatch(type);
        });

        function getPropertyTypeMatch(type){

            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('.property-type-match').html(response);
                }
            });
        }

        $(".modal-select-2").select2({
            placeholder: "Select",
            dropdownParent:$('#matchModal'),
            dropdownAutoWidth: true,
            width: '100%'
        });

        $('.modal-select-2').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $(".modal-cc-select-2").select2({
            placeholder: "Select",
            dropdownParent:$('#contactCategoryModal'),
            dropdownAutoWidth: true,
            width: '100%'
        });

        $('.modal-cc-select-2').on('select2:open', function (e) {
            const evt = "scroll.select2";
            $(e.target).parents().off(evt);
            $(window).off(evt);
        });

        $('.add-contact-cat-btn').click(function () {
            $('#CC_NumberCheques , #CC_MoveInDay , #CC_AgencyName , #CC_SaleBudget , #CC_BuyType , #CC_BuyerType').val('');
            $('#P_Type , #CC_PropertyType , #CC_MasterProject , #CC_Bedroom ').val('').change();
            $('#ContactCategory').val('').change();
        });


        $('#CC_Emirate').change(function () {
            let val=$(this).val();
            getCCMasterProject(val);
        });

        function getCCMasterProject(val){
            $.ajax({
                url:"{{ route('master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('#CC_MasterProject').html(response);
                }
            });
        }

        $('#CC_MasterProject').change(function () {
            let val=$(this).val();
            if(val.length<2){
                getCommunity(val);
                $('#CC_Community').removeAttr('disabled');
            }else{
                getCommunity('');
                $('#CC_Community').attr('disabled','disabled');
            }
            $('#CC_Community').change();
        });

        function getCommunity(val){
            $.ajax({
                url:"{{ route('community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    $('#CC_Community').html(response);
                }
            });
        }

        $('.modal-match').click(function () {
            $('#listing').val([{!! (($Contact->contact_category=='buyer' ||$Contact->contact_category=='tenant') ? (($Contact->contact_category=='buyer') ? '1' : '2') : '' ) !!}]).trigger('change');
            $('#master-project').val([{!! substr($contactmp_id,0,-1) !!}]).trigger('change');
            $('#bedrooms').val([{!! substr($contactbed_id,0,-1) !!}]).trigger('change');
            table.draw();
        });
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
