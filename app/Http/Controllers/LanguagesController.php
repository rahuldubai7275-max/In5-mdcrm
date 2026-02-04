<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    public function Languages(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/language', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Language::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/language');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Language = Language::find(request('update'));
        $Language->name = request('name');
        $Language->save();
        return redirect('/admin/language');
    }

    public function Delete(){
        $Language = Language::find( request('Delete') );
        $Language->delete();
        return redirect('/admin/language');
    }

}
