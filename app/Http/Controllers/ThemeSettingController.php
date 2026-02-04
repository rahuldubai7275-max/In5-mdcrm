<?php

namespace App\Http\Controllers;

use App\Models\ThemeSetting;
use Illuminate\Http\Request;

class ThemeSettingController extends Controller
{

    public function Edit(Request $request){
        $request->validate([
            'MenuColor'=>'required|string',
        ]);

        $adminAuth=\Auth::guard('admin')->user();

        $ThemeSetting = ThemeSetting::where('admin_id',$adminAuth->id)->first();

        if(!$ThemeSetting){
            ThemeSetting::create([
                'admin_id'=>$adminAuth->id,
                'menu_color'=>request('MenuColor'),
                'theme_layout'=>request('ThemeLayout'),
                'collapse_sidebar'=>request('CollapseSidebar'),
                'navbar_color'=>request('NavbarColor'),
                'navbar_type'=>request('NavbarType'),
                'footer_type'=>request('FooterType')
            ]);
        }else {
            if(request('MenuColor'))
                $ThemeSetting->menu_color = request('MenuColor');

            if(request('ThemeLayout'))
                $ThemeSetting->theme_layout = request('ThemeLayout');

            $ThemeSetting->collapse_sidebar = request('CollapseSidebar');

//            if(request('NavbarColor'))
                $ThemeSetting->navbar_color = request('NavbarColor');

            if(request('NavbarType'))
                $ThemeSetting->navbar_type = request('NavbarType');

            if(request('FooterType'))
                $ThemeSetting->footer_type = request('FooterType');

            $ThemeSetting->save();

            return json_encode($request->all());
        }
    }
}
