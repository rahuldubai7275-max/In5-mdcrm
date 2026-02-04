<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function Bank(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/banks', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        Bank::create([
            'name'=>request('name'),
        ]);
        return redirect('/banks');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $Bank = Bank::find(request('update'));
        $Bank->name = request('name');
        $Bank->save();
        return redirect('/banks');
    }

    public function Delete(){
        $Bank = Bank::find( request('Delete') );
        $Bank->delete();
        return redirect('/banks');
    }

}
