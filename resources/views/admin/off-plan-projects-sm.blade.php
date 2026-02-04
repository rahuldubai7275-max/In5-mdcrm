
@extends('layouts/contentLayoutMaster')

@section('title', 'Developer Projects')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')
    @if (Session::has('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{!!  Session::get('error')  !!}</li>
            </ul>
        </div>
    @endif
    @if($adminAuth->type<3)
        <div class="card action-card">
            <div class="card-content collapse show">
                <div class="card-body card-dashboard p-1">
                    <div class="row">
                        <div class="col-12">
                            <a class="btn bg-gradient-info py-1 px-2 waves-effect waves-light w-100" href="{{route('off-plan-project.add.page')}}">Add</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div id="data-box"></div>
    <div id="marker-end"></div>

    <div data-v-8314f794="" class="btn-scroll-to-top"><button data-v-8314f794="" type="button" class="btn btn-icon btn-primary" style="position: relative;"><svg data-v-8314f794="" xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-up"><line data-v-8314f794="" x1="12" y1="19" x2="12" y2="5"></line><polyline data-v-8314f794="" points="5 12 12 5 19 12"></polyline></svg></button></div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}

@endsection
@section('page-script')
    {{-- Page js files--}}
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="/js/scripts/jquery.lazyloadxt.js"></script>

    <script>
        let start = 0;
        let scrollPosition = 0;
        let selectItem='';
        let search=0;

        $('#marker-end').on('lazyshow', function () {
            if(search===1){
                start  = start + 3;
                search=0;
            }
            getData();
            start  = start + 3;
            $(window).lazyLoadXT();
            $('#marker-end').lazyLoadXT({visibleOnly: false, checkDuplicates: false});
        }).lazyLoadXT({visibleOnly: false});

        function getData() {
            $.ajax({
                url: '{{ route('off-plan-project.get.data-sm') }}',
                type: "POST",
                data: {
                    '_token': $('form input[name="_token"]').val(),
                    'start': start,
                },
                success: function (response) {
                    let obj = JSON.parse(response);
                    if(start===0)
                        $('#data-box').html(obj.aaData);
                    else
                        $('#data-box').append(obj.aaData);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        $('#search').click(function(){
            search=1;
            start=0;
            getData();
        });

        $('body').on('click','#data-box .hold-box',function(){
            if(!$('#data-box').hasClass('selected-lest')) {
                let brochure=$(this).data('brochure');
                window.open('/off-plan/brochure/'+brochure+'{{ (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) }}');
            }
        });
    </script>
@endsection
