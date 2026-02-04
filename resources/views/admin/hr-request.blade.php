
@extends('layouts/contentLayoutMaster')

@section('title', 'HR Requests')

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
            <h4 class="card-title">HR Requests</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights request-create-btn" href="#requestCreate" data-toggle="modal">Add</a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped datatable1 truncate-table">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $HRRequests=\App\Models\HRRequest::get();
                        @endphp
                            @foreach($HRRequests as $row)
                                @php
                                    $AdminHrRequest=\App\Models\AdminHrRequest::where('hr_request_id',$row->id)->count();
                                @endphp
                            <tr>
                                <td>{{$row->title}}</td>
                                <td>{{\Helper::changeDatetimeFormat($row->created_at)}}</td>
                                <td>
                                    <div class="action d-flex font-medium-3" data-id="{{$row->id}}" data-model="{{route('hr-request.delete')}}">
                                        <a href="#requestCreate" data-toggle="modal" class="edit-record"><i class="users-edit-icon feather icon-edit-1 font-medium-3 mr-50"></i></a>
                                        @if($AdminHrRequest==0) <a href="javascript:void(0);" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a> @endif
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

    <div class="modal fade text-left" id="requestCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('hr-request.add') }}" class="modal-content" novalidate>
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
                                <label for="request">Request</label>
                                <textarea class="form-control" id="title" name="title" placeholder="Request"></textarea>
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
        $('.datatable1').DataTable({ "order": [[ 1, "desc" ]] });

        $('table').on('click','.edit-record',function () {
            $("#_id").val($(this).parent().data('id'));
            $("#title").val($(this).parents('tr').children('td:first-child').html());
            $("#requestCreate .modal-title").html('Edit Survey');
            $("#requestCreate button:submit").html('Edit');
            $("#requestCreate form").attr('action','{{ route('hr-request.edit') }}');
        });
        $('.request-create-btn').click(function () {
            $("#_id").val('');
            $("#request").val('');
            $("#requestCreate .modal-title").html('Add');
            $("#requestCreate button:submit").html('Add');
            $("#requestCreate form").attr('action','{{ route('hr-request.add') }}');
        });

        $('body').on('click','.hr-request',function() {
            $('#ViewModal .modal-title').html( 'Request' );
            $('#ViewModal .modal-body').html( $(this).html() );
        });
    </script>
@endsection
