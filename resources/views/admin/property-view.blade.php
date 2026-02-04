
@extends('layouts/contentLayoutMaster')

@section('title', 'Property')

@section('vendor-style')
    <!-- vendor css files -->
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">

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

        .border-color .border-left{
            border-color:#d3d2d2 !important;
        }

        .info-box .pb-1, .info-box .py-1 {
            padding-top: 0.7rem !important;
            padding-bottom: 0.7rem !important;
        }
        .slider-nav{
            margin-bottom: 0px;
        }
	</style>
@endsection
@section('content')
    <!-- Form wizard with step validation section start -->

@php
    if($Property){

        $mc=0;
        $expected_price=0;
        if($Property->expected_price){
            $expected_price=$Property->expected_price;
            //if($Property->property_type_id==3 || $Property->property_type_id==4 || $Property->property_type_id==6){
                $mc= $Property->bua==0 ? 0 : ($Property->expected_price/$Property->bua) ;
            //}else{
            //    $mc= $Property->plot_sqft==0 ? 0 : ($Property->expected_price/$Property->plot_sqft);
            //}
        }

        if($Property->listing_type_id==2){
            if($Property->yearly){
                $expected_price=$Property->yearly;
            }else if($Property->monthly){
                $expected_price=$Property->monthly;
            }else if($Property->weekly){
                $expected_price=$Property->weekly;
            }else{
                $expected_price=$Property->daily;
            }
        }

        $adminAuth=\Auth::guard('admin')->user();
        $company=\App\Models\Company::find($adminAuth->company_id);

        $PropertyParent=App\Models\Property::where('id',$Property->parent_id)->first();
        $PropertyType=App\Models\PropertyType::where('id',$Property->property_type_id)->first();
        $ClientManager=App\Models\Admin::where('id',$Property->client_manager_id )->first();
        $ClientManager_2=App\Models\Admin::where('id', $Property->client_manager2_id )->first();
        $Contact=App\Models\Contact::where('id',$Property->contact_id)->first();
        $MasterProject=App\Models\MasterProject::where('id',$Property->master_project_id)->first();
        $Community=App\Models\Community::find($Property->community_id);
        $ClusterStreet=App\Models\ClusterStreet::find($Property->cluster_street_id);
        $VillaType=App\Models\VillaType::where('id',$Property->villa_type_id)->first();
        $Bedroom=App\Models\Bedroom::where('id',$Property->bedroom_id)->first();
        $Bathroom=App\Models\Bathroom::where('id',$Property->bathroom_id)->first();
        $View=App\Models\View::where('id',$Property->view)->first();

        $bedroomText='Bedrooms';
            if($Bedroom && $Bedroom->name=='1')
                $bedroomText='Bedroom';
        $bathroomText='Bathrooms';
            if($Bathroom && $Bathroom->name=='1')
                $bathroomText='Bathroom';

        $PFeatures = App\Models\PropertyFeature::join('features', 'features.id', '=', 'property_features.feature_id')->where('property_id',$Property->id)->get();

        $noteNotFeedback=DB::select("SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND contact_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, contact_id, note, date_at, time_at, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < DATE_SUB(NOW(), INTERVAL 2 DAY) AND note_subject IN (2,3) AND property_note.status=1 AND `note` is null AND admin_id=".$adminAuth->id." ORDER BY created_at desc");

        $cluster_street='';
        $villa_number='';
        $fullName='';
        $contactNumber='';
        $contactEmail='';

        $ma_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_ma')->first();

        $activity_reg=0;
        if($ma_setting->status==1 && ($Property->status==2 || $Property->status==3 || $Property->status==4) && $adminAuth->type==4
        && !($Property->client_manager_id == $adminAuth->id || $Property->client_manager2_id == $adminAuth->id) ){
            //$ma_setting_admin=\App\Models\SettingAdmin::where('setting_id',$ma_setting->id)->where('admin_id',$Property->client_manager_id)->first();
            $ma_setting_admin=\App\Models\SettingAdmin::whereIn('admin_id',[$Property->client_manager_id,$Property->client_manager2_id])->where('setting_id',$ma_setting->id)->first();
            if(!$ma_setting_admin) {

                $last_activity=$Property->created_at;

                $propertyNoteLast=\App\Models\PropertyNote::whereIn('admin_id', [$Property->client_manager_id,$Property->client_manager2_id])->where('property_id',$Property->id)->orderBy('id','DESC')->first();
                if($propertyNoteLast){//$Property->last_activity
                    $last_activity=$propertyNoteLast->created_at;//$Property->last_activity;
                }

                $today = \Carbon\Carbon::now();
                $today = $today->format('Y/n/j H:i:s');
                $date_two = \Carbon\Carbon::parse($last_activity);
                $minutes = $date_two->diffInMinutes($today);
                $hours = $date_two->diffInHours($today);
                $days = $date_two->diffInDays($today);

                if($ma_setting->time_type==1 && $minutes >= $ma_setting->time){
                    $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : '';
                    $villa_number = $Property->villa_number;
                    $fullName = $Contact->firstname.' '.$Contact->lastname;
                    $contactNumber = ( ($Contact) ? ($Contact->main_number!='' && $Contact->main_number!='+971') ? '<a href="tel:'.$Contact->main_number.'">'.$Contact->main_number.'</a>' : '<a href="tel:'.$Contact->number_two.'">'.$Contact->number_two.'</a>' : '' );
                    $contactEmail = $Contact->email.'">'.$Contact->email;

                    $activity_reg=1;
                }

                if($ma_setting->time_type==2 && $hours >= $ma_setting->time){
                    $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : '';
                    $villa_number = $Property->villa_number;
                    $fullName = $Contact->firstname.' '.$Contact->lastname;
                    $contactNumber = ( ($Contact) ? ($Contact->main_number!='' && $Contact->main_number!='+971') ? '<a href="tel:'.$Contact->main_number.'">'.$Contact->main_number.'</a>' : '<a href="tel:'.$Contact->number_two.'">'.$Contact->number_two.'</a>' : '' );
                    $contactEmail = $Contact->email.'">'.$Contact->email;

                    $activity_reg=1;
                }

                if($ma_setting->time_type==3 && $days >= $ma_setting->time){
                    $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : '';
                    $villa_number = $Property->villa_number;
                    $fullName = $Contact->firstname.' '.$Contact->lastname;
                    $contactNumber = ( ($Contact) ? ($Contact->main_number!='' && $Contact->main_number!='+971') ? '<a href="tel:'.$Contact->main_number.'">'.$Contact->main_number.'</a>' : '<a href="tel:'.$Contact->number_two.'">'.$Contact->number_two.'</a>' : '' );
                    $contactEmail = $Contact->email.'">'.$Contact->email;

                    $activity_reg=1;
                }
            }
        }else {
            if ($adminAuth->type != 4 || $Property->client_manager_id == $adminAuth->id || $Property->client_manager2_id == $adminAuth->id) {
                $cluster_street = ($ClusterStreet) ? $ClusterStreet->name : '';
                $villa_number = $Property->villa_number;
                $fullName = $Contact->firstname.' '.$Contact->lastname;
                $contactNumber = ( ($Contact) ? ($Contact->main_number!='' && $Contact->main_number!='+971') ? '<a href="tel:'.$Contact->main_number.'">'.$Contact->main_number.'</a>' : '<a href="tel:'.$Contact->number_two.'">'.$Contact->number_two.'</a>' : '' );
                $contactEmail = $Contact->email.'">'.$Contact->email;

                $activity_reg=1;
            }
        }
    }
