@extends('layouts/contentLayoutMaster')

@section('title', 'Add Lead')

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/wizard.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection
@section('content')

@php
    $admin = Auth::guard('admin')->user();
@endphp

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form class="row" method="post" action="{{ $route }}" novalidate>
                @csrf
                <div class="col-lg-12 col-xl-12">
                    <div class="row">
                        <div class="col-sm-4 property-detail-form">
                            <div class="row m-0">
                                <div class="col-12 pb-2">
                                    <h6 class="text-primary">Lead Contact</h6>
                                </div>
                                <div class="col-6">
                                    <div class="form-group form-label-group">
                                        <input type="text" class="form-control required" id="ReferenceNumber" name="ReferenceNumber" disabled value="Lead-{{($lead) ? $lead->id : $leadMax}}" placeholder="Reference Number" >
                                        <label for="MarketAppraisalRef">Reference Number</label>
                                    </div>
                                </div>
                                <div  class="col-6">
                                    <div class="form-group form-label-group">
                                        <label for="contact_category">Contact Category <span>*</span></label>
                                        <select class="custom-select form-control" id="contact_category" name="contact_category" required>
                                            <option value="" selected>Select</option>
                                            <option value="buyer">Buyer</option>
                                            <option value="tenant">Tenant</option>
                                            {{--<option value="agent">Agent</option>--}}
                                            <option value="owner">Owner</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="name">Full Name <span>*</span></label>
                                        <input type="text" id="name" name="name" placeholder="Full Name" value="{{($lead) ? $lead->name : ''}}" class="form-control" required>
                                    </fieldset>
                                </div>
                                <div class="col-sm-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="mobile_number">Phone Number</label>
                                        <input type="text" id="mobile_number" name="mobile_number" value="{{($lead) ? $lead->mobile_number : ''}}" maxlength="20" onkeypress="return isNumber(event)" class="form-control country-code">
                                    </fieldset>
                                </div>
                                <div class="col-sm-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="mobile_number_2">Second Number</label>
                                        <input type="text" id="mobile_number_2" name="mobile_number_2" value="{{($lead) ? $lead->mobile_number_2 : ''}}" maxlength="20" onkeypress="return isNumber(event)" class="form-control country-code">
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="email">Email</label>
                                        <input type="text" id="email" name="email" value="{{($lead) ? $lead->email : ''}}" placeholder="Email" maxlength="64" class="form-control">
                                    </fieldset>
                                </div>

                                <div class="col-sm-6">
                                    <fieldset class="form-group form-label-group">
                                        <label for="source">Lead Source</label>
                                        <select class="form-control select2" id="source" name="source">
                                            <option value="">All</option>
                                            @php
                                                $ContactSources=App\Models\ContactSource::orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($ContactSources as $CSource)
                                                <option value="{{ $CSource->id }}" {{($lead && $lead->source==$CSource->id) ? 'selected' : ''}}>{{ $CSource->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label for="referrer">Job Title</label>
                                        <select class="custom-select form-control" id="job_title" name="job_title">
                                            <option value="">Select</option>
                                            @php
                                                $JobTitles=\App\Models\JobTitle::where('possession_or_job',2)->orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($JobTitles as $jobTitle)
                                                <option value="{{ $jobTitle->id }}" {{($lead && $lead->job_title_id==$jobTitle->id) ? 'selected' : ''}}>{{ $jobTitle->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group form-label-group">
                                        <label for="referrer">Recommended From</label>
                                        <select class="custom-select form-control" id="referrer" name="referrer">
                                            <option value="">Select</option>
                                            @php
                                                $Referrers=\App\Models\Referrer::where('admin_id',$admin->id)->orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($Referrers as $referrer)
                                                <option value="{{ $referrer->id }}" {{($lead && $lead->referrer_id==$referrer->id) ? 'selected' : ''}}>{{ $referrer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="budget">Budget</label>
                                        <input type="text" id="budget" name="budget" value="{{($lead) ? number_format($lead->budget) : ''}}" class="form-control number-format">
                                    </fieldset>
                                </div>

                                <div  class="col-sm-12 {{($admin->type==3 || $admin->type==4 || $admin->type==5) ? 'd-none' : ''}}">
                                    <fieldset class="form-group form-label-group">
                                        <label for="select-agent">Assign To <span>*</span></label>
                                        <select class="form-control select2" id="assign_to" name="assign_to" required>
                                            <option value="">All</option>
                                            @php
                                                $Agents=\Helper::getCM_DropDown_list('1');
                                            @endphp
                                            @foreach($Agents as $agent)
                                                <option value="{{ $agent->id }}" {{($admin->type==3 || $admin->type==4) ?( ($admin->id==$agent->id)? 'selected':'') : ''}}>{{ $agent->firstname.' '.$agent->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                {{--<div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="created_by">Created By</label>
                                        <input type="text" class="form-control" id="created_by" name="created_by" value="{{$admin->firstname.' '.$admin->lastname}}"  disabled>
                                    </fieldset>
                                </div>--}}
                            </div>
                        </div>
                        <div class="col-sm-4 property-detail-form">
                            <div class="row m-0">
                                <div class="col-12 pb-2">
                                    <h6 class="text-primary">Lead Information</h6>
                                </div>

                                <div class="col-sm-12 {{($lead && $lead->contact_category=='tenant') ? 'd-none' : ''}}">
                                    <div class="form-group form-label-group">
                                        <label>Looking For</label>
                                        <select class="custom-select form-control" id="looking_for" name="looking_for">
                                            <option value="">Select</option>
                                            @foreach(BUYER_LOOKING_FOR as $kay=>$value)
                                                <option value="{{$kay}}">{{$value}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    <div class="form-group form-label-group">
                                        <label for="Emirate">Emirate</label>
                                        <select class="custom-select form-control" id="emirate" name="emirate">
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

                                <div class="col-sm-12 d-none">
                                    <div class="form-group form-label-group">
                                        <label for="developer">Developer</label>
                                        <select class="custom-select form-control match-off-plan-project-select select2" id="developer" name="developer">
                                            <option value="">Select</option>
                                            @php
                                                $Developers=\App\Models\Developer::orderBy('name','ASC')->get();
                                            @endphp
                                            @foreach($Developers as $developer)
                                                <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 d-none">
                                    <div class="form-label-group form-group">
                                        <select class="form-control select-2-off-plan-project" name="off_plan_project" id="off_plan_project">
                                            <option value="">Select</option>
                                        </select>
                                        <label for="off_plan_project">New Projects</label>
                                    </div>
                                </div>

                                <div  class="col-sm-12 d-none">
                                    <div class="form-group form-label-group">
                                        <label for="master_project">Master Project</label>
                                        <select class="custom-select form-control select2" id="master_project" name="master_project">

                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 d-none">
                                    <fieldset class="form-group form-label-group">
                                        <label for="Community">Project</label>
                                        <select class="form-control select2" name="community" id="community">

                                        </select>
                                    </fieldset>
                                </div>

                                @if(!$lead)
                                <div class="col-sm-12">
                                    <div class="pt-1">
                                        <fieldset class="form-group form-label-group mb-0">
                                            <textarea data-length="1500" class="form-control char-textarea" name="note" id="note" rows="6" placeholder="Note"></textarea>
                                            <label>Note</label>
                                        </fieldset>
                                        <small class="counter-value float-right"><span class="char-count">0</span> / 1500 </small>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 text-center">
                    @if($lead) <input type="hidden" name="_id" value="{{$lead->id}}"> @endif
                    <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-sm-right mt-1">Save Lead</button>
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
    <script src="{{ asset(mix('js/scripts/forms/wizard-steps.js')) }}"></script>
    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/off-plan-project-select.js"></script>
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>

    <script>
        @if($lead)
        $(document).ready(function(){
            $('#contact_category').val('{{$lead->contact_category}}').change();
            $('#assign_to').val('{{$lead->assign_to}}').change();

            $('#emirate').val('{{$lead->emirate_id}}').change();

            $('#looking_for').val('{{$lead->looking_for}}').change();
        });
        @endif
    </script>
    <script>
        $('#contact_category').change(function () {
            let val=$(this).val();
            $('#developer').val('{{($lead) ? $lead->developer_id : ''}}').change();
            $('#budget').val('{{($lead) ? number_format($lead->budget) : ''}}');

            $('#budget').parent().parent().removeClass('d-none');
            $('#looking_for').parent().children('label').html('Looking For');

            if(val=='owner'){
                $('#looking_for').parent().children('label').html('Available');
                $('#budget').parent().parent().addClass('d-none');
                $('#budget').val('');
            }

            $('#looking_for').val('{{($lead) ? $lead->looking_for : ''}}').change();
            $('#looking_for').parent().parent().removeClass('d-none');

            if(val=='tenant'){
                $('#looking_for').val(1).change();
                $('#looking_for').parent().parent().addClass('d-none');

                $('#developer').val('').trigger('change');
                $('#off_plan_project').val('').trigger('change');
            }
        });


        $('#looking_for').change(function(){
            let val=$(this).val();
            let contact_category=$('#contact_category').val();

            $('#master_project , #community , #developer , #off_plan_project').parent().parent().removeClass('d-none');
            if(val=='1') {
                $('#developer , #off_plan_project').parent().parent().addClass('d-none');
            }
            if(val=='2') {
                $('#community , #master_project').parent().parent().addClass('d-none');
            }

            $('#emirate').val('{{(($lead)? $lead->emirate_id : '')}}').change();

            if(contact_category!='tenant') {
                $('#developer').val('{{($lead)? $lead->developer_id : '' }}').trigger('change');
                @if($offPlanProject)
                $('#off_plan_project')
                    .empty()
                    .append('<option selected value="{{$offPlanProject->id}}">{{$offPlanProject->project_name}}</option>');
                $('#off_plan_project').select2('data', {
                    id: "{{$offPlanProject->id}}",
                    label: "{{$offPlanProject->project_name}}"
                });
                @else
                $('#off_plan_project').val('').trigger('change');
                @endif
            }else{
                //$('#developer').val('').trigger('change');
                //$('#off_plan_project').val('').trigger('change');
            }

            $('#master_project').val('{{($lead)? $lead->master_project_id : '' }}').trigger('change');
            $('#community').val('{{($lead)? $lead->community_id : '' }}').trigger('change');

        });

        $('#emirate').change(function () {
            let val=$(this).val(); //alert(val);
            getMasterProject(val);
        });

        function getMasterProject(val){
            $.ajax({
                url:"{{ route('master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('#master_project').html(response);
                    @if($lead)$('#master_project').val('{{$lead->master_project_id }}').trigger('change');@endif
                }
            });
        }

        $('#master_project').change(function () {
            let val=$(this).val();

            getCommunity(val);
            $('#community').change();
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
                    @if($lead)$('#community').val('{{$lead->community_id }}').trigger('change');@endif
                }
            });
        }

        $(".country-code").intlTelInput({
            // allowDropdown: false,
            autoHideDialCode: false,
            // autoPlaceholder: "off",
            dropdownContainer: "body",
            // excludeCountries: ["us"],
            formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //     $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //         var countryCode = (resp && resp.country) ? resp.country : "";
            //         callback(countryCode);
            //     });
            // },
            hiddenInput: "full_number",
            initialCountry: "auto",
            nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            preferredCountries: ['ae'],
            // separateDialCode: true,
            utilsScript: "js/build/js/utils.js"
        });
    </script>


@endsection
