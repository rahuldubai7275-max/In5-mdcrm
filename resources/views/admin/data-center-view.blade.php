
@extends('layouts/contentLayoutMaster')

@section('title', 'Data')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
    <style>
        .dropdown-toggle::after{
            display: none;
        }
        .dropdown-menu{
            right: 0 !important;
            left: unset !important;
        }
    </style>
@endsection
@php
    $adminAuth=\Auth::guard('admin')->user();
    $company=\App\Models\Company::find($adminAuth->company_id);
    $status_by=App\Models\Admin::find($data->status_by);

    $propertyDataCenter=[];
    if($data->master_project && $data->master_project!='-' &&
    $data->project && $data->project!='-' &&
    $data->villa_unit_no && $data->villa_unit_no!='-'
    ){
        $propertyDataCenter=App\Models\DataCenter::where('master_project',$data->master_project)->
            where('project',$data->project)->
            where('st_cl_fr',$data->st_cl_fr)->
            where('villa_unit_no',$data->villa_unit_no)->where('id','!=',$data->id)->get();
    }

    $contactDataCenter=[];
    if(($data->phone_number && $data->phone_number!='-') ||
    ($data->phone_number_2 && $data->phone_number_2!='-') ||
    ($data->email && $data->email!='-')
    ){
        //$orWhere=[];
        //if($data->phone_number && $data->phone_number!='-')
        //    $orWhere[]=['phone_number', '=', $data->phone_number];

        //if($data->phone_number_2 && $data->phone_number_2!='-')
        //    $orWhere[]=['phone_number_2', '=', $data->phone_number_2];

        //if($data->email && $data->email!='-')
        //    $orWhere[]=['email', '=', $data->email];

        //$contactDataCenter=App\Models\DataCenter::orWhere($orWhere)->where('id','!=',$data->id)->get();

        $contactDataCenter=App\Models\DataCenter::where('name',$data->name)->
            where('phone_number',$data->phone_number)->
            where('phone_number_2',$data->phone_number_2)->
            where('email',$data->email)->where('id','!=',$data->id)->get();
    }
    $master_project='';
    if($data->master_project_id && !request('page')){
        $MProject=\App\Models\MasterProject::find($data->master_project_id);
        $master_project=$MProject->name;
    }else{
        $master_project=$data->master_project;
    }
    $projectTxt='';
    if($data->project_id && !request('page')){
        $Project=\App\Models\Community::where('id',$data->project_id)->first();
        $projectTxt=$Project->name;
    }else{
        $projectTxt=$data->project;
    }

    $phone_number='';
    $phone_number_2='';
    $email='';
    $st_cl_fr='';
    $villa_unit_no='';
    if(($data->added_to_property=='' && $data->added_to_contact=='') || $data->status!=2
    || ($data->added_to_property_admin==$adminAuth->id || $data->added_to_contact_admin==$adminAuth->id)
    || $adminAuth->type<3){
        if($data->phone_number!='-')
            $phone_number=$data->phone_number;
        if($data->phone_number_2!='-')
            $phone_number_2=$data->phone_number_2;
        if($data->email!='-')
            $email=$data->email;
        if($data->st_cl_fr!='-')
            $st_cl_fr=$data->st_cl_fr;
        if($data->villa_unit_no!='-')
            $villa_unit_no=$data->villa_unit_no;
    }

        $status='<span class="badge badge-pill badge-light-'.DataCenterStatusColor[$data->status] .'">'.DataCenterStatus[$data->status].'</span>';

        $added_to_contact='';
        if($data->added_to_contact) {
            $Contact=App\Models\Contact::where('id',$data->added_to_contact)->first();
            $added_to_contact='<a href="/admin/contact/view/'.$Contact->id.'">'.$Contact->firstname.' '.$Contact->lastname.'</a>';

            $status_by=App\Models\Admin::find($data->added_to_contact_admin);
            $status='<span class="badge badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Contact</span>';
        }
        $obj['added_to_contact']=$added_to_contact;

        $added_to_property='';
        if($data->added_to_property) {
            $Property=App\Models\Property::where('id',$data->added_to_property)->first();
            $added_to_property='<a href="/admin/property/view/'.$Property->id.'">'.$company->sample.'-'.(($Property->listing_type_id==1) ? 'S' : 'R').'-'.$Property->ref_num.'</a>';

            $status_by=App\Models\Admin::find($data->added_to_property_admin);
            $status='<span class="badge badge-pill badge-light-'.DataCenterStatusColor[2] .'">Added To Property</span>';
        }
