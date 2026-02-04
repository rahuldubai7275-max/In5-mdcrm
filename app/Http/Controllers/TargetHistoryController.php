<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Models\TargetHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TargetHistoryController extends Controller
{
    public function Store(){
        $date= explode('-',date("Y-m-d",strtotime("-1 month")));
        $targets=Target::where('period',1)->get();
        foreach($targets as $row){
            TargetHistory::create([
                'company_id'=>$row->company_id,
                'admin_id'=>$row->admin_id,
                'year'=>$date[0],
                'month'=>$date[1],
                'num_calls'=>$row->num_calls,
                'num_viewing'=>$row->num_viewing,
                'num_ma'=>$row->num_ma,
                'num_listing'=>$row->num_listing,
                'commission'=>$row->commission
            ]);
        }
    }
}
