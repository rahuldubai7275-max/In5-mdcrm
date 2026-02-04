<?php

namespace App\Http\Controllers;

use App\Models\SurveyAnswer;
use App\Models\Task;
use App\Models\TaskTitle;
use Illuminate\Http\Request;

class TaskTitleController extends Controller
{
    public function TaskTitles(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/task-title', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'type'=>'required',
            'title'=>'required'
        ]);
        $adminAuth=\Auth::guard('admin')->user();
        TaskTitle::create([
            'company_id'=>$adminAuth->company_id,
            'type'=>request('type'),
            'title'=>request('title')
        ]);
        return redirect('/admin/task-title');
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'type'=>'required',
            'title'=>'required',
        ]);
        $id=request('_id');

        $TaskTitle = TaskTitle::find($id);

        $adminAuth = \Auth::guard('admin')->user();
        if(!$TaskTitle || $TaskTitle->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $TaskTitle->type = request('type');
        $TaskTitle->title = request('title');
        $TaskTitle->save();
        return redirect('/admin/task-title');
    }

    public function statusAction(Request $request){
        $request->validate([
            'question'=>'required|string',
            'status'=>'required|string',
        ]);
        $TaskTitle = TaskTitle::find(request('question'));

        $adminAuth = \Auth::guard('admin')->user();
        if(!$TaskTitle || $TaskTitle->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $TaskTitle->status = request('status');
        $TaskTitle->save();
    }

    public function delete(){
        $TaskTitle = TaskTitle::find( request('Delete') );

        $adminAuth = \Auth::guard('admin')->user();
        if(!$TaskTitle || $TaskTitle->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Task = Task::where( 'task_title_id',$TaskTitle->id )->count();

        if($Task>0){
            return redirect('/admin/task-title');
        }

        $TaskTitle->delete();
        return redirect('/admin/task-title');
    }

}
