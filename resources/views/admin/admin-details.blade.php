
@extends('layouts/contentLayoutMaster')

@section('title', 'Users Details')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
    $company=App\Models\Company::find($adminAuth->company_id);
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
        <div class="card-header">
            <h4 class="card-title">Admins</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="d-flex">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                </div>
                <form method="post" action="{{ route($route)  }}" enctype="multipart/form-data" novalidate>
                    {!! csrf_field() !!}
                    <div class="row mt-1">
                        <div class="col-12 mb-2">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="p-image mx-auto mb-2">
                                        <img id="imagePreview" src="{{ ($admin && $admin->pic_name) ? '/storage/'.$admin->pic_name : '/images/Defult2.jpg'}}">
                                        <div class="profile-input">
                                            <label for="PicName"><i class="feather icon-camera font-large-2 text-white"></i></label>
                                            <input class="file-upload d-none" name="PicName" type="file" id="PicName" accept="image/" required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" id="firstname" name="firstname" value="{{ ($admin) ? $admin->firstname : old('firstname') }}" autocomplete="off" class="form-control" placeholder="First Name" required>
                                                <label for="firstname">First Name <span>*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="lastname" value="{{ ($admin) ? $admin->lastname : old('lastname') }}" name="lastname" placeholder="Last Name" required>
                                                <label for="lastname">Last Name <span>*</span></label>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <label for="DateBirth">Date of birth</label>
                                                <input type="text" id="DateBirth" name="DateBirth" value="{{ ($admin) ? $admin->date_birth : old('DateBirth') }}" autocomplete="off" class="form-control format-picker" placeholder="Date" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <label for="DateJoined">Date of Join <span>*</span></label>
                                                <input type="text" id="DateJoined" name="DateJoined" value="{{ ($admin) ? $admin->date_joined : old('DateJoined') }}" autocomplete="off" class="form-control format-picker" placeholder="Date" required readonly>
                                            </div>
                                        </div>
                                        {{--<div class="col-sm-4">
                                            <div class="form-group form-label-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" name="Gender" id="Gender">
                                                    <option value="">Select</option>
                                                    @foreach(GENDER as $key => $value)
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>

                                                <script>
                                                    document.getElementById('gender').value="{{ ($admin) ? $admin->dender : old('Gender') }}";
                                                </script>
                                            </div>
                                        </div>--}}
                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <select class="form-control" name="AdminType" id="AdminType" required>
                                                    <option value="">Select</option>
                                                    @foreach(AdminType as $key => $value)
                                                        @if( $adminAuth->super!=1 && $adminAuth->type>=$key)
                                                            @continue
                                                        @endif
                                                        <option value="{{ $key }}">{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                                <label for="AdminType">Access Level <span>*</span></label>
                                            </div>
                                        </div>
                                        @php
                                        $main_super=0;
                                        if( $admin && $admin->main_super==1 )
                                            $main_super=1;

                                        @endphp
                                        @if( !$admin || ($adminAuth->super==1 || $adminAuth->type<$admin->type) )
                                            @if($main_super!=1)
                                            <div class="col-sm-6">
                                                <div class="form-group form-label-group">
                                                    <select class="form-control" name="status" id="status" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="2">Deactive</option>
                                                    </select>
                                                    <label for="status">Status <span>*</span></label>
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-label-group">
                                <input type="email" class="form-control required" id="email" name="email" value="{{ ($admin) ? $admin->email :  old('email') }}" placeholder="Email" required>
                                <label for="email">Company Email <span>*</span></label>
                            </div>
                        </div>
                        @if(!$admin)
                        <div class="col-3">
                            <div class="form-group form-label-group">
                                <div class="controls"> <!--data-validation-required-message="The min field must be at least 6 characters." minlength="6" required-->
                                    <input type="text" class="form-control" id="password" name="password" placeholder="Password" data-validation-required-message="The min field must be at least 6 characters." minlength="6" required>
                                    <div class="help-block"></div>
                                </div>
                                <label for="password">Password <span>*</span></label>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control required mobile-number-paste" onkeypress="return isNumber(event)" maxlength="20" id="MainNumber" name="MainNumber" value="{{ ($admin) ? $admin->main_number : '+971' }}" placeholder="UAE Mobile Number" minlength="13" maxlength="13" required>
                                <label for="MainNumber">UAE Mobile Number <span>*</span></label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control country-code required mobile-number-paste" onkeypress="return isNumber(event)" maxlength="20" id="MobileNumber" name="MobileNumber" value="{{ ($admin) ? $admin->mobile_number : old('MobileNumber') }}" placeholder="Second Number">
                                <label for="MobileNomber">Second Number</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control required" id="JobTitle" name="JobTitle" value="{{ ($admin) ? $admin->job_title : old('JobTitle') }}" placeholder="Job Title">
                                <label for="">Job Title</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control required" id="ReraBRN" name="ReraBRN" value="{{ ($admin) ? $admin->rera_brn : old('ReraBRN') }}" placeholder="RERA BRN">
                                <label for="ReraBRN">RERA BRN</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control required" id="OfficeTel" name="OfficeTel" onkeypress="return isNumber(event)" maxlength="20" value="{{ ($admin) ? $admin->office_tel : ((old('OfficeTel')) ? old('OfficeTel') : $company->office_tel ) }}" placeholder="Office Tel">
                                <label for="OfficeTel">Office Tel</label>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <fieldset class="form-group form-label-group">
                                <label for="supervisor">For Portals</label>
                                <select class="form-control" id="supervisor" name="supervisor">
                                    <option value="">Select</option>
                                    @php
                                        $ClientManagers= \App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status',1)->whereNotNull('rera_brn')->where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                    @endphp
                                    @foreach($ClientManagers as $ClientManager)
                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-sm-5">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control required" id="Address" name="Address" value="{{ ($admin) ? $admin->address : old('Address') }}" placeholder="Address">
                                <label for="Address">Address</label>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group form-label-group">
                                <input type="email" class="form-control" id="personal_email" name="personal_email" value="{{ ($admin) ? $admin->personal_email :  old('personal_email') }}" placeholder="Personal Email">
                                <label for="personal_email">Personal Email</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h4 class="text-primary border-bottom">Salary</h4>
                                    <div class="custom-scrollbar pe-1" style="height: 270px">
                                        <div class="row m-0 pt-2">
                                            <div class="col-sm-6">
                                                <div class="form-group form-label-group">
                                                    <label for="BasicSalary">Basic (AED)</label>
                                                    <input type="text" id="BasicSalary" name="BasicSalary" value="{{ ($admin) ? number_format($admin->basic_salary) : old('BasicSalary') }}" autocomplete="off" class="form-control number-format" placeholder="Basic (AED)">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group form-label-group">
                                                    <label for="AllowanceSalary">Allowance (AED)</label>
                                                    <input type="text" id="AllowanceSalary" name="AllowanceSalary" value="{{ ($admin) ? number_format($admin->allowance_salary) : old('AllowanceSalary') }}" autocomplete="off" class="form-control number-format" placeholder="Allowance (AED)">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="Commission">Commission</label>
                                                    <select class="form-control" name="Commission" id="Commission">
                                                        <option value="">Select</option>
                                                        <option value="1">Yes</option>
                                                        <option value="2">No</option>
                                                    </select>

                                                    <script>
                                                        document.getElementById('Commission').value="{{ ($admin) ? $admin->commission : old('Commission') }}";
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="PaymentMethod">Payment Method</label>
                                                    <select class="form-control" name="PaymentMethod" id="PaymentMethod">
                                                        <option value="">Select</option>
                                                        <option value="1">Cash</option>
                                                        <option value="2">Bank Transfer</option>
                                                        <option value="3">WPS</option>
                                                    </select>

                                                    <script>
                                                        document.getElementById('PaymentMethod').value="{{ ($admin) ? $admin->payment_method : old('PaymentMethod') }}";
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="Bank">Bank</label>
                                                    <select class="form-control" name="Bank" id="Bank">
                                                        <option value="">Select</option>
                                                        @php
                                                            $banks=\App\Models\Bank::get();
                                                        @endphp
                                                        @foreach($banks as $row)
                                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                                        @endforeach
                                                    </select>

                                                    <script>
                                                        document.getElementById('Bank').value="{{ ($admin) ? $admin->bank_id : old('Bank') }}";
                                                    </script>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="AccountNumber">Account Number</label>
                                                    <input type="text" id="AccountNumber" name="AccountNumber" value="{{ ($admin) ? $admin->account_number : old('AccountNumber') }}" autocomplete="off" class="form-control" placeholder="Account Number">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="IBANNumber">IBAN Number</label>
                                                    <input type="text" id="IBANNumber" name="IBANNumber" value="{{ ($admin) ? $admin->iban_number : old('IBANNumber') }}" autocomplete="off" class="form-control" placeholder="IBAN Number">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="LabourPersonalId">Personal Labour ID Number</label>
                                                    <input type="text" id="LabourPersonalId" name="LabourPersonalId" value="{{ ($admin) ? $admin->labour_personal_id : old('LabourPersonalId') }}" autocomplete="off" class="form-control" placeholder="Personal Labour ID Number">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group form-label-group">
                                                    <label for="BankRoutingCode">Bank Routing Code</label>
                                                    <input type="text" id="BankRoutingCode" name="BankRoutingCode" value="{{ ($admin) ? $admin->bank_routing_code : old('BankRoutingCode') }}" autocomplete="off" class="form-control" placeholder="Bank Routing Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    {{--<h4 class="text-primary border-bottom">Leave</h4>
                                    <div class=" pe-1">
                                        <div class="row m-0 pt-2">
                                            <div class="col-sm-12">
                                                <div class="form-group form-label-group">
                                                    <label for="LeaveDays">Leave Days <span>*</span></label>
                                                    <input type="number" id="LeaveDays" name="LeaveDays" value="{{ ($admin) ? $admin->leave_days : old('LeaveDays') }}" autocomplete="off" class="form-control" placeholder="Leave Days" required>
                                                </div>
                                            </div>
                                            @if($admin)
                                            @if($admin->use_annual_current_year==null && $admin->date_joined && $adminAuth->super==1)
                                                <div class="col-sm-12">
                                                    <div class="form-group form-label-group">
                                                        <label for="UseAnnualCurrentYear">Current Year Used Annual Leave <span>*</span></label>
                                                        <input type="number" id="UseAnnualCurrentYear" name="UseAnnualCurrentYear" value="{{ ($admin) ? $admin->use_annual_current_year : old('UseAnnualCurrentYear') }}" autocomplete="off" class="form-control" placeholder="Current Year Used Annual Leave" required>
                                                    </div>
                                                </div>
                                            @endif
                                            @if($admin->use_annual_previous_year==null && $admin->date_joined && $adminAuth->super==1)

                                                @php
                                                    $today = date('Y-m-d');
                                                    $date_two = \Carbon\Carbon::parse($today);
                                                    $years = $date_two->diffInYears($admin->date_joined);
                                                @endphp
                                                @if($years>0)
                                                <div class="col-sm-12">
                                                    <div class="form-group form-label-group">
                                                        <label for="UseAnnualPreviousYear">Previous Year Used Annual Leave (Carry Forward) <span>*</span></label>
                                                        <input type="number" id="UseAnnualPreviousYear" name="UseAnnualPreviousYear" value="{{ ($admin) ? $admin->use_annual_previous_year : old('UseAnnualPreviousYear') }}" autocomplete="off" class="form-control" placeholder="Previous Year Used Annual Leave (Carry Forward)" required>
                                                    </div>
                                                </div>
                                                @endif
                                            @endif
                                            @endif
                                        </div>
                                    </div>--}}

                                    <h4 class="text-primary border-bottom">Expiration Date</h4>
                                    <div class="custom-scrollbar pe-1" style="height: 250px">
                                        <div class="row m-0 pt-2">
                                            <div class="col-sm-12">
                                                <div class="form-group form-label-group">
                                                    <label for="VisaExpirationDate">Visa</label>
                                                    <input type="text" id="VisaExpirationDate" name="VisaExpirationDate" value="{{ ($admin) ? $admin->visa_expiration_date : old('VisaExpirationDate') }}" autocomplete="off" class="form-control format-picker" placeholder="Date">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group form-label-group">
                                                    <label for="InsuranceExpirationDate">Insurance</label>
                                                    <input type="text" id="InsuranceExpirationDate" name="InsuranceExpirationDate" value="{{ ($admin) ? $admin->insurance_expiration_date : old('InsuranceExpirationDate') }}" autocomplete="off" class="form-control format-picker" placeholder="Date">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group form-label-group">
                                                    <label for="LabourCardExpirationDate">Labour Card</label>
                                                    <input type="text" id="LabourCardExpirationDate" name="LabourCardExpirationDate" value="{{ ($admin) ? $admin->labour_card_expiration_date : old('LabourCardExpirationDate') }}" autocomplete="off" class="form-control format-picker" placeholder="Date">
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="form-group form-label-group">
                                                    <label for="ReraCardExpirationDate">RERA Card</label>
                                                    <input type="text" id="ReraCardExpirationDate" name="ReraCardExpirationDate" value="{{ ($admin) ? $admin->rera_card_expiration_date : old('ReraCardExpirationDate') }}" autocomplete="off" class="form-control format-picker" placeholder="Date">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <h4 class="text-primary border-bottom">Documents</h4>
                                    <div class="custom-scrollbar pe-1" style="height: 270px">
                                        <fieldset class="form-group mb-0">
                                            <label for="passport-file">Passport</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="passport-file" id="passport-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".passport-progress-bar" data-input="#passport">
                                                    <label class="custom-file-label" for="other-file">{{ ($admin && $admin->passport) ? 'Passport file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="passport"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="passport" name="passport" value="{{ ($admin) ? $admin->passport : old('passport') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped passport-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="emirates_id-file">Emirates ID</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="emirates_id-file" id="emirates_id-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".emirates_id-progress-bar" data-input="#emirates_id">
                                                    <label class="custom-file-label" for="emirates_id-file">{{ ($admin && $admin->emirates_id) ? 'Emirates ID file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="emirates_id"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="emirates_id" name="emirates_id" value="{{ ($admin) ? $admin->emirates_id : old('emirates_id') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped emirates_id-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="labour_contract-file">Labour Contract</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="labour_contract-file" id="labour_contract-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".labour_contract-progress-bar" data-input="#labour_contract">
                                                    <label class="custom-file-label" for="labour_contract-file">{{ ($admin && $admin->labour_contract) ? 'Labour Contract file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="labour_contract"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="labour_contract" name="labour_contract" value="{{ ($admin) ? $admin->labour_contract : old('labour_contract') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped labour_contract-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="labour_card-file">Labour Card</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="labour_card-file" id="labour_card-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".labour_card-progress-bar" data-input="#labour_card">
                                                    <label class="custom-file-label" for="labour_card-file">{{ ($admin && $admin->labour_card) ? 'Labour Card file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="labour_card"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="labour_card" name="labour_card" value="{{ ($admin) ? $admin->labour_card : old('labour_card') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped labour_card-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="residents_visa-file">Residents Visa</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="residents_visa-file" id="residents_visa-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".residents_visa-progress-bar" data-input="#residents_visa">
                                                    <label class="custom-file-label" for="residents_visa-file">{{ ($admin && $admin->residents_visa) ? 'Residents Visa file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="residents_visa"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="residents_visa" name="residents_visa" value="{{ ($admin) ? $admin->residents_visa : old('residents_visa') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped residents_visa-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="insurance-file">Insurance</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="insurance-file" id="insurance-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".insurance-progress-bar" data-input="#insurance">
                                                    <label class="custom-file-label" for="insurance-file">{{ ($admin && $admin->insurance) ? 'Insurance file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="insurance"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="insurance" name="insurance" value="{{ ($admin) ? $admin->insurance : old('insurance') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped insurance-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="rera_card-file">RERA Card</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="rera_card-file" id="rera_card-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".rera_card-progress-bar" data-input="#rera_card">
                                                    <label class="custom-file-label" for="rera_card-file">{{ ($admin && $admin->rera_card) ? 'RERA Card file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="rera_card"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="rera_card" name="rera_card" value="{{ ($admin) ? $admin->rera_card : old('rera_card') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped rera_card-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="job_offer-file">Job Offer</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="job_offer-file" id="job_offer-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".job_offer-progress-bar" data-input="#job_offer">
                                                    <label class="custom-file-label" for="job_offer-file">{{ ($admin && $admin->job_offer) ? 'Job Offer file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="job_offer"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="job_offer" name="job_offer" value="{{ ($admin) ? $admin->job_offer : old('job_offer') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped job_offer-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="contract_of_employment-file">Contract Of Employment</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="contract_of_employment-file" id="contract_of_employment-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".contract_of_employment-progress-bar" data-input="#contract_of_employment">
                                                    <label class="custom-file-label" for="contract_of_employment-file">{{ ($admin && $admin->contract_of_employment) ? 'Contract Of Employment file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="contract_of_employment"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="contract_of_employment" name="contract_of_employment" value="{{ ($admin) ? $admin->contract_of_employment : old('contract_of_employment') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped contract_of_employment-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="cancellation-file">Cancellation</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="cancellation-file" id="cancellation-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".cancellation-progress-bar" data-input="#cancellation">
                                                    <label class="custom-file-label" for="cancellation-file">{{ ($admin && $admin->cancellation) ? 'Cancellation file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="cancellation"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="cancellation" name="cancellation" value="{{ ($admin) ? $admin->cancellation : old('cancellation') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped cancellation-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="other-file">Other</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="other-file" id="other-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".other-progress-bar" data-input="#other">
                                                    <label class="custom-file-label" for="other-file">{{ ($admin && $admin->other) ? 'Other file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="px-1"><a href="javascript:void(0);" class="doc-download" data-input="other"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="other" name="other" value="{{ ($admin) ? $admin->other : old('other') }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped other-progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-1">
                            <fieldset class="border">
                                <legend class="w-unset">Emergency Contact</legend>
                                <div class="row m-0 mt-2">
                                    <div class="col-sm-3">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="EmergencyContactName" name="EmergencyContactName" value="{{ ($admin) ? $admin->emergency_contact_name : old('EmergencyContactName') }}" placeholder="Name">
                                            <label for="EmergencyContactName">Name</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control country-code" onkeypress="return isNumber(event)" maxlength="20" id="EmergencyContactNumber" name="EmergencyContactNumber" value="{{ ($admin) ? $admin->emergency_contact_number : old('EmergencyContactNumber') }}" placeholder="Number">
                                            <label for="EmergencyContactNumber">Number</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="EmergencyContactEmail" name="EmergencyContactEmail" value="{{ ($admin) ? $admin->emergency_contact_email : old('EmergencyContactEmail') }}" placeholder="Email">
                                            <label for="EmergencyContactEmail">Email</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-3">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="EmergencyContactRelation" name="EmergencyContactRelation" value="{{ ($admin) ? $admin->emergency_contact_relation : old('EmergencyContactRelation') }}" placeholder="Relation">
                                            <label for="EmergencyContactRelation">Relation</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-9">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="EmergencyContactAddress" name="EmergencyContactAddress" value="{{ ($admin) ? $admin->emergency_contact_address : old('EmergencyContactAddress') }}" placeholder="Address">
                                            <label for="EmergencyContactAddress">Address</label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <dvi class="col-12 mt-2">
                            @if($admin)<input type="hidden" name="_id" value="{{ ($admin) ? $admin->id : '' }}">@endif
                            <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="submit">Save</button>
                            <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right d-none" id="submit-admin" name="submit" value="{{ ($admin) ? $admin->id : '' }}">Save</button>
                        </dvi>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <!--<script src="/js/scripts/build/js/intlTelInput.min.js"></script>-->
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
    <script>


        $('#AdminType').val( '{{ ($admin) ? $admin->type : old("AdminType") }}' );
        $('#status').val( '{{ ($admin) ? $admin->status : 1 }}' );
        $('#supervisor').val( '{{ ($admin) ? $admin->supervisor_id : '' }}' ).change();

        $("#PicName").change(function () {
            ImagePreview(this,550000,['gif','png','jpg','jpeg'],'#imagePreview');
        });

        $("#UseAnnualCurrentYear , #UseAnnualPreviousYear").keyup(function () {
            let leaveDays=parseInt( $('#LeaveDays').val() );
            let num=parseInt( $(this).val() );
            if(num>leaveDays){
                $(this).val('');
            }
        });

        $("#submit").click(function () {
            //let rera=$('#ReraBRN').val();
            //let supervisor=$('#supervisor').val();
            //let type=$('#AdminType').val();

            let error=0;
            // if(rera=='' && supervisor=='' && type!='8') {
            //     toast_('', 'Please fill the Rera BRN / For Portal.', $timeOut = 20000, $closeButton = true);
            // }else{
            //     error=1;
            // }

            if(error==0){
                $('button[name="submit"]').click();
            }

        });

        $(".country-code").intlTelInput({
            // allowDropdown: false,
            autoHideDialCode: false,
            // autoPlaceholder: "off",
            dropdownContainer: "body",
            // excludeCountries: ["us"],
            formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //     $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //         var countryCode = (resp && resp.country) ? resp.country : "";
            //         callback(countryCode);
            //     });
            // },
            hiddenInput: "full_number",
            initialCountry: "auto",
            nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            preferredCountries: ['ae'],
            // separateDialCode: true,
            utilsScript: "js/build/js/utils.js"
        });
    </script>
@endsection
