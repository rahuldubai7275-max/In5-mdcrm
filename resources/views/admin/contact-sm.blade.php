
@extends('layouts/contentLayoutMaster')

@section('title', 'Contacts')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
    <style>
        a.ui-input-clear.ui-btn.ui-icon-delete.ui-btn-icon-notext.ui-corner-all.ui-input-clear-hidden {
            display: none;
        }
    </style>
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
                    <li><a data-action="collapse"><i class="feather icon-chevron-up"></i></a></li>
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
                                    <select class="form-control select2" id="select-client-manager" name="client_manager">
                                        <option value="">Select</option>
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-client-manager">Client Manager 2</label>
                                    <select class="form-control select2" id="select-client-manager-2" name="client_manager_2">
                                        <option value="">Select</option>
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
                                <div class="form-group form-label-group">
                                    <label for="deal-contact">Our Deals</label>
                                    <select class="custom-select form-control" id="deal-contact" name="deal_contact">
                                        <option value="">Select</option>
                                        <option value="1">Rental</option>
                                        <option value="2">Sales</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
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
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-contact-categories">Contact Categories</label>
                                    <select class="form-control" id="select-contact-categories" name="contact_categories">
                                        <option value="">Select</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="tenant">Tenant</option>
                                        <option value="agent">Agent</option>
                                        <option value="owner">Owner</option>
                                        <option value="developer">Developer</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="select-developer">Developer</label>
                                    <select class="custom-select form-control select2" id="select-developer" name="select_developer">
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
                                <fieldset class="form-group form-label-group">
                                    <label for="select-color">Last Activity</label>
                                    <select class="form-control" id="select-color" name="select_color">
                                        @php
                                            $activity_contact_setting_2=\App\Models\Setting::where('id',8)->first();
                                            $activity_contact_setting_3=\App\Models\Setting::where('id',9)->first();
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
                                    <label for="select-finance-status">Finance Status</label>
                                    <select class="form-control" id="select-finance-status" name="finance_status">
                                        <option value="">Select</option>
                                        <option value="Cash Purchaser">Cash Purchaser</option>
                                        <option value="Mortgage Purchaser">Mortgage Purchaser</option>
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
                            <div class="col-sm-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-master-project">Master Project</label>
                                    <select class="form-control  select2" id="select-master-project" name="master_project">
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-sm-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="Community">Project</label>
                                    <select class="form-control select2" id="Community" name="community">
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
                                    <select class="custom-select form-control" id="PropertyType" name="property_type">
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="Bedrooms">Bedrooms</label>
                                    <select class="custom-select form-control" id="Bedroom" name="bedroom">
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
                                <div class="form-group form-label-group">
                                    <input type="number" id="ref-number" name="id" autocomplete="off" class="form-control" placeholder="Ref Number">
                                    <label for="ref-number">Ref Number</label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-contact-source">Contact Source</label>
                                    <select class="form-control select2" id="select-contact-source" name="contact_source">
                                        <option value="">Select</option>
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
                            @endif

                            <input type="hidden" name="contact" value="{{(request("c")) ? request("c") : "contacts"}}">
                            <div class="col-sm-{{($admin->type==1) ? '9':'12'}}">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light {{($admin->type==1) ? 'float-right':'w-100'}}" id="search">Search</button>
                                @if($admin->type==1)<button type="submit" class="btn btn-export bg-gradient-info waves-effect waves-light float-left">Export</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('contact-action')}}">
        @csrf

        <div class="card action-card">
            <div class="card-content collapse show">
                <div class="card-body card-dashboard p-1">
                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-6">
                                    <a class="btn bg-gradient-info py-1 px-2 waves-effect waves-light w-100" data-ajax="false" href="/admin/add-contacts">Add</a>
                                </div>
                                <div class="col-6">
                                    <a class="btn bg-gradient-info py-1 px-0 waves-effect waves-light w-100 disabled btn-sand-mail" data-toggle="modal" href="#modalSendMail">Send Email</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 assign-to-list d-none">
                            @if($admin->type<3)
                                <fieldset class="form-group form-label-group assign-to mt-2" style="min-width:180px">
                                    <label for="admin">Client Manager 1</label>
                                    <select class="form-control select2" id="ClientManager" name="ClientManager">
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
                                    <label for="DeveloperProject">Developer Projects</label>
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
    @include('admin/history')


    <div data-v-8314f794="" class="btn-scroll-to-top"><button data-v-8314f794="" type="button" class="btn btn-icon btn-primary" style="position: relative;"><svg data-v-8314f794="" xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line data-v-8314f794="" x1="12" y1="19" x2="12" y2="5"></line><polyline data-v-8314f794="" points="5 12 12 5 19 12"></polyline></svg></button></div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $('#data-box').on('click','.preview-category a',function(){
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
                            url: "{{route('contact-action')}}",
                            type: "POST",
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                contact: selected,
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

        $('#private').change(function(){
            if($(this).val()=='1'){
                $('#select-client-manager , #select-client-manager-2').attr('disabled','disabled');
            }else{
                $('#select-client-manager , #select-client-manager-2').removeAttr('disabled');
            }
            $('#select-client-manager , select-client-manager-2').val('').change();
        });

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
                    $('#select-master-project').html(response);
                }
            });
        }

        $('#select-master-project').change(function () {
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
                    $('#Community').html(response);
                }
            });
        }

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
    </script>

    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="/js/scripts/jquery.lazyloadxt.js"></script>
    <script>
        let contact='{{(request("c")) ? request("c") : "contacts"}}';
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
                url: '{{ route('contacts.get.data-sm') }}',
                type: "POST",
                data: {
                    contact: contact,
                    _token: $('form input[name="_token"]').val(),
                    start: start,
                    client_manager:$('#select-client-manager').val(),//.join(","),
                    client_manager_tow:$('#select-client-manager-2').val(),//.join(","),
                    private:$('#private').val(),
                    // creator: (alt==1) ? $('#creator').val().join(",") : '',
                    finance_status: $('#select-finance-status').val(),
                    contact_categories: $('#select-contact-categories').val(),//.join(","),
                    looking_for: $('#select-looking-for').val(),
                    select_color: $('#select-color').val(),
                    first_name: $('#first-name').val(),
                    email_address: $('#email-address').val(),
                    contact_number: $('#contact-number').val(),
                    emirate: $('#emirate').val(),
                    master_project: $('#select-master-project').val(),//.join(","),
                    community: $('#Community').val(),//.join(","),
                    p_type: $('#P_Type').val(),
                    property_type: $('#PropertyType').val(),//.join(","),
                    bedroom: $('#Bedroom').val(),//.join(","),
                    id: $('#ref-number').val(),
                    budget_from: $('#budget-from').val(),
                    budget_to: $('#budget-to').val(),
                    contact_source: $('#select-contact-source').val(),//.join(","),
                    select_developer: $('#select-developer').val(),//.join(","),
                    deal_contact: $('#deal-contact').val(),
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

        $('body').on('click','#data-box .hold-box .card-info',function(){
            if(!$('#data-box').hasClass('selected-lest')) {
                let id=$(this).data('id');
                window.open('/admin/contact/view/' + id);
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
                $('.btn-sand-mail').removeClass('disabled');
                $('#data-box').addClass('selected-lest');
                $("#data-box input:checkbox[name='contact[]']:checked").each(function(){
                    checked.push($(this).val());
                });
            } else {
                $('.assign-to-list').addClass('d-none');
            $('.btn-sand-mail').addClass('disabled');
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
