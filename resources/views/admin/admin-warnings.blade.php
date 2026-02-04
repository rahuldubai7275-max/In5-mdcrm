
@extends('layouts/contentLayoutMaster')

@section('title', 'Warning Letters')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">

@endsection
@php
    $adminAuth=\Auth::guard('admin')->user();
    $hr_access=\App\Models\SettingAdmin::where('setting_id',16)->where('admin_id',$adminAuth->id)->first();
@endphp
@section('content')

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
                            <label for="select-warning">Warning Type</label>
                            <select class="form-control" name="select-warning" id="select-warning">
                                <option value="">Select</option>
                                @php
                                    $warningType=\App\Models\WarningType::get();
                                @endphp
                                @foreach($warningType as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group validate">
                            <label for="from-date">Added Date</label>
                            <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group rented-until-box">
                            <label for="to-date">Added Date</label>
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

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Warning Letters</h5>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    @if($adminAuth->type==1 || $adminAuth->type==6 || $hr_access) <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights add-request-btn" href="javascript:void(0);" data-toggle="modal" data-target="#warningCreate">New</a></li> @endif
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table truncate-table datatable1 table-striped order-column dataTable">
                        <thead>
                        <tr>
                            <th>CM</th>
                            <th>Warning Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Added Date</th>
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
    @if($adminAuth->type==1 || $adminAuth->type==6 || $hr_access)
    <div class="modal fade text-left" id="warningCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('warning.add') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">New Warning Letter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-12">
                            <div class="form-group form-label-group">
                                <label for="warning">Warning Type</label>
                                <select class="form-control" name="warning" id="warning" required>
                                    <option value="">Select</option>
                                    @php
                                        $warningType=\App\Models\WarningType::get();
                                    @endphp
                                    @foreach($warningType as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <fieldset class="form-group form-label-group">
                                <label for="select-admin">User</label>
                                <select class="form-control select2" id="admin" name="admin">
                                    <option value="">Select</option>
                                    @php
                                        $Agents=App\Models\Admin::where('main_number','!=','+971502116655')->where('status','1')->orderBy('firstname','ASC')->get();
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-label-group">
                                <label for="select-reason">Reason</label>
                                <select class="form-control" name="select-reason" id="select-reason" required>
                                    <option value="">Select</option>
                                    <option value="Absenteeism">Absenteeism</option>
                                    <option value="Dress code violation">Dress code violation</option>
                                    <option value="Misconduct">Misconduct</option>
                                    <option value="Negligence">Negligence</option>
                                    <option value="Policy violations">Policy violations</option>
                                    <option value="Poor performance">Poor performance</option>
                                    <option value="Sexual harassment">Sexual harassment</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 d-none">
                            <div class="form-group form-label-group">
                                <label for="reason">Other Reason</label>
                                <input type="text" id="reason" name="reason" class="form-control" placeholder="Other Reason" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="description">Notice</label>
                                <textarea class="form-control" rows="10" id="description" name="description" placeholder="Notice"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    <div class="modal fade text-left" id="warningDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
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
            fixedColumns: {
                start: 2
            },
            scrollX: true,
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 4, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('warnings.get.datatable') }}',
                'data': function(data){
                    data.admin=$('#select-admin').val();
                    data.warning=$('#select-warning').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 5]}],
            'columns': [
                {data: 'firstname'},
                {data: 'name'},
                {data: 'reason'},
                {data: 'status'},
                {data: 'created_at'},
                {data: 'Action'}

            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('#select-reason').change(function(){
            let val=$(this).val();
            $('#reason').val(val).parent().parent().addClass('d-none');
            if(val==='Other'){
                $('#reason').val('').parent().parent().removeClass('d-none');
            }
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if (!html) {
                let id = $(this).parent().children('td').children('.action').data('id');

                if (id != undefined) {
                    $.ajax({
                        url: "{{ route('warning.details') }}",
                        type: "POST",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            warning: id
                        },
                        success: function (response) {
                            $('#warningDetail .modal-body').html(response);
                            $('#warningDetail').modal('show');
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
