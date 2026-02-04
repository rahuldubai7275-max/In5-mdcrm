
@extends('layouts/contentLayoutMaster')

@section('title', 'Properties')

@section('vendor-style')
    {{-- vendor css files --}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">--}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection

@php
    $admin = Auth::guard('admin')->user();
    $company=\App\Models\Company::find($admin->company_id);
    $ClientManagers=\Helper::getCM_DropDown_list('1');
@endphp
@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif
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
                    <form action="{{route('properties-export')}}" class="filter-form">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="client-manager">Client Manager</label>
                                    <select class="form-control select2" multiple id="client-manager" name="client_manager">
                                        <option value="">Select</option>
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="client-manager">Client Manager 2</label>
                                    <select class="form-control select2" multiple id="client-manager-2" name="client_manager_2">
                                        <option value="">Select</option>
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="listing">Listing Type</label>
                                    <select class="form-control" id="listing" name="listing">
                                        <option value="">Select</option>
                                        <option value="1">Sales</option>
                                        <option value="2">Rent</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label>Residential / Commercial</label>
                                    <select class="custom-select form-control" id="type" name="type">
                                        <option value="">Select</option>
                                        @foreach(PropertyType as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="property-type">Property Type</label>
                                    <select class="form-control select2" multiple id="property-type" name="property_type">
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="status">Listing Status</label>
                                    <select class="form-control select2" multiple  id="status" name="status" {{(request('p'))? 'disabled' : ''}}>
                                        <option value="">Select</option>
                                        <option value="pf_error">PF Error</option>
                                        @foreach(Status as $key => $value)
                                            <option value="{{ $key }}">{{ ( ($key==1) ? 'Listed':$value ).( ($key==5 || $key==7)? $company->sample: '') }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="Emirate">Emirate</label>
                                    <select class="custom-select form-control" id="emirate">
                                        <option value="">Select</option>
                                        @php
                                            $Emirates=\App\Models\Emirate::orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($Emirates as $Emirate)
                                            <option value="{{ $Emirate->id }}">{{ $Emirate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="master-project">Master Project</label>
                                    <select class="form-control  select2" multiple id="master-project" name="master_project">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="community">Project</label>
                                    <select class="form-control  select2" multiple id="community" name="community">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="bedrooms">Bedrooms</label>
                                    <select class="form-control select2" multiple id="bedrooms" name="bedrooms">
                                        <option value="">Select</option>
                                        @foreach($Bedrooms as $Bedroom)
                                            <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="rent-price">Rental Type</label>
                                    <select class="form-control" id="rent-price" name="rent_price">
                                        <option value="">Select</option>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="yearly">Yearly</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-color">Last Activity</label>
                                    <select class="form-control" id="select-color" name="select_color">
                                        @php
                                            $activity_contact_setting_2=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','contact_activity_2')->first();
                                            $activity_contact_setting_3=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','contact_activity_3')->first();
                                        @endphp
                                        <option value="">Select</option>
                                        <option value="Green">Less than {{$activity_contact_setting_2->time}} days</option>
                                        <option value="Yellow">Between {{$activity_contact_setting_2->time}} to {{$activity_contact_setting_3->time}} days</option>
                                        <option value="Red">More than {{$activity_contact_setting_3->time}} days</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="off_plan">Completion Status</label>
                                    <select class="custom-select form-control" id="off_plan" name="off_plan">
                                        <option value="">Select</option>
                                        @foreach(OffPlan as $key=>$value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="property_management">Property Management</label>
                                    <select class="form-control" id="property_management" name="property_management">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="2">No</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="status2">Status</label>
                                    <select class="custom-select form-control select2" multiple id="status2" name="status2">
                                        <option value="">Select</option>
                                        @foreach(Status2 as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label for="portal">Portal</label>
                                    <select class="custom-select form-control" id="portal" name="portal">
                                        <option value="">Select</option>
                                        @php
                                            $portals=\App\Models\Portal::get();
                                        @endphp
                                        @foreach($portals as $row)
                                            <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label for="VillaNumber">Villa / Unit Number</label>
                                    <input type="text" id="unit-villa-number" name="unit_villa_number" class="form-control" placeholder="Villa / Unit Number">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="number" id="ref-number" name="ref_number" autocomplete="off" class="form-control" placeholder="Ref Number">
                                            <label for="ref-number">Ref Number</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="rera-permit" name="rera_permit" placeholder="DLD Permit" aria-invalid="false">
                                            <label>DLD Permit Number</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group validate">
                                            <label for="from-date">Added Date</label>
                                            <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group rented-until-box">
                                            <label for="to-date">Added Date</label>
                                            <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Price (AED)</label>
                                            <input type="text" id="from-price" name="from_price" class="form-control number-format" placeholder="From">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Price (AED)</label>
                                            <input type="text" id="to-price" name="to_price" class="form-control number-format" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($admin->type==1)
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <fieldset class="form-group form-label-group" style="min-width:180px">
                                        <label for="admin">Creator</label>
                                        <select class="form-control select2" multiple id="creator" name="creator">
                                            <option value="">Select</option>
                                            @php
                                                $creator=\Helper::getCM_DropDown_list('0');
                                            @endphp
                                            @foreach($creator as $row)
                                                <option value="{{ $row->id }}">{{ $row->firstname.' '.$row->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <input type="hidden" name="property" value="{{(request("p")) ? request("p") : "properties"}}">
                            @endif
                            <div class="col-sm-{{($admin->type==1) ? '9':'12'}}">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                                @if($admin->type==1)<button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right mr-1">Export</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('property-action')}}" class="card">
        @csrf
        <div class="card-header">
            <h4 class="card-title">Properties</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    @if($admin->type<3)
                        <li class="assign-to-list d-none">
                            <fieldset class="form-group form-label-group mt-2 mt-md-0" style="min-width:180px">
                                <label for="admin">Client Manager 1</label>
                                <select class="form-control select2 assign-to" data-type="cm1" id="ClientManager" name="ClientManager">
                                    <option value="">Select</option>
                                    @foreach($ClientManagers as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </li>
                    @endif
                    <li class="assign-to-list d-none">
                        <fieldset class="form-group form-label-group" style="min-width:180px">
                            <label for="admin">Client Manager 2</label>
                            <select class="form-control select2 assign-to" data-type="cm2" id="AssignTo" name="AssignTo">
                                <option value="">Select</option>
                                <option value="null">None</option>
                                @foreach($ClientManagers as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </li>
                    @if($admin->type<3)<li><a href="#pfStatusModal" data-toggle="modal" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light pf-status-btn">PF Status</a></li>@endif
                    <li><a href="/admin/property" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light">Add Property</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard pt-3">
                {{--<div class="table-responsive">--}}
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
                        <th></th>
                        <th></th>
                        <th>Master Project</th>
                        <th>Project</th>
                        <th>Cluster / Street / Frond</th>
                        <th>Unit/Villa Number</th>
                        <th>Type</th>
                        <th>Bedrooms</th>
                        <th>Asking Price (AED)</th>
                        <th>CM 1</th>
                        <th>CM 2</th>
                        <th>Status</th>
                        <th>Expiration Date</th>
                        <th>Last Activity</th>
                        <th>Added Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
                {{--</div>--}}
            </div>
        </div>
        <button type="submit" class="d-none" id="submit-action"></button>
    </form>

    <!-- Modal Rent Price -->
    <div class="modal fade text-left" id="rentPriceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Rent Price</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @if($admin->type<3)
    <!-- Modal Rent Price -->
    <div class="modal fade text-left" id="pfStatusModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">PF Status</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs pf-status-tab" role="tablist">
                        <li class="nav-item"><a data-type="draft" class="nav-link active" id="draft-tab" data-toggle="tab" href="#draft" aria-controls="draft" role="tab" aria-selected="true">Draft</a></li>
                        <li class="nav-item"><a data-type="takendown" class="nav-link" id="takendown-tab" data-toggle="tab" href="#takendown" aria-controls="takendown" role="tab" aria-selected="true">Takendown</a></li>
                        {{--<li class="nav-item"><a data-type="archived" class="nav-link" id="archived-tab" data-toggle="tab" href="#archived" aria-controls="archived" role="tab" aria-selected="true">Archived</a></li>--}}
                        <li class="nav-item"><a data-type="unpublished" class="nav-link" id="unpublished-tab" data-toggle="tab" href="#unpublished" aria-controls="unpublished" role="tab" aria-selected="true">Unpublished</a></li>
                        <li class="nav-item"><a data-type="pending_approval" class="nav-link" id="pending_approval-tab" data-toggle="tab" href="#pending_approval" aria-controls="pending_approval" role="tab" aria-selected="true">Pending Approval</a></li>
                        <li class="nav-item"><a data-type="rejected" class="nav-link" id="rejected-tab" data-toggle="tab" href="#rejected" aria-controls="rejected" role="tab" aria-selected="true">Rejected</a></li>
                        {{--<li class="nav-item"><a data-type="approved" class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" aria-controls="approved" role="tab" aria-selected="true">Approved</a></li>--}}
                        <li class="nav-item"><a data-type="failed" class="nav-link" id="failed-tab" data-toggle="tab" href="#failed" aria-controls="failed" role="tab" aria-selected="true">Failed</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="draft" aria-labelledby="draft-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="takendown" aria-labelledby="takendown-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="archived" aria-labelledby="archived-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="unpublished" aria-labelledby="unpublished-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="pending_approval" aria-labelledby="pending_approval-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="rejected" aria-labelledby="rejected-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="approved" aria-labelledby="approved-tab" role="tabpanel">

                        </div>

                        <div class="tab-pane fade" id="failed" aria-labelledby="failed-tab" role="tabpanel">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @include('admin/history')
@endsection
@section('vendor-script')
    {{-- vendor files--}}
    {{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>--}}
    {{--    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>--}}
    {{--    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>--}}
    {{--    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>--}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        let property='{{(request("p")) ? request("p") : "properties"}}';
        let alt='{{$admin->type}}';
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            fixedColumns: {
                start: 2
            },
            scrollX: true,
            scrollY: 450,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('property.get.datatable') }}',
                'data': function(data){
                    // alert($('#status').val());
                    // Append to data
                    data.property = property;
                    data._token='{{csrf_token()}}';
                    data.listing=$('#listing').val();
                    data.status=$('#status').val().join(",");
                    data.type=$('#type').val();
                    data.property_type=$('#property-type').val().join(",");
                    data.client_manager=$('#client-manager').val().join(",");
                    data.client_manager_2=$('#client-manager-2').val().join(",");
                    if(alt==1) data.creator=$('#creator').val().join(",");
                    data.emirate=$('#emirate').val();
                    data.master_project=$('#master-project').val().join(",");
                    data.community=$('#community').val().join(",");
                    data.bedrooms=$('#bedrooms').val().join(",");
                    data.off_plan=$('#off_plan').val();
                    data.unit_villa_number=$('#unit-villa-number').val();
                    data.rent_price=$('#rent-price').val();
                    data.status2=$('#status2').val().join(",");
                    data.from_price=$('#from-price').val();
                    data.to_price=$('#to-price').val();
                    data.id=$('#ref-number').val();
                    data.portal=$('#portal').val();
                    data.property_management=$('#property_management').val();
                    data.rera_permit=$('#rera-permit').val();
                    data.select_color=$('#select-color').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 0,2,17 ]}],
            'columns': [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'img'},
                {data: 'status'},
                {data: 'master_project_id'},
                {data: 'community_id'},
                {data: 'cluster_street_id'},
                {data: 'villa_number'},
                {data: 'villa_type_id'},
                {data: 'bedroom_id'},
                {data: 'expected_price'},
                {data: 'client_manager_id'},
                {data: 'client_manager2_id'},
                {data: 'status2'},
                {data: 'expiration_date'},
                {data: 'last_activity'},
                {data: 'created_at'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('body').on('click','.action .ajax-delete', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('model');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: model,
                        type: "POST",
                        data: {
                            _token: $('form input[name="_token"]').val(),
                            Delete:id
                        },
                        success: function (response) {
                            if(response.r=='0') {
                                toast_('',response.msg,$timeOut=20000,$closeButton=true);
                            }else if(response.r=='-1'){
                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: response.msg,
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    cancelButtonText: 'Cancel',
                                    confirmButtonText:'Yes, delete it!',
                                    confirmButtonClass: 'btn btn-danger',
                                    cancelButtonClass: 'btn btn-primary ml-1',
                                    buttonsStyling: false,
                                }).then(function (result) {
                                    if (result.value) {
                                        $.ajax({
                                            url: model,
                                            type: "POST",
                                            data: {
                                                _token: $('form input[name="_token"]').val(),
                                                Delete:id,
                                                activities:'delete'
                                            },
                                            success: function (response) {
                                                table.ajax.reload(null, false);
                                            }, error: function (data) {
                                                var errors = data.responseJSON;
                                                console.log(errors);
                                            }
                                        });
                                    }
                                })
                            }else{
                                table.ajax.reload(null, false);
                            }
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            })
        });

        $('.assign-to').change(function(){
            var selected = new Array();

            $("form table tbody input[type=checkbox]:checked").each(function () {
                selected.push(this.value);
            });

            let type=$(this).data('type');
            if(type=='cm1' && selected.length > 10){
                toast_('', 'A maximum of 10 properties will be assigned.', $timeOut = 20000, $closeButton = true);
            }else {
                if (selected.length > 0) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: 'Cancel',
                        confirmButtonText: 'Yes',
                        confirmButtonClass: 'btn btn-primary',
                        cancelButtonClass: 'btn btn-danger ml-1',
                        buttonsStyling: false,
                    }).then(function (result) {
                        if (result.value) {
                            $.ajax({
                                url: "{{route('property-action')}}",
                                type: "POST",
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    property: selected,
                                    AssignTo: $('#AssignTo').val(),
                                    ClientManager: $('#ClientManager').val()
                                },
                                success: function (response) {
                                    table.ajax.reload(null, false);
                                    $('form table input[type=checkbox]:checked').prop('checked', false);
                                    $('.assign-to-list').addClass('d-none');
                                    $('#AssignTo').val('').change();
                                    $('#ClientManager').val('').change();
                                    if (response.r == '0') {
                                        toast_('', response.msg, $timeOut = 20000, $closeButton = true);
                                    }

                                }, error: function (data) {
                                    var errors = data.responseJSON;
                                    console.log(errors);
                                }
                            });
                        }
                    });
                }
            }
        });

        $('.pf-status-btn').click(function () {
            $('#draft-tab').click();
        });

        $('.pf-status-tab .nav-link').click(function () {
            let type=$(this).data('type');
            $.ajax({
                url:"{{ route('properties.pf-status') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    type:type
                },
                success:function (response) {
                    $('#'+type).html(response);
                }
            });
        });

        $('select , input'),change(function () {
            $('.export-btn').removeAttr('disabled');
        });


    </script>

    <script>
        $("#Listing").change(function(){
            var val=$(this).val();
            if(val=='Rent')
                $('#Status option[value="Rented"]').show();
            else
                $('#Status option[value="Rented"]').hide();
        });

        $('#type').change(function(){
            getPropertyType();
        });

        getPropertyType();
        function getPropertyType(){
            let type=$('#type').val();
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('#property-type').html(response);
                }
            });
        }
    </script>

    <script>

        $('#emirate').change(function () {
            let val=$(this).val();
            getMasterProject(val);
        });

        //$('#emirate').val(2).change();

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
            if(val.length<2){
                getCommunity(val);
                $('#community , #unit-villa-number').removeAttr('disabled');
            }else{
                getCommunity('');
                $('#community , #unit-villa-number').attr('disabled','disabled');
            }
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
                    $('#community').html(response);
                }
            });
        }

        $('#community').change(function () {
            let val=$(this).val();
            if(val.length<2){
                // getVillaNumber(val);
                $('#unit-villa-number').removeAttr('disabled');
            }else{
                // getVillaNumber('');
                $('#unit-villa-number').attr('disabled','disabled');
            }
        });

    </script>

    <script>
        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('.pf-error').parent().html();

            if(!html)
                html=$(this).children('.rent-price').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                if(id!=undefined) {
                    // window.location.href ='/admin/property/view/'+id
                    if ((property == 'RL' || property == 'rfl') && alt < 3)
                        window.open('/admin/property-edit/' + id);
                    else
                        window.open('/admin/property/view/' + id);
                }
            }
        });

        $('body .datatable1 tbody').on('click','.rent-price',function(){
            $('#rentPriceModal .modal-body').html(`
            <p><b>Daily Price:</b> AED ${$(this).data('daily')}</p>
            <p><b>Weekly Price:</b> AED ${$(this).data('weekly')}</p>
            <p><b>Monthly Price:</b> AED ${$(this).data('monthly')}</p>
            <p><b>Yearly Price:</b> AED ${$(this).data('yearly')}</p>
            `);

        });

        let history=0;
        $('body tbody').on('click','.show-history',function(){
            history=$(this).parent().data("id");
            tableHistory.draw();
        });

        $('body tbody').on('click','.history-value',function(){
            let id=$(this).data("id");
            $('#historyValueModal .modal-body').html('');
            $.ajax({
                url:"{{ route('history.value') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    id:id
                },
                success:function (response) {
                    $('#historyValueModal .modal-body').html(response);
                }
            });
        });

        var tableHistory=$('.datatable-history').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "order": [[ 4, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('history') }}',
                'data': function(data){

                    data.model = 'Property';
                    data.property = history;
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 2 ]}],
            'columns': [
                {data: 'admin_id'},
                {data: 'title'},
                {data: 'value'},
                {data: 'action'},
                {data: 'created_at'},
            ],
        });


        $('body').on('click','.action .copy-property', function () {
            var id=$(this).parent().data('id');
            var action=$(this).parent().data('copy-action');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You want to activate!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Copy',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="CopyProperty">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action',action);
                    $('.delete-form-box form input:submit').click();
                }
            })
        });

        $('body').on('click','.pf-error', function () {
            let id=$(this).data("pid");
            let type=$(this).data("type");

            $('#ViewModal .modal-body').html('');
            $('#ViewModal .modal-title').html('PF Error');

            if(type==2){
                $('#ViewModal .modal-title').html('PF Report');
            }
            $.ajax({
                url:"{{ route('property.pf-error') }}",
                type:"POST",
                data:{
                    _token:'{{csrf_token()}}',
                    id:id,
                    type:type
                },
                success:function (response) {
                    $('#ViewModal .modal-body').html(response);
                }
            });

        });
    </script>
@endsection
