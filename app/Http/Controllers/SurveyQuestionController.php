<?php

namespace App\Http\Controllers;

use App\Models\SurveyAnswer;
use App\Models\SurveyQuestion;
use Illuminate\Http\Request;

class SurveyQuestionController extends Controller
{
    public function surveyQuestions(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/survey-question', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'subject'=>'required',
            'question'=>'required|string',
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        SurveyQuestion::create([
            'company_id'=>$adminAuth->company_id,
            'subject'=>request('subject'),
            'question'=>request('question'),
        ]);
        return redirect('/admin/survey-question');
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'question'=>'required|string',
        ]);
        $id=request('_id');

        $SurveyQuestion = SurveyQuestion::find($id);

        $SurveyAnswer=SurveyAnswer::where('survey_question_id',$id)->count();

        if($SurveyAnswer==0)
            $SurveyQuestion->subject = request('subject');

        $SurveyQuestion->question = request('question');
        $SurveyQuestion->save();
        return redirect('/admin/survey-question');
    }

    public function statusAction(Request $request){
        $request->validate([
            'question'=>'required|string',
            'status'=>'required|string',
        ]);
        $SurveyQuestion = SurveyQuestion::find(request('question'));
        $SurveyQuestion->status = request('status');
        $SurveyQuestion->save();
    }

    public function delete(){
        $SurveyQuestion = SurveyQuestion::find( request('Delete') );
        $SurveyAnswer = SurveyAnswer::where( 'survey_question_id',$SurveyQuestion->id )->count();

        if($SurveyAnswer>0){
            return redirect('/admin/survey-question');
        }

        $SurveyQuestion->delete();
        return redirect('/admin/survey-question');
    }

}
