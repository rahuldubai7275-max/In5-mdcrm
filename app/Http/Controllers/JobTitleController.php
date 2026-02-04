<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    public function JobTitle(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $possession_or_job=request('poj');
        return view('/admin/job-title', [
            'pageConfigs' => $pageConfigs,
            'possession_or_job' => $possession_or_job,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $possession_or_job=request('possession_or_job');
        JobTitle::create([
            'possession_or_job'=>$possession_or_job,
            'name'=>request('name'),
        ]);
        return redirect('/admin/job-title/'.$possession_or_job);
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $JobTitle = JobTitle::find(request('update'));
        $JobTitle->name = request('name');
        $JobTitle->save();
        return redirect('/admin/job-title/'.$JobTitle->possession_or_job);
    }

    public function Delete(){
        $JobTitle = JobTitle::find( request('Delete') );
        $possession_or_job=$JobTitle->possession_or_job;
        $JobTitle->delete();
        return redirect('/admin/job-title/'.$possession_or_job);
    }

}