@endphp
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
                        <p class="m-0"><b>Ref: </b> DC-{{$data->id}} {!! $status !!} </p>

                        <h4 class="text-primary pt-1">Contact Details</h4>
                        @if($data->name) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Name: </b> {{$data->name}} </p> @endif
                        @if($phone_number) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Phone Number: </b> {{$phone_number}}</p> @endif
                        @if($phone_number_2) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Phone Number 2: </b> {{$phone_number_2}}</p> @endif
                        @if($email) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Email: </b> {{$email}} </p> @endif
                        @if($data->nationality) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Nationality: </b> {{$data->nationality}} </p> @endif

                        <h4 class="text-primary pt-1">Property Details</h4>
                        <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Master Project: </b>{{$master_project}}</p>
                        <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Project: </b>{{$projectTxt}}</p>
                        @if($st_cl_fr) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Cluster / Street / Frond: </b>{{$st_cl_fr}}</p> @endif
                        @if($villa_unit_no) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Villa/Unit Number: </b> {{$villa_unit_no}} </p> @endif
                        @if($data->size) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>BUA: </b> {{number_format($data->size)}}</p> @endif
                        @if($data->plot_size) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Plot Size: </b> {{number_format($data->plot_size)}}</p> @endif
                        @if($data->bedrooms) <p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Bedrooms: </b> {{$data->bedrooms}}</p> @endif

                        {!! ($status_by && $data->added_to_contact) ? '<p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Added to contact by : </b>'.$status_by->firstname.' '.$status_by->lastname.' <br> '.\Helper::changeDatetimeFormat($data->added_to_contact_date).'</p>' : '' !!}
                        {!! ($status_by && $data->added_to_property) ? '<p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Added to property by : </b>'.$status_by->firstname.' '.$status_by->lastname.' <br> '.\Helper::changeDatetimeFormat($data->added_to_property_date).'</p>' : '' !!}
                        {!! ($status_by && $data->status==2) ? '<p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Assigned By : </b>'.$status_by->firstname.' '.$status_by->lastname.' <br> '.\Helper::changeDatetimeFormat($data->assign_date).'</p>' : '' !!}
                        {!! ($status_by && $data->status==3) ? '<p class="border-top m-0" style="padding-bottom: 7px;padding-top: 7px"><b>Closed By : </b>'.$status_by->firstname.' '.$status_by->lastname.' <br> '.\Helper::changeDatetimeFormat($data->result_date).'</p>' : '' !!}
                        {!! ($data->colse_reason && $data->status==3) ? '<p class="border-top m-0 py-1 text-danger"><b>Reason: </b>'.$data->colse_reason.'</p>' : '' !!}

                        @if(count($propertyDataCenter)>0) <p class="border-top m-0 py-1"><a href="#otherProperties" data-toggle="modal">Other owners for the same property</a></p> @endif
                        @if(count($contactDataCenter)>0) <p class="border-top m-0 py-1"><a href="#otherContacts" data-toggle="modal">Other properties for the same owner</a></p> @endif
                    </div>

                    <div class="col-sm-8">
                        <div class="clearfix">
                            @if($data->status!=2 && $data->added_to_property=='' && $data->added_to_contact=='') <button type="button" class="btn-activity btn btn-outline-success mr-1 mb-1 waves-effect waves-light float-left" data-target="#ActivityModal" data-toggle="modal">Activity</button> @endif
                            <div class="float-right">
                                <a href="/admin/data-center-view/{{($Previous) ? $Previous : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Previous) ? '' : 'disabled'}}">
                                    <span class="d-none d-sm-block">Previous</span>
                                    <span class="d-block d-sm-none"><</span>
                                </a>
                                <a href="/admin/data-center-view/{{($Next) ? $Next : ''}}" class="btn btn-120 bg-gradient-info py-1 px-2 waves-effect waves-light {{($Next) ? '' : 'disabled'}}">
                                    <span class="d-none d-sm-block">Next</span>
                                    <span class="d-block d-sm-none">></span>
                                </a>
                            </div>
                        </div>
                        <div class="table-responsive custom-scrollbar pr-1 pt-5 mt-1" style="max-height: 450px;">
                            <table class="table table-striped truncate-table mb-0">
                                <thead>
                                <tr>
                                    <th>Activity Type</th>
                                    <th>Note</th>
                                    <th>User</th>
                                    <th>Added Date</th>
                                </tr>
                                </thead>
                                <tbody id="div_notes_section">
                                @php
                                    $DataCenterNote=\App\Models\DataCenterNote::where('data_center_id',$data->id)->get();
                                @endphp
                                @foreach($DataCenterNote as $note)

                                    @php
                                        $note_admin=\App\Models\Admin::find($note->admin_id);
                                    @endphp

                                    <tr class="note-description" data-title="{{NoteSubject[$note->note_subject]}}" data-desc="{{($note->status==2 && $note->note_subject==2) ? NoteSubject[$note->note_subject].' Cancelled' : $note->note}}">

                                        <td data-target="#ViewModal" data-toggle="modal" >{{NoteSubject[$note->note_subject]}}</td>
                                        <td data-target="#ViewModal" data-toggle="modal" >
                                            {!! ( ($note->date_at) ? \Helper::changeDatetimeFormat( $note->date_at.' '.$note->time_at).'<br>' : '' )
                                                .'<span class="note{{$note->id}}">'.\Illuminate\Support\Str::limit(strip_tags($note->note),50)

                                                 .'</span>' !!}
                                        </td>
                                        <td data-target="#ViewModal" data-toggle="modal" >{{$note_admin->firstname.' '.$note_admin->lastname}}</td>
                                        <td data-target="#ViewModal" data-toggle="modal" >{{\Helper::changeDatetimeFormat( $note->created_at)}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 mt-3">
                        @php
                            $DataCenterNote=\App\Models\DataCenterNote::where('data_center_id',$data->id)->where('data_center_id',$data->id)->get();
                        @endphp
                        @if($data->status!=2 && $data->added_to_property=='' && $data->added_to_contact=='')
                            <div class="btn-group float-right" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        @if($adminAuth->type!=7)
                                            @if($data->added_to_property=='') <a href="/admin/property?dc={{$data->id}}" class="dropdown-item">Add To Property</a> @endif
                                            @if($data->added_to_contact=='') <a href="/admin/add-contacts?dc={{$data->id}}" class="dropdown-item">Add To Contact</a> @endif
                                        @endif
                                        <a data-toggle="modal" href="#assignModal" class="dropdown-item">Assign To</a>
                                        <a data-value="4" class="dropdown-item status">Need To Follow</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($data->status!=3 && $data->status!=2 && $data->added_to_property=='' && $data->added_to_contact=='') <button type="button" data-toggle="modal" data-target="#closeModal" class="btn btn-danger waves-effect waves-light search-contact float-left">Don't Disturb</button> @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    @if($data->status!=3)
    <!-- Modal Close -->
    <div class="modal fade text-left" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('data-center-close')}}" novalidate class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Don't Disturb</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-12">
                            <fieldset class="form-group form-label-group">
                                <label for="Reason">Reason</label>
                                <select class="form-control" id="Reason" name="Reason" required>
                                    <option value="">Select</option>
                                    @foreach(DataCenterClosedReason as $reason)
                                        <option value="{{$reason}}">{{$reason}}</option>
                                    @endforeach
                                    <option value="Other">Other</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-12 d-none">
                            <div class="form-group form-label-group">
                                <input type="text" class="form-control" id="colse_reason" name="colse_reason" placeholder="Reason" required>
                                <label>Reason</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="dc" value="{{$data->id}}">
                    <button type="submit" class="btn btn-danger">Submit</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    <!-- Modal Assign -->
    <div class="modal fade text-left" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{route('data-center-assign')}}" novalidate class="modal-content">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Assign To</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row pt-2">
                        <div class="col-sm-6">
                            <input type="hidden" name="dc" value="{{$data->id}}">
                            <fieldset class="form-group form-label-group">
                                <label for="select-agent">Assign To <span>*</span></label>
                                <select class="form-control select2" id="assign_to" name="assign_to" required>
                                    <option value="">Select</option>
                                    @if($adminAuth->type==7)
                                        @php
                                        if($data->project_id!=''){
                                            $Agents=DB::select("SELECT admin_id,concat(firstname,' ',lastname) as admin_name FROM data_center_assign,admins WHERE data_center_assign.admin_id=admins.id AND `all`=1 AND admins.status=1 AND master_project_id=".$data->master_project_id."
                                                                UNION
                                                                SELECT admin_id,concat(firstname,' ',lastname) as admin_name FROM data_center_assign,admins,data_center_assign_project WHERE data_center_assign.admin_id=admins.id AND data_center_assign.id=data_center_assign_project.dca_id AND admins.status=1 AND project_id=".$data->project_id." ORDER BY admin_name ASC");
                                        }
                                        if($data->project_id==''){
                                            $Agents=DB::select("SELECT admin_id,concat(firstname,' ',lastname) as admin_name FROM data_center_assign,admins WHERE data_center_assign.admin_id=admins.id AND `unmatched`=1 AND admins.status=1 AND master_project_id=".$data->master_project_id." ORDER BY admin_name ASC");
                                        }
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->admin_id }}">{{$agent->admin_name }}</option>
                                        @endforeach
                                    @else
                                        @php
                                            $Agents=App\Models\Admin::where('main_number','!=','+971502116655')->orderBy('firstname','ASC')->get();
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-sm-6">
                            <fieldset class="form-group form-label-group">
                                <label for="contact-categories">Contact Categories</label>
                                <select class="form-control" id="contact-categories" name="contact_category" required>
                                    <option value="">Select</option>
                                    <option value="buyer">Buyer</option>
                                    <option value="tenant">Tenant</option>
                                    <option value="agent">Agent</option>
                                    <option value="owner">Owner</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" value="" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade text-left" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
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
                                    @foreach(NoteSubject as $key => $value)
                                        @if($key==2 || $key==3)
                                            @continue
                                        @endif
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-12 col-md-8">
                                    <div class="form-label-group form-group activity-not-box">
                                        <textarea id="Note" name="note" rows="2" class="form-control" placeholder="Add your note"></textarea>
                                        <label for="Notes">Notes</label>
                                    </div>

                                    <div class="form-label-group contact-property-box d-none">
                                        <div class="form-label-group form-group">
                                            <select class="select-2-user form-control" name="ActivityContact" id="ActivityContact"></select>
                                            <label for="SearchRepository" id="ActivityContactLabel">Contact</label>
                                        </div>
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
                                        <button type="button" id="AddPropertyNote" class="btn bg-gradient-info glow mb-1 float-right waves-effect waves-light">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Others Properties -->
    <div class="modal fade text-left" id="otherProperties" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Other owners for the same property</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach($propertyDataCenter as $Property)
                            <li class="list-group-item">
                                <a href="/admin/data-center-view/{{$Property->id}}" target="_blank">DC-{{$Property->id}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Others Contacts -->
    <div class="modal fade text-left" id="otherContacts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel16">Other properties for the same owner</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        @foreach($contactDataCenter as $contact)
                            <li class="list-group-item">
                                <a href="/admin/data-center-view/{{$contact->id}}" target="_blank">DC-{{$contact->id}}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.time.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/legacy.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        $('#Reason').change(function () {
            let val=$(this).val();
            $('#colse_reason').parent().parent().addClass('d-none');
            $('#colse_reason').val(val);
            if(val=='Other') {
                $('#colse_reason').parent().parent().removeClass('d-none');
                $('#colse_reason').val('');
            }
        })
    </script>

    <script>
        $('body').on('click','.status', function () {
            var val=$(this).data('value');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You want to Acknowledge!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Yes',
                confirmButtonClass: 'btn btn-primary',
                cancelButtonClass: 'btn btn-danger ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $('.delete-form-box form').append('<input type="hidden" value="'+val+'" name="status">');
                    $('.delete-form-box form').append('<input type="hidden" value="{{$data->id}}" name="dc">');
                    $('.delete-form-box form').append('<input type="submit">');
                    $('.delete-form-box form').attr('action','{{route('data-center-action')}}');
                    $('.delete-form-box form input:submit').click();
                }
            })
        });

        $('body').on('click','.note-description td',function() {
            let html=$(this).children('.action').html();
            if (!html) {
                $('#ViewModal .modal-title').html( $(this).parent().data('title') );
                $('#ViewModal .modal-body').html( $(this).parent().data('desc') );
            }
        });

        $('#NoteSubject').change(function(){
            let val=$(this).val();
            $('#ActivityModal .error').removeClass('error');
            if(val==2 || val==3 || val==6)
                $('.data-at-box').removeClass('d-none');
            else
                $('.data-at-box').addClass('d-none').children('input').val('');

            if(val==2){
                $('.contact-property-box').removeClass('d-none');
                $('.activity-not-box').addClass('d-none');
            }else{
                $('.contact-property-box').addClass('d-none').children('select').val('');
                $('.activity-not-box').removeClass('d-none');
            }
        });

        $('#AddPropertyNote').click(function () {
            let NoteSubject =$('#NoteSubject').val();
            let note =$('#Note').val();
            let date_at=$('#DateAt').val();
            let time_at=$('#TimeAt').val();
            let contact =$('#ActivityContact').val();
            let error=0;

            if(NoteSubject == ''){
                $("#NoteSubject").parent().addClass('error');
                error=1
            }else{
                $("#NoteSubject").parent().removeClass('error');
            }

            if(NoteSubject!=2) {
                if(note == ''){
                    $("#Note").parent().addClass('error');
                    error=1
                }else{
                    $("#Note").parent().removeClass('error');
                }
            }

            if(NoteSubject==2 || NoteSubject==3 || NoteSubject==6) {
                if (date_at == '') {
                    $("#DateAt").parent().addClass('error');
                    error = 1
                } else {
                    $("#DateAt").parent().removeClass('error');
                }
            }

            if(NoteSubject==2 || NoteSubject==3 || NoteSubject==6){
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

            if(NoteSubject==2){
                if(contact == '' || contact == null){
                    $("#ActivityContact").parent().addClass('error');
                    error=1
                }else{
                    $("#ActivityContact").parent().removeClass('error');
                }
            }

            if(error==0) {
                $('#AddPropertyNote').html('Please wait...').attr('disabled','disabled');
                $.ajax({
                    url: "{{ route('data-center.note.add') }}",
                    type: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        data_center: "{{ ($data) ? $data->id : '' }}",
                        note_subject: NoteSubject,
                        contact: contact,
                        date_at: date_at,
                        time_at: time_at,
                        note: note
                    },
                    success: function (response) {
                        $('#NoteSubject').val('').change();
                        $('#DateAt , #TimeAt , #Note').val('');
                        $('#AddPropertyNote').html('Submit').removeAttr('disabled');
                        $('#div_notes_section').prepend(response);
                        $('.activity-box').removeClass('d-none');
                        $('#ActivityModal').modal('hide');
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
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
