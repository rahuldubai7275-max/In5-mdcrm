<?php

namespace App\Http\Controllers;

use App\Models\FBForm;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AppController extends Controller
{
    public function install(){

        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/app-install', [
            'pageConfigs' => $pageConfigs
        ]);
    }


}
