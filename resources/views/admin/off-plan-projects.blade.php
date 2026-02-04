
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

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Developer Projects</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    @if($adminAuth->type<3) <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-light" href="{{route('off-plan-project.add.page')}}">Add</a></li> @endif
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table truncate-table datatable1 table-striped order-column dataTable">
                        <thead>
                        <tr>
                            <th>Master Project</th>
                            <th>Project</th>
                            <th>Developer</th>
                            <th>Property Type</th>
                            <th>Status</th>
                            <th>Launch Date</th>
                            <th>Completion Date</th>
                            <th>Added Date</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}<script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}
    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            "order": [[ 7, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('off-plan-project.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    data._token='{{csrf_token()}}';
                    //data.first_name=$('#first_name').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 8 ]}],
            'columns': [
                {data: 'master_project_id'},
                {data: 'community_id'},
                {data: 'developer_id'},
                {data: 'property_type_id'},
                {data: 'status'},
                {data: 'date_of_launch'},
                {data: 'date_of_completion'},
                {data: 'created_at'},
                {data: 'action'}
            ],
        });
        $('#search').click(function(){
            table.draw();
        });


        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {
                let brochure=$(this).parent().children('td').children('.action').data('brochure');
                // window.location.href ='/admin/contact/view/'+id
                if(brochure!=undefined) {
                    window.open('/off-plan/brochure/'+brochure+'{{ (($adminAuth->type!=2)? '?a='.\Helper::idCode($adminAuth->id) : '' ) }}');
                }
            }

        });
    </script>
@endsection
