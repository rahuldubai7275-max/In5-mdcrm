{{-- For submenu --}}
<ul class="menu-content">
    @foreach($menu as $submenu)
    <?php
            $submenuTranslation = "";
            if(isset($menu->i18n)){
                $submenuTranslation = $menu->i18n;
            }

            if($submenu->name=='Deal Tracking' && $admin->super!=1)
                continue;

            $badgeDanger=0;
            $countBadgeDanger=0;

            if($submenu->name=='Leave Requests') {
                $request_approver_admin_id = \App\Models\SettingAdmin::where('setting_id', 17)->pluck('admin_id')->toArray();
                $request_main_admin_id = \App\Models\SettingAdmin::where('setting_id', 22)->pluck('admin_id')->toArray();
                $approver_access = 0;
                if ($request_main_admin_id) {
                    if (in_array($admin->id, $request_main_admin_id))
                        $approver_access = 1;
                } else {
                    if ($admin->super == 1)
                        $approver_access = 1;
                }

                if (($approver_access == 1 || in_array($admin->id, $request_approver_admin_id))) {
                    if ($approver_access == 1) {
                        $countBadge = \App\Models\AdminRequest::where('manager_status', 0)->where('cancel_request', 0)->count();
                        $countBadgeDanger = \App\Models\AdminRequest::where('cancel_request', 1)->count();
                    } else {
                        $countBadge = \App\Models\AdminRequest::where('manager_status', 0)->where('hr_status', 0)->count();
                    }

                    if ($countBadge > 0)
                        $submenu->badge = $countBadge;

                    if ($countBadgeDanger > 0)
                        $badgeDanger = $countBadgeDanger;
                }
            }

            if($submenu->name=='Other Requests') {
                $hr_access=0;
                if($admin->type=='6') {
                    $hr_access = 1;
                }else {
                    $SettingAdmin = \App\Models\SettingAdmin::where('setting_id', 16)->where('admin_id', $admin->id)->first();
                    if($SettingAdmin) {
                        $hr_access = 1;
                    }
                }

                $countBadge=0;
                if($hr_access==1)
                    $countBadge=\App\Models\AdminHrRequest::where('status',0)->count();

                if($countBadge>0)
                    $submenu->badge=$countBadge;
            }
        ?>
    <li class="{{ ($submenu->name=='Activities Report') ? 'd-none d-sm-block' : '' }} {{ (request()->is(substr($submenu->url,1))) ? 'active' : '' }}">
        <a href="{{ $submenu->url }}">
            <i class="{{ isset($submenu->icon) ? $submenu->icon : "" }}"></i>
{{--            <span class="menu-title" data-i18n="{{ $submenuTranslation }}">{{ __('locale.'.$submenu->name) }}</span>--}}
            <span class="menu-title" data-i18n="{{ $submenuTranslation }}">{{ __($submenu->name) }}</span>
            @if (isset($submenu->badge))
                    <?php $badgeClasses = "badge badge-pill badge-primary float-right" ?>
                <span
                    class="{{ isset($submenu->badgeClass) ? $submenu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$submenu->badge}}</span>
            @endif
            @if ($badgeDanger)
                    <?php $badgeClasses = "badge badge-pill badge-danger float-right" ?>
                <span {!! (isset($submenu->badge)) ? 'style="right:45px"' : '' !!}
                      class="{{ isset($submenu->badgeClass) ? $submenu->badgeClass.' test' : $badgeClasses.' notTest' }} ">{{$badgeDanger}}</span>
            @endif
        </a>
        @if (isset($submenu->submenu))
        @include('panels/submenu', ['menu' => $submenu->submenu])
        @endif
    </li>
    @endforeach
</ul>
