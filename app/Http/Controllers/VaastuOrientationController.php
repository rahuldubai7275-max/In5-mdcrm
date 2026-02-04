<?php

namespace App\Http\Controllers;

use App\Models\VaastuOrientation;
use Illuminate\Http\Request;

class VaastuOrientationController extends Controller
{
    // Admin - Table
    public function VaastuOrientations(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $VaastuOrientations=VaastuOrientation::get();
        return view('/admin/vaastu-orientation', [
            'pageConfigs' => $pageConfigs,
            'VaastuOrientations'=>$VaastuOrientations
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        VaastuOrientation::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/vaastu-orientation');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $VaastuOrientation = VaastuOrientation::find(request('update'));
        $VaastuOrientation->name = request('name');
        $VaastuOrientation->save();
        return redirect('/admin/vaastu-orientation');
    }

    public function Delete(){
        $VaastuOrientation = VaastuOrientation::find( request('Delete') );
        $VaastuOrientation->delete();
        return redirect('/admin/vaastu-orientation');
    }

}
