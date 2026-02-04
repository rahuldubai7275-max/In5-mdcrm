@extends('layouts/contentLayoutMaster')

@section('title', 'Dashboard Analytic')

@section('vendor-style')
<!-- vendor css files -->
<link rel="stylesheet" href="{{ asset(mix('vendors/css/charts/apexcharts.css')) }}">

<link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
    <style>
        .task{
            border: 1px solid #d9d9d9;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .task .btn{
            padding: 0.5rem 1rem;
            width: 80px;
        }

        .task-day-box{
            width: 95%;
        }
        .task-day-list{
            list-style: none;
            text-align: center;
            padding: 0;
            font-size: 10px;
            margin: 0;
        }

        .task-day-list b{
            font-size: 14px;
        }
        .task-day-list li{
            margin: auto;
            cursor: pointer;
        }
        .task-day-list .active{
            background-color: #FFFFFF;
            padding: 5px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 1px #b4f0fa;
            width: 40px;
            border-bottom: 3px solid #03c9e4;
        }
    </style>
@endsection

@section('content')
    @php
        $adminAuth = Auth::guard('admin')->user();
    @endphp
    @if($adminAuth->type!=6 && $adminAuth->type!=8)
    @php


        if($adminAuth->type<3){
            $sale_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 1)->count();
            $rent_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 2)->count();

            $residential_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('type', 1)->count();
            $commercial_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('type', 2)->count();

            $buyer_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'buyer')->count();
            $tenant_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'tenant')->count();
            $agent_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'agent')->count();
            $owner_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'owner')->count();
            $developer_count=App\Models\Contact::where('company_id', $adminAuth->company_id)->where('contact_category', 'developer')->count();
            
            $listed_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('status', '1')->count();
            $unlisted_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('status', '2')->count();
            $request_listed_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('status', '11')->count();

            $ma_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('status', '4')->count();

            $listedUnlisted_count=App\Models\Property::where('company_id', $adminAuth->company_id)->where('listing_type_id', 2)->count();

            $new_listed_count=DB::select('SELECT Count(*) as countAll FROM property_status_history, property WHERE property_status_history.property_id=property.id AND company_id='.$adminAuth->company_id.' AND property_status_history.status=1 AND property_status_history.created_at>="'.date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") ).'"');

            $property_archive=DB::select("SELECT STR_TO_DATE(concat( MONTHNAME(created_at),' ','1 ',year(created_at) ), '%M %d %Y') as 'month', count(*) as 'count' FROM `property` WHERE company_id=".$adminAuth->company_id." GROUP BY month ORDER BY `month` DESC LIMIT 0,12");//ORDER BY created_at DESC
            $contact_archive=DB::select("SELECT STR_TO_DATE(concat( MONTHNAME(created_at),' ','1 ',year(created_at) ), '%M %d %Y') as 'month', count(*) as 'count' FROM `contacts` WHERE company_id=".$adminAuth->company_id." GROUP BY month ORDER BY `month` DESC LIMIT 0,12");//ORDER BY created_at DESC
            $open_lead_count=App\Models\Lead::where('company_id', $adminAuth->company_id)->where('status', '0')->count();
        }else{
            $admin_id=$adminAuth->id;
            $sale_count=App\Models\Property::where('listing_type_id', 1)->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();
            $rent_count=App\Models\Property::where('listing_type_id', 2)->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();

            $residential_count=App\Models\Property::where('type', 1)->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();
            $commercial_count=App\Models\Property::where('type', 2)->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();

            $buyer_count=App\Models\Contact::where('contact_category', 'buyer')->where(function($query) use ($admin_id){
                                    $query->where('client_manager', '=', $admin_id)
                                          ->orWhere('client_manager_tow', '=', $admin_id);
                                })->count();
            $tenant_count=App\Models\Contact::where('contact_category', 'tenant')->where(function($query) use ($admin_id){
                                    $query->where('client_manager', '=', $admin_id)
                                          ->orWhere('client_manager_tow', '=', $admin_id);
                                })->count();
            $agent_count=App\Models\Contact::where('contact_category', 'agent')->where(function($query) use ($admin_id){
                                    $query->where('client_manager', '=', $admin_id)
                                          ->orWhere('client_manager_tow', '=', $admin_id);
                                })->count();
            $owner_count=App\Models\Contact::where('contact_category', 'owner')->where(function($query) use ($admin_id){
                                    $query->where('client_manager', '=', $admin_id)
                                          ->orWhere('client_manager_tow', '=', $admin_id);
                                })->count();
            $developer_count=App\Models\Contact::where('contact_category', 'developer')->where(function($query) use ($admin_id){
                                    $query->where('client_manager', '=', $admin_id)
                                          ->orWhere('client_manager_tow', '=', $admin_id);
                                })->count();

            $listed_count=App\Models\Property::where('status', '1')->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();
            $unlisted_count=App\Models\Property::where('status', '2')->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();
            $request_listed_count=App\Models\Property::where('status', '11')->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();

            $ma_count=App\Models\Property::where('status', '4')->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();

            $listedUnlisted_count=App\Models\Property::where('listing_type_id', 2)->where(function($query) use ($admin_id){
                                    $query->where('client_manager_id', '=', $admin_id)
                                          ->orWhere('client_manager2_id', '=', $admin_id);
                                })->count();

            $new_listed_count=DB::select('SELECT Count(*) as countAll FROM property,property_status_history WHERE property.id=property_status_history.property_id AND property_status_history.status=1 AND (property.client_manager2_id='.$adminAuth->id.' or property.client_manager_id='.$adminAuth->id.') AND property_status_history.created_at>="'.date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 10 days") ).'"');

            $property_archive=DB::select("SELECT STR_TO_DATE(concat( MONTHNAME(created_at),' ','1 ',year(created_at) ), '%M %d %Y') as 'month', count(*) as 'count' FROM `property` WHERE admin_id=".$adminAuth->id." GROUP BY month ORDER BY `month` DESC LIMIT 0,12");//ORDER BY created_at DESC
            $contact_archive=DB::select("SELECT STR_TO_DATE(concat( MONTHNAME(created_at),' ','1 ',year(created_at) ), '%M %d %Y') as 'month', count(*) as 'count' FROM `contacts` WHERE admin_id=".$adminAuth->id." GROUP BY month ORDER BY `month` DESC LIMIT 0,12");//ORDER BY created_at DESC


            $leadWhere=' AND company_id='.$adminAuth->company_id.' ';
            $lead_setting=\App\Models\Setting::where('company_id', $adminAuth->company_id)->where('title','open_lead')->first();
            $lead_setting_admin_id=\App\Models\SettingAdmin::where('setting_id',$lead_setting->id)->where('admin_id','!=',$adminAuth->id)->pluck('admin_id')->toArray();

            if($lead_setting->status==1 && $adminAuth->type>1 && $lead_setting_admin_id) {
                //if($lead_setting_admin_id)
                    $leadWhere .= ' AND assign_to NOT IN ('.join(',',$lead_setting_admin_id).') ';
            }

            if($adminAuth->type>2){
                $now=date('Y-m-d H:i:s');
                if($lead_setting->status==1) {
                    if ($lead_setting->time_type == 1) {
                        $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " minutes"));
                    }

                    if ($lead_setting->time_type == 2) {
                        $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " hours"));
                    }

                    if ($lead_setting->time_type == 3) {
                        $date_time = date('Y-m-d H:i:s', strtotime($now . " - " . $lead_setting->time . " days"));
                    }

                    $leadWhere.=" AND (assign_to=".$adminAuth->id." OR assign_time<'".$date_time."' )";
                }else{
                    //$date_time=date('Y-m-d H:i:s',strtotime($now. " - 1 days") );
                    $leadWhere.=" AND assign_to=".$adminAuth->id;
                }
                //$where.="AND (assign_to=".$adminAuth->id." OR leads.assign_time<'".$date_time."' )";
            }

            //if($adminAuth->type>2){
            //    $now=date('Y-m-d H:i:s');
            //    $leadWhere="AND (assign_to=".$adminAuth->id." OR leads.assign_time<'".date('Y-m-d H:i:s',strtotime($now. " - 1 days") )."' )";
            //}
            $open_lead_count_obj=DB::select("SELECT COUNT(*) as countAll FROM leads WHERE status=0 ".$leadWhere);
            $open_lead_count=$open_lead_count_obj[0]->countAll;
        }

        $pa_name=[];
        $pa_count=[];
        foreach ($property_archive as $row){
            $pa_name[]=$row->month;
            $pa_count[]=$row->count;
        }
        $ca_name=[];
        $ca_count=[];
        foreach ($contact_archive as $row){
            $ca_name[]=$row->month;
            $ca_count[]=$row->count;
        }
        $pa_pc_mount=array_unique(array_merge($pa_name, $ca_name));


        $add_to_contact_count=App\Models\Lead::where('company_id', $adminAuth->company_id)->where('status', '1')->count();
        $close_count=App\Models\Lead::where('company_id', $adminAuth->company_id)->where('status', '2')->count();
        $delete_count=App\Models\Lead::where('company_id', $adminAuth->company_id)->where('status', '3')->count();
    @endphp

    {{-- Dashboard Ecommerce Starts --}}
    <div class="row d-sm-none d-flex">
        <div class="col-6">
            <a href="/admin/leads-sm" class="card text-center">
                <div class="card-content">
                    <div class="card-body">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fa fa-magnet font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 line-ellipsis">Leads</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/properties-sm" class="card text-center">
                <div class="card-content">
                    <div class="card-body">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fa fa-building-o font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 truncate-text">Properties</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/contacts-sm" class="card text-center">
                <div class="card-content">
                    <div class="card-body">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="feather icon-user font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 line-ellipsis">Contacts</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/calendar" class="card text-center">
                <div class="card-content">
                    <div class="card-body">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="feather icon-calendar font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 line-ellipsis">Calendar</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="/admin/report" class="card text-center">
                <div class="card-content">
                    <div class="card-body px-0">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fa fa-line-chart font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 line-ellipsis">In a Glance</p>
                    </div>
                </div>
            </a>
        </div>
        <a href="/admin/off-plan-projects" class="col-6">
            <div class="card text-center">
                <div class="card-content">
                    <div class="card-body">
                        <div class="p-50 m-0 mb-1">
                            <div class="avatar-content">
                                <i class="fa fa-building-o font-large-1"></i>
                            </div>
                        </div>
                        <p class="mb-0 truncate-text">New Projects</p>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="row">
        <div class="col-12 order-2">
            <div class="row d-none d-sm-flex left-right-card-border">
                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/leads" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($open_lead_count)}}</h2>
                                <p>Open leads</p>
                            </div>
                            <div class="avatar bg-rgba-brown-dark p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-magnet text-brown-dark font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties?p=new_listing" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($new_listed_count[0]->countAll)}}</h2>
                                <p>Last 10 days listed</p>
                            </div>
                            <div class="avatar bg-rgba-dark p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-list text-dark font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties?p=rfl" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($request_listed_count)}}</h2>
                                <p>Requests for listing</p>
                            </div>
                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-list text-primary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties?p=listing" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($listed_count)}}</h2>
                                <p>Listed Properties </p>
                            </div>
                            <div class="avatar bg-rgba-success p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-home text-success font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties?p=unlisted" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($unlisted_count)}}</h2>
                                <p>{{Status[2]}}s</p>
                            </div>
                            <div class="avatar bg-rgba-warning p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-home text-warning font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties?p=ma" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($ma_count)}}</h2>
                                <p>MA</p>
                            </div>
                            <div class="avatar bg-rgba-danger p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-alert-octagon text-danger font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/contacts?c=buyers" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($buyer_count)}}</h2>
                                <p>Buyers</p>
                            </div>
                            <div class="avatar bg-rgba-purple p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-smile-o text-purple font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/contacts?c=tenants" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($tenant_count)}}</h2>
                                <p>Tenants</p>
                            </div>
                            <div class="avatar bg-rgba-secondary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-meh-o text-secondary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
            <div class="row d-sm-none left-right-card-border">
                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/leads-sm" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($open_lead_count)}}</h2>
                                <p>Open leads</p>
                            </div>
                            <div class="avatar bg-rgba-brown-dark p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-magnet text-brown-dark font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties-sm?p=new_listing" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($new_listed_count[0]->countAll)}}</h2>
                                <p>Last 10 days listed</p>
                            </div>
                            <div class="avatar bg-rgba-dark p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-list text-dark font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties-sm?p=rfl" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($request_listed_count)}}</h2>
                                <p>Requests for listing</p>
                            </div>
                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-list text-primary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties-sm?p=listing" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($listed_count)}}</h2>
                                <p>Listed Properties </p>
                            </div>
                            <div class="avatar bg-rgba-success p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-home text-success font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties-sm?p=unlisted" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($unlisted_count)}}</h2>
                                <p>{{Status[2]}}s</p>
                            </div>
                            <div class="avatar bg-rgba-warning p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-home text-warning font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/properties-sm?p=ma" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($ma_count)}}</h2>
                                <p>MA</p>
                            </div>
                            <div class="avatar bg-rgba-danger p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-alert-octagon text-danger font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/contacts-sm?c=buyers" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($buyer_count)}}</h2>
                                <p>Buyers</p>
                            </div>
                            <div class="avatar bg-rgba-purple p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-smile-o text-purple font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-lg-3 col-sm-6 col-12">
                    <a href="/admin/contacts-sm?c=buyers" class="card">
                        <div class="card-header d-flex align-items-start pb-0">
                            <div>
                                <h2 class="text-bold-700 mb-0">{{number_format($tenant_count)}}</h2>
                                <p>Tenant</p>
                            </div>
                            <div class="avatar bg-rgba-secondary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="fa fa-meh-o text-secondary font-medium-5"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
        @include('admin/recruitment-activity')

        @include('admin/task-dashboard')

        @include('admin/task-add-modal')

        @if($adminAuth->type!=2)
            @php
            //date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s'). "- 30 days") );

            $smallYear=DB::select('SELECT MIN(YEAR(created_at)) AS smallYear FROM `targets_history` '.(($adminAuth->type!=1) ? ' WHERE admin_id='.$adminAuth->id : ''));
            $firstYear_monthly=($smallYear[0]->smallYear) ? $smallYear[0]->smallYear : date('Y');
            $firstYear_yearly=($smallYear[0]->smallYear) ? $smallYear[0]->smallYear : date('Y');

            if($adminAuth->type > 2){
                $target_month_sum=DB::select('SELECT num_calls , num_viewing , num_ma , num_listing , commission FROM `targets` WHERE period=1 '. ( ($adminAuth->type > 2) ? ' AND admin_id='.$adminAuth->id : '' ) );
            }else{
                $target_month_sum=DB::select('SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets` WHERE period=1' );
            }

            @endphp
            @if($target_month_sum)
            <div class="col-md-4 col-12 order-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Month to date target</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">


                            <div class="row">
                                <div class="col-sm-6">
                                    <fieldset class="form-group form-label-group">
                                        <label for="admin">Year</label>
                                        <select class="form-control target-month-year" id="year" name="year">
                                            @for($firstYear_monthly;$firstYear_monthly<=date('Y');$firstYear_monthly++)
                                                <option value="{{$firstYear_monthly}}">{{$firstYear_monthly}}</option>
                                            @endfor
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-sm-6">
                                    <fieldset class="form-group form-label-group">
                                        <label for="month">Month</label>
                                        <select class="form-control target-month" id="month" name="month">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </fieldset>
                                </div>
                            </div>

                            @if($adminAuth->type < 3)
                            <fieldset class="form-group form-label-group">
                                <label for="admin">Client Manager</label>
                                <select class="form-control select2" id="month-target-admin" name="month-target-admin">
                                    <option value="">Select</option>
                                    @php
                                    $Agents=$Agents=DB::select('SELECT admins.* FROM admins,targets WHERE admins.id=admin_id AND targets.company_id='.$adminAuth->company_id.' AND period=1 ORDER BY admins.firstname ASC');//App\Models\Admin::get();
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            @endif

                            <div class="w-100 month-target-box">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @php
                $smallYear=DB::select('SELECT MIN(YEAR(created_at)) AS smallYear FROM `targets_history` '.(($adminAuth->type!=1) ? ' WHERE admin_id='.$adminAuth->id : ''));
                $firstYear_monthly=($smallYear[0]->smallYear) ? $smallYear[0]->smallYear : date('Y');
                $firstYear_yearly=($smallYear[0]->smallYear) ? $smallYear[0]->smallYear : date('Y');

                if($adminAuth->type > 2){
                    $target_year_sum=DB::select('SELECT num_calls , num_viewing , num_ma , num_listing , commission FROM `targets` WHERE period=2 '. ( ($adminAuth->type > 2) ? ' AND admin_id='.$adminAuth->id : '' ) );
                }else{
                    $target_year_sum=DB::select('SELECT SUM(`num_calls`) as num_calls , SUM(`num_viewing`) as  num_viewing , SUM(`num_ma`) as num_ma , SUM(`num_listing`) as num_listing , SUM(`commission`) as commission FROM `targets` WHERE period=2' );
                }
            @endphp
            @if($target_year_sum)
            <div class="col-md-4 col-12 order-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Year to date target</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">


                            <fieldset class="form-group form-label-group">
                                <label>Year</label>
                                <select class="form-control target-year-year">
                                    @for($firstYear_yearly;$firstYear_yearly<=date('Y');$firstYear_yearly++)
                                        <option value="{{$firstYear_yearly}}">{{$firstYear_yearly}}</option>
                                    @endfor
                                </select>
                            </fieldset>

                            @if($adminAuth->type < 3)
                            <fieldset class="form-group form-label-group">
                                <label for="admin">Client Manager</label>
                                <select class="form-control select2" id="year-target-admin" name="year-target-admin">
                                    <option value="">Select</option>
                                    @php
                                    $Agents=DB::select('SELECT admins.* FROM admins,targets WHERE admins.id=admin_id AND targets.company_id='.$adminAuth->company_id.' AND period=2 ORDER BY admins.firstname ASC');//App\Models\Admin::get();
                                    @endphp
                                    @foreach($Agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                            @endif

                            <div class="w-100 year-target-box">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endif
        <div class="col-lg-4 col-12 order-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between pb-0">
                    <h4 class="card-title">Property Listing</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pt-0 pb-5">
                        <div id="customer-chart"></div>
                    </div>
                    <ul class="list-group list-group-flush customer-info" style="position: absolute;width: 100%;bottom: 12px;">
                        <li class="list-group-item d-flex justify-content-between ">
                            <div class="series-info">
                                <i class="fa fa-circle font-small-3 text-primary"></i>
                                <span class="text-bold-600">Sale</span>
                            </div>
                            <div class="product-result">
                                <span>{{$sale_count}}</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between ">
                            <div class="series-info">
                                <i class="fa fa-circle font-small-3 text-warning"></i>
                                <span class="text-bold-600">Rent</span>
                            </div>
                            <div class="product-result">
                                <span>{{$rent_count}}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @if($adminAuth->type!=2)
        <div class="col-lg-4 col-12 order-12" style="padding-bottom: 2.2rem">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between pb-0">
                    <h4 class="card-title">Residential/Commercial</h4>
                </div>
                <div class="card-content">
                    <div class="card-body py-0">
                        <div id="property-type-chart"></div>
                        <div id="session-chart"></div>
                    </div>
                    <ul class="list-group list-group-flush customer-info">
                        <li class="list-group-item d-flex justify-content-between ">
                            <div class="series-info">
                                <i class="fa fa-circle font-small-3 text-primary"></i>
                                <span class="text-bold-600">Residential</span>
                            </div>
                            <div class="product-result">
                                <span>{{$residential_count}}</span>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between ">
                            <div class="series-info">
                                <i class="fa fa-circle font-small-3 text-warning"></i>
                                <span class="text-bold-600">Commercial</span>
                            </div>
                            <div class="product-result">
                                <span>{{$commercial_count}}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @endif


        <div class="col-md-4 order-12" style="padding-bottom: 2.2rem;">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Leads</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div id="lead-status-chart" class="height-300"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 order-12" style="padding-bottom: 2.2rem">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title">Contact Categories</h4>
                </div>
                <style>
                .height-320 {
                      height: 320px;
                    }
                </style>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-320">
                          <canvas id="contact-cat-chart"></canvas>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
            @if($adminAuth->type==2)
                <div class="col-lg-4 col-12 order-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <h4 class="card-title">Residential/Commercial</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body py-0">
                                <div id="property-type-chart"></div>
                                <div id="session-chart"></div>
                            </div>
                            <ul class="list-group list-group-flush customer-info">
                                <li class="list-group-item d-flex justify-content-between ">
                                    <div class="series-info">
                                        <i class="fa fa-circle font-small-3 text-primary"></i>
                                        <span class="text-bold-600">Residential</span>
                                    </div>
                                    <div class="product-result">
                                        <span>{{$residential_count}}</span>
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between ">
                                    <div class="series-info">
                                        <i class="fa fa-circle font-small-3 text-warning"></i>
                                        <span class="text-bold-600">Commercial</span>
                                    </div>
                                    <div class="product-result">
                                        <span>{{$commercial_count}}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        <div class="col-md-12 d-none d-sm-block  order-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Added properties and contacts</h4>
                </div>
                <div class="card-content">
                    <div class="card-body pl-0">
                        <div class="height-300">
                            <div id="revenue-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade text-left" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel20" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel20"></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Ref</th>
                            <th>CM</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer justify-content-start">

                </div>
            </div>
        </div>
    </div>

    {{-- Dashboard Ecommerce ends --}}
    @endif

    @if($adminAuth->type==6)
        <div class="row">
            @include('admin/recruitment-activity')

            @include('admin/task-dashboard')

            @include('admin/task-add-modal')
        </div>
    @endif

    @if($adminAuth->type==8)
        <div class="row">
            @include('admin/task-dashboard')

            @include('admin/task-add-modal')
        </div>
    @endif
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('/vendors/js/charts/chart.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
<script src="{{ asset(mix('/vendors/js/charts/echarts/echarts.min.js')) }}"></script>

<script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
@if($adminAuth->type!=6 && $adminAuth->type!=8)
<script>

  var $primary = '#7367F0';
  var $success = '#28C76F';
  var $danger = '#EA5455';
  var $warning = '#FF9F43';
  var $info = '#00cfe8';
  var $primary_light = '#A9A2F6';
  var $danger_light = '#f29292';
  var $success_light = '#55DD92';
  var $warning_light = '#ffc085';
  var $info_light = '#1fcadb';
  var $strok_color = '#b9c3cd';
  var $label_color = '#e7e7e7';
  var $white = '#fff';

  var grid_line_color = '#dae1e7';
  var scatter_grid_color = '#f3f3f3';
  var $scatter_point_light = '#D1D4DB';
  var $scatter_point_dark = '#5175E0';
  var $black = '#000';

  var themeColors = [$primary, $success, $danger, $warning, $label_color];
      // Customer Chart
  // -----------------------------

  var customerChartoptions = {
    chart: {
      type: 'pie',
      height: 330,
      dropShadow: {
        enabled: false,
        blur: 5,
        left: 1,
        top: 1,
        opacity: 0.2
      },
      toolbar: {
        show: false
      }
    },
    labels: ['Sale', 'Rent'],
    series: [{{$sale_count}}, {{$rent_count}}],
    dataLabels: {
      enabled: false
    },
    legend: { show: false },
    stroke: {
      width: 5
    },
    colors: [$primary, $warning],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [$primary_light, $warning_light]
      }
    }
  }

  var customerChart = new ApexCharts(
    document.querySelector("#customer-chart"),
    customerChartoptions
  );

  customerChart.render();
