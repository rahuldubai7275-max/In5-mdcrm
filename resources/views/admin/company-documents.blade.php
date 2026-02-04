
@extends('layouts/contentLayoutMaster')

@section('title', $page_name)

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
@endphp
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{$page_title}}</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li class="d-none d-md-inline-block"><a data-action="expand"><i class="feather icon-maximize"></i></a></li>
                    <li class="d-none d-md-inline-block"><a data-action="collapse"><i class="feather icon-chevron-down"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="card-content collapse show">
            <div class="card-body card-dashboard">
                @if($adminAuth->type<3)
                <div>
                    <div class="text-left">
                        <a href="#ModalTaregt" data-toggle="modal" class="btn btn-primary mb-1 waves-effect waves-light">Add New Document</a>
                    </div>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped dataex-html5-selectors datatable1 truncate-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Date Added</th>
                            <th>File Size</th>
                            <th>Added By</th>
                            <th>File Type</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($companyDocuments as $row)
                            @php
                            $admin=\App\Models\Admin::where('id',$row->admin_id)->first();
                            $fileType= explode(".",$row->docname) ;
                            @endphp
                            <tr>
                                <td title="{{$row->name}}">{{$row->name}}</td>
                                <td>{{($row->category) ? CompanyDocCat[$row->category] : ''}}</td>
                                <td>{{$row->created_at}}</td>
                                <td>{{ ($row->docname) ? \Helper::size_as_kb( \Illuminate\Support\Facades\Storage::size('public/images/'.$row->docname) ): '' }}</td>
                                <td>{{$admin->firstname.' '.$admin->lastname}}</td>
                                <td>{{($row->docname) ? strtoupper($fileType[1]) : ''}}</td>
                                <td>
                                    <div data-id="{{ $row->id }}" data-model="{{ route('company-document.delete') }}" class="action font-medium-2 d-flex">
                                        <a href="/storage/{{$row->docname}}" target="_blank" title="Download"><i class="fa fa-cloud-download"></i></a>
                                        @if($adminAuth->type==1)
                                        <a href="javascript:void(0)" class="delete mx-1" title="Delete"><i class="users-delete-icon feather icon-trash-2"></i></a>
                                        @endif
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

    <div class="modal fade" id="ModalTaregt" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
             role="document">
            <form method="post" id="record-form" class="modal-content" novalidate="">
                <input type="hidden" name="_token" value="j03Fdv25LTwWzOiYPEgHwKHuVGWTON3PXwCrc0l6">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">{{$page_title}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-lg-6 col-xl-6">
                            <fieldset class="form-group">
                                <label for="document_type">Category</label>
                                <select class="form-control" id="document_type">
                                    <option value="">Select</option>
                                    @foreach(CompanyDocCat as $key=>$value)
                                        @if($type==2 && ($key!=1 && $key!=2 && $key!=12))
                                            @continue
                                        @endif
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-6 col-xl-6">
                            <fieldset class="form-group">
                                <label for="document_name">File Name</label>
                                <input type="text" class="form-control" id="document_name">
                            </fieldset>
                        </div>
                        <div class="col-lg-6 col-xl-6">
                            <fieldset class="form-group">
                                <label for="file">File</label>
                                <input type="file" class="form-control" id="document_file">
                            </fieldset>
                        </div>
                    </div>
                </div>
                <div class="modal-footer row m-0">
                    <div class="col-sm-8 m-0">
                        <div class="progress progress-bar-primary progress-xl d-none w-100">
                            <div class="progress-bar bg-teal progress-bar-striped doc-upload-progress-bar" role="progressbar"
                                 aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 text-right m-0">
                        <button type="button" class="btn btn-primary btn-doc-upload waves-effect waves-light" value="submit">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="post" action="{{ route('company-document.add') }}" class="d-none">
        @csrf
        <input type="hidden" name="type" value="{{$type}}">
        <div class="doc-box"></div>
        <button type="submit" id="doc-submit"></button>
    </form>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}
    {{--<script src="{{ asset(mix('js/scripts/datatables/datatable.js')) }}"></script>--}}

    <script>
        var table=$('.datatable1').DataTable();
    </script>

    <script> //document upload
        let ProgressBar='';
        let InputAttachDocument='';

        $('.btn-doc-upload').click(function(){
            var Action="{{ route('upload-file') }}";
            var token='{{ csrf_token() }}';
            ProgressBar='.doc-upload-progress-bar';
            InputAttachDocument='doc-input';
            uploadDocument(Action,token);
        });

        function uploadDocument(Action,token) {
            var file = _('document_file').files[0];
            // alert(file.name+" | "+file.size+" | "+file.type+" | "+file.name.split('.').pop());

            if(file.size>2000000){
                Warning('Warning!',"The size of the file is "+formatBytes(file.size)+" , The maximum allowed upload file size is 2 MB");
                $('#document_file').val(null);
                return;
            }

            if(file.name.split('.').pop()=="pdf" ||
                file.name.split('.').pop()=="doc" ||
                file.name.split('.').pop()=="docx" ||
                file.name.split('.').pop()=="xlsx" ||
                file.name.split('.').pop()=="xml" ||
                file.name.split('.').pop()=="xls" ||
                file.name.split('.').pop()=="jpg" ||
                file.name.split('.').pop()=="jpeg" ||
                file.name.split('.').pop()=="webp" ||
                file.name.split('.').pop()=="png"){
                var formdata = new FormData();
                formdata.append("AttachDocumentSubmit", "0");
                formdata.append("_token", token);
                formdata.append("DocumentFile", file);
                var ajax = new XMLHttpRequest();
                ajax.upload.addEventListener("progress", documentProgressHandler, false);
                ajax.addEventListener("load", documentCompleteHandler, false);
                ajax.addEventListener("error", errorHandler, false);
                ajax.addEventListener("abort", abortHandler, false);
                ajax.open("POST", Action);
                ajax.send(formdata);

            }else{
                Swal.fire({
                    title: 'The format is not supported.',
                    text: "Supported files (pdf, doc, docx, xlsx, xml, xls, jpg, jpeg, webp, png)",
                    type: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Cancel',
                    confirmButtonText:'Yes',
                    confirmButtonClass: 'btn btn-primary',
                    cancelButtonClass: 'btn btn-danger ml-1',
                    buttonsStyling: false,
                });
                $('.btn-doc-upload').removeAttr('disabled').removeAttr('title').html('Upload');
            }

        }

        function _(el) {
            return document.getElementById(el);
        }

        function documentProgressHandler(event) {
            $(ProgressBar).parent().removeClass("d-none");
            $('.btn-doc-upload').attr('disabled', 'disabled').html('Please wait...');
            var percent = (event.loaded / event.total) * 100;
            $(ProgressBar).css({"width": Math.round(percent) + "%"});
            $(ProgressBar).html(Math.round(percent) + "%");
        }

        function documentCompleteHandler(event) {
            // var FileName = event.target.responseText;
            var response = jQuery.parseJSON( event.target.responseText );
            $(ProgressBar).html("Upload successfully");
            // $('#AttachDocumentBtn').addClass('d-none');
            // $("#ArticleFile").val('');
            // $(InputAttachDocument).val(response.name);
            // $(InputAttachDocument).removeClass('hide');
            $('.btn-doc-upload').removeAttr('disabled').removeAttr('title').html('Upload');

            $('.doc-box').append(`
            <div class="doc-item">
            <input type="hidden" name="docname" value="${response.name}">
            <input type="hidden" name="category" value="${$("#document_type").val()}">
            <input type="hidden" name="name" value="${$('#document_name').val()}">
            <div class="media">
                <a class="media-left align-self-center" href="#">
                    <img src="${response.link}" height="64" width="64">
                </a>
                <div class="media-body pl-1">
                    <h5 class="media-heading">${$("#document_type option:selected").text()}</h5>
                    <p class="mb-0">${$('#document_name').val()}</p>
                </div>
            </div>
            </div>
            `);
            $("#document_type option:selected").val('');
            $('#document_name').val('');
            $('#document_file').val('');
            $(ProgressBar).parent().addClass("d-none");
            $('#ModalTaregt').modal('hide');
            $('#doc-submit').click();
        }

        function errorHandler(event) {
            alert('Upload Failed');
            //_("status").innerHTML = "Upload Failed";
        }

        function abortHandler(event) {
            alert('Upload Aborted');
            // _("status").innerHTML = "Upload Aborted";
        }

    </script>
@endsection
