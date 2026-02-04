
@extends('layouts/contentLayoutMaster')

@section('title', 'Leads')

@section('vendor-style')
    {{-- vendor css files --}}
    {{--<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">--}}

    <style>
        .picker {
            min-width: 250px;
        }
        .rented-until-box .picker , .available-from-box .picker , .expiration-date-box .picker {
            right: 0;
        }
    </style>

@endsection

@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    @php
        $admin=\Auth::guard('admin')->user();
    @endphp
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
                    <form action="{{route('leads-export')}}">
                        <div class="row">
                            {{--@if($admin->type<3)--}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="client-manager">Client Manager</label>
                                    <select class="form-control select2" id="client-manager" name="client_manager">
                                        <option value="0">Select</option>
                                        @php
                                        if($admin->super==1)
                                            $ClientManagers=\Helper::getCM_DropDown_list();
                                        else
                                            $ClientManagers=\Helper::getCM_DropDown_list('1');
                                        @endphp
                                        @foreach($ClientManagers as $ClientManager)
                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                        @endforeach
                                        @if($admin->type<3) <option value="null">Not Assigned</option> @endif
                                    </select>
                                </fieldset>
                            </div>
                            {{--@endif--}}
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="type">Lead Type</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="">Select</option>
                                        @foreach(LeadType as $kay=>$value)
                                        <option value="{{$kay}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="status">Lead Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select</option>
                                        <option value="1">Added To Contact</option>
                                        <option value="2">Closed</option>
                                        @if($admin->type==1)<option value="3">Deleted</option>@endif
                                        <option value="0">Open</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-contact-source">Lead Source</label>
                                    <select class="form-control select2" id="source" name="source">
                                        <option value="0">Select</option>
                                        @php
                                            $ContactSources=App\Models\ContactSource::orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($ContactSources as $CSource)
                                            <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
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
                                <fieldset class="form-group form-label-group">
                                    <label for="contact-categories">Contact Categories</label>
                                    <select class="form-control" id="contact-categories" name="contact_category">
                                        <option value="">Select</option>
                                        <option value="buyer">Buyer</option>
                                        <option value="tenant">Tenant</option>
                                        <option value="agent">Agent</option>
                                        <option value="owner">Owner</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="contact-categories">Reason For Closing</label>
                                    <select class="form-control" id="reason" name="reason">
                                        <option value="">Select</option>
                                        @foreach(LeadClosedReason as $reason)
                                            <option value="{{$reason}}">{{$reason}}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="master_project">Master Project</label>
                                    <select class="custom-select form-control select2" id="master_project" name="master_project">
                                        <option value="0">Select</option>
                                        @php
                                            $MasterProjects=App\Models\MasterProject::orderBy('name','ASC')->get();
                                        @endphp
                                        @foreach($MasterProjects as $MasterProject)
                                            <option value="{{ $MasterProject->id }}">{{ $MasterProject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" class="form-control" placeholder="Name">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="mobile-number">Contact number</label>
                                    <input type="number" id="mobile-number" name="mobile-number" autocomplete="off" class="form-control" placeholder="Contact number">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="email">Email</label>
                                    <input type="text" id="email" name="email" autocomplete="off" class="form-control" placeholder="Email">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="from-date">ENQ Date</label>
                                    <input type="text" id="from-date" name="from_date" autocomplete="off" class="form-control format-picker picker__input" placeholder="From">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="to-date">ENQ Date</label>
                                    <input type="text" id="to-date" name="to_date" autocomplete="off" class="form-control format-picker picker__input" placeholder="To">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="ref-number-property">Property Ref</label>
                                            <input type="number" id="ref-number-property" name="ref-number-property" autocomplete="off" class="form-control" placeholder="Property Ref">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="ref-number">Lead Ref</label>
                                            <input type="number" id="ref-number" name="ref_number" autocomplete="off" class="form-control" placeholder="Lead Ref">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" id="search" class="btn bg-gradient-info waves-effect waves-light {{($admin->type==1) ? 'float-right':'w-100'}}">Search</button>
                                @if($admin->type==1)<button type="submit" class="btn bg-gradient-info waves-effect waves-light float-left">Export</button>@endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form method="post" action="{{route('lead-action')}}">
       @csrf
        <div class="card action-card">
            <div class="card-content collapse show">
                <div class="card-body card-dashboard p-1">
                    <div class="row">
                        <div class="col-12">
                            <a href="/admin/lead" data-ajax="false" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light w-100">Add Lead</a>
                        </div>
                        <div class="col-12 assign-to-list d-none">
                            <fieldset class="form-group form-label-group mt-2 mt-md-0 mb-0" style="min-width:180px">
                                <label for="admin">Assign To</label>
                                <select class="form-control select2" id="LeadAssignTo" name="AssignTo">
                                    <option value="">Select</option>
                                    @php
                                        $Agents=$ClientManagers=\Helper::getCM_DropDown_list('1');
                                    @endphp
                                    @foreach($Agents as $agent)
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

    <div data-v-8314f794="" class="btn-scroll-to-top"><button data-v-8314f794="" type="button" class="btn btn-icon btn-primary" style="position: relative;"><svg data-v-8314f794="" xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line data-v-8314f794="" x1="12" y1="19" x2="12" y2="5"></line><polyline data-v-8314f794="" points="5 12 12 5 19 12"></polyline></svg></button></div>
@endsection
@section('vendor-script')
{{-- vendor files --}}
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        $('#LeadAssignTo').change(function(){
            var val = [];
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
                            url: "{{route('lead-action')}}",
                            type: "POST",
                            data: {
                                _token: $('form input[name="_token"]').val(),
                                lead: selected,
                                AssignTo: $('#LeadAssignTo').val()
                            },
                            success: function (response) {
                                $('#search').click();
                                $('#data-box input[type=checkbox]:checked').prop('checked', false);
                                $('.assign-to-list').addClass('d-none');
                                $('#LeadAssignTo').val('').change();
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
    </script>

    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="/js/scripts/jquery.lazyloadxt.js"></script>
    <script>
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
                url: '{{ route('leads.get.data-sm') }}',
                type: "POST",
                data: {
                    '_token': $('form input[name="_token"]').val(),
                    'start': start,
                    'leads':'{{request('new') ? 'new':''}}',
                    'status': $('#status').val(),
                    'client_manager': $('#client-manager').val(),
                    'source': $('#source').val(),
                    'private': $('#private').val(),
                    'type': $('#type').val(),
                    'contact_category': $('#contact-categories').val(),
                    'master_project': $('#master_project').val(),
                    'ref_number': $('#ref-number').val(),
                    'ref_number_property': $('#ref-number-property').val(),
                    'name': $('#name').val(),
                    'mobile_number': $('#mobile-number').val(),
                    'email': $('#email').val(),
                    'reason': $('#reason').val(),
                    'from_date': $('#from-date').val(),
                    'to_date': $('#to-date').val()
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

        $('#private').change(function(){
            if($(this).val()=='1'){
                $('#client-manager').attr('disabled','disabled');
            }else{
                $('#client-manager').removeAttr('disabled');
            }
            $('#client-manager').val('').change();
        });

        $('body').on('click','#data-box .hold-box',function(){
            if(!$('#data-box').hasClass('selected-lest')) {
                let id=$(this).data('id');
                window.open('/admin/lead/view/' + id);
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
