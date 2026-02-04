
@extends('layouts/contentLayoutMaster')

@section('title', 'Leave Requests')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
    $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();

    $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();
    $approver_access=0;
    if($request_main_admin_id){
        if(in_array($adminAuth->id, $request_main_admin_id))
            $approver_access=1;
    }else{
        if($adminAuth->super==1)
            $approver_access=1;
    }

    $today = date('Y-m-d');
    $newYear=date('Y').'-'.date('m-d',strtotime($adminAuth->date_joined));
    if($newYear>$today){
        $newYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
    }

    $takenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$newYear."' AND admin_id=".$adminAuth->id);
    $halfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$newYear."' AND admin_id=".$adminAuth->id);
    $carryForwardTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=9 AND datetime_from >= '".$newYear."' AND admin_id=".$adminAuth->id);

    $useAnnalLeave=$takenDays[0]->allDays+($halfTakenDays[0]->allDays/2);

    $date_two = \Carbon\Carbon::parse($today);
    $years = $date_two->diffInYears($adminAuth->date_joined);
    $carryForwardDays=0;
    if($years>0) {
        //$previousYear=date('Y').'-'.date('m-d',strtotime($adminAuth->date_joined));
        $previousYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
        $beforeYearTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$adminAuth->id);
        $beforeYearHalfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$adminAuth->id);

        $carryForwardDays=$adminAuth->leave_days-(($beforeYearTakenDays[0]->allDays+($beforeYearHalfTakenDays[0]->allDays/2))+$carryForwardTakenDays[0]->allDays);
    }
