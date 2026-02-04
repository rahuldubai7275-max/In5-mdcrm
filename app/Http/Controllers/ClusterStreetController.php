<?php

namespace App\Http\Controllers;

use App\Models\ClusterStreet;
use App\Models\Community;
use Illuminate\Http\Request;

class ClusterStreetController extends Controller
{
    public function ClusterStreets(){
        $pageConfigs = [
            'pageHeader' => false
        ];

        if(request('status')=='0')
            $ClusterStreets=ClusterStreet::where('status','0')->with('Community')->get();
        else
            $ClusterStreets=ClusterStreet::with('Community')->get();

        $Communitys=Community::get();
        return view('/admin/cluster-street', [
            'pageConfigs' => $pageConfigs,
            'ClusterStreets'=>$ClusterStreets,
            'Communitys'=>$Communitys
        ]);
    }
    public function GetCommunityAjax(){
        $ClusterStreets=ClusterStreet::where('community_id',request('Community'))->get();
        $output='<option value="">select</option>';
        $output.='<option value="0">N/A</option>';
        foreach ($ClusterStreets as $row){
            $output.='<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo  $output;
    }
    public function Store(Request $request){
        $request->validate([
            'Community'=>'required',
            'name'=>'required|string',
        ]);
        $community_id=request('Community');
        $name=request('name');
        $ClusterStreetChack=ClusterStreet::where('community_id',$community_id)->where('name',$name)->first();
        if(!$ClusterStreetChack) {
            ClusterStreet::create([
                'community_id' => $community_id,
                'name' => $name
            ]);
        }
        return redirect('/admin/cluster-street');
    }

    public function StoreAjax(Request $request){
        $request->validate([
            'community'=>'required',
            'name'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();

        $community_id=request('community');
        $name=request('name');
        $inserted=ClusterStreet::where('community_id',$community_id)->where('name',$name)->first();
        if(!$inserted) {
            $inserted = ClusterStreet::create([
                'admin_id' => $adminAuth->id,
                'community_id' => $community_id,
                'name' => $name,
                'status' => '0',
            ]);
        }

        $clusterStreet=ClusterStreet::where('community_id',$community_id)->get();

        $output='<option value="">select</option>';
        foreach($clusterStreet as $cs){
            $output.='<option value="'.$cs->id.'">'.$cs->name.'</option>';
        }

        return ['options'=>$output,'selected'=>$inserted->id];
    }

    public function Edit(Request $request){
        $request->validate([
            'Community'=>'required',
            'name'=>'required|string',
        ]);
        $ClusterStreet = ClusterStreet::find(request('update'));
        $beforeStatus=$ClusterStreet->status;
        $ClusterStreet->community_id = request('Community');
        $ClusterStreet->name = request('name');
        $ClusterStreet->status='1';
        $ClusterStreet->save();

        if($beforeStatus==1)
            return redirect('/admin/cluster-street');
        else
            return redirect('/admin/cluster-street?status=0');
    }

    public function confirm(){
        $ClusterStreet = ClusterStreet::find( request('confirm') );
        $ClusterStreet->status='1';
        $ClusterStreet->save();
        return redirect('/admin/cluster-street?status=0');
    }

    public function Delete(){
        $ClusterStreet = ClusterStreet::find( request('Delete') );
        $beforeStatus=$ClusterStreet->status;
        $ClusterStreet->delete();

        if($beforeStatus==1)
            return redirect('/admin/cluster-street');
        else
            return redirect('/admin/cluster-street?status=0');
    }

}
