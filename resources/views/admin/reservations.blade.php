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
                                        <input type="hidden" name="reservation_id" value="">

                                        <div class="mb-2">
                                            <label for="event_id" class="form-label mb-0">Destination/ Activity</label>
                                            <input type="text" class="form-control rounded-1" name="event_name" id="event_name" placeholder="Enter Destination/Activity" required>
                                            <span id="event_name_error"></span>
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
                                <input type="hidden" name="hidden_id" id="edit_reservation_id" value="">

                                <div class="mb-2">
                                    <label for="event_edit" class="form-label mb-0">Destination/Activity</label>
                                    <input type="text" class="form-control rounded-1" name="event_name" id="event_edit" placeholder="Enter Destination/Activity" required>
                                    <span id="event_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="driver_edit" class="form-label mb-0">Driver</label>
                                    <select class="form-select" name="driver_id" id="driver_edit">
                                        <option value="" disabled selected>Select Driver</option>
                                        @foreach ($drivers as $driver)
                                        <option value="{{$driver->driver_id}}">{{ $driver->dr_fname }} {{ $driver->dr_mname }} {{ $driver->dr_lname }}</option>
                                        @endforeach
                                    </select>
                                    <span id="driver_id_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="vehicle_edit" class="form-label mb-0">Vehicle</label>
                                    <select class="form-select" name="vehicle_id" id="vehicle_edit">
                                        <option value="" disabled selected>Select Vehicle</option>
                                        @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->vehicle_id }}"> {{ $vehicle->vh_brand }} - {{ $vehicle->vh_type }} - {{ $vehicle->vh_plate }} - {{$vehicle->vh_capacity}}</option>
                                        @endforeach
                                    </select>
                                    <span id="vehicle_id_error"></span>
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
                                    <input type="text" class="form-control rounded-1" name="rs_passengers" id="rs_passengers_edit" placeholder="Enter Number of Passengers">
                                    <span id="rs_passengers_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_travel_type_edit" class="form-label mb-0">Travel Type</label>
                                    <select class="form-select" name="rs_travel_type" id="rs_travel_type_edit">
                                        <option value="" disabled selected>Select Travel Type</option>
                                        <option value="Outside Province Transport">Outside Province Transport</option>
                                        <option value="Daily Transport">Daily Transport</option>
                                    </select>
                                    <span id="rs_travel_type_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_voucher_edit" class="form-label mb-0">Trip Ticket No.</label>
                                    <input type="text" class="form-control rounded-1" name="rs_voucher" id="rs_voucher_edit" placeholder="Enter Voucher code">
                                    <span id="rs_voucher_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_approval_status_edit" class="form-label mb-0">Approval Status</label>
                                    <select class="form-select" name="rs_approval_status" id="rs_approval_status_edit">
                                        <option value="" disabled selected>Select Approval Status</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Rejected">Rejected</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                    <span id="rs_approval_status_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_status_edit" class="form-label mb-0">Reservation Status</label>
                                    <select class="form-select" name="rs_status" id="rs_status_edit">
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
    var table = $('.reservations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('reservations.show') }}",
            type: 'GET',
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX error: " + textStatus + ' : ' + errorThrown);
            }
        },
        columns: [
    { data: 'reservation_id', name: 'reservation_id' },
    {
        data: 'ev_name',
        name: 'ev_name',
        render: function(data, type, row) {
            return row.events ? row.events.ev_name : 'N/A';
        }
    },
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
    { data: 'rq_full_name', name: 'requestors.rq_full_name' },
    { 
        data: 'office', 
        name: 'office.off_name',
        render: function(data, type, row) {
            return data ? data : 'N/A';
        }
    },
    { data: 'rs_voucher', name: 'rs_voucher' },
    { data: 'rs_passengers', name: 'rs_passengers' },
    { data: 'rs_travel_type', name: 'rs_travel_type' },
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
    { data: 'rs_approval_status', name: 'rs_approval_status' },
    { data: 'rs_status', name: 'rs_status' },
    { 
        data: 'action', 
        name: 'action', 
        orderable: false, 
        searchable: false,
        render: function(data, type, row) {
            return `
                <button type="button" class="btn btn-sm btn-primary edit" data-id="${row.reservation_id}">Edit</button>
                <button type="button" class="btn btn-sm btn-danger delete" id="${row.reservation_id}">Delete</button>
            `;
        }
    }
],
        order: [
            [0, 'asc']
        ]
    });


            
            // INSERT
            $("#insertBtn").click(function() {
                var action_url = "{{ route('reservations.getEvents') }}";
                $.ajax({
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: action_url,
                    dataType: 'json',
                    success: function(data) {
                        console.log("Events data received:", data);
                        getEvents(data);
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", error);
                        console.error("Response:", xhr.responseText);
                    }
                });

                $("#insertModal").modal("show");
            });