@endphp
    <div class="card">
        <!--<div class="card-header">
            <h4 class="card-title">Add New Property</h4>
        </div>-->
        <div class="card-content">
            <div class="card-body container">
                <div class="row">
                    <div class="clearfix col-sm-12">
                        <div class="float-md-left">
                            <p style="margin-bottom:5px;" class="text-gray">
                                <b>For {{(($Property->listing_type_id==1) ? 'Sale' : 'Rent')}}: {{$company->sample}}-{{(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num .(($Property->status) ? ' - '.Status[$Property->status] : '')}}</b>
                                @if($PropertyParent) | <small>Copied From: <a target="_blank" href="/admin/property/view/{{$PropertyParent->id}}">{{$company->sample}}-{{(($PropertyParent->listing_type_id==1) ? 'S' : 'R').'-'.$PropertyParent->ref_num}}</a></small>@endif

                            </p>
                            {{--( ($adminAuth->type!=4 || $Property->admin_id==$adminAuth->id) ? $cluster_street.(($Property->villa_number) ? ' | '.'No '.$villa_number : '') : '' )--}}
                            <p style="margin-bottom:5px;"><b>{{(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' | '.$Community->name : '').(($cluster_street) ? ' | '.$cluster_street : '').(($villa_number) ? ' | '.'No '.$villa_number : '')  }}</b></p>
                            {!! ($Property && $Property->rera_permit) ? '<p style="margin-bottom:5px;" class="text-gray"><b>DLD PERMIT: '.$Property->rera_permit.'</b></p>' : '' !!}

                        </div>
                        <div class="float-md-right" id="preview-price">
                            <b>AED {{number_format($expected_price)}}</b>
                            {!! ($Property->listing_type_id==1) ? '<p class="m-0 text-gray">AED '.number_format( $mc ).' per Sq Ft</p>' : '' !!}
                        </div>
                    </div>
                    <div class="clearfix my-1 col-sm-12">
                        @php
                        //$Previous=App\Models\Property::where('id','>',$Property->id)->orderBy('id','ASC')->first();
                        //$Next=App\Models\Property::where('id','<',$Property->id)->orderBy('id','DESC')->first();
                        @endphp
                        <div class="float-left">
                            <a href="/admin/property/view/{{($Previous) ? $Previous : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Previous) ? '' : 'disabled'}}">
                                <span class="d-none d-sm-block">Previous</span>
                                <span class="d-block d-sm-none"><</span>
                            </a>
                            <a href="/admin/property/view/{{($Next) ? $Next : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Next) ? '' : 'disabled'}}">
                                <span class="d-none d-sm-block">Next</span>
                                <span class="d-block d-sm-none">></span>
                            </a>
                        </div>
                        @if(!request('match') && $company->package!=1)
                        <div class="float-right">
                            <a href="#matchModal" data-toggle="modal" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light modal-match">Match</a>
                        </div>
                        @endif
                    </div>

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-8 mb-1 mb-md-0">
                                <div class="wrap-modal-slider">
                                    <div class="slider slider-for">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    <div><img class="img-fluid" src="/storage/{{ $picture }}"></div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                    <div class="slider slider-nav">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    <div><img class="img-fluid" src="/storage/{{ $picture }}"></div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="bg-gray h-100  px-1 info-box" style="position: relative;padding-bottom: 50px">
                                    <h1 class="text-center py-1">{{ $company->brand }}</h1>
                                    {!! ($ClientManager) ? '<p class="border-bottom m-0 pb-1"><b>CM 1: </b>'.$ClientManager->firstname.' '.$ClientManager->lastname.'</p>' : ''  !!}
                                    {!! ($ClientManager_2) ? '<p class="border-bottom m-0 py-1"><b>CM 2: </b>'.$ClientManager_2->firstname.' '.$ClientManager_2->lastname.'</p>' : ''  !!}
                                    <p class="border-bottom m-0 py-1"><b>Added Date: </b>{{ \Helper::changeDatetimeFormat( $Property->created_at) }}</p>
                                    @if($Contact && $Contact->private==0)
                                    {!! ($fullName) ? (($Contact) ? '<p class="border-bottom m-0 py-1"><b>'.( ($Contact->contact_category=='agent') ? 'Agent' : 'Owner' ).': </b>'.$fullName.'</p>' : '') : '' !!}
                                    {!! ($contactNumber) ? '<p class="border-bottom m-0 py-1"><b>Contact Number: </b>'.$contactNumber.'</p>' : '' !!}
                                    {!! ($contactEmail) ? (($Contact && $Contact->email) ?'<p class="border-bottom m-0 py-1"><b>Email: </b><a href="mailto:'.$contactEmail.'</a></p>' : '') : '' !!}
                                    @endif
{{--                                    {!! ($adminAuth->type!=4 || $Property->client_manager_id==$adminAuth->id || $Property->client_manager2_id==$adminAuth->id) ? (($Contact) ? '<p class="border-bottom m-0 py-1"><b>OWNER: </b>'..'</p>' : '') : '' !!}--}}
{{--                                    {!! ($adminAuth->type!=4 || $Property->client_manager_id==$adminAuth->id || $Property->client_manager2_id==$adminAuth->id) ? '<p class="border-bottom m-0 py-1"><b>Contact Number: </b>'..'</p>' : '' !!}--}}
{{--                                    {!! ($adminAuth->type!=4 || $Property->client_manager_id==$adminAuth->id || $Property->client_manager2_id==$adminAuth->id) ? (($Contact && $Contact->email) ?'<p class="border-bottom m-0 py-1"><b>Email: </b><a href="mailto:'..'</a></p>' : '') : '' !!}--}}
                                    {!! ($Property->status2) ? '<p class="border-bottom m-0 py-1"><b>Status: </b>'.Status2[$Property->status2].'</p>' : '' !!}
                                    {!! ($Property->off_plan) ? '<p class="border-bottom m-0 py-1"><b>Ready Or Off Plan: </b>'.OffPlan[$Property->off_plan].'</p>' : '' !!}
                                    @if($Property->expiration_date)<p class="border-bottom border-top m-0 py-1"><b>Listing Form Expiration: </b> {{ date('d-m-Y',strtotime($Property->expiration_date)) }}</p>@endif
                                    @if($Property->daily)<p class="border-bottom border-top m-0 py-1"><b>Daily: </b> AED {{ number_format($Property->daily) }}</p>@endif
                                    @if($Property->weekly)<p class="border-bottom border-top m-0 py-1"><b>Weekly: </b> AED {{ number_format($Property->weekly) }}</p>@endif
                                    @if($Property->monthly)<p class="border-bottom border-top m-0 py-1"><b>Monthly: </b> AED {{ number_format($Property->monthly) }}</p>@endif
                                    @if($Property->yearly)<p class="border-bottom border-top m-0 py-1"><b>Yearly: </b> AED {{ number_format($Property->yearly) }}</p>@endif
                                    @if($Property->status2==6)
                                    {{--<p class="border-bottom border-top m-0 py-1"><b>Rented For: </b>{{ ($Property->rent_price) ? number_format($Property->rent_price) : '' }}</p>--}}
                                    <p class="m-0 py-1"><b>Rented Until: </b>{{ ($Property->rented_until) ? $Property->rented_until : '' }}</p>
                                    @endif
                                    {!! ($Property->viewing_arrangement) ? '<p class="border-bottom m-0 py-1"><b>Viewing: </b>'.$Property->viewing_arrangement.'</p>' : '' !!}

                                    @if($Contact && $Contact->private==0)
                                    @if(($adminAuth->type!=4 || $Property->client_manager_id==$adminAuth->id || $Property->client_manager2_id==$adminAuth->id))
                                        <div class="border-bottom m-0 py-1 justify-content-center d-flex">
                                        {!! ($Contact->main_number!='+971' && $Contact->main_number!='') ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1 px-1" href="https://wa.me/'.$Contact->main_number.'"><i class="font-medium-3 fa fa-whatsapp"></i></a><a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="tel:'.$Contact->main_number.'"><i class="font-medium-3 fa fa-phone"></i> Call</a>': '' !!}

                                        {!! (($Contact->main_number=='+971' || $Contact->main_number=='') && $Contact->number_two) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="https://wa.me/'.$Contact->number_two.'"><i class="font-medium-3 fa fa-whatsapp"></i></a><a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light mr-1" href="tel:/'.$Contact->number_two.'"><i class="fa fa-phone"></i> Call</a>' : ''  !!}

                                        {{--{!! ($Contact->email ) ? '<a style="width:89px;padding-left:5px;padding-right:5px" class="btn btn-outline-success waves-effect waves-light" href="mailto:'.$Contact->email.'"><i class="fa fa-envelope-o"></i> Email</a>' : '' !!}--}}
                                        </div>
                                    @endif
                                    @endif
                                    <div style="position: absolute;width: 92%;bottom: 10px">
                                        <a href="/property/brochure/{{ \Helper::idCode($Property->id).(($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) }}" target="_blank" style="padding-top:1rem;padding-bottom:1rem;" class="btn bg-gradient-info px-2 waves-effect waves-light w-100">Brochure</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mt-1">
                        <div class="row bg-gray border rounded py-2 m-0 justify-content-center border-color">
                            <div class="col-6 col-md-2 mb-1 text-center">
                                <p class="m-0"><b>Category</b></p>
                                <p class="m-0">{{($PropertyType) ? $PropertyType->name : ''}}</p>
                            </div>

                            <div class="col-6 col-md-2 mb-1 text-center border-left border-color-gold">
                                <p class="m-0"><b>Type</b></p>
                                <p class="m-0">{{($VillaType) ? $VillaType->name : 'N/A'}}</p>
                            </div>

                            <div class="col-6 col-md-2 mb-1 text-center border-left border-left-mobile-unset">
                                <p class="m-0"><b><i class="fa fa-bed"></i><!--No. of Beds--></b></p>
                                <p class="m-0">{{($Bedroom) ? $Bedroom->name : 'N/A'}}</p>
                            </div>

                            <div class="col-6 col-md-2 mb-1 text-center border-left">
                                <p class="m-0"><b>BUA</b></p>
                                <p class="m-0">{{number_format($Property->bua)}} Sq Ft</p>
                            </div>
                            {{--@if($Property->plot_sqft)--}}
                            <div class="col-6 col-md-2 mb-1 text-center border-left border-left-mobile-unset">
                                <p class="m-0"><b>Plot</b></p>
                                <p class="m-0">{{($Property->plot_sqft) ? number_format($Property->plot_sqft).' Sq Ft' : 'N/A'}}</p>
                            </div>
                            {{--@endif--}}
                            {{--@if($Property->expected_price)--}}
                            <div class="col-6 col-md-2 mb-1 text-center border-left">
                                <p class="m-0"><b>Price (AED)</b></p>
                                <p class="m-0 truncate">{{number_format($expected_price)}}</p>
                            </div>
                            {{--@endif--}}
                            @if($Property->number_cheques)
                            <!--<div class="col-sm-3 text-center border-left">
                                <p class="m-0"><b>No. of Cheques</b></p>
                                <p class="m-0">{{$Property->number_cheques}}</p>
                            </div>-->
                            @endif


                        </div>
                    </div>
                    <div class="col-sm-12">
                        <h5 class="mt-1 mb-0 pb-1" style="border-bottom: 1px solid #2c2c2c;">Description</h5>
                        <div id="preview-description" class="white_goverlay pt-2" style="height:250px;">
                            <p>{!!($Property) ? nl2br($Property->description) : ''!!}</p>
                            <ul class="order_list list-inline-item p-2">
                                {!! ($VillaType) ? '<li>Type: '.$VillaType->name.'</li>' : '' !!}
								{!! ($Property->bua) ? '<li>BUA: '.number_format($Property->bua).' Sq Ft</li>' : '' !!}
                                {!! ($Property->plot_sqft) ? '<li>Plot: '.number_format($Property->plot_sqft).' Sq Ft</li>' : '' !!}
                                {!! ($Bedroom) ? '<li>'.$Bedroom->name.' '.( ($Bedroom->name != 'Studio') ? ' '.$bedroomText:'' ).'</li>' : '' !!}
                                {!! ($Bathroom && $Bathroom->name!='0') ? '<li>'.$Bathroom->name.' '.$bathroomText.'</li>' : '' !!}
                                {!! ($Property->maid=='Yes') ? "<li>Maid's Room</li>" : '' !!}
                                {!! ($Property->study=='Yes') ? '<li>Study Room</li>' : '' !!}
                                {!! ($Property->storage=='Yes') ? '<li>Storage Room</li>' : '' !!}
                                {!! ($View) ? '<li>'.$View->name.'</li>' : '' !!}
                                {!! ($Property->furnished && ($Property->property_type_id!=19 && $Property->property_type_id!=29)) ? '<li>'.$Property->furnished.'</li>' : '' !!}
                                {!! ($Property->parking && $Property->parking!='0') ? '<li>'.$Property->parking.' Parking</li>' : '' !!}
                                {!! ($Property->usp) ? '<li>'.$Property->usp.'</li>' : '' !!}
                                {!! ($Property->usp2) ? '<li>'.$Property->usp2.'</li>' : '' !!}
                                {!! ($Property->usp3) ? '<li>'.$Property->usp3.'</li>' : '' !!}
                                {!! ($Property->status2 && ($Property->property_type_id!=19 && $Property->property_type_id!=29)) ? '<li>'.Status2[$Property->status2].'</li>' : '' !!}
							</ul>
                        </div>
                    </div>

                    <div class="clearfix col-12">
						<a href="javascript:void(0);" class="read-more float-right">Read More</a>
					</div>

                    <div class="clearfix col-sm-12">
                        <div class="float-left">
                            @if($activity_reg==1) <button type="button" class="btn-activity btn btn-outline-success mr-1 mb-1 waves-effect waves-light float-left {{ ($noteNotFeedback) ? 'warning-feedback' : '' }}" {!! ($noteNotFeedback) ? '' : 'data-target="#ActivityModal" data-toggle="modal"' !!}>Activity</button> @endif
                        </div>
                        <div class="float-right">


                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 activity-box {{($PropertyNote) ? '' : 'd-none'}}">
                        <div class="table-responsive custom-scrollbar pr-1" style="max-height: 450px;">
                            <table class="table table-striped truncate-table mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Activity Type</th>
                                        <th>Contact</th>
                                        <th>Feedback / Note</th>
                                        <th>CM</th>
                                        <th>Added Date</th>
                                    </tr>
                                </thead>
                                <tbody id="div_notes_section">
                                    @foreach($PropertyNote as $note)
                                    @php
                                    $ActivityContact='';
                                    $ActivityContact=App\Models\Contact::where('id',$note->contact_id)->first();
                                    @endphp
                                    <tr class="note-description" data-title="{{NoteSubject[$note->note_subject]}}" data-desc="{{($note->status==2 && $note->note_subject==2) ? NoteSubject[$note->note_subject].' Cancelled' : $note->note}}">
                                        <td>
                                            <div class="action" data-id="{{$note->id}}" data-model="{{route('property.note.cancel')}}">
                                                {!! (($note->note_subject==2 || $note->note_subject==3) && $note->type=="property" && $note->status==1 && $adminAuth->id==$note->admin_id && $note->note=='' && date('Y-m-d H:i:s') > $note->date_at.' '.$note->time_at ) ? '<a href="#ActivityFeedbackModal" class="feedback-register" data-toggle="modal" data-note="'.$note->note.'"><span class="btn btn-primary" style="min-width:100%">Feedback</span></a></a>' : '' !!}
                                                {!! (($note->note_subject==2 || $note->note_subject==3) && $note->status==1 && $note->type=="property" && date('Y-m-d H:i:s') < $note->date_at.' '.$note->time_at) ? '<a href="javascript:void(0);" class="disabled"><span class="btn btn-danger" style="min-width:100%">Cancel</span></a>' : '' !!}
                                            </div>
                                        </td>
                                        <td data-target="#ViewModal" data-toggle="modal" >{{NoteSubject[$note->note_subject]}}</td>
                                        <td>{!! ($ActivityContact) ? '<a href="/admin/contact/view/'.$ActivityContact->id.'">'.$ActivityContact->firstname.' '.$ActivityContact->lastname.'</a>' : 'N/A' !!}</td>
                                        <td data-target="#ViewModal" data-toggle="modal" >
                                            {!! ( ($note->date_at) ? \Helper::changeDatetimeFormat( $note->date_at.' '.$note->time_at).'<br>' : '' )
                                                .'<span class="note{{$note->id}}">'.\Illuminate\Support\Str::limit(strip_tags($note->note),50)
                                                .( ( $note->status==2) ? (($note->note) ? '<br>':'').'<span class="text-danger">'.NoteSubject[$note->note_subject].' Cancelld</span>' : '' )
                                                 .'</span>' !!}
                                        </td>
                                        <td data-target="#ViewModal" data-toggle="modal" >{{$note->firstname.' '.$note->lastname}}</td>
                                        <td data-target="#ViewModal" data-toggle="modal" >{{\Helper::changeDatetimeFormat( $note->created_at)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="matchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable  modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Contacts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @php
                    $ClientManagers=\Helper::getCM_DropDown_list('1');
                @endphp
                <div class="modal-body">
                    <div class="row my-1" id="match-filter">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-client-manager">Client Manager</label>
                                <select class="form-control modal-select-2" multiple id="select-client-manager">
                                    @foreach($ClientManagers as $ClientManager)
                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-client-manager-2">Client Manager 2</label>
                                <select class="form-control modal-select-2" multiple id="select-client-manager-2">
                                    @foreach($ClientManagers as $ClientManager)
                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label>Looking For</label>
                                <select class="custom-select form-control" id="select-looking-for" name="looking_for">
                                    <option value="">Select</option>
                                    @foreach(BUYER_LOOKING_FOR as $kay=>$value)
                                        <option value="{{$kay}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-contact-categories">Contact Categories</label>
                                <select class="form-control select2" multiple id="select-contact-categories" name="contact_categories">
                                    <option value="buyer">Buyer</option>
                                    <option value="tenant">Tenant</option>
                                    <option value="agent">Agent</option>
                                    <option value="owner">Owner</option>
                                    <option value="developer">Developer</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="select-developer">Developer</label>
                                <select class="custom-select form-control select2" multiple id="select-developer" name="select_developer">
                                    <option value="">Select</option>
                                    @php
                                        $developers=\App\Models\Developer::orderBy('name','DESC')->get();
                                    @endphp
                                    @foreach($developers as $dev)
                                        <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-Color">Last Activity</label>
                                <select class="form-control" id="select-Color">
                                    <option value="">Select</option>
                                    <option value="Green">Less than 15 days (Green)</option>
                                    <option value="Yellow">Between 15 to 30 days (Yellow)</option>
                                    <option value="Red">More than 30 days (Red)</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-finance-status">Finance Status</label>
                                <select class="form-control" id="select-finance-status">
                                    <option value="">Select</option>
                                    <option value="Cash Purchaser">Cash Purchaser</option>
                                    <option value="Mortgage Purchaser">Mortgage Purchaser</option>
                                    <option value="Swapping Deal">Swapping Deal</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="Emirate">Emirate</label>
                                <select class="custom-select form-control" id="emirate">
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
                        <div class="col-sm-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-master-project">Master Project</label>
                                <select class="form-control  modal-select-2" multiple id="select-master-project">
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-3">
                            <fieldset class="form-group form-label-group">
                                <label for="Community">Project</label>
                                <select class="form-control  select2" multiple id="Community">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label>Residential / Commercial</label>
                                <select class="custom-select form-control" id="type" name="type">
                                    <option value="">Select</option>
                                    @foreach(PropertyType as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label>Property Type</label>
                                <select class="custom-select form-control modal-select-2" multiple id="PropertyType" name="PropertyType">
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label for="Bedrooms">Bedrooms</label>
                                <select class="custom-select form-control modal-select-2" multiple id="Bedroom" name="Bedroom">
                                    @php
                                        $Bedrooms=App\Models\Bedroom::get();
                                    @endphp
                                    @foreach($Bedrooms as $Bedroom)
                                        <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-contact-source">Contact Source</label>
                                <select class="form-control modal-select-2" multiple id="select-contact-source">
                                    @php
                                        $ContactSources=App\Models\ContactSource::orderBy('name','ASC')->get();
                                    @endphp
                                    @foreach($ContactSources as $CSource)
                                        <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <input type="text" id="first-name" autocomplete="off" class="form-control" placeholder="Name">
                                <label for="first-name">Name</label>
                            </div>
                        </div>
                        {{--<div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <input type="text" id="last-name" autocomplete="off" class="form-control" placeholder="Last Name">
                                <label for="last-name">Last Name</label>
                            </div>
                        </div>--}}
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <input type="text" id="contact-number" autocomplete="off" class="form-control country-code" placeholder="Contact Number">
                                <label for="contact-number">Contact Number</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <input type="text" id="email-address" autocomplete="off" class="form-control" placeholder="Email Address">
                                <label for="email-address">Email Address</label>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label for="deal-contact">Our Deals</label>
                                        <select class="custom-select form-control" id="deal-contact" name="deal_contact">
                                            <option value="">Select</option>
                                            <option value="1">Rental</option>
                                            <option value="2">Sales</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <input type="number" id="ref-number" autocomplete="off" class="form-control" placeholder="Ref Number">
                                        <label for="ref-number">Ref Number</label>
                                    </div>
                                </div>
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
                                        <input type="text" id="budget-from" autocomplete="off" class="form-control number-format" placeholder="From">
                                        <label for="budget-from">Budget (AED)</label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <input type="text" id="budget-to" autocomplete="off" class="form-control number-format" placeholder="To">
                                        <label for="budget-to">Budget (AED)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                        </div>
                    </div>
                    <table class="table truncate-table datatable1 table-striped order-column dataTable">
                        <thead>
                        <tr>
                            <th>Ref</th>
                            <th>FullName</th>
                            <th>Finance Status</th>
                            <th>Contact Categories</th>
                            <th>CM1</th>
                            <th>CM2</th>
                            <th>Budget (AED)</th>
                            <th>Last Activity</th>
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

    @if($Property)

    <!-- Modal Activity -->
    @include('admin/activity-modal')

    @endif

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
{{--    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

    <script>
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            fade: true,
            asNavFor: '.slider-nav'
        });
        $('.slider-nav').slick({
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            focusOnSelect: true
        });
    </script>
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
    $('.print-preview').click(function(){
        let element=$(this).data('print');
        $("#"+element+" .modal-body").printThis({
            debug: false,               // show the iframe for debugging
            importCSS: true,            // import parent page css
            // importStyle: true,         // import style tags
            printContainer: true,       // print outer container/$.selector
            loadCSS: "/css/bootstrap.css",                // path to additional css file - use an array [] for multiple
            pageTitle: "",              // add title to print page
            removeInline: false,        // remove inline styles from print elements
            removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
            printDelay: 333,            // variable print delay
            header: null,               // prefix to html
            footer: null,               // postfix to html
            base: false,                // preserve the BASE tag or accept a string for the URL
            formValues: true,           // preserve input/form values
            canvas: false,              // copy canvas content
            doctypeString: '',       // enter a different doctype for older markup
            removeScripts: false,       // remove script tags from print content
            copyTagClasses: false,      // copy classes from the html & body tag
            beforePrintEvent: null,     // function for printEvent in iframe
            beforePrint: null,          // function called before iframe is filled
            afterPrint: null            // function called before iframe is removed
        });
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

            if(val==2) {
                $('.contact-property-box').removeClass('d-none');
                $('.activity-not-box').addClass('d-none');
            }else if(val==3){
                $('.activity-not-box').addClass('d-none');
            }else{
                $('.contact-property-box').addClass('d-none').children('select').val('');
                $('.activity-not-box').removeClass('d-none');
            }
        });

        $('#AddPropertyNote').click(function () {
            let NoteSubject =$('#NoteSubject').val();
            let note =$('#Note').val();
            let date_at=$('#DateAt').val();
            let time_at=$('#TimeAt').val();
            let contact =$('#ActivityContact').val();
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

            if(NoteSubject==2 || NoteSubject==3 || NoteSubject==6) {
                if (date_at == '') {
                    $("#DateAt").parent().addClass('error');
                    error = 1
                } else {
                    $("#DateAt").parent().removeClass('error');
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
                if(contact == '' || contact == null){
                    $("#ActivityContact").parent().addClass('error');
                    error=1
                }else{
                    $("#ActivityContact").parent().removeClass('error');
                }
            }

            if(error==0) {
                $('#AddPropertyNote').html('Please wait...').attr('disabled','disabled');
                $.ajax({
                    url: "{{ route('property.note.add') }}",
                    type: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        property: "{{ ($Property) ? $Property->id : '' }}",
                        note_subject: NoteSubject,
                        contact: contact,
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
                url:"{{ route('property.note.edit') }}",
                type:"POST",
                data:{
                    _token:'{{csrf_token()}}',
                    id:id,
                    note:note
                },
                success:function (response) {
                    $('#div_notes_section #note'+id).html(note);
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
                    url: "{{route('contact.ajax.select')}}",
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
                placeholder: 'Contact Information',
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
                            <div class="image-box"><img src="${repo.picutre}" /></div>
                            <div class="w-100 ml-1">
                                <div><b>${repo.fullname}</b></div>
                                <div>${repo.main_number}</div>
                                <div>${repo.email}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelection (repo) {
            return repo.fullname || repo.text ;

        }
    </script>

    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            fixedColumns: {
                start: 2
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
                'url': '{{ route('contacts.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    data.FetchUser = 1;
                    data._token='{{csrf_token()}}';
                    data.client_manager=$('#select-client-manager').val().join(",");
                    data.client_manager_tow=$('#select-client-manager-2').val().join(",");
                    data.finance_status=$('#select-finance-status').val();
                    data.contact_categories=$('#select-contact-categories').val().join(",");
                    data.looking_for=$('#select-looking-for').val();
                    data.select_Color=$('#select-Color').val();
                    data.first_name=$('#first-name').val();
                    //data.last_name=$('#last-name').val();
                    data.email_address=$('#email-address').val();
                    data.contact_number=$('#contact-number').val();
                    data.emirate=$('#emirate').val();
                    data.master_project=$('#select-master-project').val().join(",");
                    data.community=$('#Community').val().join(",");
                    data.property_type=$('#PropertyType').val().join(",");
                    data.bedroom=$('#Bedroom').val().join(",");
                    data.budget_from=$('#budget-from').val();
                    data.budget_to=$('#budget-to').val();
                    data.contact_source=$('#select-contact-source').val().join(",");
                    data.select_developer=$('#select-developer').val().join(",");
                    data.deal_contact=$('#deal-contact').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 3,8 ]}],
            'columns': [
                {data: 'id'},
                {data: 'firstname'},
                {data: 'buy_type'},
                {data: 'contact_categories'},
                {data: 'client_manager'},
                {data: 'client_manager_tow'},
                {data: 'sale_budget'},
                {data: 'last_activity'},
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
                window.open('/admin/contact/view/'+id+'?p={{$Property->id}}&match=true');
                //window.location.href ='/admin/contact/view/'+id+'?p={{$Property->id}}';
            }

        });

        $('#type').change(function(){
            getPropertyType();
        });

        getPropertyType();
        function getPropertyType(){
            let type=$('#type').val();
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('#PropertyType').html(response);
                }
            });
        }
    </script>

    <script>
        $('#emirate').change(function () {
            let val=$(this).val();
            getMasterProject(val);
        });

        //$('#emirate').val(2).change();

        function getMasterProject(val){
            $.ajax({
                url:"{{ route('master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('#select-master-project').html(response);
                    $('#select-master-project').val([{!! $Property->master_project_id !!}]).trigger('change');
                }
            });
        }

        @if(request('c'))
            let contact='{{request('c')}}';

            $.ajax({
                url:"{{ route('get-contact-ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    contact:contact
                },
                success:function (response) {
                    $('.select-2-user')
                        .empty()
                        .append('<option selected value="'+response.id+'">'+response.firstname+' '+response.lastname+'</option>');
                    $('.select-2-user').select2('data', {
                        id: response.id,
                        label:response.firstname+' '+response.lastname
                    });
                }
            });
        @endif
    </script>

    <script>l
        $('#select-master-project').change(function () {
            let val=$(this).val();
            if(val.length<2){
                getCommunity(val);
                $('#Community').removeAttr('disabled');
            }else{
                getCommunity('');
                $('#Community').attr('disabled','disabled');
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
                    $('#Community').html(response);
                }
            });
        }
    </script>

    <script>
            $(".modal-select-2").select2({
                // the following code is used to disable x-scrollbar when click in select input and
                // take 100% width in responsive also
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

            $('.modal-match').click(function () {
                $('#emirate').val({{$Property->emirate_id}}).trigger('change');
                $('#select-contact-categories').val([{!! (($Property->listing_type_id==1) ? '"buyer"' : '"tenant"') !!}]).trigger('change');
                $('#Bedroom').val([{!! $Property->bedroom_id !!}]).trigger('change');
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
