  @php
$configData = Helper::applClasses();
@endphp
<div
  class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow"
  data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto"><a class="navbar-brand" target="_blank" href="/">
        @php
         $BrandLogo='/images/'.LOGO;
         $MenuIndex=0;
         $admin = Auth::guard('admin')->user();
         $hr_admin=\App\Models\Admin::where('type',6)->pluck('id')->toArray();
         $hr_access=\App\Models\SettingAdmin::where('setting_id',16)->pluck('admin_id')->toArray();
         $survey_access=\App\Models\SettingAdmin::where('setting_id',19)->pluck('admin_id')->toArray();
         $task_access=\App\Models\Setting::find(24);
         $task_access_admin=\App\Models\SettingAdmin::where('setting_id',24)->pluck('admin_id')->toArray();
         $request_approver_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();

         $request_main_admin_id=\App\Models\SettingAdmin::where('setting_id',22)->pluck('admin_id')->toArray();
            $approver_access=0;
            if($request_main_admin_id){
                if(in_array($admin->id, $request_main_admin_id))
                    $approver_access=1;
            }else{
                if($admin->super==1)
                    $approver_access=1;
            }
        @endphp
{{--          <div class="brand-logo" style=" background: url({{ $BrandLogo }}) no-repeat;--}}
{{--  /*background-position: -65px -54px;*/--}}
{{--    background-size: contain;--}}
{{--    background-position: center;"></div>--}}
          <h2 class="brand-text mb-0">{{env('APP_NAME')}}</h2>
        </a></li>
      <li class="nav-item nav-toggle">
        <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
          <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
          <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"
            data-ticon="icon-disc"></i>
        </a>
      </li>
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
      {{-- Foreach menu item starts --}}

      @if(isset($menuData[$MenuIndex]))
      @foreach($menuData[$MenuIndex]->menu as $menu)
      @if(isset($menu->navheader))
      <li class="navigation-header">
        <span>{{ $menu->navheader }}</span>
      </li>
      @else
      {{-- Add Custom Class with nav-item --}}
      @php
          if($menu->name!='User Guide'){
              if(in_array($admin->id, $hr_admin) &&
                 ($menu->name!='Dashboard' && $menu->name!='Calendar' && $menu->name!='Report' && $menu->name!='HR' && $menu->name!='Survey Report' && $menu->name!='Requests'))
                 continue;

              //if(($admin->type>2 && $menu->name!='Property') && ($admin->type>2 && $menu->name!='Contacts'))
              if( ($admin->type>2 && $menu->name=='Admin') || ($admin->type>2 && $menu->name=='Company') )
                continue;

              $deal_access=2;
              if(env('DEAL_ACCESS')!='0')
                  $deal_access=5;

              if($admin->type>$deal_access && $menu->name=='Deals')
                  continue;

              if($admin->type==8 && $menu->name!='Requests' && $menu->name!='Calendar' && $menu->name!='Task Management')
                  continue;

              if($admin->type==7 && $menu->name!='Dashboard' && $menu->slug!='data-center-agent' && $menu->name!='Requests' && $menu->name!='Calendar')
                  continue;

              if($admin->type>1 && $menu->name=='Targets')
                  continue;

              if($admin->super!=1 && $menu->name=='Control Panel')
                  continue;

              if($menu->name=='HR'){
                  //if ($hr_admin){
                  //    if($admin->super!=1 && !in_array($admin->id, $hr_admin))
                  //        continue;
                  //}else{
                  //    if($admin->super!=1 && !in_array($admin->id, $hr_access))
                  //        continue;
                  //}

                  if (!in_array($admin->id, $hr_admin) && !in_array($admin->id, $hr_access) && $admin->type!=1)
                      continue;
              }

              if($admin->super==1 && $menu->name=='Survey Report')
                  continue;

              if($admin->super==1 && $menu->name=='Task Management')
                  continue;

              if( !in_array($admin->id, $survey_access) && $menu->name=='Survey Report')
                  continue;

              if( !in_array($admin->id, $task_access_admin) && $task_access->status!=1 && $menu->name=='Task Management')
                  continue;

              if($admin->super!=1 && $menu->name=='Survey')
                  continue;

              if($admin->super!=1 && $menu->name=='Task')
                  continue;

              if($admin->type>2 && $menu->slug=='data-center')
                  continue;

              if($admin->type<3 && $menu->slug=='data-center-agent')
                  continue;
          }

          $badgeDanger=0;
          $countBadgeDanger=0;

          $custom_classes = "";
          if(isset($menu->classlist)) {
          $custom_classes = $menu->classlist;
          }
          $translation = "";
          if(isset($menu->i18n)){
          $translation = $menu->i18n;
          }
      @endphp
      @if($menu->name=='Leads' || $menu->name=='Properties' || $menu->name=='Contacts' || $menu->name=='Requests' || $menu->name=='Agent Forms')
            <li class="nav-item d-block d-sm-none {{ (request()->is(substr($menu->url,1))) ? 'active' : '' }} {{ $custom_classes }}">
                <a href="{{ $menu->url }}-sm">
                    <i class="{{ $menu->icon }}"></i>
                    {{--<span class="menu-title" data-i18n="{{ $translation }}">{{ __('locale.'.$menu->name) }}</span>--}}
                    <span class="menu-title" data-i18n="{{ $translation }}">{{ __($menu->name) }}</span>

                    @if (isset($menu->badge))
                            <?php $badgeClasses = "badge badge-pill badge-primary float-right" ?>
                        <span
                            class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$menu->badge}}</span>
                    @endif
                    @if ($badgeDanger)
                            <?php $badgeClasses = "badge badge-pill badge-danger float-right" ?>
                        <span {!! (isset($menu->badge)) ? 'style="right:45px"' : '' !!}
                              class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$badgeDanger}}</span>
                    @endif
                </a>
                @if(isset($menu->submenu))
                    @include('panels/submenu', ['menu' => $menu->submenu])
                @endif
            </li>

      @endif
      <li class="nav-item {{ ($menu->name!='Dashboard' && $menu->name!='Developer Projects' && $menu->name!='Calendar' && $menu->name!='Report' && $menu->name!='Control Panel') ? 'd-none d-sm-block' : '' }} {{ (request()->is(substr($menu->url,1))) ? 'active' : '' }} {{ $custom_classes }}">
        <a href="{{ $menu->url }}" {!!  (isset($menu->target) && $menu->target=="_blank") ? 'target="_blank"' : ''  !!}>
          <i class="{{ $menu->icon }}"></i>
          {{--<span class="menu-title" data-i18n="{{ $translation }}">{{ __('locale.'.$menu->name) }}</span>--}}
          <span class="menu-title" data-i18n="{{ $translation }}">{{ __($menu->name) }}</span>
          @if($menu->name=='Requests') <span class="badge badge-pill badge-primary float-right blink_me text-primary d-none" style="right:45px ;">1</span>@endif
          @if (isset($menu->badge))
          <?php $badgeClasses = "badge badge-pill badge-primary float-right" ?>
          <span
            class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$menu->badge}}</span>
          @endif
          @if ($badgeDanger)
          <?php $badgeClasses = "badge badge-pill badge-danger float-right" ?>
          <span {!! (isset($menu->badge)) ? 'style="right:45px"' : '' !!}
            class="{{ isset($menu->badgeClass) ? $menu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$badgeDanger}}</span>
          @endif
        </a>
        @if(isset($menu->submenu))
        @include('panels/submenu', ['menu' => $menu->submenu])
        @endif
      </li>
      @endif
      @endforeach
      @endif
      {{-- Foreach menu item ends --}}

        <li>
            <img src="{{$BrandLogo}}" class="d-block mx-auto" style="width: 70%">
        </li>
    </ul>
  </div>
</div>
<!-- END: Main Menu-->