// EDIT
$(document).on('click', '.edit', function(e) {
    e.preventDefault();
    var reservation_id = $(this).data('id');
    var action_url = "{{ route('reservations.edit', ':id') }}".replace(':id', reservation_id);
    
    console.log("Edit button clicked for reservation ID:", reservation_id);

    $.ajax({
        url: action_url,
        type: 'GET',
        success: function(response) {
            console.log("Edit data received:", response);
            if (response.result) {
                populateEditForm(response.result);
                $('#edit_reservation_modal').modal('show');
            } else {
                console.error("Unexpected response structure:", response);
                showErrorMessage('Error: Unexpected response from server');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching reservation data:", error);
            console.error("Response:", xhr.responseText);
            showErrorMessage('Error fetching reservation data');
        }
    });
});

function populateEditForm(reservation) {
    $('#edit_reservation_id').val(reservation.reservation_id);
    $('#event_edit').val(reservation.events.ev_name);
    $('#driver_edit').val(reservation.reservation_vehicles[0].driver_id);
    $('#vehicle_edit').val(reservation.reservation_vehicles[0].vehicle_id);
    $('#requestor_edit').val(reservation.requestor_id);
    $('#office_edit').val(reservation.off_id);
    $('#rs_passengers_edit').val(reservation.rs_passengers);
    $('#rs_travel_type_edit').val(reservation.rs_travel_type);
    $('#rs_voucher_edit').val(reservation.rs_voucher);
    $('#rs_approval_status_edit').val(reservation.rs_approval_status);
    $('#rs_status_edit').val(reservation.rs_status);
}

// UPDATE
$('#reservation_edit').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    console.log('Form data being sent:', formData);

    $.ajax({
        url: "{{ route('reservations.update') }}",
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Update response:', response);
            if (response.success) {
                $('#edit_reservation_modal').modal('hide');
                $('#reservations-table').DataTable().ajax.reload();
                showSuccessMessage(response.success);
            } else {
                showErrorMessage(response.error || 'An error occurred while updating the reservation');
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', xhr.responseText);
            showErrorMessage('An error occurred while updating the reservation');
        }
    });
});


    
// STORE
$('#reservations-form').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    
    console.log('Form data being sent:');
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Response:', response);
            if(response.reservation) {
                console.log('Saved reservation:', response.reservation);
                
                // Hide the modal
                $('#insertModal').modal('hide');
                
                // Reload the DataTable
                $('#reservations-table').DataTable().ajax.reload(null, false);
                
                // Show success message
                showSuccessMessage('Reservation created successfully');

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





$('#reservation_edit').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    console.log('Form data:', formData);

    $.ajax({
        url: '/admin/reservations/update',
        type: 'PUT',
        data: formData,
        success: function(response) {
            console.log('Update success:', response);
            if (response.reservation) {
                // Hide the modal
                $('#edit_reservation_modal').modal('hide');
                
                // Reload the DataTable
                $('#reservations-table').DataTable().ajax.reload();
                
                // Show success message
                showSuccessMessage('Reservation updated successfully');
            }
        },
        error: function(xhr, status, error) {
            console.error('Update error:', xhr.responseText);
            // Show error message
            showErrorMessage('An error occurred while updating the reservation');
        }
    });
});

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

            // CANCEL
            var reservation_id;
            $(document).on('click', '.cancel', function() {
                reservation_id = $(this).attr('id');
                $('#confirm_message').text("Are You sure you want to Cancel?");
                $('#confirmModal').modal('show');
                $('#ok_button').prop('action', 'cancelled');
            });

            // DELETE
            $(document).on('click', '.delete', function() {
                reservation_id = $(this).attr('id');
                $('#confirm_message').text("Are You sure you want to Delete?");
                $('#confirmModal').modal('show');
                $('#ok_button').prop('action', 'delete');
            });

            $('#ok_button').click(function() {
                var action = $(this).prop('action');
                if (action == 'delete') {
                    $.ajax({
                        url: "/delete-reservation/" + reservation_id,
                        success: function(data) {
                            setTimeout(function() {
                                $('#confirmModal').modal('hide');
                                $('#reservations-table').DataTable().ajax.reload();
                            });
                        }
                    });
                }
            });

            // Helper functions
            function getEvents(data) {
                console.log("Inside getEvents function, data:", data);
                if (data && data.length > 0) {
                    var selectOptions = '<option value="" disabled selected>Select Event</option>';
                    $.each(data, function(index, event) {
                        console.log("Processing event:", event);
                        selectOptions += "<option value='" + event.event_id + "'>" + event.ev_name + " - " + event.ev_venue + "</option>";
                    });
                    console.log("Generated options:", selectOptions);
                    $('#event_id').html(selectOptions);
                } else {
                    console.log("No events data or empty array");
                    $('#event_id').html('<option value="" disabled selected>No events available</option>');
                }
            }
            function editEvents(data, selectedId) {
    var selectElement = $('#event_edit');
    selectElement.empty();
    
    console.log("Editing events, data:", data);
    console.log("Selected ID:", selectedId);

    if (data && data.length > 0) {
        selectElement.append('<option value="" disabled>Select Destination/Activity</option>');
        $.each(data, function(index, event) {
            var selected = (event.event_id == selectedId) ? 'selected' : '';
            selectElement.append(`<option value="${event.event_id}" ${selected}>${event.ev_name} - ${event.ev_venue}</option>`);
        });
    } else {
        selectElement.append('<option value="" disabled selected>No events available</option>');
    }
}

