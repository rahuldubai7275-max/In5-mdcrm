<?php

namespace App\Http\Controllers;

use App\Models\ContactSource;
use Illuminate\Http\Request;

class ContactSourceController extends Controller
{
    // Admin - Table
    public function ContactSources(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $ContactSources=ContactSource::get();
        return view('/admin/contact-source', [
            'pageConfigs' => $pageConfigs,
            'ContactSources'=>$ContactSources
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        ContactSource::create([
            'name'=>request('name'),
        ]);
        return redirect('/admin/contact-source');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $ContactSource = ContactSource::find(request('update'));
        $ContactSource->name = request('name');
        $ContactSource->save();
        return redirect('/admin/contact-source');
    }

    public function Delete(){
        $ContactSource = ContactSource::find( request('Delete') );
        $ContactSource->delete();
        return redirect('/admin/contact-source');
    }

}

