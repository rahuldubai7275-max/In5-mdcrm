
@extends('layouts/contentLayoutMaster')

@section('title', 'Add Developer Project')


@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/wizard.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">

    <style>
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
    </style>
@endsection
@section('content')

    @php
        $admin = Auth::guard('admin')->user();
    @endphp

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <form class="row" method="post" action="{{ route($route) }}" novalidate>
                    @csrf
                    <div class="col-lg-12 col-xl-12">
                        <div class="row">
                            <div class="col-sm-4 property-detail-form">
                                <div class="row m-0">
                                    <div class="col-12 pb-2">
                                        <h6 class="text-primary">Information</h6>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <label for="Emirate">Emirate <span>*</span></label>
                                            <select class="custom-select form-control" id="Emirate" name="Emirate" required>
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
                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <label for="MasterProject">Master Project <span>*</span></label>
                                            <select class="custom-select form-control select2" id="MasterProject" name="MasterProject" required>
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <label for="Community">Project</label>
                                            <select class="custom-select form-control select2" id="Community" name="Community">
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>

                                    {{--<div class="col-12">
                                        <div class="form-group form-label-group expiration-date-box">
                                            <input type="text" class="form-control required" id="project_number" name="project_number" value="{{ ($offPlanProject) ? $offPlanProject->project_number : '' }}" placeholder="Project Number">
                                            <label>Project Number</label>
                                        </div>
                                    </div>--}}

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Developer">Developer <span>*</span></label>
                                            <select class="custom-select form-control select2" id="Developer" name="Developer" required>
                                                <option value="">Select</option>
                                                @php
                                                    $developers=\App\Models\Developer::get();
                                                @endphp
                                                @foreach($developers as $dev)
                                                    <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Developer">Status <span>*</span></label>
                                            <select class="custom-select form-control" id="status" name="status" required>
                                                @foreach(OffPlanProjectStatus as $key=>$value)
                                                    <option value="{{ $key }}" {{ ($offPlanProject) ? (($key==$offPlanProject->status) ? 'selected' : '') : (($key==1) ? 'selected' : '') }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Residential / Commercial<span>*</span></label>
                                            <select class="custom-select form-control" id="Type" name="Type" required>
                                                <option value="">Select</option>
                                                @foreach(PropertyType as $key => $value)
                                                    <option value="{{ $key }}" {{ ($offPlanProject && $offPlanProject->type==$key) ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Property Type <span>*</span></label>
                                            <select class="custom-select form-control" id="PropertyType" name="PropertyType" required>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group expiration-date-box">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="date_of_launch" name="date_of_launch" value="{{ ($offPlanProject) ? $offPlanProject->date_of_launch : '' }}" placeholder="Date Of Launch">
                                            <label>Date Of Launch</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" id="starting_price" name="starting_price" value="{{ ($offPlanProject) ? number_format($offPlanProject->starting_price) : '' }}" placeholder="Starting Price">
                                            <label>Starting Price</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Compilation Year</label>
                                            <select class="custom-select form-control" id="year" name="year">
                                                <option value="">Select</option>
                                                @php
                                                    $year=date('Y');
                                                    $yearTarget=$year+10;
                                                @endphp
                                                @for($year; $year<$yearTarget ; $year++)
                                                    <option value="{{$year}}">{{$year}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Compilation Quarter</label>
                                            <select class="custom-select form-control" id="quarter" name="quarter">
                                                <option value="">Select</option>
                                                <option value="1">Q1</option>
                                                <option value="2">Q2</option>
                                                <option value="3">Q3</option>
                                                <option value="4">Q4</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Bedroom From</label>
                                            <select class="custom-select form-control" id="bedroom_from" name="bedroom_from">
                                                <option value="">Select</option>
                                                <option value="0">Studio</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Bedroom To</label>
                                            <select class="custom-select form-control" id="bedroom_to" name="bedroom_to">
                                                <option value="">Select</option>
                                                <option value="0">Studio</option>
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                <option value="11">11</option>
                                                <option value="12">12</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control number-format" id="size_from" name="size_from" value="{{ ($offPlanProject) ? $offPlanProject->size_from : '' }}" placeholder="Size From">
                                            <label>Size From (sqft)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control number-format" id="size_to" name="size_to" value="{{ ($offPlanProject) ? $offPlanProject->size_to : '' }}" placeholder="Size To">
                                            <label>Size To (sqft)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" autocomplete="off" id="payment_plan" name="payment_plan" value="{{ ($offPlanProject) ? $offPlanProject->payment_plan : '' }}" placeholder="00 / 00">
                                            <label>Payment Plan</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>PHPP</label>
                                            <select class="custom-select form-control" id="phpp" name="phpp">
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-8">
                                <div class="row mb-1">
                                    <div class="col-sm-7">
                                        <h6 class="text-primary float-left">Media & Description</h6>
                                    </div>
                                </div>
                                <div class="custom-scrollbar pr-1" style="max-height: 550px;">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <fieldset class="form-group">
                                                <label>Photos</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="AttachFile" accept=".jpeg,.jpg,.png,.gif,.svg,.webp" multiple>
                                                    <label class="custom-file-label" for="AttachFile">Photos</label>
                                                </div>
                                            </fieldset>
                                        </div>
                                        <div class="col-sm-3">
                                            <button class="btn btn-primary waves-effect px-1 mt-2" id="AttachFileBtn" data-type="multi" data-token="{{ csrf_token() }}" data-action="{{ env('MD_URL') }}/api/upload/image" type="button" disabled="disabled">
                                                Upload
                                            </button>
                                        </div>

                                        <div class="progress progress-bar-primary progress-xl mb-2 d-none w-100">
                                            <div class="progress-bar bg-teal progress-bar-striped" id="PresentProgressBar" role="progressbar"
                                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row custom-scrollbar AttachFileBox" style="max-height: 120px;">
                                                @if($offPlanProject)
                                                    @if($offPlanProject->pictures)
                                                        @foreach(explode(',', $offPlanProject->pictures) as $picture)
                                                            <div class="border mx-auto mb-1 property-iamge-box">
                                                                <a href="{{ env('MD_URL') }}/storage/{{ $picture }}" class="property-image">
                                                                    <img src="{{ env('MD_URL') }}/storage/{{ $picture }}" height="100px" width="100px">
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>
                                            <a href="#showAllImageModal" data-toggle="modal" class="show-all-images"><small>Show All</small></a>

                                            <!-- Modal Show All Images -->
                                            <div class="modal fade text-left" id="showAllImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title" id="myModalLabel16">Images</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row showAllAttachFileBox sort-element mx-0 px-1">
                                                                @if($offPlanProject)
                                                                    @if($offPlanProject->pictures)
                                                                        @foreach(explode(',', $offPlanProject->pictures) as $picture)
                                                                            <div class="col-sm-3 mb-1">
                                                                                <div class="border">
                                                                                    <div class=" mx-auto property-iamge-box">
                                                                                        <a href="{{ env('MD_URL') }}/storage/{{ $picture }}" class="property-image d-block w-100">
                                                                                            <img src="{{ env('MD_URL') }}/storage/{{ $picture }}">
                                                                                        </a>
                                                                                        <input type="hidden" value="{{ $picture }}" name="InputAttachFile[]">
                                                                                    </div>
                                                                                    <div class="action clearfix px-1" data-name="{{ $picture }}">
                                                                                        <a title="remove" href="javascript:void(0)" class="file-delete d-blok w-100"><i class="feather icon-trash-2"></i> <small>Delete</small></a>

                                                                                        <div class="custom-control custom-switch d-flex align-items-center">
                                                                                            <input type="checkbox" class="custom-control-input" vlaue="1" name="{{ current(explode('.',$picture)) }}" id="customSwitch{{ $loop->index }}">
                                                                                            <label class="custom-control-label" for="customSwitch{{ $loop->index }}"></label>
                                                                                            <span class="switch-label"><small>Watermark</small></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a title="remove" href="javascript:void(0)" class="delete-all-img d-blok text-dark"><i class="feather icon-trash-2"></i> Delete All</a>
                                                            <div class="custom-control custom-switch d-flex align-items-center">
                                                                <input type="checkbox" class="custom-control-input" id="checkAllWatermark">
                                                                <label class="custom-control-label" for="checkAllWatermark"></label>
                                                                <span class="switch-label">Watermark All</span>
                                                            </div>
                                                            <!--<button type="button" class="btn btn-primary" id="checkAllWatermark"></button>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{--<fieldset class="form-group mb-0">
                                        <label for="video_link-file">Video</label>
                                        <div class="d-flex">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input document-upload" data-this="video_link-file" id="video_link-file"
                                                       data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".video_link-progress-bar" data-input="#video_link">
                                                <label class="custom-file-label" for="video_link-file">{{ ($offPlanProject && $offPlanProject->video_link) ? 'Video' : 'Choose file' }}</label>
                                            </div>
                                            <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="video_link"><i class="fa fa-download"></i></a></div>
                                        </div>
                                        <input type="hidden" id="video_link" name="video_link" value="{{ ($offPlanProject) ? $offPlanProject->video_link : old('video_link') }}">
                                        <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                            <div class="progress-bar bg-teal progress-bar-striped video_link-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            </div>
                                        </div>
                                    </fieldset>--}}

                                    <div class="row mt-1 mx-0">
                                        <div class="col-11 p-0">
                                            <div class="form-group form-label-group mt-2">
                                                <input type="text" class="form-control char-textarea" id="video_link" name="video_link" value="{{ ($offPlanProject) ? $offPlanProject->video_link : '' }}" placeholder="Video Link">
                                                <label for="Video Link">Video Link</label>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <a href="#" target="_blank" class="video_view font-medium-3 mt-2"><i class="feather icon-video"></i></a>
                                        </div>
                                    </div>

                                    <fieldset class="form-group">
                                        <label for="file-file">File</label>
                                        <div class="d-flex">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input document-upload" data-this="file-file" id="file-file"
                                                       data-token="{{ csrf_token() }}" data-action="{{ env('MD_URL') }}/api/upload/file" data-progress=".file-progress-bar" data-input="#file">
                                                <label class="custom-file-label" for="other-file">{{ ($offPlanProject && $offPlanProject->file) ? 'File' : 'Choose file' }}</label>
                                            </div>
                                            <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="file"><i class="fa fa-download"></i></a></div>
                                        </div>
                                        <input type="hidden" id="file" name="file" value="{{ ($offPlanProject) ? $offPlanProject->file : old('file') }}">
                                        <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                            <div class="progress-bar bg-teal progress-bar-striped file-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                            </div>
                                        </div>
                                    </fieldset>


                                    <div class="form-group form-label-group">
                                        <input type="text" class="form-control required" id="project_name" name="project_name" value="{{ ($offPlanProject) ? $offPlanProject->project_name : '' }}" placeholder="Project Name" required>
                                        <label>Project Name <span>*</span></label>
                                    </div>


                                    <div class="">
                                        <fieldset class="form-group form-label-group">
                                            <textarea class="form-control char-textarea" name="description" id="description" rows="6" placeholder="Description">{{ ($offPlanProject) ? $offPlanProject->description : '' }}</textarea>
                                            <label>Description</label>
                                        </fieldset>
                                        {{--<small class="counter-value float-right"><span class="char-count">0</span> / 1500 </small>--}}
                                    </div>

                                    <div class="form-group form-label-group">
                                        <input type="text" class="form-control" id="map" name="map" placeholder="Map">
                                        <label>Map</label>
                                    </div>

                                    <div id="map-box">
                                        {!! ($offPlanProject) ? $offPlanProject->map : '' !!}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-12 text-right">
                        <input type="hidden" name="_id" value="{{ ($offPlanProject) ? $offPlanProject->id : '' }}">
                        <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right mt-1">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <!--<script src="{{ asset(mix('vendors/js/extensions/jquery.steps.min.js')) }}"></script>-->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <script type="text/javascript" src="/js/scripts/magnific-popup.min.js"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    {{--    <script src="{{ asset(mix('js/scripts/forms/wizard-steps.js')) }}"></script>--}}
    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>
    <script src="/js/scripts/uploade-doc.js"></script>

    <script>
        $(document).ready(function() {
            $('#quarter').val('{{ ($offPlanProject) ? $offPlanProject->quarter : '' }}');
            $('#year').val('{{ ($offPlanProject) ? $offPlanProject->year : '' }}');
            $('#bedroom_from').val('{{ ($offPlanProject) ? $offPlanProject->bedroom_from : '' }}');
            $('#bedroom_to').val('{{ ($offPlanProject) ? $offPlanProject->bedroom_to : '' }}');
            $("#phpp").val("{{ ($offPlanProject) ? $offPlanProject->phpp : '' }}");//.change();
            $("#Emirate").val("{{ ($offPlanProject) ? $offPlanProject->emirate_id : '' }}");//.change();
            $("#Developer").val("{{ ($offPlanProject) ? $offPlanProject->developer_id : '' }}").change();
        });
    </script>
    <script>
        var UploderType='multi';
        $('#AttachFile').change(function(){
            $('#AttachFileBtn').removeAttr("disabled");
        });

        $('#AttachFileBtn').click(function(){
            $('#AttachFileBtn').attr("disabled","disabled");
            UploderType=$(this).data('type');
            var Action=$(this).data('action');
            var token=$(this).data('token');
            AttachFile(Action,token);
        });

        function AttachFile(Action,token) {
            var file = _("AttachFile").files[0];
            var form_data = new FormData();

            // Read selected files
            var totalfiles = document.getElementById('AttachFile').files.length;
            for (var index = 0; index < totalfiles; index++) {
                // form_data.append("files[]", document.getElementById('AttachFile').files[index]);


                //  alert(file.name+" | "+file.size+" | "+file.type);
                var formdata = new FormData();
                formdata.append("AttachFileSubmit", "0");
                formdata.append("_token", token);
                formdata.append("FileFile", document.getElementById('AttachFile').files[index]);
                var ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", progressPHandler, false);
                ajax.addEventListener("load", completePHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", Action);
                ajax.send(formdata);
            }
        }
        function _(el) {
            return document.getElementById(el);
        }

        function progressPHandler(event) {
            $('#PresentProgressBar').parent().removeClass("d-none");
            $('#AttachFileBtn').attr('disabled','disabled');
            var percent = (event.loaded / event.total) * 100;
            $("#PresentProgressBar").css({"width": Math.round(percent) + "%"});
            $("#PresentProgressBar").html('Uploading');//html(Math.round(percent) + "%");
        }

        function completePHandler(event) {
            var response = jQuery.parseJSON( event.target.responseText );
            // alert( event.target.responseText )
            if(response.result=='true'){
                $('.AttachFileBox').append(`<div class="border mx-auto mb-1 property-iamge-box">
                                        <a href="{{env('MD_URL')}}${response.link}" class="property-image">
                                            <img src="{{env('MD_URL')}}${response.link}" height="100px" width="100px">
                                        </a>
                                    </div>

                                    `);

                let name=response.name;
                name=name.split(".")[0];
                $('.showAllAttachFileBox').append(`<div class="col-sm-3 mb-1">
                                        <div class="border">
                                        <div class=" mx-auto property-iamge-box">
                                            <a href="{{env('MD_URL')}}${response.link}" class="property-image d-block w-100">
                                                <img src="{{env('MD_URL')}}${response.link}">
                                            </a>
                                            <input type="hidden" value="${response.name}" name="InputAttachFile[]">
                                        </div>
                                        <div class="action clearfix" data-name="${response.name}">
                                            <a title="remove" href="javascript:void(0)" class="file-delete d-blok w-100"><i class="feather icon-trash-2"></i> <small>Delete</small></a>

                                            <div class="custom-control custom-switch d-flex align-items-center">
                                                <input type="checkbox" class="custom-control-input"  vlaue="1" name="${name}" id="${name}">
                                                <label class="custom-control-label" for="${name}"></label>
                                                <span class="switch-label"><small>Watermark</small></span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>`);
                if(UploderType!='multi'){
                    $("#PresentProgressBar").html('<p class="m-0">upload successful');
                    $('#AttachFileBtn').addClass('d-none');
                }else{
                    $('#PresentProgressBar').parent().addClass("d-none");
                    $('#AttachFileBtn').attr('disabled','disabled');
                    $("#PresentProgressBar").css({"width": 0 + "%"});
                    $("#PresentProgressBar").html('');
                    $("#AttachFile").val('').parent().children('label').html('');
                }
                $('#AttachFileBtn').removeAttr('disabled','disabled');
            }else{
                alert(response.message);
            }
        }

        function errorHandler(event) {
            //_("status").innerHTML = "Upload Failed";
        }

        function abortHandler(event) {
            // _("status").innerHTML = "Upload Aborted";
        }

        $('body').on('click','.file-delete',function(){
            var file=$(this).parent().data('name');
            var e=$(this).parent().parent();

            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url:"{{ route('delete-image') }}",
                        type:"POST",
                        data:{
                            _token:'{{ csrf_token() }}',
                            FileDelete:file
                        },
                        success:function (response) {
                            editImages(file);
                            e.remove();
                        }
                    });
                }
            });
        });

        $('body').on('click','.delete-all-img',function(){
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $( ".showAllAttachFileBox .file-delete" ).each(function() {
                        var file=$(this).parent().data('name');
                        var e=$(this).parent().parent();
                        $.ajax({
                            url:"{{ route('delete-image') }}",
                            type:"POST",
                            data:{
                                _token:'{{ csrf_token() }}',
                                FileDelete:file
                            },
                            success:function (response) {
                                editImages(file);
                                e.remove();
                            }
                        });
                    });
                }
            })

        });

        $("#checkAllWatermark").click(function(){
            $('.showAllAttachFileBox  input:checkbox').not(this).prop('checked', this.checked);
        });

        function editImages(image_name){
            $.ajax({
                url:"{{ route('off-plan-project.edit.image') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    id:"{{ ($offPlanProject) ? $offPlanProject->id : '' }}",
                    image_name:image_name
                }
            });
        }

        $('.property-image').magnificPopup({
            type: 'image',
            removalDelay: 300,
            mainClass: 'mfp-fade',
            gallery: {
                enabled: true
            },
            zoom: {
                enabled: true,
                duration: 300,
                easing: 'ease-in-out',
                opener: function (openerElement) {
                    return openerElement.is('img') ? openerElement : openerElement.find('img');
                }
            }
        });
    </script>
    <script>
        $('#map').change(function(){
            let val=$(this).val();
            if(val){
                $('#map-box').html(val);
            }
        });

        $('#Type').change(function(){
            getPropertyType();
        });

        getPropertyType();
        function getPropertyType(){
            let type=$('#Type').val();
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('#PropertyType').html(response);
                    $('#PropertyType').val('{{ ($offPlanProject) ? $offPlanProject->property_type_id : '' }}').change();
                }
            });
        }

        $('#Emirate').change(function () {
            let val=$(this).val();
            getMasterProject(val);
        });

        getMasterProject({{ ($offPlanProject) ? $offPlanProject->emirate_id : '' }});
        function getMasterProject(val){
            $.ajax({
                url:"{{ route( 'master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    Emirate:val
                },
                success:function (response) {
                    $('#MasterProject').html(response);
                    $('.master-propject-add').html(response);
                    $('#MasterProject').val('{{ ($offPlanProject) ? $offPlanProject->master_project_id : '' }}');
                    $('.master-propject-add').val('{{ ($offPlanProject) ? $offPlanProject->master_project_id : ''}}').change();
                }
            });
        }

        $('#MasterProject').change(function () {
            let val=$(this).val();
            $('.master-propject-add').val(val).change();
            getCommunity(val);
        });

        getCommunity('{{ ($offPlanProject) ? $offPlanProject->master_project_id : '' }}');
        function getCommunity(val){
            $.ajax({
                url:"{{ route('community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    MasterProject:val
                },
                success:function (response) {
                    $('#Community').html(response);
                    $('#Community').val('{{ ($offPlanProject) ? $offPlanProject->community_id : '' }}');
                    if($('#Community').val()=='' )
                        $('#Community').change();
                }
            });
        }

        $('#Community').change(function (){
            let val=$(this).val();
            let text='{{ ($offPlanProject) ? $offPlanProject->project_name : '' }}';
            if(val) {
                text=$("#Community option:selected").text();
            }

            $("#project_name").val(text);
        });

        $('#video_link').change(function(){
            videoView();
        });
        videoView();
        function videoView(){
            $('.video_view').attr('href','#').addClass('d-none').removeClass('d-block');
            let link=$('#video_link').val();
            if(link!='')
                $('.video_view').attr('href',link).removeClass('d-none').addClass('d-block');
        }
    </script>
    <script>
        var fixHelperModified = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },

            updateIndex = function (e, ui) {

            };

        $(".sort-element").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

        $(".sort-element").sortable({
            distance: 5,
            delay: 100,
            opacity: 0.6,
            cursor: 'move',
            update: function () {
            }
        });
    </script>

@endsection
