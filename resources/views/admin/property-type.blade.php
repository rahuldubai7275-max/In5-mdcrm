
@extends('layouts/contentLayoutMaster')

@section('title', 'DataTables')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add Property Type</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('property-type.add') }}" class="form error" novalidate>
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group form-label-group">
                                            <label>Residential/Commercial <span>*</span></label>
                                            <select class="custom-select form-control" id="type" name="type" required>
                                                <option value="">Select</option>
                                                @foreach(PropertyType as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" id="name" class="form-control" placeholder="Property Type" name="name" required="required">
                                            <label for="Name">Property Type <span>*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-12 record-action-box">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1 btn-create" value="submit">Add</button>
                                        <div class="update-btn-box d-none">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1" name="update" value="submit">Update</button>
                                            <button type="reset" class="btn btn-primary mr-1 mb-1">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Property Type</h4>
                    <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
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
                                    <th>Residential/Commercial</th>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($PropertyTypes as $row)
                                    <tr>
                                        <td>{{ ($row->type) ? PropertyType[$row->type] : '' }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            <div data-id="{{ $row->id }}" data-model="{{ route('property-type.delete') }}" data-edit="{{ route('property-type.edit') }}"
                                                 data-type="{{ $row->type }}" data-name="{{ $row->name }}" class="action font-medium-2 d-flex">
                                                <a href="javascript:void(0)" class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                                                <a href="javascript:void(0)" class="delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
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

            $('#type').val($(this).parent().data('type'));
            $('#name').val($(this).parent().data('name'));
            $('#record-form button[name=update]').val($(this).parent().data('id'));


            $('.btn-create').addClass('d-none');
            $('.record-action-box .update-btn-box').removeClass('d-none');
        });
        $('#record-form :reset').click(function () {
            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });
    </script>
@endsection
