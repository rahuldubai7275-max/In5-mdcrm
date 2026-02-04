
@extends('layouts/contentLayoutMaster')

@section('title', 'Data Center')

@section('vendor-style')
    {{-- vendor css files --}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">--}}
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
                <div class="row">

                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="master-project">Master Project</label>
                            <select class="form-control select2 filter-master-project" id="master-project" name="master_project">
                                <option value="">Select</option>
                                @php
                                    if($adminAuth->type<3)
                                        $masterProjects=\App\Models\DataCenter::select('master_project_id')->distinct()->get();
                                    else
                                        $masterProjects=\App\Models\DataCenterAccess::select('master_project_id')->where('admin_id',$adminAuth->id)->distinct()->get();
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
                            <select class="form-control filter-community select2-checkbox" multiple id="community" name="community">

                            </select>
                        </fieldset>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3 d-none">
                        <div class="form-group form-label-group">
                            <input type="text" class="form-control" id="project-text" name="project-text" placeholder="Project">
                            <label for="project-text">Project</label>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="assigned">Forwarded</label>
                            <select class="form-control" id="assigned" name="assigned">
                                <option value="">Select</option>
                                <option value="1">Forwarded</option>
                                <option value="2">Not Forwarded</option>

                            </select>
                        </fieldset>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="assigned_to">Client Manager</label>
                            <select class="form-control" id="assigned_to" name="assigned_to">
                                <option value="">Select</option>
                                @php
                                    if($adminAuth->super==1)
                                        $ClientManagers=\App\Models\Admin::where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                    else
                                        $ClientManagers=\App\Models\Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                @endphp
                                @foreach($ClientManagers as $ClientManager)
                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="file-name">File Name</label>
                            <select class="form-control select2" id="file-name">
                                <option value="">Select</option>
                                @php
                                    $Fails=\App\Models\DataCenterFile::orderBy('name','ASC')->get();
                                @endphp
                                @foreach($Fails as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                @endforeach
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
                                    <option value="{{$key}}">{{($key==2)? 'Assigned To' :  $value}}</option>
                                @endforeach
                                <option value="added_to_property">Added To Property</option>
                                <option value="added_to_contact">Added To Contact</option>
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <input type="text" class="form-control" id="villa-unit-no" name="villa-unit-no" placeholder="Villa / Unit Number">
                            <label for="villa-unit-no">Villa / Unit Number</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <input type="text" class="form-control" id="ref-id" name="ref-id" placeholder="Ref Number">
                            <label for="ref-id">Ref Number</label>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="bua-from" name="bua-from" placeholder="Min">
                                    <label for="bua-from">BUA</label>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="bua-to" name="bua-to" placeholder="Max">
                                    <label for="bua-to">BUA</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="plot-from" name="plot-from" placeholder="Min">
                                    <label for="plot-from">Plot Size</label>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="plot-to" name="plot-to" placeholder="Max">
                                    <label for="plot-to">Plot Size</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                        <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right mx-1" data-toggle="modal" data-target="#modalUpload">Upload Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Data</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    {{--<li><a class="btn bg-gradient-info mt-1 mt-md-0 py-1 m px-2 waves-effect waves-light disabled btn-match-master-project" data-toggle="modal" href="#modalMatchMasterProject">Match Master Project</a></li>--}}
                    <li><a class="btn bg-gradient-info mt-1 mt-md-0 py-1 m px-2 waves-effect waves-light disabled btn-match-master-project" data-toggle="modal" href="#modalAssign">Forward</a></li>
                    <li><a class="btn bg-gradient-info mt-1 mt-md-0 py-1 m px-2 waves-effect waves-light disabled btn-match-project" data-toggle="modal" href="#modalMatchProject">Match Project</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body pt-3">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>
                            <div class="d-inline-block">
                                <fieldset>
                                    <label>
                                        <input type="checkbox" class="checkAll">
                                    </label>
                                </fieldset>
                            </div>
                        </th>
                        <th>Ref</th>
                        <th>Status</th>
                        <th>Master Project</th>
                        <th>Project</th>
                        <th>Cluster / Street / Frond</th>
                        <th>Unit/Villa Number</th>
                        <th>Bedrooms</th>
                        <th>BUA (sqft)</th>
                        <th>Plot Size (sqft)</th>
                        <th>Forwarded From</th>
                        <th>Forwarded To</th>
                        <th>Forwarded Date</th>
                        <th>Assigned From</th>
                        <th>Assigned To</th>
                        <th>Assigned Date</th>
                        <th>Added To Contact</th>
                        <th>Added To Property</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="modalMatchProject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Match Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-6">
                            <fieldset class="form-group form-label-group">
                                <label for="master-project">Master Project</label>
                                <select class="form-control master-project select2" name="master_project">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>

                        <div class="col-sm-6">
                            <fieldset class="form-group form-label-group">
                                <label for="community">Project</label>
                                <select class="form-control community select2" id="match_project">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-match-project" class="btn btn-primary">Match</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('data-center-import')}}" class="modal-content" novalidate enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload File</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <p>Please make your Excel exactly like the template below.<br>
                                <b>Template: </b><a href="/images/import-template-image.png" class="px-2 font-medium-5" target="_blank"><i class="feather icon-download"></i></a></p>
                        </div>
                        <div class="col-12 mt-1">
                            <fieldset class="form-group form-label-group">
                                <label for="master-project">Master Project</label>
                                <select class="form-control master-project select2" name="MasterProject" required>
                                    <option value="">Select</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-12">
                            <fieldset class="form-group">
                                <label for="basicInputFile">File</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file" accept=".xlsx,.xls" required>
                                    <label class="custom-file-label" for="file">Choose file</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="submit" class="btn btn-primary upload-file">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade text-left" id="modalAssign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Forward</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <fieldset class="form-group form-label-group mt-2 mt-md-0" style="min-width:180px">
                                <label for="admin">Forward To</label>
                                <select class="form-control select2" id="AgentAssignTo" name="AssignTo">
                                    <option value="">Select</option>
                                    @php
                                        $Agents=App\Models\Admin::where('status',1)->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnAgentAssignTo" class="btn btn-primary">Forward</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    {{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>--}}
    {{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>--}}
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
                start: 2
            },
            scrollX: true,
            scrollY: 370,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 25, 50,100,500, -1], [10, 25, 50,100,500, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('data-center.get.datatable') }}',
                'data': function(data){

                    let file=$('#file-name').val();
                    let master_project_id=$('#master-project').val();
                    let project_text=$('#project-text').val();

                    let unmatched=$('#unmatched').val();
                    let assigned=$('#assigned').val();
                    let assigned_to=$('#assigned_to').val();
                    let project_id=$('#community').val().join(',');
                    let status=$('#status').val();
                    let bedroom=$('#bedroom').val();
                    let villa_unit_no=$('#villa-unit-no').val();
                    let ref_id=$('#ref-id').val();
                    let bua_from=$('#bua-from').val();
                    let bua_to=$('#bua-to').val();
                    let plot_from=$('#plot-from').val();
                    let plot_to=$('#plot-to').val();

                    let data_center='0';
                    if(file !='' || master_project_id !='' || unmatched !='' || assigned !='' || project_id !='' || assigned_to !='' || status !='' ||
                        name !='' || bedroom !='' || bua_from !='' || villa_unit_no !='' || ref_id !='' || bua_to !='' || plot_from !='' || plot_to)
                        data_center='1';

                    data._token='{{csrf_token()}}';
                    data.data_center=data_center;
                    data.file=file;
                    data.project_text=project_text;
                    data.master_project_id=master_project_id;
                    data.unmatched=unmatched;
                    data.assigned=assigned;
                    data.assigned_to=assigned_to;
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
            aoColumnDefs: [{bSortable: false,aTargets: [ 0 ]}],
            'columns': [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'status'},
                {data: 'master_project'},
                {data: 'project'},
                {data: 'st_cl_fr'},
                {data: 'villa_unit_no'},
                {data: 'bedrooms'},
                {data: 'size'},
                {data: 'plot_size'},
                {data: 'agent_assign_admin'},
                {data: 'agent_assign'},
                {data: 'agent_assign_time'},
                {data: 'status_by'},
                {data: 'assign_to'},
                {data: 'assign_date'},
                {data: 'added_to_contact'},
                {data: 'added_to_property'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });
        $('body').on('change','.dt-length select',function(){
            let val=$(this).val();

            $('body table .checkAll').removeAttr('disabled');
            if(val<0)
                $('body table .checkAll').attr('disabled','disabled');
        });

        $('.upload-file').click(function(){
            let input=$('#modalUpload #file');
            //let bytes = input.files[0].size;alert(bytes);
            let ext = $(input).val().split('.').pop().toLowerCase();
            let Format=['xlsx'];
            if ($.inArray(ext, Format) == -1){
                Warning('Warning!',"Invalid Image Format! Image Format Must Be "+Format+".");
                $(input).val(null);
            }else{
                $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...')
            }
        });

        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
        });

        $('#unmatched').change(function(){
            let val=$(this).val();
            $('#community').parent().parent().removeClass('d-none');
            $('#project-text').parent().parent().removeClass('d-none');
            if(val==2)
                $('#community').parent().parent().addClass('d-none');
            else
                $('#project-text').parent().parent().addClass('d-none');

            $('#community').val('').change();
            $('#project-text').val('');
        });

        $('#assigned').change(function(){
            let val=$(this).val();
            $('#assigned_to').parent().parent().removeClass('d-none');
            if(val==2)
                $('#assigned_to').attr('disabled','disabled');
            else
                $('#assigned_to').removeAttr('disabled');

            $('#assigned_to').val('').change();
        });

        $('#btnAgentAssignTo').click(function(){
            let selected = new Array();
            let AssignTo = $('#AgentAssignTo').val()

            $("table tbody input[type=checkbox]:checked").each(function () {
                selected.push(this.value);
            });

            if (selected.length > 0) {
                $.ajax({
                    url: "{{route('data-agent-action')}}",
                    type: "POST",
                    data: {
                        _token: $('form input[name="_token"]').val(),
                        'data': selected,
                        'AssignTo': AssignTo
                    },
                    success: function (response) {
                        table.ajax.reload(null, false);
                        $('form table input[type=checkbox]:checked').prop('checked', false);
                        $('#AgentAssignTo').val('').change();
                        $('#modalAssign').modal('toggle');
                        if(response.r=='0') {
                            toast_('',response.msg,$timeOut=20000,$closeButton=true);
                        }
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        });

        $('#btn-match-project').click(function(){
            var selected = new Array();

            $("table tbody input[type=checkbox]:checked").each(function () {
                selected.push(this.value);
            });

            $.ajax({
                url: "{{route('data-center-match-project')}}",
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    data: selected,
                    project: $('#match_project').val()
                },
                success: function (response) {
                    table.ajax.reload(null, false);
                    $('table input[type=checkbox]:checked').prop('checked', false);
                    $('#modalMatchProject').modal('hide');
                    if(response.r=='0') {
                        toast_('',response.msg,$timeOut=20000,$closeButton=true);
                    }

                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

    </script>
    <script>
        getMasterProject('2');
        function getMasterProject(val){
            $.ajax({
                url:"{{ route('master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('.master-project').html(response);
                }
            });
        }

        $('#modalMatchProject .master-project').change(function () {
            let val=$(this).val();
            getCommunity(val);

            $('#modalMatchProject .community').change();
        });

        $('.filter-master-project').change(function () {
            let val=$(this).val();
            $('#modalMatchProject .master-project').val(val).change();
            $.ajax({
                url:"{{ route('community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    response=response.replace('<option value="">select</option>', "")
                    $('.filter-community').html(response);
                }
            });
        });

        function getCommunity(val){
            $.ajax({
                url:"{{ route('community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    $('#modalMatchProject .community').html(response);
                }
            });
        }

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('a').html();

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
    </script>
@endsection
