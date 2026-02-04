
@extends('layouts/contentLayoutMaster')

@section('title', 'Property')

@section('vendor-style')
    <!-- vendor css files -->
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css" />
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">

    <style>
        .slick-next {
            right: 0px;
        }

        .slick-prev {
            left: 0px;
            z-index:300;
        }

        .slick-slider {
            margin-bottom: 10px;
        }

        .slick-initialized .slick-slide {
            padding: 0 5px;
        }

        .white_goverlay:before {
            background: rgb(255,255,255);
            background: linear-gradient(180deg, rgba(255,255,255, 0.2) 20%, rgba(255,255,255, 0.3) 30%, rgba(255,255,255, 0.8) 20%);
            bottom: 0;
            content: "";
            height: 90px;
            left: 0;
            position: absolute;
            right: 0;
            width: 100%;
            z-index: 1;
        }
        #preview-description , #dld-description{
            overflow: hidden;
        }
        .picker {
            min-width: 250px;
        }

        .rented-until-box .picker , .available-from-box .picker , .expiration-date-box .picker {
            right: 0;
        }

        .custom-switch .custom-control-label::before {
            height: 1rem !important;
            width: 2rem !important;
        }

        .custom-switch .custom-control-label::after {
            width: 0.8rem !important;
            height: 0.8rem !important;
        }

        .custom-switch .custom-control-label {
            height: 1rem;
            width: 2.1rem;
        }
        #PortalsModal .custom-control.custom-checkbox{
            height: 100%;
            align-items: center;
            display: flex;
        }

        #PortalsModal .custom-control.custom-checkbox label img{
            margin: 10px;
        }

        #PortalsModal .custom-control-input:checked ~ .custom-control-label::before {
            color: #fff;
            border: 3px solid #7367f0;
            background-color: #7367f000;
        }

        #PortalsModal .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
            background-image: unset !important;
        }

        #PortalsModal .custom-control-label::before{
            left: 0px !important;
            background-color: unset !important;
        }

        #PortalsModal .custom-control{
            padding-left: unset !important;
            justify-content: center;
        }

        #PortalsModal .custom-control-label{
            position: unset !important;
        }

        #PortalsModal .custom-control-label::before, #PortalsModal .custom-control-label::after {
            width: 100% !important;
            height: 100% !important;
        }

        .add-btn {
            position: absolute;
            padding: 0.25rem 0;
            font-size: 0.7rem;
            top: -20px;
            right: 3px;
            opacity: 1;
        }

        @media print {
            body{
                background:#fff !important;
                background-color:#fff !important;
            }

            #dld-print-box{
                padding-bottom: 0px;
                position: relative;
                height: 100% !important;
                flex: unset !important;
            }

            #dld-print-box .dld-footer{
                position: absolute;
                bottom: 0;
            }
        }

    </style>
