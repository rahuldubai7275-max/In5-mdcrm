
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

    <link id="datepickerTheme" href="/css/plugins/datepiker/persian-datepicker.css" rel="stylesheet"/>

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
                    <div class="card-header justify-content-center pb-0">
                        <div class="card-title">
                            <img src="/images/{{LOGO}}" style="width: 178px;">
                        </div>
                    </div>
                    <div class="card-content">
                        <form class="card-body" method="post" action="{{ route('recruitment.form.store') }}" novalidate>
                            @csrf
                            <div class="row mt-1">
                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="first_name">First Name <span>*</span></label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="last_name">Last Name <span>*</span></label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" required>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="gender">Gender <span>*</span></label>
                                        <select class="form-control" name="gender" id="gender" required>
                                            <option value="">Select</option>
                                            @foreach(GENDER as $key=>$value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="education_level">Education Level <span>*</span></label>
                                        <select class="form-control" name="education_level" id="education_level" required>
                                            <option value="">Select</option>
                                            @foreach(EducationLevel as $key=>$value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="job_title">Applying For <span>*</span></label>
                                        <select class="form-control" name="job_title" id="job_title" required>
                                            <option value="">Select</option>
                                            @php
                                                $JobTitles=\App\Models\JobTitle::orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($JobTitles as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-sm-4">
                                    <fieldset class="form-group form-label-group">
                                        <label>Nationality <span>*</span></label>
                                        <select class="form-control select2" id="nationally" name="nationally" required>
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="starting_date">Available From <span>*</span></label>
                                        <input type="text" class="form-control format-picker" id="starting_date" name="starting_date" autocomplete="off" placeholder="Available From" readonly required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="years_of_experience">Years Of Experience In U.A.E <span>*</span></label>
                                        <input type="text" class="form-control" id="years_of_experience" name="years_of_experience" placeholder="Years Of Experience In U.A.E" required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="expected_salary">Expected Salary</label>
                                        <input type="text" class="form-control number-format" id="expected_salary" name="expected_salary" placeholder="Expected Salary">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="commission_percent">Commission %</label>
                                        <input type="number" class="form-control" id="commission_percent" name="commission_percent" placeholder="Commission %">
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="mobile_number">Mobile number <span>*</span></label>
                                        <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Mobile Number" required>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group form-label-group">
                                        <label for="email">Email <span>*</span></label>
                                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group form-label-group">
                                        <label for="languages">Languages <span>*</span></label>
                                        <select class="form-control select2" multiple name="languages[]" id="languages" required>
                                            <option value="">Select</option>
                                            @php
                                                $languages=\App\Models\Language::orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($languages as $row)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group form-label-group mb-0">
                                        <label for="special_note">Special Note</label>
                                        <input type="text" class="form-control" id="special_note" name="special_note" placeholder="Special Note">
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <fieldset class="form-group mb-0">
                                        <label for="cv-file">CV</label>
                                        <div class="d-flex">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input document-upload" data-this="cv-file" id="cv-file"
                                                       data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".cv-progress-bar" data-input="#cv">
                                                <label class="custom-file-label" for="cv-file">Choose file</label>
                                            </div>
                                            <!--<div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="document"><i class="fa fa-download"></i></a></div>-->
                                        </div>
                                        <input type="hidden" id="cv" name="cv">
                                        <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                            <div class="progress-bar bg-teal progress-bar-striped cv-progress-bar" role="progressbar"
                                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <div class="mt-2 text-right">
                                <button type="button" id="submit" class="btn btn-primary">Save</button>
                                <button type="submit" name="submit" class="d-none"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="/vendors/js/vendors.min.js"></script>
    <script src="/vendors/js/forms/select/select2.full.min.js"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/pwa.js"></script>
    <script type="text/javascript" src="/js/scripts/magnific-popup.min.js"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script src="https://crm.smdamarketing.com/vendors/js/forms/validation/jquery.validate.min.js"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jqBootstrapValidation.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/forms/validation/form-validation.js')) }}"></script>
    <!-- Datepicker -->
    <script src="/js/scripts/datepiker/persian-date.min.js"></script>
    <script src="/js/scripts/datepiker/persian-datepicker.js"></script>
    <!-- datepicker -->

    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
    <script src="/js/scripts/footer.js"></script>
    <script>
        populateCountries("nationally", "");

        function toast_($title,$msg,$timeOut=20000,$closeButton=true) {
            toastr.error($msg, $title, {"closeButton": $closeButton, "timeOut": $timeOut});
        }

        $('#submit').click(function(){
            let error=0;
            let commission_percent=$('#commission_percent').val();
            let expected_salary=$('#expected_salary').val();
            if(commission_percent>100){
                $('#commission_percent').parent().addClass('error');
                error=1;
            }
            if(expected_salary=='' && commission_percent==''){
                toast_('','One of the expected salary or commission must be filled.',$timeOut=20000,$closeButton=true);
                error=1;
            }
            if(error==0){
                $('button[name="submit"]').click();
            }
        });

        $('.format-picker').persianDatepicker({
            initialValue: false,
            format: 'YYYY-MM-DD',
            // altFormat: 'YYYY-MM-DD',
            calendarType: 'gregorian',
            gregorian:{
                locale:'en'
            },
            text:{
                btnNextText: '>'
            },
            autoClose: true,
            calendar:{
                persian: {
                    locale: 'en'
                }
            },
            toolbox:{
                enabled:true,
                todayButton:{
                    enabled: true,
                },
                calendarSwitch:{
                    enabled: false,
                },
            },
            navigator:{
                text:{
                    btnNextText:'>',
                    btnPrevText:'<'
                },
                scroll:{
                    enabled: false
                },
            },
        });
    </script>

@endsection
