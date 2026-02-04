
@extends('layouts/contentLayoutMaster')

@section('title', 'Control Panel')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="/css/jquery.minicolors.css">
@endsection

@php
    $adminAuth=\Auth::guard('admin')->user();
@endphp

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Main Access</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('main-access-setting') }}" class="form error" novalidate>
                            @csrf
                            @php
                                $main_access_setting_admin_id=\App\Models\Admin::whereNull('main_super')->where('super',1)->where('company_id',$adminAuth->company_id)->pluck('id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="main_access_user">Included</label>
                                            <select class="form-control select2-checkbox" id="main_access_user" name="main_access_user[]">
                                                @php
                                                    $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->where('type','1')->whereNull('main_super')->orderBy('firstname','ASC')->get();
                                                @endphp
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Survey Report Access</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('survey-access-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $survey_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','survey_access')->first();
                                $survey_access_setting_admin_id=[];
                                if($survey_access_setting)
                                    $survey_access_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$survey_access_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <fieldset class="form-group form-label-group">
                                            <label for="survey_access_user">Included</label>
                                            <select class="form-control select2-checkbox" id="survey_access_user" name="survey_access_user[]">
                                                @php
                                                    $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->whereNotIn('type', [7,8])->whereNull('super')->orderBy('firstname','ASC')->get();
                                                @endphp
                                                @foreach($ClientManagers as $ClientManager)
                                                    <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Last Activity</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-contact-activity-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $activity_contact_setting_1=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_1')->first();
                                $activity_contact_setting_2=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_2')->first();
                                $activity_contact_setting_3=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','contact_activity_3')->first();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="first_warning_time" name="first_warning_time" class="form-control" placeholder="Beginning" value="{{($activity_contact_setting_1) ? $activity_contact_setting_1->time : ''}}" disabled>
                                                    <label for="first_warning_time">Beginning</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pl-0">
                                                <img src="/images/imoji-green.png" style="width: 33px;height: 33px">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="second_warning_time" name="second_warning_time" class="form-control" placeholder="First Change" value="{{($activity_contact_setting_2) ? $activity_contact_setting_2->time : ''}}">
                                                    <label for="second_warning_time">First Change (after ... days)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pl-0">
                                                <img src="/images/imoji-yellow.png" style="width: 33px;height: 33px">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="third_warning_time" name="third_warning_time" class="form-control" placeholder="Second Change" value="{{($activity_contact_setting_3) ? $activity_contact_setting_3->time : ''}}">
                                                    <label for="third_warning_time">Second Change (after ... days)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pl-0">
                                                <img src="/images/imoji-red.png" style="width: 33px;height: 33px">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Properties Expiration Date</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-expiration-property-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $expiration_property_setting_1=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_1')->first();
                                $expiration_property_setting_2=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_2')->first();
                                $expiration_property_setting_3=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_property_3')->first();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="first_warning_time" name="first_warning_time" class="form-control" placeholder="First warning" value="{{($expiration_property_setting_1) ? $expiration_property_setting_1->time : ''}}">
                                                    <label for="first_warning_time">First Reminder (days in advance)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-success" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="second_warning_time" name="second_warning_time" class="form-control" placeholder="Second warning" value="{{($expiration_property_setting_2) ? $expiration_property_setting_2->time : ''}}">
                                                    <label for="second_warning_time">Second Reminder (days in advance))</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-warning" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="third_warning_time" name="third_warning_time" class="form-control" placeholder="Third  warning" value="{{($expiration_property_setting_3) ? $expiration_property_setting_3->time : ''}}">
                                                    <label for="third_warning_time">Third  Reminder (days in advance)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-danger" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Users Expiration Date</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-expiration-user-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $expiration_user_setting_1=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_1')->first();
                                $expiration_user_setting_2=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_2')->first();
                                $expiration_user_setting_3=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','expiration_user_3')->first();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="first_warning_time" name="first_warning_time" class="form-control" placeholder="First warning" value="{{($expiration_user_setting_1) ? $expiration_user_setting_1->time : ''}}">
                                                    <label for="first_warning_time">First Reminder (days in advance)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-success" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="second_warning_time" name="second_warning_time" class="form-control" placeholder="Second warning" value="{{($expiration_user_setting_2) ? $expiration_user_setting_2->time : ''}}">
                                                    <label for="second_warning_time">Second Reminder (days in advance)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-warning" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <div class="form-label-group">
                                                    <input type="text" id="third_warning_time" name="third_warning_time" class="form-control" placeholder="Third  warning" value="{{($expiration_user_setting_3) ? $expiration_user_setting_3->time : ''}}">
                                                    <label for="third_warning_time">Third  Reminder (days in advance)</label>
                                                </div>
                                            </div>
                                            <div class="col-2 pt-1">
                                                <span class="badge badge-pill badge-danger" style="width: 15px;height: 15px"> </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Calendar</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('calendar-color-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $calendar_viewing_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_viewing')->first();
                                $calendar_appointment_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_appointment')->first();
                                $calendar_reminder_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_reminder')->first();
                                $calendar_cancelled_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','calendar_cancelled')->first();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-label-group form-group">
                                            <label for="viewing">Viewing</label>
                                            <input type="text" id="viewing" name="viewing" class="form-control demo" value="{{($calendar_viewing_setting) ? $calendar_viewing_setting->value : ''}}" data-control="hue">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-label-group form-group">
                                            <label for="cancelled">Cancelled</label>
                                            <input type="text" id="cancelled" name="cancelled" class="form-control demo" value="{{($calendar_cancelled_setting) ? $calendar_cancelled_setting->value : ''}}" data-control="hue">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-label-group form-group">
                                            <label for="viewing">Appointment</label>
                                            <input type="text" id="viewing" name="appointment" class="form-control demo" value="{{($calendar_appointment_setting) ? $calendar_appointment_setting->value : ''}}" data-control="hue">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-label-group form-group">
                                            <label for="viewing">Reminder</label>
                                            <input type="text" id="appointment" name="reminder" class="form-control demo" value="{{($calendar_reminder_setting) ? $calendar_reminder_setting->value : ''}}" data-control="hue">
                                        </div>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-buyer-tenant-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $buyer_tenant_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','open_buyer_tenant')->first();
                                $buyer_tenant_setting_admin_id=[];
                                if($buyer_tenant_setting)
                                    $buyer_tenant_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$buyer_tenant_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-inline-block mb-2">
                                            <div class="custom-control custom-switch custom-switch-primary">
                                                <input type="checkbox" class="custom-control-input switch-a-d" data-input="buyer_tenant" id="buyer_tenant_switch" name="buyer_tenant_switch" {{($buyer_tenant_setting && $buyer_tenant_setting->status==1) ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="buyer_tenant_switch"></label>
                                                <span class="switch-label"><b>Buyer/Tenant</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <label for="buyer_tenant_time_type">Time Type</label>
                                                    <select class="form-control" id="buyer_tenant_time_type" name="buyer_tenant_time_type">
                                                        <option value="1">Minutes</option>
                                                        <option value="2">Hours</option>
                                                        <option value="3">Days</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <input type="text" id="buyer_tenant_time" name="buyer_tenant_time" class="form-control" placeholder="Counts" value="{{($buyer_tenant_setting) ? $buyer_tenant_setting->time : ''}}">
                                                    <label for="buyer_tenant_time">Counts</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="buyer_tenant_user">Not Included</label>
                                                    <select class="form-control select2-checkbox" id="buyer_tenant_user" name="buyer_tenant_user[]">
                                                        @php
                                                            $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->orderBy('firstname','ASC')->get();
                                                        @endphp
                                                        @foreach($ClientManagers as $ClientManager)
                                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-2 p-0">
                                                <input type="checkbox" class="checkbox" data-target="#buyer_tenant_user"> All
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-lead-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $lead_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','open_lead')->first();
                                $lead_setting_admin_id=[];
                                if($lead_setting)
                                    $lead_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$lead_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-inline-block mb-2">
                                            <div class="custom-control custom-switch custom-switch-primary">
                                                <input type="checkbox" class="custom-control-input switch-a-d" data-input="lead" id="lead_switch" name="lead_switch" {{($lead_setting && $lead_setting->status==1) ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="lead_switch"></label>
                                                <span class="switch-label"><b>Leads</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <label for="lead_time_type">Time Type</label>
                                                    <select class="form-control" id="lead_time_type" name="lead_time_type">
                                                        <option value="1">Minutes</option>
                                                        <option value="2">Hours</option>
                                                        <option value="3">Days</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <input type="text" id="lead_time" name="lead_time" class="form-control" placeholder="Counts" value="{{($lead_setting) ? $lead_setting->time : ''}}">
                                                    <label for="lead_time">Counts</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="lead_user">Not Included</label>
                                                    <select class="form-control select2-checkbox" id="lead_user" name="lead_user[]">
                                                        @php
                                                            $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->orderBy('firstname','ASC')->get();
                                                        @endphp
                                                        @foreach($ClientManagers as $ClientManager)
                                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-2 p-0">
                                                <input type="checkbox" class="checkbox" data-target="#lead_user"> All
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('open-ma-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $ma_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','open_ma')->first();
                                $ma_setting_admin_id=[];
                                if($ma_setting)
                                    $ma_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$ma_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-inline-block mb-2">
                                            <div class="custom-control custom-switch custom-switch-primary">
                                                <input type="checkbox" class="custom-control-input switch-a-d" data-input="ma" id="ma_switch" name="ma_switch" {{($ma_setting && $ma_setting->status==1) ? 'checked' : ''}}>
                                                <label class="custom-control-label" for="ma_switch"></label>
                                                <span class="switch-label"><b>{{Status[4]}}/Pocket/{{Status[3]}}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <label for="ma_time_type">Time Type</label>
                                                    <select class="form-control" id="ma_time_type" name="ma_time_type">
                                                        <option value="1">Minutes</option>
                                                        <option value="2">Hours</option>
                                                        <option value="3">Days</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-label-group">
                                                    <input type="text" id="ma_time" name="ma_time" class="form-control" placeholder="Counts" value="{{($ma_setting) ? $ma_setting->time : ''}}">
                                                    <label for="ma_time">Counts</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="ma_user">Not Included</label>
                                                    <select class="form-control select2-checkbox" id="ma_user" name="ma_user[]">
                                                        @php
                                                            $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->orderBy('firstname','ASC')->get();
                                                        @endphp
                                                        @foreach($ClientManagers as $ClientManager)
                                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-2 p-0">
                                                <input type="checkbox" class="checkbox" data-target="#ma_user"> All
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Brochure</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('brochure-bg-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $brochure_bg_sale_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','brochure_sale_bg')->first();
                                $brochure_bg_rent_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','brochure_rent_bg')->first();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">Sale</div>
                                            <div class="col-6">Rent</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_sale" class="custom-control-input" value="bg-brochure-sale.jpg" id="sale_1" {{($brochure_bg_sale_setting && $brochure_bg_sale_setting->value=='bg-brochure-sale.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="sale_1"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-sale.jpg"> </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_rent" class="custom-control-input" value="bg-brochure-rent.jpg" id="rent_1" {{($brochure_bg_rent_setting && $brochure_bg_rent_setting->value=='bg-brochure-rent.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="rent_1"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-rent.jpg"> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_sale" class="custom-control-input" value="bg-brochure-sale-2.jpg" id="sale_2" {{($brochure_bg_sale_setting && $brochure_bg_sale_setting->value=='bg-brochure-sale-2.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="sale_2"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-sale-2.jpg"> </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_rent" class="custom-control-input" value="bg-brochure-rent-2.jpg" id="rent_2" {{($brochure_bg_rent_setting && $brochure_bg_rent_setting->value=='bg-brochure-rent-2.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="rent_2"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-rent-2.jpg"> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_sale" class="custom-control-input" value="bg-brochure-sale-3.jpg" id="sale_3" {{($brochure_bg_sale_setting && $brochure_bg_sale_setting->value=='bg-brochure-sale-3.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="sale_3"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-sale-3.jpg"> </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_rent" class="custom-control-input" value="bg-brochure-rent-3.jpg" id="rent_3" {{($brochure_bg_rent_setting && $brochure_bg_rent_setting->value=='bg-brochure-rent-3.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="rent_3"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-rent-3.jpg"> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_sale" class="custom-control-input" value="bg-brochure-sale-4.jpg" id="sale_4" {{($brochure_bg_sale_setting && $brochure_bg_sale_setting->value=='bg-brochure-sale-4.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="sale_4"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-sale-4.jpg"> </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_rent" class="custom-control-input" value="bg-brochure-rent-4.jpg" id="rent_4" {{($brochure_bg_rent_setting && $brochure_bg_rent_setting->value=='bg-brochure-rent-4.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="rent_4"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-rent-4.jpg"> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_sale" class="custom-control-input" value="bg-brochure-sale-5.jpg" id="sale_5" {{($brochure_bg_sale_setting && $brochure_bg_sale_setting->value=='bg-brochure-sale-5.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="sale_5"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-sale-5.jpg"> </label>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="custom-control custom-radio" data-v-aa799a9e="">
                                                    <input type="radio" name="bg_rent" class="custom-control-input" value="bg-brochure-rent-5.jpg" id="rent_5" {{($brochure_bg_rent_setting && $brochure_bg_rent_setting->value=='bg-brochure-rent-5.jpg') ? 'checked' : ''}}>
                                                    <label class="custom-control-label" for="rent_5"> <img style="width: 100px;height: 30px" src="/images/bg-brochure-rent-5.jpg"> </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-1">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Import Contacts</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('upload-contact-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $upload_contact_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','upload_contact')->first();
                                $upload_contact_setting_admin_id=[];
                                if($upload_contact_setting)
                                    $upload_contact_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$upload_contact_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-10">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="lead_user">Included</label>
                                                    <select class="form-control select2-checkbox" id="upload_contact_user" name="upload_contact_user[]">
                                                        @php
                                                            $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->orderBy('firstname','ASC')->get();
                                                        @endphp
                                                        @foreach($ClientManagers as $ClientManager)
                                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                            <div class="col-2 p-0">
                                                <input type="checkbox" class="checkbox" data-target="#upload_contact_user"> All
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-12"  style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Task Management</h4>
                </div>
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form id="record-form" method="post" action="{{ route('task-access-setting') }}" class="form error" novalidate>
                            @csrf

                            @php
                                $task_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','all_task')->first();
                                $task_access_setting_admin_id=[];
                                if($task_access_setting)
                                    $task_access_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$task_access_setting->id)->pluck('admin_id')->toArray();
                            @endphp
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <fieldset class="form-group form-label-group">
                                                    <label for="lead_user">Included</label>
                                                    <select class="form-control select2-checkbox" id="task_access_user" name="task_access_user[]">
                                                        @php
                                                            $ClientManagers=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status','1')->orderBy('firstname','ASC')->get();
                                                        @endphp
                                                        @foreach($ClientManagers as $ClientManager)
                                                            <option value="{{ $ClientManager->id }}">{{ $ClientManager->firstname.' '.$ClientManager->lastname }}</option>
                                                        @endforeach
                                                    </select>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input type="checkbox" class="checkbox" name="status" value="1" {{ ($task_access_setting && $task_access_setting->status=='1') ? 'checked':'' }}> User Own Task
                                    </div>
                                    <div class="col-12" style="position: absolute;bottom: 20px;right: 10px">
                                        <button type="submit" class="btn btn-primary float-right" value="submit">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script src="/js/scripts/select2.multi-checkboxes.js"></script>
    <script src="/js/scripts/jquery.minicolors.js"></script>
    <script>
        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
        })
    </script>

    <script>
        $('.switch-a-d').change(function () {
            let input=$(this).data('input');

            if ($(this).prop('checked')==true){
                $('#' + input + '_time_type').removeAttr('disabled');
                $('#' + input + '_time').removeAttr('disabled');
                $('#' + input + '_user').removeAttr('disabled');
            }else {
                $('#' + input + '_time_type').attr('disabled', 'disabled');
                $('#' + input + '_time').attr('disabled', 'disabled');
                $('#' + input + '_user').attr('disabled', 'disabled');
            }
        });

        $(".checkbox").click(function(){
            let target=$(this).data('target')
            if($(".checkbox").is(':checked') ){
                $(target+" > option").prop("selected","selected");
                $(target).trigger("change");
            }else{
                $(target+" > option").prop("selected","")
                $(target).trigger("change");
            }
        });

        $(document).ready(function(){
            $('#ma_switch').change();
            $('#ma_time_type').val({{($ma_setting) ? $ma_setting->time_type : ''}});
            $('#ma_user').val([{{join(',',$ma_setting_admin_id)}}]).change();


            $('#lead_switch').change();
            $('#lead_time_type').val({{($lead_setting) ? $lead_setting->time_type : ''}});
            $('#lead_user').val([{{join(',',$lead_setting_admin_id)}}]).change();

            $('#upload_contact_user').val([{{join(',',$upload_contact_setting_admin_id)}}]).change();
            $('#task_access_user').val([{{join(',',$task_access_setting_admin_id)}}]).change();

            $('#buyer_tenant_switch').change();
            $('#buyer_tenant_time_type').val({{($buyer_tenant_setting) ? $buyer_tenant_setting->time_type : ''}});
            $('#buyer_tenant_user').val([{{join(',',$buyer_tenant_setting_admin_id)}}]).change();

            $('#main_access_user').val([{{join(',',$main_access_setting_admin_id)}}]).change();
            {{--$('#hr_access_user').val([{{join(',',$hr_access_setting_admin_id)}}]).change();--}}
            $('#survey_access_user').val([{{join(',',$survey_access_setting_admin_id)}}]).change();
            {{--$('#request_approver_user').val([{{join(',',$request_approver_setting_admin_id)}}]).change();
            $('#request_main_user').val([{{join(',',$request_main_setting_admin_id)}}]).change();--}}
        });
    </script>

    <script>
        $(document).ready( function() {

            $('.demo').each( function() {
                //
                // Dear reader, it's actually very easy to initialize MiniColors. For example:
                //
                //  $(selector).minicolors();
                //
                // The way I've done it below is just for the demo, so don't get confused
                // by it. Also, data- attributes aren't supported at this time...they're
                // only used for this demo.
                //
                $(this).minicolors({
                    control: $(this).attr('data-control') || 'hue',
                    defaultValue: $(this).attr('data-defaultValue') || '',
                    format: $(this).attr('data-format') || 'hex',
                    keywords: $(this).attr('data-keywords') || '',
                    inline: $(this).attr('data-inline') === 'true',
                    letterCase: $(this).attr('data-letterCase') || 'lowercase',
                    opacity: $(this).attr('data-opacity'),
                    position: $(this).attr('data-position') || 'bottom',
                    swatches: $(this).attr('data-swatches') ? $(this).attr('data-swatches').split('|') : [],
                    change: function(value, opacity) {
                        if( !value ) return;
                        if( opacity ) value += ', ' + opacity;
                        if( typeof console === 'object' ) {
                            console.log(value);
                        }
                    },
                    theme: 'bootstrap'
                });

            });

        });
    </script>
@endsection
