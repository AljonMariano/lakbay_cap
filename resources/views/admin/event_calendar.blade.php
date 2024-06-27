<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Events Calendar</title>
    <?php $title_page = 'Schedules';?>
    @include('includes.header')
</head>
<body>

    <div id="calendar">
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var scheduledEvents = @json($events);
            var calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'UTC',
                initialView: 'dayGridMonth'
                , events: scheduledEvents
                , editable: true
                , selectable: true
            });
            calendar.render();
        });

    </script>
</body>
</html>
