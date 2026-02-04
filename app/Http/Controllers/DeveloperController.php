<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use App\Models\MasterProject;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function Developers(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $developers=Developer::get();
        $MasterProjects=MasterProject::get();
        return view('/admin/developer', [
            'pageConfigs' => $pageConfigs,
            'developers'=>$developers,
            'MasterProjects'=>$MasterProjects
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Developer::create([
            'name'=>request('name')
        ]);
        return redirect('/admin/developers');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $developer = Developer::find(request('update'));
        $developer->name = request('name');
        $developer->save();
        return redirect('/admin/developers');
    }

    public function Delete(){
        $developer = Developer::find( request('Delete') );
        $developer->delete();
        return redirect('/admin/developers');
    }

}

