@if($configData["mainLayoutType"] == 'horizontal' && isset($configData["mainLayoutType"]))
    <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarColor'] }} navbar-fixed">
        <div class="navbar-header d-xl-block d-none">
            <ul class="nav navbar-nav flex-row">
                <li class="nav-item"><a class="navbar-brand" href="dashboard-analytics">
                        <div class="brand-logo"></div>
                    </a></li>
            </ul>
        </div>
        @else
            <nav
                class="header-navbar navbar-expand-lg navbar navbar-with-menu {{ $configData['navbarClass'] }} navbar-light navbar-shadow {{ $configData['navbarColor'] }}">
                @endif
                <div class="navbar-wrapper">
                    <div class="navbar-container content">
                        <div class="navbar-collapse" id="navbar-mobile">
                            <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                                <ul class="nav navbar-nav">
                                    <li class="nav-item d-xl-none mr-auto">
                                        <a id="mobile-back" class="nav-link" style="font-size: 1.75rem;" data-ajax="false" href="{{ redirect()->getUrlGenerator()->previous() }}"><i class="feather icon-chevron-left"></i></a>
                                    </li>
                                    <li class="nav-item mobile-menu d-xl-none mr-auto">
                                        <a class="nav-link nav-menu-main menu-toggle hidden-xs d-none" href="#"><i class="ficon feather icon-menu"></i></a>
                                    </li>
                                    {{--<li class="nav-item d-xl-none mr-auto">
                                        <a href="javascript:close_window();" id="mobile-back" class="nav-link d-none"><span style="padding: 5px" class="btn btn-primary">Back</span></a>
                                    </li>--}}
                                </ul>

                            </div>
                            <ul class="nav navbar-nav float-right">

                                <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i
                                            class="ficon feather icon-maximize"></i></a></li>

                                @php
                                    $countNotif=0;
                                    $admin = Auth::guard('admin')->user();
                                    $reminderWhere=[ ['note_subject','=','6'],['admin_id','=',$admin->id],['seen','=',0],['date_at','<=',date('Y-m-d')] ];
                                    //$RecruitmentNoteReminderWhere=[ ['note_subject','=','3'],['admin_id','=',$admin->id],['seen','=',0],['date_at','<=',date('Y-m-d')] ];
                                    //$PropertyReminder=App\Models\PropertyNote::where($reminderWhere)->orderBy('id', 'desc')->get();
                                    //$ContactReminder=App\Models\ContactNote::where($reminderWhere)->orderBy('id', 'desc')->get();
                                    //$LeadReminder=App\Models\LeadNote::where($reminderWhere)->orderBy('id', 'desc')->get();
                                    //$RecruitmentNote=App\Models\RecruitmentNote::where($RecruitmentNoteReminderWhere)->orderBy('id', 'desc')->get();
                                    $DataCenterReminder=App\Models\DataCenterNote::where($reminderWhere)->orderBy('id', 'desc')->get();
                                    //$countNotif+=count($PropertyReminder);
                                    //$countNotif+=count($ContactReminder);
                                    //$countNotif+=count($LeadReminder);
                                    $Reminders=App\Models\Activities::where($reminderWhere)->orderBy('time_at', 'ASC')->get();
                                    $countNotif+=count($Reminders);

                                    //$countNotif+=count($RecruitmentNote);
                                    $countNotif+=count($DataCenterReminder);
                                    $today = date('Y-m-d');

                                    $rejectRFL=App\Models\PropertyStatusHistory::where('status',11)->where('rfl_status',2)->where('h_admin_id',$admin->id)->get();
                                    $acceptRFL=App\Models\PropertyStatusHistory::where('status',11)->where('seen',0)->where('rfl_status',1)->where('h_admin_id',$admin->id)->get();
                                    $countNotif+=count($rejectRFL);
                                    $countNotif+=count($acceptRFL);

                                    /*$AdminHrRequest=App\Models\AdminHrRequest::where('admin_id',$admin->id)->
                                    where('status',1)->where('seen','0')->get();
                                    $countNotif+=count($AdminHrRequest);

                                    $AdminRequest=App\Models\AdminRequest::where('admin_id',$admin->id)->
                                    whereIn('manager_status', [1,2])->where('result_seen','0')->get();//where('manager_status','!=','0')
                                    $countNotif+=count($AdminRequest);

                                    $AdminRequestCancelled=App\Models\AdminRequest::where('admin_id',$admin->id)->where('approve_cancel_seen','1')->get();
                                    $countNotif+=count($AdminRequestCancelled);*/

                                    /*$hr_access=\App\Models\SettingAdmin::where('setting_id',16)->pluck('admin_id')->toArray();
                                    $hr_admin=\App\Models\Admin::where('type',6)->where('status',1)->pluck('id')->toArray();
                                    if($hr_admin){
                                        $hr_access=$hr_admin;
                                    }*/

                                    /*if(($admin->super==1 || in_array($admin->id, $hr_access))){
                                        $EU_setting_1=\App\Models\Setting::where('id',13)->first();
                                        $EU_setting_2=\App\Models\Setting::where('id',14)->first();
                                        $EU_setting_3=\App\Models\Setting::where('id',15)->first();

                                        $expirationVisa=App\Models\Admin::where('status', 1)->where('main_number','!=','+971502116655')->where('visa_expiration_date','<=',date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")))->get();
                                        $expirationInsurance=App\Models\Admin::where('status', 1)->where('main_number','!=','+971502116655')->where('insurance_expiration_date','<=',date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")))->get();
                                        $expirationLabourCard=App\Models\Admin::where('status', 1)->where('main_number','!=','+971502116655')->where('labour_card_expiration_date','<=',date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")))->get();
                                        $expirationReraCard=App\Models\Admin::where('status', 1)->where('main_number','!=','+971502116655')->where('rera_card_expiration_date','<=',date('Y-m-d',strtotime($today. "+ ".$EU_setting_1->time." days")))->get();

                                        $countNotif+=count($expirationVisa);
                                        $countNotif+=count($expirationInsurance);
                                        $countNotif+=count($expirationLabourCard);
                                        $countNotif+=count($expirationReraCard);
                                    }*/

                                    $EP_setting_1=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','expiration_property_1')->first();
                                    $EP_setting_2=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','expiration_property_2')->first();
                                    $EP_setting_3=\App\Models\Setting::where('company_id', $admin->company_id)->where('title','expiration_property_3')->first();
                                    $expiration30property=0;
                                    $expiration15property=0;
                                    $expiration7property=0;
                                    if($EP_setting_1){
                                        if($admin->type<3){
                                            if($admin->type==2){
                                                $expiration30property=App\Models\Property::where('company_id', $admin->company_id)->where('status', 1)->whereBetween('expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EP_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EP_setting_1->time." days"))])->count();
                                                $expiration15property=App\Models\Property::where('company_id', $admin->company_id)->where('status', 1)->whereBetween('expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EP_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EP_setting_2->time." days"))])->count();
                                            }
                                            $expiration7property=App\Models\Property::where('company_id', $admin->company_id)->where('status', 1)->whereBetween('expiration_date', [$today, date('Y-m-d',strtotime($today. "+ ".$EP_setting_3->time." days"))])->count();
                                        }
                                        /*else{
                                            $expiration30property=App\Models\Property::orWhere([
                                                                                          ['client_manager_id', '=', $admin->id],
                                                                                          ['client_manager2_id', '=', $admin->id]
                                                                                      ])->where('status', 1)->whereBetween('expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EP_setting_2->time." days")), date('Y-m-d',strtotime($today. "+ ".$EP_setting_1->time." days"))])->count();
                                            $expiration15property=App\Models\Property::orWhere([
                                                                                          ['client_manager_id', '=', $admin->id],
                                                                                          ['client_manager2_id', '=', $admin->id]
                                                                                      ])->where('status', 1)->whereBetween('expiration_date', [date('Y-m-d',strtotime($today. "+ ".$EP_setting_3->time." days")), date('Y-m-d',strtotime($today. "+ ".$EP_setting_2->time." days"))])->count();
                                            $expiration7property=App\Models\Property::orWhere([
                                                                                          ['client_manager_id', '=', $admin->id],
                                                                                          ['client_manager2_id', '=', $admin->id]
                                                                                      ])->where('status', 1)->whereBetween('expiration_date', [$today, date('Y-m-d',strtotime($today. "+ ".$EP_setting_3->time." days"))])->count();
                                        }*/
                                        $countNotif+=$expiration30property;
                                        $countNotif+=$expiration15property;
                                        $countNotif+=$expiration7property;
                                    }
                                  if($admin->type==2){
                                      $DayBefore=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 2)->where('acknowledge', 0)->where('tenancy_renewal_date','<=',date('Y-m-d',strtotime($today. "+ 1 days")))->get();
                                      $WeekBefore=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 3)->where('acknowledge', 0)->where('tenancy_renewal_date','<=', date('Y-m-d',strtotime($today. "+ 7 days")))->get();
                                      $Month1Before=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 4)->where('acknowledge', 0)->where('tenancy_renewal_date','<=', date('Y-m-d',strtotime($today. "+ 30 days")))->get();
                                      $Months2Before=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 5)->where('acknowledge', 0)->where('tenancy_renewal_date','<=', date('Y-m-d',strtotime($today. "+ 60 days")))->get();
                                      $Months3Before=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 6)->where('acknowledge', 0)->where('tenancy_renewal_date','<=', date('Y-m-d',strtotime($today. "+ 90 days")))->get();
                                      $HundredDayBefore=App\Models\Deal::where('company_id', $admin->company_id)->where('set_reminder', 6)->where('acknowledge', 0)->where('tenancy_renewal_date','<=', date('Y-m-d',strtotime($today. "+ 100 days")))->get();

                                      $all_deal_reminder=0;
                                      $all_deal_reminder+=count($DayBefore);
                                      $all_deal_reminder+=count($WeekBefore);
                                      $all_deal_reminder+=count($Month1Before);
                                      $all_deal_reminder+=count($Months2Before);
                                      $all_deal_reminder+=count($Months3Before);
                                      $all_deal_reminder+=count($HundredDayBefore);

                                      $countNotif+=$all_deal_reminder;
                                  }

                                  $leadNotif=App\Models\Lead::where('assign_to',$admin->id)->where('seen','0')->count();
                                  $countNotif+=$leadNotif;

                                  $PropertyNotif=0;
                                  $NewVillaTypeNotif=0;
                                  $NewClusterStreetNotif=0;
                                  if($admin->type==2){
                                    //$PropertyNotif=App\Models\Property::where('status',11)->get();
                                    $PropertyNotif=App\Models\Property::where('company_id', $admin->company_id)->where('status',11)->count();
                                    $countNotif+=$PropertyNotif;//count($PropertyNotif)

                                    //$NewVillaTypeNotif=App\Models\VillaType::where('status','0')->count();
                                    //$NewClusterStreetNotif=App\Models\ClusterStreet::where('status','0')->count();

                                    //$countNotif+=$NewVillaTypeNotif;
                                    //$countNotif+=$NewClusterStreetNotif;
                                  }

                                  $taskWhere=[ ['admin_id','!=',$admin->id],['assign_to','=',$admin->id],['status','=','0'],['date_at','>=',date('Y-m-d')] ];
                                  $tasks=\App\Models\Task::where($taskWhere)->get();
                                  $countNotif+=count($tasks);

                                @endphp

                                @if($admin->type==2)
                                    @php

                                    @endphp
                                @endif
                                <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#"
                                                                                       data-toggle="dropdown"><i class="ficon feather icon-bell"></i><span
                                            class="badge badge-pill badge-primary badge-up">{{$countNotif}}</span></a>
                                    <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                        <li class="dropdown-menu-header">
                                            <div class="dropdown-header m-0 p-2">
                                                <h3 class="white">{{$countNotif}} New</h3><span class="grey darken-2">Notifications</span>
                                            </div>
                                        </li>
                                        <li class="scrollable-container media-list">
                                            @if($admin->type<=2)
                                                @if($PropertyNotif>0)
                                                    <a class="d-none d-sm-flex justify-content-between" href="/admin/properties?p=RL">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left">
                                                                <i class="fa fa-building-o font-medium-5 primary"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Request For Listing - {{$PropertyNotif}}</h6>
                                                                {{--<small class="notification-text"> Do you want to see the properties?</small>--}}
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a class="d-flex d-sm-none justify-content-between" data-ajax="false" href="/admin/properties-sm?p=RL">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left">
                                                                <i class="fa fa-building-o font-medium-5 primary"></i>
                                                            </div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Request For Listing - {{$PropertyNotif}}</h6>
                                                                {{--<small class="notification-text"> Do you want to see the properties?</small>--}}
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endif
                                            @endif
                                            @foreach($acceptRFL as $row)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/property/view/{{$row->property_id}}?rfl={{$row->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left">
                                                            <i class="fa fa-building-o font-medium-5 primary"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="success media-heading">Request For Listing Accepted</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                            @foreach($rejectRFL as $row)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/property-edit/{{$row->property_id}}?rfl={{$row->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left">
                                                            <i class="fa fa-building-o font-medium-5 primary"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="danger media-heading">Request For Listing Rejected</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach

                                            {{--@foreach($AdminHrRequest as $AHRRequest)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/requests-hr?id={{$AHRRequest->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Your request has been replied</h6>
                                                            --}}{{--<small class="notification-text">Replied</small>--}}{{--
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach($AdminRequest as $ARequest)

                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/requests?id={{$ARequest->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 {{($ARequest->manager_status==1)? 'success' : 'danger'}}"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="{{($ARequest->manager_status==1)? 'success' : 'danger'}} media-heading">Leave Requests</h6>
                                                            <small class="notification-text">{{($ARequest->manager_status==1)? 'The request had been approved' : 'The request has been rejected'}}</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach($AdminRequestCancelled as $ARCancelled)

                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/requests?id={{$ARCancelled->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="media-heading">Leave Requests</h6>
                                                            <small class="notification-text">Request for leave cancellation approved</small>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach--}}

                                            {{--@foreach($ContactReminder as $crNotif)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/contact/view/{{$crNotif->contact_id}}?reminder={{$crNotif->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6>
                                                            <small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($crNotif->note),100)}}</small>
                                                        </div>
                                                        <time class="media-meta">{{Helper::changeDatetimeFormat($crNotif->date_at.' '.$crNotif->time_at)}}</time></small>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach($PropertyReminder as $prNotif)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/property/view/{{$prNotif->property_id}}?reminder={{$prNotif->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6><small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($prNotif->note),50)}}</small>
                                                        </div>
                                                        <time class="media-meta">{{Helper::changeDatetimeFormat($prNotif->date_at.' '.$prNotif->time_at)}}</time></small>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach($LeadReminder as $lrNotif)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/lead/view/{{$lrNotif->lead_id}}?reminder={{$lrNotif->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6><small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($lrNotif->note),50)}}</small>
                                                        </div>
                                                        <time class="media-meta">{{Helper::changeDatetimeFormat($lrNotif->date_at.' '.$lrNotif->time_at)}}</time></small>
                                                    </div>
                                                </a>
                                            @endforeach--}}

                                            @foreach($Reminders as $rNotif)
                                                @php
                                                if($rNotif->type=='property')
                                                    $rLink='/admin/property/view/'.$rNotif->property_id.'?reminder='.$rNotif->id;

                                                if($rNotif->type=='contact')
                                                    $rLink='/admin/contact/view/'.$rNotif->contact_id.'?reminder='.$rNotif->id;

                                                if($rNotif->type=='lead')
                                                    $rLink='/admin/lead/view/'.$rNotif->lead_id.'?reminder='.$rNotif->id;

                                                @endphp
                                                <a class="d-flex justify-content-between" data-ajax="false" href="{{$rLink}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6><small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($rNotif->note),50)}}</small>
                                                        </div>
                                                        <time class="media-meta">{{Helper::changeDatetimeFormat($rNotif->date_at.' '.$rNotif->time_at)}}</time></small>
                                                    </div>
                                                </a>
                                            @endforeach

                                            @foreach($tasks as $taskNotif)
                                                @php
                                                $taskTitle=\App\Models\TaskTitle::find($taskNotif->task_title_id);
                                                @endphp
                                                <a class="d-flex justify-content-between" data-ajax="false" href="javascript:void(0);">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            @if($taskTitle) <h6 class="primary media-heading">{{$taskNotif->name}}</h6> @endif
                                                            <small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($taskNotif->description),50)}}</small>
                                                        </div>
                                                        <time class="media-meta">{{Helper::changeDatetimeFormat($taskNotif->date_at.' '.$taskNotif->time_at)}}</time></small>
                                                    </div>
                                                </a>
                                            @endforeach

                                            {{--@foreach($RecruitmentNote as $rrNotif)
                                                <a class="d-flex justify-content-between" href="/admin/recruitment-view/{{$rrNotif->recruitment_id }}?reminder={{$rrNotif->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6>
                                                            <small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($rrNotif->note),100)}}</small>
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endforeach--}}

                                            @foreach($DataCenterReminder as $dcNotif)
                                                <a class="d-flex justify-content-between" data-ajax="false" href="/admin/data-center-view/{{$dcNotif->data_center_id}}?reminder={{$dcNotif->id}}">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">Reminder!</h6><small class="notification-text">{{\Illuminate\Support\Str::limit(strip_tags($dcNotif->note),50)}}</small>
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endforeach

                                            @if($leadNotif > 0)
                                                <a class="d-none d-sm-flex justify-content-between" data-ajax="false" href="/admin/leads?new=true">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">New lead has been assigned to you - {{$leadNotif}}</h6>
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                                <a class="d-flex d-sm-none justify-content-between" data-ajax="false" href="/admin/leads-sm?new=true">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="primary media-heading">New lead has been assigned to you - {{$leadNotif}}</h6>
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endif

                                            @if($expiration7property>0)
                                                <a class="d-none d-sm-flex justify-content-between" href="/admin/properties?p=7-0">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 danger"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="danger media-heading">Less then 7 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                                <a class="d-flex d-sm-none justify-content-between" data-ajax="false" href="/admin/properties-sm?p=7-0">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 danger"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="danger media-heading">Less then 7 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endif

                                            @if($expiration15property>0)
                                                <a class="d-none d-sm-flex justify-content-between" href="/admin/properties?p=15-7">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 warning"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="warning media-heading">Between 7 to 15 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                                <a class="d-flex d-sm-none justify-content-between" data-ajax="false" href="/admin/properties-sm?p=15-7">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 warning"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="warning media-heading">Between 7 to 15 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endif

                                            @if($expiration30property>0)
                                                <a class="d-none d-sm-flex justify-content-between" href="/admin/properties?p=30-15">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 success"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="success media-heading">Between 15 to 30 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                                <a class="d-flex d-sm-none justify-content-between" data-ajax="false" href="/admin/properties-sm?p=30-15">
                                                    <div class="media d-flex align-items-start">
                                                        <div class="media-left"><i class="feather icon-bell font-medium-5 success"></i></div>
                                                        <div class="media-body">
                                                            <h6 class="success media-heading">Between 15 to 30 remaining days to expiration</h6>
                                                            {{--<small class="notification-text">Do you want to see the properties?</small>--}}
                                                        </div>
                                                        <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                    </div>
                                                </a>
                                            @endif


                                                {{--@if(($admin->super==1 || in_array($admin->id, $hr_access)))
                                                    @if(count($expirationVisa)>0)
                                                        @foreach($expirationVisa as $row)
                                                            @php
                                                                $date_two = \Carbon\Carbon::parse($today);
                                                                $days = $date_two->diffInDays($row->visa_expiration_date);

                                                                $euColor='success';
                                                                if($days<$EU_setting_2->time)
                                                                    $euColor='warning';
                                                                if($days<$EU_setting_3->time)
                                                                    $euColor='danger';
                                                            @endphp
                                                            <a class="d-flex justify-content-between" data-ajax="false" href="/admin/admin-profile/{{$row->id}}">
                                                                <div class="media d-flex align-items-start">
                                                                    <div class="media-left"><i class="feather icon-bell font-medium-5 {{$euColor}}"></i></div>
                                                                    <div class="media-body">
                                                                        <h6 class="{{$euColor}} media-heading">Visa expiration date</h6>
                                                                        <small class="notification-text">{{$row->firstname.' '.$row->lastname}}</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif

                                                    @if(count($expirationInsurance)>0)
                                                        @foreach($expirationInsurance as $row)
                                                            @php
                                                                $date_two = \Carbon\Carbon::parse($today);
                                                                $days = $date_two->diffInDays($row->insurance_expiration_date);

                                                                $euColor='success';
                                                                if($days<$EU_setting_2->time)
                                                                    $euColor='warning';
                                                                if($days<$EU_setting_3->time)
                                                                    $euColor='danger';
                                                            @endphp
                                                            <a class="d-flex justify-content-between" data-ajax="false" href="/admin/admin-profile/{{$row->id}}">
                                                                <div class="media d-flex align-items-start">
                                                                    <div class="media-left"><i class="feather icon-bell font-medium-5 {{$euColor}}"></i></div>
                                                                    <div class="media-body">
                                                                        <h6 class="{{$euColor}} media-heading">Insurance expiration date</h6>
                                                                        <small class="notification-text">{{$row->firstname.' '.$row->lastname}}</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif

                                                    @if(count($expirationLabourCard)>0)
                                                        @foreach($expirationLabourCard as $row)
                                                            @php
                                                                $date_two = \Carbon\Carbon::parse($today);
                                                                $days = $date_two->diffInDays($row->labour_card_expiration_date);

                                                                $euColor='success';
                                                                if($days<$EU_setting_2->time)
                                                                    $euColor='warning';
                                                                if($days<$EU_setting_3->time)
                                                                    $euColor='danger';
                                                            @endphp
                                                            <a class="d-flex justify-content-between" data-ajax="false" href="/admin/admin-profile/{{$row->id}}">
                                                                <div class="media d-flex align-items-start">
                                                                    <div class="media-left"><i class="feather icon-bell font-medium-5 {{$euColor}}"></i></div>
                                                                    <div class="media-body">
                                                                        <h6 class="{{$euColor}} media-heading">Labour card expiration date</h6>
                                                                        <small class="notification-text">{{$row->firstname.' '.$row->lastname}}</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif

                                                    @if(count($expirationReraCard)>0)
                                                        @foreach($expirationReraCard as $row)
                                                            @php
                                                                $date_two = \Carbon\Carbon::parse($today);
                                                                $days = $date_two->diffInDays($row->rera_card_expiration_date);

                                                            $euColor='success';
                                                            if($days<$EU_setting_2->time)
                                                                $euColor='warning';
                                                            if($days<$EU_setting_3->time)
                                                                $euColor='danger';
                                                            @endphp
                                                            <a class="d-flex justify-content-between" data-ajax="false" href="/admin/admin-profile/{{$row->id}}">
                                                                <div class="media d-flex align-items-start">
                                                                    <div class="media-left"><i class="feather icon-bell font-medium-5 {{$euColor}}"></i></div>
                                                                    <div class="media-body">
                                                                        <h6 class="{{$euColor}} media-heading">Rera card expiration date</h6>
                                                                        <small class="notification-text">{{$row->firstname.' '.$row->lastname}}</small>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @endif
                                                @endif--}}

                                            @if($admin->type==2)
                                                @foreach($DayBefore as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Reminder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                @foreach($WeekBefore as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Remainder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                @foreach($Month1Before as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Remainder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                @foreach($Months2Before as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Remainder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                @foreach($Months3Before as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Remainder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                @foreach($HundredDayBefore as $row)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/deal-view/{{$row->id}}?t=t">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">Tenancy Renewal Remainder</h6>
                                                            </div>
                                                            <!--<time class="media-meta" datetime="2015-06-11T18:29:20+08:00">9 hours ago</time></small>-->
                                                        </div>
                                                    </a>
                                                @endforeach

                                                {{--@if($NewClusterStreetNotif>0)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/cluster-street?status=0">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">New Cluster / Street / Frond - {{$NewClusterStreetNotif}}</h6>
                                                                --}}{{--                                    <small class="notification-text">Do you want to see the deals?</small>--}}{{--
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endif

                                                @if($NewVillaTypeNotif>0)
                                                    <a class="d-flex justify-content-between" data-ajax="false" href="/admin/type?status=0">
                                                        <div class="media d-flex align-items-start">
                                                            <div class="media-left"><i class="feather icon-bell font-medium-5 primary"></i></div>
                                                            <div class="media-body">
                                                                <h6 class="primary media-heading">New Type - {{$NewVillaTypeNotif}}</h6>
                                                                --}}{{--                                    <small class="notification-text">Do you want to see the deals?</small>--}}{{--
                                                            </div>
                                                        </div>
                                                    </a>
                                                @endif--}}
                                            @endif
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown dropdown-user nav-item">
                                    <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                        @php
                                            if(Auth::guard('admin')->check()){
                                                $admin = Auth::guard('admin')->user();
                                                $logout=route('admin.logout');
                                            }
                                        @endphp
                                        <div class="user-nav d-sm-flex d-none">
                                            <span class="user-name text-bold-600">{{ $admin->firstname.' '.$admin->lastname }}</span>
                                            <span class="user-status">@if($admin->super==1)<span class="text-success font-medium-5"><i class="fa fa-check-circle"></i></span>@endif {{  AdminType[$admin->type] }}</span>
                                        </div>
                                        <span>
                                            <img class="round" src="{{ ($admin->pic_name) ? '/storage/'.$admin->pic_name : '/images/Defult2.jpg'}}" height="40" width="40" />
                                        </span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" data-ajax="false" href="/admin/profile"><i class="feather icon-user"></i> Profile</a>
                                        <a class="dropdown-item" data-ajax="false" href="{{ $logout }}"><i class="feather icon-power"></i> Logout</a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- END: Header-->
