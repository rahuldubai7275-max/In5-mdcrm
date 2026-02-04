
@extends('layouts/contentLayoutMaster')

@section('title', 'Other Requests')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection
@php
    $hr_access=0;
    $adminAuth=\Auth::guard('admin')->user();
    if($adminAuth->type=='6' || $adminAuth->type=='1') {
        $hr_access = 1;
    }else {
        $SettingAdmin = \App\Models\SettingAdmin::where('company_id', $adminAuth->company_id)->where('title', 'hr_access')->where('admin_id', $adminAuth->id)->first();
        if($SettingAdmin) {
            $hr_access = 1;
        }
    }
@endphp
@section('content')

    @if($hr_access==1)
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
                                    <option value="">Select</option>
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
                                        $requests=\App\Models\HRRequest::orderBy('title','ASC')->get();
                                    @endphp
                                    @foreach($requests as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="select-controller-status">Status</label>
                                <select class="form-control" id="select-status">
                                    <option value="">Select</option>
                                    <option value="0">New</option>
                                    <option value="1">Replied</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label for="from-date">Request Date</label>
                                        <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label for="to-date">Request Date</label>
                                        <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                    </div>
                                </div>
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
            <h5 class="card-title d-none d-md-block">Other Requests</h5>
            <h5 class="d-block d-md-none">Other Requests</h5>
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
                            <th>User</th>
                            <th>Request Type</th>
                            <th>Status</th>
                            <th>Request Date</th>
                            <th>Reply Date</th>
                            <th></th>
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
            <form method="post" action="{{ route('request-hr.add') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Requests</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-12">
                            <div class="form-group form-label-group">
                                <label for="request">Request Type</label>
                                <select class="form-control" name="hr_request" id="hr_request" required>
                                    <option value="">Select</option>
                                    @php
                                        $requests=\App\Models\HRRequest::orderBy('title','ASC')->get();
                                    @endphp
                                    @foreach($requests as $row)
                                        <option value="{{ $row->id }}">{{ $row->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" placeholder="Description"></textarea>
                            </div>
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

    <div class="modal fade text-left" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('request-hr.reply') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reply Requests</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">

                    </div>
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
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        var table=$('.datatable1').DataTable({
            // fixedColumns: {
            //     start: 1
            // },
            // scrollX: true,
            // scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 3 , "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('requests-hr.get.datatable') }}',
                'data': function(data){

                    // Append to data

                    data._token='{{csrf_token()}}';
                    data.admin=$('#select-admin').val();
                    data.request=$('#select-request').val();
                    data.status=$('#select-status').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }

            },
            // aoColumnDefs: [{bSortable: false,aTargets: [  7 ]}],
            'columns': [
                {data: 'firstname'},
                {data: 'hr_request_id'},
                {data: 'status'},
                {data: 'created_at'},
                {data: 'reply_date'},
                {data: 'action'}

            ],
        });
        $('#search').click(function(){
            table.draw();
        });

    </script>

    <script>

        $('#submit').click(function(){
            $('#requestCreate button[name="submit"]').click();
        });

        $('.add-request-btn').click(function(){
            $('#request').val('').change();
            $('#description').val('');
        });
        $('body').on('click','.reply-request',function (){
            let id= $(this).parent().data('id');

            $('#_id').val(id);
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {

                let id=$(this).parent().children('td').children('.action').data('id');

                if(id!=undefined) {
                    $.ajax({
                        url:"{{ route('hr-request.details') }}",
                        type:"POST",
                        data:{
                            _token:$('meta[name="csrf-token"]').attr('content'),
                            request:id
                        },
                        success:function (response) {
                            $('#replyModal .modal-title').html(response.user);
                            $('#replyModal .modal-body').html(response.detail);
                            $('#replyModal').modal('show');
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            }

        });
    </script>

@endsection
