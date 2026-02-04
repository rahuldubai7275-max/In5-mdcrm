
@extends('layouts/contentLayoutMaster')

@section('title', 'Targets')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection

@section('content')
    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Targets (Monthly)</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a href="#ModalTaregt" data-toggle="modal" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light btn-add-target">Add Target</a></li>
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
                            <th>CM</th>
                            <th>phone calls</th>
                            <th>viewings</th>
                            <th>MA</th>
                            <th>Listings</th>
                            <th>Commission</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Change Password -->
    <div class="modal fade" id="ModalTaregt" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('target.add') }}" id="record-form" class="modal-content" novalidate>
                {!! csrf_field() !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Target</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <fieldset class="form-group form-label-group">
                                <label for="admin">Client Manager</label>
                                <select class="form-control select2" id="admin" name="admin">
                                    <option value="">Select</option>
                                    @php
                                    $Agents=\Helper::getCM_DropDown_list('1');
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-label-group">
                                <input type="text" id="num_calls" class="form-control number-format" placeholder="Number of Phone Calls" name="num_calls" required="required">
                                <label for="num_calls">Number Of phone calls</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-label-group">
                                <input type="text" id="num_viewing" class="form-control number-format" placeholder="Number of Viewings" name="num_viewing" required="required">
                                <label for="num_viewing">Number of Viewings</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-label-group">
                                <input type="text" id="num_ma" class="form-control number-format" placeholder="Number of MA" name="num_ma" required="required">
                                <label for="num_ma">Number of MA</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-label-group">
                                <input type="text" id="num_listing" class="form-control number-format" placeholder="Number of Listings" name="num_listing" required="required">
                                <label for="num_listing">Number of Listings</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-label-group">
                                <input type="text" id="commission" class="form-control number-format" placeholder="Commission (AED)" name="commission" required="required">
                                <label for="commission">Commission (AED)</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 record-action-box">
                        <input type="hidden" name="period" value="{{request('period')}}">
                        <button type="submit" class="btn btn-primary mr-1 mb-1 btn-create float-right" value="submit">Add</button>
                        <div class="update-btn-box d-none float-right">
                            <button type="reset" class="btn btn-primary mr-1 mb-1">Cancel</button>
                            <input type="hidden" name="update">
                            <button type="submit" class="btn btn-primary mr-1 mb-1" value="submit">Update</button>
                        </div>
                    </div>
                </div>
            </form>
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
    {{--<script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
    <script>
        var ActionAdd=$('#record-form').attr('action');
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "order": [[ 0, "asc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('targets.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    data.target = '{{request('period')}}';
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 6 ]}],
            'columns': [
                {data: 'firstname'},
                {data: 'num_calls'},
                {data: 'num_viewing'},
                {data: 'num_ma'},
                {data: 'num_listing'},
                {data: 'commission'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('body').on('click','.edit-record',function () {
            var ActionEdit = $(this).parent().data('edit');
            $('#record-form').attr('action',ActionEdit);

            $('#admin').val($(this).parent().data('admin')).change();
            $('#num_calls').val($(this).parent().data('num_calls'));
            $('#num_viewing').val($(this).parent().data('num_viewing'));
            $('#num_ma').val($(this).parent().data('num_ma'));
            $('#num_listing').val($(this).parent().data('num_listing'));
            $('#commission').val($(this).parent().data('commission'));
            $('#record-form input[name=update]').val($(this).parent().data('id'));


            $('.btn-create').addClass('d-none');
            $('.record-action-box .update-btn-box').removeClass('d-none');
        });
        $('#record-form :reset').click(function () {
            $('#admin').val('').change();
            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });
        $('.btn-add-target').click(function () {
            $('#admin').val('').change();
            $('#admin , #num_calls , #num_viewing , #num_ma, #num_listing , #commission').val('').change();

            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });

    </script>


@endsection