/*
  var propertyTypeChartoptions = {
    chart: {
      type: 'pie',
      height: 330,
      dropShadow: {
        enabled: false,
        blur: 5,
        left: 1,
        top: 1,
        opacity: 0.2
      },
      toolbar: {
        show: false
      }
    },
    labels: ['Residential', 'Commercial'],
    series: [{{$residential_count}}, {{$commercial_count}}],
    dataLabels: {
      enabled: false
    },
    legend: { show: false },
    stroke: {
      width: 5
    },
    colors: [$primary, $warning],
    fill: {
      type: 'gradient',
      gradient: {
        gradientToColors: [$primary_light, $warning_light]
      }
    }
  }

  var propertyTypeChart = new ApexCharts(
    document.querySelector("#property-type-chart"),
    propertyTypeChartoptions
  );

  propertyTypeChart.render();

*/

  var sessionChartoptions = {
      chart: {
          type: 'donut',
          height: 325,
          toolbar: {
              show: false
          }
      },
      dataLabels: {
          enabled: false
      },
      series: [{{$residential_count}}, {{$commercial_count}}],
      legend: { show: false },
      // comparedResult: [2,  8],
      labels: ['Residential', 'Commercial'],
      stroke: { width: 0 },
      colors: [$primary, '#FF6F00'],
      fill: {
          type: 'gradient',
          gradient: {
              gradientToColors: [$primary_light, '#FFE57F']
          }
      }
  }

  var sessionChart = new ApexCharts(
      document.querySelector("#session-chart"),
      sessionChartoptions
  );

  sessionChart.render();

