
@extends('layouts/contentLayoutMaster')

@section('title', 'New projects')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="/css/simplePagination.css" rel="stylesheet">

    <style>
        .off-plan-btn-edit{
            border: 0;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            background: var(--primary);
            color: #fff !important;
            position: absolute;
            right: 10px;
            bottom: 0;
            padding: 0 5px;
        }

        .coming-soon-tag{
            position: absolute;
            background: #c20000;
            color: #fff;
            left: 10px;
            top: 10px;
            border-radius: 5px;
            padding: 3px 10px;
            -webkit-text-stroke-width: thin;
        }

        .developer-tag{
            position: absolute;
            background: rgba(78, 62, 62, 0.48);
            color: #fff;
            right: 10px;
            top: 10px;
            border-radius: 5px;
            padding: 3px 10px;
            -webkit-text-stroke-width: thin;
        }
    </style>

@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')

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
                    <div class="row">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="Emirate">Emirate</label>
                                <select class="custom-select form-control" id="emirate">
                                    <option value="">Select</option>
                                    @php
                                        $Emirates=\App\Models\Emirate::get();
                                    @endphp
                                    @foreach($Emirates as $Emirate)
                                        <option value="{{ $Emirate->id }}">{{ $Emirate->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="master-project">Master Project</label>
                                <select class="form-control  select2" id="master-project">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>
                        {{--<div class="col-12 col-sm-6 col-lg-3">
                            <fieldset class="form-group form-label-group">
                                <label for="community">Project</label>
                                <select class="form-control  select2" id="community">
                                    <option value="">Select</option>

                                </select>
                            </fieldset>
                        </div>--}}

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label>Project Name</label>
                                <input type="text" id="project-name" class="form-control" placeholder="Project Name">
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label for="Developer">Status</label>
                                <select class="custom-select form-control" id="status">
                                    @foreach(OffPlanProjectStatus as $key=>$value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label>Residential / Commercial</label>
                                <select class="custom-select form-control" id="p_type">
                                    <option value="">Select</option>
                                    @foreach(PropertyType as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group form-label-group">
                                <label>Property Type</label>
                                <select class="custom-select form-control" id="property-type">
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label for="select-developer">Developer</label>
                                <select class="custom-select form-control select2" id="select-developer">
                                    <option value="">Select</option>
                                    @php
                                        $developers=\App\Models\Developer::orderBy('name','ASC')->get();
                                    @endphp
                                    @foreach($developers as $dev)
                                        <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label>PHPP</label>
                                <select class="custom-select form-control" id="phpp" name="phpp">
                                    <option value="">Select</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="form-group form-label-group">
                                <label>Handover Date</label>
                                <select class="custom-select form-control" id="year" name="year">
                                    <option value="">Select</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label>Starting Price (AED)</label>
                                        <input type="text" id="from-price" class="form-control number-format" placeholder="From">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group form-label-group">
                                        <label>Starting Price (AED)</label>
                                        <input type="text" id="to-price" class="form-control number-format" placeholder="To">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group validate">
                                        <label for="from-date">Date Of Launch</label>
                                        <input type="text" name="from-date-launch" id="from-date-launch" autocomplete="off" class="form-control format-picker" readonly placeholder="From">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-label-group rented-until-box">
                                        <label for="to-date">Date Of Launch</label>
                                        <input type="text" name="to-date-launch" id="to-date-launch" autocomplete="off" class="form-control format-picker" readonly placeholder="To">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    @php
                                        $Bedrooms=App\Models\Bedroom::get();
                                    @endphp

                                    <fieldset class="form-group form-label-group">
                                        <label for="bedrooms">Bedrooms From</label>
                                        <select class="form-control" id="bedroom_from">
                                            <option value="">Select</option>
                                            @foreach($Bedrooms as $Bedroom)
                                                <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-sm-6">
                                    <fieldset class="form-group form-label-group">
                                        <label for="bedrooms">Bedrooms To</label>
                                        <select class="form-control" id="bedroom_to">
                                            <option value="">Select</option>
                                            @foreach($Bedrooms as $Bedroom)
                                                <option value="{{ $Bedroom->id }}">{{ $Bedroom->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right px-2" id="search" style="width: 110px">Search</button>
                            {{--@if($adminAuth->type<3) <a class="btn bg-gradient-info mx-sm-1 px-2 waves-effect waves-light float-sm-right float-left" style="width: 110px" href="{{route('off-plan-project.add.page')}}">Add</a> @endif--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row data-box">
        @foreach($projects as $row)
        <div class="col-sm-4 pb-2">
            <div class="card off-plan-project h-100 text-dark mb-0">
                <div class="position-relative">
                    <a href="/off-plan/brochure/{{\Helper::idCode($row->id)}}{{ (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) }}" target="_blank"><img class="card-img-top" src="{{$row->pictures}}"></a>
                    {!! $row->edit=='true'?'<a class="off-plan-btn-edit" target="_blank" href="/admin/off-plan-project-edit/'.\Helper::idCode($row->id).'" ><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>':'' !!}
                    {!! ($row->content_status=='3'?'<span class="coming-soon-tag">'.$row->content_status_name.'</span>':'') !!}
                    {!! ($row->developer?'<span class="developer-tag">'.$row->developer.'</span>':'') !!}
                </div>
                <a href="/off-plan/brochure/{{\Helper::idCode($row->id)}}{{ (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) }}" target="_blank" class="card-body text-dark">
                    <h5 class="card-title text-truncate">{{$row->project_name}}</h5>
                    <p class="card-text text-truncate mb-0">{{$row->master_project_name}}</p>
                    <div class="d-block mt-1">
                        <span class="float-left text-center">
                            <span>Completion Date</span><br>
                            <span>{{$row->date_of_completion}}</span>
                        </span>
                        <div class="float-right text-center">
                            <span>Starting Price</span><br>
                            <span>AED {{$row->starting_price}}</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center">
        <div  class="simple-pagination" id="page1"></div>
    </div>
@endsection
@section('vendor-script')
@endsection
@section('page-script')
    {{-- Page js files
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
{{--    <script src="/js/jquery.js"></script>--}}
    <script src="/js/scripts/jquery.simplePagination.js"></script>

    <script>
        $(document).ready(function(){

            let emirate='';
            let master_project='';
            //let community='';
            let developer='';
            let status='';
            let p_type='';
            let property_type='';
            let project_name='';
            let phpp='';
            let year='';
            let from_price='';
            let to_price='';
            let from_date_launch='';
            let to_date_launch='';
            let bedroom_from='';
            let bedroom_to='';

            let items={{$items}};
            $('#page1').pagination({
                items:items,
                itemsOnPage:18,
                pages:0,
                displayedPages:5,
                edges:3,
                useStartEdge:true,
                useEndEdge:true,
                currentPage:0,
                useAnchors:true,
                hrefTextPrefix:'#page',
                hrefTextSuffix:'',
                selectOnClick:true,

                cssStyle:'light-theme',
                onPageClick:function(pageNumber, event) {
                    let start=(pageNumber-1)*18;
                    getData(start);
                }

            });

            function getData(start){
                $('.data-box').html('');
                $.ajax({
                    url:"{{ route('off-plan-project.get.ajax') }}",
                    type:"POST",
                    data:{
                        '_token':$('meta[name="csrf-token"]').attr('content'),
                        'start':start,

                        'emirate':emirate,
                        'master_project':master_project,
                        //'community':community,
                        'developer':developer,
                        'status':status,
                        'p_type':p_type,
                        'property_type':property_type,
                        'project_name':project_name,
                        'phpp':phpp,
                        'year':year,
                        'from_price':from_price,
                        'to_price':to_price,
                        'from_date_launch':from_date_launch,
                        'to_date_launch':to_date_launch,
                        'bedroom_from':bedroom_from,
                        'bedroom_to':bedroom_to,
                    },
                    success:function (response) {
                        $('.data-box').html('');
                        $('.data-box').html(response);

                    },error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }

            $('#search').click(function (){
                //pagination.updateItems(100);

                emirate=$('#emirate').val();
                master_project=$('#master-project').val();
                //community=$('#community').val();
                developer=$('#select-developer').val();
                status=$('#status').val();
                p_type=$('#p_type').val();
                property_type=$('#property-type').val();
                project_name=$('#project-name').val();
                phpp=$('#phpp').val();
                year=$('#year').val();
                from_price=$('#from-price').val();
                to_price=$('#to-price').val();
                from_date_launch=$('#from-date-launch').val();
                to_date_launch=$('#to-date-launch').val();
                bedroom_from=$('#bedroom_from').val();
                bedroom_to=$('#bedroom_to').val();

                $.ajax({
                    url:"{{ route('off-plan-project.get.ajax') }}",
                    type:"POST",
                    data:{
                        '_token':$('meta[name="csrf-token"]').attr('content'),
                        'start':0,
                        'search':'1',
                        'emirate':emirate,
                        'master_project':master_project,
                        //'community':community,
                        'developer':developer,
                        'status':status,
                        'p_type':p_type,
                        'property_type':property_type,
                        'project_name':project_name,
                        'phpp':phpp,
                        'year':year,
                        'from_price':from_price,
                        'to_price':to_price,
                        'from_date_launch':from_date_launch,
                        'to_date_launch':to_date_launch,
                        'bedroom_from':bedroom_from,
                        'bedroom_to':bedroom_to,
                    },
                    success:function (response) {
                        $('#page1').pagination('updateItems', response.items);
                        $('#page1').pagination('selectPage', 1);
                        //$('.data-box').html(response.output);

                    },error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            });

            $('#emirate').change(function () {
                let val=$(this).val();
                getMasterProject(val);
            });

            //$('#emirate').val(2).change();

            function getMasterProject(val){
                $.ajax({
                    url:"{{ route('off-plan-project.get.ajax_mp') }}",
                    type:"POST",
                    data:{
                        _token:'{{ csrf_token() }}',
                        Emirate:val
                    },
                    success:function (response) {
                        $('#master-project').html(response);
                    }
                });
            }

            $('#master-project').change(function () {
                let val=$(this).val();
                //getCommunity(val);
                //$('#Community').change();
            });

            function getCommunity(val){
                $.ajax({
                    url:"{{ route('community.get.ajax') }}",
                    type:"POST",
                    data:{
                        _token:'{{ csrf_token() }}',
                        MasterProject:val
                    },
                    success:function (response) {
                        $('#community').html(response);
                    }
                });
            }

            $('#p_type').change(function(){
                getPropertyType();
            });

            getPropertyType();
            function getPropertyType(){
                let type=$('#p_type').val();
                $.ajax({
                    url:"{{ route('property-type.ajax.get') }}",
                    type:"POST",
                    data:{
                        _token:$('meta[name="csrf-token"]').attr('content'),
                        type:type
                    },
                    success:function (response) {
                        $('#property-type').html(response);
                    }
                });
            }

            // $('body').click(function (event) {
            //     //let r=$(event.target).is('.off-plan-btn-edit');
            //     if($(event.target).is('.off-plan-btn-edit')) {
            //         let href = $(this).data('href');
            //         alert(href);
            //     }
            // });

        });
    </script>

@endsection
