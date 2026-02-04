<?php

namespace App\Http\Controllers;

use App\Models\Bedroom;
use Illuminate\Http\Request;

class BedroomController extends Controller
{
    // Admin - Table
    public function Bedrooms(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Bedrooms=Bedroom::get();
        return view('/admin/bedrooms', [
            'pageConfigs' => $pageConfigs,
            'Bedrooms'=>$Bedrooms
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Bedroom::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/bedrooms');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Bedroom = Bedroom::find(request('update'));
        $Bedroom->name = request('name');
        $Bedroom->save();
        return redirect('/admin/bedrooms');
    }

    public function Delete(){
        $Bedroom = Bedroom::find( request('Delete') );
        $Bedroom->delete();
        return redirect('/admin/bedrooms');
    }

}
