<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Task;
use App\Models\TaskTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{
    public function Tasks(){
        $pageConfigs = [
            'pageHeader' => false
        ];
        return view('/admin/tasks', [
            'pageConfigs' => $pageConfigs,
        ]);
    }

    public function Store(Request $request){
        $request->validate([
            'date_at'=>'required'
        ]);

        $adminAuth=\Auth::guard('admin')->user();
        $title=request('title');
        $task_title_id=null;
        if($title) {
            $taskTitle = TaskTitle::find($title);
            $task_title_id=$taskTitle->id;
        }
        foreach (request('assign_to') as $admin_id) {
            Task::create([
                'company_id' => $adminAuth->company_id,
                'admin_id' => $adminAuth->id,
                'assign_to' => $admin_id,
                'task_title_id' => $task_title_id,
                'description' => request('description'),
                'date_at' => request('date_at'),
                'time_at' => request('time_at')
            ]);
        }

        return Redirect::back();
        //return redirect('/admin/tasks');
    }

    public function edit(Request $request){
        $request->validate([
            '_id'=>'required',
            'date_at'=>'required'
        ]);
        $id=request('_id');

        $title=request('title');
        $task_title_id=null;
        if($title) {
            $taskTitle = TaskTitle::find($title);
            $task_title_id=$taskTitle->id;
        }

        $Task = Task::find($id);

        $adminAuth = \Auth::guard('admin')->user();
        if(!$Task || $Task->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $Task->assign_to=request('_assign_to');
        $Task->task_title_id=$task_title_id;
        $Task->description=request('description');
        $Task->date_at=request('date_at');
        $Task->time_at=request('time_at');
        $Task->save();
        return Redirect::back();
    }

    public function action(Request $request){
        $request->validate([
            '_id'=>'required',
            'status'=>'required'
        ]);

        $task=Task::find(request('_id'));

        $adminAuth = \Auth::guard('admin')->user();
        if(!$task || $task->company_id!=$adminAuth->company_id){
            return abort(404);
        }

        $task->status=request('status');
        $task->reason=request('reason');
        $task->save();

        return redirect('/admin');
    }

    public function getTasks()
    {
        ## Read value
        $draw = request('draw');
        $start = request('start');
        $rowperpage = request('length'); // Rows display per page
        $columnIndex = request('order')[0]['column']; // Column index
        $columnName = request('columns')[$columnIndex]['data']; // Column name
        $columnSortOrder = request('order')[0]['dir']; // asc or desc
        $searchValue = request('search')['value']; // Search value



        // $dataWhere=[];
        $where = '';

        $orderBy=' ORDER BY date_at DESC, time_at DESC';
        if($columnIndex){
            $orderBy=" ORDER BY ".$columnName." ".$columnSortOrder;

            if($columnName=='date_at'){
                $orderBy=' ORDER BY date_at DESC, time_at DESC';
            }
        }

        $now=date('Y-m-d H:i:s');
        $adminAuth=\Auth::guard('admin')->user();
        $admin_id=$adminAuth->id;
        if(request('admin')!=''){
            $admin_id=request('admin');
        }
        if(request('assign_to')=='' && request('admin')=='') {
            $where .= ' AND (admin_id="' . $admin_id . '" OR assign_to="' . $adminAuth->id . '") ';
        }else {

            if (request('assign_to'))
                $where .= ' AND assign_to="' . request('assign_to') . '"';

            if (request('admin'))
                $where .= ' AND admin_id="' . request('admin') . '"';

        }
        $totalRecords = DB::select("SELECT COUNT(*) as countAll FROM `tasks` WHERE company_id=".$adminAuth->company_id." ".$where);
        $totalRecords = $totalRecords[0]->countAll;

        if( request('from_date') )
            $where.=' AND date_at >="'.request('from_date').'"';

        if( request('to_date') )
            $where.=' AND date_at <="'.request('to_date').'"';

        if( request('create_from_date') ){
            $where.=' AND created_at >="'.request('create_from_date').' 00:00:00"';
        }

        if( request('create_to_date') ) {
            $where .= ' AND created_at <="' . request('create_to_date') . ' 23:59:59"';
        }

        if( request('status') || request('status')=='0' ) {
            if( request('status')=='3' ) {
                $where.=' AND concat(date_at," ",IF(time_at!=NULL, "time_at", "00:00:00") ) >="'.$now.'"';
            }else {
                $where .= ' AND status="' . request('status') . '"';
            }
        }

        if( request('task') )
            $where.=' AND task_title_id="'.request('task').'"';

        $totalRecordwithFilter=DB::select("SELECT COUNT(*) as countAll FROM tasks WHERE company_id=".$adminAuth->company_id." ".$where);
        $totalRecordwithFilter=$totalRecordwithFilter[0]->countAll;
        $data = array();

        #record number with filter
        if($rowperpage=='-1'){
            $Records=DB::select("SELECT * FROM tasks WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy);
        }else{
            $Records=DB::select("SELECT * FROM tasks WHERE company_id=".$adminAuth->company_id." ".$where.$orderBy." limit ".$start.",".$rowperpage);
        }

        $obj=[];
        foreach($Records as $row){
            $admin=Admin::find($row->admin_id);
            $assign_to=Admin::find($row->assign_to);

            $title=\Illuminate\Support\Str::limit(strip_tags($row->description),50);
            $taskTitle=TaskTitle::find($row->task_title_id);
            if($row->task_title_id){
                $taskTitle=TaskTitle::where('id',$row->task_title_id)->first();
                $title=$taskTitle->title;
            }
            $obj['admin_id']=$admin->firstname.' '.$admin->lastname;
            $obj['assign_to']=($assign_to) ? $assign_to->firstname.' '.$assign_to->lastname : '';
            $obj['task_title_id']='<span class="task-desc" data-target="#ViewModal" data-toggle="modal" data-title="'.$title.'" data-desc="'.$row->description.'">'.(($taskTitle) ? $taskTitle->title : $row->description).'</span>';
            $obj['date_at']=($row->time_at)? \Helper::changeDatetimeFormat($row->date_at.' '.$row->time_at) : date('d-m-y',strtotime($row->date_at));
            $obj['created_at']=\Helper::changeDatetimeFormat($row->created_at);

            $status='';
            $action='';
            if($row->status==2){
                $status='<span class="badge badge-pill badge-light-danger w-100 task-desc" data-target="#ViewModal" data-toggle="modal" data-title="Cancelled Reason" data-desc="'.$row->reason.'">Cancelled</span>';
            }else{
                $datetime = $row->date_at . ' ' . $row->time_at;
                if (strtotime($datetime) > strtotime($now)) {
                    $status = '<span class="badge badge-pill badge-light-success w-100">Upcoming</span>';
                    $action='<a href="#modalCreate" data-toggle="modal" class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>';
                }
                if ($row->status==1) {
                    $status = '<span class="badge badge-pill badge-light-primary w-100">Done</span>';
                }
            }
            if($row->status==0){
                $action.='<a href="javascript:void(0);" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>';
            }
            $obj['status']=$status;

            $obj['action']='<div class="action font-medium-3 d-flex" data-id="'.$row->id.'" data-model="'.route('task.delete').'"
                            data-title="'.$row->task_title_id.'"
                            data-cm="'.$row->assign_to.'"
                            data-desc="'.$row->description.'"
                            data-date="'.$row->date_at.'"
                            data-time="'.$row->time_at.'"
                            >
                                '.$action.'
                            </div>';

            $data[] = $obj;
            $obj=[];
        }
        ## Response
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        );

        return json_encode($response);
    }

    public function ajaxDayTasks(){
        $adminAuth=\Auth::guard('admin')->user();
        $Tasks=Task::where('assign_to',$adminAuth->id)->where('date_at',request('date'))->get();

        $output='';
        foreach($Tasks as $row) {
            $taskAdmin = Admin::find($row->admin_id);
            $taskTitle = TaskTitle::find($row->task_title_id);
            $typeColor = '#ffdbba';
            $typeName = '';
            if ($taskTitle) {
                $type = Task_Type[$taskTitle->type];
                $typeName = $type[0];
                $typeColor = $type[1];
            }
            $time_at_t = '';
            if ($row->time_at) {
                $time_at = explode(':', $row->time_at);
                $time_at_t = '<span><i class="feather icon-clock font-medium-1" style="margin-right:5px"></i>' . $time_at[0] . ':' . $time_at[1] . '</span>';
            }
            $action = '';
            $statusIcon = '';
            if ($row->status == '0') {
                $action = '<a href="#taskCancelModal" data-toggle="modal" class="btn btn-danger task-cancel mr-1">Cancel</a>';

                if(($row->date_at.' '.$row->time_at) <= date('Y-m-d H:i:s')) {
                    $action .= '<a href="javascript:void(0);" class="btn btn-primary task-status" data-type="1">Done</a>';
                }
            }else{
                if ($row->status==2) {
                    $statusIcon='<i class="feather icon-x-circle mr-1 text-danger"></i>';
                }
                if ($row->status==1) {
                    $statusIcon='<i class="feather icon-check-circle mr-1 text-success"></i>';
                }
            }
            $title=\Illuminate\Support\Str::limit(strip_tags($row->description),50);
            $taskTitle=TaskTitle::find($row->task_title_id);
            if($row->task_title_id){
                $taskTitle=TaskTitle::where('id',$row->task_title_id)->first();
                $title=$taskTitle->title;
            }

            $output .= '<div class="task mb-2" style="background-color: ' . $typeColor . '">
                            <div class="clearfix"><span class="float-left"><i class="feather icon-user mr-1"></i>' . (($taskAdmin->id == $adminAuth->id) ? 'Own' : $taskAdmin->firstname . ' ' . $taskAdmin->lastname) . '</span><span class="float-right border-2 ml-1">'.$typeName.'</span></div>
                            <h6>' .$statusIcon. $title . '</h6>
                            <p class="m-0">' . $row->description . '</p>


                            <div class="clearfix mt-1">
                                <div class="float-left">
                                    <span>' . $time_at_t . '</span>
                                </div>
                                    <div class="float-right" data-id="' . $row->id . '" data-model="' . route('task.status') . '">
                                        ' . $action . '
                                    </div>
                            </div>
                        </div>';
        }
        return $output;
    }

    public function delete(){
        $Task = Task::find( request('Delete') );
        $adminAuth = \Auth::guard('admin')->user();
        if(!$Task || $Task->company_id!=$adminAuth->company_id){
            return abort(404);
        }
        $Task->delete();
        return redirect('/admin/tasks');
    }
}
