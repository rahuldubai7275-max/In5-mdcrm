<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\MasterProject;
use App\Models\Property;
use Illuminate\Http\Request;

class IndexController extends Controller
{

    public function index(){
        return redirect('/admin/login/');
//        $pageConfigs = [
//            'pageHeader' => false
//        ];
//        $Properties=Property::where('featured','Yes')->get();
//        return view('/site/index', [
//            'pageConfigs' => $pageConfigs,
//            'Properties'=>$Properties
//        ]);
    }

}

