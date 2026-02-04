@extends('layouts/contentLayoutMaster')

@section('title', 'DC Report')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">

<style>
    .avatar {
        cursor: default !important;
    }
</style>
@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection

@php
$adminAuth = Auth::guard('admin')->user();
$call_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=1 '.$filter);

$note_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=4 '.$filter);

$email_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=5 '.$filter);

$reminder_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE `note_subject`=6 '.$filter);
$lead_count=DB::select('SELECT Count(*) as countAll FROM leads WHERE 1 '.(($admins) ? str_replace('admin_id','telesales_id',$filter) : ' AND telesales_id IS NOT NULL'));
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
                <div class="users-list-filter">
                    <form>
                        <div class="row mt-1">
                            @if($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6)
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <fieldset class="form-group form-label-group">
                                        <label for="select-agent">Client Manager</label>
                                        <select class="form-control select2" id="select-admin">
                                            <option value="">Select</option>
                                            @php
                                                $Agents=\Helper::getCM_DropDown_list('0');
                                            @endphp
                                            @foreach($Agents as $agent)
                                                <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            @endif
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-note-subject">Activity Type</label>
                                    <select class="form-control" id="select-note-subject">
                                        <option value="">Select</option>
                                        @foreach(NoteSubject as $key => $value)
                                            @if($key==2 || $key==3)
                                                @continue
                                            @endif
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group validate">
                                    <label for="from-date">Date</label>
                                    <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group rented-until-box">
                                    <label for="to-date">Date</label>
                                    <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0" id="call_count">{{number_format($call_count[0]->countAll)}}</h5>
                        <p>Calls</p>
                    </div>
                    <div class="avatar bg-rgba-pink p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-phone text-pink font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0" id="email_count">{{number_format($email_count[0]->countAll)}}</h5>
                        <p>Emails</p>
                    </div>
                    <div class="avatar bg-rgba-yellow p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-mail text-yellow font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0" id="reminder_count">{{number_format($reminder_count[0]->countAll)}}</h5>
                        <p>Reminders</p>
                    </div>
                    <div class="avatar bg-rgba-blue-dark p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-clock text-blue-dark font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0" id="lead_count">{{number_format($lead_count[0]->countAll)}}</h5>
                        <p class="truncate-text" title="Added To Contact Leads">Assigned</p>
                    </div>
                    <div class="avatar bg-rgba-success p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-magnet text-success font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Activities Report</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped dataex-html5-selectors datatable1 truncate-table">
                        <thead>
                        <tr>
                            <th>Ref</th>
                            @if($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6) <th>CM</th> @endif
                            <th>Activity Type</th>
                            <th>Note</th>
                            <th>Date - Time</th>
                            <th>Added Date</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Dashboard Ecommerce ends --}}
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->

<script>
    $('#admin').val('{{ $admins }}').trigger('change');
</script>

<script>
    var table=$('.datatable1').DataTable({
        // dom: 'Bflrtip',
        // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
        'processing': true,
        'serverSide': true,
        'searching': false,
        'serverMethod': 'post',
         "order": [[ {{ ($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6) ?  5 : 4 }}, "desc" ]],
        "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
        'ajax': {
            'type': 'post',
            'url': '{{ route('dc-activities.get.datatable') }}',
            'data': function(data){
                // Read values
                // var UserType = $('#MemberType').val();
                // var Country = $('#Country').val();

                // Append to data
                // data.contact = 'contacts';
                data._token='{{csrf_token()}}';
                @if($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6)data.agent = '{{$adminAuth->id}}'; @endif
                data.type = $('#select-type').val();
                data.note_subject = $('#select-note-subject').val();
                data.admin = $('#select-admin').val();
                data.from_date = $('#from-date').val();
                data.to_date = $('#to-date').val();
            }

        },
        // aoColumnDefs: [{bSortable: false,aTargets: [ 2,4,5 ]}],
        'columns': [
            {data: 'data_center_id'},
                @if($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6) {data: 'firstname'}, @endif
            {data: 'note_subject'},
            {data: 'note'},
            {data: 'date_at'},
            {data: 'created_at'},
        ],
    });
    $('#search').click(function(){
        $.ajax({
            url: "{{route('dc-report.filter')}}",
            type: "POST",
            data: {
                _token: $('form input[name="_token"]').val(),
                note_subject: $('#select-note-subject').val(),
                admin: $('#select-admin').val(),
                from_date: $('#from-date').val(),
                to_date: $('#to-date').val(),
            },
            success: function (response) {
                $('#call_count').html(response.call_count);
                $('#email_count').html(response.email_count);
                $('#reminder_count').html(response.reminder_count);
                $('#lead_count').html(response.lead_count);
            }, error: function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
        table.draw();
    });

    $('body').on('click','td .note',function() {
        $('#ViewModal .modal-title').html( $(this).data('title') );
        $('#ViewModal .modal-body').html( $(this).data('desc') );
    });
</script>

@endsection
