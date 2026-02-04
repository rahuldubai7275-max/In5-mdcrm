<div class="modal fade text-left" id="ActivityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Activity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row my-2">
                    <div class="col-12 mx-auto">
                        <div class="form-group form-label-group">
                            <label>Activity Type</label>
                            <select class="custom-select form-control" id="NoteSubject" name="NoteSubject">
                                <option value="">Select</option>
                                @foreach(NoteSubject as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-label-group form-group activity-not-box">
                            <textarea id="Note" name="note" rows="2" class="form-control" placeholder="Add your note"></textarea>
                            <label for="Notes">Notes</label>
                        </div>

                        <div class="form-label-group contact-property-box d-none">
                            <div class="form-label-group form-group">
                                <select class="select-2-user form-control" name="ActivityContact" id="ActivityContact"></select>
                                <label for="SearchRepository" id="ActivityContactLabel">Contact</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-label-group form-group data-at-box d-none">
                            <input type="text" class="form-control limit-format-picker" id="DateAt" name="DateAt" placeholder="Date">
                            <label for="DateAt">Date</label>
                        </div>
                        <div class="form-label-group form-group data-at-box d-none">
                            <input type="text" class="form-control mt-2 limit-timepicker" id="TimeAt" name="TimeAt" placeholder="Time">
                            <label for="TimeAt">Time</label>
                        </div>
                    </div>

                    <div class="col-12 clearfix w-100">
                        <button type="button" id="AddPropertyNote" class="btn bg-gradient-info glow mb-1 float-right waves-effect waves-light">Submit</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade text-left" id="ActivityFeedbackModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel16">Feedback</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">

                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-12 col-md-8">
                                <div class="form-label-group ">
                                    <textarea id="FeedbackNote" rows="2" class="form-control" placeholder="Add your note"></textarea>
                                    <label for="Notes">Notes</label>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">

                                <div class="clearfix w-100">
                                    <button type="button" id="EditPropertyNote" class="btn bg-gradient-info glow float-right waves-effect waves-light">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
