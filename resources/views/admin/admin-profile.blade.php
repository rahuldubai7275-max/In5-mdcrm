
@extends('layouts/contentLayoutMaster')

@section('title', 'Users Details')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- account start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Profile</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="users-view-image" style="width: 100px">
                            <img src="{{ ($admin && $admin->pic_name) ? '/storage/'.$admin->pic_name : '/images/Defult2.jpg'}}" class="users-avatar-shadow w-100 rounded mb-2 pr-2 ml-1" alt="avatar">
                        </div>
                        <div class="col-12 col-sm-9 col-md-6 col-lg-5">
                            <div>
                                <p class="mb-0"><b>Full Name: </b>{{$admin->firstname.' '.$admin->lastname}}</p>
                            </div>
                            <div>
                                <p class="mb-0"><b>Role: </b>{{AdminType[$admin->type]}}</p>
                            </div>
                            <div>
                                <p class="mb-0"><b>Email: </b>{{$admin->email}}</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-12 col-lg-3">
                            <div>
                                <p class="mb-0"><b>Status: </b><span class="badge badge-pill badge-light-{{ ADMIN_STATUS_COLOR[$admin->status] }}">{{ADMIN_STATUS_NAME[$admin->status]}}</span></p>
                            </div>
                            <div>
                                <p class="mb-0"><b>Date of join: </b>{{($admin->date_joined) ? date('d/m/Y',strtotime($admin->date_joined)): ''}}</p>
                            </div>
                            <div>
                                <p class="mb-1"><b>Date of birth: </b>{{($admin->date_birth) ? date('d/m/Y',strtotime($admin->date_birth)): ''}}</p>
                            </div>
                        </div>
                        @if($admin->id==$adminAuth->id)
                            <div class="col-12 col-md-12 col-lg-2">
                                <a href="#ModalChangePassword" data-toggle="modal" class="btn btn-primary px-1">Change Password</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- account end -->

        <!-- Modal Change Password -->
        <div class="modal fade" id="ModalChangePassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
                <form method="post" action="{{ route('admin.own.change.password')  }}" class="modal-content" novalidate>
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
                                <div class="col-md-12">
                                    <div class="form-group form-label-group">
                                        <div class="controls">
                                            <input type="password" class="form-control required" id="oldPassword" name="oldPassword" data-validation-required-message="The min field must be at least 6 characters." minlength="6" placeholder="Old Password" required>
                                            <div class="help-block"></div>
                                        </div>
                                        <label for="password">Old Password</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group form-label-group">
                                        <div class="controls">
                                            <input type="password" class="form-control required" id="newPassword" name="newPassword" data-validation-required-message="The min field must be at least 6 characters." minlength="6" placeholder="New Password" required>
                                            <div class="help-block"></div>
                                        </div>
                                        <label for="password">New Password</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group form-label-group">
                                        <div class="controls">
                                            <input type="password" class="form-control required" id="confirmPassword" name="confirmPassword" data-validation-required-message="The min field must be at least 6 characters." minlength="6" placeholder="Confirm New Password" required>
                                            <div class="help-block"></div>
                                        </div>
                                        <label for="password">Confirm New Password</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-info waves-effect waves-light change_password_btn">Change Password</button>
                        <button type="submit" name="submit" class="d-none"></button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Salary start -->
        <div class="col-md-5" style="padding-bottom: 2.2rem">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">Salary</div>
                </div>
                <div class="card-body">
                    <div>
                        <p><b>Basic: </b>{{ number_format($admin->basic_salary) }}</p>
                    </div>
                    <div>
                        <p><b>Allowance: </b>{{number_format($admin->allowance_salary)}}</p>
                    </div>
                    <div>
                        <p><b>Total Salary: </b>{{number_format($admin->basic_salary+$admin->allowance_salary)}}</p>
                    </div>
                    <div>
                        <p><b>Commission: </b>{{($admin->commission==1) ? 'Yes' : 'No'}}</p>
                    </div>
                    <div>
                        <p><b>Payment Method: </b>{{($admin->payment_method) ? PAYMENT_METHOD[$admin->payment_method] : ''}}</p>
                    </div>
                    <div>
                        @php
                            $bank=\App\Models\Bank::find($admin->bank_id);
                        @endphp
                        <p><b>Bank: </b>{{($bank) ? $bank->name : ''}}</p>
                    </div>
                    <div>
                        <p><b>Account Number: </b>{{$admin->account_number}}</p>
                    </div>
                    <div>
                        <p><b class="mb-0">IBAN Number: </b>{{$admin->iban_number}}</p>
                    </div>
                    <div>
                        <p><b class="mb-0">Personal Labour ID Number: </b>{{$admin->labour_personal_id}}</p>
                    </div>
                    <div>
                        <p><b class="mb-0">Bank Routing Code: </b>{{$admin->bank_routing_code}}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Salary start -->

        <!-- Expiration date end -->
        <div class="col-md-3" style="padding-bottom: 2.2rem">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">Expiration Date</div>
                </div>
                <div class="card-body">

                    @php
                        $EU_setting_1=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_1')->first();
                        $EU_setting_2=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_2')->first();
                        $EU_setting_3=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','expiration_user_3')->first();

                        $today = date('Y-m-d');
                        $date_two = \Carbon\Carbon::parse($today);
                        $days = $date_two->diffInDays($admin->visa_expiration_date);
                        $visaColor='';
                        if($admin->visa_expiration_date<$today){
                            $visaColor = 'danger';
                        }else {
                            if ($days <= $EU_setting_1->time)
                                $visaColor = 'success';
                            if ($days <= $EU_setting_2->time)
                                $visaColor = 'warning';
                            if ($days <= $EU_setting_3->time)
                                $visaColor = 'danger';
                        }
                        $visa_expiration_date=($admin->visa_expiration_date) ? '<span class="text-'.$visaColor.'">'.date('d-m-Y',strtotime($admin->visa_expiration_date)).'</span>':'';

                        $days = $date_two->diffInDays($admin->insurance_expiration_date);
                        $insuranceColor='';
                        if($admin->insurance_expiration_date<$today){
                            $insuranceColor = 'danger';
                        }else {
                            if ($days <= $EU_setting_1->time)
                                $insuranceColor = 'success';
                            if ($days <= $EU_setting_2->time)
                                $insuranceColor = 'warning';
                            if ($days <= $EU_setting_3->time)
                                $insuranceColor = 'danger';
                        }
                        $insurance_expiration_date=($admin->insurance_expiration_date) ? '<span class="text-'.$insuranceColor.'">'.date('d-m-Y',strtotime($admin->insurance_expiration_date)).'</span>':'';

                        $days = $date_two->diffInDays($admin->rera_card_expiration_date);
                        $rera_cardColor='';
                        if($admin->rera_card_expiration_date<$today){
                            $rera_cardColor = 'danger';
                        }else {
                            if ($days <= $EU_setting_1->time)
                                $rera_cardColor = 'success';
                            if ($days <= $EU_setting_2->time)
                                $rera_cardColor = 'warning';
                            if ($days <= $EU_setting_3->time)
                                $rera_cardColor = 'danger';
                        }
                        $rera_card_expiration_date=($admin->rera_card_expiration_date) ? '<span class="text-'.$rera_cardColor.'">'.date('d-m-Y',strtotime($admin->rera_card_expiration_date)).'</span>':'';

                        $days = $date_two->diffInDays($admin->labour_card_expiration_date);
                        $labour_cardColor='';
                        if($admin->labour_card_expiration_date<$today){
                            $labour_cardColor = 'danger';
                        }else {
                            if ($days <= $EU_setting_1->time)
                                $labour_cardColor = 'success';
                            if ($days <= $EU_setting_2->time)
                                $labour_cardColor = 'warning';
                            if ($days <= $EU_setting_3->time)
                                $labour_cardColor = 'danger';
                        }
                        $labour_card_expiration_date=($admin->labour_card_expiration_date) ? '<span class="text-'.$labour_cardColor.'">'.date('d-m-Y',strtotime($admin->labour_card_expiration_date)).'</span>':'';

                    @endphp

                    <div>
                        <p><b>Visa: </b>{!! $visa_expiration_date !!}</p>
                    </div>
                    <div>
                        <p><b>Insurance: </b>{!! $insurance_expiration_date !!}</p>
                    </div>
                    <div>
                        <p><b>Rera Card: </b>{!! $rera_card_expiration_date !!}</p>
                    </div>
                    <div>
                        <p><b>Labour Card: </b>{!! $labour_card_expiration_date !!}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Expiration date end -->

        <!-- Documents start -->
        <div class="col-md-4" style="padding-bottom: 2.2rem">
            <div class="card h-100">
                <div class="card-header">
                    <div class="card-title">Documents</div>
                </div>
                <div class="card-body">
                    {!! ($admin->passport) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Passport: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->passport.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->emirates_id) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Emirates ID: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->emirates_id.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->labour_contract) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Labour Contract: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->labour_contract.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->labour_card) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Labour Card: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->labour_card.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->residents_visa) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Residents Visa: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->residents_visa.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->insurance) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Insurance: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->insurance.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->rera_card) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>RERA Card: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->rera_card.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->job_offer) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Job Offer: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->job_offer.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->contract_of_employment) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Contract Of Employment: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->contract_of_employment.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->cancellation) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Cancellation: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->cancellation.'" target="_blank">Download</a></div>' : '' !!}
                    {!! ($admin->other) ? '<div class="clearfix d-block mb-1"><p class="float-left m-0"><b>Other: </b></p><a class="btn btn-primary float-right font-small-2 px-1" href="/storage/'.$admin->other.'" target="_blank">Download</a></div>' : '' !!}
                </div>
            </div>
        </div>

        <!-- request -->
        {{--<div class="col-12 ">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Leave Requests</div>
                </div>

                <div class="card-body">
                    @php
                        $newYear=date('Y').'-'.date('m-d',strtotime($admin->date_joined));
                        $today = date('Y-m-d');
                        if($newYear>$today){
                            $newYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
                        }

                        $takenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
                        $halfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
                        $sickTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=5 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
                        $carryForwardTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=9 AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);
                        $otherTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id IN (1,2,3,4,6,10) AND datetime_from >= '".$newYear."' AND admin_id=".$admin->id);

                        $date_two = \Carbon\Carbon::parse($today);
                        $years = $date_two->diffInYears($admin->date_joined);
                        $carryForwardDays=0;
                        if($years>0) {
                            //$previousYear=date('Y').'-'.date('m-d',strtotime($admin->date_joined));
                            $previousYear=date('Y-m-d',strtotime($newYear. "- 1 years"));
                            $beforeYearTakenDays=DB::select("SELECT SUM(number_days) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=7 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$admin->id);
                            $beforeYearHalfTakenDays=DB::select("SELECT count(*) AS allDays FROM `admin_requests` WHERE manager_status=1 AND request_id=8 AND datetime_from >= '".$previousYear."' AND datetime_from <= '".$newYear."' AND admin_id=".$admin->id);

                            $carryForwardDays=$admin->leave_days-(($beforeYearTakenDays[0]->allDays+($beforeYearHalfTakenDays[0]->allDays/2))+$carryForwardTakenDays[0]->allDays);
                        }

                        $date_two = \Carbon\Carbon::parse($today);
                        $month = $date_two->diffInMonths($newYear);

                        $leave=($admin->leave_days/12)*$month;
                        $takenDaysAll=$takenDays[0]->allDays+($halfTakenDays[0]->allDays/2);
                        $Remaining=$leave-$takenDaysAll;
                    @endphp
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <p><b>Annual Leave Days: </b> {{$admin->leave_days}} </p>
                        </div>
                        <div class="col-sm-4">
                            <p><b>Entitled Days: </b> {{$leave}} </p>
                        </div>
                        <div class="col-sm-4 text-success">
                            <p><b>Remaining Days: </b> {{$Remaining}} </p>
                        </div>
                        <div class="col-sm-4">
                            <p><b>Annual Leave Taken Days:</b> {{$takenDaysAll}} </p>
                        </div>
                        <div class="col-sm-4">
                            <p><b>Sick Leave Taken Days:</b> {{(($sickTakenDays[0]->allDays)?:0)}} </p>
                        </div>
                        <div class="col-sm-4">
                            <p><b>Other Leave Days:</b> {{(($otherTakenDays[0]->allDays)?:0)}} </p>
                        </div>

                        {!!  ($carryForwardDays) ? '<div class="col-sm-4"><p><b>Carry forward Days: </b> '.$carryForwardDays.' </p></div>' : '' !!}

                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors datatable1 truncate-table">
                            <thead>
                            <tr>
                                <th>Request Type</th>
                                <th>From Date</th>
                                <th>To date</th>
                                <th>Number of Days</th>
                                <th>Resume date</th>
                                <th>Status</th>
                                <th>Request Date</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>--}}
        <!-- request end -->

        <!-- Other request -->
        {{--<div class="col-12 ">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Other Requests</div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped dataex-html5-selectors datatable1-or truncate-table">
                            <thead>
                            <tr>
                                <th>Request Type</th>
                                <th>Status</th>
                                <th>Request Date</th>
                                <th>Reply Date</th>
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

        <div class="modal fade text-left" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <form method="post" action="{{ route('request-hr.reply') }}" class="modal-content" novalidate>
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Reply Requests</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mt-1">

                        </div>
                    </div>
                </form>
            </div>
        </div>--}}
        <!-- Other request end -->

        {{--@php
            $countWarnings=\App\Models\AdminWarning::where('admin_id',$admin->id)->count();
        @endphp
        @if($countWarnings>0)
        <!-- warning -->
        <div class="col-12 ">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Warning Letters</h5>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table class="table table-striped dataex-html5-selectors datatable-warning truncate-table">
                                <thead>
                                <tr>
                                    <th>Warning Type</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Send Date</th>
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
        <!-- warning End -->
        @endif--}}
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        var table=$('.datatable1').DataTable({
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('requests.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    data.profile = {{$admin->id}};
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [  ]}],
            'columns': [
                {data: 'request_id'},
                {data: 'datetime_from'},
                {data: 'datetime_to'},
                {data: 'number_days'},
                {data: 'resumption_date'},
                {data: 'manager_status'},
                {data: 'created_at'}
            ],
        });

        $(".change_password_btn").click(function () {
            let newPassword=$('#newPassword').val();
            let confirmPassword=$('#confirmPassword').val();

            let error=0;
            if(newPassword!=confirmPassword) {
                toast_('', 'The password is not the same as its confirmation.', $timeOut = 20000, $closeButton = true);
                error=1;
            }

            if(error==0){
                $('#ModalChangePassword button[name="submit"]').click();
            }
        });

    </script>

    <script>
        var table=$('.datatable1-or').DataTable({
            // fixedColumns: {
            //     start: 1
            // },
            // scrollX: true,
            // scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 2 , "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('requests-hr.get.datatable') }}',
                'data': function(data){

                    // Append to data

                    data._token='{{csrf_token()}}';
                    data.profile = {{$admin->id}};
                }

            },
            // aoColumnDefs: [{bSortable: false,aTargets: [  7 ]}],
            'columns': [
                {data: 'hr_request_id'},
                {data: 'status'},
                {data: 'created_at'},
                {data: 'reply_date'},
                {data: 'action'}

            ],
        });


        $('body .datatable1-or tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {

                let id=$(this).parent().children('td').children('.action').data('id');

                if(id!=undefined) {
                    $.ajax({
                        url:"{{ route('hr-request.details') }}",
                        type:"POST",
                        data:{
                            _token:$('meta[name="csrf-token"]').attr('content'),
                            request:id
                        },
                        success:function (response) {
                            $('#replyModal .modal-title').html(response.user);
                            $('#replyModal .modal-body').html(response.detail);
                            $('#replyModal').modal('show');
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            }

        });
    </script>

    <script>
        var table=$('.datatable-warning').DataTable({
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 3, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('warnings.get.datatable') }}',
                'data': function(data){
                    data.admin={{$admin->id}};
                    data._token='{{csrf_token()}}';
                }

            },
            //aoColumnDefs: [{bSortable: false,aTargets: [ 4]}],
            'columns': [
                {data: 'name'},
                {data: 'reason'},
                {data: 'status'},
                {data: 'created_at'},

            ],
        });
    </script>

@endsection
