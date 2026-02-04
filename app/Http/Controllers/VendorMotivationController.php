<?php

namespace App\Http\Controllers;

use App\Models\VendorMotivation;
use Illuminate\Http\Request;

class VendorMotivationController extends Controller
{
    // Admin - Table
    public function VendorMotivations(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $VendorMotivations=VendorMotivation::get();
        return view('/admin/vendor-motivation', [
            'pageConfigs' => $pageConfigs,
            'VendorMotivations'=>$VendorMotivations
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        VendorMotivation::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/vendor-motivation');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $VendorMotivation = VendorMotivation::find(request('update'));
        $VendorMotivation->name = request('name');
        $VendorMotivation->save();
        return redirect('/admin/vendor-motivation');
    }

    public function Delete(){
        $VendorMotivation = VendorMotivation::find( request('Delete') );
        $VendorMotivation->delete();
        return redirect('/admin/vendor-motivation');
    }

}

