
@extends('layouts/contentLayoutMaster')

@section('title', 'Data Center Files')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data Center Files</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table zero-configuration-n table-striped">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Master Project</th>
                            <th>Added Date</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                        $files=\App\Models\DataCenterFile::orderBy('created_at','DESC')->get();
                        @endphp
                        @foreach($files as $row)
                            @php
                                $masterPorject=\App\Models\MasterProject::find($row->master_project_id);
                            @endphp
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ $masterPorject->name }}</td>
                                <td>{{\Helper::changeDatetimeFormat($row->created_at)}}</td>
                                <td>
                                    <div data-id="{{ $row->id }}" data-model="{{ route('dc-file.delete') }}" class="action font-medium-2 d-flex">
                                        {!! ($row->file_name && $row->file_name) ? '<a href="/storage/'.$row->file_name.'" title="Download"><i class="fa fa-cloud-download"></i></a>' : '' !!}
                                        {{--<a href="javascript:void(0)" class="delete" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>--}}
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

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script>

    </script>
@endsection
