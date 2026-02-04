
@extends('layouts/contentLayoutMaster')

@section('title', 'Properties')

@section('vendor-style')
    {{-- vendor css files --}}
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
                    <li><a data-action="collapse"><i class="feather icon-chevron-up"></i></a></li>
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
                                    <select class="form-control select2" id="client-manager" name="client_manager">
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
                                    <select class="form-control select2" id="client-manager-2" name="client_manager_2">
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
                                    <select class="form-control select2" id="property-type" name="property_type">
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="status">Listing Status</label>
                                    <select class="form-control select2"  id="status" name="status">
                                        <option value="">Select</option>
                                        <option value="pf_error">PF Error</option>
                                        @foreach(Status as $key => $value)
                                            <option value="{{ $key }}">{{ $value.( ($key==5 || $key==7)? $company->sample: '') }}</option>
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
                                    <select class="form-control  select2" id="master-project" name="master_project">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="community">Project</label>
                                    <select class="form-control  select2" id="community" name="community">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="bedrooms">Bedrooms</label>
                                    <select class="form-control select2" id="bedrooms" name="bedrooms">
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
                                        <option value="Green">Less than {{$activity_contact_setting_2->time}} days (Green)</option>
                                        <option value="Yellow">Between {{$activity_contact_setting_2->time}} to {{$activity_contact_setting_3->time}} days (Yellow)</option>
                                        <option value="Red">More than {{$activity_contact_setting_3->time}} days (Red)</option>
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
                                    <select class="custom-select form-control select2" id="status2" name="status2">
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
                                    <input type="number" id="ref-number" name="ref_number" autocomplete="off" class="form-control" placeholder="Ref Number">
                                    <label for="ref-number">Ref Number</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label for="VillaNumber">Villa / Unit Number</label>
                                    <input type="text" id="unit-villa-number" name="unit_villa_number" class="form-control" placeholder="Villa / Unit Number">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="rera-permit" name="rera_permit" placeholder="DLD Permit Number" aria-invalid="false">
                                    <label>DLD Permit Number</label>
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
                                    <select class="form-control select2" id="creator" name="creator">
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
                            <div class="col-sm-2">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light {{($admin->type==1) ? 'float-right':'w-100'}}" id="search">Search</button>
                                    @if($admin->type==1)<button type="submit" class="btn bg-gradient-info waves-effect waves-light float-left">Export</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('property-action')}}">
        @csrf
        <div class="card action-card">
            <div class="card-content collapse show">
                <div class="card-body card-dashboard p-1">
                    <div class="row">
                        <div class="col-12">
                            <a href="/admin/property" data-ajax="false" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light w-100">Add Property</a>
                        </div>
                        <div class="col-12 assign-to-list d-none">
                            @if($admin->type<3)
                                <fieldset class="form-group form-label-group mt-2 mt-md-0" style="min-width:180px">
                                    <label for="admin">Client Manager 1</label>
                                    <select class="form-control select2 assign-to" id="ClientManager" name="ClientManager">
                                        <option value="">Select</option>
                                        @foreach($ClientManagers as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            @endif
                            <fieldset class="form-group form-label-group mb-0" style="min-width:180px">
                                <label for="admin">Client Manager 2</label>
                                <select class="form-control select2 assign-to" id="AssignTo" name="AssignTo">
                                    <option value="">Select</option>
                                    <option value="null">Select</option>
                                    @foreach($ClientManagers as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="data-box"></div>
        <div id="marker-end"></div>

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
    @include('admin/history')

    <div data-v-8314f794="" class="btn-scroll-to-top"><button data-v-8314f794="" type="button" class="btn btn-icon btn-primary" style="position: relative;"><svg data-v-8314f794="" xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line data-v-8314f794="" x1="12" y1="19" x2="12" y2="5"></line><polyline data-v-8314f794="" points="5 12 12 5 19 12"></polyline></svg></button></div>
@endsection
@section('vendor-script')
    {{-- vendor files--}}

@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
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

            $("#data-box input[type=checkbox]:checked").each(function () {
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
                            url: "{{route('property-action')}}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                property: selected,
                                AssignTo: $('#AssignTo').val(),
                                ClientManager: $('#ClientManager').val()
                            },
                            success: function (response) {
                                $('#search').click();
                                $('#data-box input[type=checkbox]:checked').prop('checked', false);
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

        $('select , input').change(function () {
            $('.export-btn').removeAttr('disabled');
        });

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
                    $('#community').html(response);
                }
            });
        }

    </script>

    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="/js/scripts/jquery.lazyloadxt.js"></script>
    <script>
        let property='{{(request("p")) ? request("p") : "properties"}}';
        let alt='{{$admin->type}}';
        let start = 0;
        let scrollPosition = 0;
        let selectItem='';
        let search=0;

        $('#marker-end').on('lazyshow', function () {
            if(search===1){
                start  = start + 3;
                search=0;
            }
            getData();
            start  = start + 3;
            $(window).lazyLoadXT();
            $('#marker-end').lazyLoadXT({visibleOnly: false, checkDuplicates: false});
        }).lazyLoadXT({visibleOnly: false});

        function getData() {
            $.ajax({
                url: '{{ route('property.get.data-sm') }}',
                type: "POST",
                data: {
                    property: property,
                    _token: $('form input[name="_token"]').val(),
                    start: start,
                    listing: $('#listing').val(),
                    status: $('#status').val(),//.join(","),
                    type: $('#type').val(),
                    property_type: $('#property-type').val(),//.join(","),
                    client_manager: $('#client-manager').val(),//.join(","),
                    client_manager_2: $('#client-manager-2').val(),//.join(","),
                    creator: (alt==1) ? $('#creator').val() : '',
                    emirate:$('#emirate').val(),
                    master_project: $('#master-project').val(),//.join(","),
                    community: $('#community').val(),//.join(","),
                    bedrooms: $('#bedrooms').val(),//.join(","),
                    off_plan: $('#off_plan').val(),
                    unit_villa_number: $('#unit-villa-number').val(),
                    rent_price: $('#rent-price').val(),
                    status2: $('#status2').val(),//.join(","),
                    from_price: $('#from-price').val(),
                    to_price: $('#to-price').val(),
                    id: $('#ref-number').val(),
                    portal: $('#portal').val(),
                    property_management: $('#property_management').val(),
                    rera_permit: $('#rera-permit').val(),
                    select_color: $('#select-color').val(),
                    from_date: $('#from-date').val(),
                    to_date: $('#to-date').val()
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    if(start===0)
                        $('#data-box').html(obj.aaData);
                    else
                        $('#data-box').append(obj.aaData);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        $('#search').click(function(){
            search=1;
            start=0;
            getData();
        });

        $('body').on('click','#data-box .hold-box',function(){
            if(!$('#data-box').hasClass('selected-lest')) {
                let id=$(this).data('id');
                if ((property == 'RL' || property == 'rfl') && alt < 3)
                    window.open('/admin/property-edit/' + id);
                else
                    window.open('/admin/property/view/' + id);
            }
        });

        $('body').on('click','.selected-lest .hold-box',function(){
            if($(this).hasClass('select')) {
                $(this).removeClass('select');
                $(this).children('input[type=checkbox]').prop('checked', false);
                active_assign();
            }else{
                selectItem = $(this);
                select_card();
            }
        });

        $('body #data-box').on('taphold','.hold-box',function(){
            selectItem=$(this);
            select_card();
        });

        function select_card() {
            selectItem.addClass('select');
            selectItem.children('input[type="checkbox"]').prop('checked', true);
            active_assign()
        }

        function active_assign(){
            var checkboxes = $('#data-box input[type="checkbox"]').filter(":checked").length;
            var checked=[];
            if (checkboxes != 0) {
                $('.assign-to-list').removeClass('d-none');
                $('#data-box').addClass('selected-lest');
                $("#data-box input:checkbox[name='contact[]']:checked").each(function(){
                    checked.push($(this).val());
                });
            } else {
                $('.assign-to-list').addClass('d-none');
                $('#data-box').removeClass('selected-lest');
            }

            $('#email_submit').val(checked.join());
        }

        window.addEventListener("scroll", (event) => {
            scrollPosition = $(window).scrollTop();
            if (scrollPosition >= 100){
                $('.action-card').addClass('card-fix');
            }else{
                $('.action-card').removeClass('card-fix');
            }
        });
    </script>
@endsection
