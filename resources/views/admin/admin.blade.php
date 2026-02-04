
@extends('layouts/contentLayoutMaster')

@section('title', 'Users')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();

    $EU_setting_1=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_1')->first();
    $EU_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_2')->first();
    $EU_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_3')->first();
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
                                <div class="form-group form-label-group validate">
                                    <label for="first_name">First Name</label>
                                    <input type="text" id="first_name" class="form-control" placeholder="First Name">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group rented-until-box">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" id="last_name" class="form-control" placeholder="Last Name">
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-type">Access Level</label>
                                    <select class="form-control" id="select-type">
                                        <option value="">Select</option>
                                        @foreach(AdminType as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-status">Status</label>
                                    <select class="form-control" id="select-status">
                                        <option value="">Select</option>
                                        <option value="1">Active</option>
                                        <option value="2">Deactive</option>
                                    </select>
                                </fieldset>
                            </div>

                            @php
                            $option='<option value="Red">Less then '.$EU_setting_3->time.' days (Red)</option>
                            <option value="Yellow">Between '.$EU_setting_3->time.' to '.$EU_setting_2->time.' days (Yellow)</option>
                            <option value="Green">Between '.$EU_setting_2->time.' to '.$EU_setting_1->time.' days (Green)</option>';
                            @endphp

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-visa-expiry">Visa Expiry</label>
                                    <select class="form-control" id="select-visa-expiry">
                                        <option value="">Select</option>
                                        {!! $option !!}
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-insurance-expiry">Insurance Expiry</label>
                                    <select class="form-control" id="select-insurance-expiry">
                                        <option value="">Select</option>
                                        {!! $option !!}
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-rera-card-expiry">RERA Card Expiry</label>
                                    <select class="form-control" id="select-rera-card-expiry">
                                        <option value="">Select</option>
                                        {!! $option !!}
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-labour-card-expiry">Labour Card Expiry</label>
                                    <select class="form-control" id="select-labour-card-expiry">
                                        <option value="">Select</option>
                                        {!! $option !!}
                                    </select>
                                </fieldset>
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
            <h4 class="card-title">Users</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    {!!$add_btn!!}
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @php
                $today=date('Y-m-d');

                $expirationVisa_1=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('visa_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days"))])->count();
                $expirationVisa_2=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('visa_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days"))])->count();
                $expirationVisa_3=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->where('visa_expiration_date','<',  date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")))->count();

                $expirationInsurance_1=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('insurance_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days"))])->count();
                $expirationInsurance_2=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('insurance_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days"))])->count();
                $expirationInsurance_3=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->where('insurance_expiration_date','<',  date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")))->count();

                $expirationLabourCard_1=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('labour_card_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days"))])->count();
                $expirationLabourCard_2=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('labour_card_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days"))])->count();
                $expirationLabourCard_3=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->where('labour_card_expiration_date','<',  date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")))->count();

                $expirationReraCard_1=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('rera_card_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days"))])->count();
                $expirationReraCard_2=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->whereBetween('rera_card_expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EU_setting_2->time." days"))])->count();
                $expirationReraCard_3=App\Models\Admin::where('company_id', $adminAuth->company_id)->where('status', 1)->where('rera_card_expiration_date','<',  date('Y-m-d',strtotime($today. "+ ".$EU_setting_3->time." days")))->count();
                @endphp
                <div class="table-responsive">
                    <table class="table truncate-table datatable1 table-striped order-column dataTable">
                        <thead>
                        <tr>
                            <th></th>
                            <th>FirstName</th>
                            <th>LastName</th>
                            <th>Access Level</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Date of join</th>
                            <th>Visa Expiry
                                {!! ($expirationVisa_1) ? '<span class="badge badge badge-pill badge-success">'.$expirationVisa_1.'</span>' : ''  !!}
                                {!! ($expirationVisa_2) ? '<span class="badge badge badge-pill badge-warning">'.$expirationVisa_2.'</span>' : ''  !!}
                                {!! ($expirationVisa_3) ? '<span class="badge badge badge-pill badge-danger">'.$expirationVisa_3.'</span>' : ''  !!}
                            </th>
                            <th>Insurance Expiry
                                {!! ($expirationInsurance_1) ? '<span class="badge badge badge-pill badge-success">'.$expirationInsurance_1.'</span>' : ''  !!}
                                {!! ($expirationInsurance_2) ? '<span class="badge badge badge-pill badge-warning">'.$expirationInsurance_2.'</span>' : ''  !!}
                                {!! ($expirationInsurance_3) ? '<span class="badge badge badge-pill badge-danger">'.$expirationInsurance_3.'</span>' : ''  !!}
                            </th>
                            <th>RERA Card Expiry
                                {!! ($expirationReraCard_1) ? '<span class="badge badge badge-pill badge-success">'.$expirationReraCard_1.'</span>' : ''  !!}
                                {!! ($expirationReraCard_2) ? '<span class="badge badge badge-pill badge-warning">'.$expirationReraCard_2.'</span>' : ''  !!}
                                {!! ($expirationReraCard_3) ? '<span class="badge badge badge-pill badge-danger">'.$expirationReraCard_3.'</span>' : ''  !!}
                            </th>
                            <th>Labour Card Expiry
                                {!! ($expirationLabourCard_1) ? '<span class="badge badge badge-pill badge-success">'.$expirationLabourCard_1.'</span>' : ''  !!}
                                {!! ($expirationLabourCard_2) ? '<span class="badge badge badge-pill badge-warning">'.$expirationLabourCard_2.'</span>' : ''  !!}
                                {!! ($expirationLabourCard_3) ? '<span class="badge badge badge-pill badge-danger">'.$expirationLabourCard_3.'</span>' : ''  !!}
                            </th>
                            <th>Added Date</th>
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

    <!-- Modal Change Password -->
    <div class="modal fade" id="ModalChangePassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('admin.change.password')  }}" class="modal-content" novalidate>
                {!! csrf_field() !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-md-12">
                            <div class="form-group form-label-group">
                                <div class="controls">
                                    <input type="text" class="form-control required" id="password" name="password" data-validation-required-message="The min field must be at least 6 characters." minlength="6" placeholder="Password" required>
                                    <div class="help-block"></div>
                                </div>
                                <label for="password"></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="changePassword" id="changePassword">
                    <button type="submit" class="btn bg-gradient-info waves-effect waves-light">Change Password</button>
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
    {{-- Page js files
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
    <script>
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
            "order": [[ 1, "asc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('admins.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    data.target = '{{request('period')}}';
                    data._token='{{csrf_token()}}';
                    data.first_name=$('#first_name').val();
                    data.last_name=$('#last_name').val();
                    data.type=$('#select-type').val();
                    data.status=$('#select-status').val();
                    data.visa_expiry=$('#select-visa-expiry').val();
                    data.insurance_expiry=$('#select-insurance-expiry').val();
                    data.rera_card_expiry=$('#select-rera-card-expiry').val();
                    data.labour_card_expiry=$('#select-labour-card-expiry').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 0,12 ]}],
            'columns': [
                {data: 'pic_name'},
                {data: 'firstname'},
                {data: 'lastname'},
                {data: 'type'},
                {data: 'email'},
                {data: 'status'},
                {data: 'date_joined'},
                {data: 'visa_expiration_date'},
                {data: 'insurance_expiration_date'},
                {data: 'rera_card_expiration_date'},
                {data: 'labour_card_expiration_date'},
                {data: 'created_at'},
                {data: 'action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('body tbody').on('click','.change-password',function(){
            let id = $(this).parent().data('id');
            $('#changePassword').val(id);
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                // window.location.href ='/admin/contact/view/'+id
                if(id!=undefined) {
                    window.open('/admin/admin-profile/' + id);
                }
            }

        });

        @php
            $admin = Auth::guard('admin')->user();
            if($admin->type==2)
                  echo "$('.change-password').val().attr('disabled','disabled');";
        @endphp
    </script>
@endsection
