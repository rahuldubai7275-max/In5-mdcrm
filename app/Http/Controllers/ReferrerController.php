<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Referrer;
use Illuminate\Http\Request;

class ReferrerController extends Controller
{
    public function referrers()
    {
        $pageConfigs = [
            'pageHeader' => false
        ];

        return view('/admin/referrer', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);
        $adminAuth = \Auth::guard('admin')->user();

        Referrer::create([
            'admin_id' => $adminAuth->id,
            'name'=>request('name'),
            'email'=>request('email'),
            'phone_number'=>request('phone_number'),
            'country'=>request('country'),
            'city'=>request('city'),
        ]);

        return redirect('/admin/referrers');
    }

    public function Edit(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $referrer = Referrer::find(request('update'));
        $referrer->name = request('name');
        $referrer->email = request('email');
        $referrer->phone_number = request('phone_number');
        $referrer->country = request('country');
        $referrer->city = request('city');

        $referrer->save();


        return redirect('/admin/referrers');
    }

    public function Delete()
    {
        $Referrer = Referrer::find(request('Delete'));

        $adminAuth = \Auth::guard('admin')->user();
        if($Referrer->admin_id==$adminAuth->id)
            $Referrer->delete();
        return redirect('/admin/referrers');
    }
}
