
@extends('layouts/contentLayoutMaster')

@section('title', 'Data Center')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection
@php
    $adminAuth=\Auth::guard('admin')->user();
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
                    <form action="{{route('data-export')}}">
                        <div class="row">

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="master-project">Master Project</label>
                                    <select class="form-control master-project select2" id="master-project" name="master_project_id">
                                        <option value="">Select</option>
                                        @php
                                            $masterProjects=DB::select("SELECT DISTINCT master_project_id FROM `data_center`  WHERE agent_assign=".$adminAuth->id."
                                                                        UNION
                                                                        SELECT master_project_id FROM `data_center_access`  WHERE admin_id=".$adminAuth->id);
                                                //$masterProjects=\App\Models\DataCenterAccess::select('master_project_id')->where('admin_id',$adminAuth->id)->distinct()->get();
                                        @endphp
                                        @foreach($masterProjects as $row)
                                            @php
                                            $master_project=\App\Models\MasterProject::find($row->master_project_id);
                                            @endphp
                                            <option value="{{$master_project->id}}">{{$master_project->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="unmatched">Matched</label>
                                    <select class="form-control" id="unmatched" name="unmatched">
                                        <option value="">Select</option>
                                        <option value="1">Matched</option>
                                        <option value="2">Unmatched</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="community">Project</label>
                                    <select class="form-control community select2-checkbox" multiple id="community" name="community">

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="bedroom">Bedrooms</label>
                                    <select class="form-control" id="bedroom" name="bedroom">
                                        <option value="">Select</option>
                                        @for($i=1;$i<=12;$i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select</option>
                                        @foreach(DataCenterStatus as $key=>$value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                        @if($adminAuth->type!=7)
                                        <option value="added_to_property">Added To Property</option>
                                        <option value="added_to_contact">Added To Contact</option>
                                        @endif
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="villa-unit-no" name="villa_unit_no" placeholder="Villa / Unit Number">
                                    <label for="villa-unit-no">Villa / Unit Number</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="ref-id" name="ref_id" placeholder="Ref Number">
                                    <label for="ref-id">Ref Number</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="bua-from" name="bua_from" placeholder="Min">
                                            <label for="bua-from">BUA</label>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="bua-to" name="bua_to" placeholder="Max">
                                            <label for="bua-to">BUA</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="plot-from" name="plot_from" placeholder="Min">
                                            <label for="plot-from">Plot</label>
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="plot-to" name="plot_to" placeholder="Max">
                                            <label for="plot-to">Plot</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                                <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right mr-1">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body pt-3">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Status</th>
                        <th>Master Project</th>
                        <th>Project</th>
                        <th>Cluster / Street / Frond</th>
                        <th>Unit/Villa Number</th>
                        <th>Bedrooms</th>
                        <th>BUA (sqft)</th>
                        <th>Plot (sqft)</th>
                        <th>Assigned From</th>
                        <th>Assigned To</th>
                        <th>Assigned Date</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
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
    <script src="/js/scripts/select2.multi-checkboxes.js"></script>
    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            fixedColumns: {
                start: 1
            },
            scrollX: true,
            scrollY: 370,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('data-center-agent.get.datatable') }}',
                'data': function(data){

                    let master_project_id=$('#master-project').val();
                    let unmatched=$('#unmatched').val();
                    let project_id=$('#community').val().join(',');
                    let status=$('#status').val();
                    let bedroom=$('#bedroom').val();
                    let villa_unit_no=$('#villa-unit-no').val();
                    let ref_id=$('#ref-id').val();
                    let bua_from=$('#bua-from').val();
                    let bua_to=$('#bua-to').val();
                    let plot_from=$('#plot-from').val();
                    let plot_to=$('#plot-to').val();

                    // let data_center='0';
                    // if(master_project_id !='' || unmatched !='' || project_id !='' || status !='' ||
                    //      name !='' || bedroom !='' || bua_from !='' || bua_to !='' || plot_from !='' || plot_to)
                        data_center='1';

                    // Append to data
                    data.page='matched';
                    data._token='{{csrf_token()}}';
                    data.data_center=data_center;
                    data.master_project_id=master_project_id;
                    data.unmatched=unmatched;
                    data.project_id=project_id;
                    data.status=status;
                    data.bedroom=bedroom;
                    data.villa_unit_no=villa_unit_no;
                    data.ref_id=ref_id;
                    data.bua_from=bua_from;
                    data.bua_to=bua_to;
                    data.plot_from=plot_from;
                    data.plot_to=plot_to;
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 10 ]}],
            'columns': [
                {data: 'id'},
                {data: 'status'},
                {data: 'master_project'},
                {data: 'project'},
                {data: 'st_cl_fr'},
                {data: 'villa_unit_no'},
                {data: 'bedrooms'},
                {data: 'size'},
                {data: 'plot_size'},
                {data: 'status_by'},
                {data: 'assign_to'},
                {data: 'assign_date'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });
        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
        });
        $('#unmatched').change(function(){
            let val=$(this).val();
            $('#community').removeAttr('disabled','disabled');
            if(val==2)
                $('#community').attr('disabled','disabled');

            $('#community').val('').change();
        });

    </script>
    <script>
        {{--getMasterProject('2');--}}
        {{--function getMasterProject(val){--}}
        {{--    $.ajax({--}}
        {{--        url:"{{ route('master-project.get.ajax') }}",--}}
        {{--        type:"POST",--}}
        {{--        data:{--}}
        {{--            _token:'{{ csrf_token() }}',--}}
        {{--            Emirate:val--}}
        {{--        },--}}
        {{--        success:function (response) {--}}
        {{--            $('.master-project').html(response);--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        $('#master-project').change(function () {
            let val=$(this).val();
            // if(val.length<2){
                getCommunity(val);
            //     $('#community , #unit-villa-number').removeAttr('disabled');
            // }else{
            //     getCommunity('');
            //     $('#community , #unit-villa-number').attr('disabled','disabled');
            // }
            $('#Community').change();
        });

        function getCommunity(val){
            $.ajax({
                url:"{{ route('community.get.ajax') }}",//"{{ route('community.get.ajax.data-center') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    response=response.replace('<option value="">select</option>', "")
                    $('.community').html(response);
                }
            });
        }

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('.reason-view').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                // window.location.href ='/admin/property/view/'+id
                if(id!=undefined) {
                    window.open('/admin/data-center-view/' + id);
                }
            }
        });

        $('body .datatable1 tbody').on('click','.reason-view',function(){
            $('#ViewModal .modal-title').html( 'Reason' );
            $('#ViewModal .modal-body').html( $(this).html() );
        });
    </script>
@endsection
