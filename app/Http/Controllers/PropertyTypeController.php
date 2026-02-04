<?php

namespace App\Http\Controllers;

use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertyTypeController extends Controller
{

    public function PropertyTypes(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $PropertyTypes=PropertyType::get();
        return view('/admin/property-type', [
            'pageConfigs' => $pageConfigs,
            'PropertyTypes'=>$PropertyTypes
        ]);
    }

    public function PropertyTypesAjax(){
        $PropertyTypes=PropertyType::where('type',request('type'))->orderBy('name','ASC')->get();

        $output='<option value="">Select</option>';
        foreach($PropertyTypes as $ptype){
            $output.='<option value="'.$ptype->id.'">'.$ptype->name.'</option>';
        }
        echo $output;
    }



    public function Store(Request $request){
        // return $request;
        $request->validate([
            'type'=>'required',
            'name'=>'required|string',
        ]);
        PropertyType::create([
            'type'=>request('type'),
            'name'=>request('name'),
        ]);
        return redirect('/admin/property-type');
    }

    public function Edit(Request $request){
        $request->validate([
            'type'=>'required',
            'name'=>'required|string',
        ]);
        $PropertyType = PropertyType::find(request('update'));
        $PropertyType->type = request('type');
        $PropertyType->name = request('name');
        $PropertyType->save();
        return redirect('/admin/property-type');
    }

    public function Delete(){
        $PropertyType = PropertyType::find( request('Delete') );
        $PropertyType->delete();
        return redirect('/admin/property-type');
    }

}
