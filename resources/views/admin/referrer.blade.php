
@extends('layouts/contentLayoutMaster')

@section('title', 'Referrers')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
    <link rel="stylesheet" href="/js/scripts/build/css/intlTelInput.css">
@endsection

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
            <h4 class="card-title">Recommenders</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li><a href="#ModalReferrer" data-toggle="modal" class="btn bg-gradient-info py-1 px-2 waves-effect waves-light btn-add-referrer">Add Recommender</a></li>
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                <div class="table-responsive">
                    <table class="table table-striped zero-configuration truncate-table">
                        <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>phone Number</th>
                            <th>Email</th>
                            <th>Country</th>
                            <th>City</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $adminAuth = \Auth::guard('admin')->user();
                            $referrers=\App\Models\Referrer::where('admin_id',$adminAuth->id)->get();
                        @endphp

                        @foreach($referrers as $row)
                            <tr>
                            <td>{{$row->name}}</td>
                            <td>{{$row->phone_number}}</td>
                            <td>{{$row->email}}</td>
                            <td>{{$row->country}}</td>
                            <td>{{$row->city}}</td>
                            <td>
                                <div class="d-flex action font-medium-3" data-id="{{$row->id}}" data-model="{{route('referrer.delete')}}" data-name="{{$row->name}}" data-pnumber="{{$row->phone_number}}" data-email="{{$row->email}}" data-country="{{$row->country}}" data-city="{{$row->city}}">
                                    <a href="#ModalReferrer" data-toggle="modal" class="edit-record" title="Edit"><i class="users-edit-icon feather icon-edit-1 mr-50"></i></a>
                                    <a href="javascript:void(0)" class="delete" title="delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
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

    <!-- Modal Add -->
    <div class="modal fade" id="ModalReferrer" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
            <form method="post" action="{{ route('referrer.add') }}" id="record-form" class="modal-content" novalidate>
                {!! csrf_field() !!}
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Recommender</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-6">
                            <div class="form-group form-label-group">
                                <input type="text" id="name" class="form-control" placeholder="Full Name" name="name" required="required">
                                <label for="name">Full Name <span>*</span></label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group form-label-group">
                                <input type="text" id="phone_number" class="form-control country-code" placeholder="phone Number" name="phone_number">
                                <label for="phone_number">phone Number</label>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group form-label-group">
                                <input type="text" id="email" class="form-control" placeholder="Email" name="email">
                                <label for="email">Email</label>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <fieldset class="form-group form-label-group">
                                <label for="country">Country</label>
                                <select class="form-control select2" id="country" name="country">
                                    <option value="">Select</option>
                                </select>
                            </fieldset>
                        </div>

                        <div class="col-sm-6">
                            <div class="form-group form-label-group">
                                <input type="text" id="city" class="form-control" placeholder="City" name="city">
                                <label for="city">City</label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-12 record-action-box">
                        <button type="submit" class="btn btn-primary mr-1 mb-1 btn-create float-right" value="submit">Add</button>
                        <div class="update-btn-box d-none float-right">
                            <button type="reset" class="btn btn-primary mr-1 mb-1">Cancel</button>
                            <input type="hidden" name="update">
                            <button type="submit" class="btn btn-primary mr-1 mb-1" value="submit">Update</button>
                        </div>
                    </div>
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
    <script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>
    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/build/js/intlTelInput.min.js"></script>
    <script>
        populateCountries("country", "");

        var ActionAdd=$('#record-form').attr('action');

        $('#search').click(function(){
            table.draw();
        });

        $('body').on('click','.edit-record',function () {
            var ActionEdit = "{{route('referrer.edit')}}";
            $('#record-form').attr('action',ActionEdit);

            $('#name').val($(this).parent().data('name'));
            $('#phone_number').val($(this).parent().data('pnumber'));
            $('#email').val($(this).parent().data('email'));
            $('#country').val($(this).parent().data('country'));
            $('#city').val($(this).parent().data('city'));
            $('#record-form input[name=update]').val($(this).parent().data('id'));


            $('.btn-create').addClass('d-none');
            $('.record-action-box .update-btn-box').removeClass('d-none');
        });
        $('#record-form :reset').click(function () {
            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });
        $('.btn-add-referrer').click(function () {
            $('#name , #phone_number , #email, #country , #city').val('').change();

            $('#record-form').attr('action',ActionAdd);
            $('.btn-create').removeClass('d-none');
            $('.record-action-box .update-btn-box').addClass('d-none');
        });

        $(".country-code").intlTelInput({
            // allowDropdown: false,
            autoHideDialCode: false,
            // autoPlaceholder: "off",
            dropdownContainer: "body",
            // excludeCountries: ["us"],
            formatOnDisplay: false,
            // geoIpLookup: function(callback) {
            //     $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //         var countryCode = (resp && resp.country) ? resp.country : "";
            //         callback(countryCode);
            //     });
            // },
            hiddenInput: "full_number",
            initialCountry: "auto",
            nationalMode: false,
            // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
            // placeholderNumberType: "MOBILE",
            preferredCountries: ['ae'],
            // separateDialCode: true,
            utilsScript: "js/build/js/utils.js"
        });

    </script>


@endsection
