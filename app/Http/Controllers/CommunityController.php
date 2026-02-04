<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\CommunityParent;
use App\Models\DataCenterAccess;
use App\Models\MasterProject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    // Admin - Table
    public function Communitys(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Communitys=Community::with('MasterProject')->get();
        $MasterProjects=MasterProject::get();
        return view('/admin/community', [
            'pageConfigs' => $pageConfigs,
            'Communitys'=>$Communitys,
            'MasterProjects'=>$MasterProjects
        ]);
    }

    public function GetMasterProjectAjax(){
        $Communitys=Community::where('master_project_id',request('MasterProject'))->orderBy('id','ASC')->get();
        $output='<option value="">select</option>';
        foreach ($Communitys as $row){
            $parent=CommunityParent::where('id',$row->parent_id)->first();
            $output.='<option value="'.$row->id.'">'.$row->name.(($parent) ? ' - '.$parent->name:'').'</option>';
        }
        echo  $output;
    }

    public function GetMasterProjectAjaxDataCenter(){
        $adminAuth=\Auth::guard('admin')->user();
        $projects = DataCenterAccess::where('admin_id', $adminAuth->id)->where('master_project_id', request('MasterProject'))->pluck('projects')->toArray();
        $Communitys=DB::select("SELECT * FROM community WHERE id in (".join(',',$projects).")");
        $output='';
        foreach ($Communitys as $row){
            $parent=CommunityParent::where('id',$row->parent_id)->first();
            $output.='<option value="'.$row->id.'">'.$row->name.(($parent) ? ' - '.$parent->name:'').'</option>';
        }
        echo  $output;
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Community::create([
            'master_project_id'=>request('MasterProject'),
            'name'=>request('name')
        ]);
        return redirect('/admin/community');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Community = Community::find(request('update'));
        $Community->master_project_id = request('MasterProject');
        $Community->name = request('name');
        $Community->save();
        return redirect('/admin/community');
    }

    public function Delete(){
        $Community = Community::find( request('Delete') );
        $Community->delete();
        return redirect('/admin/community');
    }

}