////////////////////////////////////////////////////////////////////////////////////////////////////

 var contactCatCharctx = document.getElementById('contact-cat-chart').getContext('2d');

var contactCatCharOptions = {
  responsive: true,
  maintainAspectRatio: false,
  responsiveAnimationDuration: 500,
  legend: { display: false },

  scales: {
    xAxes: [{
      gridLines: {
        color: grid_line_color
      },
      ticks: {
        beginAtZero: true,
        precision: 0
      }
    }],
    yAxes: [{
      gridLines: {
        color: grid_line_color
      },
      ticks: {
        beginAtZero: true
      }
    }]
  }
};

var contactCatCharData = {
  labels: ["Buyer", "Tenant", "Agent", "Owner", "Developer"],
  datasets: [{
    data: [
      {{$buyer_count}},
      {{$tenant_count}},
      {{$agent_count}},
      {{$owner_count}},
      {{$developer_count}}
    ],
    backgroundColor: [
      '#1976D2',
      '#1E88E5',
      '#2196F3',
      '#42A5F5',
      '#93c9f5'
    ],
    borderWidth: 0,
    barThickness: 18,
    maxBarThickness: 22
  }]
};

new Chart(contactCatCharctx, {
  type: 'horizontalBar',
  data: contactCatCharData,
  options: contactCatCharOptions
});


