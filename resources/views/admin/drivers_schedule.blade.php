<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Driver's Schedules</title>
</head>
<body>
    <select name="driver" id="driverSelect">
        @foreach($drivers as $driver)
        <option value="{{ $driver->dr_emp_id }}">{{ $driver->dr_fname }}</option>
        @endforeach
    </select>

    <select name="rsdriver" id="rsdriverSelect">
        @foreach($reservations as $reservation)
        <option value="{{ $reservation->drivers->dr_fname }}">{{ $reservation->event_id }}</option>
        @endforeach
    </select>







    <div id="driverIdDisplay"></div>
    <div id="rsdriverIdDisplay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var driverSelect = document.getElementById('driverSelect');
            var driverIdDisplay = document.getElementById('driverIdDisplay');
            var rsdriverSelect = document.getElementById('rsdriverSelect');
            var rsdriverIdDisplay = document.getElementById('rsdriverIdDisplay');

            driverSelect.addEventListener('change', function() {
                var selectedDriverId = driverSelect.value;
                driverIdDisplay.textContent = "Driver ID: " + selectedDriverId;
            });
            rsdriverSelect.addEventListener('change', function() {
                var rsselectedDriverId = rsdriverSelect.value;
                rsdriverIdDisplay.textContent = "Reserved Driver ID: " + rsselectedDriverId;
            });
                        
        });

    </script>
</body>
</html>
