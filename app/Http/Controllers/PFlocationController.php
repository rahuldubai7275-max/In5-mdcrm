<?php

namespace App\Http\Controllers;

use App\Models\Emirate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PFlocationController extends Controller
{
    public function GetAjax(){

        $token_response = Http::withBody(json_encode(['apiKey'=>env('PF_KEY'),'apiSecret'=>env('PF_SECRET')]),'application/json')->
        post('https://atlas.propertyfinder.com/v1/auth/token');
        $token_response= json_decode($token_response);

        $response = Http::withToken($token_response->accessToken)
            ->get('https://atlas.propertyfinder.com/v1/locations?search='.request('q'));
        $response=json_decode($response);
        $data = $response->data;
        $json=[];
        foreach ($data as $row){
            $name=[];
            foreach ($row->tree as $value) {
                $name[]=$value->name;
            }

            $json[] = ['id'=>$row->id, 'address'=>join(' - ',array_reverse($name))];
        }
        return $json;
    }

}