@endsection
@section('content')
    <!-- Form wizard with step validation section start -->

    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif

    @php
        $admin = Auth::guard('admin')->user();
        $Company=App\Models\Company::find($admin->company_id);
        $ClientManagers=\Helper::getCM_DropDown_list('1');
        $disabled='';
        $disabled2='';
        /*if($admin->type==3){
            $disabled2='';
            $disabled='disabled';
        }
        if($admin->type==4){
            $disabled2='';
            $disabled='disabled';
        }
        if($Property){
            if(($admin->type==3 && $Property->admin_id==$admin->id) || ($Property->status!=1) ){
                $disabled2='';
                $disabled='';
            }

            if($Property->status==4){
                $disabled2='';
                $disabled='';
            }
        }*/

    $data_center='';
    $master_project_id='';
    $project_id='';
    $villa_number='';
    $bua=null;
    $plot_sqft=null;
    if(request('dc')){
        $data_center=\App\Models\DataCenter::find(request('dc'));
        $master_project_id=$data_center->master_project_id;
        $project_id=$data_center->project_id;
        $villa_number=$data_center->villa_unit_no;
        $bua=$data_center->size;
        $plot_sqft=$data_center->plot_size;
    }

    $requester='';
    if($Property && $Property->status==11){
        $PropertyStatusHistory=App\Models\PropertyStatusHistory::where('property_id',$Property->id)->where('status',11)->orderBy('id','DESC')->first();
        $requesterAdmin=App\Models\Admin::find($PropertyStatusHistory->h_admin_id);
        $requester='<div class="text-primary"><b>Requested By: '.$requesterAdmin->firstname.' '.$requesterAdmin->lastname.'</b></div>';
        if($PropertyStatusHistory && $PropertyStatusHistory->status==11 && $PropertyStatusHistory->rfl_status==2){
            echo '<div class="alert alert-danger">The listing request has been rejected for the following reasons:<br><br>'.nl2br($PropertyStatusHistory->reason).'</div>';
        }
    }
    @endphp

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add New Property</h4>
        </div>
        <div class="card-content">
            <div class="card-body property-detail-form">
                <form method="post" action="{{ ($Property) ? route('property.edit') : route('property.add') }}" class="add-property-form clearfix" enctype="multipart/form-data" novalidate>
                    @csrf
                    @if($data_center)
                        <input type="hidden" name="data_center_id" value="{{$data_center->id}}">
                    @endif
                    <div class="row">
                        <div class="col-sm-4">
                            <h5 class="text-primary">Property Address & Detail</h5>
                            <div class="custom-scrollbar pr-1" style="max-height: 450px;">

                                <div class="row mx-0 pt-2">
                                    <div class="col-sm-12">
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-inline-block mr-1">
                                                <fieldset>
                                                    <div class="vs-radio-con">
                                                        <input type="radio" id="ListingType1" name="ListingType" checked="" value="1" {{ ($Property) ? $disabled : '' }}>
                                                        <span class="vs-radio">
                                                            <span class="vs-radio--border"></span>
                                                            <span class="vs-radio--circle"></span>
                                                        </span>
                                                        <span class="">Sale</span>
                                                    </div>
                                                </fieldset>
                                            </li>
                                            <li class="d-inline-block">
                                                <fieldset>
                                                    <div class="vs-radio-con">
                                                        <input type="radio" id="ListingType2" name="ListingType" value="2" {{ ($Property) ? $disabled : '' }}>
                                                        <span class="vs-radio">
                                                            <span class="vs-radio--border"></span>
                                                            <span class="vs-radio--circle"></span>
                                                        </span>
                                                        <span class="">Rent</span>
                                                    </div>
                                                </fieldset>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-6 mt-2">
                                        <div class="form-group form-label-group">
                                            <input type="text" autocomplete="off" class="form-control required" id="DateEntered" name="DateEntered" value="{{ \Helper::changeDatetimeFormat( ($Property) ? $Property->created_at : date('Y-m-d H:i:s') ) }}" readonly placeholder="Date Entered" >
                                            <label for="DateEntered">Date Entered</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 mt-2">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required" id="ReferenceNumber" disabled value="{{$Company->sample}}-S-<?=$propertyMax?>" placeholder="Reference Number" >
                                            <label>Reference Number</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="VendorMotivation">Vendor Motivation</label>
                                            <select class="custom-select form-control" id="VendorMotivation" name="VendorMotivation" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($VendorMotivations as $VMotivation)
                                                    <option value="{{ $VMotivation->id }}">{{ $VMotivation->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Residential / Commercial<span>*</span></label>
                                            <select class="custom-select form-control" id="Type" name="Type" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach(PropertyType as $key => $value)
                                                    <option value="{{ $key }}" {{ ($Property && $Property->type==$key) ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Property Type <span>*</span></label>
                                            <select class="custom-select form-control" id="PropertyType" name="PropertyType" required {{ ($Property) ? $disabled : '' }}>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="OffPlan">Completion Status <span>*</span></label>
                                            <select class="custom-select form-control" id="OffPlan" name="OffPlan" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach(OffPlan as $key=>$value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="OffPlanDetailsSaleType">Off Plan Sale Type</label>
                                            <select class="custom-select form-control" id="OffPlanDetailsSaleType" name="OffPlanDetailsSaleType" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="1" {{ ($Property && $Property->offplanDetails_saleType==1) ? 'selected' : '' }}>New</option>
                                                <option value="2" {{ ($Property && $Property->offplanDetails_saleType==2) ? 'selected' : '' }}>Resale</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="number" class="form-control" id="OffPlanDetailsDldWaiver" name="OffPlanDetailsDldWaiver" value="{{ ($Property) ? $Property->offplanDetails_dldWaiver : '' }}" placeholder="%" {{ ($Property) ? $disabled : '' }}>
                                            <label for="OffPlanDetailsDldWaiver">Off Plan DLD Waiver %</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control number-format" onkeypress="return isNumber(event)" id="OffPlanDetailsOriginalPrice" name="OffPlanDetailsOriginalPrice" value="{{ ($Property) ? number_format($Property->offplanDetails_originalPrice) : '' }}" placeholder="AED" {{ ($Property) ? $disabled : '' }}>
                                            <label for="OffPlanDetailsOriginalPrice">Off Plan Original Price</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control number-format" onkeypress="return isNumber(event)" id="OffPlanDetailsAmountPaid" name="OffPlanDetailsAmountPaid" value="{{ ($Property) ? number_format($Property->offplanDetails_amountPaid) : '' }}" placeholder="AED" {{ ($Property) ? $disabled : '' }}>
                                            <label for="OffPlanDetailsAmountPaid">OffPlan Amount Paid</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="CompletionDate" name="CompletionDate" value="{{ ($Property) ? $Property->completion_date : '' }}" placeholder="Completion Date" {{ ($Property) ? $disabled : '' }} readonly >
                                            <label for="">Completion Date</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <label for="PFLocation">PF Location <span>*</span></label>
                                            <select class="custom-select form-control select2" id="PFLocation" name="PFLocation" required {{ ($Property) ? $disabled : '' }}>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Emirate">Emirate <span>*</span></label>
                                            <select class="custom-select form-control select2" id="Emirate" name="Emirate" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($Emirates as $Emirate)
                                                    <option value="{{ $Emirate->id }}">{{ $Emirate->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="MasterProject">Master Project <span>*</span></label>
                                            <select class="custom-select form-control select2" id="MasterProject" name="MasterProject" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Community">Project <span>*</span></label>
                                            <select class="custom-select form-control select2" id="Community" name="Community" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="ClusterStreet">Cluster / Street / Frond</label>
                                            <a href="#AddClusterStreetModal" data-toggle="modal" title="Add" class="add-btn"><i class="fa fa-plus-circle"></i></a>
                                            <select class="custom-select form-control select2" id="ClusterStreet" name="ClusterStreet" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="VillaNumber" name="VillaNumber" required value="{{ ($Property) ? $Property->villa_number : $villa_number }}" placeholder="Villa/Unit Number" {{ ($Property) ? $disabled : '' }}>
                                            <label>Villa/Unit Number<span>*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="VillaType">Type</label>
                                            <a href="#AddTypeModal" data-toggle="modal" title="Add" class="add-btn"><i class="fa fa-plus-circle"></i></a>
                                            <select class="custom-select form-control select2" id="VillaType" name="VillaType" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="BUA" name="BUA" required value="{{ ($Property) ? number_format($Property->bua) : number_format($bua) }}" placeholder="BUA (sqft)"  {{ ($Property) ? $disabled : '' }}>
                                            <label for="BUA">BUA (sqft) <span>*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control number-format" onkeypress="return isNumber(event)" id="PlotSQFT" name="PlotSQFT" value="{{ ($Property) ? number_format($Property->plot_sqft) : number_format($plot_sqft) }}" placeholder="Plot (sqft)"  {{ ($Property) ? $disabled : '' }}>
                                            <label for="PlotSQFT">Plot (sqft)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Bedrooms">Bedrooms</label>
                                            <select class="custom-select form-control" id="Bedrooms" name="Bedrooms" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($Bedrooms as $Bedroom)
                                                    <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Bathrooms">Bathrooms <span>*</span></label>
                                            <select class="custom-select form-control" id="Bathrooms" name="Bathrooms" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($Bathrooms as $Bathroom)
                                                    <option value="{{ $Bathroom->id }}">{{ $Bathroom->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Maid's Room</label>
                                            <select class="custom-select form-control" id="Maid" name="Maid" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Driver's Room</label>
                                            <select class="custom-select form-control" id="Driver" name="Driver" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Study Room</label>
                                            <select class="custom-select form-control" id="Study" name="Study" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Storage Room</label>
                                            <select class="custom-select form-control" id="Storage" name="Storage" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">View <span>*</span></label>
                                            <select class="custom-select form-control select2" id="View" name="View" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($Views as $View)
                                                    <option value="{{ $View->id }}">{{ $View->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Parking <span>*</span></label>
                                            <select class="custom-select form-control" id="Parking" name="Parking" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @for ($i = 0; $i < 11; $i++)
                                                    <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Furnished <span>*</span></label>
                                            <select class="custom-select form-control" id="Furnished" name="Furnished" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Furnished">Furnished</option>
                                                <option value="Unfurnished">Unfurnished</option>
                                                <option value="Semi furnished">Semi furnished</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="Status2">Status<span>*</span></label>
                                            <select class="custom-select form-control" id="Status2" name="Status2" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach(Status2 as $key => $value)
                                                    <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6" >
                                        <div class="form-group form-label-group rented-until-box">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="RentedFrom" name="RentedFrom" value="{{ ($Property) ? $Property->rented_from : '' }}" placeholder="Rented from" {{ ($Property) ? $disabled : '' }} readonly>
                                            <label for="">Rented from</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="RentedUntil" name="RentedUntil" value="{{ ($Property) ? $Property->rented_until : '' }}" placeholder="Rented until" {{ ($Property) ? $disabled : '' }} readonly>
                                            <label for="">Rented until</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">Vacating Notice</label>
                                            <select class="custom-select form-control" id="VacatingNotice" name="VacatingNotice" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="AvailableFrom" name="AvailableFrom" value="{{ ($Property) ? $Property->available_from : '' }}" placeholder="Available from" {{ ($Property) ? $disabled : '' }} readonly required>
                                            <label for="">Available from <span>*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="ContactSource">Contact Source <span>*</span></label>
                                            <select class="custom-select form-control select2" id="ContactSource" name="ContactSource" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($ContactSources as $CSource)
                                                    <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="RentedFor" name="RentedFor" value="{{ ($Property) ? number_format($Property->rented_for) : ''}}" placeholder="Rented For"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="RentedFor">Rented For</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="ExpectedPrice" name="ExpectedPrice" value="{{ ($Property) ? number_format($Property->expected_price) : ''}}" placeholder="Expected Price (AED)"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="ExpectedPrice">Expected Price (AED)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="DailyPrice" name="DailyPrice" value="{{ ($Property) ? number_format($Property->daily) : ''}}" placeholder="Daily Price (AED)"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="DailyPrice">Daily Price (AED)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="WeeklyPrice" name="WeeklyPrice" value="{{ ($Property) ? number_format($Property->weekly) : ''}}" placeholder="Weekly Price (AED)"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="WeeklyPrice">Weekly Price (AED)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="MonthlyPrice" name="MonthlyPrice" value="{{ ($Property) ? number_format($Property->monthly) : ''}}" placeholder="Monthly Price (AED)"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="MonthlyPrice">Monthly Price (AED)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required number-format" onkeypress="return isNumber(event)" id="YearlyPrice" name="YearlyPrice" value="{{ ($Property) ? number_format($Property->yearly) : ''}}" placeholder="Yearly Price (AED)"  {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="YearlyPrice">Yearly Price (AED)</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="">No. of Cheques </label>
                                            <select class="custom-select form-control" id="NumberCheques" name="NumberCheques" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @for ($i = 1; $i < 13; $i++)
                                                    <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label for="VaastuOrientation">Vaastu Orientation</label>
                                            <select class="custom-select form-control" id="VaastuOrientation" name="VaastuOrientation" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($VaastuOrientations as $VOrientations)
                                                    <option value="{{ $VOrientations->id }}">{{ $VOrientations->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="property_management">Property Management</label>
                                            <select class="form-control" id="property_management" name="property_management">
                                                <option value="">Select</option>
                                                <option value="1">Yes</option>
                                                <option value="2">No</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-sm-4">
                            <div class="row mb-1">
                                <div class="col-sm-7">
                                    <h6 class="text-primary float-left">Media & Description</h6>
                                </div>
                            </div>
                            <div class="custom-scrollbar pr-1" style="max-height: 450px;">
                                <div class="row m-0">
                                    <div class="col-sm-9">
                                        <fieldset class="form-group">
                                            <label>Photos</label>
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="AttachFile" accept=".jpeg,.jpg,.png,.gif,.svg,.webp" multiple {{ ($Property) ? $disabled2 : '' }}>
                                                <label class="custom-file-label" for="AttachFile">Photos</label>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-primary waves-effect px-1 mt-2" id="AttachFileBtn" data-type="multi" data-token="{{ csrf_token() }}" data-action="{{ route('upload-image') }}" type="button" disabled="disabled">
                                            Upload
                                        </button>
                                    </div>

                                    <div class="progress progress-bar-primary progress-xl mb-2 d-none w-100">
                                        <div class="progress-bar bg-teal progress-bar-striped" id="PresentProgressBar" role="progressbar"
                                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row custom-scrollbar AttachFileBox" style="max-height: 120px;">
                                            @if($Property)
                                                @if($Property->pictures)
                                                    @foreach(explode(',', $Property->pictures) as $picture)
                                                        <div class="border mx-auto mb-1 property-iamge-box">
                                                            <a href="/storage/{{ $picture }}" class="property-image">
                                                                <img src="/storage/{{ $picture }}" height="100px" width="100px">
                                                            </a>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </div>
                                        <a href="#showAllImageModal" data-toggle="modal" class="show-all-images"><small>Show All</small></a>

                                        <!-- Modal Show All Images -->
                                        <div class="modal fade text-left" id="showAllImageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel16">Images</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row showAllAttachFileBox sort-element mx-0 px-1">
                                                            @if($Property)
                                                                @if($Property->pictures)
                                                                    @foreach(explode(',', $Property->pictures) as $picture)
                                                                        <div class="col-sm-3 mb-1">
                                                                            <div class="border">
                                                                                <div class=" mx-auto property-iamge-box">
                                                                                    <a href="/storage/{{ $picture }}" class="property-image d-block w-100">
                                                                                        <img src="/storage/{{ $picture }}">
                                                                                    </a>
                                                                                    <input type="hidden" value="{{ $picture }}" name="InputAttachFile[]">
                                                                                </div>
                                                                                <div class="action clearfix px-1" data-name="{{ $picture }}">
                                                                                    <a title="remove" href="javascript:void(0)" class="file-delete d-blok w-100"><i class="feather icon-trash-2"></i> <small>Delete</small></a>

                                                                                    <div class="custom-control custom-switch d-flex align-items-center">
                                                                                        <input type="checkbox" class="custom-control-input" vlaue="1" name="{{ current(explode('.',$picture)) }}" id="customSwitch{{ $loop->index }}">
                                                                                        <label class="custom-control-label" for="customSwitch{{ $loop->index }}"></label>
                                                                                        <span class="switch-label"><small>Watermark</small></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a title="remove" href="javascript:void(0)" class="delete-all-img d-blok text-dark"><i class="feather icon-trash-2"></i> Delete All</a>
                                                        <div class="custom-control custom-switch d-flex align-items-center">
                                                            <input type="checkbox" class="custom-control-input" id="checkAllWatermark">
                                                            <label class="custom-control-label" for="checkAllWatermark"></label>
                                                            <span class="switch-label">Watermark All</span>
                                                        </div>
                                                        <!--<button type="button" class="btn btn-primary" id="checkAllWatermark"></button>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row m-0">
                                    <div class="col-11">
                                        <div class="form-group form-label-group mt-2">
                                            <input type="text" class="form-control char-textarea" id="VideoLink" name="VideoLink" value="{{ ($Property) ? $Property->video_link : '' }}" placeholder="Video Link" {{ ($Property) ? $disabled2 : '' }}>
                                            <label for="Video Link">Video Link</label>
                                        </div>
                                    </div>
                                    <div class="col-1">
                                        <a href="#" target="_blank" class="video_view font-medium-3 mt-2"><i class="feather icon-video"></i></a>
                                    </div>
                                </div>
                                {{--@if($admin->type<=2)--}}
                                    <div class="row m-0 land_department_qr_box" style="padding: 7px;border: 1px solid #d9d9d9;border-radius: 5px;color: rgba(34, 41, 47, 1) !important;">
                                        <div class="col-4">
                                            <div class="p-image" style="width: 65px;height: 65px;">
                                                <img id="land_department_qr_Preview" src="{{ ($Property && $Property->land_department_qr) ? '/storage/'.$Property->land_department_qr : '/images/Default.png'}}" style="border-color: #ccc; border-radius: 0">
                                                <div class="profile-input" style="border-radius:0">
                                                    <label for="land_department_qr"><i class="feather icon-camera font-large-2 text-white"></i></label>
                                                    <input class="file-upload d-none" name="land_department_qr" type="file" id="land_department_qr" accept="image/">
                                                </div>
                                                <input type="hidden" id="land_department_qr_check" value="{{ ($Property && $Property->land_department_qr) ? $Property->land_department_qr : ''}}">
                                            </div>
                                        </div>
                                        <div class="col-8 d-flex align-items-center">
                                            <label for="land_department_qr">Land Department QR code</label>
                                        </div>
                                    </div>
                                {{--@endif--}}
                                <div class="form-group form-label-group mt-2">
                                    <input data-length="100" type="text" class="form-control" id="Video_360_Degrees" name="Video_360_Degrees" value="{{ ($Property) ? $Property->video_360_degrees : '' }}" placeholder="360 Degrees Photos Link" {{ ($Property) ? $disabled2 : '' }}>
                                    <label for="Video_360_Degrees">360 Degrees Photos Link</label>
                                </div>
                                <div class="form-group form-label-group mt-2">
                                    <input data-length="100" type="text" class="form-control char-textarea" id="Title" name="Title" required value="{{ ($Property) ? $Property->title : '' }}" placeholder="Title" {{ ($Property) ? $disabled2 : '' }}>
                                    <label for="Title">Title <span>*</span></label>
                                    <small class="counter-value float-right"><span class="char-count">0</span> / 100 </small>
                                </div>

                                <div class="form-group form-label-group mt-2">
                                    <input data-length="100" type="text" class="form-control char-textarea" id="WebsiteTitle" name="WebsiteTitle" value="{{ ($Property) ? $Property->website_title : '' }}" placeholder="Website Title" {{ ($Property) ? $disabled2 : '' }}>
                                    <label for="Title">Website Title</label>
                                    <small class="counter-value float-right"><span class="char-count">0</span> / 100 </small>
                                </div>

                                <div class="mb-1">
                                    <fieldset class="form-group form-label-group mb-0">
                                        <textarea data-length="1500" class="form-control char-textarea" name="Description" id="Description" required rows="12" placeholder="Description" {{ ($Property) ? $disabled2 : '' }}>{{ ($Property) ? $Property->description : '' }}</textarea>
                                        <label>Description <span>*</span></label>
                                    </fieldset>
                                    <small class="counter-value float-right"><span class="char-count">0</span> / 1500 </small>
                                </div>

                                <div class="form-group form-label-group mt-2">
                                    <input type="text" class="form-control" id="USP" name="USP" value="{{ ($Property) ? $Property->usp : '' }}" placeholder="USP" {{ ($Property) ? $disabled : '' }}>
                                    <label>USP</label>
                                </div>

                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="USP2" name="USP2" value="{{ ($Property) ? $Property->usp2 : '' }}" placeholder="USP" {{ ($Property) ? $disabled : '' }}>
                                    <label>USP</label>
                                </div>

                                <div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="USP3" name="USP3" value="{{ ($Property) ? $Property->usp3 : '' }}" placeholder="USP" {{ ($Property) ? $disabled : '' }}>
                                    <label>USP</label>
                                </div>

                                {{--<div class="form-group form-label-group">
                                    <input type="text" class="form-control" id="USP4" name="USP4" value="{{ ($Property) ? $Property->usp4 : '' }}" placeholder="USP" {{ ($Property) ? $disabled : '' }}>
                                    <label>USP</label>
                                </div>--}}

                                <div class="col-sm-12">
                                    <div class="form-group form-label-group">
                                        <a class="w-100 d-block" style="padding: 7px;border: 1px solid #d9d9d9;border-radius: 5px;color: rgba(34, 41, 47, 1) !important;" data-toggle="modal" href="#Features">Amenities</a>
                                    </div>
                                </div>

                                <!-- Modal Features -->
                                <div class="modal fade text-left" id="Features" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel16">Amenities</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="mt-2">
                                                    <h5 class="text-primary">Property Amenities</h5>

                                                    <div class="row m-0">
                                                        @foreach($Amenities as $ame)

                                                            @php
                                                                $checked='';
                                                                if($Property){
                                                                    $PropertyFeature=App\Models\PropertyFeature::where('property_id', $Property->id)->where('feature_id', $ame->id)->first();
                                                                    if($PropertyFeature)
                                                                        $checked="checked";
                                                                }
                                                            @endphp
                                                            <div class="col-sm-3" style="margin-top: 5px">
                                                                <fieldset>
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input" name="FeaturesCheck[]" id="FeaturesCheck{{ $ame->id }}" value="{{ $ame->id }}" {{$checked}} {{ ($Property) ? $disabled : '' }}>
                                                                        <label class="custom-control-label" for="FeaturesCheck{{ $ame->id }}">{{ $ame->name }}</label>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item"><a class="nav-link active" id="Owner-tab" data-toggle="tab" href="#Owner" aria-controls="Owner" role="tab" aria-selected="true">Owner</a></li>
                                <li class="nav-item"><a class="nav-link" id="Documents-tab" data-toggle="tab" href="#Documents" aria-controls="Documents" role="tab" aria-selected="true">Documents</a></li>
                                @if($Property)
                                    {{--                                <li class="nav-item"><a class="nav-link" id="Preview-tab" data-toggle="modal" href="#PreviewModal" aria-controls="Preview" role="tab" aria-selected="true">Preview</a></li>--}}
                                    @if($admin->type<3)<li class="nav-item"><a class="nav-link" id="Dld-tab" data-toggle="modal" href="#DLDModal" aria-controls="dld" role="tab" aria-selected="true">DLD</a></li>@endif
                                @endif
                            </ul>
                            <div class="custom-scrollbar" style="max-height: 250px">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="Owner" aria-labelledby="Owner-tab" role="tabpanel">
                                        <div class="row m-0 mt-1 contact-select-box">
                                            <div class="col-sm-12">
                                                <div class="form-label-group form-group">
                                                    <select class="select-2-user form-control" name="contact" multiple="multiple"></select>
                                                    <label for="SearchRepository">select contact (type and hit go) <span>*</span></label>
                                                </div>
                                            </div>
                                            {{--<div class="col-sm-3">
                                                <button type="button" class="btn btn-primary waves-effect waves-light search-contact">Go</button>
                                            </div>--}}
                                            <div class="col-12">
                                                <a data-toggle="modal" href="#AddContactModal" id="add-contact-btn" class="d-block px-2">Add new Contact</a>
                                            </div>
                                        </div>

                                        <ul class="list-group list-group-flush property-owner-list" style="display: block;">

                                        </ul>
                                    </div>
                                    <div class="tab-pane fade" id="Documents" aria-labelledby="Documents-tab" role="tabpanel">
                                        <fieldset class="form-group mb-0">
                                            <label for="passport-file">Passport</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="passport-file" id="passport-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".passport-progress-bar" data-input="#Passport" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="passport-file">{{ ($Property && $Property->passport) ? 'Passport file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="Passport"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="Passport" name="Passport" value="{{ ($Property) ? $Property->passport : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped passport-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="title-deed-file">Title Deed</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="title-deed-file" id="title-deed-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".title-deed-progress-bar" data-input="#TitleDeed" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="title-deed-file">{{ ($Property && $Property->title_deed) ? 'Title Deed file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="TitleDeed"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="TitleDeed" name="TitleDeed" value="{{ ($Property) ? $Property->title_deed : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped title-deed-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="form-a-file">Form A / Rental Form</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="form-a-file" id="form-a-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".form-a-progress-bar" data-input="#FormA" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="form-a-file">{{ ($Property && $Property->form_a) ? 'Form A / Rental Form file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="FormA"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="FormA" name="FormA" value="{{ ($Property) ? $Property->form_a : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped form-a-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="eid-front-file">EID Front</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="eid-front-file" id="eid-front-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".eid-front-progress-bar" data-input="#EIDFront" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="eid-front-file">{{ ($Property && $Property->eid_front) ? 'EID Front file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="EIDFront"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="EIDFront" name="EIDFront" value="{{ ($Property) ? $Property->eid_front : '' }}">
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
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".eid-back-progress-bar" data-input="#EIDBack" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="eid-back-file">{{ ($Property && $Property->eid_back) ? 'EID Back file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="EIDBack"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="EIDBack" name="EIDBack" value="{{ ($Property) ? $Property->eid_back : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped eid-back-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="power-of-attorney-file">Power of Attorney</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="power-of-attorney-file" id="power-of-attorney-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".power-of-attorney-progress-bar" data-input="#PowerOfAttorney" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="power-of-attorney-file">{{ ($Property && $Property->power_of_attorney) ? 'Power of Attorney file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="PowerOfAttorney"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="PowerOfAttorney" name="PowerOfAttorney" value="{{ ($Property) ? $Property->power_of_attorney : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped power-of-attorney-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="visa-file">Visa</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="visa-file" id="visa-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".visa-progress-bar" data-input="#Visa" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="power-of-attorney-file">{{ ($Property && $Property->visa) ? 'Visa file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="Visa"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="Visa" name="Visa" value="{{ ($Property) ? $Property->visa : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped visa-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>

                                        <fieldset class="form-group mb-0">
                                            <label for="other-doc-file">Other</label>
                                            <div class="d-flex">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input document-upload" data-this="other-doc-file" id="other-doc-file"
                                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".other-doc-progress-bar" data-input="#OtherDoc" {{ ($Property) ? $disabled : '' }}>
                                                    <label class="custom-file-label" for="power-of-attorney-file">{{ ($Property && $Property->other_doc) ? 'Other file' : 'Choose file' }}</label>
                                                </div>
                                                <div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="OtherDoc"><i class="fa fa-download"></i></a></div>
                                            </div>
                                            <input type="hidden" id="OtherDoc" name="OtherDoc" value="{{ ($Property) ? $Property->other_doc : '' }}">
                                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                                <div class="progress-bar bg-teal progress-bar-striped other-doc-progress-bar" role="progressbar"
                                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                            <h5 class="text-primary mt-2">{{($admin->type<=2) ? 'Admin section' : 'Agent section'}}</h5>

                            <div class="custom-scrollbar pr-1" style="max-height: 300px">
                                <div class="row m-0 pt-2">
                                @if($admin->type<=2)
                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="TitleDeedNo" name="TitleDeedNo" value="{{ ($Property) ? $Property->title_deed_no : '' }}" placeholder="Title deed/Oqood no"  {{ ($Property) ? $disabled : '' }}>
                                            <label>Title deed / Oqood no</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="ReraPermit" name="ReraPermit" value="{{ ($Property) ? $Property->rera_permit : '' }}" placeholder="DLD Permit Number"  {{ ($Property) ? $disabled : '' }}>
                                            <label>DLD Permit Number</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="DTCMNumber" name="DTCMNumber" value="{{ ($Property) ? $Property->dtcm_number : '' }}" placeholder="DTCM Number"  {{ ($Property) ? $disabled : '' }}>
                                            <label>DTCM Number</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="StartingDate" name="StartingDate" value="{{ ($Property) ? $Property->starting_date : '' }}" placeholder="Starting Date" readonly>
                                            <label>Starting Date</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group expiration-date-box">
                                            <input type="text" class="form-control required format-picker" autocomplete="off" id="ExpirationDate" name="ExpirationDate" value="{{ ($Property) ? $Property->expiration_date : '' }}" placeholder="Expiration Date" readonly>
                                            <label>Expiration Date</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 {{ ($Property) ? 'd-none' : '' }}">
                                        <fieldset class="form-group form-label-group">
                                            <label for="ClientManager">Client Manager 1 <span>*</span></label><!--Actual Agent-->
                                            <select class="form-control select2" name="ClientManager" id="ClientManager1" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}" {{ ($admin->id==$ClientManager->id) ? 'selected' : ''}}>{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    {{--<div class="col-sm-12 {{ ($Property) ? 'd-none' : '' }}">
                                        <fieldset class="form-group form-label-group">
                                            <label for="ClientManager2">Client Manager 2</label><!--Assigned to-->
                                            <select class="form-control select2" name="ClientManager2" id="ClientManager2" >
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>--}}

                                    <div class="col-sm-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="status">Action <span>*</span></label>
                                            <select class="form-control" name="Status" id="Status" required>
                                                <option value="">Select</option>
                                                @foreach(Status as $key => $value)
                                                    <option value="{{ $key }}">{{ $value.( ($key==5 || $key==7)? $Company->sample: '') }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-sm-12 d-none">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="ViewingArrangement" name="ViewingArrangement" value="{{ ($Property) ? $Property->viewing_arrangement : '' }}" placeholder="Viewing Arrangement">
                                            <label>Viewing Arrangement</label>
                                        </div>
                                    </div>

                                    {{ ($Property) ? ( ($Property->verify_status) ? '<div class="col-sm-6">
                                                        <button type="button" class="btn btn-outline-success mr-1 mb-1 waves-effect waves-light float-left ">Confirm Listing</button>
                                                      </div>' : '' ) : '' }}

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <a class="w-100 d-block" style="padding: 7px;border: 1px solid #d9d9d9;border-radius: 5px;color: rgba(34, 41, 47, 1) !important;" data-toggle="modal" href="#PortalsModal">Portals</a>
                                        </div>
                                    </div>

                                    <!-- Modal Portals -->
                                    <div class="modal fade text-left" id="PortalsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel16">Portals</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="">
                                                        <ul class="list-unstyled m-0 row">
                                                            @php
                                                                $Portals=\App\Models\Portal::get();
                                                            @endphp
                                                            @foreach($Portals as $portal )
                                                                @php
                                                                    $checked='checked';
                                                                    if($Property){
                                                                        $PortalProperty=\App\Models\PortalProperty::where('portal_id',$portal->id)->where('property_id',$Property->id)->first();
                                                                        if(!$PortalProperty)
                                                                            $checked='';
                                                                    }

                                                                    $portalLogo='/images/'.$portal->logo;
                                                                    if($portal->id==4){
                                                                        $portalLogo='/storage/'.$Company->logo;
                                                                    }
                                                                @endphp
                                                                <li class="d-inline-block col-sm-3"  style="margin-top: 5px">
                                                                    <div class="custom-control custom-checkbox">
                                                                        <input type="checkbox" class="custom-control-input d-none" name="PortalCheck[]" value="{{$portal->id}}" id="PortalCheck{{$portal->id}}" {{$checked}}>
                                                                        <label class="custom-control-label" for="PortalCheck{{$portal->id}}">
                                                                            <img width="100" src="{{$portalLogo}}">
                                                                        </label>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-sm-12 {{ ($Property) ? 'd-none' : '' }}">
                                        <fieldset class="form-group form-label-group">
                                            <label for="ClientManager">Client Manager 1 <span>*</span></label><!--Actual Agent-->
                                            <select class="form-control" name="ClientManager" id="ClientManager" required {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}" {{ ( ($admin->type==3 || $admin->type==4) && $admin->id==$ClientManager->id) ? 'selected' : ''}}>{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    {{--<div class="col-sm-6 {{ ($Property) ? 'd-none' : '' }}">
                                        <fieldset class="form-group form-label-group">
                                            <label for="ClientManager2">Client Manager 2</label><!--Assigned to-->
                                            <select class="form-control" name="ClientManager2" id="ClientManager2" >
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>--}}
                                    {{--<input type="hidden" name="ClientManager" value="{{ $admin->id }}">--}}
                                    <div class="col-sm-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="status">Action <span>*</span></label>
                                            <select class="form-control" name="Status" id="Status" required>
                                                <option value="">Select</option>
                                                <option value="11">{{Status[11]}}</option>
                                                <option value="2">{{Status[2]}}</option>
                                                <option value="4">{{Status[4]}}</option>
                                                @if($Property && !in_array($Property->status, [2,4,11]))
                                                    <option value="{{$Property->status}}">{{Status[$Property->status].( ($Property->status==5 || $Property->status==7)? $Company->sample: '')}}</option>
                                                @endif
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-sm-12 d-none">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="ViewingArrangement" name="ViewingArrangement" value="{{ ($Property) ? $Property->viewing_arrangement : '' }}" placeholder="Viewing Arrangement">
                                            <label>Viewing Arrangement</label>
                                        </div>
                                    </div>
                                @endif
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="status">For Portals <span>*</span></label>
                                            <select class="form-control" name="ForPortals" id="ForPortals" required>
                                                <option value="1">Plot</option>
                                                <option value="2" selected>BUA</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Exclusive</label>
                                            <select class="custom-select form-control" id="Exclusive" name="Exclusive" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Featured</label>
                                            <select class="custom-select form-control" id="Featured" name="Featured" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <label>Ask For Price</label>
                                            <select class="custom-select form-control" id="AskForPrice" name="AskForPrice" {{ ($Property) ? $disabled : '' }}>
                                                <option value="">Select</option>
                                                <option value="Yes">Yes</option>
                                                <option value="No">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <div class="row m-0">
                                <div class="col-sm-4">
                                    {!! $requester !!}
                                </div>
                                <div class="col-sm-8 d-flex flex-sm-row flex-column justify-content-end">
                                    @if($Property && $Property->status==11 && $PropertyStatusHistory->rfl_status==0 && $admin->type<=2)<button type="button" id="reject" class="btn bg-gradient-danger glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light" data-toggle="modal" data-target="#rejectModal" style="width: 120px">Reject</button>@endif
                                    @if($Property && $Property->status==11 && $PropertyStatusHistory->rfl_status==0 && $admin->type<=2)<button type="button" id="listed" class="btn bg-gradient-success glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light" style="width: 120px">Listing</button>@endif

                                    @if($Property && $Property->status==11)<input type="hidden" name="psh_id" value="{{ ($PropertyStatusHistory) ? $PropertyStatusHistory->id : '' }}">@endif
                                    @if($Property)<input type="hidden" name="_id" value="{{ ($Property) ? $Property->id : '' }}">@endif

                                    <button type="button" id="submit" class="btn  bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light float-right mx-auto" style="width: 120px">{{ ($Property) ? 'Update' : 'Add' }}</button>
                                    <button type="submit" name="submit" value="{{ ($Property) ? $Property->id : 'add' }}" class="btn  bg-gradient-info glow mb-1 mb-sm-0 mr-0 mr-sm-1 waves-effect waves-light d-none">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($Property && $Property->status==11 && $admin->type<=2)
        <!-- Modal Reject -->
        <div class="modal fade text-left" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('request-listed.reject')}}" novalidate class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Reject</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <textarea class="form-control" rows="4" id="reject_reason" name="reject_reason" placeholder="Reason" required></textarea>
                                <label>Reason</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="psh_id" value="{{$PropertyStatusHistory->id}}">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($Property)
        @php
            $PropertyType=App\Models\PropertyType::where('id',$Property->property_type_id)->first();
            $MasterProject=App\Models\MasterProject::where('id',$Property->master_project_id)->first();
            $Project=App\Models\Community::where('id',$Property->community_id)->first();
            $VillaType=App\Models\VillaType::where('id',$Property->villa_type_id)->first();
            $Bedroom=App\Models\Bedroom::where('id',$Property->bedroom_id)->first();
            $Bathroom=App\Models\Bathroom::where('id',$Property->bathroom_id)->first();
            $View=App\Models\View::where('id',$Property->view)->first();

            $PFeatures = App\Models\PropertyFeature::join('features', 'features.id', '=', 'property_features.feature_id')->where('property_id',$Property->id)->get();

            $expected_price=0;
            if($Property->expected_price){
                $expected_price=$Property->expected_price;
            }

            if($Property->listing_type_id==2){
                if($Property->yearly){
                    $expected_price=$Property->yearly;
                }else if($Property->monthly){
                    $expected_price=$Property->monthly;
                }else if($Property->weekly){
                    $expected_price=$Property->weekly;
                }else{
                    $expected_price=$Property->daily;
                }
            }
        @endphp

        @if($admin->type<3)
            <!-- Modal DLDModal -->
            <div class="modal fade text-left" id="DLDModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel16">DLD Brochure</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row m-0" id="dld-print-box">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="mb-2 mt-1 p-2 col-12" style="background-color:#000;color: #eace06;">
                                            <p class="mb-0">Referrence Number: {{$Company->sample}}-{{(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num}}</p>
                                            <p class="mb-0">Community: {{($MasterProject) ? $MasterProject->name : ''}}</p>
                                            <p class="mb-0">Sub-Community: {{($Project) ? $Project->name : ''}}</p>
                                            <p class="mb-0">Price: {{number_format($expected_price)}} AED</p>
                                            <p class="mb-0">{{($Company && $Company->name) ? $Company->name : ''}} ORN: {{($Company && $Company->rera_orn) ? $Company->rera_orn : ''}}</p>
                                        </div>

                                        <div class=="col-12">
                                            {{--<div class="row">
                                                @if($Property)
                                                    @if($Property->pictures)
                                                        @foreach(explode(',', $Property->pictures) as $picture)
                                                            <div class="col-sm-4 mb-1">
                                                                <img class="img-fluid" src="/storage/{{ $picture }}">
                                                            </div>
                                                            @if($loop->index==5)
                                                                @break
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @endif
                                            </div>--}}
                                            <div class="wrap-modal-print">
                                                <div class="row">
                                                    @if($Property)
                                                        @if($Property->pictures)
                                                            @foreach(explode(',', $Property->pictures) as $picture)
                                                                <div class="col-sm-6"><img class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                                @if($loop->index=='1')
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </div>
                                                <div class="row mt-1">
                                                    @if($Property)
                                                        @if($Property->pictures)
                                                            @foreach(explode(',', $Property->pictures) as $picture)
                                                                @if($loop->index<='1')
                                                                    @continue
                                                                @endif
                                                                <div class="col-sm-3 px-1"><img class="img-fluid" src="/laravel/storage/app/public/images/{{ $picture }}" class="rounded"></div>
                                                                @if($loop->index=='5')
                                                                    @break
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <h5 class="mt-1 mb-0 pb-1" style="border-bottom: 1px solid #2c2c2c;">Description</h5>
                                            <div id="dld-description" class="position-relative pt-2">{{--white_goverlay--}}
                                                <p>{!!($Property) ? nl2br($Property->description) : ''!!}</p>
                                            </div>
                                            {{--                                        <div class="w-100">--}}
                                            {{--                                            <a href="javascript:void(0);" class="read-more float-right">Read More</a>--}}
                                            {{--                                        </div>--}}
                                        </div>


                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="print-preview btn bg-gradient-info glow waves-effect waves-light" data-print="dld-print-box">Print</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @endif

    <!-- Modal Add Cluster / Street / Frond -->
    <div class="modal fade text-left" id="AddClusterStreetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="" class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Add Cluster / Street / Frond</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="MasterProject">Master Project <span>*</span></label>
                                <select class="custom-select form-control select2 select2 master-propject-add" name="MasterProject" required disabled>
                                    <option value="">Select</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="Community">Project <span>*</span></label>
                                <select class="custom-select form-control select2 community-add" name="Community" required disabled>
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group expiration-date-box">
                                <input type="text" class="form-control" name="name" placeholder="Cluster / Street / Frond">
                                <label>Cluster / Street / Frond</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button"  id="add-cluster-street-btn" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Add Type -->
    <div class="modal fade text-left" id="AddTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Add Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="MasterProject">Master Project <span>*</span></label>
                                <select class="custom-select form-control select2 master-propject-add" name="MasterProject" required disabled>
                                    <option value="">Select</option>

                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="Community">Project <span>*</span></label>
                                <select class="custom-select form-control select2 community-add" name="Community" required disabled>
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group expiration-date-box">
                                <input type="text" class="form-control" name="name" placeholder="Type">
                                <label>Type</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="add-type-btn" class="btn btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Contact -->
    <div class="modal fade" id="AddContactModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <form method="post" action="{{ route( 'contact.add.ajax') }}" id="add-contact-form" class="property-detail-form modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="text-primary">Client Details</h5>
                            <div class="custom-scrollbar" style="max-height: 450px;">
                                <div class="m-0 row pt-1">
                                    <div class="col-6">
                                        <div class="form-group form-label-group">
                                            <label for="ContactCategory">Contact Category<span>*</span></label>
                                            <select class="custom-select form-control" id="ContactCategory" name="ContactCategory" required>
                                                <option value="">Select</option>
                                                <option value="agent">Agent</option>
                                                <option value="owner">Owner</option>
                                                <option value="developer">Developer</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 buyer tenant agent owner">
                                        <div class="form-group form-label-group">
                                            <label>Contact Source <span>*</span></label>
                                            <select class="custom-select form-control select2" id="Contact_Source" name="ContactSource" >
                                                <option value="">Select</option>
                                                @foreach($ContactSources as $CSource)
                                                    <option value="{{ $CSource->id }}">{{ $CSource->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 developer d-none">
                                        <div class="form-group form-label-group">
                                            <label for="Developer">Developer <span>*</span></label>
                                            <select class="custom-select form-control select2" id="Developer" name="Developer">
                                                <option value="">Select</option>
                                                @php
                                                    $developers=\App\Models\Developer::get();
                                                @endphp
                                                @foreach($developers as $dev)
                                                    <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-label-group">
                                            <label>Client Manager1 <span>*</span></label>
                                            <select class="custom-select form-control select2" name="ClientManager" required>
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}" {{ ( $admin->id==$ClientManager->id) ? 'selected' : ''}} >{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-label-group">
                                            <label>Client Manager2</label>
                                            <select class="custom-select form-control select2" name="ClientManagerTwo">
                                                <option value="">Select</option>
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 agent">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" name="AgencyName" placeholder="Agency Name">
                                            <label>Agency Name</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="custom-scrollbar" style="max-height: 150px;">
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
                                            <input type="text" name="City" class="form-control" placeholder="City">
                                            <label for="City">City</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" name="Address" class="form-control" placeholder="Address Line 1">
                                            <label>Address Line 1</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(request('dc'))
                                @if($data_center->name && $data_center->name != '-') <p class="m-0"><b>Name:</b> {{ $data_center->name }}</p> @endif
                                @if($data_center->phone_number && $data_center->phone_number != '-') <p class="m-0"><b>Phone number:</b> {{ $data_center->phone_number }}</p> @endif
                                @if($data_center->phone_number_2 && $data_center->phone_number_2 != '-') <p class="m-0"><b>Phone number 2:</b> {{ $data_center->phone_number_2 }}</p> @endif
                                @if($data_center->email && $data_center->email != '-') <p class="m-0"><b>Email:</b> {{ $data_center->email }}</p> @endif
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <h5 class="text-primary mb-1">Client Profile</h5>
                            <div class="custom-scrollbar pr-1" style="max-height: 450px;">
                                <div class="m-0 row pt-1">
                                    <div class="col-12 col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label>Title <span>*</span></label>
                                            <select class="form-control" name="Title" required>
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
                                            <input type="text" class="form-control" name="FirstName" placeholder="First Name" required>
                                            <label>First Name <span>*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" name="LastName" placeholder="Last Name" required>
                                            <label>Last Name <span>*</span></label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control mobile-number-paste" value="+971" onkeypress="return isNumber(event)" id="MainNumber" name="MainNumber" placeholder="UAE Mobile Numbe" maxlength="13">
                                            <label>UAE Mobile Number</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control country-code mobile-number-paste" onkeypress="return isNumber(event)" id="NumberTwo" name="NumberTwo" placeholder="Second Number" maxlength="19">
                                            <label>Second Number</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="Email" name="Email" placeholder="Email 1">
                                            <label>Email 1</label>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" id="EmailTwo" name="EmailTwo" placeholder="Email 2">
                                            <label>Email 2</label>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label>Nationalities</label>
                                            <select class="form-control select2" id="Nationalities" name="Nationalities">
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <div class="form-group form-label-group">
                                            <input type="text" class="form-control" name="PreferredLanguage" placeholder="Language">
                                            <label>Language</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-info waves-effect waves-light" id="add-new-contact-submit">Add</button>
                    <button type="submit" class="btn bg-gradient-info waves-effect waves-light d-none" id="add-contact">Add</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Form wizard with step validation section end -->

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script type="text/javascript" src="/js/scripts/magnific-popup.min.js"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/printThis.js"></script>
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>

    <script>
        populateCountries("Nationalities", "");
        populateCountries("Country", "");

        function autoFillInfo(){
            $('#preview-price').html( $('#ExpectedPrice').val() );

            $('#preview-price').html( $('#ExpectedPrice').val() );
        }
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

    @if($Property)
        <script>
            $(document).ready(function(){
                $("#ListingType{{ $Property->listing_type_id }}").attr('checked', 'checked');
                $("#Status").val("{{ $Property->status }}").change();
                $("#ForPortals").val("{{ ($Property->size_for_portals)? $Property->size_for_portals : '2' }}").change();
                $("#Status2").val("{{ $Property->status2 }}").change();
                // $("#PropertyType").val("{{ $Property->property_type_id }}");
                $("#ContactSource").val("{{ $Property->contact_source_id }}").change();
                $("#VendorMotivation").val("{{ $Property->vendor_motivation_id }}");
                $("#Type").val("{{ $Property->type }}").change();
                $("#Bedrooms").val("{{ $Property->bedroom_id }}");
                $("#Bathrooms").val("{{ $Property->bathroom_id }}");
                $("#VaastuOrientation").val("{{ $Property->vaastu_orientation_id }}").change();
                $("#ClientManager1").val("{{ $Property->client_manager_id }}");
                $("#ClientManager2").val("{{ $Property->client_manager2_id }}");
                $("#OffPlan").val("{{ $Property->off_plan }}");//.change();
                $("#property_management").val("{{ $Property->property_management }}");

                $("#Exclusive").val("{{ $Property->exclusive }}");
                $("#Published").val("{{ $Property->published }}");
                $("#Featured").val("{{ $Property->featured }}");
                $("#AskForPrice").val("{{ $Property->ask_for_price }}");
                $("#OccupancyStatus").val("{{ $Property->occupancy_status }}");
                $("#ReferenceNumber").val("{{$Company->sample}}-{{(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num}}");


                $("#Maid").val("{{ $Property->maid }}");
                $("#Driver").val("{{ $Property->driver }}");
                $("#Study").val("{{ $Property->study }}");
                $("#Storage").val("{{ $Property->storage }}");
                $("#View").val("{{ $Property->view }}").change();
                $("#Parking").val("{{ $Property->parking }}");

                $("#Furnished").val("{{ $Property->furnished}}");
                $("#VacatingNotice").val("{{ $Property->vacating_notice}}");
                $("#NumberCheques").val("{{ $Property->number_cheques}}");
                // $("#Frequency").val("{{ $Property->frequency}}");

                @if($pf_l_id!='')
                $('#PFLocation').empty().append('<option selected value="'+'{{ $pf_l_id }}'+'">'+'{{ $pf_name_address }}'+'</option>');
                $('#PFLocation').select2('data', {
                    id: '{{ $pf_l_id }}',
                    label:'{{ $pf_name_address }}'
                });
                @endif
            });
        </script>
    @endif

    <script>
        $('.print-preview').click(function(){
            let element=$(this).data('print');
            $("#"+element).printThis({
                // debug: false,               // show the iframe for debugging
                importCSS: true,            // import parent page css
                importStyle: true,         // import style tags
                printContainer: true,       // print outer container/$.selector
                loadCSS: "/css/bootstrap.css",                // path to additional css file - use an array [] for multiple
                // pageTitle: "",              // add title to print page
                removeInline: false,        // remove inline styles from print elements
            });
        });
    </script>

    <script>
        $("#Emirate").val("{{ ($Property) ? $Property->emirate_id : '2' }}").trigger('change');

        $('input[type=radio][name=ListingType]').change(function() {
            changeReferenceNumber(this.value);
        });

        function changeReferenceNumber(value){
            $('#ExpectedPrice , #DailyPrice , #WeeklyPrice , #MonthlyPrice , #YearlyPrice').parent().parent().addClass('d-none');
            $("#ExpectedPrice").parent().children('label').children('span').remove();

            $('#DailyPrice , #WeeklyPrice , #MonthlyPrice').removeAttr('disabled');
            if (value == '1') {
                $("#ReferenceNumber").val("{{$Company->sample}}-S-{{($Property) ? $Property->ref_num : $propertyMax}}");

                // $('#Frequency').parent().parent().addClass('d-none');
                // $("#Frequency").parent().children('label').children('span').remove();

                $("#VendorMotivation").parent().parent().removeClass('d-none');//removeAttr('disabled');

                $("#Status2").val('{{( ($Property) ? $Property->status2 : '' )}}').change().parent().parent().removeClass('d-none');//removeAttr('disabled');

                $("#OffPlan").val('{{( ($Property) ? $Property->off_plan : '' )}}');//.removeAttr('disabled');//.change();
                OffPlan();//$("#OffPlan").change();
                $("#CompletionDate").val('{{( ($Property) ? $Property->completion_date : '' )}}').change();//.removeAttr('disabled');
                //$("#OffPlan").parent().children('label').append('<span>*</span>');

                $('#ExpectedPrice').parent().parent().removeClass('d-none');
                $("#ExpectedPrice").parent().children('label').append('<span>*</span>');

            }
            else if (value == '2') {
                $("#ReferenceNumber").val("{{$Company->sample}}-R-{{($Property) ? $Property->ref_num : $propertyMax}}");

                $('#Frequency').parent().parent().removeClass('d-none');
                $("#Frequency").parent().children('label').append('<span>*</span>');

                $('#VendorMotivation').parent().parent().addClass('d-none');//attr('disabled','disabled');

                $("#Status2").val('{{( ($Property) ? $Property->status2 : '3' )}}').change();//.attr('disabled','disabled');

                //$("#OffPlan").parent().children('label').children('span').remove();
                //$("#OffPlan").val('').attr('disabled','disabled');
                OffPlan();
                $("#CompletionDate").val('{{( ($Property) ? $Property->completion_date : '' )}}').change().parent().parent().addClass('d-none');//attr('disabled','disabled');

                $('#DailyPrice').parent().parent().removeClass('d-none');
                $('#WeeklyPrice').parent().parent().removeClass('d-none');
                $('#MonthlyPrice').parent().parent().removeClass('d-none');
                $('#YearlyPrice').parent().parent().removeClass('d-none');
                if($('#Type').val()==2){
                    $('#DailyPrice , #WeeklyPrice , #MonthlyPrice').parent().parent().addClass('d-none');//attr('disabled','disabled');
                }

            }

            $('#Type').change();
        }

        changeReferenceNumber('{{($Property) ? $Property->listing_type_id : '1'}}');

        $('#VideoLink').change(function(){
            videoView();
        });
        videoView();
        function videoView(){
            $('.video_view').attr('href','#').addClass('d-none').removeClass('d-block');
            let link=$('#VideoLink').val();
            if(link!='')
                $('.video_view').attr('href',link).removeClass('d-none').addClass('d-block');
        }
        $('body').on('click','.note-description td',function() {
            let html=$(this).children('.action').html();
            if (!html) {
                $('#ViewModal .modal-title').html( $(this).parent().data('title') );
                $('#ViewModal .modal-body').html( $(this).parent().data('desc') );
            }
        });

        function OffPlan(){
            let val=$('#OffPlan').val();
            $('#CompletionDate').parent().parent().addClass('d-none');//attr('disabled','disabled');
            $('#OffPlanDetailsSaleType , #OffPlanDetailsDldWaiver , #OffPlanDetailsOriginalPrice , #OffPlanDetailsAmountPaid').parent().parent().addClass('d-none');//attr('disabled','disabled');
            $('#OffPlanDetailsSaleType').val('{{( ($Property) ? $Property->offplanDetails_saleType : '' )}}').change();
            $('#OffPlanDetailsDldWaiver').val('{{( ($Property) ? $Property->offplanDetails_dldWaiver : '' )}}');
            $('#OffPlanDetailsOriginalPrice').val('{{( ($Property) ? $Property->offplanDetails_originalPrice : '' )}}');
            $('#OffPlanDetailsAmountPaid').val('{{( ($Property) ? $Property->offplanDetails_amountPaid : '' )}}');

            $("#OffPlanDetailsSaleType").parent().children('label').children('span').remove();
            $("#OffPlanDetailsOriginalPrice").parent().children('label').children('span').remove();

            if(val=='completed' || val=='completed_primary'){
                $('#OffPlanDetailsSaleType').val('').change();
                $('#OffPlanDetailsDldWaiver').val('');
                $('#OffPlanDetailsOriginalPrice').val('');
                @if(!$Property) $('#ExpectedPrice').val(''); @endif
                $('#OffPlanDetailsAmountPaid').val('');
            }

            $('#Status2').val('{{($Property) ? $Property->status2 : ''}}').change();
            if(val=='off_plan' || val=='off_plan_primary'){
                $('#Status2').val('5').change();
                $('#CompletionDate').parent().parent().removeClass('d-none');//removeAttr('disabled');
                $('#OffPlanDetailsSaleType').parent().children('label').append('<span>*</span>');
                if(val=='off_plan')
                    $('#OffPlanDetailsSaleType').val(2).change();
                if(val=='off_plan_primary')
                    $('#OffPlanDetailsSaleType').val(1).change();
                $('#OffPlanDetailsOriginalPrice').parent().children('label').append('<span>*</span>');
                $('#OffPlanDetailsSaleType , #OffPlanDetailsDldWaiver , #OffPlanDetailsOriginalPrice , #OffPlanDetailsAmountPaid').parent().parent().removeClass('d-none');//removeAttr('disabled');
            }

            let ListingType=$('input[type=radio][name=ListingType]:checked').val();

            if($('#PropertyType').val()!='19' && $('#PropertyType').val()!='29') {
                $('#Status2').parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#Status2").parent().children('label').children('span').remove();
                $("#Status2").parent().children('label').append('<span>*</span>');

                if (val == 'off-plan' && ListingType != '2') {
                    $('#Status2').val('').parent().parent().addClass('d-none');
                    $('#Status2').change();
                    //$('#Status2').attr('disabled', 'disabled').val('').change();
                    $("#Status2").parent().children('label').children('span').remove();
                }
            }
        }

        function OffPlanDetailsSaleType(){
            let val=$('#OffPlanDetailsSaleType').val();
            $("#OffPlanDetailsDldWaiver").parent().children('label').children('span').remove();
            $("#OffPlanDetailsAmountPaid").parent().children('label').children('span').remove();
            if(val==1){
                $('#OffPlanDetailsDldWaiver').parent().children('label').append('<span>*</span>');
            }
            if(val==2){
                $('#OffPlanDetailsAmountPaid').parent().children('label').append('<span>*</span>');
            }
        }

        $('#OffPlanDetailsOriginalPrice').change(function(){
            $('#ExpectedPrice').val( $(this).val() ).change();
        });

        $('#OffPlan').change(function(){
            OffPlan()
        });

        $('#OffPlanDetailsSaleType').change(function(){
            OffPlanDetailsSaleType()
        });

        $('#Status2').change(function(){
            let value=$('#Status2').val();

            if (value == '2') {
                $("#RentPrice").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedFrom").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedFor").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedUntil").parent().removeClass('error').children('label').children('span').remove();
                $("#VacatingNotice").parent().removeClass('error').children('label').children('span').remove();
                //$("#AvailableFrom").parent().removeClass('error').children('label').children('span').remove();
                $("#NumberCheques").parent().removeClass('error').children('label').children('span').remove();

                $("#RentPrice").parent().children('label').append('<span>*</span>');
                $("#RentedFrom").parent().children('label').append('<span>*</span>');
                $("#RentedFor").parent().children('label').append('<span>*</span>');
                $("#RentedUntil").parent().children('label').append('<span>*</span>');
                $("#VacatingNotice").parent().children('label').append('<span>*</span>');
                //$("#AvailableFrom").parent().children('label').append('<span>*</span>');
                $("#NumberCheques").parent().children('label').append('<span>*</span>');
            } else {
                $("#RentPrice").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedFrom").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedFor").parent().removeClass('error').children('label').children('span').remove();
                $("#RentedUntil").parent().removeClass('error').children('label').children('span').remove();
                $("#VacatingNotice").parent().removeClass('error').children('label').children('span').remove();
                //$("#AvailableFrom").parent().removeClass('error').children('label').children('span').remove();
                $("#NumberCheques").parent().removeClass('error').children('label').children('span').remove();
            }

            if(value!=2){
                $("#RentPrice").parent().parent().addClass('d-none');//attr('disabled','disabled');
                $("#RentedFrom").parent().parent().addClass('d-none');//attr('disabled','disabled');
                $("#RentedFor").parent().parent().addClass('d-none');//attr('disabled','disabled');
                $("#RentedUntil").parent().parent().addClass('d-none');//attr('disabled','disabled');
                $("#VacatingNotice").parent().parent().addClass('d-none');//attr('disabled','disabled');
                $("#NumberCheques").parent().parent().addClass('d-none');//attr('disabled','disabled');
            }else{
                $("#RentPrice").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#RentedFrom").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#RentedFor").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#RentedUntil").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#VacatingNotice").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#NumberCheques").parent().parent().removeClass('d-none');//removeAttr('disabled');
            }


            if( $('input[type=radio][name=ListingType]:checked').val()==2 ){
                $("#RentPrice").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#NumberCheques").parent().parent().removeClass('d-none');//removeAttr('disabled');
            }


        });


        $('#ContactCategory').change(function(){
            let val=$(this).val();
            $('.buyer , .tenant , .agent , .owner , .developer').addClass('d-none');
            // $.each(val, function( index, value ) {
            $('.'+val).removeClass('d-none');
            // });
        });

        $("#checkAllWatermark").click(function(){
            $('.showAllAttachFileBox  input:checkbox').not(this).prop('checked', this.checked);
        });

        $("#listed").click(function(){
            $('#Status').val('1');
            $('#submit').click();
        });
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
                    url: "{{route('contact.ajax.select.cm')}}",
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
                            <div class="image-box"><img src="${repo.picutre}" /></div>
                            <div class="w-100 ml-1">
                                <div><b>${repo.fullname}</b></div>
                                <div>${repo.main_number}</div>
                                <div>${repo.email}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelection (repo) {
            return repo.fullname ;

        }
    </script>

    <script>
        let property_type_array = ['12','13','14','15','19'];
        HideShowContactSelect();
        function HideShowContactSelect(){
            var count=$('.property-owner-list li').length;
            if(count>0)
                $('.contact-select-box').addClass('d-none');
            else
                $('.contact-select-box').removeClass('d-none');
        }
        @if($Property)
        GetCantact('{{$Property->contact_id}}','edit');
        @endif
        function GetCantact(id,type){
            $.ajax({
                url:"{{ route('contact.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('#AddContactModal form input[name="_token"]').val(),
                    id:id,
                    type:type
                },
                success:function (response) {
                    $('.property-owner-list').html(response);
                    HideShowContactSelect();
                }
            });
        }
        $('.select-2-user').change(function () {
            var id=$('.select-2-user').val();
            GetCantact(id,'');
        });
        $('.add-property-form').on('click','.remove-property-owner',function () {
            $(this).parent().parent().remove();

            HideShowContactSelect();
            $(".contact-select-box select").html("");
            $('.contact-select-box select').change();
        });

        function duplicatePropertyCheck(){
            let id = '{{ ($Property) ? $Property->id : 'null' }}';
            let listing=$('input[type=radio][name=ListingType]:checked').val();
            let emirate=$('#Emirate').val();
            let master_project=$('#MasterProject').val();
            let project=$('#Community').val();
            let cluster_street=$('#ClusterStreet').val();
            let vila_unit_number=$('#VillaNumber').val();
            if(emirate!='' && master_project!='' && project!='' && vila_unit_number!='' ){
                $.ajax({
                    url:"{{ route('property-duplicatePropertyCheck') }}",
                    type:"POST",
                    data:{
                        _token:'{{csrf_token()}}',
                        id:id,
                        listing:listing,
                        master_project:master_project,
                        project:project,
                        cluster_street:cluster_street,
                        vila_unit_number:vila_unit_number
                    },
                    success:function (response) {
                        if(response){
                            $('#ViewModal .modal-title').html( 'This property is already existing' );
                            $('#ViewModal .modal-body').html(`<h5><a target="_blank" href="/admin/property/view/${response.id}">Click here to {{$Company->sample}}-${ ((response.listing_type_id==1) ? 'S' : 'R')+'-'+response.ref_num} </a></h5>`);
                            $('#ViewModal').modal('show');
                        }
                    },error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        }
        $('#MainNumber , #NumberTwo , #Email , #EmailTwo').change(function(){

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
                        main_number: main_number,
                        number_two: number_two,
                        //email: email,
                        //email_two: email_two,
                    },
                    success: function (response) {
                        if (response) {
                            $('#ViewModal .modal-title').html('Already registered');
                            $('#ViewModal .modal-body').html(`<h5><a target="_blank" href="/admin/contact/view/${response.id}">Click here to {{$Company->sample}}_${response.id}</a></h5>`);
                            $('#ViewModal').modal('show');
                        }
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        });

        $('#Status').change(function(){
            let status=$(this).val();

            $('#ViewingArrangement').parent().parent().addClass('d-none');

            if(status==11 || status==2)
                $('#ViewingArrangement').parent().parent().removeClass('d-none');

        });

        $('#add-new-contact-submit').click(function () {
            let error=0;

            let ContactCategory=$('#ContactCategory').val();
            let ContactSource=$('#Contact_Source').val();
            let Developer=$('#Developer').val();

            if(ContactCategory=='developer'){
                if(Developer==''){
                    $("#Developer").parent().addClass('error');
                    error=1
                }
            }else{
                if(ContactSource==''){
                    $("#Contact_Source").parent().addClass('error');
                    error=1
                }
            }

            if(error==0) {
                let number=$('#MainNumber').val();
                let number2=$('#NumberTwo').val();

                if(number.length < 13 && number.length > 4){
                    toast_('','The UAE Mobile Number is not correct.',$timeOut=20000,$closeButton=true);
                    error=1;
                }

                if(error==0) {
                    if ((number == '+971' && number2 == '') || (number.length < 13 && number2.length < 11)) {
                        toast_('','One of the numbers must be filled.',$timeOut=20000,$closeButton=true);
                    } else {
                        $('#add-contact-form button[type="submit"]').click();
                    }
                }
            }else{
                toast_('','Please fill up all required fields.',$timeOut=20000,$closeButton=true);
            }
        });

        $('#add-contact-form').ajaxForm(function(data) {
            if(data=='false'){
                toast_('','Email is already existing.',$timeOut=20000,$closeButton=true);
                $('#add-contact').prop('disabled',false);
            }else if(data=='false1'){
                toast_('','One of the numbers must be filled.',$timeOut=20000,$closeButton=true);
                $('#add-contact').prop('disabled',false);
            }else if(data=='false2'){
                toast_('','Mobile Number is already existing.',$timeOut=20000,$closeButton=true);
                $('#add-contact').prop('disabled',false);
            }else if(data=='false3'){
                toast_('','Email is already existing.',$timeOut=20000,$closeButton=true);
                $('#add-contact').prop('disabled',false);
            }else{
                GetCantact(data,'');
                $('#AddContactModal').modal('toggle');
            }
        });

        $('#Emirate').change(function () {
            let val=$(this).val();
            getMasterProject(val);
            {{ ($Property) ? '' : 'duplicatePropertyCheck();' }}
        });

        $('#VillaNumber').change(function () {
            {{ ($Property) ? '' : 'duplicatePropertyCheck();' }}
        });

        $('#add-cluster-street-btn').click(function () {
            let community=$('#AddClusterStreetModal .community-add').val();
            let name=$('#AddClusterStreetModal input[name="name"]').val();
            if(community==''){
                $('#AddClusterStreetModal .community-add').parent().addClass('error');
            }

            if(name==''){
                $('#AddClusterStreetModal input[name="name"]').parent().addClass('error');
            }

            if(community!='' && name!=''){
                $('#add-cluster-street-btn').html('please wait...').attr('disabled','disabled');
                $.ajax({
                    url:"{{ route( 'cluster-street.add.ajax') }}",
                    type:"POST",
                    data:{
                        _token:'{{ csrf_token() }}',
                        community:community,
                        name:name
                    },
                    success:function (response) {
                        $('#ClusterStreet').html(response.options);
                        $('#ClusterStreet').val(response.selected);
                        $('#AddClusterStreetModal').modal('hide');
                        $('#AddClusterStreetModal input[name="name"]').val('');
                        $('#add-cluster-street-btn').html('Add').removeAttr('disabled','disabled');
                    },error: function (data) {
                        let errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
            // getMasterProject(val);
        });

        $('#add-type-btn').click(function () {
            let community=$('#AddTypeModal .community-add').val();
            let name=$('#AddTypeModal input[name="name"]').val();
            if(community==''){
                $('#AddTypeModal .community-add').parent().addClass('error');
            }

            if(name==''){
                $('#AddTypeModal input[name="name"]').parent().addClass('error');
            }

            if(community!='' && name!=''){
                $('#add-type-btn').html('please wait...').attr('disabled','disabled');
                $.ajax({
                    url:"{{ route( 'villa-type.add.ajax') }}",
                    type:"POST",
                    data:{
                        _token:'{{ csrf_token() }}',
                        community:community,
                        name:name
                    },
                    success:function (response) {
                        $('#VillaType').html(response.options);
                        $('#VillaType').val(response.selected);
                        $('#AddTypeModal').modal('hide');
                        $('#AddTypeModal input[name="name"]').val('');
                        $('#add-type-btn').html('Add').removeAttr('disabled','disabled');
                    },error: function (data) {
                        let errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
            // getMasterProject(val);
        });

        getMasterProject('{{ ($Property) ? $Property->emirate_id : '2' }}');
        function getMasterProject(val){
            $.ajax({
                url:"{{ route( 'master-project.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Emirate:val
                },
                success:function (response) {
                    $('#MasterProject').html(response);
                    $('.master-propject-add').html(response);
                    $('#MasterProject').val('{{ ($Property) ? $Property->master_project_id : $master_project_id }}');
                    $('.master-propject-add').val('{{ ($Property) ? $Property->master_project_id : $master_project_id}}').change();
                }
            });
        }

        $('#MasterProject').change(function () {
            let val=$(this).val();
            $('.master-propject-add').val(val).change();
            getCommunity(val);
            {{ ($Property) ? '' : 'duplicatePropertyCheck();' }}
        });
        getCommunity('{{ ($Property) ? $Property->master_project_id : $master_project_id }}');
        function getCommunity(val){
            $.ajax({
                url:"{{ route( (Auth::guard('contact-admin')->check()) ? 'company-community.get.ajax' : 'community.get.ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    MasterProject:val
                },
                success:function (response) {
                    $('#Community').html(response);
                    $('.community-add').html(response);
                    // $('#ClusterStreet').html('');
                    // $('#VillaType').html('');
                    // $('#VillaNumber').html('');
                    $('#Community').val('{{ ($Property) ? $Property->community_id : $project_id }}');
                    $('.community-add').val('{{ ($Property) ? $Property->community_id : $project_id }}').change();
                    if($('#Community').val()=='' )
                        $('#Community').change();
                }
            });
        }
        $('#Community').change(function () {
            let val=$(this).val();
            $('.community-add').val(val).change();
            getClusterStreet(val);
            getType(val);
            {{ ($Property) ? '' : 'duplicatePropertyCheck();' }}
            //getVillaNumber(val);
        });
        getClusterStreet('{{ ($Property) ? $Property->community_id : '' }}');
        function getClusterStreet(val){
            $.ajax({
                url:"{{ route( 'cluster-street.get.ajax' ) }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Community:val
                },
                success:function (response) {
                    $('#ClusterStreet').html(response);
                    $('#ClusterStreet').val('{{ ($Property) ? $Property->cluster_street_id : '' }}');
                }
            });
        }
        getType('{{ ($Property) ? $Property->community_id : '' }}');
        function getType(val){
            $.ajax({
                url:"{{ route( 'type.get.ajax' ) }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    Community:val
                },
                success:function (response) {
                    $('#VillaType').html(response);
                    $('#VillaType').val('{{ ($Property) ? $Property->villa_type_id : '' }}');
                }
            });
        }

        let rera_number='';
        let for_portal='';

        function getCMinfo(admin){
            $.ajax({
                url:"{{ route( 'admin.info' ) }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    'admin':admin
                },
                success:function (response) {
                    rera_number=response.rera_brn;
                    for_portal=response.supervisor_id;

                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        getCMinfo("{{ ($Property) ? $Property->client_manager_id : '' }}");

        $('#ClientManager1').change(function () {
            getCMinfo($(this).val());
        });

        $('#submit').click(function () {
            let count = $('.property-owner-list li').length;

            let error=0;
            let ListingType=$("input[name='ListingType']:checked").val();
            let OffPlan=$('#OffPlan').val();
            let Status2=$('#Status2').val();
            let PropertyType=$('#PropertyType').val();
            let PlotSQFT=$('#PlotSQFT').val();

            let Status=$('#Status').val();

            /*if(rera_number=='' && for_portal=='' && Status==1){
                toast_('','The agent does not have the RERA BRN.',$timeOut=20000,$closeButton=true);
                error=1;
            }*/

            if ( (OffPlan!='off_plan' && OffPlan!='off_plan_primary') && Status2=='' && (PropertyType!=19 && PropertyType!=29)) {
                $("#Status2").parent().addClass('error');
                error=1;
            }else{
                $("#Status2").parent().removeClass('error');
            }

            if ( OffPlan=='off_plan' || OffPlan=='off_plan_primary') {
                let OffPlanDetailsSaleType=$("#OffPlanDetailsSaleType").val()
                if($("#OffPlanDetailsSaleType").val()==''){
                    $("#OffPlanDetailsSaleType").parent().addClass('error');
                    error=1
                }else{
                    $("#OffPlanDetailsSaleType").parent().removeClass('error');
                }
                if($("#OffPlanDetailsOriginalPrice").val()==''){
                    $("#OffPlanDetailsOriginalPrice").parent().addClass('error');
                    error=1
                }else{
                    $("#OffPlanDetailsOriginalPrice").parent().removeClass('error');
                }

                if(OffPlanDetailsSaleType==1){
                    if($("#OffPlanDetailsDldWaiver").val()==''){
                        $("#OffPlanDetailsDldWaiver").parent().addClass('error');
                        error=1
                    }else{
                        $("#OffPlanDetailsDldWaiver").parent().removeClass('error');
                    }
                }

                if(OffPlanDetailsSaleType==2){
                    if($("#OffPlanDetailsAmountPaid").val()==''){
                        $("#OffPlanDetailsAmountPaid").parent().addClass('error');
                        error=1
                    }else{
                        $("#OffPlanDetailsAmountPaid").parent().removeClass('error');
                    }
                }

                if($("#OffPlanDetailsDldWaiver").val()>100){
                    $("#OffPlanDetailsDldWaiver").parent().addClass('error');
                    error=1
                }
            }

            if (Status2 == '2') {
                if($("#RentPrice").val()==''){
                    $("#RentPrice").parent().addClass('error');
                    error=1
                }else{
                    $("#RentPrice").parent().removeClass('error');
                }
                if($("#RentedFrom").val()==''){
                    $("#RentedFrom").parent().addClass('error');
                    error=1
                }else{
                    $("#RentedFrom").parent().removeClass('error');
                }
                if($("#RentedFor").val()==''){
                    $("#RentedFor").parent().addClass('error');
                    error=1
                }else{
                    $("#RentedFor").parent().removeClass('error');
                }
                if($("#RentedUntil").val()==''){
                    $("#RentedUntil").parent().addClass('error');
                    error=1
                }else{
                    $("#RentedUntil").parent().removeClass('error');
                }
                if($("#VacatingNotice").val()==''){
                    $("#VacatingNotice").parent().addClass('error');
                    error=1
                }else{
                    $("#VacatingNotice").parent().removeClass('error');
                }
                /*if($("#AvailableFrom").val()==''){
                    $("#AvailableFrom").parent().addClass('error');
                    error=1
                }else{
                    $("#AvailableFrom").parent().removeClass('error');
                }*/
                if($("#NumberCheques").val()==''){
                    $("#NumberCheques").parent().addClass('error');
                    error=1
                }else{
                    $("#NumberCheques").parent().removeClass('error');
                }
            }

            if ( PropertyType=='' || PropertyType==null ) {
                $("#PropertyType").parent().addClass('error');
                error=1;
            }else{
                $("#PropertyType").parent().removeClass('error');
            }

            if ((PropertyType == '2' || PropertyType == '5' || PropertyType == '16' || PropertyType == '20' || PropertyType == '19' || PropertyType == '29') && PlotSQFT=='') {
                $("#PlotSQFT").parent().addClass('error');
                error=1;
            }else{
                $("#PlotSQFT").parent().removeClass('error');
            }

            let ClusterStreet=$("#ClusterStreet").val();
            if ((PropertyType == '2' || PropertyType == '5') && ( ClusterStreet=='' || ClusterStreet==null) ) {
                $("#ClusterStreet").parent().addClass('error');
                error=1;
            }else{
                $("#ClusterStreet").parent().removeClass('error');
            }

            let MasterProject=$("#MasterProject").val();
            if ( MasterProject=='' || MasterProject==null ) {
                $("#MasterProject").parent().addClass('error');
                error=1;
            }else{
                $("#MasterProject").parent().removeClass('error');
            }

            let Community=$("#Community").val();
            if ( Community=='' || Community==null ) {
                $("#Community").parent().addClass('error');
                error=1;
            }else{
                $("#Community").parent().removeClass('error');
            }

            if(!property_type_array.includes(PropertyType) &&
                PropertyType != '26' && PropertyType != '27' && PropertyType != '28' && PropertyType != '29' && PropertyType != '30') {
                if($("#Bathrooms").val()==''){
                    $("#Bathrooms").parent().addClass('error');
                    error=1
                }else{
                    $("#Bathrooms").parent().removeClass('error');
                }
            }

            if(!property_type_array.includes(PropertyType) && $('#Type').val()==1){
                if($("#Bedrooms").val()==''){
                    $("#Bedrooms").parent().addClass('error');
                    error=1
                }else{
                    $("#Bedrooms").parent().removeClass('error');
                }
            }

            if(PropertyType != '19' && PropertyType != '29'){
                if($("#Parking").val()==''){
                    $("#Parking").parent().addClass('error');
                    error=1
                }else{
                    $("#Parking").parent().removeClass('error');
                }

                if($("#Furnished").val()==''){
                    $("#Furnished").parent().addClass('error');
                    error=1
                }else{
                    $("#Furnished").parent().removeClass('error');
                }

                if($("#Status2").val()==''){
                    $("#Status2").parent().addClass('error');
                    error=1
                }else{
                    $("#Status2").parent().removeClass('error');
                }
            }

            if($('#Type').val()=='2' && ListingType==2){
                if($("#YearlyPrice").val()==''){
                    $("#YearlyPrice").parent().addClass('error');
                    error=1
                }else{
                    $("#YearlyPrice").parent().removeClass('error');
                }
            }

            if($('#Type').val()=='1'){

                if($('#PropertyType').val()!='19' &&
                    $('#PropertyType').val()!='12' &&
                    $('#PropertyType').val()!='13' &&
                    $('#PropertyType').val()!='14' &&
                    $('#PropertyType').val()!='15') {

                    if ($("#Maid").val() == '') {
                        $("#Maid").parent().addClass('error');
                        error = 1
                    } else {
                        $("#Maid").parent().removeClass('error');
                    }

                    if ($("#Driver").val() == '') {
                        $("#Driver").parent().addClass('error');
                        error = 1
                    } else {
                        $("#Driver").parent().removeClass('error');
                    }

                    if ($("#Study").val() == '') {
                        $("#Study").parent().addClass('error');
                        error = 1
                    } else {
                        $("#Study").parent().removeClass('error');
                    }

                    if ($("#Storage").val() == '') {
                        $("#Storage").parent().addClass('error');
                        error = 1
                    } else {
                        $("#Storage").parent().removeClass('error');
                    }
                }
            }

            if($("input[name='ListingType']:checked").val()==1){
                if($("#ExpectedPrice").val()==''){
                    $("#ExpectedPrice").parent().addClass('error');
                    error=1
                }else{
                    $("#ExpectedPrice").parent().removeClass('error');
                }
            }else{
                if ( $("#DailyPrice").val()=='' && $("#WeeklyPrice").val()=='' && $("#MonthlyPrice").val()=='' && $("#YearlyPrice").val()=='' ){
                    error=1
                    toast_('','One of the prices must be filled.',$timeOut=20000,$closeButton=true);
                }
            }

            if( $('#Status').val()=='1' && $('#Emirate').val()=='2' && $('#land_department_qr_check').val()=='') {
                if ($('#land_department_qr')[0].files.length === 0) {
                    $(".land_department_qr_box").css("border", "1px solid #EA5455");
                    error = 1;
                }
            } else {
                $(".land_department_qr_box").css("border", "1px solid #d9d9d9");
            }

            if( $('#Status').val()=='1'){
                if($('#ReraPermit').val()=='' && $('#DTCMNumber').val()==''){
                    toast_('','Property must have either DLD permit number or DTCM number.',$timeOut=20000,$closeButton=true);
                    error = 1;
                }
            }

            if(error==0){
                if (count > 0){
                    $('button[name="submit"]').click();
                }else{
                    toast_('','Contact selection is required.',$timeOut=20000,$closeButton=true);
                }
            }else{
                toast_('','Please fill up all required fields.',$timeOut=20000,$closeButton=true);
            }

        });

        $('#PropertyType').change(function(){
            let value=$('#PropertyType').val();
            let type=$('#Type').val();

            $("#ClusterStreet").parent().removeClass('error').children('label').children('span').remove();
            $("#PlotSQFT").parent().removeClass('error').children('label').children('span').remove();
            $("#PlotSQFT").parent().parent().addClass('d-none');//attr('disabled','disabled');
            $("#ForPortals").val('2').attr('disabled','disabled');


            $('#Bathrooms , #Parking ,#Furnished , #Status2').parent().parent().removeClass('d-none');
            if(type==1) {
                $('#Bedrooms , #Maid , #Driver , #Study , #Storage').parent().parent().removeClass('d-none');
            }

            if(type==2) {
                if(value == '26' || value == '27' || value == '28' || value == '30')
                    $('#Bathrooms').parent().parent().addClass('d-none');
            }

            if (value == '2' || value == '5') {
                $("#ClusterStreet").parent().children('label').append('<span>*</span>');
            }

            if (value == '2' || value == '5' || value == '16' || value == '20' || value == '19' || value == '29') {
                $("#PlotSQFT").parent().parent().removeClass('d-none');//removeAttr('disabled');
                $("#PlotSQFT").parent().children('label').append('<span>*</span>');
                $("#ForPortals").val('{{ ($Property) ? $Property->size_for_portals : '2' }}').removeAttr('disabled','disabled');
            }

            if (type == '2') {
                $("#Bedrooms").parent().removeClass('error').children('label').children('span').remove();
                $("#Bedrooms").val('').parent().parent().addClass('d-none');
            }else{
                $("#Bedrooms").parent().removeClass('error').children('label').children('span').remove();
                $("#Bedrooms").val('').parent().parent().addClass('d-none');

                if(!property_type_array.includes(value)){
                    $("#Bedrooms").parent().children('label').append('<span>*</span>');
                    $("#Bedrooms").val('{{ ($Property) ? $Property->bedroom_id : '' }}').parent().parent().removeClass('d-none');
                }
            }

            if(value == '19' || value == '29'){
                $('#Bedrooms , #Bathrooms , #Maid , #Driver , #Study , #Storage , #Parking ,#Furnished , #Status2').parent().parent().addClass('d-none');
                $('#Status2').change();
            }

            if(value == '12' || value == '13' || value == '14' || value == '15' ){
                $('#Bedrooms , #Bathrooms , #Maid , #Driver , #Study , #Storage').parent().parent().addClass('d-none');
            }
        });

        $('#Type').change(function(){
            let value=$(this).val();
            $("#Bedrooms , #Maid , #Driver , #Study , #Storage , #YearlyPrice").parent().removeClass('error').children('label').children('span').remove();
            $("#Bedrooms , #Maid , #Driver , #Study , #Storage ").parent().children('label').append('<span>*</span>');
            $("#Bedrooms , #Maid , #Driver , #Study , #Storage ").val('').parent().parent().removeClass('d-none');//removeAttr('disabled');//.val('');
            if (value == '2') {
                $("#Bedrooms , #Maid , #Driver , #Study , #Storage").parent().removeClass('error').children('label').children('span').remove();
                $("#Bedrooms , #Maid , #Driver , #Study , #Storage").val('').parent().parent().addClass('d-none');
                let ListingType=$("input[name='ListingType']:checked").val();
;                if(ListingType==2){
                    $("#YearlyPrice").parent().children('label').append('<span>*</span>');
                    $("#DailyPrice , #WeeklyPrice , #MonthlyPrice").val('').parent().parent().addClass('d-none');
                }
            }
            getPropertyType();
        });

        getPropertyType();
        function getPropertyType(){
            let type=$('#Type').val();
            $.ajax({
                url:"{{ route('property-type.ajax.get') }}",
                type:"POST",
                data:{
                    _token:$('#AddContactModal form input[name="_token"]').val(),
                    type:type
                },
                success:function (response) {
                    $('#PropertyType').html(response);
                    $('#PropertyType').val('{{ ($Property) ? $Property->property_type_id : '' }}').change();
                }
            });
        }

        $('#VillaNumber').keyup(function(){
            let val=$(this).val();
            if (val==0){
                $(this).val('');
                val='';
            }
            val=val.toUpperCase();
            val = val.replace(/\s/g, '');
            $(this).val(val);
        });

        $('#add-contact-btn').click(function(){
            $('#add-contact-form').result();
            $('#ContactCategory').change();
        });


    </script>

    <script>

        var UploderType='multi';
        $('#AttachFile').change(function(){
            $('#AttachFileBtn').removeAttr("disabled");
        });

        $('#AttachFileBtn').click(function(){
            $('#AttachFileBtn').attr("disabled","disabled");
            UploderType=$(this).data('type');
            var Action=$(this).data('action');
            var token=$(this).data('token');
            AttachFile(Action,token);
        });

        function AttachFile(Action,token) {
            var file = _("AttachFile").files[0];
            var form_data = new FormData();

            // Read selected files
            var totalfiles = document.getElementById('AttachFile').files.length;
            for (var index = 0; index < totalfiles; index++) {
                // form_data.append("files[]", document.getElementById('AttachFile').files[index]);


                //  alert(file.name+" | "+file.size+" | "+file.type);
                var formdata = new FormData();
                formdata.append("AttachFileSubmit", "0");
                formdata.append("_token", token);
                formdata.append("FileFile", document.getElementById('AttachFile').files[index]);
                var ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", progressPHandler, false);
                ajax.addEventListener("load", completePHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", Action);
                ajax.send(formdata);
            }
        }
        function _(el) {
            return document.getElementById(el);
        }

        function progressPHandler(event) {
            $('#PresentProgressBar').parent().removeClass("d-none");
            $('#AttachFileBtn').attr('disabled','disabled');
            var percent = (event.loaded / event.total) * 100;
            $("#PresentProgressBar").css({"width": Math.round(percent) + "%"});
            $("#PresentProgressBar").html('Uploading');//html(Math.round(percent) + "%");
        }

        function completePHandler(event) {
            var response = jQuery.parseJSON( event.target.responseText );
            // alert( event.target.responseText )
            if(response.result=='true'){
                $('.AttachFileBox').append(`<div class="border mx-auto mb-1 property-iamge-box">
                                        <a href="${response.link}" class="property-image">
                                            <img src="${response.link}" height="100px" width="100px">
                                        </a>
                                    </div>

                                    `);

                let name=response.name;
                name=name.split(".")[0];
                $('.showAllAttachFileBox').append(`<div class="col-sm-3 mb-1">
                                        <div class="border">
                                        <div class=" mx-auto property-iamge-box">
                                            <a href="${response.link}" class="property-image d-block w-100">
                                                <img src="${response.link}">
                                            </a>
                                            <input type="hidden" value="${response.name}" name="InputAttachFile[]">
                                        </div>
                                        <div class="action clearfix" data-name="${response.name}">
                                            <a title="remove" href="javascript:void(0)" class="file-delete d-blok w-100"><i class="feather icon-trash-2"></i> <small>Delete</small></a>

                                            <div class="custom-control custom-switch d-flex align-items-center">
                                                <input type="checkbox" class="custom-control-input" vlaue="1" checked name="${name}" id="${name}">
                                                <label class="custom-control-label" for="${name}"></label>
                                                <span class="switch-label"><small>Watermark</small></span>
                                            </div>
                                        </div>
                                        </div>
                                    </div>`);
                showImage();
                if(UploderType!='multi'){
                    $("#PresentProgressBar").html('<p class="m-0">upload successful');
                    $('#AttachFileBtn').addClass('d-none');
                }else{
                    $('#PresentProgressBar').parent().addClass("d-none");
                    $('#AttachFileBtn').attr('disabled','disabled');
                    $("#PresentProgressBar").css({"width": 0 + "%"});
                    $("#PresentProgressBar").html('');
                    $("#AttachFile").val('').parent().children('label').html('');
                }
                $('#AttachFileBtn').removeAttr('disabled','disabled');
            }else{
                alert(response.message);
            }
        }

        function errorHandler(event) {
            //_("status").innerHTML = "Upload Failed";
        }

        function abortHandler(event) {
            // _("status").innerHTML = "Upload Aborted";
        }
        //==========================================================================================================\\

        $('body').on('click','.file-delete',function(){
            var file=$(this).parent().data('name');
            var e=$(this).parent().parent();

            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url:"{{ route('delete-image') }}",
                        type:"POST",
                        data:{
                            _token:'{{ csrf_token() }}',
                            FileDelete:file
                        },
                        success:function (response) {
                            editImages(file);
                            e.remove();
                        }
                    });
                }
            });
        });

        $('body').on('click','.delete-all-img',function(){
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $( ".showAllAttachFileBox .file-delete" ).each(function() {
                        var file=$(this).parent().data('name');
                        var e=$(this).parent().parent();
                        $.ajax({
                            url:"{{ route('delete-image') }}",
                            type:"POST",
                            data:{
                                _token:'{{ csrf_token() }}',
                                FileDelete:file
                            },
                            success:function (response) {
                                editImages(file);
                                e.remove();
                            }
                        });
                    });
                }
            })

        });

        function editImages(image_name){
            $.ajax({
                url:"{{ route('property.edit.image') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    id:"{{ ($Property) ? $Property->id : '' }}",
                    image_name:image_name
                }
            });
        }

        $('#Title').keyup(function () {
            var value = $(this).val();
            $(this).parent().children('small').children('.char-count').html(value.length);
            if ( value.length > 50)
                $(this).addClass('is-invalid');
            else
                $(this).removeClass('is-invalid');
        });

        function showImage(){
            $('.property-image').magnificPopup({
                type: 'image',
                removalDelay: 300,
                mainClass: 'mfp-fade',
                gallery: {
                    enabled: true
                },
                zoom: {
                    enabled: true,
                    duration: 300,
                    easing: 'ease-in-out',
                    opener: function (openerElement) {
                        return openerElement.is('img') ? openerElement : openerElement.find('img');
                    }
                }
            });
        }

        showImage();


    </script>

    <script> //document upload
        let ProgressBar='';
        let InputAttachDocument='';
        let _this=''

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
                var ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", documentProgressHandler, false);
                ajax.addEventListener("load", documentCompleteHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", Action);
                ajax.send(formdata);
            }else{
                Swal.fire({
                    title: "The format is not supported.",
                    text: "Supported files (pdf, doc, docx, xlsx, xml, xls, jpg, jpeg, webp, png)",
                    type: "warning",
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText:'Ok',
                    buttonsStyling: false,
                });
            }

        }

        // function _(el) {
        //     return document.getElementById(el);
        // }

        function documentProgressHandler(event) {
            $(ProgressBar).parent().removeClass("d-none");
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

        // function errorHandler(event) {
        //     //_("status").innerHTML = "Upload Failed";
        // }
        //
        // function abortHandler(event) {
        //     // _("status").innerHTML = "Upload Aborted";
        // }

    </script>

    <script>
        var fixHelperModified = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },

            updateIndex = function (e, ui) {

            };

        $(".sort-element").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

        $(".sort-element").sortable({
            distance: 5,
            delay: 100,
            opacity: 0.6,
            cursor: 'move',
            update: function () {
            }
        });
    </script>
    <script>
        $("#land_department_qr").change(function () {
            ImagePreview(this,550000,['gif','png','jpg','jpeg'],'#land_department_qr_Preview');
        });

        $(".land_department_qr_box").click(function () {
            $(".land_department_qr_box").css("border", "1px solid #d9d9d9");
        });

    </script>

    <script>
        PFLocation();
        function PFLocation(SelectType=false) {
            // Loading remote data
            $("#PFLocation").select2({
                dropdownAutoWidth: true,
                minimumInputLength: 2,
                width: '100%',
                multiple:SelectType,
                ajax: {
                    url: "{{ route('pf-location.get.ajax') }}",
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

                        return { results: data };
                    },
                    cache: true
                },
                placeholder: 'Location...',
                minimumResultsForSearch: Infinity,
                templateResult: pf_formatRepo,
                templateSelection: pf_formatRepoSelection,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function pf_formatRepo (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="w-100 ml-1">
                                <div>${repo.address}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function pf_formatRepoSelection (repo) {
            return repo.address || repo.text ;

        }
    </script>

@endsection
