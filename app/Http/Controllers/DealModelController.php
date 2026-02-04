<?php

namespace App\Http\Controllers;

use App\Models\DealModel;
use Illuminate\Http\Request;

class DealModelController extends Controller
{
    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'email_content'=>'required|string',
        ]);
        $DealModel = DealModel::find(request('_id'));
        $DealModel->email_content = request('email_content');
        $DealModel->save();
    }

    public function getEmailContent(Request $request){
        $request->validate([
            '_id'=>'required',
        ]);
        return $DealModel = DealModel::find(request('_id'));
    }
}
