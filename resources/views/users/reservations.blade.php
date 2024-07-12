<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservations</title>
    <?php $title_page = 'Reservations';?>
    @include('includes.user_header')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="row">
        <div class="col">
            <h4 class="text-uppercase">Reservations</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <button type="button" class="btn btn-lg btn-success" data-bs-toggle="modal" data-bs-target="#insertModal">
                Reserve
            </button>

            <div class="modal fade" id="insertModal" tabindex="-1" aria-labelledby="insertModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="insertModalLabel">Reservation Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('users.reservations.store') }}" method="POST" class="reservations-form" id="reservations-form" name="reservations-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="reservation_id" value="">

                                        <div class="mb-2">
                                            <label for="event_name" class="form-label mb-0">Destination/ Activity</label>
                                            <input type="text" class="form-control rounded-1" name="event_name" id="event_name" placeholder="Enter Destination/Activity" required>
                                            <span id="event_name_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_from" class="form-label mb-0">From</label>
                                            <input type="text" class="form-control rounded-1" name="rs_from" id="rs_from" placeholder="Enter Starting Point" required>
                                            <span id="rs_from_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_start" class="form-label mb-0">Start Date</label>
                                            <input type="date" class="form-control rounded-1" name="rs_date_start" id="rs_date_start" required>
                                            <span id="rs_date_start_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_start" class="form-label mb-0">Start Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start" required>
                                            <span id="rs_time_start_display"></span>
                                            <span id="rs_time_start_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_end" class="form-label mb-0">End Date</label>
                                            <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end" required>
                                            <span id="rs_date_end_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_end" class="form-label mb-0">End Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end" required>
                                            <span id="rs_time_end_display"></span>
                                            <span id="rs_time_end_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="driver_id" class="form-label mb-0">Driver</label>
                                            <select class="form-select driver-select" name="driver_id[]" id="driver_id" multiple>
                                                <option value="" disabled>Select Driver(s)</option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{$driver->driver_id}}">{{ $driver->dr_fname }} {{ $driver->dr_mname }} {{ $driver->dr_lname }}</option>
                                                @endforeach
                                            </select>
                                            <span id="driver_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="vehicle_id" class="form-label mb-0">Vehicle</label>
                                            <select class="form-select vehicle-select" name="vehicle_id[]" id="vehicle_id" multiple>
                                                <option value="" disabled>Select Vehicle(s)</option>
                                                @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->vehicle_id }}">{{ $vehicle->vh_brand }} - {{ $vehicle->vh_type }} - {{ $vehicle->vh_plate }} - {{$vehicle->vh_capacity}}</option>
                                                @endforeach
                                            </select>
                                            <span id="vehicle_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="office" class="form-label mb-0">Office</label>
                                            <select class="form-select" name="off_id" id="off_id" required>
                                                <option value="">Select Office</option>
                                                @foreach ($offices as $office)
                                                <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="office_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_passengers" class="form-label mb-0">Passengers</label>
                                            <input type="number" class="form-control rounded-1" name="rs_passengers" placeholder="Enter Number of Passengers" id="rs_passengers" value="">
                                            <span id="rs_passengers_error"></span>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <label for="rs_travel_type" class="form-label mb-0">Travel Type</label>
                                            <select class="form-select" name="rs_travel_type" id="rs_travel_type">
                                                <option value="" disabled selected>Select Travel Type</option>
                                                <option value="Within Province Transport">Within Province Transport</option>
                                                <option value="Outside Province Transport">Outside Province Transport</option>
                                                <option value="Daily Transport">Daily Transport</option>
                                            </select>
                                            <span id="rs_travel_type_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_voucher" class="form-label mb-0">Trip Ticket No.</label>
                                            <input type="text" class="form-control rounded-1" name="rs_voucher" placeholder="Enter Voucher code" id="rs_voucher" value="">
                                            <span id="rs_voucher_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="requestor_id" class="form-label mb-0">Requestor</label>
                                            <select class="form-control rounded-1" name="requestor_id" id="requestor_id" required>
                                                <option value="">Select Requestor</option>
                                                @foreach ($requestors as $requestor)
                                                <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="requestor_id_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="submit" value="insert" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <span id="form_result"></span>
    <div class="container">
        <div class="row">
            <div class="col">
                <table class="table table-bordered table-hover reservations-table" id="reservations-table" name="reservations-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Event</td>
                            <td>From</td>
                            <td>Start Date</td>
                            <td>Start Time</td>
                            <td>End Date</td>
                            <td>End Time</td>
                            <td>Vehicle</td>
                            <td>Driver</td>
                            <td>Requestor</td>
                            <td>Passengers</td>
                            <td>Travel Type</td>
                            <td>Voucher</td>
                            <td>Approval</td>
                            <td>Status</td>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>