function editDrivers(data, selectedIds) {
    var selectElement = $('#driver_edit');
    selectElement.empty();
    
    if (data && data.length > 0) {
        selectElement.append('<option value="" disabled>Select Driver</option>');
        $.each(data, function(index, driver) {
            var selected = selectedIds.includes(driver.driver_id) ? 'selected' : '';
            selectElement.append(`<option value="${driver.driver_id}" ${selected}>${driver.dr_fname} ${driver.dr_mname} ${driver.dr_lname}</option>`);
        });
    } else {
        selectElement.append('<option value="" disabled>No drivers available</option>');
    }
}


function editVehicles(data, selectedIds) {
    var selectElement = $('#vehicle_edit');
    selectElement.empty();
    
    if (data && data.length > 0) {
        selectElement.append('<option value="" disabled>Select Vehicle</option>');
        $.each(data, function(index, vehicle) {
            var selected = selectedIds.includes(vehicle.vehicle_id) ? 'selected' : '';
            selectElement.append(`<option value="${vehicle.vehicle_id}" ${selected}>${vehicle.vh_brand} - ${vehicle.vh_type} - ${vehicle.vh_plate} - ${vehicle.vh_capacity}</option>`);
        });
    } else {
        selectElement.append('<option value="" disabled>No vehicles available</option>');
    }
}
        });
    </script>

    @include('includes.footer')
</body>
</html>



