
@extends('layouts/contentLayoutMaster')

@section('title', 'Company Profile')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-2">Company Profile</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="my_dashboard_review">
                <form method="post" action="{{route('companys.edit')}}" enctype="multipart/form-data">
                    @csrf
                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Company Name</span>
                        </div>
                        <div class="col-md-8">
                          <input value="{{($company)  ?  $company->name  :  ''}}" type="text" class="form-control" id="name" name="name">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>RERA ORN</span>
                        </div>
                        <div class="col-md-8">
                          <input value="{{($company)  ?  $company->rera_orn  :  ''}}" type="number" class="form-control" id="rera_orn" name="rera_orn">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>TRN</span>
                        </div>
                        <div class="col-md-8">
                          <input value="{{($company)  ?  $company->trn  :  ''}}" type="number" class="form-control" id="trn" name="trn">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Address</span>
                        </div>
                        <div class="col-md-8">
                          <input value="{{($company)  ?  $company->address  :  ''}}" type="text" class="form-control" id="address" name="address">
                        </div>
                    </fieldset>

                  <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Office Tel</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->office_tel  :  ''}}" type="number" class="form-control" id="office_tel" name="office_tel">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Office Fax</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->office_fax  :  ''}}" type="number" class="form-control" id="office_fax" name="office_fax">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Primary Email</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->primary_email :  ''}}" type="email" class="form-control" id="primary_email" name="primary_email">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Website</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->website  :  ''}}" type="text" class="form-control" id="website" name="website">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Facebook</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->facebook  :  ''}}" type="text" class="form-control" id="facebook" name="facebook">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Instagram</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->instagram  :  ''}}" type="text" class="form-control" id="instagram" name="instagram">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Tik tok</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->tiktok  :  ''}}" type="text" class="form-control" id="tiktok" name="tiktok">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Linkedin</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->linkedin  :  ''}}" type="text" class="form-control" id="linkedin" name="linkedin">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Youtube</span>
                        </div>
                        <div class="col-md-8">
                            <input value="{{($company)  ?  $company->youtube  :  ''}}" type="text" class="form-control" id="youtube" name="youtube">
                        </div>
                    </fieldset>

                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Company Profile</span>
                        </div>
                        <div class="col-md-8">
                            <textarea id="" cols="2" rows="8" class="form-control" name="company_profile">{{($company)  ?  $company->company_profile  :  ''}}</textarea>
                        </div>
                    </fieldset>

                    @if($company->bayut_integrate)
                    <fieldset class="form-group row">
                        <div class="col-md-4">
                            <span>Bayut XML Link</span>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-10">
                                    <input value="https://crm.mdcrms.com/api/properties/bd/{{ $company->id.'_'.$company->api_key }}" type="text" class="form-control" id="bayut-xml-link">
                                </div>
                                <div class="col-2">
                                    <a  id="btn-copy" href="javascript:void(0);" class="btn bg-gradient-info" title="Copy bayut XML link"><i class="fa fa-copy"></i></a>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    @endif

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row m-0 logo_box" style="padding: 7px;border: 1px solid #d9d9d9;border-radius: 5px;color: rgba(34, 41, 47, 1) !important;">
                                <div class="col-4">
                                    <div class="p-image" style="width: 80px;height: 80px;">
                                        <img id="logo_Preview" src="{{ ($company && $company->logo) ? '/storage/'.$company->logo : '/images/Default.png'}}" style="border-color: #ccc; border-radius: 0">
                                        <div class="profile-input" style="border-radius:0">
                                            <label for="logo"><i class="feather icon-camera font-large-2 text-white"></i></label>
                                            <input class="file-upload d-none" name="logo" type="file" id="logo" accept="image/">
                                        </div>
                                        <input type="hidden" id="logo_check" value="{{ ($company && $company->logo) ? $company->logo : ''}}">
                                    </div>
                                </div>
                                <div class="col-8 d-flex align-items-center">
                                    <label for="logo">Logo</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row m-0 watermark_box" style="padding: 7px;border: 1px solid #d9d9d9;border-radius: 5px;color: rgba(34, 41, 47, 1) !important;">
                                <div class="col-4">
                                    <div class="p-image" style="width: 80px;height: 80px;">
                                        <img id="watermark_Preview" src="{{ ($company && $company->watermark) ? '/storage/'.$company->watermark : '/images/Default.png'}}" style="border-color: #ccc; border-radius: 0">
                                        <div class="profile-input" style="border-radius:0">
                                            <label for="watermark"><i class="feather icon-camera font-large-2 text-white"></i></label>
                                            <input class="file-upload d-none" name="watermark" type="file" id="watermark" accept="image/">
                                        </div>
                                        <input type="hidden" id="watermark_check" value="{{ ($company && $company->watermark) ? $company->watermark : ''}}">
                                    </div>
                                </div>
                                <div class="col-8 d-flex align-items-center">
                                    <label for="watermark">Watermark</label>
                                </div>
                            </div>
                        </div>
                    </div>

                  <div class="col-xl-12 text-right mt-2">
                      <input type="hidden" name="_id" value="{{($company)  ?  $company->id  :  ''}}">
                    <button type="submit" name="update" value="1" class="btn bg-gradient-info waves-effect waves-light float-right">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
@endsection
@section('page-script')
    {{-- Page js files --}}
    <script>
        $("#logo").change(function () {
            ImagePreview(this,550000,['png'],'#logo_Preview');
        });
        $("#watermark").change(function () {
            ImagePreview(this,550000,['png'],'#watermark_Preview');
        });
    </script>

    <script>
        // var wallet_address = $("#copy_wallet_address_input");
        var btnCopy = $("#btn-copy");

        // copy text on click
        btnCopy.on("click", function () {
            //var dummy = document.createElement('input'),
            var dummy = document.getElementById('bayut-xml-link');

            //$('#link-input').removeClass('d-none');
            //document.body.appendChild(dummy);
            //dummy.value = txt;
            dummy.select();
            document.execCommand('copy');
            //document.body.removeChild(dummy);
            //$('#link-input').addClass('d-none');

            toastr.success('Link Copied');
        });

        function close_window() {
            //if (confirm("Close Window?")) {
            close();
            //}
        }
    </script>
@endsection
