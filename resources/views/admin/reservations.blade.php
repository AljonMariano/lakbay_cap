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
                            <form action="" method="POST" class="reservations-form" id="reservations-form" name="reservations-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="reservation_id" value="">

                                        <div class="mb-2">
                                            <label for="event_id" class="form-label mb-0">Event Name</label>
                                            <select class="form-select" name="event_id" id="event_id">
                                                <option value="" disabled selected>Select Event</option>
                                                @foreach ($events as $event)
                                                <option value="{{ $event->event_id }}">{{ $event->ev_name }} - {{ $event->ev_venue }}</option>
                                                @endforeach
                                            </select>
                                            <span id="event_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="driver_id" class="form-label mb-0">Driver</label>
                                            <select class="form-select" name="driver_id[]" id="driver_id">
                                                <option value="" disabled selected>Select Driver</option>
                                                @foreach ($drivers as $driver)
                                                <option value="{{$driver->driver_id}}">{{ $driver->dr_fname }} {{ $driver->dr_mname }} {{ $driver->dr_lname }}</option>
                                                @endforeach
                                            </select>
                                            <span id="driver_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="vehicle_id" class="form-label mb-0">Vehicle</label>
                                            <select class="form-select" name="vehicle_id[]" id="vehicle_id">
                                                <option value="" disabled selected>Select Vehicle</option>
                                                @foreach ($vehicles as $vehicle)
                                                <option value="{{ $vehicle->vehicle_id }}"> {{ $vehicle->vh_brand }} - {{ $vehicle->vh_type }} - {{ $vehicle->vh_plate }} - {{$vehicle->vh_capacity}}</option>
                                                @endforeach
                                            </select>
                                            <span id="vehicle_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="requestor_id" class="form-label mb-0">Requestor</label>
                                            <select class="form-select" name="requestor_id" id="requestor_id">
                                                <option value="" disabled selected>Select Requestor</option>
                                                @foreach ($requestors as $requestor)
                                                <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="requestor_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="office" class="form-label mb-0">Office</label>
                                            <select class="form-select" name="off_id[]" id="office_id">
                                                @foreach ($offices as $office)
                                                <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                                @endforeach
                                            </select>
                                            <span id="office_id_error"></span>
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
                                            <label for="rs_voucher" class="form-label mb-0">Voucher</label>
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
                            <th>Event Name</th>
                            <th>Vehicles</th>
                            <th>Drivers</th>
                            <th>Requestor</th>
                            <th>Office</th>
                            <th>Voucher</th>
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
    <div class="modal fade" tabindex="-1" id="edit_reservation_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="reservation_edit" name="reservation_edit" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">Edit Reservation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <input type="hidden" name="reservation_id" id="edit_reservation_id" value="">

                                <div class="mb-2">
                                    <label for="event_edit" class="form-label mb-0">Destination/Activity</label>
                                    <select class="form-select" name="event_id" id="event_edit">
                                    </select>
                                    <span id="event_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="driver_edit" class="form-label mb-0">Driver</label>
                                    <select class="form-select" name="driver_id[]" id="driver_edit" multiple>
                                    </select>
                                    <span id="driver_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="vehicle_edit" class="form-label mb-0">Vehicle</label>
                                    <select class="form-select" name="vehicle_id[]" id="vehicle_edit" multiple>
                                    </select>
                                    <span id="vehicle_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="requestor_edit" class="form-label mb-0">Requestor</label>
                                    <select class="form-select" name="requestor_id" id="requestor_edit">
                                        @foreach ($requestors as $requestor)
                                        <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="requestor_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_voucher_edit" class="form-label mb-0">Voucher</label>
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

                                <div class="mb-2">
                                    <label for="rs_passengers_edit" class="form-label mb-0">Passengers</label>
                                    <input type="text" class="form-control rounded-1" name="rs_passengers" id="rs_passengers_edit" placeholder="Enter Number of Passengers">
                                    <span id="rs_passengers_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_travel_type_edit" class="form-label mb-0">Travel Type</label>
                                    <input type="text" class="form-control rounded-1" name="rs_travel_type" id="rs_travel_type_edit" placeholder="Enter Travel Type">
                                    <span id="rs_travel_type_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="office_edit" class="form-label mb-0">Office</label>
                                    <select class="form-select" name="off_id" id="office_edit">
                                        @foreach ($offices as $office)
                                        <option value="{{ $office->off_id }}">{{ $office->off_name }}</option>
                                        @endforeach
                                    </select>
                                    <span id="office_edit_error"></span>
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
    <div class="modal fade" tabindex="-1" id="confirmModal">
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
            $('.drivers-select, .events-edit, .drivers-edit, .vehicles-edit').select2();

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
                    { data: 'ev_name', name: 'events.ev_name' },
                    { 
                        data: 'vehicles',
                        name: 'reservation_vehicles.vehicles.vh_brand'
                    },
                    { 
                        data: 'drivers',
                        name: 'reservation_vehicles.drivers.dr_fname'
                    },
                    { data: 'rq_full_name', name: 'requestors.rq_full_name' },
                    { 
                        data: 'office', 
                        name: 'office.off_name',
                        render: function(data, type, row, meta) {
                            return data ? data : 'N/A';
                        }
                    },
                    { data: 'rs_voucher', name: 'rs_voucher' },
                    { data: 'rs_passengers', name: 'rs_passengers' },
                    { data: 'rs_travel_type', name: 'rs_travel_type' },
                    { 
                        data: 'created_at', 
                        name: 'created_at',
                        render: function(data, type, row, meta) {
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
                                <button type="button" class="btn btn-sm btn-primary edit" edit-id="${row.reservation_id}">Edit</button>
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
                        getEvents(data);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                $("#insertModal").modal("show");
            });

            // EDIT
            $(document).on('click', '.edit', function(e) {
                e.preventDefault();
                $('#reservation_edit')[0].reset();
                var reservation_id = $(this).attr('edit-id');
                var action_url = "{{ route('reservations.edit', ':id') }}".replace(':id', reservation_id);
                
                $.ajax({
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: action_url,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data); // Add this line to log the data to the console

                        var reservation = data.result;
                        var rowVehicles = reservation.reservation_vehicles;
                        var vehicle_ids = rowVehicles.map((item) => item.vehicle_id);
                        var driver_ids = rowVehicles.map((item) => item.driver_id).filter((item) => item != null);

                        editEvents(reservation.events, reservation.event_id);
                        editDrivers(reservation.reservation_vehicles.map(rv => rv.drivers), driver_ids);
                        editVehicles(reservation.reservation_vehicles.map(rv => rv.vehicles), vehicle_ids);

                        $('#requestor_edit').val(reservation.requestor_id);
                        $('#rs_voucher_edit').val(reservation.rs_voucher);
                        $('#rs_approval_status_edit').val(reservation.rs_approval_status);
                        $('#rs_status_edit').val(reservation.rs_status);
                        $('#rs_passengers_edit').val(reservation.rs_passengers); // Passengers
                        $('#rs_travel_type_edit').val(reservation.rs_travel_type); // Travel Type
                        $('#office_edit').val(reservation.off_id); // Office
                        $('#hidden_id').val(reservation_id);
                        $('#edit_reservation_modal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });

                $('#form_result').html('');
            });

            // STORE
            $('#reservations-form').on('submit', function(event) {
                event.preventDefault();
                var action_url = "{{url('/insert-reservation')}}";
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: action_url,
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        var html = '';
                        if (data.success) {
                            html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                            $('#reservations-table').DataTable().ajax.reload();

                            $("#insertModal").modal("hide");
                        }
                        $('#form_result').html(html);
                    },
                    error: function(data) {
                        var errors = data.responseJSON.errors;
                        var html = '<span class="text-danger">';
                        $.each(errors, function(key, value) {
                            $('#' + key + '_error').html(html + value + '</span>');
                            $('#' + key).on('input', function() {
                                if ($(this).val().trim() !== '') {
                                    $('#' + key + '_error').empty();
                                }
                            });
                        });
                    }
                });
            });

            // UPDATE
            $('#reservation_edit').on('submit', function(event) {
                event.preventDefault();
                var action_url = "{{ url('/update-reservation')}}";
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: action_url,
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var count = 0; count < data.errors.length; count++) {
                                html += '<p>' + data.errors[count] + '</p>';
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                            $('#reservations-table').DataTable().ajax.reload();
                            $('#edit_reservation_modal').modal('hide');
                            $('#reservation_edit')[0].reset();
                        }
                        $('#form_result').html(html);
                    },
                    error: function(data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            });

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
                if (data.length > 0) {
                    var selectOptions = [];
                    $.each(data, function(index, event) {
                        selectOptions += "<option value='" + event.event_id + "'>" + event.ev_name + " - " + event.ev_venue + "</option>";
                    });
                    $('#event_id').html(selectOptions);
                } else {
                    $('#event_id').html('<option value="" disabled selected>No events available</option>');
                }
            }

            function editEvents(data, id) {
                if (data.length > 0) {
                    var selectOptions = '';
                    $.each(data, function(index, events) {
                        selectOptions += "<option value='" + events.event_id + "'>" + events.ev_name + " - " + events.ev_venue + "</option>";
                    });
                    $('#event_edit').html(selectOptions);
                    $('#event_edit').val(id);
                } else {
                    $('#event_edit').html('<option value="" disabled selected>No events available</option>');
                }
            }

            function editDrivers(data, ids) {
                if (data.length > 0) {
                    var selectOptions = [];
                    $.each(data, function(index, driver) {
                        selectOptions += "<option value='" + driver.driver_id + "'>" + driver.dr_fname + "</option>";
                    });
                    $('#driver_edit').html(selectOptions);
                    $('#drivers-edit').select2();
                    $('#driver_edit').select2().val(ids).change();
                } else {
                    $('#driver_edit').html('<option value="" disabled selected>No drivers available</option>');
                }
            }

            function editVehicles(data, ids) {
                if (data.length > 0) {
                    var selectOptions = [];
                    $.each(data, function(index, vehicle) {
                        selectOptions += "<option value='" + vehicle.vehicle_id + "'>" + vehicle.vh_brand + "</option>";
                    });
                    $('#vehicle_edit').html(selectOptions);
                    $('#vehicle_edit').select2().val(ids).change();
                } else {
                    $('#vehicle_edit').html('<option value="" disabled selected>No vehicles available</option>');
                }
            }
        });
    </script>

    @include('includes.footer')
</body>
</html>



