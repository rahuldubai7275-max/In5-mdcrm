
@extends('layouts.app')

@section('title', 'Install App')

@section('vendor-style')
    <!-- vendor css files -->
@endsection

@section('content')
    <!-- Form wizard with step validation section start -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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

    <style>
        .download {
            width: 200px;
            height: 75px;
            background: black;
            float: left;
            border-radius: 5px;
            position: relative;
            color: #fff !important;
            cursor: pointer;
            border: 1px solid #fff;
        }

        .download > .fa {
            color: #fff;
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
        }

        .df,
        .dfn {
            position: absolute;
            left: 70px;
        }

        .df {
            /*top: 20px;*/
            font-size: 1.5em;
        }

        .dfn {
            top: 33px;
            font-size: 1.08em;
        }

        .download:hover {
            -webkit-filter: invert(100%);
            filter: invert(100%);
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-8 col-lg-8">
                <div class="card">
                    <div class="card w-100 mb-0">
                        <div class="card-header justify-content-center pb-0">
                            <div class="card-title">
                                {{--<img src="/images/{{LOGO}}" style="width: 178px;">--}}
                                {{--<h2 class="">{{env('APP_NAME')}}</h2>--}}
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-6 d-flex justify-content-center">
                                        <a href="javascript:void(0);" class="download android d-flex align-items-center" id="install">
                                            <i class="fa fa fa-android fa-3x"></i>
                                            <span class="df">Download</span>
                                            {{--<span class="dfn">Google Play</span>--}}
                                        </a>
                                    </div>
                                    <div class="col-sm-6 d-flex justify-content-center">
                                        <a href="#iosModal" data-toggle="modal" class="download apple d-flex align-items-center" id="install">
                                            <i class="fa fa fa-apple fa-3x"></i>
                                            <span class="df">Download</span>
                                            {{--<span class="dfn">App Store</span>--}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade text-left" id="iosModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel20">Install App</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Install this application on your home screen for quick and easy access when you’re on the go.
                </div>
                <div class="modal-footer justify-content-start">
                    <p>Just tap <svg style="width: 20px" class="pwa-install-prompt__guide__icon" viewBox="0 0 128 128" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title>Share</title><path fill="#007AFF" d="M48.883,22.992L61.146,10.677L61.146,78.282C61.146,80.005 62.285,81.149 64,81.149C65.715,81.149 66.854,80.005 66.854,78.282L66.854,10.677L79.117,22.992C79.693,23.57 80.256,23.853 81.114,23.853C81.971,23.853 82.534,23.57 83.11,22.992C84.25,21.848 84.25,20.125 83.11,18.981L65.997,1.794C65.715,1.511 65.421,1.215 65.139,1.215C64.563,0.932 63.718,0.932 62.861,1.215C62.579,1.498 62.285,1.498 62.003,1.794L44.89,18.981C43.75,20.125 43.75,21.848 44.89,22.992C46.029,24.149 47.744,24.149 48.883,22.992ZM103.936,35.32L81.114,35.32L81.114,41.053L103.936,41.053L103.936,121.27L24.064,121.27L24.064,41.053L46.886,41.053L46.886,35.32L24.064,35.32C20.928,35.32 18.355,37.904 18.355,41.053L18.355,121.27C18.355,124.419 20.928,127.003 24.064,127.003L103.936,127.003C107.072,127.003 109.645,124.419 109.645,121.27L109.645,41.053C109.645,37.891 107.072,35.32 103.936,35.32Z"></path></svg> then “Add to Home Screen”</p>
                </div>
            </div>
        </div>
    </div>

    <script src="/vendors/js/vendors.min.js"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/pwa.js?v={{strtotime(date('Y-m-d H:i:s'))}}"></script>

@endsection

