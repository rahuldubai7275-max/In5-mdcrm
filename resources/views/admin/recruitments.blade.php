
@extends('layouts/contentLayoutMaster')

@section('title', 'Recruitment')

@section('vendor-style')
    {{-- vendor css files --}}
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.css" rel="stylesheet">
    <link href="/css/dataTables-custom.css" rel="stylesheet">

@endsection
@php
    $adminAuth = Auth::guard('admin')->user();

@endphp
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Filters</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                    <li><a data-action="close"><i class="feather icon-x"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body">
                <div class="row mt-1">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label for="select-job-title">Applying For</label>
                            <select class="form-control select2" id="select-job-title">
                                <option value="">Select</option>
                                @php
                                    $JobTitles=\App\Models\JobTitle::get()
                                @endphp
                                @foreach($JobTitles as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender">
                                <option value="">Select</option>
                                @foreach(GENDER as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="education_level">Education Level</label>
                            <select class="form-control" id="education_level">
                                <option value="">Select</option>
                                @foreach(EducationLevel as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="languages">Languages</label>
                            <select class="form-control select2" id="languages">
                                <option value="">Select</option>
                                @php
                                    $languages=\App\Models\Language::orderBy('name','ASC')->get();
                                @endphp
                                @foreach($languages as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <fieldset class="form-group form-label-group">
                            <label>Nationality</label>
                            <select class="form-control select2" id="nationally" >
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="form-group form-label-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Name">
                        </div>
                    </div>


                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <label for="from_expected_salary">Salary</label>
                                    <input type="text" class="form-control number-format" id="from_expected_salary" placeholder="From">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group form-label-group">
                                    <label for="to_expected_salary">Salary</label>
                                    <input type="text" class="form-control number-format" id="to_expected_salary" placeholder="To">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group form-label-group validate">
                                    <label for="from-date">Added Date</label>
                                    <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group form-label-group rented-until-box">
                                    <label for="to-date">Added Date</label>
                                    <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right" id="search">Search</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title d-none d-md-block">Recruitment</h5>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights" href="{{route('recruitment.add.page')}}">Add</a></li>
                    <li><a class="btn bg-gradient-info py-1 px-2 waves-effect waves-lights" id="copy-link" data-link="{{route('recruitment.form')}}" href="javascript:void(0);">Copy Link</a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Expected Salary</th>
                        <th>Commission %</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Applying For</th>
                        <th>Languages</th>
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
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.8/fc-5.0.4/datatables.min.js"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
    <script src="/js/scripts/countries.js"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        populateCountries("nationally", "");
    </script>
    <script>

        var table=$('.datatable1').DataTable({
            fixedColumns: {
                start: 2
            },
            scrollX: true,
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[7, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('recruitment.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    data.job_title=$('#select-job-title').val();
                    data.education_level=$('#education_level').val();
                    data.languages=$('#languages').val();
                    data.nationally=$('#nationally').val();
                    data.name=$('#name').val();
                    data.from_expected_salary=$('#from_expected_salary').val();
                    data.to_expected_salary=$('#to_expected_salary').val();
                    data.gender=$('#gender').val();
                    data.from_date=$('#from-date').val();
                    data.to_date=$('#to-date').val();
                    data._token='{{csrf_token()}}';
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 6,8 ]}],
            'columns': [
                {data: 'first_name'},
                {data: 'expected_salary'},
                {data: 'commission_percent'},
                {data: 'mobile_number'},
                {data: 'email'},
                {data: 'name'},
                {data: 'language'},
                {data: 'created_at'},
                {data: 'action'}

            ],
        });
        $('#search').click(function(){
            table.draw();
        });


        $('#submit').click(function(){
            let error=0;
            let commission_percent=$('#commission_percent').val();
            if(commission_percent>100){
                $('#commission_percent').parent().addClass('error');
                error=1;
            }
            if(error==0){
                $('#recruimentCreate button[name="submit"]').click();
            }
        });

        $('body .datatable1 tbody').on('click','tr td',function(){
            let html=$(this).children('.action').html();
            if(!html)
                html=$(this).children('.checkbox').html();

            if (!html) {
                let id=$(this).parent().children('td').children('.action').data('id');
                // window.location.href ='/admin/property/view/'+id
                if(id!=undefined) {
                    window.open('/admin/recruitment-view/' + id);
                }
            }
        });

        var btnCopy = $("#copy-link");

        // copy text on click
        btnCopy.on("click", function () {
            var dummy = document.createElement('input'),
                text = $("#copy-link").data('link');

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
