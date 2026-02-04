
@extends('layouts/contentLayoutMaster')

@section('title', 'Calendar')

@section('vendor-style')
    <!-- Vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/fullcalendar.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/extensions/daygrid.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/calendars/extensions/timegrid.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/pickadate/pickadate.css')) }}">
@endsection
@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/plugins/calendars/fullcalendar.css')) }}">
    <style>
        #fc-default{
            overflow: auto;
        }
        .fc-view-container{
            min-width: 600px;
            overflow: auto;
        }
        .fc .fc-event .fc-title {
            color: #000000;
        }
    </style>
@endsection

@section('content')

    <section id="basic-examples">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="cal-category-bullets d-none">

                            </div>
                            <div id='fc-default'></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- calendar Modal starts-->
        <div class="modal fade text-left modal-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-text-bold-600" id="cal-modal">Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body pt-2">
                            <p><b>Client Manager: </b> <span id="cal-user"></span></p>
                            <p><b>Title: </b> <span id="cal-event-title"></span></p>
                            <p><b>Date: </b> <span id="cal-start-date"></span></p>
                            {{--<p><b>End Date: </b> <span id="cal-end-date"></span></p>--}}
                            <p><b>Time: </b> <span id="cal-end-time"></span></p>
                            <p><b>Contact: </b> <span id="cal-contact"></span></p>
                            <p><b>Property: </b> <span id="cal-property"></span></p>
                            <p><b>Description: </b> <span id="cal-description"></span></p>
                            <p><b><span id="cal-status"></span></b></p>
                        </div>
                        {{--<div class="modal-footer">
                          <!--<button type="button" class="btn btn-primary cal-add-event waves-effect waves-light" disabled> Add Event</button>-->
                          <!--<button type="button" class="btn btn-primary d-none cal-submit-event waves-effect waves-light" disabled>submit</button>-->
                          <!--<button type="button" class="btn btn-flat-danger cancel-event waves-effect waves-light" data-dismiss="modal">Close</button>-->
                          <!--<button type="button" class="btn btn-flat-danger remove-event d-none waves-effect waves-light" data-dismiss="modal">Remove</button>-->
                        </div>--}}
                    </form>
                </div>
            </div>
        </div>
        <!-- calendar Modal ends-->


        <!-- calendar Modal starts-->
        <div class="modal fade text-left modal-task-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-text-bold-600" id="cal-modal">Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body pt-2">
                            <p><b>User: </b> <span id="cal-task-user"></span></p>
                            <p><b>Client Manager: </b> <span id="cal-task-assignto"></span></p>
                            <p><b>task: </b> <span id="cal-task-title"></span></p>
                            <p><b>Date: </b> <span id="cal-task-start-date"></span></p>
                            <p><b>Time: </b> <span id="cal-task-end-time"></span></p>
                            <p><b>Description: </b> <span id="cal-task-description"></span></p>
                            <p><b><span id="cal-task-status"></span></b></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- calendar Modal ends-->


        <!-- calendar Modal starts-->
        <div class="modal fade text-left modal-birth-calendar" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-text-bold-600" id="cal-modal">Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form action="#">
                        <div class="modal-body pt-2">
                            <p><span id="cal-birth-user"></span></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- calendar Modal ends-->


        <!-- calendar Modal starts-->
        <div class="modal fade text-left modal-calendar-activity" tabindex="-1" role="dialog" aria-labelledby="cal-modal"aria-modal="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-text-bold-600" id="cal-modal">Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body pt-2">
                        <div class="table-responsive">
                            <table class="table table-striped truncate-table mb-0">
                                <thead>
                                <tr>
                                    <th>Activity Type</th>
                                    <th>Property / Contact</th>
                                    <th>Feedback / Note</th>
                                    <th>CM</th>
                                    <th>Added Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody id="div_notes_section">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- calendar Modal ends-->
    </section>
    <!-- // Full calendar end -->

@endsection

@section('vendor-script')
    <!-- Vendor js files -->
    <script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/calendar/fullcalendar.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/calendar/extensions/daygrid.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/calendar/extensions/timegrid.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/calendar/extensions/interactions.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/pickadate/picker.date.js')) }}"></script>
