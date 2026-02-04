
@extends('layouts/contentLayoutMaster')

@section('title', 'Task')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Create Task</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights task-create-btn" href="#titleCreate" data-toggle="modal">Add</a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped datatable1 truncate-table">
                        <thead>
                        <tr>
                            <th>Task</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $TaskTitles=\App\Models\TaskTitle::where('company_id',$adminAuth->company_id)->get();
                        @endphp
                            @foreach($TaskTitles as $row)

                                @php
                                    $Task=\App\Models\Task::where('task_title_id',$row->id)->count();
                                    $type=Task_Type[$row->type];
                                @endphp
                            <tr>
                                <td><span class="task-title" data-target="#ViewModal" data-toggle="modal">{{$row->title}}</span></td>
                                <td><span class="task-type badge badge-pill badge-light-dark w-100" style="background-color: {{$type[1]}}">{{$type[0]}}</span></td>
                                <td>
                                    <div class="custom-control custom-switch switch-lg custom-switch-success">
                                        <input type="checkbox" class="custom-control-input" data-sq="{{$row->id}}" id="customSwitch{{$row->id}}" {{($row->status==1) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch{{$row->id}}">
                                            <span class="switch-text-left">Enable</span>
                                            <span class="switch-text-right">Disable</span>
                                        </label>
                                    </div>
                                </td>
                                <td>{{\Helper::changeDatetimeFormat($row->created_at)}}</td>
                                <td>
                                    <div class="action d-flex font-medium-3" data-id="{{$row->id}}" data-type="{{$row->type}}"  data-subject="{{ $row->subject }}" data-model="{{route('task-title.delete')}}">
                                        <a href="#titleCreate" data-toggle="modal" class="edit-record" data-answer="{{ ($Task==0) ? '1' : '0'}}"><i class="users-edit-icon feather icon-edit-1 font-medium-3 mr-50"></i></a>
                                        @if($Task==0) <a href="javascript:void(0);" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a> @endif
                                    </div>
                                </td>
                            </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="titleCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('task-title.add') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Select</option>
                                    @foreach(Task_Type as $key => $value)
                                        <option value="{{$key}}">{{$value[0]}}</option>
                                    @endforeach
                                </select>
                                <label for="title">Type <span>*</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="title">Create <span>*</span></label>
                                <textarea class="form-control" id="title" name="title" placeholder="Title" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_id" id="_id">
                    <button type="submit" name="submit" id="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $('.datatable1').DataTable({ "order": [[ 2, "desc" ]] });
        $('table').on('change','input:checkbox',function () {
            let question=$(this).data('sq');
            let status=2;
            if ($(this).prop('checked')==true){
                status=1;
            }

            $.ajax({
                url:"{{ route('task-title.status') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    question:question,
                    status:status,
                },
                success:function (response) {
                    $('#mostModal .modal-body').html(response);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('table').on('click','.edit-record',function () {
            $("#_id").val($(this).parent().data('id'));

            $("#type").val($(this).parent().data('type'));
            $("#title").val($(this).parents('tr').children('td:first-child').children('span').html());
            $("#titleCreate .modal-title").html('Edit Task');
            $("#titleCreate button:submit").html('Edit');
            $("#titleCreate form").attr('action','{{ route('task-title.edit') }}');
        });

        $('.task-create-btn').click(function () {
            $("#_id").val('');

            $("#title").val('').removeAttr('disabled');
            $("#type").val('').removeAttr('disabled');

            $("#title").val('');
            $("#type").val('');

            $("#titleCreate .modal-title").html('Add');
            $("#titleCreate button:submit").html('Add');
            $("#titleCreate form").attr('action','{{ route('task-title.add') }}');
        });

        $('body').on('click','.task-title',function() {
            $('#ViewModal .modal-title').html( 'Title' );
            $('#ViewModal .modal-body').html( $(this).html() );
        });
    </script>
@endsection
