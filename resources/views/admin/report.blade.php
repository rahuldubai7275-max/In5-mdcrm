@extends('layouts/contentLayoutMaster')

@section('title', 'In a Glance')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">

<style>
    .picker {
        min-width: 250px;
    }
    /*.rented-until-box .picker , .available-from-box .picker , .expiration-date-box .picker {*/
    /*    right: 0;*/
    /*}*/

    .avatar {
        cursor: default !important;
    }
</style>
@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
@endsection

@php
$adminAuth = Auth::guard('admin')->user();
$sale_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 1)->count();
$rent_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 2)->count();

$residential_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('type', 1)->count();
$commercial_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('type', 2)->count();

$buyer_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'buyer')->count();
$tenant_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'tenant')->count();
$agent_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'agent')->count();
$owner_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'owner')->count();

$listedUnlisted_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 2)->count();

$listed_count=DB::select('SELECT Count(*) as countAll FROM property WHERE company_id='.$adminAuth->company_id.' AND status=1 '.$filterForListedUnlisted);

$unlisted_count=DB::select('SELECT Count(*) as countAll FROM property WHERE company_id='.$adminAuth->company_id.' AND status=2 '.$filterForListedUnlisted);

$ma_count=DB::select('SELECT Count(*) as countAll FROM property WHERE company_id='.$adminAuth->company_id.' AND status=4 '.$filter);

$call_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=1 '.$filter);

$call_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=1 '.$filter);

$call_dc_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=1 '.$filter);

$viewing_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=2 AND status=1 '.str_replace('created_at','date_at',$filter));

$viewing_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=2 AND status=1 '.str_replace('created_at','date_at',$filter));

$viewing_canceled_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=2 AND status=2 '.str_replace('created_at','date_at',$filter));

$viewing_canceled_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=2 AND status=2 '.str_replace('created_at','date_at',$filter));

$appointment_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=3 AND status=1 '.str_replace('created_at','date_at',$filter));

$appointment_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=3 AND status=1 '.str_replace('created_at','date_at',$filter));

$appointment_canceled_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=3 AND status=2 '.str_replace('created_at','date_at',$filter));

$appointment_canceled_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=3 AND status=2 '.str_replace('created_at','date_at',$filter));

$note_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=4 '.$filter);

$note_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=4 '.$filter);

$email_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=5 '.$filter);

$email_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=5 '.$filter);

$email_dc_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=5 '.$filter);

$reminder_property_count=DB::select('SELECT COUNT(*) as countAll FROM `property_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=6 '.$filter);

$reminder_contact_count=DB::select('SELECT COUNT(*) as countAll FROM `contact_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=6 '.$filter);

$reminder_dc_count=DB::select('SELECT COUNT(*) as countAll FROM `data_center_note` WHERE company_id='.$adminAuth->company_id.' AND `note_subject`=6 '.$filter);

$company_commission_sum=DB::select('SELECT SUM(company_commission) as sumAll FROM `deal` WHERE company_id='.$adminAuth->company_id.' AND deal.status=1  '.(($from_date) ? 'AND created_at >="'.$from_date.' 00:00:00"' : '') .(($to_date) ? ' AND created_at <="'.$to_date.' 23:59:59"': ''));

if($admins=='' && $adminAuth->type > 2 && $adminAuth->type == 5 && $adminAuth->type == 6){
    $commission_sum=DB::select('SELECT SUM(commission) as sumAll FROM `deal` WHERE company_id='.$adminAuth->company_id.' AND deal.status=1 '.$filter);;
}else{
    $commission_sum=DB::select('SELECT SUM(deal_agents.commission) as sumAll FROM deal,deal_agents WHERE deal.id=deal_agents.deal_id AND company_id='.$adminAuth->company_id.' AND deal.status=1 '.str_replace(['admin_id','created_at' ],['agent_id','deal_agents.created_at' ],$filter));
}
$deal_count=DB::select('SELECT Count(*) as countAll FROM (SELECT DISTINCT deal.id FROM deal,deal_agents WHERE deal.id=deal_agents.deal_id AND company_id='.$adminAuth->company_id.' AND deal.status=1 '.str_replace(['admin_id','created_at' ],['agent_id','deal_agents.created_at' ],$filter).') AS count_deal');

$ma_count=DB::select('SELECT Count(*) as countAll FROM property WHERE company_id='.$adminAuth->company_id.' AND status=4 '.$filter);
$total_properties_count=DB::select('SELECT Count(*) as countAll FROM property WHERE  company_id='.$adminAuth->company_id.' '.$filter);
$total_contacts_count=DB::select('SELECT Count(*) as countAll FROM contacts WHERE company_id='.$adminAuth->company_id.' AND lead_id IS NULL '.$filter);

$closed_lead_count=DB::select('SELECT Count(*) as countAll FROM leads WHERE company_id='.$adminAuth->company_id.' AND status=2 '.str_replace('admin_id','result_specifier',$filter));
$add_contact_lead_count=DB::select('SELECT Count(*) as countAll FROM leads WHERE company_id='.$adminAuth->company_id.' AND status=1 '.str_replace('admin_id','result_specifier',$filter));
@endphp