@endphp
@section('content')
    @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)))
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filters</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="row mt-1">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="select-admin">Client Manager</label>
                            <select class="form-control select2" id="select-admin">
                                <option value="0">Select</option>
                                @php
                                    $Agents=App\Models\Admin::where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                @endphp
                                @foreach($Agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="select-request">Request Type</label>
                            <select class="form-control" id="select-request">
                                <option value="">Select</option>
                                @php
                                    $requests=\App\Models\Request::orderBy('title','ASC')->get();
                                @endphp
                                @foreach($requests as $row)
                                    <option value="{{ $row->id }}">{{ $row->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($approver_access==1)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="select-controller">Request Controller</label>
                            <select class="form-control" id="select-controller">
                                <option value="">Select</option>
                                @php
                                    $request_approver_admin=\App\Models\SettingAdmin::where('setting_id',17)->get();
                                @endphp
                                @foreach($request_approver_admin as $row)
                                    @php
                                        $admin=\App\Models\Admin::find($row->admin_id);
                                    @endphp
                                    <option value="{{ $admin->id }}">{{ $admin->firstname.' '.$admin->lastname }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="select-controller-status">Controller Decision</label>
                            <select class="form-control" id="select-controller-status">
                                <option value="">Select</option>
                                <option value="0">New</option>
                                <option value="1">Accepted</option>
                                <option value="2">Rejected</option>
                            </select>
                        </div>
                    </div>
                    @if($approver_access==1)
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="select-manager">Manager</label>
                                <select class="form-control" id="select-manager">
                                    <option value="0">Select</option>
                                    @php
                                        $Agents=App\Models\Admin::where('main_number','!=','+971502116655')->where('type','1')->orderBy('firstname','ASC')->get();
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="select-manager-status">Manager Decision</label>
                            <select class="form-control" id="select-manager-status">
                                <option value="">Select</option>
                                <option value="0">New</option>
                                <option value="1">Accepted</option>
                                <option value="2">Rejected</option>
                                <option value="3">Cancellation Requests</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group validate">
                            <label for="from-date">Request Date</label>
                            <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group rented-until-box">
                            <label for="to-date">Request Date</label>
                            <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h5 class="card-title d-none d-md-block">Leave Requests</h5>
            <h5 class="d-block d-md-none">Leave Requests</h5>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights add-request-btn" href="javascript:void(0);" data-toggle="modal" data-target="#requestCreate">New Request</a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table truncate-table datatable1 table-striped order-column dataTable">
                        <thead>
                        <tr>
                            @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)))<th>User</th>@endif
                            <th>Request Type</th>
                            <th>From Date</th>
                            <th>To date</th>
                            <th>Number of Days</th>
                            <th>Resume date</th>
                            <th>Status</th>
                                @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)))<th>Decided By</th>@endif
                            <th>Request Date</th>
                            <th>Cancellation Request</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="requestCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('request.add') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Leave Requests</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-8">
                            <div class="form-group form-label-group">
                                <label for="request">Request Type</label>
                                <select class="form-control" name="request" id="request" required>
                                    <option value="">Select</option>
                                    @php
                                        $requests=\App\Models\Request::orderBy('title','ASC')->get();
                                    @endphp
                                    @foreach($requests as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-label-group">
                                <label for="number_days">Number of Days</label>
                                <select class="form-control" name="number_days" id="number_days">
                                    <option value="">Select</option>
                                    @for($i=1;$i<=30;$i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group form-label-group">
                                <label for="datetime_from">From</label>
                                <input type="text" class="form-control" autocomplete="off" id="datetime_from" name="datetime_from" placeholder="Date" required>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group form-label-group">
                                <label for="datetime_to">To</label>
                                <input type="text" class="form-control" autocomplete="off" id="datetime_to" name="datetime_to" placeholder="Date">
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group form-label-group">
                                <label for="resumption_date">Resume Date</label>
                                <input type="text" class="form-control fformat-picker" autocomplete="off" id="resumption_date" name="resumption_date" placeholder="Date">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Description"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <fieldset class="form-group mb-0">
                                <label for="document-file">File</label>
                                <div class="d-flex">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input document-upload" data-this="document-file" id="document-file"
                                               data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".document-progress-bar" data-input="#Document">
                                        <label class="custom-file-label" for="document-file">Choose file</label>
                                    </div>
                                    <!--<div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="document"><i class="fa fa-download"></i></a></div>-->
                                </div>
                                <input type="hidden" id="Document" name="Document"  value="">
                                <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                    <div class="progress-bar bg-teal progress-bar-striped document-progress-bar" role="progressbar"
                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="submit" class="btn btn-primary">Submit</button>
                    <button type="submit" name="submit" class="d-none"></button>
                </div>
            </form>
        </div>
    </div>

{{--    @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)))--}}
    <div class="modal fade text-left" id="requestDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <form method="post" action="{{ route('request.confirm') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header d-block">
                    <div class="modal-title">Details</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="transform: translate(8px, -25px);">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </form>
        </div>
    </div>
{{--    @endif--}}
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        var table=$('.datatable1').DataTable({
            fixedColumns: {
                start: 1
            },
            scrollX: true,
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ @if($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)) 2 @else 6 @endif, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('requests.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    @if(($approver_access!=1 && !in_array($adminAuth->id, $request_approver_admin_id))) data.profile = {{$adminAuth->id}}; @endif

                    data.admin=$('#select-admin').val();
                    data.request=$('#select-request').val();
                    data.controller=$('#select-controller').val();
                    data.controller_status=$('#select-controller-status').val();
                    data.manager=$('#select-manager').val();
                    data.manager_status=$('#select-manager-status').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ @if(($approver_access!=1 && !in_array($adminAuth->id, $request_approver_admin_id))) 7 @else 7,9 @endif ]}],
            'columns': [
                @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id))){data: 'admin_id'},@endif
                {data: 'request_id'},
                {data: 'datetime_from'},
                {data: 'datetime_to'},
                {data: 'number_days'},
                {data: 'resumption_date'},
                {data: 'manager_status'},
                @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id))){data: 'manager_admin'},@endif
                {data: 'created_at'},
                {data: 'action'}

            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        let useAnnalLeave={{$useAnnalLeave}};
        let annalLeave={{$adminAuth->leave_days}};
        let carryForwardLeave={{$carryForwardDays}};

        $('#submit').click(function(){
            let request=$('#request').val();
            let number_days=parseInt($('#number_days').val());
            let error=0
            if((request=='7' || request=='8') &&  (useAnnalLeave+number_days)>annalLeave){
                toast_('','the number of days is more than the allowed limit.',$timeOut=20000,$closeButton=true);
                error=1;
            }
            if(request=='9' && carryForwardLeave<number_days){
                toast_('','the number of days is more than the allowed limit.',$timeOut=20000,$closeButton=true);
                error=1;
            }
            if($('#number_days').val()=='' && request!=8){
                $('#number_days').parent().addClass('error');
                error=1;
            }

            if(error==0){
                $('#requestCreate button[name="submit"]').click();
            }
        });
        $('#request').change(function(){
            let id=$(this).val();
            $('#number_days , #datetime_to , #resumption_date').removeAttr('disabled');
            if(id==8){
                $('#number_days , #datetime_to , #resumption_date').val('').attr('disabled','disabled');
            }
        });
        $('#select-controller-status').change(function(){
            let val=$(this).val();
            $('#select-manager-status').removeAttr('disabled');
            if(val!=''){
                $('#select-manager-status').val('').attr('disabled','disabled');
            }
        });
        $('#select-manager-status').change(function(){
            let val=$(this).val();
            $('#select-controller-status').removeAttr('disabled');
            if(val!=''){
                $('#select-controller-status').val('').attr('disabled','disabled');
            }
        });
    </script>
{{--    @if(($approver_access==1 || in_array($adminAuth->id, $request_approver_admin_id)))--}}
    <script>
        $('.modal-body').on('click','.btn-submit',function(){
            let status=$(this).val();
            $('.modal-body #request-status').val(status);
            $('#submit-status').click();
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {

                let id=$(this).parent().children('td').children('.action').data('id');

                if(id!=undefined) {
                    $.ajax({
                        url:"{{ route('request.details') }}",
                        type:"POST",
                        data:{
                            _token:$('meta[name="csrf-token"]').attr('content'),
                            request:id
                        },
                        success:function (response) {
                            $('#requestDetail .modal-title').html(response.user);
                            $('#requestDetail .modal-body').html(response.detail);
                            $('#requestDetail').modal('show');
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            }
        });


        var to, from, resume;

        resume=$('#resumption_date').persianDatepicker({
            initialValue: false,
            format: 'YYYY-MM-DD',
            // altFormat: 'YYYY-MM-DD',
            minDate: '{{date('Y-m-d')}}',
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
            }
        });

        to=$('#datetime_to').persianDatepicker({
            initialValue: false,
            format: 'YYYY-MM-DD',
            // altFormat: 'YYYY-MM-DD',
            minDate: '{{date('Y-m-d')}}',
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
            onSelect: function (unix) {
                to.touched = true;
                if (from && from.options && from.options.maxDate != unix) {
                    var cachedValue = from.getState().selected.unixDate;
                    from.options = {maxDate: unix};
                    if (from.touched) {
                        from.setDate(cachedValue);
                    }
                }
                if (resume && resume.options && resume.options.minDate != unix) {
                    var cachedValue = resume.getState().selected.unixDate;
                    resume.options = {minDate: unix};
                    if (resume.touched) {
                        resume.setDate(cachedValue);
                    }
                }
            }
        });

        from=$('#datetime_from').persianDatepicker({
            initialValue: false,
            format: 'YYYY-MM-DD',
            minDate: '{{date('Y-m-d')}}',
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
            onSelect: function (unix) {
                from.touched = true;
                if (to && to.options && to.options.minDate != unix) {
                    var cachedValue = to.getState().selected.unixDate;
                    to.options = {minDate: unix};
                    if (to.touched) {
                        to.setDate(cachedValue);
                    }
                }
            }
        });

        $('.add-request-btn').click(function(){
            $('#request').val('').change();
            $('#number_days').val('');
            $('#datetime_from').val('').change();
            $('#datetime_to').val('').change();
            $('#resumption_date').val('').change();
            $('#description').val('');
            $('#Document').val('');

            to.options = {minDate: '{{date('Y-m-d')}}'};
            from.options = {maxDate: ''};
        });

        $('body').on('click','.action .cancel-action', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('model-cancel-action');
            var status=$(this).data('status');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You want to disable!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {

                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="_id">');
                    $('.delete-form-box form').append('<input type="hidden" value="'+status+'" name="CancelStatus">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input:submit').click();
                }
            })
        });

        $('body').on('click','.action .lr-delete', function () {
            var id=$(this).parent().data('id');
            var model='{{route('request.delete')}}';
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
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="Delete">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input:submit').click();
                }
            })
        });

        $('body').on('click','.action .lr-cancel', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('model');
            Swal.fire({
                title: 'Are you sure?',
                text: "Your leave request will be cancelled!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {

                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="disabled">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input:submit').click();
                }
            })
        });
    </script>
{{--    @endif--}}
@endsection
