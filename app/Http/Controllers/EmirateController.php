<?php

namespace App\Http\Controllers;

use App\Models\Emirate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmirateController extends Controller
{
    public function Emirates(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        $Emirates=Emirate::get();
        return view('/admin/emirates', [
            'pageConfigs' => $pageConfigs,
            'Emirates'=>$Emirates
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);
        $path="";
        if ($request->file('PicName')) {
            $imagePath = $request->file('PicName');

            $path = $request->file('PicName')->store('public/images');
        }

        if( $path ){
            $imageName=explode("/",$path);
            $imageName=end ($imageName);

            $Data=[
                'name'=>request('name'),
                'picname'=>$imageName
            ];
        }else{
            $Data=[
                'name'=>request('name'),
            ];
        }
        Emirate::create($Data);
        return redirect('/admin/emirates');
    }

    public function Edit(Request $request){
        $request->validate([
            'name'=>'required|string',
        ]);

        $Emirate = Emirate::find(request('update'));
        $path="";
        if ($request->file('PicName')) {
            $imagePath = $request->file('PicName');

            $path = $request->file('PicName')->store('public/images');
        }

        if( $path ){
            $imageName=explode("/",$path);
            $imageName=end ($imageName);

            Storage::delete('public/images/'.$Emirate->picname);
            $Emirate->picname = $imageName;
        }

        $Emirate->name = request('name');
        $Emirate->save();
        return redirect('/admin/emirates');
    }

    public function Delete(){
        $Emirate = Emirate::find( request('Delete') );
        Storage::delete('public/images/'.$Emirate->picname);
        $Emirate->delete();
        return redirect('/admin/emirates');
    }

}