////////////////////////////////////////////////////////////////////////////////////////////////

  var $dark_green = '#4ea397';
  var $green = '#22c3aa';
  var $light_green = '#7bd9a5';
  var $lighten_green = '#a8e7d2';
  var barChart = echarts.init(document.getElementById('lead-status-chart'));

  // var i;
  function randomize() {
      return Math.round(300 + Math.random() * 700) / 10
  };

  var barChartoption = {
      legend: {},
      tooltip: {},
      dataset: {
          source: [
              ['product', 'Open', 'Closed', 'Add to contact' @if($adminAuth->type==1) ,'Deleted' @endif],
              ['Leads', {{$open_lead_count}}, {{$close_count}}, {{$add_to_contact_count}} @if($adminAuth->type==1) ,{{$delete_count}} @endif],
          ],
      },


      xAxis: {
          type: 'category',
          splitLine: { show: true },
      },
      yAxis: {},
      // Declare several bar series, each will be mapped
      // to a column of dataset.source by default.
      series: [
          {
              type: 'bar',
              itemStyle: {color: $dark_green},
          },
          {
              type: 'bar',
              itemStyle: {color: $green},
          },
          {
              type: 'bar',
              itemStyle: {color: $light_green},
          },
          {
              type: 'bar',
              itemStyle: {color: $lighten_green},
          }
      ]
  };
  barChart.setOption(barChartoption);


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  var revenueChartoptions = {
      chart: {
          height: 270,
          toolbar: { show: false },
          type: 'line',
      },
      stroke: {
          curve: 'smooth',
          dashArray: [0, 8],
          width: [4, 2],
      },
      grid: {
          borderColor: $label_color,
      },
      legend: {
          show: false,
      },
      colors: [$danger_light, $strok_color],

      fill: {
          type: 'gradient',
          gradient: {
              shade: 'dark',
              inverseColors: false,
              gradientToColors: [$primary, $strok_color],
              shadeIntensity: 1,
              type: 'horizontal',
              opacityFrom: 1,
              opacityTo: 1,
              stops: [0, 100, 100, 100]
          },
      },
      markers: {
          size: 0,
          hover: {
              size: 5
          }
      },
      xaxis: {
          labels: {
              style: {
                  colors: $strok_color,
              }
          },
          axisTicks: {
              show: false,
          },
          categories: [ @foreach ($pa_pc_mount as $row) {!! "'".date('Y, F',strtotime($row))."'" !!} @if(!$loop->last) {{','}} @endif  @endforeach].reverse(),
          axisBorder: {
              show: false,
          },
          tickPlacement: 'on',
      },
      yaxis: {
          tickAmount: 5,
          labels: {
              style: {
                  color: $strok_color,
              },
              formatter: function (val) {
                  return val > 999 ? (val / 1000).toFixed(1) + 'k' : val;
              }
          }
      },
      tooltip: {
          x: { show: false }
      },
      series: [{
          name: "Properties",
          data: [@foreach ($pa_count as $row) {!! "'".$row."'" !!} @if(!$loop->last) {{','}} @endif  @endforeach].reverse()
      },
          {
              name: "Contacts",
              data: [@foreach ($ca_count as $row) {!! "'".$row."'" !!} @if(!$loop->last) {{','}} @endif  @endforeach].reverse()
          }
      ],

  }

  var revenueChart = new ApexCharts(
      document.querySelector("#revenue-chart"),
      revenueChartoptions
  );

  revenueChart.render();

