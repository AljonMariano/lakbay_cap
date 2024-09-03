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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <style>
        input[type="date"], input[type="time"] {
            position: relative;
            z-index: 1;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple {
            min-height: 38px;
            height: auto;
            overflow: hidden;
            padding: 2px 6px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 2px 5px;
            margin: 2px;
            font-size: 0.875rem;
            max-width: calc(100% - 4px);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
            margin: 0;
            padding: 0;
            min-height: 30px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            margin-right: 3px;
        }
        .table-responsive {
            max-width: 95%;
            margin-left: auto;
            margin-right: auto;
        }
        #reservations-table {
            width: 100% !important;
        }
        .container, .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }
        
        #reservations-table_wrapper {
            margin-left: -15px !important;
            width: calc(100% + 30px) !important;
        }
        
        #reservations-table {
            width: 100% !important;
        }
        
        .dataTables_wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }
        
        #reservations-table th,
        #reservations-table td {
            padding-left: 15px !important;
        }
        
        .dataTables_filter, .dataTables_length {
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        /* New styles to adjust table positioning */
        .cover-container {
            max-width: 100% !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        .container, .container-fluid {
            max-width: 100% !important;
            padding-left: 15px !important;
            padding-right: 15px !important;
        }

        #reservations-table_wrapper {
            width: 100% !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        #reservations-table {
            width: 100% !important;
        }

        .dataTables_wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
        }

        .dataTables_filter {
            text-align: right !important;
        }

        @media (min-width: 768px) {
            .container, .container-fluid {
                padding-left: 30px !important;
                padding-right: 30px !important;
            }
        }

        .select2-container {
            width: 100% !important;
        }
        .select2-selection--multiple {
            overflow: hidden !important;
            height: auto !important;
        }

        .table-responsive {
            overflow-x: auto;
        }
        #reservations-table {
            width: 100% !important;
            border-collapse: collapse;
        }
        #reservations-table th, #reservations-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            white-space: nowrap;
        }
        #reservations-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        #reservations-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        #reservations-table tr:hover {
            background-color: #f5f5f5;
        }
        .action-buttons button {
            margin: 2px;
        }

        #alert-container {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        #success-message, #error-message {
            margin-bottom: 0;
        }

        .select2-results__option[aria-disabled=true] {
            opacity: 0.6;
        }
        .select2-results__option[aria-disabled=true]:hover {
            cursor: not-allowed;
        }
        .select2-selection__choice__remove {
            display: none !important;
        }
    </style>
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
                            <button type="button" class="btn-close" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('reservations.store') }}" method="POST" class="reservations-form" id="reservations-form" name="reservations-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="reservation_id" id="edit_reservation_id" value="">

                                        <div class="mb-2">
                                            <label for="destination_activity" class="form-label mb-0">Destination/Activity</label>
                                            <input type="text" class="form-control rounded-1" id="destination_activity" name="destination_activity" placeholder="Enter Destination/Activity" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_from" class="form-label mb-0">From</label>
                                            <input type="text" class="form-control rounded-1" name="rs_from" id="rs_from" placeholder="Enter Starting Point" required>
                                            <span id="rs_from_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_start" class="form-label mb-0">Start Date</label>
                                            <input type="text" id="rs_date_start" name="rs_date_start" class="form-control rounded-1 datepicker" placeholder="Select Start Date" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_start" class="form-label mb-0">Start Time</label>
                                            <input type="text" id="rs_time_start" name="rs_time_start" class="form-control rounded-1 timepicker" placeholder="Select Start Time" required>
                                            <button type="button" class="btn btn-sm btn-secondary mt-1" onclick="setCurrentTime('rs_time_start')">Set Current Time</button>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_end" class="form-label mb-0">End Date</label>
                                            <input type="text" id="rs_date_end" name="rs_date_end" class="form-control rounded-1 datepicker" placeholder="Select End Date" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_end" class="form-label mb-0">End Time</label>
                                            <input type="text" id="rs_time_end" name="rs_time_end" class="form-control rounded-1 timepicker" placeholder="Select End Time" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="driver_id" class="form-label mb-0">Driver</label>
                                            <select class="form-control" id="driver_id" name="driver_id[]" multiple required>
                                            </select>
                                            <span id="driver_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="vehicle_id" class="form-label mb-0">Vehicle</label>
                                            <select class="form-control" id="vehicle_id" name="vehicle_id[]" multiple required>
                                            </select>
                                            <span id="vehicle_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="is_outsider">
                                                <input type="checkbox" id="is_outsider" name="is_outsider" value="1"> Outside of Capitol?
                                            </label>
                                        </div>

                                        <div class="mb-2">
                                            <label for="off_id">Office</label>
                                            <select class="form-control" id="off_id" name="off_id">
                                                <option value="" disabled selected>Select Office</option>
                                                @foreach ($offices as $office)
                                                    <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control d-none" id="outside_office" name="outside_office" placeholder="Enter outside office">
                                        </div>

                                        <div class="mb-2">
                                            <label for="requestor_id">Requestor</label>
                                            <select class="form-control" id="requestor_id" name="requestor_id">
                                                <option value="" disabled selected>Select Requestor</option>
                                                @foreach($requestors as $requestor)
                                                    <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control d-none" id="outside_requestor" name="outside_requestor" placeholder="Enter outside requestor">
                                            <span id="requestor_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_passengers" class="form-label mb-0">Passengers</label>
                                            <input type="number" class="form-control rounded-1" name="rs_passengers" id="rs_passengers" placeholder="Enter Number of Passengers" required>
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
                                            <label for="rs_purpose" class="form-label mb-0">Purpose</label>
                                            <input type="text" class="form-control rounded-1" name="rs_purpose" placeholder="Enter Purpose" id="rs_purpose" required>
                                            <span id="rs_purpose_error"></span>
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

    <!-- Add the alerts here, just above the table -->
    <div id="alert-container" class="mb-3">
        <div id="success-message" class="alert alert-success d-none" role="alert"></div>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
    </div>

    <span id="form_result"></span>
    <div class="table-responsive">
        <table id="reservations-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Destination/Activity</th>
                    <th>From</th>
                    <th>Start Date</th>
                    <th>Start Time</th>
                    <th>End Date</th>
                    <th>End Time</th>
                    <th>Requestor</th>
                    <th>Office</th>
                    <th>Driver</th>
                    <th>Vehicle</th>
                    <th>Purpose</th>
                    <th>Passengers</th>
                    <th>Travel Type</th>
                    <th>Reserved on</th>
                    <th>Approval Status</th>
                    <th>Status</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
      

    <script>
        var reservationsDataUrl = "{{ route('reservations.data') }}";
    </script>
    <script>
        window.appRoutes = {
            getDrivers: "{{ route('get.drivers') }}",
            getVehicles: "{{ route('get.vehicles') }}",
            approve: "{{ route('reservations.approve', ':id') }}",
            reject: "{{ route('reservations.reject', ':id') }}",
            cancel: "{{ route('reservations.cancel', ':id') }}",            
            update: "{{ route('reservations.update', ':id') }}",
            edit: "{{ route('reservations.edit', ':id') }}",
            done: "{{ route('reservations.done', ':id') }}",
            destroy: "{{ route('reservations.destroy', ':id') }}",
            getDriversAndVehicles: "{{ route('get.drivers.vehicles') }}",
        };
    </script>
    <script src="{{ asset('js/admin/reservations.js') }}"></script>
    <script src="{{ asset('js/admin/reservation-print.js') }}"></script>
    <script>
        console.log('Reservations.js loaded and executed');

      
        $(document).ready(function() {
            console.log('Document ready in reservations.js');
        });

       
        setTimeout(function() {
            console.log('Delayed log from reservations.js');
        }, 1000);
    </script>
    <script>
        if (typeof jQuery != 'undefined') {
            console.log('jQuery is loaded');
        } else {
            console.log('jQuery is not loaded');
        }
    </script>
    <script>
        console.log('Inline script in reservations.blade.php executed');
    </script>
    <script>
       
        $(document).ready(function() {
            // Initialize datepicker
            $(".datepicker").flatpickr({
                dateFormat: "Y-m-d",
                allowInput: true,
                clickOpens: true
            });

            // Initialize timepicker
            $(".timepicker").flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "h:i K", 
                time_24hr: false,
                allowInput: true,
                clickOpens: true
            });
        });

        // Define the setCurrentTime function
        function setCurrentTime(inputId) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const currentTime = `${hours}:${minutes}`;
            
            const input = document.getElementById(inputId);
            input.value = currentTime;
            
            // Trigger the change event to update flatpickr
            const event = new Event('input', { bubbles: true });
            input.dispatchEvent(event);
        }
    </script>

    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approvalModalLabel">Confirm Approval</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to approve this reservation?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmApproval">Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Reject Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectionForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason for Rejection</label>
                            <textarea class="form-control" id="rejectionReason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancellationModalLabel">Cancel Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancellationForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancellationReason" class="form-label">Reason for Cancellation</label>
                            <textarea class="form-control" id="cancellationReason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Cancel Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="printConfirmationModal" tabindex="-1" aria-labelledby="printConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="printConfirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure?</p>
                    <p>Would you like to print it?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmDone">Yes</button>
                    <button type="button" class="btn btn-info" id="confirmPrint">Print</button>
                </div>
            </div>
        </div>
    </div>

    

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this reservation?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_reservation_modal" tabindex="-1" aria-labelledby="editReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReservationModalLabel">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_reservation_form" method="POST">
                    @csrf
                    @method('POST')
                    <input type="hidden" name="reservation_id" id="edit_reservation_id">
                    <div class="modal-body">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label for="destination_activity_edit" class="form-label mb-0">Destination/Activity</label>
                                    <input type="text" class="form-control rounded-1" id="destination_activity_edit" name="destination_activity" placeholder="Enter Destination/Activity" required>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_from_edit" class="form-label mb-0">From</label>
                                    <input type="text" class="form-control rounded-1" id="rs_from_edit" name="rs_from" placeholder="Enter From" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="rs_date_start_edit" class="form-label mb-0">Start Date</label>
                                        <input type="date" class="form-control rounded-1 datepicker" id="rs_date_start_edit" name="rs_date_start" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="rs_time_start_edit" class="form-label mb-0">Start Time</label>
                                        <input type="time" class="form-control rounded-1 timepicker" id="rs_time_start_edit" name="rs_time_start" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="rs_date_end_edit" class="form-label mb-0">End Date</label>
                                        <input type="date" class="form-control rounded-1 datepicker" id="rs_date_end_edit" name="rs_date_end" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label for="rs_time_end_edit" class="form-label mb-0">End Time</label>
                                        <input type="time" class="form-control rounded-1 timepicker" id="rs_time_end_edit" name="rs_time_end" required>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="driver_id_edit" class="form-label mb-0">Driver</label>
                                    <select class="form-control" id="driver_id_edit" name="driver_id[]" multiple required>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label for="vehicle_id_edit" class="form-label mb-0">Vehicle</label>
                                    <select class="form-control" id="vehicle_id_edit" name="vehicle_id[]" multiple required>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label for="is_outsider_edit">
                                        <input type="checkbox" id="is_outsider_edit" name="is_outsider" value="1"> Outside of Capitol?
                                    </label>
                                </div>

                                <div class="mb-2">
                                    <label for="off_id_edit">Office</label>
                                    <select class="form-control" id="off_id_edit" name="off_id">
                                        <option value="" disabled selected>Select Office</option>
                                        @foreach ($offices as $office)
                                            <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control d-none" id="outside_office_edit" name="outside_office" placeholder="Enter outside office">
                                </div>

                                <div class="mb-2">
                                    <label for="requestor_id_edit">Requestor</label>
                                    <select class="form-control" id="requestor_id_edit" name="requestor_id">
                                        <option value="" disabled selected>Select Requestor</option>
                                        @foreach($requestors as $requestor)
                                            <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control d-none" id="outside_requestor_edit" name="outside_requestor" placeholder="Enter outside requestor">
                                    <span id="requestor_id_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_passengers_edit" class="form-label mb-0">Passengers</label>
                                    <input type="number" class="form-control rounded-1" name="rs_passengers" id="rs_passengers_edit" placeholder="Enter Number of Passengers" required>
                                    <span id="rs_passengers_edit_error"></span>
                                </div>
                                
                                <div class="mb-2">
                                    <label for="rs_travel_type_edit" class="form-label mb-0">Travel Type</label>
                                    <select class="form-select" name="rs_travel_type" id="rs_travel_type_edit">
                                        <option value="" disabled selected>Select Travel Type</option>
                                        <option value="Within Province Transport">Within Province Transport</option>
                                        <option value="Outside Province Transport">Outside Province Transport</option>
                                        <option value="Daily Transport">Daily Transport</option>
                                    </select>
                                    <span id="rs_travel_type_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_purpose_edit" class="form-label mb-0">Purpose</label>
                                    <input type="text" class="form-control rounded-1" name="rs_purpose" placeholder="Enter Purpose" id="rs_purpose_edit" required>
                                    <span id="rs_purpose_edit_error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Reservation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>






