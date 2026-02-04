
@extends('layouts/contentLayoutMaster')

@section('title', 'Business Timing')

@section('vendor-style')
    {{-- vendor css files --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/datatables.min.css')) }}">
@endsection

@section('content')
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Business Timing</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
          <div class="row">
            <div class="col-lg-12">
              <div class="my_dashboard_review">
                <form method="post" action="{{route('business-timings.edit')}}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th scope="col">Days</th>
                                <th scope="col">Starting Time</th>
                                <th scope="col">Ending Time</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($businessTimings as $row)
                            <tr>
                                <td>
                                    <fieldset>
                                        <input type="checkbox" value="{{$row->id}}" name="day[]" id="day{{$row->id}}" {{($row->status==1) ? 'checked' : ''}}>
                                        <label for="day{{$row->id}}">{{$row->day}}</label>
                                    </fieldset>
                                </td>
                                <td>
                                    <fieldset>
                                        <input type="time" name="from_time{{$row->id}}" value="{{$row->from_time}}" class="form-control toggle" {{($row->status==1) ? '' : 'disabled'}}>
                                    </fieldset>
                                </td>
                                <td>
                                    <fieldset>
                                        <input type="time" name="to_time{{$row->id}}" value="{{$row->to_time}}" class="form-control toggle" {{($row->status==1) ? '' : 'disabled'}}>
                                    </fieldset>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                  <div class="col-xl-12 text-right">
                    <button type="submit" name="update" value="1" class="btn bg-gradient-info waves-effect waves-light float-right">Save</button>
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
        $("input:checkbox").on("change", function () {
            console.log("sddf");
            $(this)
                .closest("tr")
                .find(".toggle")
                .prop("disabled", !this.checked);
        });
    </script>
@endsection
