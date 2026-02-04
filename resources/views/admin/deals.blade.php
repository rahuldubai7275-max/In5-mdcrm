
@extends('layouts/contentLayoutMaster')

@section('title', 'Deals')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">

@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')
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
                    <form>
                        <div class="row mt-1">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-agent">Clieant Manager</label>
                                    <select class="form-control select2" id="select-agent">
                                        <option value="0">Select</option>
                                        @php
                                            $Agents=\Helper::getCM_DropDown_list('1');
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-type">Deal Type</label>
                                    <select class="form-control" id="select-type">
                                        <option value="">Select</option>
                                        <option value="1">Rental</option>
                                        <option value="2">Sales</option>
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

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="master-project">Master Project</label>
                                    <select class="form-control  select2" multiple  id="master-project">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="community">Project</label>
                                    <select class="form-control  select2" multiple  id="community">
                                        <option value="">Select</option>

                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-Status">Deal Status</label>
                                    <select class="form-control" id="select-status">
                                        <option value="">Select</option>
                                        <option value="1">Done</option>
                                        <option value="2">Deleted</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-source">Contact Source</label>
                                    <select class="form-control select2" id="select-source">
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
                                    <label for="set_reminder">Reminder</label>
                                    <select class="form-control" id="select-reminder">
                                        <option value="">Select</option>
                                        <option value="7">100 Days in advance</option>
                                        <option value="6">3 Months in advance</option>
                                        <option value="5">2 Months in advance</option>
                                        <option value="4">1 Month in advance</option>
                                        <option value="3">1 Week in advance</option>
                                        <option value="2">1 Day in advance</option>
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
                                    <input type="number" id="ref-number" autocomplete="off" class="form-control" placeholder="Ref Number">
                                    <label for="ref-number">Deal Ref Number</label>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <input type="text" id="buyer-tenant" autocomplete="off" class="form-control" placeholder="Buyer's / Tenant's Name">
                                    <label for="first-name">Buyer's / Tenant's Name</label>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label for="VillaNumber">Villa / Unit Number</label>
                                    <input type="text" id="unit-villa-number" class="form-control" placeholder="Villa / Unit Number">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label>Commission (AED)</label>
                                    <input type="text" id="from-commission" class="form-control number-format" placeholder="From">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group">
                                    <label>Commission (AED)</label>
                                    <input type="text" id="to-commission" class="form-control number-format" placeholder="To">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Price (AED)</label>
                                            <input type="text" id="from-price" class="form-control number-format" placeholder="From">
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Price (AED)</label>
                                            <input type="text" id="to-price" class="form-control number-format" placeholder="To">
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

                            <div class="col-12">
                                <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Deals</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    @php
                      $deal_access=2;
                      if(env('DEAL_ACCESS')!='0')
                          $deal_access=5;
                    @endphp
                    @if($adminAuth->type<=$deal_access)
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-light" href="/admin/add-deal">New Deal</a></li>
                    @endif
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard pt-3">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>Ref</th>
                        <th>Deal Type</th>
                        <th>Property Address</th>
                        <th>Buyer / Tenant</th>
                        <th>Price (AED)</th>
                        <th>CM</th>
                        <th>Deal Date</th>
                        <th>Added Date</th>
                        <th>Commission (AED)</th>
                        <th>Source</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="disabledDealModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('deal.disabled')}}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel4">Delete</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <fieldset class="form-label-group">
                        <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Reason"></textarea>
                        <label for="label-textarea">Reason</label>
                    </fieldset>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancel</button>
                    <input type="hidden" id="_id" name="_id" value="">
                    <button type="submit" class="btn btn-danger" id="disabled" name="disabled">Delete</button>
                </div>
            </form>
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

    <script>
        $('#select-reminder').val('{{(request("d")) ? request("d") : ""}}');
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
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 0, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('deals.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    // data.contact = 'contacts';
                    data._token='{{csrf_token()}}';
                    data.id=$('#ref-number').val();
                    data.type=$('#select-type').val();
                    data.status=$('#select-status').val();
                    data.from_price=$('#from-price').val();
                    data.to_price=$('#to-price').val();
                    data.agent=$('#select-agent').val();
                    data.buyer_tenant=$('#buyer-tenant').val();
                    data.emirate=$('#emirate').val();
                    data.master_project=$('#master-project').val().join(",");
                    data.community=$('#community').val().join(",");
                    data.unit_villa_number=$('#unit-villa-number').val();
                    data.from_commission=$('#from-commission').val();
                    data.to_commission=$('#to-commission').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                    data.source=$('#select-source').val();
                    data.reminder=$('#select-reminder').val();
                    data.property_management=$('#property_management').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 2,5,10 ]}],
            'columns': [
                {data: 'id'},
                {data: 'type'},
                {data: 'property_address'},
                {data: 'buyer_Tenant'},
                {data: 'deal_price'},
                {data: 'agent'},
                {data: 'deal_date'},
                {data: 'created_at'},
                {data: 'commission'},
                {data: 'contact_source'},
                {data: 'action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if(!html)
                html=$(this).children('.rent-price').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                if(id!=undefined) {
                    window.open('/admin/deal-view/' + id);
                }
            }

        });
    </script>

    <script>
        $('#emirate').change(function () {
            let val=$(this).val();
            getMasterProject(val);
        });

        $('#emirate').val(2).change();

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
        $('body').on('click','.action .deal-disabled',function(){
            $('#reason').val('').click();
            $('#_id').val( $(this).parent().data('id') );
        });

        $('body').on('click','.action .acknowledge', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('acknowledge');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You want to Acknowledge!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes, Acknowledge!',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="_id">');
                    $('.delete-form-box form').append('<input type="submit" value="'+id+'" name="acknowledge">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input').click();
                }
            })
        });

    </script>
@endsection
