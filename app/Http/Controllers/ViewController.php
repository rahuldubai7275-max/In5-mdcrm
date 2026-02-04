<?php

namespace App\Http\Controllers;

use App\Models\View;
use Illuminate\Http\Request;

class ViewController extends Controller
{
    // Admin - Table
    public function Views(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $View=View::get();
        return view('/admin/views', [
            'pageConfigs' => $pageConfigs,
            'Views'=>$View
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        View::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/views');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $View = View::find(request('update'));
        $View->name = request('name');
        $View->save();
        return redirect('/admin/views');
    }

    public function Delete(){
        $View = View::find( request('Delete') );
        $View->delete();
        return redirect('/admin/views');
    }

}
