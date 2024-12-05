<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel FullCalendar Example</title>

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
    <h3 class="text-center">Laravel FullCalendar Example</h3>
    <div id="calendar"></div>
</div>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="eventId">
                <div class="mb-3">
                    <label for="eventTitle" class="form-label">Title</label>
                    <input type="text" class="form-control" id="eventTitle">
                </div>
                <div class="mb-3">
                    <label for="eventStart" class="form-label">Start Date</label>
                    <input type="datetime-local" id="eventStart" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="eventEnd" class="form-label">End Date</label>
                    <input type="datetime-local" id="eventEnd" class="form-control">
                </div>
                <button id="saveEventBtn" class="btn btn-primary">Save</button>
                <button id="deleteEventBtn" class="btn btn-danger" style="display: none;">Delete</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        var SITEURL = "{{ url('/') }}";
        var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        var calendar = $('#calendar').fullCalendar({
            editable: true,
            events: SITEURL + '/fullcalender',
            selectable: true,
            selectHelper: true,
            select: function(start, end) {
                $('#eventId').val(''); // Clear event ID
                $('#eventTitle').val(''); // Clear event title

                // Set the start date to the selected date (midnight)
                var selectedDate = moment(start).format('YYYY-MM-DD'); // Get the selected date
                var startTime = selectedDate + "T00:00"; // Set start time to midnight
                var endTime = moment(start).add(1, 'hours').format('YYYY-MM-DDTHH:mm'); // Set the end time to 1 hour after start

                // Bind the start and end times to the input fields
                $('#eventStart').val(startTime); // Bind to start input
                $('#eventEnd').val(endTime); // Bind to end input

                $('#deleteEventBtn').hide(); // Hide delete button on new event

                // Debugging: Log the selected start and end times
                console.log('Selected Start:', startTime);
                console.log('Selected End:', endTime);

                eventModal.show(); // Show modal for new event
            },
            eventClick: function(event) {
                console.log('Event:', event); // Log event data for debugging

                // Correct format for datetime-local inputs (24-hour format)
                var start = moment(event.start).format('YYYY-MM-DDTHH:mm');  // Start time
                var end = moment(event.end).format('YYYY-MM-DDTHH:mm');      // End time

                // Log the formatted start and end times
                console.log('Formatted Start:', start);
                console.log('Formatted End:', end);

                // Bind start and end date values to the inputs
                $('#eventStart').val(start);  // Bind start date to the input
                $('#eventEnd').val(end);      // Bind end date to the input

                // Ensure that these inputs exist and are being properly updated
                console.log($('#eventStart').val());  // Check if the value is set correctly
                console.log($('#eventEnd').val());    // Check if the value is set correctly

                // Bind other values to the inputs
                $('#eventId').val(event.id); // Bind event ID
                $('#eventTitle').val(event.title); // Bind event title

                $('#deleteEventBtn').show();  // Show delete button
                eventModal.show();            // Show the modal
            }
        });

        // Save Event
        $('#saveEventBtn').click(function () {
            var id = $('#eventId').val();
            var title = $('#eventTitle').val();
            var start = $('#eventStart').val();
            var end = $('#eventEnd').val();
            var type = id ? 'update' : 'add';

            // Debugging: Log the save data
            console.log('Save Data:', { id, title, start, end, type });

            $.ajax({
                url: SITEURL + "/fullcalenderAjax",
                type: "POST",
                data: { id, title, start, end, type },
                success: function (data) {
                    eventModal.hide();
                    toastr.success(type === 'add' ? 'Event Added' : 'Event Updated');
                    calendar.fullCalendar('refetchEvents'); // Refresh calendar
                }
            });
        });

        // Delete Event
        $('#deleteEventBtn').click(function () {
            var id = $('#eventId').val();
            $.ajax({
                url: SITEURL + "/fullcalenderAjax",
                type: "POST",
                data: { id, type: 'delete' },
                success: function () {
                    eventModal.hide();
                    toastr.success('Event Deleted');
                    calendar.fullCalendar('refetchEvents'); // Refresh calendar
                }
            });
        });
    });
</script>






</body>
</html>
