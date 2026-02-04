
@extends('layouts/contentLayoutMaster')

@section('title', 'Survey')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
    $survey_access_setting=\App\Models\Setting::where('company_id',$adminAuth->company_id)->where('title','upload_contact')->first();
    $survey_access_admin_id=[];
    if($survey_access_setting)
        $survey_access_admin_id=\App\Models\SettingAdmin::where('setting_id',17)->pluck('admin_id')->toArray();
@endphp
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Create Survey</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights question-create-btn" href="#questionCreate" data-toggle="modal">Add</a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped datatable1 truncate-table">
                        <thead>
                        <tr>
                            <th>Survey</th>
                            <th>Activity Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $surveyQuestions=\App\Models\SurveyQuestion::where('company_id',$adminAuth->company_id)->get();
                        @endphp
                            @foreach($surveyQuestions as $row)

                                @php
                                    $surveyAnswer=\App\Models\SurveyAnswer::where('survey_question_id',$row->id)->count();
                                @endphp
                            <tr>
                                <td><span class="survey-question" data-target="#ViewModal" data-toggle="modal">{{$row->question}}</span></td>
                                <td>{{NoteSubject[$row->subject]}}</td>
                                <td>
                                    <div class="custom-control custom-switch switch-lg custom-switch-success">
                                        <input type="checkbox" class="custom-control-input" data-sq="{{$row->id}}" id="customSwitch{{$row->id}}" {{($row->status==1) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch{{$row->id}}">
                                            <span class="switch-text-left">Enable</span>
                                            <span class="switch-text-right">Disable</span>
                                        </label>
                                    </div>
                                </td>
                                <td>{{\Helper::changeDatetimeFormat($row->created_at)}}</td>
                                <td>
                                    <div class="action d-flex font-medium-3" data-id="{{$row->id}}"  data-subject="{{ $row->subject }}" data-model="{{route('survey-question.delete')}}">
                                        <a href="#questionCreate" data-toggle="modal" class="edit-record" data-answer="{{ ($surveyAnswer==0) ? '1' : '0'}}"><i class="users-edit-icon feather icon-edit-1 font-medium-3 mr-50"></i></a>
                                        @if($surveyAnswer==0) <a href="javascript:void(0);" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a> @endif
                                    </div>
                                </td>
                            </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="questionCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('survey-question.add') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <select class="form-control" name="subject" id="subject" required>
                                    <option value="">Select</option>
                                    <option value="3">Appointment</option>
                                    <option value="2">Viewing</option>
                                </select>
                                <label for="subject">Activity Type</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group form-label-group">
                                <label for="question">Create</label>
                                <textarea class="form-control" id="question" name="question" placeholder="Question"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_id" id="_id">
                    <button type="submit" name="submit" id="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $('.datatable1').DataTable({ "order": [[ 2, "desc" ]] });
        $('table').on('change','input:checkbox',function () {
            let question=$(this).data('sq');
            let status=2;
            if ($(this).prop('checked')==true){
                status=1;
            }

            $.ajax({
                url:"{{ route('survey-question.status') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    question:question,
                    status:status,
                },
                success:function (response) {
                    $('#mostModal .modal-body').html(response);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('table').on('click','.edit-record',function () {
            $("#_id").val($(this).parent().data('id'));

            $("#subject").val( $(this).parents().data('subject') );

            $("#subject").removeAttr('disabled');

            if( $(this).data('answer') == 0 )
                $("#subject").attr('disabled','disabled');

            $("#question").val($(this).parents('tr').children('td:first-child').children('span').html());
            $("#questionCreate .modal-title").html('Edit Survey');
            $("#questionCreate button:submit").html('Edit');
            $("#questionCreate form").attr('action','{{ route('survey-question.edit') }}');
        });
        $('.question-create-btn').click(function () {
            $("#_id").val('');
            $("#subject").val('').removeAttr('disabled');
            $("#question").val('');
            $("#questionCreate .modal-title").html('Add');
            $("#questionCreate button:submit").html('Add');
            $("#questionCreate form").attr('action','{{ route('survey-question.add') }}');
        });

        $('body').on('click','.survey-question',function() {
            $('#ViewModal .modal-title').html( 'Question' );
            $('#ViewModal .modal-body').html( $(this).html() );
        });
    </script>
@endsection
