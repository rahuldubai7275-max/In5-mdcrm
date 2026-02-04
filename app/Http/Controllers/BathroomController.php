<?php

namespace App\Http\Controllers;

use App\Models\Bathroom;
use Illuminate\Http\Request;

class BathroomController extends Controller
{
    // Admin - Table
    public function Bathrooms(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Bathroom=Bathroom::get();
        return view('/admin/bathrooms', [
            'pageConfigs' => $pageConfigs,
            'Bathrooms'=>$Bathroom
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Bathroom::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/bathrooms');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Bathroom = Bathroom::find(request('update'));
        $Bathroom->name = request('name');
        $Bathroom->save();
        return redirect('/admin/bathrooms');
    }

    public function Delete(){
        $Bathroom = Bathroom::find( request('Delete') );
        $Bathroom->delete();
        return redirect('/admin/bathrooms');
    }

}
