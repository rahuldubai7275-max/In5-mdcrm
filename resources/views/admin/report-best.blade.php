@extends('layouts/contentLayoutMaster')

@section('title', 'Performance')

@section('vendor-style')
<!-- vendor css files -->

@endsection
@section('page-style')
<!-- Page css files -->
<link rel="stylesheet" href="{{ asset(mix('css/pages/card-analytics.css')) }}">
<style>
    .most-card p{
        max-width: 350px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .most-card {
        cursor: pointer;
    }
</style>
@endsection

@php
$adminAuth = Auth::guard('admin')->user();
@endphp


@section('content')
    {{-- Dashboard Ecommerce Starts --}}
    <div class="card">
        <div class="card-header" style="padding-bottom: 1.5rem;">
            <h4 class="card-title">Filters</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse">
            <div class="card-body">
                <div class="users-list-filter">
                    <form method="post" action="{{ route('best.report.filter') }}">
                        {!! csrf_field() !!}
                        <div class="row">

                            <div class="col-sm-4">
                                <fieldset class="form-group form-label-group">
                                    <label for="admin">Year</label>
                                    <select class="form-control" id="year" name="year">
                                        <option value="all">Overall</option>
                                        @php
                                        $smallYear=DB::select('SELECT MIN(YEAR(created_at)) AS smallYear FROM `view_activities` WHERE company_id='.$adminAuth->company_id);
                                        $firstYear=($smallYear[0]->smallYear) ? $smallYear[0]->smallYear : date('Y');
                                        @endphp
                                        @for($firstYear;$firstYear<=date('Y');$firstYear++)
                                        <option value="{{$firstYear}}">{{$firstYear}}</option>
                                        @endfor
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-sm-4">
                                <fieldset class="form-group form-label-group">
                                    <label for="month">Month</label>
                                    <select class="form-control" id="month" name="month">
                                        <option value="">Overall</option>
                                        <option value="01">January</option>
                                        <option value="02">February</option>
                                        <option value="03">March</option>
                                        <option value="04">April</option>
                                        <option value="05">May</option>
                                        <option value="06">June</option>
                                        <option value="07">July</option>
                                        <option value="08">August</option>
                                        <option value="09">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-sm-2">
                                <button type="submit" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="d-flex">
                <div>
                    <h2 class="text-primary mb-2" style="white-space: nowrap;">Performance</h2>
                </div>
                {{--<div class="w-100"><div class="w-100 border-3 border-primary"></div></div>--}}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="activity-2">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Viewings</h5>
                        <p class="truncate-text w-100">{{$mostViewing}}</p>
                    </div>
                    <div class="avatar bg-rgba-purple p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="feather icon-home text-purple font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="activity-3">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Appointments</h5>
                        <p class="truncate-text w-100">{{$mostAppointment}}</p>
                    </div>
                    <div class="avatar bg-rgba-brown-dark p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-calendar-check-o text-brown-dark font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="property-added">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Added Properties</h5>
                        <p class="truncate-text w-100">{{$mostAddedProperty}}</p>
                    </div>
                    <div class="avatar bg-rgba-brown p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-home text-brown font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="contact-added">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Added Contacts</h5>
                        <p class="truncate-text w-100">{{$mostAddedContact}}</p>
                    </div>
                    <div class="avatar bg-rgba-turquoise p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="feather icon-users text-turquoise font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="activity-1">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Calls</h5>
                        <p class="truncate-text w-100">{{$mostCaller}}</p>
                    </div>
                    <div class="avatar bg-rgba-pink p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-phone text-pink font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="leadAdded-contact">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Added To Contact Leads</h5>
                        <p class="truncate-text w-100">{{$mostAddedLead}}</p>
                    </div>
                    <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-magnet text-success font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($adminAuth->type==1)
        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="deal-added">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Deals</h5>
                        <p class="truncate-text w-100">{{$mostCountDeal}}</p>
                    </div>
                    <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-handshake-o text-primary font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="card most-card" data-toggle="modal" data-target="#mostModal" data-type="commission-added">
                <div class="card-header d-flex align-items-start pb-0">
                    <div style="width: 80%">
                        <h5 class="text-bold-700 mb-0">Commission</h5>
                        <p class="truncate-text w-100">{{$mostCommission}}</p>
                    </div>
                    <div class="avatar bg-rgba-secondary p-50 m-0 mb-1">
                        <div class="avatar-content">
                            <i class="fa fa-money text-secondary font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>


    <div class="modal fade text-left" id="mostModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('request.confirm') }}" class="modal-content" novalidate>
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </form>
        </div>
    </div>
@endsection

@section('vendor-script')
<!-- vendor files -->
@endsection
@section('page-script')
<!-- Page js files -->
<script>
    let arrayMonth=['January','February','March','April','May','June','July','August','September','October','November','December'];
    let currentYear='{{date('Y')}}';
    let currentMonth='{{date('m')}}';

    $('#year').val('{{$year}}').change();
    $('#month').val('{{$month}}');
    $('#year').change(function () {
        let val=$(this).val();
        $('#month').removeAttr('disabled');

        let toMonth='';
        if(val=='all' ){
            $('#month').val('').attr('disabled','disabled');
        }else if(currentYear==val){
            toMonth=currentMonth;
        }else{
            toMonth=12;
        }

        let monthHtml='<option value="">Overall</option>';
        let selectMount=$('#month').val();
        for(let i=0;i<toMonth;i++){
            let monthNum=i+1;
            if(monthNum<10){
                monthNum='0'+monthNum;
            }
            monthHtml+='<option value="'+monthNum+'">'+arrayMonth[i]+'</option>';
        }
        $('#month').html(monthHtml);
        $('#month').val(selectMount);

    });

    $('#year').change();

    $('.most-card').click(function () {
        let type=$(this).data('type');
        let title=$(this).find("h5").html();
        let arrayType=type.split("-");
        $('#mostModal .modal-title').html(title);
        $('#mostModal .modal-body').html('');
        $.ajax({
            url:"{{ route('best.agent.list') }}",
            type:"POST",
            data:{
                _token:$('meta[name="csrf-token"]').attr('content'),
                type:arrayType[0],
                activity_type:arrayType[1],
                year:$('#year').val(),
                month:$('#month').val(),
            },
            success:function (response) {
                $('#mostModal .modal-body').html(response);
            }, error: function (data) {
                var errors = data.responseJSON;
                console.log(errors);
            }
        });
    });
</script>

@endsection
