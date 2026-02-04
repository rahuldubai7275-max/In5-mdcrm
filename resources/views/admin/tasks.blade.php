
@extends('layouts/contentLayoutMaster')

@section('title', 'Task Management')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
    $task_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','all_task')->first();
    $task_access_admin=[];
    if($task_access_setting)
        $task_access_admin=\App\Models\SettingAdmin::where('setting_id',$task_access_setting->id)->where('admin_id',$adminAuth->id)->first();
@endphp
@section('content')
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
                    <form>
                        <div class="row mt-1">
                            @php
                                $disabled='disabled';
                                if($task_access_admin || $adminAuth->super==1)
                                    $disabled='';
                            @endphp
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
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-from">From</label>
                                    <select class="form-control select2" id="select-from">
                                        <option value="">Select</option>
                                        @php
                                            $froms=\Illuminate\Support\Facades\DB::select('SELECT DISTINCT admins.* FROM admins,tasks WHERE admins.id=tasks.admin_id and assign_to='.$adminAuth->id)
                                        @endphp
                                        @foreach($froms as $from)
                                            <option value="{{ $from->id }}">{{ $from->firstname.' '.$from->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-task">Task</label>
                                    <select class="form-control select2" id="select-task">
                                        <option value="">Select</option>
                                        @php
                                            $titleTasks=\Illuminate\Support\Facades\DB::select('SELECT DISTINCT task_title.* FROM task_title,tasks WHERE task_title.id=tasks.task_title_id and assign_to='.$adminAuth->id)
                                        @endphp
                                        @foreach($titleTasks as $tTask)
                                            <option value="{{ $tTask->id }}">{{ $tTask->title }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-status">Status</label>
                                    <select class="form-control" id="select-status">
                                        <option value="">Select</option>
                                        <option value="1">Done</option>
                                        <option value="2">Cancelled</option>
                                        <option value="3">Upcoming</option>
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
                                <div class="form-group form-label-group">
                                    <label for="to-date">Date</label>
                                    <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group validate">
                                    <label for="from-date">Added Date</label>
                                    <input type="text" id="create-from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="to-date">Added Date</label>
                                    <input type="text" id="create-to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
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

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tasks</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights task-create-btn" href="#modalCreate" data-toggle="modal">Add</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>Task</th>
                        <th>From</th>
                        <th>Assign To</th>
                        <th>Status</th>
                        <th>Date-Time</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('admin/task-add-modal')

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="/js/scripts/select2.multi-checkboxes.js"></script>
    <script>
        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
        })
    </script>
    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 4, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('tasks.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    // data.contact = 'contacts';
                    data._token='{{csrf_token()}}';
                    data.assign_to = $('#select-admin').val();
                    data.admin = $('#select-from').val();
                    data.task = $('#select-task').val();
                    data.status = $('#select-status').val();
                    data.from_date = $('#from-date').val();
                    data.to_date = $('#to-date').val();
                    data.create_from_date = $('#create-from-date').val();
                    data.create_to_date = $('#create-to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 6 ]}],
            'columns': [
                {data: 'task_title_id'},
                {data: 'admin_id'},
                {data: 'assign_to'},
                {data: 'status'},
                {data: 'date_at'},
                {data: 'created_at'},
                {data: 'action'},
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('.datatable1').on('click','.survey-detail',function(){
            let survey=$(this).parent().data('id');

            $.ajax({
                url:"{{ route('surveys.details') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    survey:survey
                },
                success:function (response) {
                    $('#surveyDetails .modal-body').html(response);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('table').on('click','.edit-record',function () {
            $("#_id").val($(this).parent().data('id'));

            $('#title').val( $(this).parent().data('title') );
            $('#_assign_to').val( $(this).parent().data('cm') ).change();
            $('#assign_to').val( $(this).parent().data('cm') ).change();
            $('#assign_to').attr('disabled','disabled');
            $('#description').val( $(this).parent().data('desc') );
            $('#DateAt').val( $(this).parent().data('date') ).change();
            $('#TimeAt').val( $(this).parent().data('time') ).change();


            $("#modalCreate .modal-title").html('Edit Task');
            $("#modalCreate button:submit").html('Edit');
            $("#modalCreate form").attr('action','{{ route('task.edit') }}');
        });

        $('.task-create-btn').click(function () {
            $("#_id").val('');

            $('#title').val( '' );
            $('#assign_to').val( '' ).change();
            $('#assign_to').removeAttr('disabled');
            $('#description').val( '' );
            $('#DateAt').val( '' ).change();
            $('#TimeAt').val( '' ).change();

            $("#modalCreate .modal-title").html('Add Task');
            $("#modalCreate button:submit").html('Add');
            $("#modalCreate form").attr('action','{{ route('task.add') }}');
        });

        $(".checkbox").click(function(){
            let target=$(this).data('target')
            if($(".checkbox").is(':checked') ){
                $(target+" > option").prop("selected","selected");
                $(target).trigger("change");
            }else{
                $(target+" > option").prop("selected","")
                $(target).trigger("change");
            }
        });
    </script>

    <script>

        (function(window, document, $) {
            'use strict';

            $('.limit-format-picker').pickadate({
                format: 'yyyy-mm-dd',
                min:true
            });

            /*******    Pick-a-time Picker  *****/
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' +dd ;
            let date=today;

            var $input= $('.limit-timepicker').pickatime({
                format: 'HH:i',
                interval:10,    });

            var picker = $input.pickatime('picker');

            $('#DateAt').change(function(){
                date=$(this).val();
                $('#TimeAt').val('');
                if(today==date){
                    picker.set('min', true);
                }else{
                    picker.set('min', false);
                }
            });
        })(window, document, jQuery);

        $('body').on('click','td .task-desc',function() {
            $('#ViewModal .modal-title').html( $(this).data('title') );
            $('#ViewModal .modal-body').html( $(this).data('desc') );
        });


        @if($disabled!='')
        $('#select-admin').val('{{$adminAuth->id}}').attr('disabled','disabled').change();
        @endif
    </script>
@endsection
