
@extends('layouts.app')

@section('title', 'Property')

@php
    $admin = Auth::guard('admin')->user();

    if($Property){


        $mc=0;
        $expected_price=0;
        if($Property->expected_price){
            $mc= $Property->bua==0 ? 0 : ($Property->expected_price/$Property->bua) ;
            $expected_price=$Property->expected_price;
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

        $PropertyType=App\Models\PropertyType::where('id',$Property->property_type_id)->first();
        $ClientManager=App\Models\Admin::where('id',$Property->client_manager_id)->first();
        $Contact=App\Models\Contact::where('id',$Property->contact_id)->first();
        $MasterProject=App\Models\MasterProject::where('id',$Property->master_project_id)->first();
        $Community=App\Models\Community::find($Property->community_id);
        $ClusterStreet=App\Models\ClusterStreet::find($Property->cluster_street_id);
        $Project=App\Models\Community::where('id',$Property->community_id)->first();
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

        //$agent=App\Models\Admin::where('id',$Property->admin_id)->first();
        if(request('a')){
            $agent_id=\Helper::idDecode(request('a'));
        }else{
            $agent_id=$Property->client_manager_id;//(($Property->client_manager2_id) ? $Property->client_manager2_id: $Property->client_manager_id);
        }
        $agent=App\Models\Admin::where('id',$agent_id)->first();
        $company=App\Models\Company::where('id',$agent->company_id)->first();
        $sm_imag='';
        if($Property->pictures)
            $sm_imag=request()->getSchemeAndHttpHost() .'/storage/'.current(explode(',', $Property->pictures));
    }
@endphp
@section('og:title', $company->name)
@section('og:description', $Property->title)
@section('og:image', $sm_imag)

@section('vendor-style')
    <!-- vendor css files -->
@endsection

@section('content')
    <!-- Form wizard with step validation section start -->

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600">
    <link rel="stylesheet" href="/vendors/css/vendors.min.css">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/bootstrap-extended.css">
    <link rel="stylesheet" href="/css/colors.css">
    <link rel="stylesheet" href="/vendors/css/forms/select/select2.min.css">
    <link rel="stylesheet" href="/vendors/css/ui/prism.min.css">
    <link rel="stylesheet" href="/vendors/css/extensions/toastr.css">


    <link rel="stylesheet" href="/vendors/css/tables/datatable/datatables.min.css">

    <link rel="stylesheet" href="/vendors/css/pickers/pickadate/pickadate.css">
    <link rel="stylesheet" href="/css/components.css">
    <link rel="stylesheet" href="/css/themes/dark-layout.css">
    <link rel="stylesheet" href="/css/themes/semi-dark-layout.css">
    <link rel="stylesheet" href="/vendors/css/animate/animate.css">
    <link rel="stylesheet" href="/vendors/css/extensions/sweetalert2.min.css">
    <link rel="stylesheet" href="/css/plugins/forms/validation/form-validation.css">

    <link rel="stylesheet" href="/css/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" href="/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" href="/css/plugins/extensions/toastr.css">


    <link rel="stylesheet" href="/css/custom-laravel.css">

    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css" />

    <style>
        .slick-next {
            right: 30px;
        }

        .slick-prev {
            left: 15px;
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

        .border-color-gold{
            border-color: #EBD2AE !important;
        }

        .border-color-dark{
            border-color: #000 !important;
        }

        .color-gold{
            color: #EBD2AE !important;
        }

        .color-dark{
            color: #000 !important;
        }

        .card-icon-box i{
            border: 1px solid;
            border-radius: 50%;
            padding: 5px;
            width: 25px;
            height: 25px;
            text-align: center;
        }

        .mb-10{
            margin-bottom: 10px !important;
        }

        .slick-next:before, .slick-prev:before {
            font-size: 35px !important;
            /*opacity: .8 !important;*/
        }

        .wrap-modal-print{
            display: none;
        }

        @media print {
            body{
                background:#fff !important;
                background-color:#fff !important;
            }
            .bg-gray{
                background:#fff !important;
            }
            .wrap-modal-print{
                display: block;
            }
            .wrap-modal-slider{
                display: none;
            }
            .slider-for .slick-slide{
                margin:auto;
            }

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
                -webkit-print-color-adjust: exact;
            }

            .text-gray{
                color:#ACACAC;
                -webkit-print-color-adjust: exact;
            }

            .border-color-gold.border , .border-color-gold.border-left , .border-color-gold.border-bottom{
                /*border: 3px;*/
                border-color: #EBD2AE !important;

                -webkit-print-color-adjust: exact;
            }

            .border-color-dark{
                border-color: #000 !important;
                -webkit-print-color-adjust: exact;
            }

            .color-gold{
                color: #EBD2AE !important;
                -webkit-print-color-adjust: exact;
            }

            .color-dark{
                color: #000 !important;
                -webkit-print-color-adjust: exact;
            }

            .card-icon-box i{
                border: 1px solid;
                border-radius: 50%;
                padding: 5px;
                width: 25px;
                height: 25px;
                text-align: center;
            }

            .mb-10{
                margin-bottom: 10px !important;
            }

            #brochure{
                padding-bottom: 0px;
                position: relative;
                height: 100%;
                margin: 0 !important;
            }

            #brochure .footer{
                position: absolute;
                bottom: 0;
            }
        }
    </style>

    <div class="container">
        <div class="row" id="brochure">
            <div class="col-12">
                <div class="row">
                    <div class="clearfix col-12 mb-1">
                        <div class="float-sm-right text-sm-right text-center" id="preview-price">
                            <img class="{{($Property && $Property->land_department_qr) ? 'float-right' : ''}}" src="/laravel/storage/app/public/images/{{$company->logo}}" style="width: 120px;">
                            {!! ( ($Property && $Property->land_department_qr) ? '<img class="float-left d-block d-sm-none  mr-2" style="width:100px;height:100px" src="/laravel/storage/app/public/images/'.$Property->land_department_qr.'" alt="">' : '') !!}

                        </div>
                        <div class="float-left d-flex">
                            {!! ( ($Property && $Property->land_department_qr) ? '<img class="d-none d-sm-block mr-2" style="width:100px;height:100px" src="/laravel/storage/app/public/images/'.$Property->land_department_qr.'" alt="">' : '') !!}
                            <div>
                                <p style="margin:5px;margin-top:0;"><b>FOR {{(($Property->listing_type_id==1) ? 'SALE' : 'RENT')}} </b> Ref. No. {{$company->sample}}-{{(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num}}</p>
                                <p style="margin:5px;"><b>{{(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' | '.$Community->name : '')}}</b></p>
                                <p style="margin:5px;"><b>AED {{number_format($expected_price)}}</b></p>
                                {!! ($Property->listing_type_id==1) ? '<p style="margin:5px;"><b>AED '.number_format( $mc ).' / Sq Ft</b></p>' : '' !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="wrap-modal-slider">
                                    <div class="slider slider-for">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    <div style="width:100%"><img style="width:100%" class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                    <div class="slider slider-nav">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    <div><img class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="wrap-modal-print">
                                    <div class="row">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    <div class="col-sm-6"><img class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                    @if($loop->index=='1')
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                    <div class="row mt-1">
                                        @if($Property)
                                            @if($Property->pictures)
                                                @foreach(explode(',', $Property->pictures) as $picture)
                                                    @if($loop->index<='1')
                                                        @continue
                                                    @endif
                                                    <div class="col-sm-3 px-1"><img class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                    @if($loop->index=='5')
                                                        @break
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 mt-1">
                        <div class="row bg-gray border border-color-gold rounded py-1 m-0 justify-content-center border-color">
                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center">
                                <p class="m-0"><b>Category</b></p>
                                <p class="m-0">{{($PropertyType) ? $PropertyType->name : ''}}</p>
                            </div>

                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center border-left border-color-gold">
                                <p class="m-0"><b>Type</b></p>
                                <p class="m-0">{{($VillaType) ? $VillaType->name : 'N/A'}}</p>
                            </div>

                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center border-left border-color-gold">
                                <p class="m-0"><b><i class="fa fa-bed"></i><!--No. of Beds--></b></p>
                                <p class="m-0">{{($Bedroom) ? $Bedroom->name : 'N/A'}}</p>
                            </div>

                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center border-left border-color-gold">
                                <p class="m-0"><b>BUA</b></p>
                                <p class="m-0">{{number_format($Property->bua)}} Sq Ft</p>
                            </div>

                            {{--@if($Property->property_type_id!=3)--}}
                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center border-left border-color-gold">
                                <p class="m-0"><b>Plot</b></p>
                                <p class="m-0">{{($Property->plot_sqft) ? number_format($Property->plot_sqft).' Sq Ft' : 'N/A'}}</p>
                            </div>
                            {{--@endif--}}

                            @if($Property->property_type_id!=19 &&$Property->property_type_id!=29)
                            <div class="col-6 col-sm-2 mb-2 mb-sm-0 text-center border-left border-color-gold">
                                <p class="m-0"><b>Status</b></p>
                                <p class="m-0">{{($Property->status2 && ($Property->property_type_id!=19 && $Property->property_type_id!=29)) ? Status2[$Property->status2] : 'N/A'}}</p>
                            </div>
                            @endif

                            @if($Property->number_cheques)
                            <!--<div class="col-sm-3 text-center border-left">
                                <p class="m-0"><b>No. of Cheques</b></p>
                                <p class="m-0">{{$Property->number_cheques}}</p>
                            </div>-->
                            @endif


                        </div>
                    </div>
                    <div class="col-sm-12" style="">
                        <h5 class="mt-1 mb-0 pb-1 border-bottom  border-color-gold">Description</h5>
                        <div id="preview-description" class="pt-2">
                            <p class="mb-0">{!!($Property) ? nl2br($Property->description) : ''!!}</p>

                            <ul class="order_list list-inline-item px-2 pt-1">
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
                </div>
            </div>
            <div class="footer w-100">
                @php
                    $brochure_bg_sale_setting=\App\Models\Setting::where('company_id',$company->id)->where('title','brochure_sale_bg')->first();
                    $brochure_bg_rent_setting=\App\Models\Setting::where('company_id',$company->id)->where('title','brochure_rent_bg')->first();
                @endphp
                <div class="p-2 mb-1" style="background-image:url('/images/{{(($Property->listing_type_id==1) ? $brochure_bg_sale_setting->value : $brochure_bg_rent_setting->value)}}');background-size: cover;">
                    <div class="border border-color-{{(($Property->listing_type_id==1) ? 'gold' : 'gold')}} color-{{(($Property->listing_type_id==1) ? 'gold' : 'dark')}} p-1 card-icon-box rounded">
                        <div class="row">
                            <div class="col-sm-5 order-3 order-sm-1">
                                <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-map-marker"></i></span> <span>{{$company->address}}</span></div>
                                @if($company->office_tel) <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-mobile"></i></span> <span>{{$company->office_tel}}</span></div>@endif
                                @if($company->rera_orn) <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>ORN: {{$company->rera_orn}}</span></div>@endif
                                <div class="mb-0 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>DLD Permit no: {{($Property) ? $Property->rera_permit : ''}}</span></div>
                            </div>
                            <div class="col-sm-2 d-flex align-items-sm-center order-1 order-sm-2">
                                <div class="avatar mr-1 avatar-xl mx-auto" style="width: 93px;height: 93px;">
                                    <img src="{{ ($agent->pic_name) ? '/laravel/storage/app/public/images/'.$agent->pic_name : '/images/Defult2.jpg'}}" style="width: 93px;height: 93px;border: 3px solid #{{(($Property->listing_type_id==1) ? 'EBD2AE' : 'EBD2AE')}} !important;">
                                </div>
                            </div>
                            <div class="clearfix col-sm-5 order-2 order-sm-3">
                                <div class="float-sm-right">
                                    <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-user"></i></span> <span>{{$agent->firstname.' '.$agent->lastname}}</span></div>
                                    <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-envelope"></i></span> <span>{{$agent->email}}</span></div>
                                    <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-mobile"></i></span> <span>{{($agent->main_number) ? $agent->main_number : $agent->mobile_number}}</span></div>
                                    <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>BRN: {{$agent->rera_brn}}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($admin)
            <div class="d-flex mobile-footer bg-gray pt-1 d-block d-lg-none">
                <a id="share_btn" href="#shareModal" data-toggle="modal" class="btn bg-gradient-info waves-effect waves-light px-1 mx-auto">Share</a>

                <a href="javascript:close_window();" class="btn bg-gradient-danger waves-effect waves-light close-btn px-1 mx-auto"><i class="feather icon-x"></i></a>

                <a href="javascript:void(0);" class="btn bg-gradient-info waves-effect waves-light float-right print_save px-1 mx-auto">Save</a>
            </div>

            <div class="row d-none d-sm-flex">
                <div class="col-3">
                    <a style="width:150px" href="#shareModal" data-toggle="modal" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light float-left">Share</a>
                    {{--<a  id="btn-copy" href="javascript:void(0);" class="share-link bg-gradient-info"><i class="fa fa-copy"></i></a>--}}
                </div>
                <div class="col-6 d-flex justify-content-center">
                     <a style="width:150px" href="javascript:close_window();" class="btn bg-gradient-danger py-1 px-2 waves-effect waves-light d-block d-lg-none">close</a>
                </div>
                <div class="col-3">
                    <a style="width:150px" href="javascript:void(0);" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light float-right print_save">Save And Print</a>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade text-left" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-text-bold-600" id="cal-modal">Share</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-2 font-medium-4">
                    <input type="text" id="link-input" class="d-none">
                    <a id="share_in_wp" class="share-link" href="" style="background: #25d366;"><i class="fa fa-whatsapp"></i></a>
                    <a id="share_in_tg" class="share-link" href="" style="background: #0088CC;"><i class="fa fa-telegram"></i></a>
                    <a  id="btn-copy" href="javascript:void(0);" class="share-link bg-gradient-info"><i class="fa fa-copy"></i></a>
                </div>
                <!--<div class="modal-footer">
                </div>-->
            </div>
        </div>
    </div>

    <script src="/vendors/js/vendors.min.js"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/magnific-popup.min.js"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/printThis.js"></script>
    <!--<script src="/js/scripts/jquery.printarea.js"></script>-->
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>

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
        $('.print_save').click(function(){
            // let element=$(this).data('print');
            $("#brochure").printThis({
                // debug: false,               // show the iframe for debugging
                importCSS: true,            // import parent page css
                importStyle: true,         // import style tags
                printContainer: true,       // print outer container/$.selector
                loadCSS: "/css/bootstrap.css",                // path to additional css file - use an array [] for multiple
                // pageTitle: "",              // add title to print page
                removeInline: false,        // remove inline styles from print elements
                // removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
                // printDelay: 333,            // variable print delay
                // header: null,               // prefix to html
                // footer: null,               // postfix to html
                // base: false,                // preserve the BASE tag or accept a string for the URL
                // formValues: true,           // preserve input/form values
                // canvas: false,              // copy canvas content
                // doctypeString: '',       // enter a different doctype for older markup
                // removeScripts: false,       // remove script tags from print content
                // copyTagClasses: false,      // copy classes from the html & body tag
                // beforePrintEvent: null,     // function for printEvent in iframe
                // beforePrint: null,          // function called before iframe is filled
                // afterPrint: null            // function called before iframe is removed
            });

        });
    </script>

    <script>

        var text = window.location.href+"{{($admin && $admin->type!=2) ?  (request('a')) ? '' : '?a='.\Helper::idCode($admin->id) : ''}}";

        $('#share_in_wp').attr('href','whatsapp://send?text='+text);
        $('#share_in_tg').attr('href','tg://msg_url?url='+text);
    </script>
    <script>
        // var wallet_address = $("#copy_wallet_address_input");
        var btnCopy = $("#btn-copy");

        // copy text on click
        btnCopy.on("click", function () {
            //var dummy = document.createElement('input'),
            var dummy = document.getElementById('link-input'),
                txt = window.location.href+"{{($admin && $admin->type!=2) ?  (request('a')) ? '' : '?a='.\Helper::idCode($admin->id) : ''}}";

            $('#link-input').removeClass('d-none');
            //document.body.appendChild(dummy);
            dummy.value = txt;
            dummy.select();
            document.execCommand('copy');
            //document.body.removeChild(dummy);
            $('#link-input').addClass('d-none');

            toastr.success('Link Copied');
        });

        function close_window() {
            //if (confirm("Close Window?")) {
            close();
            //}
        }
    </script>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
    <elevenlabs-convai agent-id="agent_7301k4sbbz9hevqvke70r2egaay2"></elevenlabs-convai>
    <script src="https://unpkg.com/@elevenlabs/convai-widget-embed" async type="text/javascript"></script>
@endsection
@section('page-script')
    <!-- Page js files -->


@endsection
