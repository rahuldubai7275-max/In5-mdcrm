
@extends('layouts/contentLayoutMaster')

@section('title', 'Leads')

@section('vendor-style')
    {{-- vendor css files --}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">--}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">

    <style>
        .picker {
            min-width: 250px;
        }
        .rented-until-box .picker , .available-from-box .picker , .expiration-date-box .picker {
            right: 0;
        }
    </style>

@endsection

@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    @php
        $admin=\Auth::guard('admin')->user();
        $company=\App\Models\Company::find($admin->company_id);
    @endphp
    <div class="card">
        <div class="card-header" style="padding-bottom: 1.5rem;">
            <h4 class="card-title">Filters</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse">
            <div class="card-body">
                <div class="users-list-filter">
                    <form action="{{route('leads-export')}}">
                        <div class="row">
                            {{--@if($admin->type<3)--}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="client-manager">Client Manager</label>
                                    <select class="form-control select2" id="client-manager" name="client_manager">
                                        <option value="0">Select</option>
                                        @php
                                        if($admin->super==1)
                                            $ClientManagers=\Helper::getCM_DropDown_list();
                                        else
                                            $ClientManagers=\Helper::getCM_DropDown_list('1');
                                        @endphp
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                        @if($admin->type<3) <option value="null">Not Assigned</option> @endif
                                    </select>
                                </fieldset>
                            </div>
                            {{--@endif--}}

                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="type">Lead Type</label>
                                            <select class="form-control" id="type" name="type">
                                                <option value="">Select</option>
                                                @foreach(LeadType as $kay=>$value)
                                                <option value="{{$kay}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="status">Lead Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="">Select</option>
                                                <option value="1">Added To Contact</option>
                                                <option value="2">Closed</option>
                                                {{--@if($admin->type==1)<option value="3">Deleted</option>@endif--}}
                                                <option value="0">Open</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            @if($company->private==1)
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-private">Privacy Level</label>
                                    <select class="form-control" id="private" name="private">
                                        <option value="0">Company</option>
                                        <option value="1">Personal</option>
                                    </select>
                                </fieldset>
                            </div>
                            @endif
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-contact-source">Lead Source</label>
                                    <select class="form-control select2" id="source" name="source">
                                        <option value="0">Select</option>
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
                                <fieldset class="form-group form-label-group">
                                    <label for="contact-categories">Contact Categories</label>
                                    <select class="form-control" id="contact-categories" name="contact_category">
                                        <option value="">Select</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="tenant">Tenant</option>
                                        <option value="agent">Agent</option>
                                        <option value="owner">Owner</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="referrer">Job Title</label>
                                    <select class="custom-select form-control" id="job-title" name="job_title">
                                        <option value="">Select</option>
                                        @php
                                            $JobTitles=\App\Models\JobTitle::where('possession_or_job',2)->orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($JobTitles as $jobTitle)
                                            <option value="{{ $jobTitle->id }}">{{ $jobTitle->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="referrer">Recommended From</label>
                                    <select class="custom-select form-control" id="referrer" name="referrer">
                                        <option value="">Select</option>
                                        @php
                                            $Referrers=\App\Models\Referrer::where('admin_id',$admin->id)->orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($Referrers as $referrer)
                                            <option value="{{ $referrer->id }}">{{ $referrer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="contact-categories">Reason For Closing</label>
                                    <select class="form-control" id="reason" name="reason">
                                        <option value="">Select</option>
                                        @foreach(LeadClosedReason as $reason)
                                            <option value="{{$reason}}">{{$reason}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
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

                            <div class="col-12 col-sm-6 col-lg-3">
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
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="master_project">Master Project</label>
                                    <select class="custom-select form-control select2" id="master_project" name="master_project">

                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-label-group">
                                    <div class="form-label-group form-group">
                                        <select class="form-control select-2-off-plan-project" id="off-plan-project" name="off_plan_project">
                                            <option value="">Select</option>
                                        </select>
                                        <label for="off_plan_project">New Projects</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="name">Full Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Full Name">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="mobile-number">Contact number</label>
                                    <input type="number" id="mobile-number" name="mobile-number" autocomplete="off" class="form-control" placeholder="Contact number">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="email">Email</label>
                                    <input type="text" id="email" name="email" autocomplete="off" class="form-control" placeholder="Email">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="from-budget">Budget</label>
                                            <input type="text" id="from-budget" name="from_budget" class="form-control number-format" placeholder="From">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="to-budget">Budget</label>
                                            <input type="text" id="to-budget" name="to_budget" class="form-control number-format" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="from-date">ENQ Date</label>
                                            <input type="text" id="from-date" name="from_date" autocomplete="off" class="form-control format-picker picker__input" placeholder="From">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="to-date">ENQ Date</label>
                                            <input type="text" id="to-date" name="to_date" autocomplete="off" class="form-control format-picker picker__input" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="ref-number-property">Property Ref</label>
                                            <input type="number" id="ref-number-property" name="ref-number-property" autocomplete="off" class="form-control" placeholder="Property Ref">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="ref-number">Lead Ref</label>
                                            <input type="number" id="ref-number" name="ref_number" autocomplete="off" class="form-control" placeholder="Lead Ref">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" id="search" class="btn bg-gradient-info waves-effect waves-light float-right">Search</button>
                                @if($admin->type==1)<button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right mr-1">Export</button>@endif
                                {{--@php
                                    $upload_contact_setting=\App\Models\Setting::where('company_id',$admin->company_id)->where('title','upload_contact')->first();
                                    $upload_contact_user=[];
                                    if($upload_contact_setting)
                                        $upload_contact_user=\App\Models\SettingAdmin::where('setting_id',$upload_contact_setting->id)->where('admin_id',$admin->id)->first();
                                @endphp--}}
                                @if($admin->type==1 || $company->private==1)<button type="button" class="btn bg-gradient-info waves-effect waves-light float-right mx-1" data-toggle="modal" data-target="#modalUpload">Import Leads</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('lead-action')}}" class="card">
        @csrf
        <div class="card-header">
            <h4 class="card-title">Leads</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="assign-to-list d-none">
                        <fieldset class="form-group form-label-group mt-2 mt-md-0" style="min-width:180px">
                            <label for="admin">Assign To</label>
                            <select class="form-control select2" id="LeadAssignTo" name="AssignTo">
                                <option value="">Select</option>
                                @php
                                    $Agents=$ClientManagers=\Helper::getCM_DropDown_list('1');
                                @endphp
                                @foreach($Agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </li>
                    <li><a href="/admin/lead" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light">Add Lead</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard pt-3">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>
                            @if($admin->type<3)
                            <div class="d-inline-block">
                                <fieldset>
                                    <label>
                                        <input type="checkbox" class="checkAll">
                                    </label>
                                </fieldset>
                            </div>
                            @endif
                        </th>
                        <th>REF</th>
                        <th>Lead Status</th>
                        <th>Lead Source</th>
                        <th>Lead Type</th>
                        <th>Property Ref</th>
                        <th>Reason For Closing</th>
                        <th>Full Name</th>
                        <th>Phone Number</th>
                        <th>Email</th>
                        <th>Master Project</th>
                        <th>Contact Categories</th>
                        <th>ENQ Date</th>
                        <th>Assigned From</th>
                        <th>Assigned Date</th>
                        <th>Assigned To</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <button type="submit" class="d-none" id="submit-action"></button>
    </form>

    <div class="modal fade text-left" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('leads-import')}}" class="modal-content" novalidate enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <p>Please make your Excel exactly like the template below.<br>
                                <b>Template: </b><a href="/images/lead_import_template.png" class="px-2 font-medium-5" target="_blank"><i class="feather icon-download"></i></a><br>
                                Please don't upload more than 500 records</p>
                        </div>
                        <div class="col-sm-12">
                            <fieldset class="form-group">
                                <label for="basicInputFile">File</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="file">Choose file</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit" class="btn btn-primary upload-file">Upload</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
{{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>--}}
{{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>--}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="/js/scripts/off-plan-project-select.js"></script>

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
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 12, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('leads.get.datatable') }}',
                'data': function(data){
                    // Append to data
                    // data.contact = 'contacts';
                    data._token='{{csrf_token()}}';
                    data.leads='{{request('new') ? 'new':''}}';
                    data.status=$('#status').val();
                    data.client_manager=$('#client-manager').val();
                    data.source=$('#source').val();
                    data.private=$('#private').val();
                    data.type=$('#type').val();
                    data.contact_category=$('#contact-categories').val();
                    data.referrer=$('#referrer').val();
                    data.job_title=$('#job-title').val();
                    data.developer=$('#developer').val();
                    data.emirate=$('#emirate').val();
                    data.master_project=$('#master_project').val();
                    data.off_plan_project=$('#off-plan-project').val();
                    data.ref_number=$('#ref-number').val();
                    data.ref_number_property=$('#ref-number-property').val();
                    data.name=$('#name').val();
                    data.mobile_number=$('#mobile-number').val();
                    data.email=$('#email').val();
                    data.reason=$('#reason').val();
                    data.from_budget=$('#from-budget').val();
                    data.to_budget=$('#to-budget').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }
            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 0,14,16 ]}],
            'columns': [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'status'},
                {data: 'source'},
                {data: 'type'},
                {data: 'property_id'},
                {data: 'colse_reason'},
                {data: 'name'},
                {data: 'mobile_number'},
                {data: 'email'},
                {data: 'master_project_id'},
                {data: 'contact_category'},
                {data: 'created_at'},
                {data: 'admin_id'},
                {data: 'assign_time'},
                {data: 'assign_to'},
                {data: 'action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('#private').change(function(){
            if($(this).val()=='1'){
                $('#client-manager').attr('disabled','disabled');
            }else{
                $('#client-manager').removeAttr('disabled');
            }
            $('#client-manager').val('').change();
        });

        $('body .datatable1 tbody').on('click','.reason-view',function(){
            $('#ViewModal .modal-title').html( 'Reason' );
            $('#ViewModal .modal-body').html( $(this).html() );
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('.rent-price').html();

            if(!html)
                html=$(this).children('.reason-view').html();

            if(!html)
                html=$(this).children('.is-link').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                // window.location.href ='/admin/property/view/'+id
                if(id!=undefined) {
                    window.open('/admin/lead/view/' + id);
                }
            }

        });

        $('#LeadAssignTo').change(function(){
            var val = [];
            var selected = new Array();

            $("form table tbody input[type=checkbox]:checked").each(function () {
                selected.push(this.value);
            });

            if (selected.length > 0) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Cancel',
                    confirmButtonText: 'Yes',
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                }).then(function (result) {
                    if (result.value) {
                        $.ajax({
                            url: "{{route('lead-action')}}",
                            type: "POST",
                            data: {
                                _token: $('form input[name="_token"]').val(),
                                lead: selected,
                                AssignTo: $('#LeadAssignTo').val()
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
                                $('form table input[type=checkbox]:checked').prop('checked', false);
                                $('.assign-to-list').addClass('d-none');
                                $('#LeadAssignTo').val('').change();
                                if(response.r=='0') {
                                    toast_('',response.msg,$timeOut=20000,$closeButton=true);
                                }
                            }, error: function (data) {
                                var errors = data.responseJSON;
                                console.log(errors);
                            }
                        });
                    }
                });
            }
        });

        /*$('body').on('click','.action .ajax-delete', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('model');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: model,
                        type: "POST",
                        data: {
                            _token: $('form input[name="_token"]').val(),
                            Delete:id
                        },
                        success: function (response) {
                            if(response.r=='0') {
                                toast_('',response.msg,$timeOut=20000,$closeButton=true);
                            }else{
                                table.ajax.reload(null, false);
                            }
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            })
        });*/


        $('body').on('click','.action .ajax-delete', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('model');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: model,
                        type: "POST",
                        data: {
                            _token: $('form input[name="_token"]').val(),
                            Delete:id
                        },
                        success: function (response) {
                            if(response.r=='0') {
                                toast_('',response.msg,$timeOut=20000,$closeButton=true);
                            }else if(response.r=='-1'){
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: response.msg,
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    cancelButtonText: 'Cancel',
                                    confirmButtonText:'Yes, delete it!',
                                    confirmButtonClass: 'btn btn-danger',
                                    cancelButtonClass: 'btn btn-primary ml-1',
                                    buttonsStyling: false,
                                }).then(function (result) {
                                    if (result.value) {
                                        $.ajax({
                                            url: model,
                                            type: "POST",
                                            data: {
                                                _token: $('form input[name="_token"]').val(),
                                                Delete:id,
                                                activities:'delete'
                                            },
                                            success: function (response) {
                                                table.ajax.reload(null, false);
                                            }, error: function (data) {
                                                var errors = data.responseJSON;
                                                console.log(errors);
                                            }
                                        });
                                    }
                                })
                            }else{
                                table.ajax.reload(null, false);
                            }
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            })
        });
    </script>
    <script>
        $('#emirate').change(function () {
            let val=$(this).val();
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
                }
            });
        }
        $(document).ready(function() {
            $('#master_project').select2({
                placeholder: "Select Mater Project",
                allowClear: true,
                width: '100%', // Add this line
                language: {
                    noResults: function() {
                        return "Select Emirate First";
                    }
                }
            }).on('select2:open', function() {
                // Check if no results message exists and add class
                setTimeout(function() {
                    var $noResults = $('.select2-results__message');
                    if($noResults.length > 0) {
                        $noResults.addClass('no-data-found');
                    }
                }, 100);
            });
        });
    </script>

@endsection
