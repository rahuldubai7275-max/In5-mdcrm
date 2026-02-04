<?php

namespace App\Http\Controllers;

use App\Models\VillaType;
use App\Models\Community;
use Illuminate\Http\Request;

class VillaTypeController extends Controller
{

    public function VillaTypes(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        if(request('status')=='0')
            $VillaTypes=VillaType::where('status','0')->with('Community')->get();
        else
            $VillaTypes=VillaType::with('Community')->get();
        $Communitys=Community::get();

        return view('/admin/villa-type', [
            'pageConfigs' => $pageConfigs,
            'VillaTypes'=>$VillaTypes,
            'Communitys'=>$Communitys
        ]);
    }
    public function GetCommunityAjax(){
        $VillaType=VillaType::where('community_id',request('Community'))->get();
        $output='<option value="">select</option>';
        $output.='<option value="0">N/A</option>';
        foreach ($VillaType as $row){
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
        $VillaTypeChack=VillaType::where('community_id',$community_id)->where('name',$name)->first();
        if(!$VillaTypeChack) {
            VillaType::create([
                'community_id' => $community_id,
                'name' => $name,
            ]);
        }
        return redirect('/admin/type');
    }

    public function StoreAjax(Request $request){
        $request->validate([
            'community'=>'required',
            'name'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        $community_id=request('community');
        $name=request('name');
        $inserted=VillaType::where('community_id',$community_id)->where('name',$name)->first();
        if(!$inserted) {
            $inserted = VillaType::create([
                'admin_id' => $adminAuth->id,
                'community_id' => $community_id,
                'name' => $name,
                'status' => '0',
            ]);
        }

        $villaType=VillaType::where('community_id',$community_id)->get();

        $output='<option value="">select</option>';
        foreach($villaType as $vtype){
            $output.='<option value="'.$vtype->id.'">'.$vtype->name.'</option>';
        }

        return ['options'=>$output,'selected'=>$inserted->id];
    }

    public function Edit(Request $request){
        $request->validate([
            'Community'=>'required',
            'name'=>'required|string',
        ]);
        $VillaType = VillaType::find(request('update'));
        $beforeStatus=$VillaType->status;
        $VillaType->community_id = request('Community');
        $VillaType->name = request('name');
        $VillaType->status='1';
        $VillaType->save();

        if($beforeStatus==1)
            return redirect('/admin/type');
        else
            return redirect('/admin/type?status=0');
    }

    public function confirm(){
        $VillaType = VillaType::find( request('confirm') );
        $VillaType->status='1';
        $VillaType->save();
        return redirect('/admin/type?status=0');
    }

    public function Delete(){
        $VillaType = VillaType::find( request('Delete') );
        $beforeStatus=$VillaType->status;
        $VillaType->delete();
        if($beforeStatus==1)
            return redirect('/admin/type');
        else
            return redirect('/admin/type?status=0');
    }

}