</script>

<script>
    $('body').on('click','.show-target',function(){
        let type=$(this).data('type');
        //let admin=$('#month-target-admin').val();

        $('#targetModal .modal-title').html('Number of '+type);
        $('#targetModal .modal-body tbody').html('');
        let admin=$('#month-target-admin').val();
        let period=1;
        let year=$('.target-month-year').val();
        let month=$('.target-month').val();

        $.ajax({
            url:"{{ route('target-ajax-show') }}",
            type:"POST",
            data:{
                _token:'{{csrf_token()}}',
                type:type,
                admin:admin,
                period:period,
                year:year,
                month:month,
            },
            success:function (response) {
                $('#targetModal .modal-body tbody').html(response);
            },error: function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
    });

    $('#month-target-admin , .target-month-year , .target-month').change(function(){

        let admin=$('#month-target-admin').val();
        let period=1;
        let year=$('.target-month-year').val();
        let month=$('.target-month').val();
        $.ajax({
            url:"{{ route('target-ajax-dashboard') }}",
            type:"POST",
            data:{
                _token:'{{csrf_token()}}',
                admin:admin,
                period:period,
                year:year,
                month:month,
            },
            success:function (response) {
                $('.month-target-box').html(response);
            },error: function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
    });

    $('#year-target-admin , .target-year-year').change(function(){
        let admin=$('#year-target-admin').val();
        let period=2;
        let year=$('.target-year-year').val();
        $.ajax({
            url:"{{ route('target-ajax-dashboard') }}",
            type:"POST",
            data:{
                _token:'{{csrf_token()}}',
                admin:admin,
                period:period,
                year:year
            },
            success:function (response) {
                $('.year-target-box').html(response);
            },error: function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
    });

    let arrayMonth=['January','February','March','April','May','June','July','August','September','October','November','December'];
    let currentYear='{{date('Y')}}';
    let currentMonth='{{date('m')}}';

    $('#month').val('{{date('m')}}');
    $('#year').val('{{date('Y')}}').change();
    $('#year').change(function () {
        let val=$(this).val();
        $('#month').removeAttr('disabled');

        let toMonth='';
        if(val=='all' ){
            $('#month').val('').attr('disabled','disabled');
        }else if(currentYear==val){
            toMonth=currentMonth;
        }else{
            toMonth=12;
        }

        let monthHtml='';//'<option value="">Overall</option>';
        let selectMount=$('#month').val();
        for(let i=0;i<toMonth;i++){
            let monthNum=i+1;
            if(monthNum<10){
                monthNum='0'+monthNum;
            }
            monthHtml+='<option value="'+monthNum+'">'+arrayMonth[i]+'</option>';
        }
        $('#month').html(monthHtml);
        $('#month').val(selectMount);

    });

    $('#year').change();
    $('.target-year-year').change();
</script>
<script>
    $('#mobile-back').addClass('d-none');
    $('#navbar-mobile .mobile-menu .menu-toggle').removeClass('d-none');
</script>
@endif

    <script>
        $('body').on('click','.task-status', function () {
            let id=$(this).parent().data('id');
            let model=$(this).parent().data('model');
            let status=$(this).data('type');

            let confirm='cancel';
            if(status==1)
                confirm='done';

            Swal.fire({
                title: 'Are you sure?',
                // text: "You want to activate!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes, '+confirm,
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="_id">');
                    $('.delete-form-box form').append('<input type="hidden" value="'+status+'" name="status">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input:submit').click();
                }
            })
        });

        $('body').on('click','.task-cancel', function () {
            let id=$(this).parent().data('id');
            $('#task_cancel_id').val(id);
        });

        (function(window, document, $) {
            'use strict';

            $('.limit-format-picker').pickadate({
                format: 'yyyy-mm-dd',
                min:true
            });

            /*******    Pick-a-time Picker  *****/
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' +dd ;
            let date=today;

            var $input= $('.limit-timepicker').pickatime({
                format: 'HH:i',
                interval:10,    });

            var picker = $input.pickatime('picker');

            $('#DateAt').change(function(){
                date=$(this).val();
                $('#TimeAt').val('');
                if(today==date){
                    picker.set('min', true);
                }else{
                    picker.set('min', false);
                }
            });
        })(window, document, jQuery);
    </script>

    <script>
        let todayDate='{{ date('Y-m-d') }}';
        let dayCount=-4;
        let selectDay=todayDate;
        let days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        setDaysList();
        function setDaysList() {
            let d = new Date();
            if(dayCount<0) {
                d.setDate(d.getDate() - Math.abs(dayCount));
            }else {
                d.setDate(d.getDate() + dayCount);
            }

            let i = 1
            let daysHtml = '';
            for (i; i <= 7; i++) {
                d.setDate(d.getDate() + 1);
                let fullDate = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
                daysHtml += '<li class="' + ((selectDay == fullDate) ? 'active' : '') + '" data-date="' + fullDate + '">' + days[d.getDay()] + '<br><b>' + d.getDate() + '</b></li>';
            }

            $('.task-day-box ul').html(daysHtml);
        }

        $('.task-day-previous').click(function (){
            dayCount--;
            setDaysList();
        });

        $('.task-day-next').click(function (){
            dayCount++;
            setDaysList();
        });

        $('body').on('click','.task-day-box ul li',function () {
            let date = $(this).data('date');
            selectDay = date;
            $('.task-day-box ul li.active').removeClass('active');
            $(this).addClass('active');
            getDayTasks(date);
        });
        getDayTasks(todayDate);
        function getDayTasks(date){
            $.ajax({
                url:"{{ route('task.day') }}",
                type:"POST",
                data:{
                    _token:'{{csrf_token()}}',
                    date:date,
                },
                success:function (response) {
                    $('.task-list-box').html(response);
                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

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
    </script>
    <script src="/js/scripts/select2.multi-checkboxes.js"></script>
    <script>
        $('.select2-checkbox').select2MultiCheckboxes({
            placeholder: "Choose multiple elements",
        })
    </script>

@endsection
