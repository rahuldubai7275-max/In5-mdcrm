@php
    $admin = Auth::guard('admin')->user();

    $RecruitmentInterview=DB::select('SELECT * FROM `recruitment_note` WHERE `note_subject`=1 AND `status`=1 AND admin_id='.$admin->id
    .' AND concat(date_at," ",time_at)>"'.date('Y-m-d H:i:s').'" ORDER BY concat(date_at," ",time_at) ASC');//AND concat(date_at," ",time_at)<"'.date('Y-m-d').' 23:59:59"

    $RecruitmentReminder=App\Models\RecruitmentNote::where([ ['note_subject','=','3'],['admin_id','=',$admin->id],['seen','=',0],['date_at','<=',date('Y-m-d')] ])
    ->orderBy('id', 'desc')->get();

@endphp

@if(count($RecruitmentReminder)>0)
<div class="col-12 order-0">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Reminder For Recruitment</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="custom-scrollbar" style="max-height: 300px;">
                    <ul class="list-group list-group-flush">
                        @foreach($RecruitmentReminder as $note)
                            @php
                                //agent=App\Models\Admin::where('id',$note->admin_id)->first();
                                $Recruitment=App\Models\Recruitment::where('id',$note->recruitment_id)->first();
                            @endphp
                            <li class="list-group-item">
                                <a href="/admin/recruitment-view/{{$note->recruitment_id }}?reminder={{$note->id}}">
                                    <p>{{$Recruitment->first_name .' '.$Recruitment->last_name}} {{\Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at)}}</p>
                                    <p class="m-0">{{$note->note}}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($RecruitmentInterview)
<div class="col-12 order-0">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Interview With</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="custom-scrollbar" style="max-height: 300px;">
                    <ul class="list-group list-group-flush">
                        @foreach($RecruitmentInterview as $note)
                            @php
                                //$agent=App\Models\Admin::where('id',$note->admin_id)->first();
                                $Recruitment=App\Models\Recruitment::where('id',$note->recruitment_id)->first();
                            @endphp
                            <li class="list-group-item">
                                <a href="/admin/recruitment-view/{{$note->recruitment_id }}">
                                    <p>{{$Recruitment->first_name .' '.$Recruitment->last_name}} {{\Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at)}}</p>
                                    <p class="m-0">{{$note->note}}</p>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
@endif

