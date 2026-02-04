<?php

namespace App\Http\Controllers;

use App\Models\Emirate;
use App\Models\MasterProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MasterProjectController extends Controller
{
    // Admin - Table
    public function MasterProjects(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $MasterProjects=MasterProject::with('Emirate')->get();
        $Emirates=Emirate::get();
        return view('/admin/master-project', [
            'pageConfigs' => $pageConfigs,
            'MasterProjects'=>$MasterProjects,
            'Emirates'=>$Emirates
        ]);
    }
    public function GetEmirateAjax(){
        $MasterProjects=MasterProject::where('emirate_id',request('Emirate'))->orderBy('name','ASC')->get();

        /*$url=env('MD_URL').'/api/master-projects';
        $token='';env('MD_TOKEN');
        $data=[
            'emirate_id'=>request('Emirate')
            ];
        $response=Http::withBody(json_encode($data),'application/json')->withToken($token)->post($url);
        $MasterProjects=json_decode($response);*/
        $output='<option value="">select</option>';
        foreach ($MasterProjects as $row){
            $output.='<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo  $output;
    }
    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        MasterProject::create([
            'emirate_id'=>request('Emirate'),
            'name'=>request('name'),
        ]);
        return redirect('/admin/master-project');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $MasterProject = MasterProject::find(request('update'));
        $MasterProject->emirate_id = request('Emirate');
        $MasterProject->name = request('name');
        $MasterProject->save();
        return redirect('/admin/master-project');
    }

    public function Delete(){
        $MasterProject = MasterProject::find( request('Delete') );
        $MasterProject->delete();
        return redirect('/admin/master-project');
    }

}

