
@extends('layouts.app')

@section('title', 'Survey')

@section('vendor-style')
    <!-- vendor css files -->
@endsection
@php
    $company=App\Models\Company::find($survey->company_id);
@endphp
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
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        <ul class="m-0 p-0" style="list-style-type: none;">
                            <li>{!!  Session::get('success')  !!}</li>
                        </ul>
                    </div>
                @endif
                <div class="card">
                    <div class="card w-100 mb-0">
                        <div class="card-header justify-content-center pb-0">
                            <div class="card-title">
                                <img src="/storage/{{ $company->logo }}" style="width: 178px;">
                            </div>
                        </div>
                        <div class="card-content">
                            <form method="post" action="{{route('survey.answer')}}" class="card-body">
                                <p class="my-2 text-center"><b>Help us to improve,  your opinion is important to us.</b></p>
                                @csrf
                                @if($survey->status==0)
                                    @php
                                    $survey_subject=['Property_Appointment'=>3,'Property_Viewing'=>2,'Contact_Appointment'=>3,'Contact_Viewing'=>2];
                                    $questions=\App\Models\SurveyQuestion::where('company_id',$company->id)->where('status',1)->where('subject',$survey_subject[$survey->model])->get();
                                    @endphp
                                    <ul class="list-group list-group-flush">
                                        @foreach($questions as $row)
                                        <li class="list-group-item">
                                                <p><b>{{$row->question}}</b></p>
                                                <input type="hidden" name="question[]" value="{{$row->id}}">
                                                <div class="rate-start-box-{{$row->id}} d-flex">
                                                    <div>Very poor</div>
                                                    <div class="mx-1">
                                                    <span class="font-medium-1 rate-star d-flex">
                                                        <i class="fa fa-star text-secondary star star-1" data-rate="1" data-parent=".rate-start-box-{{$row->id}}"></i>
                                                        <i class="fa fa-star text-secondary star star-2" data-rate="2" data-parent=".rate-start-box-{{$row->id}}"></i>
                                                        <i class="fa fa-star text-secondary star star-3" data-rate="3" data-parent=".rate-start-box-{{$row->id}}"></i>
                                                        <i class="fa fa-star text-secondary star star-4" data-rate="4" data-parent=".rate-start-box-{{$row->id}}"></i>
                                                        <i class="fa fa-star text-secondary star star-5" data-rate="5" data-parent=".rate-start-box-{{$row->id}}"></i>
                                                    </span>
                                                        <input type="hidden" name="rate_{{$row->id}}">
                                                    </div>
                                                    <div>Very satisfied</div>
                                                </div>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <div class="form-group form-label-group mb-2 mt-3">
                                        <label for="comment">Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" placeholder="Comment"></textarea>
                                    </div>
                                    <input type="hidden" name="_id" value="{{$survey->id}}">
                                @else
                                    @php
                                        $SurveyAnswer=\App\Models\SurveyAnswer::where('survey_id',$survey->id)->get();
                                    @endphp
                                    <ul class="list-group list-group-flush">
                                        @foreach ($SurveyAnswer as $row)
                                            @php
                                                $SurveyQuestion=\App\Models\SurveyQuestion::find($row->survey_question_id);
                                            @endphp
                                        <li class="list-group-item">
                                            <p><b>{{$SurveyQuestion->question}}</b></p>
                                            <div class="d-flex">
                                                <div class="font-small-1">Very poor</div>
                                                <div class="mx-1">
                                                    <span class="font-medium-1 rate-star d-flex">
                                                        <i class="fa fa-star text-{{( ($row->rate>=1) ? 'warning' : 'secondary' )}}"></i>
                                                        <i class="fa fa-star text-{{( ($row->rate>=2) ? 'warning' : 'secondary' )}}"></i>
                                                        <i class="fa fa-star text-{{( ($row->rate>=3) ? 'warning' : 'secondary' )}}"></i>
                                                        <i class="fa fa-star text-{{( ($row->rate>=4) ? 'warning' : 'secondary' )}}"></i>
                                                        <i class="fa fa-star text-{{( ($row->rate>=5) ? 'warning' : 'secondary' )}}"></i>
                                                    </span>
                                                </div>
                                                <div class="font-small-1">Very satisfied</div>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ul>

                                    <div class="form-group form-label-group mb-2 mt-3">
                                        <label for="comment">Comment</label>
                                        <textarea class="form-control" id="comment" name="comment" placeholder="Comment" disabled>{{$survey->comment}}</textarea>
                                    </div>

                                @endif
                                <button type="submit" class="btn btn-primary waves-effect waves-light d-block mx-auto" {{($survey->status==0) ? '' : 'disabled'}}>Submit</button>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-center font-medium-3">
                                        {!! ($company)  ?  '<a href="'.$company->facebook.'" class="mx-1"><i class="fa fa-facebook"></i></a>'  :  '' !!}
                                        {!! ($company)  ?  '<a href="'.$company->instagram.'" class="mx-1"><i class="fa fa-instagram"></i></a>'  :  '' !!}
                                        {{--                                    {!! ($company)  ?  '<a href="'.$company->tiktok.'" class="mx-1"><i class="fa fa-instagram"></i></a>'  :  '' !!}--}}
                                        {!! ($company)  ?  '<a href="'.$company->linkedin.'" class="mx-1"><i class="fa fa-linkedin"></i></a>'  :  '' !!}
                                        {!! ($company)  ?  '<a href="'.$company->youtube.'" class="mx-1"><i class="fa fa-youtube"></i></a>'  :  '' !!}

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/vendors/js/vendors.min.js"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/pwa.js"></script>
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
