
@extends('layouts/contentLayoutMaster')

@section('title', 'Deal Tracking')

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
                <h4 class="card-title">Deal Track</h4>
            </div>
            <div class="card-content collapse show">
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <fieldset class="form-group form-label-group">
                                    <label for="deal_model">Deal Model</label>
                                    <select class="form-control" id="deal_model" name="deal_model">
                                        <option value="">Select</option>
                                        @php
                                        $deal_model=\App\Models\DealModel::get();
                                        @endphp
                                        @foreach($deal_model as $row)
                                            <option value="{{ $row->id }}">{{ $row->title }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>
                            </div>
                            <div class="col-sm-3">
                                <a href="#trackingAdd" data-toggle="modal" class="btn btn-primary btn-add-tracking w-100 d-none">Create</a>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush sort-element mt-2" id="tracking-step-box">

                        </ul>

                        <div class="mt-2 pt-2 email-content-box d-none">
                            <div class="form-group form-label-group">
                                <label for="email-content">Email Content</label>
                                <textarea class="form-control" id="email-content" rows="5"></textarea>
                            </div>
                            <div class="clearfix">
                                <div class="float-lg-left"><span class="text-danger">$#TrackingLink</span> <span class="text-primary">will be amended to the tracking link.</span></div>
                                <button type="button" class="btn btn-primary float-lg-right mb-2" id="save-email-content">Save Email Content</button>
                            </div>
                        </div>
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
                                <label for="step-title">Title</label>
                                <input class="form-control" id="step-title" placeholder="Title">
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

@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script type="text/javascript" src="/js/scripts/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js" integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $('#add-step-tracking').click(function () {
            let title=$('#step-title').val();
            $('#add-step-tracking').attr('disabled','disabled');
            $.ajax({
                url:"{{ route('deal-tracking-default.add') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    deal_model:$('#deal_model').val(),
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
                url:"{{ route('deal-tracking-default.edit') }}",
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

        $('.btn-add-tracking').click(function () {
            $('#trackingAdd .modal-title').html('Add tracking Step');
            $('#step-title').val('');
            $('#edit-step-tracking').addClass('d-none');
            $('#add-step-tracking').removeClass('d-none');
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
                        url:"{{ route('deal-tracking-default.delete') }}",
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

        getTracking();
        function getTracking() {
            $('#tracking-step-box').html('');
            $.ajax({
                url:"{{ route('deal-tracking-default.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    deal_model:$('#deal_model').val()
                },
                success:function (response) {
                    $('#tracking-step-box').html(response);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        getEmailContent();
        function getEmailContent() {
            $('#email-content').val('');
            $.ajax({
                url:"{{ route('deal-model-email.get') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    _id:$('#deal_model').val()
                },
                success:function (response) {
                    $('#email-content').val(response.email_content);
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        $('#save-email-content').click(function () {
            $.ajax({
                url:"{{ route('deal-model-email.edit') }}",
                type:"POST",
                data:{
                    _token:$('meta[name="csrf-token"]').attr('content'),
                    _id:$('#deal_model').val(),
                    email_content:$('#email-content').val()
                },
                success:function (response) {
                    toastr.success('save');
                }, error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        $('#deal_model').change(function () {
            let val=$(this).val();
            $('.btn-add-tracking , .email-content-box').addClass('d-none');
            if(val!=''){
                $('.btn-add-tracking , .email-content-box').removeClass('d-none');
            }
            getTracking();
            getEmailContent();
        });
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
                    url:"{{ route('deal-tracking-default.row') }}",
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
@endsection
