<!-- BEGIN: Footer-->
<!-- Modal View -->

<div class="modal fade text-left" id="ViewModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="RegisterFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @php
                  $admin = Auth::guard('admin')->user();
                    $now = date('Y-m-d H:i:s');
                  $noteNotFeedback=DB::select("SELECT 'lead' as type,lead_notes.id, admin_id, property_id, note_subject, lead_id, null as 'contact_id', note, date_at, time_at, lead_notes.created_at,firstname,lastname FROM lead_notes,admins WHERE lead_notes.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND lead_notes.status=1 AND `note` is null AND admin_id=".$admin->id."
                UNION
                SELECT 'contact' as type,contact_note.id, admin_id, property_id, note_subject, null as 'lead_id', contact_id, note, date_at, time_at, contact_note.created_at,firstname,lastname FROM contact_note,admins WHERE contact_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND contact_note.status=1 AND `note` is null AND admin_id=".$admin->id."
                UNION
                SELECT 'property' as type,property_note.id, admin_id, property_id, note_subject, null as 'lead_id', contact_id, note, date_at, time_at, property_note.created_at,firstname,lastname FROM property_note,admins WHERE property_note.admin_id=admins.id AND CONCAT(`date_at`,' ',`time_at`) < '".$now."' AND note_subject IN (2,3) AND property_note.status=1 AND `note` is null AND admin_id=".$admin->id." ORDER BY created_at desc");

                @endphp
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Property/Contact Feedback</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($noteNotFeedback as $noteFb)
                        @php
                        $FBtitle='';
                        if($noteFb->type=='lead'){
                            $FBlead=App\Models\Lead::where('id',$noteFb->lead_id)->first();
                            $FBtitle='Lead-'.$FBlead->id;
                            $FBid=$FBlead->id;
                        }elseif($noteFb->type=='contact'){
                            $FBcontact=App\Models\Contact::where('id',$noteFb->contact_id)->first();
                            $FBtitle=SAMPLE.'-'.$FBcontact->id;
                            $FBid=$FBcontact->id;
                        }else{
                            $FBproperty=App\Models\Property::where('id',$noteFb->property_id)->first();
                            $FBtitle=SAMPLE.'-'.(($FBproperty->listing_type_id==1) ? 'S' : 'R').'-'.$FBproperty->id;
                            $FBid=$FBproperty->id;
                        }
                        @endphp
                        <tr class="go-to-reg-feedback" data-type="{{$noteFb->type}}" data-id="{{$FBid}}">
                            <td>{{$FBtitle}}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@php
    $admin = Auth::guard('admin')->user();
    $warnings=\App\Models\AdminWarning::where('status','0')->where('admin_id',$admin->id)->get();
@endphp
@if(count($warnings)>0)
<div class="modal fade text-left" id="AcknowledgebackModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16"></h4>
            </div>
            <form method="post" action="{{route('warning.acknowledge')}}" class="modal-body">
                @csrf
                <input type="hidden" id="WarningAcknowledge_id" name="WarningAcknowledge_id" value="">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="text-danger">It's a warning letter,additionally you received an email in this regard.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($warnings as $warning)
                            @php
                            $WarningType=\App\Models\WarningType::find($warning->warning_id);
                            @endphp
                        <tr>
                            <td>{{$WarningType->name}}</td>
                            <td><button type="submit" value="{{$warning->id}}" class="warning-acknowledge btn btn-outline-danger waves-effect waves-light">Acknowledge</button></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
@endif
<div class="delete-form-box">
    <form method="post" data-ajax="false">
        {!! csrf_field() !!}
    </form>
</div>
@if($configData["mainLayoutType"] == 'horizontal' && isset($configData["mainLayoutType"]))
<footer
    class="footer {{ $configData['footerType'] }} {{($configData['footerType']=== 'footer-hidden') ? 'd-none':''}} footer-light navbar-shadow">
    @else
    <footer
        class="footer {{ $configData['footerType'] }}  {{($configData['footerType']=== 'footer-hidden') ? 'd-none':''}} footer-light">
        @endif
{{--        <p class="clearfix blue-grey lighten-2 mb-0"><span--}}
{{--                class="float-md-left d-block d-md-inline-block mt-25">COPYRIGHT &copy; 2019<a--}}
{{--                    class="text-bold-800 grey darken-2" href="https://1.envato.market/pixinvent_portfolio"--}}
{{--                    target="_blank">Pixinvent,</a>All rights Reserved</span><span--}}
{{--                class="float-md-right d-none d-md-block">Hand-crafted & Made with<i--}}
{{--                    class="feather icon-heart pink"></i></span>--}}
{{--            <button class="btn btn-primary btn-icon scroll-top" type="button"><i--}}
{{--                    class="feather icon-arrow-up"></i></button>--}}
{{--        </p>--}}
    </footer>
    <!-- END: Footer-->
