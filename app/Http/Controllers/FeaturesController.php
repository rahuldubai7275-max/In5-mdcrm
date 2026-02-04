<?php

namespace App\Http\Controllers;

use App\Models\Features;
use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    // Admin - Table
    public function Features(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Features=Features::get();
        return view('/admin/building-tower', [
            'pageConfigs' => $pageConfigs,
            'Features'=>$Features
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Features::create([
            'type'=>request('type'),
            'name'=>request('name'),
        ]);
        return redirect('/admin/building-tower');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Features = Features::find(request('update'));
        $Features->type = request('type');
        $Features->name = request('name');
        $Features->save();
        return redirect('/admin/building-tower');
    }

    public function Delete(){
        $Features = Features::find( request('Delete') );
        $Features->delete();
        return redirect('/admin/building-tower');
    }

}