<script type="text/javascript">
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#reservations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('users.reservations.show') }}",
            error: function (xhr, error, thrown) {
                console.error('DataTables error:', error, thrown);
            }
        },
        columns: [
            {data: 'reservation_id', name: 'reservation_id', title: 'ID'},
            {data: 'ev_name', name: 'ev_name', title: 'Event'},
            {data: 'rs_from', name: 'rs_from', title: 'From'},
            {data: 'rs_date_start', name: 'rs_date_start', title: 'Start Date'},
            {data: 'rs_time_start', name: 'rs_time_start', title: 'Start Time', render: function(data) {
                return formatTime(data);
            }},
            {data: 'rs_date_end', name: 'rs_date_end', title: 'End Date'},
            {data: 'rs_time_end', name: 'rs_time_end', title: 'End Time', render: function(data) {
                return formatTime(data);
            }},
            {data: 'vehicles', name: 'vehicles', title: 'Vehicle'},
            {data: 'drivers', name: 'drivers', title: 'Driver'},
            {data: 'requestor', name: 'requestor', title: 'Requestor'},
            {data: 'rs_passengers', name: 'rs_passengers', title: 'Passengers'},
            {data: 'rs_travel_type', name: 'rs_travel_type', title: 'Travel Type'},
            {data: 'rs_voucher', name: 'rs_voucher', title: 'Voucher'},
            {data: 'rs_approval_status', name: 'rs_approval_status', title: 'Approval'},
            {data: 'rs_status', name: 'rs_status', title: 'Status'},
        ],
        order: [[0, 'desc']],
    });

    // Initialize select2 for multiple selections with search
    $('#driver_id, #vehicle_id').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select option(s)',
        allowClear: true,
        closeOnSelect: false,
        dropdownParent: $('#insertModal')
    });

    // Handle removal of selections
    $('#driver_id, #vehicle_id').on('select2:unselecting', function (e) {
        e.preventDefault();
        var element = $(this);
        setTimeout(function() {
            element.val(element.val().filter(function(value) {
                return value != e.params.args.data.id;
            })).trigger('change');
        }, 0);
    });

    // Prevent modal from closing when clicking inside the Select2 dropdown
    $(document).on('click', '.select2-container--open .select2-search__field, .select2-container--open .select2-results__option', function (e) {
        e.stopPropagation();
    });

    // Handle form submission
    $('#reservations-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        console.log('Form data being sent:', formData);

        $.ajax({
            url: "{{ route('users.reservations.store') }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log('Success response:', response);
                if(response.success) {
                    // Clear the form
                    $('#reservations-form')[0].reset();
                    $('.driver-select, .vehicle-select').val(null).trigger('change');
                    
                    // Close the modal
                    $('#insertModal').modal('hide');
                    
                    // Reload the DataTable
                    $('#reservations-table').DataTable().ajax.reload();
                    
                    // Show success message using the existing script
                    $('#form_result').html('<div class="alert alert-success">' + response.success + '</div>');
                    
                    // Optionally, you can make the success message disappear after a few seconds
                    setTimeout(function() {
                        $('#form_result').html('');
                    }, 5000); // 5000 milliseconds = 5 seconds
                } else {
                    console.error('Unexpected response structure:', response);
                    $('#form_result').html('<div class="alert alert-danger">Error: Unexpected response from server</div>');
                }
            },
            error: function(xhr) {
                console.error('Error response:', xhr.responseText);
                var errorMessage = 'Error creating reservation';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    if (typeof xhr.responseJSON.error === 'object') {
                        // Handle validation errors
                        errorMessage = 'Validation errors:';
                        for (var field in xhr.responseJSON.error) {
                            errorMessage += '\n- ' + xhr.responseJSON.error[field].join(', ');
                        }
                    } else {
                        errorMessage += ': ' + xhr.responseJSON.error;
                    }
                }
                $('#form_result').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    });

    // Load drivers and vehicles
    $.ajax({
        url: "{{ route('users.get.drivers.vehicles') }}",
        method: 'GET',
        success: function(response) {
            var driverSelect = $('#driver_id');
            var vehicleSelect = $('#vehicle_id');

            // Clear existing options
            driverSelect.empty().append('<option value="" disabled>Select Driver(s)</option>');
            vehicleSelect.empty().append('<option value="" disabled>Select Vehicle(s)</option>');

            $.each(response.drivers, function(index, driver) {
                driverSelect.append($('<option>', {
                    value: driver.id,
                    text: driver.name
                }));
            });

            $.each(response.vehicles, function(index, vehicle) {
                vehicleSelect.append($('<option>', {
                    value: vehicle.id,
                    text: vehicle.name
                }));
            });

            // Trigger change to update Select2
            driverSelect.trigger('change');
            vehicleSelect.trigger('change');
        },
        error: function(xhr) {
            console.error('Error loading drivers and vehicles:', xhr.responseText);
        }
    });

    function formatTime(time) {
        if (!time) return '';
        let [hours, minutes] = time.split(':');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        return (hours < 10 ? '0' : '') + hours + ':' + minutes + ' ' + ampm;
    }

    function updateTimeDisplay() {
        let startTime = $('#rs_time_start').val();
        let endTime = $('#rs_time_end').val();

        if (startTime) {
            $('#rs_time_start_display').text(formatTime(startTime));
        }
        if (endTime) {
            $('#rs_time_end_display').text(formatTime(endTime));
        }
    }

    // Add event listeners to update display when time changes
    $('#rs_time_start, #rs_time_end').on('change', updateTimeDisplay);

    // Initial update
    updateTimeDisplay();

    // Modal initialization
    var myModal = new bootstrap.Modal(document.getElementById('insertModal'), {
        keyboard: false,
        backdrop: 'static'
    });

    // Reinitialize Select2 when modal is shown
    $('#insertModal').on('shown.bs.modal', function () {
        $('#driver_id, #vehicle_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select option(s)',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $('#insertModal')
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('insertModal'), {
        keyboard: false
    });
});
</script>



<style>
    input[type="date"], input[type="time"] {
        position: relative;
        z-index: 1;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ensure date and time inputs are clickable
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const timeInputs = document.querySelectorAll('input[type="time"]');

    dateInputs.forEach(input => {
        input.addEventListener('click', function(e) {
            e.preventDefault();
            this.showPicker();
        });
    });

    timeInputs.forEach(input => {
        input.addEventListener('click', function(e) {
            e.preventDefault();
            this.showPicker();
        });
    });
});
</script>

@include('includes.footer');
</html>