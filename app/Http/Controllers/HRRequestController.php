<?php

namespace App\Http\Controllers;

use App\Models\HRRequest;
use Illuminate\Http\Request;

class HRRequestController extends Controller
{
    public function HRRequests(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/hr-request', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'title'=>'required|string',
        ]);
        HRRequest::create([
            'title'=>request('title'),
        ]);
        return redirect('/admin/hr-request');
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'title'=>'required|string',
        ]);
        $HRRequest = HRRequest::find(request('_id'));
        $HRRequest->title = request('title');
        $HRRequest->save();
        return redirect('/admin/hr-request');
    }


    public function delete(){
        $HRRequest = HRRequest::find( request('Delete') );
        $HRRequest->delete();
        return redirect('/admin/hr-request');
    }

}
