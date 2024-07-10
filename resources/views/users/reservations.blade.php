<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reservations</title>
    <?php $title_page = 'Reservations';?>
    @include('includes.user_header')
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
                            <td>Vehicle</td>
                            <td>Driver</td>
                            <td>Requestor</td>
                            <td>Voucher</td>
                            <td>Travel Type</td>
                            <td>Passengers</td>
                            <td>Date Filed</td>
                            <td>Approval</td>
                            <td>Status</td>
                            <td>Office</td>
                            
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
        var table = $('.reservations-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('users.reservations.show') }}",
                error: function (xhr, error, thrown) {
                    console.error('DataTables AJAX error:', error, thrown);
                }
            },
            columns: [
                {data: 'reservation_id', name: 'reservation_id'},
                {data: 'ev_name', name: 'ev_name'},
                {data: 'vehicles', name: 'vehicles'},
                {data: 'drivers', name: 'drivers'},
                {data: 'rq_full_name', name: 'rq_full_name'},
                {data: 'rs_voucher', name: 'rs_voucher'},
                {data: 'rs_travel_type', name: 'rs_travel_type'},
                {data: 'rs_passengers', name: 'rs_passengers'},
                {data: 'created_at', name: 'created_at'},
                {data: 'rs_approval_status', name: 'rs_approval_status'},
                {data: 'rs_status', name: 'rs_status'},
                {data: 'office', name: 'office'},
            ],
            order: [[0, 'desc']],
            drawCallback: function(settings) {
                console.log('DataTables draw callback', settings);
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
        url: "{{ route('users.reservations.store') }}",
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            console.log('Response:', response);
            if(response.success) {
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
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('insertModal'), {
        keyboard: false
    });
});
</script>

@include('includes.footer');
</html>