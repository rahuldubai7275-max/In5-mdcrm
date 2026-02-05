        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/bootstrap.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/bootstrap-extended.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/colors.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/ui/prism.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.css')) }}">
        {{-- Vendor Styles --}}
        @yield('vendor-style')
        {{-- Theme Styles --}}
{{--        <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">--}}
        <link rel="stylesheet" href="{{ asset(mix('css/components.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/themes/dark-layout.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/themes/semi-dark-layout.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/sweetalert2.min.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/validation/form-validation.css')) }}">

        <link id="datepickerTheme" href="/css/plugins/datepiker/persian-datepicker.css" rel="stylesheet"/>
{{-- {!! Helper::applClasses() !!} --}}
@php
$configData = Helper::applClasses();
@endphp

{{-- Layout Styles works when don't use customizer --}}

{{-- @if($configData['theme'] == 'dark-layout')
        <link rel="stylesheet" href="{{ asset(mix('css/themes/dark-layout.css')) }}">
@endif
@if($configData['theme'] == 'semi-dark-layout')
        <link rel="stylesheet" href="{{ asset(mix('css/themes/semi-dark-layout.css')) }}">
@endif --}}
{{-- Page Styles --}}
@if($configData['mainLayoutType'] === 'horizontal')
        <link rel="stylesheet" href="{{ asset(mix('css/core/menu/menu-types/horizontal-menu.css')) }}">
@endif
        <link rel="stylesheet" href="{{ asset(mix('css/core/menu/menu-types/vertical-menu.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/core/colors/palette-gradient.css')) }}">
        <link rel="stylesheet" href="{{ asset(mix('css/plugins/extensions/toastr.css')) }}">
{{-- Page Styles --}}
        @yield('page-style')
{{-- Laravel Style --}}
        <link rel="stylesheet" href="{{ asset(mix('css/custom-laravel.css')) }}{{'?v='.date('YmdHis')}}">
{{-- Custom RTL Styles --}}

        <style>
            .btn-120{
                width: 120px;
            }
            .btn-150{
                width: 150px;
            }
            .select2-results__option .wrap:before{
                font-family:fontAwesome;
                color:#7267ef;
                content:"\f096";
                width:25px;
                height:25px;
                padding-right: 10px;

            }
            .select2-results__option[aria-selected=true] .wrap:before{
                content:"\f14a";
            }
            /* not required css */

            .select2-multiple, .select2-multiple2
            {
                width: 50%
            }

            .select2-container--default .select2-results__option[aria-selected=true] {
                background-color: unset !important;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected]{
                color: #626262 !important;
            }

            .ui-loader {
                display: none;
            }
            @media (max-width: 991.98px){
                .btn-120{
                    width: unset !important;
                }
                .btn-150{
                    width: unset !important;
                }
                .heading-elements .list-inline {
                     display: unset !important;
                }
                .heading-elements {
                    text-align: unset !important;
                }
                .card .card-header .heading-elements, .card .card-header .heading-elements-toggle{
                    position: unset !important;
                }
                div.dataTables_wrapper div.dataTables_length{
                    text-align: unset !important;
                }
                .border-left-mobile-unset{
                    border-left: unset !important;
                }

                .pace-active{
                    display: none !important;
                }

                .content-header{
                    display: none !important;
                }

                .font-mobile-small {
                    font-size: 0.7rem !important;
                }

                .add-to-contact-btn {
                    margin-left: auto !important;
                    margin-right: auto !important;
                    display: block;
                }
            }

            .blink_me {
                animation: blinker 1s linear infinite !important;
            }

            @keyframes blinker {
                50% { opacity: 0; }
            }
            .select2-results__message.no-data-found{
                color: red;
            }

            {{--.main-menu {
                height: 96% !important;
                border-top: 3px solid;
                border-bottom: 3px solid;
                border-color: #f8da6e;
                border-radius: 10px;
                margin: 15px 11px 10px 10px;
            }

            .card {
                border-top: 3px solid;
                border-color: #f8da6e;
            }

            .left-right-card-border .card {
                border-top: 0px !important;
                border-left: 3px solid;
                border-right: 3px solid;
                border-color: #f8da6e;
            }--}}
/*start theme css*/
            @php
            $adminAuth=Auth::guard('admin')->user();
            $Theme='';
            $MenuColor='';
            if($adminAuth) {
                $Theme = \App\Models\ThemeSetting::where('admin_id', $adminAuth->id)->first();
                if($Theme && $Theme->menu_color)
                    $MenuColor = explode("|", $Theme->menu_color);
            }
            @endphp
            @if($Theme)
            .customizer .customizer-toggle{
                background: <?=$MenuColor[2];?> !important;
            }
            .main-menu .navbar-header .navbar-brand .brand-text,body.vertical-layout.vertical-menu-modern .toggle-icon{
                color:<?=$MenuColor[2];?> !important;
            }
            .navigation-main li.active > a{
                <?=$MenuColor[1];?>
            }
            @endif
            /*end theme css*/

        </style>

@if($configData['direction'] === 'rtl')
        <link rel="stylesheet" href="{{ asset(mix('css/custom-rtl.css')) }}">
@endif
