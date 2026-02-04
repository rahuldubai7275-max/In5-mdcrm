
@extends('layouts/contentLayoutMaster')

@section('title', 'Survey Report')

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
                            @if($adminAuth->type<3)
                                <div class="col-12 col-sm-6 col-lg-3">
                                    <fieldset class="form-group form-label-group">
                                        <label for="select-agent">Client Manager</label>
                                        <select class="form-control select2" id="select-admin">
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
                            @endif

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-note-subject">Survey For</label>
                                    <select class="form-control" id="select-note-subject">
                                        <option value="">Select</option>
                                        <option value="Viewing">Viewing</option>
                                        <option value="Appointment">Appointment</option>
                                    </select>
                                </fieldset>
                            </div>


                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <select class="select-2-contact form-control" id="select-contact"></select>
                                    <label for="SearchRepository">Contact</label>
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
                                    <label for="select-status">Status</label>
                                    <select class="form-control" id="select-status">
                                        <option value="">Select</option>
                                        <option value="1">Replied</option>
                                        <option value="0">Not Replied</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <fieldset class="form-group form-label-group">
                                    <label for="select-avg">Rate Avg</label>
                                    <select class="form-control" id="select-avg">
                                        <option value="">Select</option>
                                        <option value="1-2">Between 1 to 2 Stars</option>
                                        <option value="2-3">Between 2 to 3 Stars</option>
                                        <option value="3-4">Between 3 to 4 Stars</option>
                                        <option value="4-5">Between 4 to 5 Stars</option>
                                        <option value="5-5">5 Stars</option>
                                    </select>
                                </fieldset>
                            </div>

                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group validate">
                                    <label for="from-date">Date</label>
                                    <input type="text" id="from-date" autocomplete="off" class="form-control format-picker" placeholder="From">
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="form-group form-label-group rented-until-box">
                                    <label for="to-date">Date</label>
                                    <input type="text" id="to-date" autocomplete="off" class="form-control format-picker" placeholder="To">
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
            <h4 class="card-title">Survey Report</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <table class="table truncate-table datatable1 table-striped order-column dataTable">
                    <thead>
                    <tr>
                        <th>CM</th>
                        <th>Survey For</th>
                        <th>Property Ref</th>
                        <th>Contact</th>
                        <th>Avg Rate</th>
                        <th>Status</th>
                        <th>Time</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="surveyDetails" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <form method="post" class="modal-content" novalidate>
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
            scrollY: 430,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            "order": [[ 6, "desc" ]],
            "lengthMenu": [[10, 25, 50,100, -1], [10, 25, 50,100, "All"]],
            'ajax': {
                'type': 'post',
                'url': '{{ route('surveys.get.datatable') }}',
                'data': function(data){
                    // Read values

                    // Append to data
                    // data.contact = 'contacts';
                    data._token='{{csrf_token()}}';
                    data.property = $('#select-property').val();
                    data.contact = $('#select-contact').val();
                    data.subject = $('#select-note-subject').val();
                    data.admin = $('#select-admin').val();
                    data.status = $('#select-status').val();
                    data.avg = $('#select-avg').val();
                    data.from_date = $('#from-date').val();
                    data.to_date = $('#to-date').val();
                }

            },
            aoColumnDefs: [{bSortable: false,aTargets: [ 7 ]}],
            'columns': [
                {data: 'firstname'},
                {data: 'model'},
                {data: 'property_id'},
                {data: 'contact_id'},
                {data: 'avg'},
                {data: 'status'},
                {data: 'date_at'},
                {data: 'action'},
            ],
        });
        $('#search').click(function(){
            table.draw();
        });

        $('.datatable1').on('click','.survey-detail',function(){
            let survey=$(this).parent().data('id');

            $.ajax({
                url:"{{ route('surveys.details') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    survey:survey
                },
                success:function (response) {
                    $('#surveyDetails .modal-body').html(response);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });
    </script>

    <script>
        // var wallet_address = $("#copy_wallet_address_input");
        var btnCopy = $(".copy-link");

        // copy text on click
        // btnCopy.on("click", function () {
        $("body").on('click','.copy-link',function () {
            let id=$(this).parent().data('id');
            var dummy = document.createElement('input'),
                text = "{{request()->getSchemeAndHttpHost()}}/survey/"+id;

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
                // .on("select2:open", function(e) {
                // if ($(this).data('state') === 'unselected') {
                //     $(this).removeData('state');
                //     var self = $(this);
                //     setTimeout(function() {
                //         self.select2('close');
                //     }, 1);
                // }
            // });

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
@endsection
