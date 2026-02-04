@php
    $adminAuth = Auth::guard('admin')->user();
    $task_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','all_task')->first();
    $task_access_admin=[];
    if($task_access_setting)
        $task_access_admin=\App\Models\SettingAdmin::where('setting_id',$task_access_setting->id)->where('admin_id',$adminAuth->id)->first();
@endphp
<div class="col-md-4 col-12 order-1 order-sm-12" style="padding-bottom: 2.2rem;">
    <div class="card h-100 mb-0">
        <div class="card-header">
            <h4 class="card-title">Daily Tasks</h4>
            @php
                $task_access=0;
                if($task_access_setting && ($task_access_setting->status==1 || $task_access_admin) )
                    $task_access=1;
            @endphp

            @if($task_access==1 || $adminAuth->super==1) <a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights question-create-btn" href="#modalCreate" data-toggle="modal">Add</a> @endif

        </div>
        <div class="card-content">
            <div class="card-body">
                <div class="clearfix d-flex align-items-center">
                    <div class="task-day-previous cursor-pointer font-medium-5"><i class="feather icon-chevron-left"></i></div>
                    <div class="task-day-box">
                        <ul class="d-flex task-day-list">

                        </ul>
                    </div>
                    <div class="task-day-next cursor-pointer font-medium-5"><i class="feather icon-chevron-right"></i></div>
                </div>
                <div class="task-list-box custom-scrollbar pt-1" style="height: {{ ($adminAuth->type==1) ? '452' : '390' }}px;">

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="taskCancelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <form method="post" action="{{route('task.status')}}" novalidate class="modal-content">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Close</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row pt-2">
                    <div class="col-12">
                        <div class="form-group form-label-group">
                            <input type="text" class="form-control" id="cancel_reason" name="reason" placeholder="Reason" required>
                            <label>Reason</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="task_cancel_id" name="_id">
                <input type="hidden" id="task_cancel_status" name="status" value="2">
                <button type="submit" class="btn btn-danger">Submit</button>
            </div>
        </form>
    </div>
</div>
