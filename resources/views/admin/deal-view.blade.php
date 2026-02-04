@extends('layouts/contentLayoutMaster')

@section('title', 'Deal')

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/forms/wizard.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection
@section('content')

@php
    $admin = Auth::guard('admin')->user();

    $Agents=App\Models\Admin::get();
    $dealAgents='';
    $countDealAgent=0;
    $dealDocuments=[];
    if($deal){
        $admin=App\Models\Admin::where('id',$deal->admin_id)->first();
        $company=App\Models\Company::where('id',$deal->company_id)->first();
        $dealModel=App\Models\DealModel::where('id',$deal->deal_model_id)->first();
        $dealAgents=App\Models\DealAgent::where('deal_id',$deal->id)->orderBy('id', 'asc')->get();
        $countDealAgent=count($dealAgents);

        $dealDocuments=App\Models\DealDocument::where('deal_id',$deal->id)->orderBy('id', 'asc')->get();
    }

    $ReminderDocuments=['1'=>'Never','2'=>'1 Day in advance','3'=>'1 Week in advance','4'=>'1 Month in advance','5'=>'2 Months in advance','6'=>'3 Months in advance','7'=>'100 Days in advance'];
@endphp

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <div class="row">
                @csrf
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-sm-7">
                            <h6 class="text-primary ">Deal Details</h6>
                            <p><b>Ref: </b> {{'D-'.$deal->id}}</p>
                            @if($deal->deal_date)<p><b>Deal Date: </b> {{date('d-m-Y',strtotime($deal->deal_date))}}</p>@endif
                            <p><b>Transaction Type: </b> {{($deal->type==1) ? 'Rental' : 'Sales'}}</p>
                            @if($deal->deal_model_id && $deal->type==2) <p><b>Deal Model: </b> {{$dealModel->title}}</p> @endif
                            <p><b>Created By: </b> {{$admin->firstname.' '.$admin->lastname}}</p>
                            @if($deal->tenancy_contract_start_date)<p><b>Tenancy Contract Starting Date: </b> {{date('d-m-Y',strtotime($deal->tenancy_contract_start_date))}}</p>@endif
                            @if($deal->tenancy_renewal_date)<p><b>Tenancy Contract Renewal Date: </b> {{date('d-m-Y',strtotime($deal->tenancy_renewal_date))}}</p>@endif
                            @if($deal->cheques)<p><b>Cheques: </b> {{$deal->cheques}}</p>@endif
                            @if($deal->set_reminder && $deal->type==1) <p><b>Set Reminder: </b> {{$ReminderDocuments[$deal->set_reminder]}}</p> @endif
                            @if($deal->type==1) <p><b>Property Management: </b> {{($deal->property_management==1) ? 'Yes' : 'No'}}</p> @endif

                            <div class="d-flex deal-info-box">

                            </div>
                        </div>
                        <div class="col-sm-5">
                            <h6 class="text-primary">Commission Details</h6>
                            <p><b>Deal Price: </b> AED {{number_format($deal->deal_price)}}</p>
                            <p><b>Commission: </b> AED {{number_format($deal->commission)}}</p>
                            <p><b>{{ $company->brand }} Commission: </b> {{$deal->company_percent.'% - AED '.number_format($deal->company_commission)}}</p>
                            @if($countDealAgent>0)
                                @php
                                $agent1=\App\Models\Admin::find($dealAgents[0]->agent_id);
                                @endphp

                                <p><b>CM 1: </b>{{ $agent1->firstname.' '.$agent1->lastname }}</p>
                                <p><b>Commission: </b>{{$dealAgents[0]->percent.'% - AED '.number_format($dealAgents[0]->commission)}}</p>
                            @endif
                            @if($countDealAgent>1)
                                @php
                                $agent1=\App\Models\Admin::find($dealAgents[1]->agent_id);
                                @endphp

                                <p><b>CM 2: </b>{{ $agent1->firstname.' '.$agent1->lastname }}</p>
                                <p><b>Commission: </b>{{$dealAgents[1]->percent.'% - AED '.number_format($dealAgents[1]->commission)}}</p>
                            @endif
                            @if($countDealAgent>2)
                                @php
                                $agent1=\App\Models\Admin::find($dealAgents[2]->agent_id);
                                @endphp

                                <p><b>CM 3: </b>{{ $agent1->firstname.' '.$agent1->lastname }}</p>
                                <p><b>Commission: </b>{{$dealAgents[2]->percent.'% - AED '.number_format($dealAgents[2]->commission)}}</p>
                            @endif

                            {!! ($deal->inactive_reason && $deal->status==2) ? '<p class="border-top m-0 py-1 text-danger"><b>Reason: </b>'.$deal->inactive_reason.'</p>' : '' !!}
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <h6 class="text-primary"></h6>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"><a class="nav-link active" id="Documents-tab" data-toggle="tab" href="#Documents" aria-controls="Documents" role="tab" aria-selected="true">Documents</a></li>
                        {{--<li class="nav-item"><a class="nav-link" id="Tracking-tab" data-toggle="tab" href="#Tracking" aria-controls="Tracking" role="tab" aria-selected="true">Track The Deal</a></li>--}}
                    </ul>
                    <div class="custom-scrollbar pt-2" style="height: 420px;">
                        <div class="tab-content">
                            <div class="tab-pane active deal-doc-box" id="Documents" aria-labelledby="Documents-tab" role="tabpanel">
                            @foreach($dealDocuments as $dDoc)
                            <div class="doc-item mb-1">
                                <input type="hidden" name="deal_doc[]" value="{{$dDoc->docname}}">
                                <input type="hidden" name="document_type[]" value="{{$dDoc->type}}">
                                <input type="hidden" name="document_name[]" value="{{$dDoc->name}}">
                                <div class="media">
                                    <a class="media-left align-self-center" target="_blank" href="/storage/{{$dDoc->docname}}">
                                        <img src="/storage/{{$dDoc->docname}}" height="64" width="64">
                                    </a>
                                    <div class="media-body pl-1">
                                        <h5 class="media-heading">{{($dDoc->type) ? DealDocType[$dDoc->type] : ''}}</h5>
                                        @if($dDoc->name) <p class="mb-0"><b>Contract No:</b> {{$dDoc->name}}</p>@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                            </div>

                            {{--<div class="tab-pane" id="Tracking" aria-labelledby="Tracking-tab" role="tabpanel">
                                @php
                                    $completedTracking=\App\Models\DealTracking::where('type',1)->where('deal_id',$deal->id)->first();
                                @endphp
                                <div class="row m-0">
                                    <div class="col-sm-6">
                                        @if($completedTracking && $completedTracking->status==0)<a href="#trackingAdd" data-toggle="modal" class="btn btn-primary btn-add-traking w-100">Create</a>@endif
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="javascript:void(0);" class="btn btn-primary w-100" id="tracking-copy-link">Copy link</a>
                                    </div>
                                </div>

                                <ul class="list-group list-group-flush sort-element mt-2" id="tracking-step-box">

                                </ul>

                                <ul class="list-group list-group-flush border-top-" id="tracking-completed-box">
                                    @if($completedTracking)
                                        <li class="list-group-item">
                                            <div class="float-left">
                                                <div class="d-flex">
                                                    <div><span class="badge badge-{{( ($completedTracking->status==1) ? 'success' : 'secondary' )}} badge-pill mr-1">{{$completedTracking->row}}</span></div>
                                                    <span>{{$completedTracking->title}}</span>
                                                </div>
                                            </div>
                                            <div class="action float-right font-medium-1"   data-id="{{$completedTracking->id}}">
                                                <a href="#doneTracking" data-toggle="modal" class="tracking-done"><i class="users-edit-icon feather icon-check-circle"></i></a>
                                            </div>
                                        </li>
                                    @endif
                                </ul>
                            </div>--}}
                        </div>
                    </div>
                </div>
                @if(request('t'))
                <div class="col-12" data-id="{{$deal->id}}"  data-acknowledge="{{route("deal.acknowledge")}}">
                    @php
                        $today = date('Y-m-d');
                        if($deal->set_reminder==2)
                            $sr_days=1;
                        elseif($deal->set_reminder==3)
                            $sr_days=7;
                        elseif($deal->set_reminder==4)
                            $sr_days=30;
                        elseif($deal->set_reminder==5)
                            $sr_days=60;
                        elseif($deal->set_reminder==6)
                            $sr_days=90;
                        else
                            $sr_days=0;
                    @endphp
                    {!! ( ( $deal->acknowledge==0 && $deal->set_reminder>1 && $deal->tenancy_renewal_date <= date('Y-m-d', strtotime($today . "+ ".$sr_days." days")) ) ? '<a href="javascript:void(0);" data-toggle="modal" class="btn btn-outline-primary waves-effect waves-light mx-1 font-medium-1 acknowledge float-right">Acknowledge</a>' : '' ) !!}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="trackingAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add tracking Step</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="form-group form-label-group">
                            <label for="step-title">Step</label>
                            <input class="form-control" id="step-title" placeholder="Step">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="_id" id="_id">
                <button type="button" class="btn btn-primary d-none" id="edit-step-tracking">Update</button>
                <button type="button" class="btn btn-primary" id="add-step-tracking">Add</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="doneTracking" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Done</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mt-1">
                    <div class="col-12">
                        <div class="form-group form-label-group">
                            <label for="Date">Date</label>
                            <input class="form-control format-picker" id="tracking-date" placeholder="Date">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-tracking-done">Done</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <!--<script src="{{ asset(mix('vendors/js/extensions/jquery.steps.min.js')) }}"></script>-->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    <script src="{{ asset(mix('js/scripts/forms/wizard-steps.js')) }}"></script>
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

    <script>
        deal_info();
        function deal_info(){
            $.ajax({
                url:"{{ route('get-deal-info') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    property:'{{$deal->property_id}}',
                    contact:'{{$deal->contact_id }}',
                },
                success:function (response) {
                    $('.deal-info-box').html(response);
                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        $('body').on('click','.acknowledge', function () {
            var id=$(this).parent().data('id');
            var model=$(this).parent().data('acknowledge');
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
                    $('.delete-form-box form').append('<input type="hidden" value="'+id+'" name="_id">');
                    $('.delete-form-box form').append('<input type="submit" value="'+id+'" name="acknowledge">');
                    $('.delete-form-box form').attr('action',model);
                    $('.delete-form-box form input').click();
                }
            })
        });

        $('body').on('click','.ajax-delete', function () {
            var id=$(this).parent().data('id');
            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes, delete it!',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url:"{{ route('deal-tracking.delete') }}",
                        type:"POST",
                        data:{
                            _token:$('meta[name="csrf-token"]').attr('content'),
                            Delete:id
                        },
                        success:function (response) {
                            getTracking();
                        }, error: function (data) {
                            var errors = data.responseJSON;
                            console.log(errors);
                        }
                    });
                }
            })
        });

        $('#add-step-tracking').click(function () {
            let title=$('#step-title').val();
            $('#add-step-tracking').attr('disabled','disabled');
            $.ajax({
                url:"{{ route('deal-tracking.add') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    deal:'{{$deal->id}}',
                    title:title
                },
                success:function (response) {
                    getTracking();
                    $('#add-step-tracking').removeAttr('disabled');
                    $('#trackingAdd').modal('toggle');
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('#edit-step-tracking').click(function () {
            let title=$('#step-title').val();
            let id=$(this).val();
            $.ajax({
                url:"{{ route('deal-tracking.edit') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    _id:id,
                    title:title
                },
                success:function (response) {
                    getTracking();
                    $('#trackingAdd').modal('toggle');
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('body').on('click','.action .tracking-edit-record', function () {
            let title=$(this).parent().data('title');
            let id=$(this).parent().data('id');
            $('#step-title').val(title);
            $('#edit-step-tracking').val(id);
            $('#trackingAdd .modal-title').html('Edit');
            $('#edit-step-tracking').removeClass('d-none');
            $('#add-step-tracking').addClass('d-none');
        });

        $('.btn-add-traking').click(function () {
            $('#trackingAdd .modal-title').html('Add tracking Step');
            $('#step-title').val('');
            $('#edit-step-tracking').addClass('d-none');
            $('#add-step-tracking').removeClass('d-none');
        });

        $('body').on('click','.action .tracking-done', function () {
            let id=$(this).parent().data('id');
            $('#btn-tracking-done').val(id);
            $('#tracking-date').val('');
        });
        $('body').on('click','#btn-tracking-done', function () {
            let id=$(this).val();
            let date=$('#tracking-date').val();
            if(date=='') {
                $('#tracking-date').parent().addClass('error');
            }else {
                $.ajax({
                    url: "{{ route('deal-tracking.done') }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        tracking: id,
                        date: date,
                    },
                    success: function (response) {
                        if (response.r == 1) {
                            getTracking();
                            $('#doneTracking').modal('toggle');
                        } else {
                            toast_('', response.msg, $timeOut = 20000, $closeButton = true);
                        }
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }
        });

        getTracking();
        function getTracking() {
            $.ajax({
                url:"{{ route('deal-tracking.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    deal:{{$deal->id}},
                },
                success:function (response) {
                    $('#tracking-step-box').html(response.step);
                    $('#tracking-completed-box').html(response.completed);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }
    </script>

    <script>
        var fixHelperModified = function (e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function (index) {
                    $(this).width($originals.eq(index).width())
                });
                return $helper;
            },

            updateIndex = function (e, ui) {
                let obj=[];
                $( "#tracking-step-box li" ).each(function( index ) {

                    $(this).find(".badge").html(index+1);
                    let id=$(this).find(".action").data('id');
                    obj[index]=id;
                });

                $.ajax({
                    url:"{{ route('deal-tracking.row') }}",
                    type:"POST",
                    data:{
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        tracking:obj
                    },
                    success:function (response) {
                        getTracking();
                    }, error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            };

        $(".sort-element").sortable({
            helper: fixHelperModified,
            stop: updateIndex
        }).disableSelection();

        $(".sort-element").sortable({
            distance: 5,
            delay: 100,
            opacity: 0.6,
            cursor: 'move',
            update: function () {
            }
        });
    </script>
    <script>
        // var wallet_address = $("#copy_wallet_address_input");
        var btnCopy = $("#tracking-copy-link");

        // copy text on click
        btnCopy.on("click", function () {
            var dummy = document.createElement('input'),
                text = "{{request()->getSchemeAndHttpHost()}}/tracking/{{$deal->id}}";

            $('#link-input').removeClass('d-none');
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand('copy');
            document.body.removeChild(dummy);
            $('#link-input').addClass('d-none');

            toastr.success('Link Copied');
        });
    </script>

@endsection