@section('content')
    {{-- Dashboard Ecommerce Starts --}}
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
                    <form method="post" action="{{ route('report.filter') }}">
                        {!! csrf_field() !!}
                        <div class="row">

                            @if($adminAuth->type < 3  || $adminAuth->type == 5  || $adminAuth->type == 6)
                            <div class="col-sm-4">
                                <fieldset class="form-group form-label-group">
                                    <label for="admin">Agent</label>
                                    <select class="form-control select2" id="admin" name="admin">
                                        <option value="">Select</option>
                                        @php
                                        $Agents=\Helper::getCM_DropDown_list('0');
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            @endif

                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label for="from-date">Date</label>
                                    <input type="text" id="from-date" name="from_date" value="{{ $from_date }}" autocomplete="off" class="form-control format-picker" placeholder="From">

                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group form-label-group">
                                    <label for="to-date">Date</label>
                                    <input type="text" id="to-date" name="to_date" value="{{ $to_date }}" autocomplete="off" class="form-control format-picker" placeholder="To">
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex">
                <div>
                    <h2 class="text-primary mb-2" style="white-space: nowrap;">In a Glance</h2>
                </div>
{{--                <div class="w-100"><div class="w-100 border-3 border-primary"></div></div>--}}
            </div>
        </div>
        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($call_property_count[0]->countAll+$call_contact_count[0]->countAll+$call_dc_count[0]->countAll)}}</h5>
                        <p>Calls</p>
                    </div>
                    <div class="avatar bg-rgba-pink p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-phone text-pink font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($note_property_count[0]->countAll+$note_contact_count[0]->countAll)}}</h5>
                        <p>Notes</p>
                    </div>
                    <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-sticky-note-o text-warning font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($email_property_count[0]->countAll+$email_contact_count[0]->countAll+$email_dc_count[0]->countAll)}}</h5>
                        <p>Emails</p>
                    </div>
                    <div class="avatar bg-rgba-yellow p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-mail text-yellow font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($reminder_contact_count[0]->countAll+$reminder_property_count[0]->countAll+$reminder_dc_count[0]->countAll)}}</h5>
                        <p>Reminders</p>
                    </div>
                    <div class="avatar bg-rgba-blue-dark p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-clock text-blue-dark font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($viewing_property_count[0]->countAll+$viewing_contact_count[0]->countAll)}}</h5>
                        <p>Viewings</p>
                    </div>
                    <div class="avatar bg-rgba-purple p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-home text-purple font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($viewing_canceled_property_count[0]->countAll+$viewing_canceled_contact_count[0]->countAll)}}</h5>
                        <p>Cancelled Viewings</p>
                    </div>
                    <div class="avatar bg-rgba-danger p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-home text-danger font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($appointment_property_count[0]->countAll+$appointment_contact_count[0]->countAll)}}</h5>
                        <p>Appointments </p>
                    </div>
                    <div class="avatar bg-rgba-brown-dark p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-calendar-check-o text-brown-dark font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($appointment_canceled_property_count[0]->countAll+$appointment_canceled_contact_count[0]->countAll)}}</h5>
                        <p class="truncate-text" title="Cancelled Appointments">Cancelled Appointments</p>
                    </div>
                    <div class="avatar bg-rgba-danger p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-calendar-check-o text-danger font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($total_contacts_count[0]->countAll)}}</h5>
                        <p>Added Contacts</p>
                    </div>
                    <div class="avatar bg-rgba-turquoise p-50 m-0">
                        <div class="avatar-content">
                            <i class="feather icon-users text-turquoise font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($total_properties_count[0]->countAll)}}</h5>
                        <p>Added Properties</p>
                    </div>
                    <div class="avatar bg-rgba-brown p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-home text-brown font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($add_contact_lead_count[0]->countAll)}}</h5>
                        <p class="truncate-text" title="Added To Contact Leads">Added To Contact Leads</p>
                    </div>
                    <div class="avatar bg-rgba-success p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-magnet text-success font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($closed_lead_count[0]->countAll)}}</h5>
                        <p>Closed Leads</p>
                    </div>
                    <div class="avatar bg-rgba-danger p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-magnet text-danger font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($deal_count[0]->countAll)}}</h5>
                        <p>Deals</p>
                    </div>
                    <div class="avatar bg-rgba-primary p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-handshake-o text-primary font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-3 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($commission_sum[0]->sumAll)}}</h5>
                        <p>Commission (AED)</p>
                    </div>
                    <div class="avatar bg-rgba-secondary p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-money text-secondary font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($listed_count[0]->countAll)}}</h5>
                        <p>Current Listed </p>
                    </div>
                    <div class="avatar bg-rgba-success p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-home text-success font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-sm-6 col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($unlisted_count[0]->countAll)}}</h5>
                        <p class="truncate-text">Current {{Status[2]}}</p>
                    </div>
                    <div class="avatar bg-rgba-warning p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-home text-warning font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($adminAuth->type==1)
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-start pb-0">
                    <div>
                        <h5 class="text-bold-700 mb-0">{{number_format($company_commission_sum[0]->sumAll)}}</h5>
                        <p>Company Commission (AED)</p>
                    </div>
                    <div class="avatar bg-rgba-secondary p-50 m-0">
                        <div class="avatar-content">
                            <i class="fa fa-money text-secondary font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    {{-- Dashboard Ecommerce ends --}}
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('/vendors/js/charts/chart.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
{{--<script src="{{ asset(mix('js/scripts/pages/dashboard-analytics.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/pages/dashboard-ecommerce.js')) }}"></script>--}}

<script>
    $('#admin').val('{{ $admins }}').trigger('change');
</script>

@endsection
