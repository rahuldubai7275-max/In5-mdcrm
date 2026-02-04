
@extends('layouts.app')

@section('title', 'New Project')

@php
    $admin = Auth::guard('admin')->user();

    if($OffPlanProject){
        $PropertyType=App\Models\PropertyType::where('id',$OffPlanProject->property_type_id)->first();

        if(request('a')){
            $agent_id=\Helper::idDecode(request('a'));
        }else{
            $agent_id=$admin->id;
        }
        $agent=App\Models\Admin::where('id',$agent_id)->first();
        $company=App\Models\Company::where('id',$agent->company_id)->first();
        $sm_imag='';
        if($OffPlanProject->pictures)
            $sm_imag='https://mdcrms.com/storage/'.current(explode(',', $OffPlanProject->pictures));
    }
@endphp
@section('og:title', $company->name)
@section('og:description', $OffPlanProject->project_name)
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

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>

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

        #map-canvas{
            width: 700px;
            height: 500px;
            margin: 0 auto;
        }
    </style>

    <div class="container pb-4">
        <div class="row" id="brochure">
            <div class="col-12">
                <div class="row">
                    <div class="clearfix col-12 mb-1">
                        <div class="float-sm-right text-sm-right text-center" id="preview-price">
                            <img src="/laravel/storage/app/public/images/{{$company->logo}}" style="width: 120px;">
                        </div>
                        <div class="float-left d-flex">
                            <div>
                                <p style="margin:5px;"><b>{{$OffPlanProject->master_project->name}}, {{$OffPlanProject->emirate->name}}</b></p>
                                <p style="margin:5px;"><b>{{$OffPlanProject->project_name }}</b></p>
                                <p style="margin:5px;"><b>{{PropertyType[$OffPlanProject->type] .' / '.$PropertyType->name }}</b></p>
                                <p style="margin:5px;"><b>{{$OffPlanProject->developer->name}}</b></p>

                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="wrap-modal-slider">
                                    <div class="slider slider-for">
                                        @if($OffPlanProject)
                                            @if($OffPlanProject->pictures)
                                                @foreach(explode(',', $OffPlanProject->pictures) as $picture)
                                                    <div style="width:100%"><img style="width:100%" class="img-fluid" src="https://mdcrms.com/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                @endforeach
                                            @endif
                                        @endif
                                    </div>
                                    <div class="slider slider-nav">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="card mb-2">
                            <div class="card-header">
                                <h4 class="card-title">Description</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <p class="mb-0">{!!($OffPlanProject) ? nl2br($OffPlanProject->description) : ''!!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pb-2">
                            {!!($OffPlanProject->video_link) ? '<a target="_blank" class="btn btn-twitter waves-effect waves-light mr-1 px-1 w-100" href="'.$OffPlanProject->video_link.'"  style="color: #ffffff; width: 150px;height: 50px;align-items: center;display: flex;justify-content: center;font-weight: 900;font-size: 18px;"><b>Video</b></a>' : ''!!}
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Details</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-7 pr-0">
                                                    <p><b>Developer:</b></p>
                                                    {!! ($OffPlanProject->date_of_launch) ? '<p><b>Date Of Launch: </b></p>' : ''  !!}
                                                    {!! ($OffPlanProject->phpp) ? '<p><b>PHPP:</b></p>' : ''  !!}
                                                    {!! ($OffPlanProject->starting_price) ? '<p><b>Starting Price:</b></p>' : ''  !!}
                                                </div>
                                                <div class="col-5 pl-0">
                                                    <p><span class="badge badge-info text-dark" style="width: 100%;background-color: #97ebf5 !important;">{{$OffPlanProject->developer->name}}</span></p>
                                                    {!! ($OffPlanProject->date_of_launch) ? '<p><span class="badge badge-danger text-dark"  style="width: 100%;background-color: #f58c8c !important;">'.date('d-m-Y',strtotime($OffPlanProject->date_of_launch)).'</span></p>' : ''  !!}
                                                    {!! ($OffPlanProject->phpp) ? '<p><span class="badge badge-warning text-dark" style="width: 100%;background-color: #f5be89 !important;">'.$OffPlanProject->phpp.'</span></p>' : ''  !!}
                                                    {!! ($OffPlanProject->starting_price) ? '<p><span class="badge badge-primary text-dark" style="width: 100%;background-color: #a8a0f7 !important;">'.number_format($OffPlanProject->starting_price).'</span></p>' : ''  !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="row">
                                                <div class="col-7 pr-0">
                                                    {!! ($OffPlanProject->payment_plan) ? '<p><b>Payment Plan:</b></p>' : ''  !!}
                                                    {!! ($OffPlanProject->property_type_id) ? '<p><b>Type:</b></p>' : ''  !!}
                                                    <p><b>Bedroom:</b></p>
                                                    <p><b>Size:</b></p>
                                                </div>
                                                <div class="col-5 pl-0">
                                                    {!! ($OffPlanProject->payment_plan) ? '<p><span class="badge badge-success text-dark" style="width: 100%;background-color: #87eab3 !important;">'.$OffPlanProject->payment_plan.'</span></p>' : ''  !!}
                                                    {!! ($OffPlanProject->property_type_id) ? '<p><span class="badge badge-yellow text-dark" style="width: 100%;background-color: #fdfda4 !important;">'.$PropertyType->name.'</span></p>' : ''  !!}
                                                    <p><span class="badge badge-light-primary" style="width: 100%;color: #1e1e1e !important;">{{(($OffPlanProject->bedroom_from=='0') ? 'Studio' : $OffPlanProject->bedroom_from).(($OffPlanProject->bedroom_to=='0') ? ' To Studio' : ' To '.$OffPlanProject->bedroom_to)}}</span></p>
                                                    <p><span class="badge badge-secondary text-dark" style="width: 100%;background-color: #c8dff7 !important;">{{number_format($OffPlanProject->size_from).' To '.number_format($OffPlanProject->size_to)}} Sqft.</span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($OffPlanProject->map)
                            <div class="card mb-2">
                                <div class="card-header">
                                    <h4 class="card-title">Location</h4>
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        {!! ($OffPlanProject->map) ? str_replace(['width:500px;','width="500"'],['','style="max-width:100%;width:100%"'],$OffPlanProject->map) : ''  !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="pb-2">
                            {!!($OffPlanProject->file) ? '<a target="_blank" class="btn btn-twitter waves-effect waves-light mr-1 px-1 w-100" href="https://mdcrms.com/laravel/storage/app/public/images/'.$OffPlanProject->file.'"  style="color: #ffffff; width: 150px;height: 50px;align-items: center;display: flex;justify-content: center;font-weight: 900;font-size: 18px;"><b>Brochure</b></a>' : ''!!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="footer w-100">
                    <div class="p-2 mb-1" style="background-image:url('/images/bg-brochure-sale.jpg');background-size: cover;">
                        <div class="border border-color-gold color-gold p-1 card-icon-box rounded">
                            <div class="row">
                                <div class="col-sm-5 order-3 order-sm-1">
                                    <div class="mb-10  d-none d-sm-flex"><span class="mr-1"><i class="fa fa-map-marker"></i></span> <span>{{$company->address}}</span></div>
                                    @if($company->office_tel) <div class="mb-10 d-none d-sm-flex"><span class="mr-1"><i class="fa fa-mobile"></i></span> <span>{{$company->office_tel}}</span></div> @endif
                                    @if($company->rera_orn) <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>ORN: {{$company->rera_orn}}</span></div> @endif
                                    <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>Developer: {{$OffPlanProject->developer->name}}</span></div>
                                </div>
                                <div class="col-sm-2 d-flex align-items-sm-center order-1 order-sm-2">
                                    <div class="avatar mr-1 avatar-xl mx-auto" style="width: 93px;height: 93px;">
                                        <img src="{{ ($agent->pic_name) ? '/laravel/storage/app/public/images/'.$agent->pic_name : '/images/Defult2.jpg'}}" style="width: 93px;height: 93px;border: 3px solid #EBD2AE !important;">
                                    </div>
                                </div>
                                <div class="clearfix col-sm-5 order-2 order-sm-3">
                                    <div class="float-sm-right">
                                        <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-user"></i></span> <span>{{$agent->firstname.' '.$agent->lastname}}</span></div>
                                        <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-envelope"></i></span> <span>{{$agent->email}}</span></div>
                                        <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-mobile"></i></span> <span>{{($agent->main_number) ? $agent->main_number : $agent->mobile_number}}</span></div>
                                        @if($agent->rera_brn) <div class="mb-10 d-flex"><span class="mr-1"><i class="fa fa-registered"></i></span> <span>BRN: {{$agent->rera_brn}}</span></div> @endif
                                    </div>
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

    <div class="modal fade text-left" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-text-bold-600" id="cal-modal">Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-2">

                </div>
                <!--<div class="modal-footer">
                </div>-->
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-text-bold-600" id="cal-modal">Map</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-2">
                    {{--<div id="map-canvas"></div>--}}
                    {!! ($OffPlanProject->map) ? $OffPlanProject->map : ''  !!}
                <!--<div class="modal-footer"></div>-->
                </div>
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
            fade: true
            //asNavFor: '.slider-nav'
        });
        {{--$('.slider-nav').slick({
            infinite: true,
            slidesToShow: 5,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            focusOnSelect: true
        });--}}
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
        // var wallet_address = $("#copy_wallet_address_input");
        var btnCopy = $("#btn-copy");

        var text = window.location.href+"{{($admin && $admin->type!=2) ?  (request('a')) ? '' : '?a='.\Helper::idCode($admin->id) : ''}}";

        $('#share_in_wp').attr('href','whatsapp://send?text='+text);
        $('#share_in_tg').attr('href','tg://msg_url?url='+text);

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
@endsection
@section('page-script')
    <!-- Page js files -->


@endsection
