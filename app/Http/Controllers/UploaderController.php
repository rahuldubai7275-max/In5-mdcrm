<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class UploaderController extends Controller
{
    public function storeImage(Request $request)
    {
        $request->validate([
            'FileFile' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:2048',
        ]);

        if ($request->file('FileFile')) {
            $imagePath = $request->file('FileFile');

            $path = $request->file('FileFile')->store('public/images');
        }
        $imageName=explode("/",$path);
        $imageName=end ($imageName);
        if( $path ){
            $FileInfo=[
                'result'=>'true',
                'name'=>$imageName,
                'link'=>str_replace('public/images','/storage',$path)
            ];
        }else{
            $FileInfo=[
                'result'=>'false',
                'message'=>''
            ];
        }

        return $FileInfo;
    }

    public function storeFile(Request $request)
    {
        $request->validate([
            'DocumentFile' => 'required|mimes:pdf,doc,docx,xlsx,xml,xls,jpg,jpeg,gif,webp,svg,png,mp4|max:31000',
        ]);

        if ($request->file('DocumentFile')) {
            $imagePath = $request->file('DocumentFile');

            $path = $request->file('DocumentFile')->store('public/images');
        }
        $imageName=explode("/",$path);
        $imageName=end ($imageName);
        if( $path ){
            $FileInfo=[
                'result'=>'true',
                'name'=>$imageName,
                'link'=>str_replace('public/images','/storage',$path)
            ];
        }else{
            $FileInfo=[
                'result'=>'false',
                'message'=>''
            ];
        }

        return $FileInfo;
    }

    public function DeleteImage(Request $request){
        Storage::delete('public/images/'.request('FileDelete'));
        return true;
    }
}