@endsection
@section('page-script')
    <!-- Page js files
        <script src="{{ asset(mix('js/scripts/extensions/fullcalendar.js')) }}"></script>-->

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // color object for different event types
            var colors = {
                primary: "#7367f0",
                success: "#28c76f",
                danger: "#ea5455",
                warning: "#ff9f43"
            };

            // chip text object for different event types
            var categoryText = {
                primary: "Others",
                success: "Business",
                danger: "Personal",
                warning: "Work"
            };
            var categoryBullets = $(".cal-category-bullets").html(),
                evtColor = "",
                eventColor = "";

            // calendar init
            var calendarEl = document.getElementById('fc-default');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ["dayGrid", "timeGrid", "interaction"],
                customButtons: {
                    /*addNew: {
                      text: ' Add',
                      click: function () {
                        var calDate = new Date,
                          todaysDate = calDate.toISOString().slice(0, 10);
                        $(".modal-calendar").modal("show");
                        $(".modal-calendar .cal-submit-event").addClass("d-none");
                        $(".modal-calendar .remove-event").addClass("d-none");
                        $(".modal-calendar .cal-add-event").removeClass("d-none")
                        $(".modal-calendar .cancel-event").removeClass("d-none")
                        $(".modal-calendar .add-category .chip").remove();
                        $("#cal-start-date").val(todaysDate);
                        $("#cal-end-date").val(todaysDate);
                        $(".modal-calendar #cal-start-date").attr("disabled", false);
                      }
                    }*/
                },
                header: {
                    left: "addNew",
                    center: "dayGridMonth,timeGridWeek,timeGridDay",
                    right: "prev,title,next"
                },
                displayEventTime: false,
                navLinks: true,
                editable: true,
                allDay: true,
                navLinkDayClick: function (date) {
                    //   $(".modal-calendar").modal("show");
                },
                dateClick: function (info) {
                    $(".modal-calendar #cal-start-date").val(info.dateStr).attr("disabled", true);
                    $(".modal-calendar #cal-end-date").val(info.dateStr);
                },
                // displays saved event values on click
                eventClick: function (info) {
                    if(info.event.extendedProps.type=='user_birth') {
                        $(".modal-birth-calendar").modal("show");
                        $(".modal-birth-calendar #cal-birth-user").html( info.event.extendedProps.user );
                    }else if(info.event.extendedProps.type=='task'){
                        $(".modal-task-calendar").modal("show");
                        $(".modal-task-calendar #cal-task-user").html(info.event.extendedProps.user);
                        $(".modal-task-calendar #cal-task-assignto").html(info.event.extendedProps.assign_to);
                        $(".modal-task-calendar #cal-task-title").html(info.event.title);
                        $(".modal-task-calendar #cal-task-start-date").html(moment(info.event.start).format('DD-MM-YYYY'));
                        $(".modal-task-calendar #cal-task-end-time").html(info.event.extendedProps.time);
                        $(".modal-task-calendar #cal-task-description").html(info.event.extendedProps.description);
                        $(".modal-task-calendar #cal-task-status").html(info.event.extendedProps.status);
                        $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
                        var eventCategory = info.event.extendedProps.dataEventColor;
                        var eventText = categoryText[eventCategory]
                        $(".modal-calendar .chip-wrapper .chip").remove();
                        $(".modal-calendar .chip-wrapper").append($("<div class='chip chip-" + eventCategory + "'>" +
                            "<div class='chip-body'>" +
                            "<span class='chip-text'> " + eventText + " </span>" +
                            "</div>" +
                            "</div>"));
                    }else{
                        $(".modal-calendar").modal("show");
                        $(".modal-calendar #cal-user").html(info.event.extendedProps.user);
                        $(".modal-calendar #cal-event-title").html(info.event.title);
                        $(".modal-calendar #cal-start-date").html(moment(info.event.start).format('DD-MM-YYYY'));
                        // $(".modal-calendar #cal-end-date").html(moment(info.event.end).format('YYYY-MM-DD'));
                        $(".modal-calendar #cal-end-time").html(info.event.extendedProps.time);
                        $(".modal-calendar #cal-contact").html(info.event.extendedProps.contact);
                        $(".modal-calendar #cal-property").html(info.event.extendedProps.property);
                        $(".modal-calendar #cal-description").html(info.event.extendedProps.description);
                        $(".modal-calendar #cal-status").html(info.event.extendedProps.status);
                        $(".modal-calendar .cal-submit-event").removeClass("d-none");
                        $(".modal-calendar .remove-event").removeClass("d-none");
                        $(".modal-calendar .cal-add-event").addClass("d-none");
                        $(".modal-calendar .cancel-event").addClass("d-none");
                        $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
                        var eventCategory = info.event.extendedProps.dataEventColor;
                        var eventText = categoryText[eventCategory]
                        $(".modal-calendar .chip-wrapper .chip").remove();
                        $(".modal-calendar .chip-wrapper").append($("<div class='chip chip-" + eventCategory + "'>" +
                            "<div class='chip-body'>" +
                            "<span class='chip-text'> " + eventText + " </span>" +
                            "</div>" +
                            "</div>"));
                    }
                },

                events: {
                    url: '{{ route("get-json.calendar") }}',
                    extraParams: function() { // a function that returns an object
                        return {
                            dynamic_value: Math.random()
                        };
                    }
                },
            });

            // render calendar
            calendar.render();

            // appends bullets to left class of header
            $("#basic-examples .fc-right").append(categoryBullets);

            // Close modal on submit button
            $(".modal-calendar .cal-submit-event").on("click", function () {
                $(".modal-calendar").modal("hide");
            });

            // Remove Event
            $(".remove-event").on("click", function () {
                var removeEvent = calendar.getEventById('newEvent');
                removeEvent.remove();
            });


            // reset input element's value for new event
            if ($("td:not(.fc-event-container)").length > 0) {
                $(".modal-calendar").on('hidden.bs.modal', function (e) {
                    $('.modal-calendar .form-control').val('');
                })
            }

            // remove disabled attr from button after entering info
            $(".modal-calendar .form-control").on("keyup", function () {
                if ($(".modal-calendar #cal-event-title").val().length >= 1) {
                    $(".modal-calendar .modal-footer .btn").removeAttr("disabled");
                }
                else {
                    $(".modal-calendar .modal-footer .btn").attr("disabled", true);
                }
            });

            // open add event modal on click of day
            $(document).on("click", ".fc-day , .fc-day-top", function () {
                let date=$(this).data('date');
                getActivity(date);
                // $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
                // $(".modal-calendar .cal-submit-event").addClass("d-none");
                // $(".modal-calendar .remove-event").addClass("d-none");
                // $(".modal-calendar .cal-add-event").removeClass("d-none");
                // $(".modal-calendar .cancel-event").removeClass("d-none");
                // $(".modal-calendar .add-category .chip").remove();
                // $(".modal-calendar .modal-footer .btn").attr("disabled", true);
                // evtColor = colors.primary;
                // eventColor = "primary";
            });

            function getActivity(date){
                $(".modal-calendar-activity").modal("show");
                // let date=$(this).data('date');

                $.ajax({
                    url:"{{ route('calendar-activity.get.ajax') }}",
                    type:"POST",
                    data:{
                        _token:'{{ csrf_token() }}',
                        date:date
                    },
                    success:function (response) {
                        $('.modal-calendar-activity tbody').html(response);
                    },error: function (data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            }

            // change chip's and event's color according to event type
            $(".calendar-dropdown .dropdown-menu .dropdown-item").on("click", function () {
                var selectedColor = $(this).data("color");
                evtColor = colors[selectedColor];
                eventTag = categoryText[selectedColor];
                eventColor = selectedColor;

                // changes event color after selecting category
                $(".cal-add-event").on("click", function () {
                    calendar.addEvent({
                        color: evtColor,
                        dataEventColor: eventColor,
                        className: eventColor
                    });
                })

                $(".calendar-dropdown .dropdown-menu").find(".selected").removeClass("selected");
                $(this).addClass("selected");

                // add chip according to category
                $(".modal-calendar .chip-wrapper .chip").remove();
                $(".modal-calendar .chip-wrapper").append($("<div class='chip chip-" + selectedColor + "'>" +
                    "<div class='chip-body'>" +
                    "<span class='chip-text'> " + eventTag + " </span>" +
                    "</div>" +
                    "</div>"));
            });

            // calendar add event
            $(".cal-add-event").on("click", function () {
                $(".modal-calendar").modal("hide");
                var eventTitle = $("#cal-event-title").val(),
                    startDate = $("#cal-start-date").val(),
                    endDate = $("#cal-end-date").val(),
                    eventDescription = $("#cal-description").val(),
                    correctEndDate = new Date(endDate);
                calendar.addEvent({
                    id: "newEvent",
                    title: eventTitle,
                    start: startDate,
                    end: correctEndDate,
                    description: eventDescription,
                    color: evtColor,
                    dataEventColor: eventColor,
                    allDay: true
                });
            });

            // date picker
            $(".pickadate").pickadate({
                format: 'yyyy-mm-dd'
            });
        });
    </script>
    <script>
        $('body').on('click','.note-description td',function() {
            let html=$(this).children('.action').html();
            if (!html) {
                $('#ViewModal .modal-title').html( $(this).parent().data('title') );
                $('#ViewModal .modal-body').html( $(this).parent().data('desc') );
            }
        });
    </script>
@endsection
