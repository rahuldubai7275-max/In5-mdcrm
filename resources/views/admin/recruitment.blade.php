
@extends('layouts/contentLayoutMaster')

@section('title', 'Recruitment')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">

@endsection
@php
    $adminAuth = Auth::guard('admin')->user();
    $language='';
    if($recruitment){
        $languages=App\Models\RecruitmentLanguage::where('recruitment_id',$recruitment->id)->get();
        $language='';
        foreach ($languages as $row){
            $language.='"'.$row->language_id.'",';
        }
    }
@endphp
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title d-none d-md-block">New Apply</h5>
        </div>
        <div class="card-content collapse show">
            <form class="card-body" method="post" action="{{ route($route) }}" novalidate>
                @csrf
                <div class="row mt-1">
                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="first_name">First Name <span>*</span></label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="First Name" value="{{($recruitment) ?  $recruitment->first_name : old('first_name')}}" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="last_name">Last Name <span>*</span></label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last Name" value="{{($recruitment) ?  $recruitment->last_name : old('last_name')}}" required>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="gender">Gender <span>*</span></label>
                            <select class="form-control" name="gender" id="gender" required>
                                <option value="">Select</option>
                                @foreach(GENDER as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            document.getElementById('gender').value="{{($recruitment) ?  $recruitment->gender : old('gender')}}"
                        </script>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="education_level">Education Level <span>*</span></label>
                            <select class="form-control" name="education_level" id="education_level" required>
                                <option value="">Select</option>
                                @foreach(EducationLevel as $key=>$value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            document.getElementById('education_level').value="{{($recruitment) ?  $recruitment->education_level : old('education_level')}}"
                        </script>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="job_title">Applying For <span>*</span></label>
                            <select class="form-control" name="job_title" id="job_title" required>
                                <option value="">Select</option>
                                @php
                                    $JobTitles=\App\Models\JobTitle::orderBy('name','ASC')->get();
                                @endphp
                                @foreach($JobTitles as $row)
                                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <script>
                            document.getElementById('job_title').value="{{($recruitment) ?  $recruitment->job_title_id : old('job_title')}}"
                        </script>
                    </div>
                    <div class="col-12 col-sm-4">
                        <fieldset class="form-group form-label-group">
                            <label>Nationality <span>*</span></label>
                            <select class="form-control select2" id="nationally" name="nationally" required>
                            </select>
                        </fieldset>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="starting_date">Available From <span>*</span></label>
                            <input type="text" class="form-control format-picker" id="starting_date" name="starting_date" autocomplete="off" placeholder="Available From" value="{{($recruitment) ? $recruitment->starting_date : old('years_of_experience')}}" readonly required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="years_of_experience">Years Of Experience In U.A.E <span>*</span></label>
                            <input type="text" class="form-control" id="years_of_experience" name="years_of_experience" placeholder="Years Of Experience In U.A.E" value="{{($recruitment) ?  $recruitment->years_of_experience : old('years_of_experience')}}" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="expected_salary">Expected Salary</label>
                            <input type="text" class="form-control number-format" id="expected_salary" name="expected_salary" placeholder="Expected Salary" value="{{($recruitment) ?  number_format($recruitment->expected_salary) : old('expected_salary')}}">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="commission_percent">Commission %</label>
                            <input type="number" class="form-control" id="commission_percent" name="commission_percent" placeholder="Commission %" value="{{($recruitment) ?  $recruitment->commission_percent : old('commission_percent')}}">
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="mobile_number">Mobile number <span>*</span></label>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number" placeholder="Mobile Number" value="{{($recruitment) ?  $recruitment->mobile_number : old('mobile_number')}}" required>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form-group form-label-group">
                            <label for="email">Email <span>*</span></label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email" value="{{($recruitment) ?  $recruitment->email : old('email')}}" required>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group form-label-group">
                            <label for="languages">Languages <span>*</span></label>
                            <select class="form-control select2" multiple name="languages[]" id="languages" required>
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

                    <div class="col-sm-12">
                        <div class="form-group form-label-group">
                            <label for="special_note">Special Note</label>
                            <input type="text" class="form-control" id="special_note" name="special_note" placeholder="Special Note" value="{{($recruitment) ?  $recruitment->special_note : old('special_note')}}">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <fieldset class="form-group mb-0">
                            <label for="cv-file">CV</label>
                            <div class="d-flex">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input document-upload" data-this="cv-file" id="cv-file"
                                           data-token="{{ csrf_token() }}" data-action="{{ route('upload-file') }}" data-progress=".cv-progress-bar" data-input="#cv">
                                    <label class="custom-file-label" for="cv-file">{{ ($recruitment && $recruitment->cv) ? 'CV file' : 'Choose file' }}</label>
                                </div>
                                <!--<div class="pl-1"><a href="javascript:void(0);" class="doc-download" data-input="document"><i class="fa fa-download"></i></a></div>-->
                            </div>
                            <input type="hidden" id="cv" name="cv" value="{{($recruitment) ?  $recruitment->cv : old('cv')}}">
                            <div class="progress progress-bar-primary progress-xl d-none w-100 mb-0">
                                <div class="progress-bar bg-teal progress-bar-striped cv-progress-bar" role="progressbar"
                                     aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="mt-2 text-right">
                    @if($recruitment)<input type="hidden" name="_id" value="{{ ($recruitment) ? $recruitment->id : '' }}">@endif
                    <button type="button" id="submit" class="btn btn-primary">Save</button>
                    <button type="submit" name="submit" class="d-none"></button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('vendor-script')
    {{-- vendor files --}}
    <script src="/js/scripts/countries.js"></script>
    <script src="/js/scripts/uploade-doc.js"></script>
@endsection
@section('page-script')
    {{-- Page js files --}}

    <script>
        populateCountries("nationally", "");
        $('#languages').val([{!! substr($language,0,-1) !!}]).trigger('change');
        $('#nationally').val('{{($recruitment) ? $recruitment->nationally : ''}}').trigger('change');

        $('#submit').click(function(){
            let error=0;
            let commission_percent=$('#commission_percent').val();
            let expected_salary=$('#expected_salary').val();
            if(commission_percent>100){
                $('#commission_percent').parent().addClass('error');
                error=1;
            }
            if(expected_salary=='' && commission_percent==''){
                toast_('','One of the expected salary or commission must be filled.',$timeOut=20000,$closeButton=true);
                error=1;
            }
            if(error==0){
                $('button[name="submit"]').click();
            }
        });
    </script>

@endsection
