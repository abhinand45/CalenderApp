<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- {{ __('Dashboard') }} --}}
        </h2>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <title>Calender</title>

            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        </head>

        <body>
            <div class="container mt-5">
                <h3 class="text-center">Event Calender</h3>
                <div id="calendar"></div>
            </div>

            <!-- Event Modal -->
            <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="eventId">
                            <div class="mb-3">
                                <label for="eventTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="eventTitle">
                                <small id="eventTitleError" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="eventStart" class="form-label">Start Date</label>
                                <input type="datetime-local" id="eventStart" class="form-control">
                                <small id="eventStartError" class="text-danger"></small>
                            </div>

                            <div class="mb-3">
                                <label for="eventEnd" class="form-label">End Date</label>
                                <input type="datetime-local" id="eventEnd" class="form-control">
                                <small id="eventEndError" class="text-danger"></small>
                            </div>

                            <button id="saveEventBtn" class="btn btn-primary">Save</button>
                            <button id="deleteEventBtn" class="btn btn-danger" style="display: none;">Delete</button>
                        </div>
                    </div>
                </div>
            </div>


            <script>
                $(document).ready(function() {
                    var SITEURL = "{{ url('/') }}";
                    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var calendar = $('#calendar').fullCalendar({
                        editable: true,
                        events: SITEURL + '/events',
                        selectable: true,
                        selectHelper: true,
                        select: function(start, end) {
                            $('#eventId').val('');
                            $('#eventTitle').val('');

                            var selectedDate = moment(start).format('YYYY-MM-DD');
                            var startTime = selectedDate + "T00:00";
                            var endTime = moment(start).add(1, 'hours').format(
                                'YYYY-MM-DDTHH:mm');

                            $('#eventStart').val(startTime);
                            $('#eventEnd').val(endTime);

                            $('#deleteEventBtn').hide();

                            console.log('Selected Start:', startTime);
                            console.log('Selected End:', endTime);

                            eventModal.show();
                        },
                        eventClick: function(event) {
                            console.log('Event:', event);

                            var start = moment(event.start).format('YYYY-MM-DDTHH:mm');
                            var end = moment(event.end).format('YYYY-MM-DDTHH:mm');
                            console.log('Formatted Start:', start);
                            console.log('Formatted End:', end);
                            $('#eventStart').val(start);
                            $('#eventEnd').val(end);
                            console.log($('#eventStart').val());
                            console.log($('#eventEnd').val());
                            $('#eventId').val(event.id);
                            $('#eventTitle').val(event.title);

                            $('#deleteEventBtn').show();
                            eventModal.show();
                        }
                    });

                    // Save Event
                    $('#saveEventBtn').click(function() {
                        var title = $('#eventTitle').val();
                        var start = $('#eventStart').val();
                        var end = $('#eventEnd').val();
                        var type = $('#eventId').val() ? 'update' : 'add';

                        $('#eventTitleError').html('');
                        $('#eventStartError').html('');
                        $('#eventEndError').html('');

                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            type: "POST",
                            data: {
                                title,
                                start,
                                end,
                                type
                            },
                            success: function(data) {
                                toastr.success('Event added or updated successfully');
                                eventModal.hide();
                                calendar.fullCalendar('refetchEvents');
                            },
                            error: function(xhr) {
                                if (xhr.status === 422) {
                                    var errors = xhr.responseJSON.errors;

                                    // Display errors in the modal
                                    if (errors.title) {
                                        $('#eventTitleError').html(errors.title[0]);
                                    }
                                    if (errors.start) {
                                        $('#eventStartError').html(errors.start[0]);
                                    }
                                    if (errors.end) {
                                        $('#eventEndError').html(errors.end[0]);
                                    }
                                } else {
                                    toastr.error('An error occurred. Please try again.');
                                }
                            }
                        });
                    });


                    // Delete Event
                    $('#deleteEventBtn').click(function() {
                        var id = $('#eventId').val();
                        $.ajax({
                            url: SITEURL + "/fullcalenderAjax",
                            type: "POST",
                            data: {
                                id,
                                type: 'delete'
                            },
                            success: function() {
                                eventModal.hide();
                                toastr.success('Event Deleted');
                                calendar.fullCalendar('refetchEvents'); 
                            }
                        });
                    });
                });
            </script>






        </body>

        </html>


    </x-slot>



</x-app-layout>
