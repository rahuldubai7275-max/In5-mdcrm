@php
    $adminAuth = Auth::guard('admin')->user();
    $task_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','all_task')->first();
    $task_access_admin=[];
    if($task_access_setting)
        $task_access_admin=\App\Models\SettingAdmin::where('setting_id',$task_access_setting->id)->where('admin_id',$adminAuth->id)->first();
@endphp
<div class="modal fade text-left" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <form method="post" action="{{ route('task.add') }}" class="modal-content" novalidate>
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Add Task</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mt-2">
                    @if($task_access_admin || $adminAuth->super==1)
                    <div class="col-12 mx-auto">
                        <div class="form-group form-label-group">
                            <label>Task</label>
                            <select class="custom-select form-control" id="title" name="title">
                                <option value="">Select</option>
                                @php
                                    $taskTitle=\App\Models\TaskTitle::where('company_id',$adminAuth->company_id)->where('status', 1)->orderBy('title','ASC')->get();
                                @endphp
                                @foreach($taskTitle as $row)
                                    <option value="{{ $row->id }}">{{ $row->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 mx-auto">
                        <div class="row">
                            <div class="col-10">
                                <div class="form-group form-label-group">
                                    <label>Users <span>*</span></label>
                                    <select class="custom-select form-control select2-checkbox" multiple id="assign_to" name="assign_to[]" required>

                                        @php
                                            $admins=\App\Models\Admin::where('company_id',$adminAuth->company_id)->where('status', 1)->orderBy('firstname','ASC')->get();
                                        @endphp
                                        @foreach($admins as $row)
                                            <option value="{{ $row->id }}">{{ $row->firstname.' '.$row->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-2 p-0">
                                <input type="checkbox" class="checkbox" data-target="#assign_to"> All
                            </div>
                        </div>
                    </div>
                    @else
                        <input type="hidden" id="assign_to" name="assign_to[]" value="{{$adminAuth->id}}">
                    @endif
                    <div class="col-12">
                        <div class="form-label-group form-group activity-not-box">
                            <textarea id="description" name="description" rows="2" class="form-control" placeholder="Manual Task" @if(!$task_access_admin && $adminAuth->super!=1) required @endif ></textarea>
                            <label for="Notes">Manual Task @if(!$task_access_admin && $adminAuth->super!=1) <span>*</span> @endif</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-group form-group data-at-box">
                            <input type="text" class="form-control limit-format-picker" id="DateAt" name="date_at" placeholder="Date" required>
                            <label for="DateAt">Date <span>*</span></label>
                        </div>
                        <div class="form-label-group form-group data-at-box">
                            <input type="text" class="form-control mt-2 limit-timepicker" id="TimeAt" name="time_at" placeholder="Time">
                            <label for="TimeAt">Time</label>
                        </div>
                    </div>

                    <div class="col-12 clearfix w-100">
                        <button type="submit" id="submit-task" class="d-none"></button>
                        <input type="hidden" id="_id" name="_id">
                        <input type="hidden" id="_assign_to" name="_assign_to">
                        <button type="button" id="add-task-btn" class="btn bg-gradient-info glow float-right waves-effect waves-light">Submit</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
