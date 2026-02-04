@extends('layouts/contentLayoutMaster')

@section('title', 'Contact Details')

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/wizard.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
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
    $admin = Auth::guard('admin')->user();
    $company=\App\Models\Company::find($admin->company_id);
    $ClientManagers=\Helper::getCM_DropDown_list('1');
    $lead='';
    $email='';
    if(request('lead')){
        $lead=\App\Models\Lead::find(request('lead'));
        $email=($lead) ? $lead->email : '';
    }
    $data_center='';
    if(request('dc')){
        $data_center=\App\Models\DataCenter::find(request('dc'));
        if($data_center && $data_center->email!='-')
            $email=($data_center->email) ? $data_center->email : '';
    }
@endphp
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-content">
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
                    <form method="post" action="{{ ($Contact) ? route('contact.edit') : route('contact.add') }}" class="row property-detail-form" novalidate>
                        @csrf
                        <div class="row m-0">
                            @if($lead)
                            <input type="hidden" name="lead_id" value="{{$lead->id}}">
                            @endif
                            @if($data_center)
                            <input type="hidden" name="data_center_id" value="{{$data_center->id}}">
                            @endif
                            <div class="col-sm-4">
                                <h5 class="text-primary">Client Details</h5>
                                <div class="custom-scrollbar pr-1" style="max-height: 450px;">
                                    <div class="m-0 row pt-1">
                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <label for="ContactCategory">Contact Category <span>*</span></label>
                                                <select class="custom-select form-control" id="ContactCategory" name="ContactCategory" required>
                                                    <option value="">Select</option>
                                                    <option value="buyer">Buyer</option>
                                                    <option value="tenant">Tenant</option>
                                                    <option value="agent">Agent</option>
                                                    <option value="owner">Owner</option>
                                                    <option value="developer">Developer</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 buyer tenant agent owner">
                                            <div class="form-group form-label-group">
                                                <label for="ContactSource">Contact Source <span>*</span></label>
                                                <select class="custom-select form-control select2" id="ContactSource" name="ContactSource">
                                                    <option value="">Select</option>
                                                    @foreach($ContactSources as $CSource)
                                                        <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 {{ ($Contact) ? 'd-none' : '' }}">
                                            <div class="form-group form-label-group">
                                                <label for="ClientManager">Client Manager 1 <span>*</span></label>
                                                <select class="custom-select form-control" id="ClientManager1" name="ClientManager" required>
                                                    <option value="">Select</option>
                                                    @foreach($ClientManagers as $ClientManager)
                                                        <option value="{{ $ClientManager->id }}" {{ ( $admin->id==$ClientManager->id) ? 'selected' : ''}} >{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{--<div class="col-sm-12 {{ ($Contact) ? 'd-none' : '' }}">
                                            <div class="form-group form-label-group">
                                                <label for="ClientManagerTwo">Client Manager 2</label>
                                                <select class="custom-select form-control" id="ClientManagerTwo" name="ClientManagerTwo">
                                                    <option value="">Select</option>
                                                    @foreach($ClientManagers as $ClientManager)
                                                        <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>--}}
                                        <div class="col-sm-12 buyer tenant">
                                            <div class="form-group form-label-group">
                                                <label>Residential / Commercial<span>*</span></label>
                                                <select class="custom-select form-control" id="P_Type" name="P_Type">
                                                    <option value="">Select</option>
                                                    @foreach(PropertyType as $key => $value)
                                                        <option value="{{ $key }}" {{ ($Contact && $Contact->p_type==$key) ? 'selected' : '' }}>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 buyer tenant">
                                            <div class="form-group form-label-group buyer tenant">
                                                <label>Property Type</label>
                                                <select class="custom-select form-control select2" multiple id="PropertyType" name="PropertyType[]">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 buyer">
                                            <div class="form-group form-label-group buyer">
                                                <label>Looking For</label>
                                                <select class="custom-select form-control" id="LookingFor" name="LookingFor">
                                                    <option value="">Select</option>
                                                    @foreach(BUYER_LOOKING_FOR as $kay=>$value)
                                                    <option value="{{$kay}}">{{$value}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 buyer tenant">
                                            <div class="form-group form-label-group buyer tenant">
                                                <label for="Emirate">Emirate</label>
                                                <select class="custom-select form-control" id="Emirate" name="Emirate">
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
                                        <div class="col-md-12 buyer developer d-none">
                                            <div class="form-group form-label-group">
                                                <label for="Developer">Developer <span>*</span></label>
                                                <select class="custom-select form-control match-off-plan-project-select select2" id="Developer" name="Developer">
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
                                        <div class="col-sm-12 buyer">
                                            <div class="form-label-group form-group">
                                                <select class="form-control select-2-off-plan-project" name="off_plan_project" id="off_plan_project">
                                                    <option value="">Select</option>
                                                </select>
                                                <label for="off_plan_project">New Projects</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 buyer tenant">
                                            <div class="form-group form-label-group buyer tenant">
                                                <label for="MasterProject">Master Project</label>
                                                <select class="custom-select form-control select2" multiple id="MasterProject" name="MasterProject[]">
                                                    @php
                                                    $MasterProjects=App\Models\MasterProject::orderBy('name','ASC')->get();
                                                    @endphp
                                                    @foreach($MasterProjects as $MasterProject)
                                                        <option value="{{ $MasterProject->id }}">{{ $MasterProject->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 buyer tenant">
                                            <fieldset class="form-group form-label-group">
                                                <label for="Community">Project</label>
                                                <select class="form-control  select2" multiple name="Community[]" id="Community">
                                                    <option value="">Select</option>

                                                </select>
                                            </fieldset>
                                        </div>
                                        <div class="col-sm-6 buyer tenant">
                                            <div class="form-group form-label-group">
                                                <label for="Bedrooms">Bedrooms</label>
                                                <select class="custom-select form-control select2" multiple id="Bedroom" name="Bedroom[]">
                                                    @php
                                                    $Bedrooms=App\Models\Bedroom::get();
                                                    @endphp
                                                    @foreach($Bedrooms as $Bedroom)
                                                        <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 tenant">
                                            <div class="form-group form-label-group">
                                                <label for="">No. of Cheques </label>
                                                <select class="custom-select form-control" id="NumberCheques" name="NumberCheques">
                                                    <option value="">Select</option>
                                                    @for ($i = 1; $i < 13; $i++)
                                                        <option value="{{$i}}">{{$i}}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 tenant">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control format-picker" autocomplete="off" id="MoveInDay" name="MoveInDay" value="{{ ($Contact) ? $Contact->move_in_day : '' }}" placeholder="Move in Date">
                                                <label for="">Move in Date</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 agent">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="AgencyName" name="AgencyName" value="{{ ($Contact) ? $Contact->agency_name : '' }}" placeholder="Agency Name">
                                                <label for="">Agency Name</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <h5 class="text-primary">Client Profile</h5>
                                <div class="custom-scrollbar pr-1" style="max-height: 450px;">
                                    <div class="m-0 row pt-1">
                                        <div class="col-12 col-sm-6">
                                            <fieldset class="form-group form-label-group">
                                                <label for="Title">Title</label>
                                                <select class="form-control" id="Title" name="Title">
                                                    <option value="">Select</option>
                                                    <option value="Mr">Mr</option>
                                                    <option value="Mrs">Mrs</option>
                                                    <option value="Ms">Ms</option>
                                                    <option value="Miss">Miss</option>
                                                    <option value="Mx">Mx</option>
                                                    <option value="Master">Master</option>
                                                    <option value="Sir">Sir</option>
                                                    <option value="Madam">Madam</option>
                                                    <option value="Dr">Dr</option>
                                                    <option value="Prof">Prof</option>
                                                    <option value="Hon">Hon</option>
                                                    <option value="HRH">HRH</option>
                                                    <option value="Sheikh">Sheikh</option>
                                                    <option value="Sheika">Sheika</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </fieldset>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="FirstName" name="FirstName" value="{{ ($Contact) ? $Contact->firstname : '' }}" placeholder="First Name" required>
                                                <label>First Name <span>*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="LastName" name="LastName" value="{{ ($Contact) ? $Contact->lastname : '' }}" placeholder="Last Name" required>
                                                <label>Last Name <span>*</span></label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <label for="referrer">Job Title</label>
                                                <select class="custom-select form-control" id="JobTitle" name="JobTitle">
                                                    <option value="">Select</option>
                                                    @php
                                                        $JobTitles=\App\Models\JobTitle::where('possession_or_job',2)->orderBy('name','ASC')->get();
                                                    @endphp
                                                    @foreach($JobTitles as $jobTitle)
                                                        <option value="{{ $jobTitle->id }}">{{ $jobTitle->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control format-picker" autocomplete="off" id="DateBirth" name="DateBirth" value="{{ ($Contact) ? $Contact->date_birth : '' }}" placeholder="Date Of Birth">
                                                <label for="DateBirth">Date Of Birth</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control w-100 mobile-number-paste" onkeypress="return isNumber(event)" id="MainNumber" name="MainNumber" value="{{ ($Contact) ? $Contact->main_number : '+971' }}" placeholder="UAE Mobile Number" maxlength="13">
                                                <label>UAE Mobile Number</label>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control country-code w-100 mobile-number-paste" onkeypress="return isNumber(event)" id="NumberTwo" name="NumberTwo" value="{{ ($Contact) ? $Contact->number_two : '' }}" placeholder="Second Number" maxlength="19">
                                                <label>Second Number</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="Email" name="Email" value="{{ ($Contact) ? $Contact->email : $email }}" placeholder="Email Address 1">
                                                <label>Email 1</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="EmailTwo" name="EmailTwo" value="{{ ($Contact) ? $Contact->email_two : '' }}" placeholder="Email Address 2">
                                                <label>Email 2</label>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <fieldset class="form-group form-label-group">
                                                <label>Nationality</label>
                                                <select class="form-control select2" id="Nationalities" name="Nationalities">
                                                </select>
                                            </fieldset>
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <div class="form-group form-label-group">
                                                <input type="text" class="form-control" id="PreferredLanguage" name="PreferredLanguage" value="{{ ($Contact) ? $Contact->language : '' }}" placeholder="Language">
                                                <label>Language</label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                @if($lead)
                                    {{--<div class="col-12 col-sm-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="add_reason" name="add_reason" placeholder="Add Reason" required>
                                            <label>Add Reason</label>
                                        </div>
                                    </div>--}}
                                    @php
                                        if($lead->property_id){
                                            $property=App\Models\Property::where('id',$lead->property_id)->first();
                                            $owner = App\Models\Contact::find( $property->contact_id );

                                            $pictures=explode(',', $property->pictures);
                                            $img_src='';
                                            if($property->pictures)
                                                $img_src=$pictures[0];

                                            $expected_price=0;
                                            if($property->expected_price){
                                                $expected_price=$property->expected_price ;
                                            }

                                            if($property->listing_type_id==2){
                                                if($property->yearly){
                                                    $expected_price=$property->yearly;
                                                }else if($property->monthly){
                                                    $expected_price=$property->monthly;
                                                }else if($property->weekly){
                                                    $expected_price=$property->weekly;
                                                }else{
                                                    $expected_price=$property->daily;
                                                }
                                            }

                                            $MasterProject=App\Models\MasterProject::where('id',$property->master_project_id)->first();
                                            $Community=App\Models\Community::find($property->community_id);
                                            $ClusterStreet=App\Models\ClusterStreet::find($property->cluster_street_id);
                                        }
                                    @endphp
                                    <div>
                                        <h5 class="text-primary">Lead Details</h5>
                                        {!! ($lead->name) ? '<p class="border-top m-0 py-1"><b>Name: </b> '.$lead->name.' </p>' : '' !!}
                                        {!! ($lead->mobile_number) ? '<p class="border-top m-0 py-1"><b>Mobile Number: </b> '.$lead->mobile_number.' </p>' : '' !!}
                                        @if($lead->property_id)
                                            <div class="col-12">
                                                <div class="d-flex deal-info-box"><img class="mr-2 rounded" width="70" height="70" src="/storage/{{$img_src}}">
                                                    <div class="text-xl">
                                                        <small>
                                                            <P class="mb-0">Reference: <a target="_blank" href="/admin/property/view/{{$property->id}}">{{$company->sample.'-'.(($property->listing_type_id==1) ? "S" : "R").'-'.$property->ref_num}}</a></P>
                                                            <P class="mb-0">
                                                                {{(($MasterProject) ? $MasterProject->name : '').(($Community) ? ' '.$Community->name : '').' | AED '.number_format($expected_price)}}
                                                            </P>
                                                            @if($admin->type<3 || $property->client_manager_id==$admin->id || $property->client_manager2_id==$admin->id)
                                                                <hr class="my-0" style="border: 1px solid gray;">
                                                                <P class="mb-0">{{$owner->firstname.' '.$owner->lastname}}</P>
                                                                <P class="mb-0">{{$company->sample.'-'.$owner->id}}</P>
                                                                <P class="mb-0">{{ucfirst($owner->contact_category)}}</P>
                                                                <P class="mb-0">{{$owner->main_number}}</P>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if(request('dc'))
                                    @if($data_center->name && $data_center->name != '-') <p class="m-0"><b>Name:</b> {{ $data_center->name }}</p> @endif
                                    @if($data_center->phone_number && $data_center->phone_number != '-') <p class="m-0"><b>Phone number:</b> {{ $data_center->phone_number }}</p> @endif
                                    @if($data_center->phone_number_2 && $data_center->phone_number_2 != '-') <p class="m-0"><b>Phone number 2:</b> {{ $data_center->phone_number_2 }}</p> @endif
                                    @if($data_center->email && $data_center->email != '-') <p class="m-0"><b>Email:</b> {{ $data_center->email }}</p> @endif
                                @endif
                            </div>

                            <div class="col-sm-4">
                                <div class="row m-0">
                                    <div class="col-12">
                                        <h5 class="text-primary">Address</h5>
                                        <div class="custom-scrollbar pr-1" style="max-height: 150px;">
                                                <div class="m-0 row pt-1">
                                                    <div class="col-sm-6">
                                                        <fieldset class="form-group form-label-group">
                                                            <label for="Country">Country</label>
                                                            <select class="form-control select2" id="Country" name="Country">
                                                                <option value="">Select</option>
                                                            </select>
                                                        </fieldset>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <div class="form-group form-label-group">
                                                            <input type="text" id="City" name="City" value="{{ ($Contact) ? $Contact->city : '' }}" class="form-control" placeholder="City">
                                                            <label for="City">City</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group form-label-group">
                                                            <input type="text" id="Address" name="Address" value="{{ ($Contact) ? $Contact->address : '' }}" class="form-control" placeholder="Address Line 1">
                                                            <label>Address Line 1</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <h5 class="text-primary buyer tenant">Budget/Status</h5>
                                        <div class="custom-scrollbar pr-1" style="max-height: 200px;">
                                            <div class="m-0 row pt-1">
                                                <div class="col-12 mt-1 buyer tenant">
                                                    <div class="form-group form-label-group">
                                                        <input type="text" id="SaleBudget" name="SaleBudget" value="{{ ($Contact) ? number_format($Contact->sale_budget) : '' }}" class="form-control number-format" onkeypress="return isNumber(event)" placeholder="Budget">
                                                        <label>Budget (AED)</label>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 mt-1 buyer">
                                                    <fieldset class="form-group form-label-group">
                                                        <select class="form-control" id="BuyType" name="BuyType">
                                                            <option value="">Select</option>
                                                            <option value="Cash Purchaser">Cash Purchaser</option>
                                                            <option value="Mortgage Purchaser">Mortgage Purchaser</option>
                                                            <option value="Swapping Deal">Swapping Deal</option>
                                                        </select>
                                                        <label>Cash / Finance</label>
                                                    </fieldset>
                                                </div>
                                                <div class="col-sm-6 mt-1 buyer">
                                                    <fieldset class="form-group form-label-group">
                                                        <select class="form-control" id="BuyerType" name="BuyerType">
                                                            <option value="">Select</option>
                                                            <option value="Investor">Investor</option>
                                                            <option value="End User">End User</option>
                                                        </select>
                                                        <label>Investor / End-user</label>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="text-primary">Document</h5>
                                        <div class="custom-scrollbar pr-1" style="max-height: 200px">
                                            <fieldset class="form-group mb-0">
                                                <label for="passport-file">Passport</label>
                                                <div class="d-flex">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input document-upload" data-this="passport-file" id="passport-file"
                                                               data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".passport-progress-bar" data-input="#Passport">
                                                        <label class="custom-file-label" for="passport-file">{{ ($Contact && $Contact->passport) ? 'Passport file' : 'Choose file' }}</label>
                                                    </div>
                                                    <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="Passport"><i class="fa fa-download"></i></a></div>
                                                </div>
                                                <input type="hidden" id="Passport" name="Passport" value="{{ ($Contact) ? $Contact->passport : '' }}">
                                                <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                    <div class="progress-bar bg-teal progress-bar-striped passport-progress-bar" role="progressbar"
                                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="form-group mb-0">
                                                <label for="eid-front-file">EID Front</label>
                                                <div class="d-flex">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input document-upload" data-this="eid-front-file" id="eid-front-file"
                                                               data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".eid-front-progress-bar" data-input="#EIDFront">
                                                        <label class="custom-file-label" for="eid-front-file">{{ ($Contact && $Contact->eid_front) ? 'EID Front file' : 'Choose file' }}</label>
                                                    </div>
                                                    <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="EIDFront"><i class="fa fa-download"></i></a></div>
                                                </div>
                                                <input type="hidden" id="EIDFront" name="EIDFront" value="{{ ($Contact) ? $Contact->eid_front : '' }}">
                                                <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                    <div class="progress-bar bg-teal progress-bar-striped eid-front-progress-bar" role="progressbar"
                                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="form-group mb-0">
                                                <label for="eid-back-file">EID Back</label>
                                                <div class="d-flex">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input document-upload" data-this="eid-back-file" id="eid-back-file"
                                                               data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".eid-back-progress-bar" data-input="#EIDBack">
                                                        <label class="custom-file-label" for="eid-back-file">{{ ($Contact && $Contact->eid_back) ? 'EID Back file' : 'Choose file' }}</label>
                                                    </div>
                                                    <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="EIDBack"><i class="fa fa-download"></i></a></div>
                                                </div>
                                                <input type="hidden" id="EIDBack" name="EIDBack" value="{{ ($Contact) ? $Contact->eid_back : '' }}">
                                                <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                    <div class="progress-bar bg-teal progress-bar-striped eid-back-progress-bar" role="progressbar"
                                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <fieldset class="form-group mb-0">
                                                <label for="other-file">Other</label>
                                                <div class="d-flex">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input document-upload" data-this="other-file" id="other-file"
                                                               data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".other-progress-bar" data-input="#Other">
                                                        <label class="custom-file-label" for="other-file">{{ ($Contact && $Contact->other_doc) ? 'Other file' : 'Choose file' }}</label>
                                                    </div>
                                                    <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="Other"><i class="fa fa-download"></i></a></div>
                                                </div>
                                                <input type="hidden" id="Other" name="Other"  value="{{ ($Contact) ? $Contact->other_doc : '' }}">
                                                <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                    <div class="progress-bar bg-teal progress-bar-striped other-progress-bar" role="progressbar"
                                                         aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                    </div>
                                                </div>
                                            </fieldset>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="col-12 text-center mt-2"><!--name="{{ ($Contact) ? 'update' : 'add' }}"-->
                            <button type="button" id="submit" class="btn  bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light float-sm-right">{{ ($Contact) ? 'Update' : 'Add' }}</button>
                            @if($Contact)<input type="hidden" name="_id" value="{{ ($Contact) ? $Contact->id : '' }}">@endif
                            <button type="submit" name="{{ ($Contact) ? 'update' : 'add' }}"  value="{{ ($Contact) ? $Contact->id : 'add' }}" class="btn d-none bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light float-right">{{ ($Contact) ? 'Update' : 'Add' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin/activity-modal')

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <!--<script src="{{ asset(mix('vendors/js/extensions/jquery.steps.min.js')) }}"></script>-->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/scripts/forms/wizard-steps.js')) }}"></script>
    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
    <script src="/js/scripts/off-plan-project-select.js"></script>

    <script>
        populateCountries("Nationalities", "");
        populateCountries("Country", "");

        $('#ContactCategory').change(function(){
            let val=$(this).val();
            $('.buyer , .tenant , .agent , .owner , .developer').addClass('d-none');

                 $('.'+val).removeClass('d-none');

            $("#SaleBudget , #Emirate , #MasterProject , #Project , #P_Type , #PropertyType , #Bedroom , #BuyType , #BuyerType , #LookingFor").parent().removeClass('error').children('label').children('span').remove();
            if (val == 'buyer') {
                $("#LookingFor").parent().children('label').append('<span>*</span>');
            }

            if (val == 'buyer' || val == 'tenant') {
                $("#SaleBudget , #Emirate , #MasterProject , #Project , #P_Type , #PropertyType , #BuyType , #BuyerType").parent().children('label').append('<span>*</span>');
            }

        });

        @if($Contact)
        $(document).ready(function(){
            $('#ContactSource').val('{{ $Contact->contact_source }}').change();
            $('#Developer').val('{{ $Contact->developer_id }}').change();
            $('#LookingFor').val('{{ $Contact->looking_for }}').change();
            $('#ClientManager1').val('{{ $Contact->client_manager }}');
            $('#ClientManagerTwo').val('{{ $Contact->client_manager_tow }}');
            $('#HiddenProfile').val('{{ $Contact->hidden_profile }}');
            $('#EmailSubscription').val('{{ $Contact->email_subscription }}');
            $('#InterestedInOffplan').val('{{ $Contact->interested_in_offplan }}');

            $("#Nationalities").val('{{ $Contact->nationality }}').change();
            $("#Title").val('{{ $Contact->title }}');
            $("#Country").val('{{ $Contact->country }}').change();
            $("#BuyType").val('{{ $Contact->buy_type }}');
            $("#BuyerType").val('{{ $Contact->buyer_type }}');
            {{--$('input[name=BuyerType][value="{{ $Contact->buyer_type }}"]').attr('checked', 'checked');--}}

            $('#ContactCategory').val('{{ $Contact->contact_category }}').change();

            @php
            $PropertyTypes=App\Models\ContactPropertyType::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
            $PropertyType='';
            foreach ($PropertyTypes as $row){
                $PropertyType.='"'.$row->property_type_id.'",';
            }

            $Bedrooms=App\Models\ContactBedroom::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
            $Bedroom='';
            foreach ($Bedrooms as $row){
                $Bedroom.='"'.$row->bedroom_id.'",';
            }

            $MasterProjects=App\Models\ContactMasterProject::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
            $MasterProject='';
            foreach ($MasterProjects as $row){
                $MasterProject.='"'.$row->master_project_id.'",';
            }

            $Communitys=App\Models\ContactCommunity::where('contact_id',$Contact->id)->whereNull('cat_id')->get();
            $Community='';
            foreach ($Communitys as $row){
                $Community.='"'.$row->community_id.'",';
            }

            @endphp
            $('#P_Type').change();
            $('#Bedroom').val([{!! substr($Bedroom,0,-1) !!}]).trigger('change');
            $('#Emirate').val('{{ $Contact->emirate_id }}').trigger('change');
            $('#MasterProject').val([{!! substr($MasterProject,0,-1) !!}]).trigger('change');
            $('#Community').val([{!! substr($Community,0,-1) !!}]).trigger('change');

            $('#NumberCheques').val('{{ ($Contact) ? $Contact->number_cheques : '' }}');


            $('#off_plan_project')
                .empty()
                .append('<option selected value="{{($offPlanProject)? $offPlanProject->id : ''}}">{{($offPlanProject)? $offPlanProject->project_name : ''}}</option>');
            $('#off_plan_project').select2('data', {
                id: "{{($offPlanProject)? $offPlanProject->id : ''}}",
                label:"{{($offPlanProject)? $offPlanProject->project_name : ''}}"
            });

        });
        @endif

        @if($lead)
        $(document).ready(function(){
            $('#ContactSource').val('{{ $lead->source }}').change();
            $('#ContactCategory').val('{{ $lead->contact_category }}').change();
            $('#MasterProject').val([{{$lead->master_project_id}}]).trigger('change');
        });
        @endif

        @if($data_center)
            $(document).ready(function(){
                $('#ContactSource').val('35').change();
            });
        @endif

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
    <script>
        let duplicate=0;
        $('#MainNumber , #NumberTwo, #Email, #EmailTwo').change(function(){
            @if(!$Contact)duplicateContact();@endif
        });

        function duplicateContact() {
            let id= '{{ ($Contact) ? $Contact->id : '' }}';
            let main_number=$('#MainNumber').val();
            let number_two=$('#NumberTwo').val();
            let contact_category=$('#ContactCategory').val();
            //let email=$('#Email').val();
            //let email_two=$('#EmailTwo').val();

            if(contact_category!='developer') {
                $.ajax({
                    url: "{{ route('get-contact-number-ajax') }}",
                    type: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        id: id,
                        main_number: main_number,
                        number_two: number_two,
                        //email: email,
                        //email_two: email_two,
                    },
                    success: function (response) {
                        if (response) {
                            $('#ViewModal .modal-title').html('Already registered');
                            $('#ViewModal .modal-body').html(`<h5><a target="_blank" href="/admin/contact/view/${response.id}">Click here to {{$company->sample}}_${response.id}</a></h5>`);
                            $('#ViewModal').modal('show');
                            duplicate = 1;
                        } else {
                            duplicate = 0;
                        }
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        }

        $('#submit').click(function () {
            // duplicateContact();
            let error=0;

            let budget=$('#SaleBudget').val();
            let category=$('#ContactCategory').val();

            let MasterProject=$('#MasterProject').val();
            let P_Type=$('#P_Type').val();
            let PropertyType=$('#PropertyType').val();
            let LookingFor=$('#LookingFor').val();
            let Bedroom=$('#Bedroom').val();
            let BuyType=$('#BuyType').val();
            let BuyerType=$('#BuyerType').val();
            let ContactSource=$('#ContactSource').val();
            let Developer=$('#Developer').val();

            if(category=='developer'){
                if(Developer==''){
                    $("#Developer").parent().addClass('error');
                    error=1
                }
            }else{
                if(ContactSource==''){
                    $("#ContactSource").parent().addClass('error');
                    error=1
                }
            }
            if(category=='buyer' || category=='tenant'){
                if(LookingFor==1) {
                    if (MasterProject == '') {
                        $("#MasterProject").parent().addClass('error');
                        error = 1
                    }
                }
                if(LookingFor==2) {
                    if (Developer == '') {
                        $("#Developer").parent().addClass('error');
                        error = 1
                    }
                }
                if(P_Type==''){
                    $("#P_Type").parent().addClass('error');
                    error=1
                }
                if(PropertyType==''){
                    $("#PropertyType").parent().addClass('error');
                    error=1
                }

                let p_type=$('#P_Type').val();
                if(p_type==1) {
                    let propertyTypeArray = $('#PropertyType').val();
                    let bedType = ['2', '3', '4', '5', '10', '11', '16', '17'];
                    var filteredArray = propertyTypeArray.filter(function (n) {
                        return bedType.indexOf(n) !== -1;
                    });

                    if (filteredArray!='') {
                        if(Bedroom==''){
                            $("#Bedroom").parent().addClass('error');
                            error=1
                        }
                    }
                }

                if(budget==''){
                    $("#SaleBudget").parent().addClass('error');
                    error=1
                }
                if(category=='buyer'){
                    if(LookingFor=='1' && BuyType==''){
                        $("#BuyType").parent().addClass('error');
                        error=1
                    }

                    if(BuyerType==''){
                        $("#BuyerType").parent().addClass('error');
                        error=1
                    }

                    if(LookingFor==''){
                        $("#LookingFor").parent().addClass('error');
                        error=1
                    }
                }
            }else{
                $("#SaleBudget").parent().removeClass('error');
            }

            if(error==0){
                let number=$('#MainNumber').val();
                let number2=$('#NumberTwo').val();

                if(number.length < 13 && number.length > 4){
                    toast_('','The UAE Mobile Number is not correct.',$timeOut=20000,$closeButton=true);
                    error=1;
                }

                @if($admin->type!=1)
                    if(duplicate==1){
                        toast_('','Already registered.',$timeOut=20000,$closeButton=true);
                        error=1;
                    }
                @endif

                if(error==0) {
                    if ((number == '+971' && number2 == '') || (number.length < 13 && number2.length < 11)) {
                        toast_('','One of the numbers must be filled.',$timeOut=20000,$closeButton=true);
                    } else {
                        $('button[type="submit"]').click();
                    }
                }
            }else{
                toast_('','Please fill up all required fields.',$timeOut=20000,$closeButton=true);
            }
        });

        $('#ActivityContactLabel').html('Property');

        $('#Developer').change(function(){
            let text=$(this).find(":selected").text();
            let val=$(this).val();
            let cat=$("#ContactCategory").val();
            //$('#FirstName').val('');
            //$('#LastName').val('');
            //$('#Title').val('');
            /*if(text!='' && val!='' && cat=='developer') {
                $('#Title').val('Other');
                $('#FirstName').val(text);
                $('#LastName').val('Developer');
            }*/
        });

        $('#LookingFor').change(function(){
            let val=$(this).val();

            $("#BuyType").val('');
            $("#BuyType").parent().removeClass('error');
            $('#BuyType').attr('disabled','disabled');
            $("#BuyType").parent().children('label').children('span').remove();
            //$("#MasterProject").parent().children('label').children('span').remove();
            //$("#Developer").parent().children('label').children('span').remove();

            $('#MasterProject , #Community , #Developer , #off_plan_project').parent().removeClass('d-none');
            if(val=='1') {
                $('#BuyType').parent().children('label').append('<span>*</span>');
                $('#BuyType').removeAttr('disabled');

                $('#Developer , #off_plan_project').parent().addClass('d-none');
            }
            if(val=='2') {
                $('#Community , #MasterProject').parent().addClass('d-none');
            }

            $('#Developer').val('').trigger('change');
            $('#off_plan_project').val('').trigger('change');
            $('#MasterProject').val([]).trigger('change');
            $('#Community').val([]).trigger('change');

        });

        $('#PropertyType').change(function(){
            let p_type=$('#P_Type').val();
            if(p_type==1) {
                let valueArray = $(this).val();
                let bedType = ['2', '3', '4', '5', '10', '11', '16', '17'];
                var filteredArray = valueArray.filter(function (n) {
                    return bedType.indexOf(n) !== -1;
                });

                $('#Bedroom').removeAttr('disabled').val([{!! ($Contact) ? substr($Bedroom,0,-1) : '' !!}]).trigger('change');

                $("#Bedroom").parent().children('label').children('span').remove();
                if (filteredArray!='') {
                    $("#Bedroom").parent().children('label').append('<span>*</span>');
                }

                if (value == '2') {
                    $("#Bedroom").attr('disabled', 'disabled').val('');
                }
            }
        });

        $('#P_Type').change(function(){
            let value=$(this).val();
            // $("#Bedroom").removeAttr('disabled').val('');
            $('#Bedroom').removeAttr('disabled').val([{!! ($Contact) ? substr($Bedroom,0,-1) : '' !!}]).trigger('change');
            if (value == '2') {
                $("#Bedroom").attr('disabled','disabled').val('');
            }
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
                    $('#PropertyType').val([{!! ($Contact && $PropertyType) ? substr($PropertyType,0,-1) : '' !!}]).trigger('change');
                }
            });
        }

        $('#Emirate').change(function () {
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
                    $('#MasterProject').html(response);
                    @if($Contact && $MasterProject)$('#MasterProject').val([{!! substr($MasterProject,0,-1) !!}]).trigger('change');@endif
                }
            });
        }

        $('#MasterProject').change(function () {
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
                    @if($Contact && $Community)$('#Community').val([{!! substr($Community,0,-1) !!}]).trigger('change');@endif
                }
            });
        }
    </script>

    <script>
        MemebrSelcet2();
        function MemebrSelcet2(SelectType=false) {
            // Loading remote data
            $(".select-2-user").select2({
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
                placeholder: 'Contact Information',
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
            return repo.ref ;

        }
    </script>

    <script> //document upload
        // let ProgressBar='';
        // let InputAttachDocument='';
        // let _this='';
        //
        // $('.document-upload').change(function(){
        //     _this=$(this).data('this');
        //     var Action=$(this).data('action');
        //     var token=$(this).data('token');
        //     ProgressBar=$(this).data('progress');
        //     ProgressBar=$(this).data('progress');
        //     InputAttachDocument=$(this).data('input');
        //     uploadDocument(Action,token);
        // });
        //
        // function uploadDocument(Action,token) {
        //     var file = _(_this).files[0];
        //     // alert(file.name+" | "+file.size+" | "+file.type+" | "+file.name.split('.').pop());
        //     if(file.size>2000000){
        //         Warning('Warning!',"The size of the file is "+formatBytes(file.size)+" , The maximum allowed upload file size is 2 MB");
        //         _this.val(null);
        //         return ;
        //     }
        //
        //     if(file.name.split('.').pop()=="pdf" ||
        //         file.name.split('.').pop()=="doc" ||
        //         file.name.split('.').pop()=="docx" ||
        //         file.name.split('.').pop()=="xlsx" ||
        //         file.name.split('.').pop()=="xml" ||
        //         file.name.split('.').pop()=="xls" ||
        //         file.name.split('.').pop()=="jpg" ||
        //         file.name.split('.').pop()=="jpeg" ||
        //         file.name.split('.').pop()=="webp" ||
        //         file.name.split('.').pop()=="png"){
        //         var formdata = new FormData();
        //         formdata.append("AttachDocumentSubmit", "0");
        //         formdata.append("_token", token);
        //         formdata.append("DocumentFile", file);
        //         var ajax = new XMLHttpRequest();
        //         ajax.upload.addEventListener("progress", documentProgressHandler, false);
        //         ajax.addEventListener("load", documentCompleteHandler, false);
        //         ajax.addEventListener("error", errorHandler, false);
        //         ajax.addEventListener("abort", abortHandler, false);
        //         ajax.open("POST", Action);
        //         ajax.send(formdata);
        //     }else{
        //         let bytes = file.size;
        //         //alert(formatBytes(bytes));
        //         Swal.fire({
        //             title: 'The format is not supported.',
        //             text: "Supported files (pdf, doc, docx, xlsx, xml, xls, jpg, jpeg, webp, png)",
        //             type: 'warning',
        //             showCancelButton: false,
        //             confirmButtonColor: '#d33',
        //             cancelButtonColor: '#3085d6',
        //             cancelButtonText: 'Cancel',
        //             confirmButtonText:'Yes',
        //             confirmButtonClass: 'btn btn-primary',
        //             cancelButtonClass: 'btn btn-danger ml-1',
        //             buttonsStyling: false,
        //         });
        //     }
        //
        // }
        //
        // function _(el) {
        //     return document.getElementById(el);
        // }
        //
        // function documentProgressHandler(event) {
        //     $(ProgressBar).parent().removeClass("d-none");
        //     // $('#AttachDocumentBtn').attr('disabled', 'disabled');
        //     var percent = (event.loaded / event.total) * 100;
        //     $(ProgressBar).css({"width": Math.round(percent) + "%"});
        //     $(ProgressBar).html(Math.round(percent) + "%");
        // }
        //
        // function documentCompleteHandler(event) {
        //     // var FileName = event.target.responseText;
        //     var response = jQuery.parseJSON( event.target.responseText );
        //     $(ProgressBar).html("Upload successfully");
        //     // $('#AttachDocumentBtn').addClass('d-none');
        //     // $("#ArticleFile").val('');
        //     $(InputAttachDocument).val(response.name);
        //     // $(InputAttachDocument).removeClass('hide');
        //     // $('#UpdateArticle').removeAttr('disabled').removeAttr('title').val(FileName);
        // }
        //
        // function errorHandler(event) {
        //     _("status").innerHTML = "Upload Failed";
        // }
        // //
        // function abortHandler(event) {
        //     _("status").innerHTML = "Upload Aborted";
        // }
    </script>
@endsection
