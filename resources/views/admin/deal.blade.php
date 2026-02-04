@extends('layouts/contentLayoutMaster')

@section('title', 'New Deal')

@section('page-style')
    <!-- Page css files -->
    <style>
        .position-relative .form-control {
            padding-right: calc(1.25em + 1rem + 1px) !important;
        }
    </style>
@endsection
@section('content')

@php
    $admin = Auth::guard('admin')->user();

    $Company=App\Models\Company::find($admin->company_id);

    $Agents=\Helper::getCM_DropDown_list('1');
    $dealAgents='';
    $countDealAgent=0;
    $dealDocuments=[];
    if($deal){
        $admin=App\Models\Admin::where('id',$deal->admin_id)->first();
        $dealAgents=App\Models\DealAgent::where('deal_id',$deal->id)->orderBy('id', 'asc')->get();
        $countDealAgent=count($dealAgents);

        $dealDocuments=App\Models\DealDocument::where('deal_id',$deal->id)->orderBy('id', 'asc')->get();
    }
@endphp

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <form class="row" method="post" action="{{ ($deal) ? route('deal.edit') : route('deal.add') }}" novalidate>
                @csrf
                <div class="col-lg-7 col-xl-7">
                    <div class="row">
                        <div class="col-lg-6 col-xl-6">
                            <h6 class="text-primary ">Deal Details</h6>
                            <div class="row custom-scrollbar pt-2" style="max-height: 400px;">
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="deal_date">Deal Date</label>
                                        <input type="text" id="deal_date" name="deal_date" autocomplete="off" class="form-control format-picker picker__input" value="{{ ($deal) ? $deal->deal_date : '' }}" placeholder="Deal Date" readonly required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <select class="select-2-property form-control" name="property" id="property" required></select>
                                        <label for="SearchRepository">Property Information</label>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                     <fieldset class="form-group form-label-group">
                                        <select class="select-2-contact form-control" name="contact" id="contact" required></select>
                                        <label for="SearchRepository">Contact Information</label>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="type">Transaction Type</label>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="">Select</option>
                                            <option value="1">Rental</option>
                                            <option value="2">Sales</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-sm-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="deal_model">Deal Model</label>
                                        <select class="form-control" id="deal_model" name="deal_model" required>
                                            <option value="">Select</option>
                                            @php
                                                $deal_model=\App\Models\DealModel::get();
                                            @endphp
                                            @foreach($deal_model as $row)
                                                <option value="{{ $row->id }}" {!!  ($row->id==5) ? 'class="d-none"' : ''  !!}>{{ $row->title }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="created_by">Created By</label>
                                        <input type="text" class="form-control" id="created_by" name="created_by" value="{{$admin->firstname.' '.$admin->lastname}}"  disabled>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="tenancy_contract_start_date">Tenancy Contract Starting Date</label>
                                        <input type="text" id="tenancy_contract_start_date" name="tenancy_contract_start_date" autocomplete="off" class="form-control format-picker picker__input" value="{{ ($deal) ? $deal->tenancy_contract_start_date : '' }}" placeholder="Tenancy Contract Starting Date" readonly>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="tenancy_renewal_date">Tenancy Contract Renewal Date</label>
                                        <input type="text" id="tenancy_renewal_date" name="tenancy_renewal_date" autocomplete="off" class="form-control format-picker picker__input" value="{{ ($deal) ? $deal->tenancy_renewal_date : '' }}" placeholder="Tenancy Contract Renewal Date" readonly>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="cheques">Cheques</label>
                                        <select class="form-control" id="cheques" name="cheques">
                                            <option value="">Select</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="set_reminder">Set Reminder</label>
                                        <select class="form-control" id="set_reminder" name="set_reminder" required>
                                            <option value="1">Never</option>
                                            <option value="7">100 Days in advance</option>
                                            <option value="6">3 Months in advance</option>
                                            <option value="5">2 Months in advance</option>
                                            <option value="4">1 Month in advance</option>
                                            <option value="3">1 Week in advance</option>
                                            <option value="2">1 Day in advance</option>
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="property_management">Property Management</label>
                                        <select class="form-control" id="property_management" name="property_management">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="2">No</option>
                                        </select>
                                    </fieldset>
                                </div>

                                {{--<div class="col-lg-12 col-xl-12">
                                    <fieldset>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="send_email" id="send_email">
                                            <label class="custom-control-label" for="send_email">Send Email</label>
                                        </div>
                                    </fieldset>
                                </div>--}}
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-6">
                            <h6 class="text-primary">Commission Details</h6>
                            <div class="row custom-scrollbar pt-2" style="max-height: 400px;">
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="deal_price">Deal Price (AED)</label>
                                        <input type="text" class="form-control number-format" id="deal_price" name="deal_price" value="{{ ($deal) ? number_format($deal->deal_price) : '' }}" placeholder="Deal Price" required>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="commission">Total Commission (AED)</label>
                                        <input type="text" class="form-control number-format" id="commission" name="commission" value="{{ ($deal) ? number_format($deal->commission) : '' }}" placeholder="Total Commission" required>
                                    </fieldset>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label>{{($Company && $Company->name) ? $Company->name : ''}} Commission</label>
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <fieldset class="position-relative">
                                                    <input type="number" class="form-control"  {!! ($deal) ? 'value="'.$deal->company_percent.'"' : '' !!} id="company_commission_percent" name="company_commission_percent" data-amount-input="#company_commission_value" placeholder="0">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-percent"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control number-format" id="company_commission_value" name="company_commission_value" disabled placeholder="0">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="agent_1">CM 1</label>
                                        <select class="form-control select2" id="agent_1" name="agent_1">
                                            <option value="">Select</option>
                                            @foreach($Agents as $agent)
                                                <option value="{{ $agent->id }}" {{($countDealAgent>0 && $dealAgents[0]->agent_id==$agent->id) ? 'selected' : ''}}>{{ $agent->firstname.' '.$agent->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label>Commission</label>
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <fieldset class="position-relative">
                                                    <input type="number" class="form-control"  {!! ($countDealAgent>0) ? 'value="'.$dealAgents[0]->percent.'"' : '' !!} id="agent_1_commission_percent" name="agent_1_commission_percent" data-amount-input="#agent_1_commission_value" placeholder="0">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-percent"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control number-format" {!! ($countDealAgent>0) ? 'value="'.number_format($dealAgents[0]->commission).'"' : '' !!} id="agent_1_commission_value" name="agent_1_commission_value" disabled placeholder="0">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="agent_two">CM 2</label>
                                        <select class="form-control select2" id="agent_2" name="agent_2">
                                            <option value="">Select</option>
                                            @foreach($Agents as $agent)
                                                <option value="{{ $agent->id }}" {{($countDealAgent>1 && $dealAgents[1]->agent_id==$agent->id) ? 'selected' : ''}}>{{ $agent->firstname.' '.$agent->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label>Commission</label>
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <fieldset class="position-relative">
                                                    <input type="number" class="form-control"  {!! ($countDealAgent>1) ? 'value="'.$dealAgents[1]->percent.'"' : '' !!} id="agent_2_commission_percent" name="agent_2_commission_percent" data-amount-input="#agent_2_commission_value" placeholder="0">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-percent"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control number-format" {!! ($countDealAgent>1) ? 'value="'.number_format($dealAgents[1]->commission).'"' : '' !!} id="agent_2_commission_value" name="agent_2_commission_value" disabled placeholder="0">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label for="agent_three">CM 3</label>
                                        <select class="form-control select2" id="agent_3" name="agent_3">
                                            <option value="">Select</option>
                                            @foreach($Agents as $agent)
                                                <option value="{{ $agent->id }}" {{($countDealAgent>2 && $dealAgents[2]->agent_id==$agent->id) ? 'selected' : ''}}>{{ $agent->firstname.' '.$agent->lastname }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                </div>

                                <div class="col-lg-12 col-xl-12">
                                    <fieldset class="form-group form-label-group">
                                        <label>Commission</label>
                                        <div class="row">
                                            <div class="col-4 pr-0">
                                                <fieldset class="position-relative">
                                                    <input type="number" class="form-control"  {!! ($countDealAgent>2) ? 'value="'.$dealAgents[2]->percent.'"' : '' !!} id="agent_3_commission_percent" name="agent_3_commission_percent" data-amount-input="#agent_3_commission_value" placeholder="0">
                                                    <div class="form-control-position">
                                                        <i class="feather icon-percent"></i>
                                                    </div>
                                                </fieldset>
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="form-control number-format" {!! ($countDealAgent>2) ? 'value="'.number_format($dealAgents[2]->commission).'"' : '' !!} id="agent_3_commission_value" name="agent_3_commission_value" disabled placeholder="0">
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="d-flex deal-info-box">

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-xl-5">
                    <div>
                        <h6 class="text-primary">Documents</h6>
                        <div class="text-center">
                            <a href="#ModalDocument" data-toggle="modal"
                               class="btn btn-primary mb-1 waves-effect waves-light">Upload</a>
                        </div>
                    </div>
                    <div class="custom-scrollbar pt-2 deal-doc-box" style="height: 320px;">
                        @foreach($dealDocuments as $dDoc)
                            <div class="doc-item mb-1">
                                <input type="hidden" name="deal_doc[]" value="{{$dDoc->docname}}">
                                <input type="hidden" name="document_type[]" value="{{$dDoc->type}}">
                                <input type="hidden" name="document_name[]" value="{{$dDoc->name}}">
                                <div class="media">
                                    <a class="media-left align-self-center" href="#">
                                        <img src="/storage/{{$dDoc->docname}}" height="64" width="64">
                                    </a>
                                    <div class="media-body pl-1">
                                        <h5 class="media-heading">{{($dDoc->type) ? DealDocType[$dDoc->type] : ''}}</h5>
                                        <p class="mb-0">{{$dDoc->name}}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if(!$deal)
                <div class="col-xl-12 text-right">
                    <button type="button" class="btn bg-gradient-info waves-effect waves-light float-right save-deal">Save Deal</button>
                    <button type="submit" name="submit" class="d-none">Save Deal</button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDocument" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalCenterTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
         role="document">
        <form method="post" action="https://crm.smdamarketing.com/admin/target/add" id="record-form"
              class="modal-content" novalidate="">
            <input type="hidden" name="_token" value="j03Fdv25LTwWzOiYPEgHwKHuVGWTON3PXwCrc0l6">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Deals Document Upload</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mt-1">
                    <div class="col-lg-6 col-xl-6">
                        <fieldset class="form-group">
                            <label for="document_type">Document Type</label>
                            <select class="form-control" id="document_type">
                                <option value="">Select</option>
                                @foreach(DealDocType as $key=>$value)
                                    <option value="{{$key}}">{{$value}}</option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-lg-6 col-xl-6">
                        <fieldset class="form-group">
                            <label for="document_name">Contract No</label>
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
                <div class="col-sm-7 m-0">
                    <div class="progress progress-bar-primary progress-xl d-none w-100">
                        <div class="progress-bar bg-teal progress-bar-striped doc-upload-progress-bar" role="progressbar"
                             aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        </div>
                    </div>
                </div>
                <div class="col-sm-5 text-right m-0">
                    <button type="button" class="btn btn-primary btn-doc-upload waves-effect waves-light" value="submit">Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('vendor-script')
    <!-- vendor files -->
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection
@section('page-script')
    @if($deal)
    <script>
        $('#type').val('{{$deal->type}}');
        $('#cheques').val('{{$deal->cheques}}');
        $('#set_reminder').val('{{$deal->set_reminder}}');
        $('#property_management').val('{{$deal->property_management}}');
    </script>
    @endif

    <script>
        propertySelcet2();
        function propertySelcet2(SelectType=false) {
            // Loading remote data
            $(".select-2-property").select2({
                dropdownAutoWidth: true,
                width: '100%',
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
                placeholder: 'Property Information',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepoProperty,
                templateSelection: formatRepoSelectionProperty,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
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
                placeholder: 'Contact Information',
                minimumResultsForSearch: Infinity,
                templateResult: formatRepoContact,
                templateSelection: formatRepoSelectionContact,
                escapeMarkup: function (markup) { if(markup!='undefined') return markup; }, // let our custom formatter work
                // minimumInputLength: 1,
            });

        }

        function formatRepoContact (repo) {
            if (repo.loading) return 'Loading...';
            var markup = `<div class="select2-member-box">
                            <div class="image-box"><img src="${repo.picutre}" /></div>
                            <div class="w-100 ml-1">
                                <div><b>${repo.ref}</b></div>
                                <div>${repo.fullname}</div>
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
        $('#type').change(function(){
            let val = $(this).val();
            $('#tenancy_contract_start_date , #tenancy_renewal_date , #cheques , #set_reminder , #property_management , #deal_model').parent().parent().addClass('d-none');

            if(val==2){
                $('#deal_model').val('').parent().parent().removeClass('d-none');
            }

            if(val==1){
                $('#tenancy_contract_start_date , #tenancy_renewal_date , #cheques , #set_reminder , #property_management').parent().parent().removeClass('d-none');
                $('#deal_model').val('5');
            }
        });

        $('#agent_1_commission_percent , #agent_2_commission_percent , #agent_3_commission_percent , #company_commission_percent').keyup(function(){
            let commission=$('#commission').val();
            commission=commission.replace(/,/g, "");
            let percent = $(this).val();
            let price=(commission/100)*percent;
            $($(this).data('amount-input')).val( price.toFixed() ).keyup();
        });

        $('#commission').keyup(function(){
            let commission=$('#commission').val();
            commission=commission.replace(/,/g, "");

            if($('#agent_1_commission_percent').val()!=''){
                let percent = $('#agent_1_commission_percent').val();
                let price=(commission/100)*percent;
                $($('#agent_1_commission_percent').data('amount-input')).val( price.toFixed() ).keyup();
            }

            if($('#agent_2_commission_percent').val()!=''){
                let percent = $('#agent_2_commission_percent').val();
                let price=(commission/100)*percent;
                $($('#agent_2_commission_percent').data('amount-input')).val( price.toFixed() ).keyup();
            }

            if($('#agent_3_commission_percent').val()!=''){
                let percent = $('#agent_3_commission_percent').val();
                let price=(commission/100)*percent;
                $($('#agent_3_commission_percent').data('amount-input')).val( price.toFixed() ).keyup();
            }

            if($('#company_commission_percent').val()!=''){
                let percent = $('#company_commission_percent').val();
                let price=(commission/100)*percent;
                $($('#company_commission_percent').data('amount-input')).val( price.toFixed() ).keyup();
            }
        });
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
            //alert(file.name+" | "+file.size+" | "+file.type+" | "+file.name.split('.').pop());

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

            $('.deal-doc-box').append(`
            <div class="doc-item mb-1">
                <input type="hidden" name="deal_doc[]" value="${response.name}">
                <input type="hidden" name="document_type[]" value="${$("#document_type").val()}">
                <input type="hidden" name="document_name[]" value="${$('#document_name').val()}">
                <div class="media">
                    <a class="media-left align-self-center" target="_blank" href="${response.link}">
                        <img src="${response.link}" height="64" width="64">
                    </a>
                    <div class="media-body pl-1">
                        <h5 class="media-heading">${$("#document_type option:selected").text()}</h5>
                        <p class="mb-0">${$('#document_name').val()}</p>
                    </div>
                    <div class="float-right" data-name="${response.name}">
                        <a href="javascript:void(0)" class="file-delete text-danger" title="Remove"><i class="feather icon-x"></i></a>
                    </div>
                </div>
            </div>
            `);
            $("#document_type option:selected").val('');
            $('#document_name').val('');
            $('#document_file').val('');
            $(ProgressBar).parent().addClass("d-none");
            $('#ModalDocument').modal('hide');
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

    <script>
        @if($deal)
            let property='{{$deal->property_id}}';
            $.ajax({
                url:"{{ route('get-property-ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    property:property
                },
                success:function (response) {
                    let txt='LL-';
                    txt+=((response.listing_type_id==1) ? 'S' : 'R')+'-'+response.id;

                    $('.select-2-property')
                        .empty()
                        .append('<option selected value="'+response.id+'">'+txt+'</option>');
                    $('.select-2-property').select2('data', {
                        id: response.id,
                        label:txt
                    });
                }
            });

            let contact='{{$deal->contact_id}}';
            $.ajax({
                url:"{{ route('get-contact-ajax') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    contact:contact
                },
                success:function (response) {
                    $('.select-2-contact')
                        .empty()
                        .append('<option selected value="'+response.id+'">'+response.firstname+' '+response.lastname+'</option>');
                    $('.select-2-contact').select2('data', {
                        id: response.id,
                        label:response.firstname+' '+response.lastname
                    });
                }
            });
        @endif
    </script>

    <script>
        $('.select-2-property , .select-2-contact').change(function () {
            deal_info();
        });

        function deal_info(){

            $.ajax({
                url:"{{ route('get-deal-info') }}",
                type:"POST",
                data:{
                    _token:'{{ csrf_token() }}',
                    property:$('.select-2-property').val(),
                    contact:$('.select-2-contact').val(),
                },
                success:function (response) {
                    $('.deal-info-box').html(response);
                },error: function (data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        }

        $('.save-deal').click(function () {
            let property=$('#property').val();
            let contact=$('#contact').val();
            let type=$('#type').val();

            let error=0;

            let commission1=0;
            if($('#agent_1_commission_percent').val()!='')
                commission1=parseInt($('#agent_1_commission_percent').val());

            let commission2=0;
            if($('#agent_2_commission_percent').val()!='')
                commission2=parseInt($('#agent_2_commission_percent').val());

            let commission3=0;
            if($('#agent_3_commission_percent').val()!='')
                commission3=parseInt($('#agent_3_commission_percent').val());

            let companyCommission=($('#company_commission_percent').val()) ? parseInt($('#company_commission_percent').val()) : 0;

            let totalCommission=companyCommission+commission1+commission2+commission3;

            if(companyCommission===0){
                toast_('','Fill in the company commission..',$timeOut=20000,$closeButton=true);
                error=1;
            }

            if(commission1!=0 || $('#agent_1').val()!=''){
                if(commission1!=0 && $('#agent_1').val()==''){
                    toast_('','CM 1 is not selected.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
                if($('#agent_1').val()!='' && commission1==0){
                    toast_('','CM 1 failed to enter the commission.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
            }

            if(commission2!=0 || $('#agent_2').val()!=''){
                if(commission2!=0 && $('#agent_2').val()==''){
                    toast_('','CM 2 is not selected.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
                if($('#agent_2').val()!='' && commission2==0){
                    toast_('','CM 2 failed to enter the commission.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
            }

            if(commission3!=0 || $('#agent_3').val()!=''){
                if(commission3!=0 && $('#agent_3').val()==''){
                    toast_('','CM 3 is not selected.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
                if($('#agent_3').val()!='' && commission3==0){
                    toast_('','CM 3 failed to enter the commission.',$timeOut=20000,$closeButton=true);
                    error=1;
                }
            }
            if($('#type').val()=='1'){
                if($('#tenancy_contract_start_date').val()==''){
                    error=1;
                    $("#tenancy_contract_start_date").parent().addClass('error');
                }
                if($('#tenancy_renewal_date').val()==''){
                    error=1;
                    $('#tenancy_renewal_date').parent().addClass('error');
                }
                if($('#property_management').val()==''){
                    error=1;
                    $('#property_management').parent().addClass('error');
                }
            }

            let agent_1=$('#agent_1').val();
            let agent_2=$('#agent_2').val();
            let agent_3=$('#agent_3').val();
            let duplicate_agent=0;

            if(agent_1!=''){
                if(agent_1==agent_2 || agent_1==agent_3)
                    duplicate_agent++;
            }

            if(agent_2!=''){
                if(agent_2==agent_1 || agent_2==agent_3)
                    duplicate_agent++;
            }

            if(agent_3!=''){
                if(agent_3==agent_2 || agent_1==agent_3)
                    duplicate_agent++;
            }

            if(duplicate_agent>0) {
                error = 1;
                toast_('','Duplicate agents are selected.',$timeOut=20000,$closeButton=true);
            }

            if(totalCommission!=100){
                error=1;
                toast_('','The sum of the commission is not 100%.',$timeOut=20000,$closeButton=true);
            }

            if(property ==null) {
                error=1;
                toast_('','Property selection is required.',$timeOut=20000,$closeButton=true);
            }

            if(contact ==null) {
                error=1;
                toast_('','Contact selection is required.',$timeOut=20000,$closeButton=true);
            }

            /*if(type==2){
                let doc=0
                $('.deal-doc-box input[name="document_type[]"]').each(function(){
                    if($(this).val()==11)
                        doc++;
                });
                if(doc==0) {
                    toast_('','Upload Form F.',$timeOut=20000,$closeButton=true);
                    error = 1;
                }
            }

            if(type==1){
                let doc=0
                $('.deal-doc-box input[name="document_type[]"]').each(function(){
                    if($(this).val()==26)
                        doc++;
                });
                if(doc==0) {
                    toast_('','Upload Rental Contract.',$timeOut=20000,$closeButton=true);
                    error = 1;
                }
            }*/

            if(error==0){
                $('button[name="submit"]').click();
            }
        });
    </script>
    <script>
        $('body').on('click','.file-delete',function(){
            var file=$(this).parent().data('name');
            var e=$(this).parent().parent().parent();

            Swal.fire({
                title: 'Are you sure?',
                // text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                cancelButtonText: 'Cancel',
                confirmButtonText:'Yes',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-primary ml-1',
                buttonsStyling: false,
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url:"{{ route('delete-image') }}",
                        type:"POST",
                        data:{
                            _token:'{{ csrf_token() }}',
                            FileDelete:file
                        },
                        success:function (response) {
                            e.remove();
                        }
                    });
                }
            });
        });
    </script>

@endsection
