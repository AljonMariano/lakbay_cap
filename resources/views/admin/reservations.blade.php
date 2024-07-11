<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservations</title>
    <?php $title_page = 'Reservations';?>
    @include('includes.admin_header')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            <a href="" role="button" class="btn btn-lg btn-success" id="insertBtn" data-bs-toggle="modal">Reserve</a>
            <div id="insertModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reservation Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('reservations.store') }}" method="POST" class="reservations-form" id="reservations-form" name="reservations-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="reservation_id" id="edit_reservation_id" value="">

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
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_start" class="form-label mb-0">Start Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_end" class="form-label mb-0">End Date</label>
                                            <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_end" class="form-label mb-0">End Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="driver_id" class="form-label mb-0">Driver</label>
                                            <select class="form-select driver-select" name="driver_id[]" id="driver_id">
                                                <option value="" disabled selected>Select Driver</option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{$driver->driver_id}}">{{ $driver->dr_fname }} {{ $driver->dr_mname }} {{ $driver->dr_lname }}</option>
                                                @endforeach
                                            </select>
                                            <span id="driver_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="vehicle_id" class="form-label mb-0">Vehicle</label>
                                            <select class="form-select vehicle-select" name="vehicle_id[]" id="vehicle_id">
                                                <option value="" disabled selected>Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->vehicle_id }}"> {{ $vehicle->vh_brand }} - {{ $vehicle->vh_type }} - {{ $vehicle->vh_plate }} - {{$vehicle->vh_capacity}}</option>
                                                @endforeach
                                            </select>
                                            <span id="vehicle_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="requestor_id" class="form-label mb-0">Requestor</label>
                                            <select class="form-select" name="requestor_id" id="requestor_id" required>
                                                <option value="" disabled selected>Select Requestor</option>
                                                @foreach ($requestors as $requestor)
                                                <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="requestor_id_error"></span>
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
                                            <input type="text" class="form-control rounded-1" name="rs_passengers" placeholder="Enter Number of Passengers" id="rs_passengers" value="">
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
                                            <label for="rs_approval_status" class="form-label mb-0">Approval Status</label>
                                            <select class="form-select" name="rs_approval_status" id="rs_approval_status">
                                                <option value="" disabled selected>Select Approval Status</option>
                                                <option value="Approved">Approved</option>
                                                <option value="Rejected">Rejected</option>
                                                <option value="Pending">Pending</option>
                                            </select>
                                            <span id="rs_approval_status_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_status" class="form-label mb-0">Reservation Status</label>
                                            <select class="form-select" name="rs_status" id="rs_status">
                                                <option value="" disabled selected>Select Status</option>
                                                <option value="On-going">On-going</option>
                                                <option value="Queued">Queued</option>
                                                <option value="Done">Done</option>
                                            </select>
                                            <span id="rs_status_error"></span>
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
                            <th>ID</th>                            
                            <th>Destination/Activity</th>
                            <th>From</th>                            
                            <th>Start Date</th>
                            <th>Start Time</th>
                            <th>End Date</th>
                            <th>End Time</th>
                            <th>Vehicles</th>
                            <th>Drivers</th>
                            <th>Requestor</th>
                            <th>Office</th>
                            <th>Trip Ticket No.</th>
                            <th>Passengers</th>
                            <th>Travel Type</th>
                            <th>Created At</th>
                            <th>Approval Status</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-------------EDIT MODAL --------------->
    <div class="modal fade" tabindex="-1" id="edit_reservation_modal" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reservation_edit" name="reservation_edit" class="form-horizontal">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">Edit Reservation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <input type="hidden" name="reservation_id" id="edit_reservation_id">

                                <div class="mb-2">
                                    <label for="event_edit" class="form-label mb-0">Destination/ Activity</label>
                                    <input type="text" class="form-control rounded-1" name="event_name" id="event_edit" placeholder="Enter Destination/Activity" required>
                                    <span id="event_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_from_edit" class="form-label mb-0">From</label>
                                    <input type="text" class="form-control rounded-1" name="rs_from" id="rs_from_edit" placeholder="Enter Starting Point" required>
                                    <span id="rs_from_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_date_start_edit" class="form-label mb-0">Start Date</label>
                                    <input type="date" class="form-control rounded-1" name="rs_date_start" id="rs_date_start_edit" required>
                                    <span id="rs_date_start_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_start_edit" class="form-label mb-0">Start Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start_edit" required>
                                    <span id="rs_time_start_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_date_end_edit" class="form-label mb-0">End Date</label>
                                    <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end_edit" required>
                                    <span id="rs_date_end_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_end_edit" class="form-label mb-0">End Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end_edit" required>
                                    <span id="rs_time_end_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="requestor_edit" class="form-label mb-0">Requestor</label>
                                    <select class="form-select" name="requestor_id" id="requestor_edit" required>
                                        @foreach ($requestors as $requestor)
                                        <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="requestor_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="office_edit" class="form-label mb-0">Office</label>
                                    <select class="form-select" name="off_id" id="office_edit" required>
                                        @foreach ($offices as $office)
                                        <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="office_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_passengers_edit" class="form-label mb-0">Passengers</label>
                                    <input type="number" class="form-control rounded-1" name="rs_passengers" id="rs_passengers_edit" required>
                                    <span id="rs_passengers_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_travel_type_edit" class="form-label mb-0">Travel Type</label>
                                    <input type="text" class="form-control rounded-1" name="rs_travel_type" id="rs_travel_type_edit" required>
                                    <span id="rs_travel_type_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_voucher_edit" class="form-label mb-0">Voucher</label>
                                    <input type="text" class="form-control rounded-1" name="rs_voucher" id="rs_voucher_edit" required>
                                    <span id="rs_voucher_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_approval_status_edit" class="form-label mb-0">Approval Status</label>
                                    <select class="form-select" name="rs_approval_status" id="rs_approval_status_edit" required>
                                        <option value="Pending">Pending</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                    </select>
                                    <span id="rs_approval_status_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_status_edit" class="form-label mb-0">Reservation Status</label>
                                    <select class="form-select" name="rs_status" id="rs_status_edit" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="On-going">On-going</option>
                                        <option value="Queued">Queued</option>
                                        <option value="Done">Done</option>
                                    </select>
                                    <span id="rs_status_edit_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="submit" value="update" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-------------CONFIRM MODAL --------------->
    <div class="modal fade" tabindex="-1" id="confirmModal" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirm_message"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" action="" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('.reservations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reservations.show') }}",
                    type: 'GET',
                    dataSrc: function(json) {
                        console.log('DataTables data:', json);
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        console.error('Response:', xhr.responseText);
                    }
                },
                columns: [
                    {data: 'reservation_id', name: 'reservation_id'},
                    {data: 'ev_name', name: 'ev_name'},
                    {data: 'rs_from', name: 'rs_from'},
                    {data: 'rs_date_start', name: 'rs_date_start'},
                    {data: 'rs_time_start', name: 'rs_time_start'},
                    {data: 'rs_date_end', name: 'rs_date_end'},
                    {data: 'rs_time_end', name: 'rs_time_end'},
                    {
                        data: 'vehicles',
                        name: 'vehicles',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    {
                        data: 'drivers',
                        name: 'drivers',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    {data: 'rq_full_name', name: 'rq_full_name'},
                    {
                        data: 'office',
                        name: 'office',
                        render: function(data, type, row) {
                            return data ? data : 'N/A';
                        }
                    },
                    {data: 'rs_voucher', name: 'rs_voucher'},
                    {data: 'rs_passengers', name: 'rs_passengers'},
                    {data: 'rs_travel_type', name: 'rs_travel_type'},
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            var date = new Date(data);
                            var formattedDate = date.toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                            return formattedDate;
                        }
                    },
                    {data: 'rs_approval_status', name: 'rs_approval_status'},
                    {data: 'rs_status', name: 'rs_status'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-sm btn-primary edit" data-id="${row.reservation_id}">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger delete" data-id="${row.reservation_id}">Delete</button>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']],
                drawCallback: function(settings) {
                    console.log('DataTables draw callback', settings);
                }
            });

            // Show the modal when the reserve button is clicked
            $('#insertBtn').on('click', function(e) {
                e.preventDefault(); // Prevent default action
                $('#insertModal').modal('show'); // Show the modal
            });

            // Handle form submission
            $('#reservations-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                var formData = $(this).serialize(); // Serialize form data

                $.ajax({
                    url: "{{ route('reservations.store') }}",
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log('Response:', response);
                        if(response.message === "Reservation created successfully") {
                            // Hide the modal
                            $('#insertModal').modal('hide');

                            // Reload the DataTable
                            table.ajax.reload(null, false);

                            // Show success message
                            showSuccessMessage(response.message);

                            // Clear the form
                            clearReservationForm();
                        } else {
                            console.error('Unexpected response structure:', response);
                            showErrorMessage('Error: Unexpected response from server');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error creating reservation:', xhr.responseText);
                        var errorMessage = 'Error creating reservation';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMessage += ': ' + xhr.responseJSON.error;
                        }
                        showErrorMessage(errorMessage);
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit', function() {
                var reservationId = $(this).data('id');
                $.ajax({
                    url: `/edit-reservation/${reservationId}`,
                    method: 'GET',
                    success: function(response) {
                        console.log('Edit response:', response);
                        var reservation = response.reservation;
                        
                        $('#edit_reservation_id').val(reservation.reservation_id);
                        $('#event_edit').val(reservation.events.ev_name);
                        $('#rs_from_edit').val(reservation.rs_from);
                        $('#rs_date_start_edit').val(reservation.rs_date_start);
                        $('#rs_time_start_edit').val(reservation.rs_time_start);
                        $('#rs_date_end_edit').val(reservation.rs_date_end);
                        $('#rs_time_end_edit').val(reservation.rs_time_end);
                        $('#requestor_edit').val(reservation.requestor_id);
                        $('#office_edit').val(reservation.off_id);
                        $('#rs_passengers_edit').val(reservation.rs_passengers);
                        $('#rs_travel_type_edit').val(reservation.rs_travel_type);
                        $('#rs_voucher_edit').val(reservation.rs_voucher);
                        $('#rs_approval_status_edit').val(reservation.rs_approval_status);
                        $('#rs_status_edit').val(reservation.rs_status);

                        // Populate drivers and vehicles
                        var driverSelect = $('#driver_id');
                        var vehicleSelect = $('#vehicle_id');
                        driverSelect.empty();
                        vehicleSelect.empty();

                        reservation.reservation_vehicles.forEach(function(rv, index) {
                            driverSelect.append(new Option(rv.drivers.dr_fname + ' ' + rv.drivers.dr_lname, rv.drivers.driver_id, false, true));
                            vehicleSelect.append(new Option(rv.vehicles.vh_brand + ' - ' + rv.vehicles.vh_plate, rv.vehicles.vehicle_id, false, true));
                        });

                        // If you're using Select2, you might need to trigger an update
                        // $('#driver_id, #vehicle_id').trigger('change');

                        // Show the edit modal
                        $('#edit_reservation_modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching reservation:', xhr.responseText);
                        showErrorMessage('Error fetching reservation');
                    }
                });
            });

            // Handle form submission for updating reservation
            $('#reservation_edit').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var reservationId = $('#edit_reservation_id').val();
                console.log('Form data being sent:', formData);
                console.log('Reservation ID:', reservationId);
                $.ajax({
                    url: "/admin/reservations/" + reservationId,
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        console.log('Update response:', response);
                        if (response.success) {
                            $('#edit_reservation_modal').modal('hide');
                            table.ajax.reload(null, false);
                            showSuccessMessage(response.success);
                        } else {
                            showErrorMessage(response.error || 'Error updating reservation');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating reservation:', xhr.responseText);
                        showErrorMessage('Error updating reservation: ' + xhr.responseText);
                    }
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete', function() {
                var reservationId = $(this).data('id');
                $('#confirmModal').modal('show');
                $('#ok_button').off('click').on('click', function() {
                    $.ajax({
                        url: `/delete-reservation/${reservationId}`,
                        method: 'GET',
                        success: function(response) {
                            $('#confirmModal').modal('hide');
                            table.ajax.reload(null, false);
                            showSuccessMessage('Reservation deleted successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting reservation:', xhr.responseText);
                            showErrorMessage('Error deleting reservation');
                        }
                    });
                });
            });

            // Function to clear the reservation form
            function clearReservationForm() {
                $('#reservations-form')[0].reset();

                // Reset select2 dropdowns if you're using them
                $('#reservations-form select').val(null).trigger('change');

                // Clear any error messages
                $('#reservations-form .error').text('');

                // Reset any custom elements or plugins
                // For example, if you're using a date picker:
                // $('#date_field').datepicker('setDate', null);
            }

            // Function to show success message
            function showSuccessMessage(message) {
                var html = "<div class='alert alert-success alert-dismissible fade show' role='alert'>" +
                           "<strong>Success!</strong> " + message +
                           "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>" +
                           "</div>";
                $('#form_result').html(html);
                // Automatically close the alert after 5 seconds
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }

            // Function to show error message
            function showErrorMessage(message) {
                var html = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>" +
                           "<strong>Error!</strong> " + message +
                           "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>" +
                           "</div>";
                $('#form_result').html(html);
            }
        });
    </script>
</body>
</html>



