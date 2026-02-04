
@extends('layouts/contentLayoutMaster')

@section('title', 'Activities Report')

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
                    <form>
                        <div class="row mt-1">
                            @php
                            $disabled='disabled';
                            if($adminAuth->type<3 || $adminAuth->type==5 || $adminAuth->type==6)
                                $disabled='';
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-agent">Client Manager</label>
                                    <select class="form-control select2" id="select-admin" {{$disabled}}>
                                        <option value="">Select</option>
                                        @php
                                            $Agents=\Helper::getCM_DropDown_list('0');
                                        @endphp
                                        @foreach($Agents as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->firstname.' '.$agent->lastname }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <select class="select-2-property form-control" id="select-property"></select>
                                    <label for="SearchRepository">Property</label>
                                </fieldset>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <select class="select-2-contact form-control" id="select-contact"></select>
                                    <label for="SearchRepository">Contact</label>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="select-type">Property / Contact</label>
                                            <select class="form-control" id="select-type">
                                                <option value="">Select</option>
                                                <option value="property">Property</option>
                                                <option value="contact">Contact</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-sm-6">
                                        <fieldset class="form-group form-label-group">
                                            <label for="select-note-cancelled">Activity Status</label>
                                            <select class="form-control" id="select-note-cancelled">
                                                <option value="">Select</option>
                                                <option value="1">Done</option>
                                                <option value="3">Upcoming</option>
                                                <option value="2">Cancelled</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-note-subject">Activity Type</label>
                                    <select class="form-control" id="select-note-subject">
                                        <option value="">Select</option>
                                        @foreach(NoteSubject as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-label-group form-group">
                                    <select class="form-control select-2-off-plan-project" id="select-developer-project">

                                    </select>
                                    <label for="DeveloperProject">Developer Projects</label>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group validate">
                                            <label for="from-date">Date-Time</label>
                                            <input type="text" id="from-date-at" autocomplete="off" class="form-control format-picker" placeholder="From">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group form-label-group rented-until-box">
                                            <label for="to-date">Date-Time</label>
                                            <input type="text" id="to-date-at" autocomplete="off" class="form-control format-picker" placeholder="To">
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
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Activities Report</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard pt-3">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>Contact / Property</th>
                        <th>CM</th>
                        <th>Ref / Name</th>
                        <th>Activity Type</th>
                        <th>For</th>
                        <th>Status</th>
                        <th>Feedback / Note</th>
                        <th>Date - Time</th>
                        <th>Added Date</th>
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
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        var table=$('.datatable1').DataTable({
            // dom: 'Bflrtip',
            // buttons: [ 'copy', 'csv', 'excel', 'pdf', 'print' ],
            fixedColumns: {
                start: 2
            },
            scrollX: true,
            scrollY: 370,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 8, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('activities.get.datatable') }}',
                'data': function(data){
                    // Read values
                    // var UserType = $('#MemberType').val();
                    // var Country = $('#Country').val();

                    // Append to data
                    // data.contact = 'contacts';
                    data._token='{{csrf_token()}}';
                    @if($adminAuth->type>2 && $adminAuth->type!=5 && $adminAuth->type!=6)data.agent = '{{$adminAuth->id}}'; @endif
                    data.property = $('#select-property').val();
                    data.contact = $('#select-contact').val();
                    data.type = $('#select-type').val();
                    data.note_subject = $('#select-note-subject').val();
                    data.cancelled = $('#select-note-cancelled').val();
                    data.off_plan_project = $('#select-developer-project').val();
                    data.admin = $('#select-admin').val();
                    data.from_date = $('#from-date').val();
                    data.to_date = $('#to-date').val();
                    data.from_date_at = $('#from-date-at').val();
                    data.to_date_at = $('#to-date-at').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 2,4,5 ]}],
            'columns': [
                {data: 'type'},
                {data: 'firstname'},
                {data: 'created_for'},
                {data: 'note_subject'},
                {data: 'contact_property'},
                {data: 'status'},
                {data: 'note'},
                {data: 'date_at'},
                {data: 'created_at'},
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('body').on('click','td .note',function() {
            $('#ViewModal .modal-title').html( $(this).data('title') );
            $('#ViewModal .modal-body').html( $(this).data('desc') );
        });

        @if($disabled!='')
        $('#select-admin').val('{{$adminAuth->id}}').attr('disabled','disabled').change();
        @endif
    </script>

    <script>
        propertySelcet2();
        function propertySelcet2(SelectType=false) {
            // Loading remote data
            $(".select-2-property").select2({
                dropdownAutoWidth: true,
                width: '100%',
                allowClear: true,
                multiple:SelectType,
                ajax: {
                    url: "{{ route('property.ajax.select') }}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Property',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepoProperty,
                templateSelection: formatRepoSelectionProperty,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            }).on("select2:unselecting", function(e) {
                $(this).data('state', 'unselected');
                $(this).empty();//.append('<option selected value="">Select</option>');
                $(this).select2('data', {
                    id: '',
                    label:'Select'
                });
                e.preventDefault();
            });

        }

        function formatRepoProperty (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="w-100 ml-1">
                                <div><b>${repo.ref}</b></div>
                                <div>${repo.address}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelectionProperty (repo) {
            return repo.ref || repo.text ;

        }
    </script>

    <script>
        contactSelcet2();
        function contactSelcet2(SelectType=false) {
            // Loading remote data
            $(".select-2-contact").select2({
                dropdownAutoWidth: true,
                width: '100%',
                allowClear: true,
                multiple:SelectType,
                ajax: {
                    url: "{{route('contact.ajax.select')}}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Contact',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepoContact,
                templateSelection: formatRepoSelectionContact,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            }).on("select2:unselecting", function(e) {
                $(this).data('state', 'unselected');
                $(this).empty();//.append('<option selected value="">Select</option>');
                $(this).select2('data', {
                    id: '',
                    label:'Select'
                });
                e.preventDefault();
            });

        }

        function formatRepoContact (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="image-box"><img src="${repo.picutre}" /></div>
                            <div class="w-100 ml-1">
                                <div><b>${repo.fullname}</b></div>
                                <div>${repo.main_number}</div>
                                <div>${repo.email}</div>
                            </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            markup += '</div></div>';

            return markup;
        }

        function formatRepoSelectionContact (repo) {
            return repo.fullname || repo.text ;

        }
    </script>
    <script>
        offPlanSelect2();
        function offPlanSelect2(SelectType=false) {
            // Loading remote data
            $(".select-2-off-plan-project").select2({
                dropdownAutoWidth: true,
                width: '100%',
                multiple:SelectType,
                ajax: {
                    url: "{{ route('off-plan.ajax.select') }}",
                    dataType: 'json',
                    type:'POST',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            page: params.page,
                            _token:'{{csrf_token()}}'
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used

                        //   params.page = params.page || 1;

                        return { results: data };

                        //   return {
                        //     results: data.items,
                        //     pagination: {
                        //       more: (params.page * 30) < data.total_count
                        //     }
                        //   };
                    },
                    cache: true
                },
                placeholder: 'Property Information',
                minimumResultsForSearch: Infinity,
                templateResult: offPlanFormatRepo,
                templateSelection: offPlanFormatRepoSelection,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function offPlanFormatRepo (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                                <div class="d-flex align-items-center">
                                    <div class="ml-1">
                                        <div><b>${repo.project_name}</b></div>
                                        <div>${repo.master_project}</div>
                                    </div>
                                </div>
                           </div>`;

            // if (repo.description) {
            // markup += '<div class="mt-2">' + repo.affiliation + '</div>';
            // }

            //markup += '</div></div>';

            return markup;
        }

        function offPlanFormatRepoSelection (repo) {
            return repo.ref ||  repo.project_name;

        }
    </script>
@endsection
