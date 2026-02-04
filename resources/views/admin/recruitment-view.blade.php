
@extends('layouts/contentLayoutMaster')

@section('title', 'Recruitment')

@section('vendor-style')
    <!-- vendor css files -->
	<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" type="text/css" href="/css/magnific-popup.css" />

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick.min.css" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.5.9/slick-theme.min.css" />

@endsection
@section('content')
    <!-- Form wizard with step validation section start -->
    <div class="card">
        <!--<div class="card-header">
            <h4 class="card-title">Add New Property</h4>
        </div>-->
        <div class="card-content">
            <div class="card-body container">
                <div class="row">

                    <div class="col-sm-4">
                        @php
                        $JobTitle=\App\Models\JobTitle::find($recruitment->job_title_id);
                        $RLanguage=\App\Models\RecruitmentLanguage::where('recruitment_id',$recruitment->id)->get();
                        $RecruitmentNote=\App\Models\RecruitmentNote::where('recruitment_id',$recruitment->id)->orderBy('created_at','DESC')->get();
                        $language='';
                        foreach($RLanguage as $row){
                            $lang=\App\Models\Language::find($row->language_id);
                            $language.=$lang->name.', ';
                        }
                        @endphp
                        <h4 class="text-primary py-2">Details</h4>
                        {!! ($recruitment) ? '<p class="border-top m-0 py-1"><b>Full Name: </b> '.$recruitment->first_name.' '.$recruitment->last_name.' </p>' : '' !!}
                        {!! ($JobTitle) ? '<p class="border-top m-0 py-1"><b>Job Title: </b> '.$JobTitle->name.' </p>' : '' !!}
                        {!! ($RLanguage) ? '<p class="border-top m-0 py-1"><b>Language: </b> '.rtrim($language,', ').' </p>' : '' !!}
                        {!! ($recruitment->expected_salary) ? '<p class="border-top m-0 py-1"><b>Expected Salary: </b> '.number_format($recruitment->expected_salary).' </p>' : '' !!}
                        {!! ($recruitment->commission_percent) ? '<p class="border-top m-0 py-1"><b>Commission %: </b> '.$recruitment->commission_percent.' </p>' : '' !!}
                        {!! ($recruitment->mobile_number) ? '<p class="border-top m-0 py-1"><b>Mobile Number: </b> '.$recruitment->mobile_number.' </p>' : '' !!}
                        {!! ($recruitment->email) ? '<p class="border-top m-0 py-1"><b>Email: </b> '.$recruitment->email.' </p>' : '' !!}
                        {!! ($recruitment->created_at) ? '<p class="border-top m-0 py-1"><b>Added Date: </b> '.\Helper::changeDatetimeFormat($recruitment->created_at).' </p>' : '' !!}
                        {!! ($recruitment->cv) ? '<div><p><b>CV: </b><a class="font-medium-5" href="/storage/'.$recruitment->cv.'" target="_blank"><i class="feather icon-download"></i></a></p></div>' : '' !!}
                    </div>

                    <div class="col-sm-8 mt-3">
                        <div class="float-left">
                            <button type="button" class="font-small-3 px-1 btn-activity btn btn-150 btn-outline-success waves-effect waves-light float-left" data-target="#ActivityModal" data-toggle="modal">Activity</button>
                        </div>
                        <div class="mt-1 activity-box">
                            <div class="table-responsive custom-scrollbar pr-1" style="max-height: 500px;">
                                <table class="table table-striped truncate-table">
                                    <thead>
                                    <tr>
                                        <th>Activity Type</th>
                                        <th>Note</th>
                                        <th>Date - Time</th>
                                        <th>User</th>
                                        <th>Added Date</th>
                                    </tr>
                                    </thead>
                                    <tbody id="div_notes_section">
                                    @foreach($RecruitmentNote as $note)
                                        @php
                                            $note_admin=\App\Models\Admin::find($note->admin_id);
                                        @endphp
                                        <tr class="note-description" data-title="{{RecruitmentNoteSubject[$note->note_subject]}}" data-desc="{{$note->note}}">
                                            <td data-target="#ViewModal" data-toggle="modal">{{RecruitmentNoteSubject[$note->note_subject]}}</td>
                                            <td data-target="#ViewModal" data-toggle="modal">
                                                {!! '<span class="note{{$note->id}}">'.
                                                    \Illuminate\Support\Str::limit(strip_tags($note->note),50)
                                                     .'</span>' !!}
                                            </td>
                                            <td data-target="#ViewModal" data-toggle="modal">{{($note->date_at) ? str_replace(':00','',\Helper::changeDatetimeFormat($note->date_at.' '.$note->time_at)) : ''}}</td>
                                            <td data-target="#ViewModal" data-toggle="modal" >{{$note_admin->firstname.' '.$note_admin->lastname}}</td>
                                            <td data-target="#ViewModal" data-toggle="modal">{{\Helper::changeDatetimeFormat($note->created_at)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form method="post" action="{{route('recruitment.note.add')}}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Activity</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row my-2">
                        <div class="col-sm-6 mx-auto">
                            <div class="form-group form-label-group">
                                <label>Activity Type</label>
                                <select class="custom-select form-control" id="NoteSubject" name="NoteSubject">
                                    <option value="">Select</option>
                                    @foreach(RecruitmentNoteSubject as $key=>$value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-label-group form-group activity-not-box">
                                        <textarea id="Note" name="Note" rows="2" class="form-control" placeholder="Add your note"></textarea>
                                        <label for="Notes">Notes</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-label-group form-group data-at-box d-none">
                                        <input type="text" class="form-control limit-format-picker" id="DateAt" name="DateAt" placeholder="Date">
                                        <label for="DateAt">Date</label>
                                    </div>
                                    <div class="form-label-group form-group data-at-box d-none">
                                        <input type="text" class="form-control mt-2 limit-timepicker" id="TimeAt" name="TimeAt" placeholder="Time">
                                        <label for="TimeAt">Time</label>
                                    </div>

                                    <div class="clearfix w-100">
                                        <input type="hidden" name="recruitment" value="{{$recruitment->id}}">
                                        <button type="button" id="AddNote" class="btn bg-gradient-info glow mb-1 float-right waves-effect waves-light">Submit</button>
                                        <button type="submit" id="submit" class="d-none">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files -->
    <script>
        $('#NoteSubject').change(function(){
            let val=$(this).val();
            $('#ActivityModal .error').removeClass('error');
            if(val==1 || val==3)
                $('.data-at-box').removeClass('d-none');
            else
                $('.data-at-box').addClass('d-none').children('input').val('');
        });

        $('#AddNote').click(function () {
            let NoteSubject =$('#NoteSubject').val();
            let note =$('#Note').val();
            let date_at=$('#DateAt').val();
            let time_at=$('#TimeAt').val();
            let error=0;

            if(NoteSubject == ''){
                $("#NoteSubject").parent().addClass('error');
                error=1
            }else{
                $("#NoteSubject").parent().removeClass('error');
            }


            if(note == ''){
                $("#Note").parent().addClass('error');
                error=1
            }else{
                $("#Note").parent().removeClass('error');
            }


            if(NoteSubject==1 || NoteSubject==3){
                if(date_at == ''){
                    $("#DateAt").parent().addClass('error');
                    error=1
                }else{
                    $("#DateAt").parent().removeClass('error');
                }
                if(time_at == ''){
                    $("#TimeAt").parent().addClass('error');
                    error=1
                }else{
                    $("#TimeAt").parent().removeClass('error');
                }
            }

            if(error==0) {
                $('#AddNote').html('Please wait...').attr('disabled','disabled');
                $('#submit').click();
            }
        });

        $('body').on('click','.note-description td',function() {
            let html=$(this).children('.action').html();
            if (!html) {
                $('#ViewModal .modal-title').html( $(this).parent().data('title') );
                $('#ViewModal .modal-body').html( $(this).parent().data('desc') );
            }
        });
    </script>
    <script>

        (function(window, document, $) {
            'use strict';

            $('.limit-format-picker').pickadate({
                format: 'yyyy-mm-dd',
                min:true
            });

            /*******    Pick-a-time Picker  *****/
            let today = new Date();
            let dd = String(today.getDate()).padStart(2, '0');
            let mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            let yyyy = today.getFullYear();

            today = yyyy + '-' + mm + '-' +dd ;
            let date=today;

            var $input= $('.limit-timepicker').pickatime({
                format: 'HH:i',
                interval:10,    });

            var picker = $input.pickatime('picker');

            $('#DateAt').change(function(){
                date=$(this).val();
                $('#TimeAt').val('');
                if(today==date){
                    picker.set('min', true);
                }else{
                    picker.set('min', false);
                }
            });
        })(window, document, jQuery);
    </script>
@endsection
