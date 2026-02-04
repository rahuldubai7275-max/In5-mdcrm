<?php

namespace App\Http\Controllers;

use App\Models\BusinessTiming;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BusinessTimingController extends Controller
{
    // Admin - Table
    public function businessTiming(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $businessTimings=BusinessTiming::get();
        return view('/admin/business-timings', [
            'pageConfigs' => $pageConfigs,
            'businessTimings'=>$businessTimings
        ]);
    }

    public function edit(Request $request){
//        BusinessTiming::query()->updated(['status'=>0,'from_time'=>null,'to_time'=>null]);
        DB::select("UPDATE `business_timings` SET `status`=0,`from_time`=null,`to_time`=null WHERE 1");
        foreach (request('day') as $id) {
            $BTiming = BusinessTiming::find($id);

            $BTiming->status = 1;
            $BTiming->from_time = request('from_time'.$id);
            $BTiming->to_time = request('to_time'.$id);
            $BTiming->save();
        }
        return redirect('/admin/business-timings');
    }

}
