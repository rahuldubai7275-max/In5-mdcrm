
@extends('layouts/contentLayoutMaster')

@section('title', 'Data Center Access')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Access</h4>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('dc-access.add') }}" class="form error" novalidate>
                            @csrf
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="admin">User</label>
                                            <select class="form-control select2" id="admin" name="Admin">
                                                <option value="">Select</option>
                                                @php
                                                    $Agents=App\Models\Admin::where('status','1')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                                @endphp
                                                @foreach($Agents as $agent)
                                                    <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group form-label-group">
                                            <label for="MasterProject">Master Project</label>
                                            <select class="custom-select form-control select2" id="master-project" name="MasterProject" required>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{--<div class="col-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="unmatched" id="unmatched">
                                            <label class="custom-control-label" for="unmatched">Unmatched</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="community">Project</label>
                                            <select class="form-control select2-checkbox" id="community" name="Community[]">

                                            </select>
                                        </fieldset>
                                    </div>--}}

                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="community">Project</label>
                                                    <select class="form-control select2-checkbox" id="community" name="Community[]">

                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-2 p-0">
                                                <input type="checkbox" class="checkbox" id="all" name="all" data-target="#community"> All
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 record-action-box">
                                        <button type="submit" class="btn btn-primary mb-1 btn-create float-right" value="submit">Access</button>
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
        <div class="col-md-8 col-sm-12">

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

                            <div class="col-12 col-sm-6">
                                <fieldset class="form-group form-label-group">
                                    <label for="master-project">Master Project</label>
                                    <select class="form-control select2 filter-master-project" id="filter-master-project">
                                        <option value="">Select</option>
                                        @php
                                            $masterProjects=\App\Models\MasterProject::orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($masterProjects as $row)
                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6">
                                <fieldset class="form-group form-label-group">
                                    <label for="admin">User</label>
                                    <select class="form-control select2" id="filter-admin">
                                        <option value="">Select</option>
                                        @php
                                            $Agents=App\Models\Admin::where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-sm-12">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Access</h4>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table datatable1 truncate-table table-striped">
                                <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Master Project</th>
                                    <th>Projects</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
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
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="/js/scripts/select2.multi-checkboxes.js"></script>
    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 3, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('dc-access.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    data._token='{{csrf_token()}}';
                    data.master_project=$('#filter-master-project').val();
                    data.admin=$('#filter-admin').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 4 ]}],
            'columns': [
                {data: 'firstname'},
                {data: 'master_project_name'},
                {data: 'projects'},
                {data: 'created_at'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });
    </script>
    <script>
        let projects='';
        var ActionAdd=$('#record-form').attr('action');
        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
            width: '100%',
        });

        $(".checkbox").click(function(){
            let target=$(this).data('target')
            if($(".checkbox").is(':checked') ){
                $(target+" > option").prop("selected","selected");
                $(target).attr('disabled','disabled').trigger("change");
            }else{
                $(target+" > option").prop("selected","");
                $(target).removeAttr('disabled').trigger("change");
            }
        });

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
                    $('#master-project').html(response);
                }
            });
        }

        $('#master-project').change(function () {
            let val=$(this).val();
            getCommunity(val);

            $('#Community').change();
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
                    response=response.replace('<option value="">select</option>', "");
                    $('#community').html(response);
                    if(projects)
                        $('#community').val(projects).change();

                }
            });
        }

        $('body').on('click','.projects',function() {
            let id=$(this).data('id');
            $('#ViewModal .modal-title').html( $(this).data('title') );
            $('#ViewModal .modal-body').html( '' );

            $.ajax({
                url:"{{ route('dc-access.get-projects') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    id:id
                },
                success:function (response) {
                    $('#ViewModal .modal-body').html( response );
                }
            });
        });

        $('body').on('click','.edit-record',function() {
            var ActionEdit = '{{route('dc-access.edit')}}';
            $('#record-form').attr('action',ActionEdit);
            let id=$(this).parent().data('id');
            let admin=$(this).parent().data('admin');
            let master_project=$(this).parent().data('masterproject');
            // let unmatched=$(this).parent().data('unmatched');
            let project=$(this).parent().data('project');
            if(project=='') {
                $('#all').prop('checked',true);
                $('#community').attr('disabled','disabled').trigger("change");
            }
            let projectJson='['+project+']';
            projects=JSON.parse(projectJson);
            $('#admin').val(admin).change();
            $('#master-project').val(master_project).change();

            // if(unmatched==1)
            //     $('#unmatched').prop('checked',true);

            $('#record-form input[name=update]').val($(this).parent().data('id'));

            $('#record-form').attr('action',ActionEdit);
            $('.btn-create').addClass('d-none');
            $('.record-action-box .update-btn-box').removeClass('d-none');
        });
        $('#record-form :reset').click(function () {
            projects=[];
            $('#admin').val('').change();
            $('#master-project').val('').change();

            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });
    </script>
@endsection
