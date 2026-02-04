<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyAnswerController extends Controller
{
    public function surveys(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/surveys', [
            'pageConfigs' => $pageConfigs,
        ]);
    }



}
