
@extends('layouts.app')

@section('title', 'Survey')

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

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-8 col-lg-8">
                <div class="card">
                    <div class="card-header justify-content-center">
                        <img src="/images/{{LOGO}}" style="width: 120px;">
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <h5 class="card-title my-2">Deal Tracking</h5>
                            <ul class="activity-timeline timeline-left list-unstyled">
                                @foreach($dealTracking as $row)
                                <li class="mb-0">
                                    <div class="d-flex">
                                        @if($loop->last)
                                            <div style="width: 2px;border: 1px solid #fff;margin-left: -42px;"></div>
                                        @else
                                            <div style="width: 2px;{!! ($row->status==1) ? 'border: 1px solid #00ca77' : '' !!};margin-left: -42px;"></div>
                                        @endif
                                        <div class="ml-4 mb-4">
                                            <div class="timeline-icon bg-{{($row->status==1) ? 'success' : 'secondary'}}">
                                                <i class="feather icon-check font-medium-1"></i>
                                            </div>
                                            <div class="timeline-info">
                                                <p class="font-weight-bold">{{$row->title}}</p>
                                            </div>
                                            <small class="">{{($row->done_date) ? date('d/m/Y',strtotime($row->done_date)) : ''}}</small>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div class="mt-3">
                                <p class="text-center mb-2"><b>Congratulations your {{($deal->type==1) ? 'process':'transfer is'}} completed.</b></p>

                                <div class="d-flex justify-content-center font-medium-3">
                                    @php
                                        $company=\App\Models\Company::where('id',1)->first();
                                    @endphp
                                    {!! ($company)  ?  '<a href="'.$company->facebook.'" class="mx-1"><i class="fa fa-facebook"></i></a>'  :  '' !!}
                                    {!! ($company)  ?  '<a href="'.$company->instagram.'" class="mx-1"><i class="fa fa-instagram"></i></a>'  :  '' !!}
{{--                                    {!! ($company)  ?  '<a href="'.$company->tiktok.'" class="mx-1"><i class="fa fa-instagram"></i></a>'  :  '' !!}--}}
                                    {!! ($company)  ?  '<a href="'.$company->linkedin.'" class="mx-1"><i class="fa fa-linkedin"></i></a>'  :  '' !!}
                                    {!! ($company)  ?  '<a href="'.$company->youtube.'" class="mx-1"><i class="fa fa-youtube"></i></a>'  :  '' !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/vendors/js/vendors.min.js"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/magnific-popup.min.js"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script>
        $('.rate-star .star').click(function () {
            let parent=$(this).data('parent');
            $(parent+' .rate-star .star').addClass('text-secondary').removeClass('text-warning');
            let rate=$(this).data('rate');
            for(let i=1;i<=rate;i++){
                $(parent+' .rate-star .star-'+i).addClass('text-warning').removeClass('text-secondary');
            }

            $(parent+' input:hidden').val(rate);
        });
    </script>
@endsection
