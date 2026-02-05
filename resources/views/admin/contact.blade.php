
@extends('layouts/contentLayoutMaster')

@section('title', 'Contacts')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection

@php
    $admin = Auth::guard('admin')->user();
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
                    <form action="{{route('contacts-export')}}">
                        <div class="row mt-1">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-client-manager">Client Manager</label>
                                    <select class="form-control select2" multiple id="select-client-manager" name="client_manager">
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-client-manager">Client Manager 2</label>
                                    <select class="form-control select2" multiple id="select-client-manager-2" name="client_manager_2">
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-private">Privacy Level</label>
                                    <select class="form-control" id="private" name="private">
                                        <option value="0">Company</option>
                                        <option value="1">Personal</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label>Looking For</label>
                                            <select class="custom-select form-control" id="select-looking-for" name="looking_for">
                                                <option value="">Select</option>
                                                @foreach(BUYER_LOOKING_FOR as $kay=>$value)
                                                    <option value="{{$kay}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="select-contact-categories">Contact Categories</label>
                                            <select class="form-control select2" multiple id="select-contact-categories" name="contact_categories" {{(request('c'))? 'disabled' : ''}}>
                                                <option value="buyer">Buyer</option>
                                                <option value="tenant">Tenant</option>
                                                <option value="agent">Agent</option>
                                                <option value="owner">Owner</option>
                                                <option value="developer">Developer</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="select-developer">Developer</label>
                                    <select class="custom-select form-control select2" multiple id="select-developer" name="select_developer">
                                        <option value="">Select</option>
                                        @php
                                            $developers=\App\Models\Developer::orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($developers as $dev)
                                            <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="select-color">Last Activity</label>
                                            <select class="form-control" id="select-color" name="select_color">
                                                @php
                                                    $activity_contact_setting_2=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','contact_activity_2')->first();
                                                    $activity_contact_setting_3=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','contact_activity_3')->first();
                                                @endphp
                                                <option value="">Select</option>
                                                <option value="Green">Less than {{$activity_contact_setting_2->time}} days (Green)</option>
                                                <option value="Yellow">Between {{$activity_contact_setting_2->time}} to {{$activity_contact_setting_3->time}} days (Yellow)</option>
                                                <option value="Red">More than {{$activity_contact_setting_3->time}} days (Red)</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6">
                                    <fieldset class="form-group form-label-group">
                                        <label for="archive">Status</label>
                                        <select class="form-control" id="select-status" name="select_status">
                                            <option value="0" selected>Active</option>
                                            <option value="1">Archive</option>
                                        </select>
                                    </fieldset>
                                </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-finance-status">Finance Status</label>
                                    <select class="form-control" id="select-finance-status" name="finance_status">
                                        <option value="">Select</option>
                                        <option value="Cash Purchaser">Cash Purchaser</option>
                                        <option value="Mortgage Purchaser">Mortgage Purchaser</option>
                                        <option value="Swapping Deal">Swapping Deal</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="Emirate">Emirate</label>
                                    <select class="custom-select form-control" id="emirate">
                                        <option value="">Select</option>
                                        @php
                                            $Emirates=\App\Models\Emirate::get();
                                        @endphp
                                        @foreach($Emirates as $Emirate)
                                            <option value="{{ $Emirate->id }}">{{ $Emirate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-master-project">Master Project</label>
                                    <select class="form-control  select2" multiple id="select-master-project" name="master_project">
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-sm-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="Community">Project</label>
                                    <select class="form-control  select2" multiple id="Community" name="community">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label>Residential / Commercial</label>
                                    <select class="custom-select form-control" id="P_Type" name="p_type">
                                        <option value="">Select</option>
                                        @foreach(PropertyType as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label>Property Type</label>
                                    <select class="custom-select form-control select2" multiple id="PropertyType" name="property_type">
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="Bedrooms">Bedrooms</label>
                                    <select class="custom-select form-control select2" multiple id="Bedroom" name="bedroom">
                                        <option value="">Select</option>
                                        @php
                                            $Bedrooms=App\Models\Bedroom::get();
                                        @endphp
                                        @foreach($Bedrooms as $Bedroom)
                                            <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-contact-source">Contact Source</label>
                                    <select class="form-control select2" multiple id="select-contact-source" name="contact_source">
                                        @foreach($ContactSources as $CSource)
                                            <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" id="first-name" name="first_name" autocomplete="off" class="form-control" placeholder="Name">
                                    <label for="first-name">Name</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" id="contact-number" name="contact_number" autocomplete="off" class="form-control country-code" placeholder="Contact Number">
                                    <label for="contact-number">Contact Number</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <input type="text" id="email-address" name="email_address" autocomplete="off" class="form-control" placeholder="Email Address">
                                    <label for="email-address">Email Address</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="deal-contact">Our Deals</label>
                                            <select class="custom-select form-control" id="deal-contact" name="deal_contact">
                                                <option value="">Select</option>
                                                <option value="1">Rental</option>
                                                <option value="2">Sales</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="number" id="ref-number" name="id" autocomplete="off" class="form-control" placeholder="Ref Number">
                                            <label for="ref-number">Ref Number</label>
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
                                            <input type="text" id="budget-from" name="budget_from" autocomplete="off" class="form-control number-format" placeholder="From">
                                            <label for="budget-from">Budget (AED)</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" id="budget-to" name="budget_to" autocomplete="off" class="form-control number-format" placeholder="To">
                                            <label for="budget-to">Budget (AED)</label>
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
                            @endif

                            <input type="hidden" name="contact" value="{{(request("c")) ? request("c") : "contacts"}}">
                            <div class="col-sm-{{($admin->type==1) ? '9':'12'}}">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                                @if($admin->type==1)<button type="submit" class="btn btn-export bg-gradient-info waves-effect waves-light float-right mr-1">Export</button>@endif
                                @php
                                    $upload_contact_setting=\App\Models\Setting::where('company_id',$admin->company_id)->where('title','upload_contact')->first();
                                    $upload_contact_user=[];
                                    if($upload_contact_setting)
                                        $upload_contact_user=\App\Models\SettingAdmin::where('setting_id',$upload_contact_setting->id)->where('admin_id',$admin->id)->first();
                                @endphp
                                @if($upload_contact_user || $admin->super==1)<button type="button" class="btn bg-gradient-info waves-effect waves-light float-right mx-1" data-toggle="modal" data-target="#modalUpload">Import Contacts</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('contact-action')}}" class="card">
        @csrf
        <div class="card-header">
            <h4 class="card-title">Contacts</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    @if($admin->type<3)
                        <li class="assign-to-list d-none">
                            <fieldset class="form-group form-label-group assign-to mt-2 mt-md-0" style="min-width:180px">
                                <label for="admin">Client Manager 1</label>
                                <select class="form-control select2" id="ClientManager" name="ClientManager">
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
                            <select class="form-control select2 assign-to" id="AssignTo" name="AssignTo">
                                <option value="">Select</option>
                                <option value="null">None</option>
                                @foreach($ClientManagers as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </li>
                    <li><a style="width: 132px" class="btn bg-gradient-info mt-1 mt-md-0 py-1 m px-2 waves-effect waves-light mr-1" href="/admin/add-contacts">Add Contact</a></li>
                    <li><a style="width: 132px" class="btn bg-gradient-info mt-1 mt-md-0 py-1 m px-2 waves-effect waves-light disabled btn-sand-mail" data-toggle="modal" href="#modalSendMail">Send Email</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard bt-3">
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
                            <th>Full Name</th>
                            <th>Finance Status</th>
                            <th>Contact Categories</th>
                            <th>CM1</th>
                            <th>CM2</th>
                            <th>Budget (AED)</th>
{{--                            <th>Source</th>--}}
{{--                            <th>Developer</th>--}}
                            <th>Property Type</th>
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
    <!-- Modal -->
    <div class="modal fade" id="modalSendMail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <form method="post" action="{{ route('send-mail')  }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Send Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="form-group form-label-group">
                                <input type="text" id="Subject" name="Subject" autocomplete="off" class="form-control" placeholder="Subject">
                                <label for="Subject">Subject</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-label-group form-group">
                                <select class="select-2-property form-control" name="Properties[]" id="Properties"></select>
                                <label for="SearchRepository" id="Properties">Properties</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label-group">
                                <div class="form-label-group form-group">
                                    <select class="form-control select-2-off-plan-project" name="DeveloperProject" id="DeveloperProject">
                                        <option value="">Select</option>
                                    </select>
                                    <label for="DeveloperProject">New Projects</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <fieldset class="form-group form-label-group mb-0">
                                <textarea class="form-control char-textarea" name="Message" id="Message" required="" rows="12" placeholder="Message" aria-invalid="false"></textarea>
                                <label>Message</label>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <fieldset class="form-group mb-0">
                        <label for="attach-file">Attach</label>
                        <div class="d-flex">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input document-upload" data-this="attach-file" id="attach-file"
                                       data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".attach-progress-bar" data-input="#attach">
                                <label class="custom-file-label" for="attach-file">Choose file</label>
                            </div>
                            <div class="pl-1"><a href="javascript:void(0);" class="d-none" id="CancelAttach" data-input="Other"><i class="fa fa-close"></i></a></div>
                        </div>
                        <input type="hidden" id="attach" name="attach" value="">
                        <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                            <div class="progress-bar bg-teal progress-bar-striped attach-progress-bar" role="progressbar"
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            </div>
                        </div>
                    </fieldset>
                    <input type="hidden" id="email_submit" name="submit" value="">
                    <button type="submit" class="btn bg-gradient-info waves-effect waves-light">Send</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Column selectors with Export Options and print table -->

    <div class="modal fade text-left" id="contactCategoryShowModal" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-text-bold-600" id="cal-modal">Queries</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body pt-2">
                    <ul class="list-group list-group-flush">

                    </ul>
                </div>
                <!--<div class="modal-footer">
                </div>-->
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="modalUpload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('contacts-import')}}" class="modal-content" novalidate enctype="multipart/form-data">
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
                                <b>Template: </b><a href="/images/contact_import_template.png" class="px-2 font-medium-5" target="_blank"><i class="feather icon-download"></i></a><br>
                                Please don't upload more than 500 records</p>
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

    @include('admin/history')
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        let contact='{{(request("c")) ? request("c") : "contacts"}}';
        let alt='{{$admin->type}}';
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],

            fixedColumns: {
                start: 2
            },
            scrollX: true,
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('contacts.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    data.contact = contact;
                    data._token='{{csrf_token()}}';
                    data.client_manager=$('#select-client-manager').val().join(",");
                    data.client_manager_tow=$('#select-client-manager-2').val().join(",");
                    data.private=$('#private').val();
                    if(alt==1) data.creator=$('#creator').val().join(",");
                    data.finance_status=$('#select-finance-status').val();
                    data.contact_categories=$('#select-contact-categories').val().join(",");
                    data.looking_for=$('#select-looking-for').val();
                    data.select_color=$('#select-color').val();
                    data.first_name=$('#first-name').val();
                    //data.last_name=$('#last-name').val();
                    data.email_address=$('#email-address').val();
                    data.status=$('#select-status').val();
                    data.contact_number=$('#contact-number').val();
                    data.emirate=$('#emirate').val();
                    data.master_project=$('#select-master-project').val().join(",");
                    data.community=$('#Community').val().join(",");
                    data.p_type=$('#P_Type').val();
                    data.property_type=$('#PropertyType').val().join(",");
                    data.bedroom=$('#Bedroom').val().join(",");
                    data.id=$('#ref-number').val();
                    data.budget_from=$('#budget-from').val();
                    data.budget_to=$('#budget-to').val();
                    data.contact_source=$('#select-contact-source').val().join(",");
                    data.select_developer=$('#select-developer').val().join(",");
                    data.deal_contact=$('#deal-contact').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 0,4,8,11 ]}],
            'columns': [
                {data: 'checkbox'},
                {data: 'id'},
                {data: 'firstname'},
                {data: 'buy_type'},
                {data: 'contact_categories'},
                {data: 'client_manager'},
                {data: 'client_manager_tow'},
                {data: 'sale_budget'},
                // {data: 'contact_source'},
                // {data: 'developer_id'},
                {data: 'contact_property_type'},
                // {data: 'lettings_budget'},
                {data: 'last_activity'},
                {data: 'created_at'},
                {data: 'Action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('#private').change(function(){
            if($(this).val()=='1'){
                $('#select-client-manager , #select-client-manager-2').attr('disabled','disabled');
            }else{
                $('#select-client-manager , #select-client-manager-2').removeAttr('disabled');
            }
            $('#select-client-manager , select-client-manager-2').val('').change();
        });

        $('body .datatable1 tbody').on('click','.preview-category a',function(){
            let id=$(this).data('id');
            $.ajax({
                url:"{{ route('get-contact-category-ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    contact:id
                },
                success:function (response) {
                    $('#contactCategoryShowModal .modal-body ul').html(response);
                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('body .datatable1 tbody').on('click','.contact-status',function(){
            let id=$(this).parent().data('id');
            let status=$(this).data('status');

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
                    /*$('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="_id">');
                    $('.delete-form-box form').append('<input type="hidden" value="'+status+'" name="status">');
                    $('.delete-form-box form').append('<input type="submit" name="submit">');
                    $('.delete-form-box form').attr('action',"{{ route('contact-status') }}");
                        $('.delete-form-box form input').click();*/

                    $.ajax({
                        url: "{{ route('contact-status') }}",
                        type: "POST",
                        data: {
                            _token: $('form input[name="_token"]').val(),
                            '_id':id,
                            'status':status
                        },
                        success: function (response) {
                            table.ajax.reload(null, false);
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            });
        });

        $('.assign-to').change(function(){
            var selected = new Array();

            $("form table tbody input[type=checkbox]:checked").each(function () {
                selected.push(this.value);
            });

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
                            url: "{{route('contact-action')}}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                contact: selected,
                                AssignTo: $('#AssignTo').val(),
                                ClientManager: $('#ClientManager').val()
                            },
                            success: function (response) {
                                table.ajax.reload(null, false);
                                $('form table input[type=checkbox]:checked').prop('checked', false);
                                $('.assign-to-list').addClass('d-none');
                                $('#AssignTo').val('').change();
                                $('#ClientManager').val('').change();
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
            }
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
                    $('#select-master-project').html(response);
                }
            });
        }
    </script>

    <script>
        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('.preview-category').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                // window.location.href ='/admin/contact/view/'+id
                if(id!=undefined) {
                    window.open('/admin/contact/view/' + id);
                }
            }

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

                    data.model = 'Contact';
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
    </script>

    <script>
        $('#P_Type').change(function(){
            getPropertyType();
        });

        getPropertyType();
        function getPropertyType(){
            let type=$('#P_Type').val();
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    type:type
                },
                success:function (response) {
                    $('#PropertyType').html(response);
                }
            });
        }
    </script>

    <script>
        PropertySelect2();
        function PropertySelect2(SelectType=true) {
            // Loading remote data
            $(".select-2-property").select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple:SelectType,
                ajax: {
                    url: "{{ route('property.ajax.select') }}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Property Information',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function formatRepo (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="w-100 ml-1">
                                <div><b>${repo.ref}</b></div>
                                <div>${repo.address}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelection (repo) {
            return repo.ref ||  repo.text;

        }
    </script>

    <script>
        offPlanSelect2();
        function offPlanSelect2(SelectType=false) {
            // Loading remote data
            $(".select-2-off-plan-project").select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple:SelectType,
                ajax: {
                    url: "{{ route('off-plan-project.ajax.select') }}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Property Information',
                minimumResultsForSearch: Infinity,
                templateResult: offPlanFormatRepo,
                templateSelection: offPlanFormatRepoSelection,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function offPlanFormatRepo (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                                <div class="d-flex align-items-center">
                                    <img style="width:80px" src="{{env('MD_URL')}}${repo.picture}">
                                    <div class="ml-1">
                                        <div><b>${repo.project_name}</b></div>
                                        <div>${repo.master_project}</div>
                                    </div>
                                </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            //markup += '</div></div>';

            return markup;
        }

        function offPlanFormatRepoSelection (repo) {
            return repo.ref ||  repo.project_name;

        }
    </script>
    <script>
        $('#select-master-project').change(function () {
            let val=$(this).val();
            if(val.length<2){
                getCommunity(val);
                $('#Community').removeAttr('disabled');
            }else{
                getCommunity('');
                $('#Community').attr('disabled','disabled');
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
                    $('#Community').html(response);
                }
            });
        }
    </script>

    <script> //document upload
        let ProgressBar='';
        let InputAttachDocument='';
        let _this='';
        var ajax;

        $('.document-upload').change(function(){
            _this=$(this).data('this');
            var Action=$(this).data('action');
            var token=$(this).data('token');
            ProgressBar=$(this).data('progress');
            ProgressBar=$(this).data('progress');
            InputAttachDocument=$(this).data('input');
            uploadDocument(Action,token);
        });

        function uploadDocument(Action,token) {
            var file = _(_this).files[0];
            // alert(file.name+" | "+file.size+" | "+file.type+" | "+file.name.split('.').pop());
            if(file.size>2000000){
                Warning('Warning!',"The size of the file is "+formatBytes(file.size)+" , The maximum allowed upload file size is 2 MB");
                _this.val(null);
                return ;
            }

            if(file.name.split('.').pop()=="pdf" ||
                file.name.split('.').pop()=="doc" ||
                file.name.split('.').pop()=="docx" ||
                file.name.split('.').pop()=="xlsx" ||
                file.name.split('.').pop()=="xml" ||
                file.name.split('.').pop()=="xls" ||
                file.name.split('.').pop()=="jpg" ||
                file.name.split('.').pop()=="jpeg" ||
                file.name.split('.').pop()=="webp" ||
                file.name.split('.').pop()=="png"){
                var formdata = new FormData();
                formdata.append("AttachDocumentSubmit", "0");
                formdata.append("_token", token);
                formdata.append("DocumentFile", file);
                ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", documentProgressHandler, false);
                ajax.addEventListener("load", documentCompleteHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", Action);
                ajax.send(formdata);
            }else{
                let bytes = file.size;
                //alert(formatBytes(bytes));
                Swal.fire({
                    title: 'The format is not supported.',
                    text: "Supported files (pdf, doc, docx, xlsx, xml, xls, jpg, jpeg, webp, png)",
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Cancel',
                    confirmButtonText:'Yes',
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                });
            }

        }

        function _(el) {
            return document.getElementById(el);
        }

        function documentProgressHandler(event) {
            $(ProgressBar).parent().removeClass("d-none");
            $('#CancelAttach').removeClass('d-none');
            // $('#AttachDocumentBtn').attr('disabled', 'disabled');
            var percent = (event.loaded / event.total) * 100;
            $(ProgressBar).css({"width": Math.round(percent) + "%"});
            $(ProgressBar).html(Math.round(percent) + "%");
        }

        function documentCompleteHandler(event) {
            // var FileName = event.target.responseText;
            var response = jQuery.parseJSON( event.target.responseText );
            $(ProgressBar).html("Upload successfully");
            // $('#AttachDocumentBtn').addClass('d-none');
            // $("#ArticleFile").val('');
            $(InputAttachDocument).val(response.name);
            // $(InputAttachDocument).removeClass('hide');
            // $('#UpdateArticle').removeAttr('disabled').removeAttr('title').val(FileName);
        }

        function errorHandler(event) {
            _("status").innerHTML = "Upload Failed";
        }
        //
        function abortHandler(event) {
            _("status").innerHTML = "Upload Aborted";
        }

        $('#CancelAttach').click(function(){
            ajax.abort();
            // $('.val-attach').removeAttr('disabled');
            $(ProgressBar).parent().addClass("d-none");
            $(ProgressBar).css({"width": "0%"});
            $(ProgressBar).html("0%");


            $(_this).val('');
            $(InputAttachDocument).val('');
            $('#attach-file').val('').parent().children().html('Choose file');

            $('#CancelAttach').addClass('d-none');
            //sleep(4000);
            // $("#Box_Attach_Display").addClass("hide");

        });

        $('.btn-sand-mail').click(function(){
            $('#Properties , #DeveloperProject').removeAttr('disabled');
            $('#Properties').val('').change();
            $('#DeveloperProject').val('').change();
        });

        $('#Properties , #DeveloperProject').change(function(){

            $('#Properties , #DeveloperProject').removeAttr('disabled');

            if( $('#Properties').val()!='' )
                $('#DeveloperProject').attr('disabled','disabled');

            if( $('#DeveloperProject').val()!='' )
                $('#Properties').attr('disabled','disabled');

        });
        $(document).ready(function() {
            $('#select-master-project').select2({
                placeholder: "Select Mater Project",
                allowClear: true,
                width: '100%', // Add this line
                language: {
                    noResults: function() {
                        return "Select Emirate First";
                    }
                }
            }).on('select2:open', function() {
                // Check if no results message exists and add class
                setTimeout(function() {
                    var $noResults = $('.select2-results__message');
                    if($noResults.length > 0) {
                        $noResults.addClass('no-data-found');
                    }
                }, 100);
            });
        });
        $(document).ready(function() {
            $('#Community').select2({
                placeholder: "Select Project",
                allowClear: true,
                width: '100%', // Add this line
                language: {
                    noResults: function() {
                        return "Select Master Project First";
                    }
                }
            }).on('select2:open', function() {
                // Check if no results message exists and add class
                setTimeout(function() {
                    var $noResults = $('.select2-results__message');
                    if($noResults.length > 0) {
                        $noResults.addClass('no-data-found');
                    }
                }, 100);
            });
        });
    </script>
@endsection
