
@extends('layouts/contentLayoutMaster')

@section('title', 'Project')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
    @php
        $adminAuth = Auth::guard('admin')->user();
    @endphp
    <div class="row">
        @if($adminAuth->type==1)
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Project</h4>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('community.add') }}" class="form error" novalidate>
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group form-label-group">
                                            <label for="MasterProject">Master Project</label>
                                            <select class="custom-select form-control select2" id="master-project" name="MasterProject" required>
                                                <option value="">Select</option>
                                                @foreach($MasterProjects as $MProject)
                                                    <option value="{{ $MProject->id }}">{{ $MProject->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-label-group">
                                            <input type="text" id="name" class="form-control" placeholder="Project" name="name" required="required">
                                            <label for="Name">Project</label>
                                        </div>
                                    </div>
                                    <div class="col-12 record-action-box">
                                        <button type="submit" class="btn btn-primary mb-1 btn-create float-right" value="submit">Add</button>
                                        <div class="update-btn-box float-right d-none">
                                            <input type="hidden" name="update">
                                            <button type="reset" class="btn btn-secondary mr-1 mb-1">Cancel</button>
                                            <button type="submit" class="btn btn-primary mb-1" value="submit">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-{{($adminAuth->type==1) ? '8':'12'}} col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Project</h4>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table zero-configuration table-striped">
                                <thead>
                                <tr>
                                    <th>Master Project</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($Communitys as $row)
                                    <tr>
                                        <td>{{ $row->MasterProject['name'] }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            @if($adminAuth->type==1)
                                            <div data-id="{{ $row->id }}" data-model="{{ route('community.delete') }}" data-edit="{{ route('community.edit') }}"
                                                 data-name="{{ $row->name }}" data-mproject="{{ $row->master_project_id }}" class="action font-medium-2 d-flex">
                                                <a href="javascript:void(0)" class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                                                <a href="javascript:void(0)" class="delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Master Project</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script>
        var ActionAdd=$('#record-form').attr('action');
        $('.edit-record').click(function () {
            var ActionEdit = $(this).parent().data('edit');
            $('#record-form').attr('action',ActionEdit);

            $('#master-project').val($(this).parent().data('mproject')).change();
            $('#name').val($(this).parent().data('name'));
            $('#record-form  input[name=update]').val($(this).parent().data('id'));


            $('.btn-create').addClass('d-none');
            $('.record-action-box .update-btn-box').removeClass('d-none');
        });
        $('#record-form :reset').click(function () {
            $('#master-project').val('').change();
            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });
    </script>
@endsection
