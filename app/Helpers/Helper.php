<?php // Code within app\Helpers\Helper.php

//namespace App\Helpers;

//use Config; //Arash Comment

class Helper
{
    public static function applClasses()
    {
        $adminAuth=\Auth::guard('admin')->user();
        $Theme='';
        if($adminAuth) {
            $Theme = \App\Models\ThemeSetting::where('admin_id', $adminAuth->id)->first();
        }

        // default data array
        $DefaultData = [
            'mainLayoutType' => 'vertical',
            'theme' => ($Theme && $Theme->theme_layout)? $Theme->theme_layout : 'light',
            'sidebarCollapsed' => ($Theme && $Theme->collapse_sidebar=='menu-collapsed') ? true : false,
            'navbarColor' => ($Theme && $Theme->navbar_color)? $Theme->navbar_color : '',
            'horizontalMenuType' => 'floating',
            'verticalMenuNavbarType' => ($Theme && $Theme->navbar_type)? $Theme->navbar_type :'floating',//'floating',
            'footerType' => ($Theme && $Theme->footer_type)? $Theme->footer_type : 'static', //footer
            'bodyClass' => ($Theme && $Theme->footer_type)? $Theme->footer_type : '',
            'pageHeader' => true,
            'contentLayout' => 'default',
            'blankPage' => false,
            'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'),
        ];

        // if any key missing of array from custom.php file it will be merge and set a default value from dataDefault array and store in data variable
        $data = array_merge($DefaultData, config('custom.custom'));

        // All options available in the template
        $allOptions = [
            'mainLayoutType' => array('vertical', 'horizontal'),
            'theme' => array('light' => 'light', 'dark' => 'dark-layout', 'semi-dark' => 'semi-dark-layout'),
            'sidebarCollapsed' => array(true, false),
            'navbarColor' => array('bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'),
            'horizontalMenuType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'),
            'horizontalMenuClass' => array('static' => 'menu-static', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'),
            'verticalMenuNavbarType' => array('floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'),
            'navbarClass' => array('floating' => 'floating-nav', 'static' => 'static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'),
            'footerType' => array('static' => 'footer-static', 'sticky' => 'fixed-footer', 'hidden' => 'footer-hidden'),
            'pageHeader' => array(true, false),
            'contentLayout' => array('default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'),
            'blankPage' => array(false, true),
            'sidebarPositionClass' => array('content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'),
            'contentsidebarClass' => array('content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'),
            'direction' => array('ltr', 'rtl'),
        ];

        //if mainLayoutType value empty or not match with default options in custom.php config file then set a default value
        foreach ($allOptions as $key => $value) {
            if (array_key_exists($key, $DefaultData)) {
                if (gettype($DefaultData[$key]) === gettype($data[$key])) {
                    // data key should be string
                    if (is_string($data[$key])) {
                        // data key should not be empty
                        if (isset($data[$key]) && $data[$key] !== null) {
                            // data key should not be exist inside allOptions array's sub array
                            if (!array_key_exists($data[$key], $value)) {
                                // ensure that passed value should be match with any of allOptions array value
                                $result = array_search($data[$key], $value, 'strict');
                                if (empty($result) && $result !== 0) {
                                    $data[$key] = $DefaultData[$key];
                                }
                            }
                        } else {
                            // if data key not set or
                            $data[$key] = $DefaultData[$key];
                        }
                    }
                } else {
                    $data[$key] = $DefaultData[$key];
                }
            }
        }

        //layout classes
        $layoutClasses = [
            'theme' => $data['theme'],
            'layoutTheme' => $allOptions['theme'][$data['theme']],
            'sidebarCollapsed' => $data['sidebarCollapsed'],
            'verticalMenuNavbarType' => $allOptions['verticalMenuNavbarType'][$data['verticalMenuNavbarType']],
            'navbarClass' => $allOptions['navbarClass'][$data['verticalMenuNavbarType']],
            'navbarColor' => $data['navbarColor'],
            'horizontalMenuType' => $allOptions['horizontalMenuType'][$data['horizontalMenuType']],
            'horizontalMenuClass' => $allOptions['horizontalMenuClass'][$data['horizontalMenuType']],
            'footerType' => $allOptions['footerType'][$data['footerType']],
            'sidebarClass' => 'menu-expanded',
            'bodyClass' => $data['bodyClass'],
            'pageHeader' => $data['pageHeader'],
            'blankPage' => $data['blankPage'],
            'blankPageClass' => '',
            'contentLayout' => $data['contentLayout'],
            'sidebarPositionClass' => $allOptions['sidebarPositionClass'][$data['contentLayout']],
            'contentsidebarClass' => $allOptions['contentsidebarClass'][$data['contentLayout']],
            'mainLayoutType' => $data['mainLayoutType'],
            'direction' => $data['direction'],
        ];

        // sidebar Collapsed
        if ($layoutClasses['sidebarCollapsed'] == 'true') {
            $layoutClasses['sidebarClass'] = "menu-collapsed";
        }

        // blank page class
        if ($layoutClasses['blankPage'] == 'true') {
            $layoutClasses['blankPageClass'] = "blank-page";
        }

        return $layoutClasses;
    }

    public static function updatePageConfig($pageConfigs)
    {
        $demo = 'custom';
        if (isset($pageConfigs)) {
            if (count($pageConfigs) > 0) {
                foreach ($pageConfigs as $config => $val) {
                    Config::set('custom.' . $demo . '.' . $config, $val);
                }
            }
        }
    }

    public static function RetPhotoUser($FileName)
    {
        if (file_exists( public_path() . 'storage/contact' . $FileName)) {
            return public_path() . 'storage/contact' . $FileName;
        } else {
            return '/storage/contact/default.png';
        }
    }

    public static function changeDatetimeFormat($Datetime)
    {
        return date('d-m-Y / H:i', strtotime($Datetime));
        //return date('d-m-Y / H:i:s', strtotime($Datetime));
    }

    public static function size_as_kb($size)
    {
        if ($size < 1024) {
            return "{$size} bytes";
        } elseif ($size < 1048576) {
            $size_kb = round($size/1024);
            return "{$size_kb} KB";
        } else {
            $size_mb = round($size/1048576, 1);
            return "{$size_mb} MB";
        }
    }

    public static function idCode($id)
    {
        $beforeNum=mt_rand(1000, 9999);
        $afterNum=mt_rand(1000, 9999);
        $idCode=$beforeNum.$id.$afterNum;
        return ($idCode*7);
    }
    public static function idDecode($idCode)
    {
        $idDecode=$idCode/7;
        $idDecode=substr($idDecode, 0, -4);
        $idDecode=substr($idDecode,  4);
        return $idDecode;
    }
    public static function getCM_DropDown_list($status='0')
    {
        $adminAuth = \Auth::guard('admin')->user();
        if($status=='0')//All
            return \App\Models\Admin::where('company_id',$adminAuth->company_id)->whereNotIn('type', [6,7,8])->orderBy('firstname','ASC')->get();

        if($status!='0')
            return \App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status',$status)->whereNotIn('type', [6,7,8])->orderBy('firstname','ASC')->get();
    }
}
