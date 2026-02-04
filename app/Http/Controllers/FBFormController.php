<?php

namespace App\Http\Controllers;

use App\Models\FBForm;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class FBFormController extends Controller
{
    public function campaigns(){

        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/campaigns', [
            'pageConfigs' => $pageConfigs
        ]);
    }

    public function insertForm(){
        $token='EAAEZC1fQ1QgUBO0Bu6TARGeIwcq5pCHaXFl1pc4fZBzVv85zGRyZAq7NV8T1ytTrEq6lrAnjQlttKglA9tzhyEFRo9NcKOeQ1M8TfKKIQZA8Ca7lmAlW0EC9WMdsjzWSECsZCjI25vlDbWZBpI5zwEcg20NMpOB2iVks5l2gXM4CTe5q5UKVwhN7YRKU5HYUUZCzIt3gob16zfJCy0ZD';
        $url='https://graph.facebook.com/v20.0/106359828114712/leadgen_forms?access_token='.$token;

        $response = Http::get($url);

        $response=json_decode($response);

        $fb_leads=$response->data;
        foreach ($fb_leads as $row){
            $check=FBForm::where('status',$row->id)->first();
            if(!$check){
                FBForm::create([
                    'form_id'=>$row->id,
                    'name'=>$row->name,
                    'status'=>$row->status,
                ]);
            }
        }
    }

}
