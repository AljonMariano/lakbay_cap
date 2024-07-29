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
                                            <input type="date" class="form-control rounded-1" name="rs_date_start" id="rs_date_start" placeholder="Select Start Date" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_start" class="form-label mb-0">Start Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start" placeholder="Select Start Time" required>
                                            <span id="rs_time_start_display"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_date_end" class="form-label mb-0">End Date</label>
                                            <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end" placeholder="Select End Date" required>
                                        </div>

                                        <div class="mb-2">
                                            <label for="rs_time_end" class="form-label mb-0">End Time</label>
                                            <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end" placeholder="Select End Time" required>
                                            <span id="rs_time_end_display"></span>
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
                                            <div class="form-check">
                                                <input type="checkbox" id="outside_provincial_capitol" name="is_outsider" value="1">
                                                <label class="form-check-label" for="outside_provincial_capitol">
                                                    Outside of Provincial Capitol?
                                                </label>
                                            </div>
                                        </div>

                                        <div id="inside_fields">
                                            <div class="mb-2">
                                                <label for="off_id" class="form-label mb-0">Office</label>
                                                <select name="off_id" id="off_id" class="form-control rounded-1" required>                                                   
                                                    <option value="" disabled selected>Select Office</option>
                                                    @foreach ($offices as $office)
                                                        <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span id="office_error"></span>
                                            </div>

                                            <div class="mb-2">
                                                <label for="requestor_id" class="form-label mb-0">Requestor</label>
                                                <select name="requestor_id" id="requestor_id" class="form-control rounded-1" required>
                                                    <option value="" disabled selected>Select Requestor</option>
                                                    @foreach($requestors as $requestor)
                                                        <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                    @endforeach
                                                </select>
                                                <span id="requestor_id_error"></span>
                                            </div>
                                        </div>

                                        <div id="outside_fields" style="display: none;">
                                            <div class="mb-2">
                                                <label for="outside_office" class="form-label mb-0">Outside Office</label>
                                                <input type="text" class="form-control rounded-1" name="outside_office" id="outside_office" placeholder="Enter Office">
                                            </div>
                                            <div class="mb-2">
                                                <label for="outside_requestor" class="form-label mb-0">Outside Requestor</label>
                                                <input type="text" class="form-control rounded-1" name="outside_requestor" id="outside_requestor" placeholder="Enter Requestor">
                                            </div>
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
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-------------EDIT MODAL --------------->
    <div id="edit_reservation_modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_reservation_form">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="reservation_id" name="reservation_id">
                        <div class="card rounded-0">
                            <div class="card-body">
                                <div class="mb-2">
                                    <label for="destination_activity_edit" class="form-label mb-0">Destination/Activity</label>
                                    <input type="text" class="form-control rounded-1" id="destination_activity_edit" name="destination_activity" required>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_from_edit" class="form-label mb-0">From</label>
                                    <input type="text" class="form-control rounded-1" name="rs_from" id="rs_from_edit" placeholder="Enter Starting Point" required>
                                    <span id="rs_from_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_date_start_edit" class="form-label mb-0">Start Date</label>
                                    <input type="date" class="form-control rounded-1" name="rs_date_start" id="rs_date_start_edit" placeholder="Select Start Date" required>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_start_edit" class="form-label mb-0">Start Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start_edit" placeholder="Select Start Time" required>
                                    <span id="rs_time_start_edit_display"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_date_end_edit" class="form-label mb-0">End Date</label>
                                    <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end_edit" placeholder="Select End Date" required>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_end_edit" class="form-label mb-0">End Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end_edit" placeholder="Select End Time" required>
                                    <span id="rs_time_end_edit_display"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="driver_id_edit" class="form-label mb-0">Driver</label>
                                    <select name="driver_id[]" id="driver_id_edit" class="form-control" multiple required>
                                        <!-- Populate with drivers -->
                                    </select>
                                    <span id="driver_id_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="vehicle_id_edit" class="form-label mb-0">Vehicle</label>
                                    <select name="vehicle_id[]" id="vehicle_id_edit" class="form-control" multiple required>
                                        <!-- Populate with vehicles -->
                                    </select>
                                    <span id="vehicle_id_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_outsider_edit" name="is_outsider">
                                        <label class="form-check-label" for="is_outsider_edit">
                                            Outside of Provincial Capitol?
                                        </label>
                                    </div>
                                </div>

                                <div id="office_requestor_fields_edit">
                                    <div class="mb-2">
                                        <label for="office_edit" class="form-label mb-0">Office</label>
                                        <select name="off_id" id="office_edit" class="form-control">
                                            <option value="">Select Office</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="office_edit_error"></span>
                                    </div>

                                    <div class="mb-2">
                                        <label for="requestor_edit" class="form-label mb-0">Requestor</label>
                                        <select name="requestor_id" id="requestor_edit" class="form-control">
                                            <option value="">Select Requestor</option>
                                            @foreach($requestors as $requestor)
                                                <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                            @endforeach
                                        </select>
                                        <span id="requestor_edit_error"></span>
                                    </div>
                                </div>

                                <div id="outside_fields_edit" style="display: none;">
                                    <div class="mb-2">
                                        <label for="outside_office_edit" class="form-label mb-0">Outside Office</label>
                                        <input type="text" class="form-control rounded-1" name="outside_office" id="outside_office_edit" placeholder="Enter Outside Office">
                                    </div>

                                    <div class="mb-2">
                                        <label for="outside_requestor_edit" class="form-label mb-0">Outside Requestor</label>
                                        <input type="text" class="form-control rounded-1" name="outside_requestor" id="outside_requestor_edit" placeholder="Enter Outside Requestor">
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_passengers_edit" class="form-label mb-0">Passengers</label>
                                    <input type="number" class="form-control rounded-1" name="rs_passengers" placeholder="Enter Number of Passengers" id="rs_passengers_edit" value="">
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

                                <div class="mb-2">
                                    <label for="reason_edit" class="form-label mb-0">Reason</label>
                                    <textarea class="form-control rounded-1" name="reason" id="reason_edit" rows="3"></textarea>
                                    <span id="reason_edit_error"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="update_reservation_btn">Update Reservation</button>
                </div>
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

    <!-- Add these modals at the end of your body tag -->
    <div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancellationModalLabel">Reason for Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancellationForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cancellationReason" class="form-label">Reason</label>
                            <textarea class="form-control" id="cancellationReason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="rejectionModal" tabindex="-1" aria-labelledby="rejectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectionModalLabel">Reason for Rejection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectionForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Reason</label>
                            <textarea class="form-control" id="rejectionReason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
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
            store: "{{ route('reservations.store') }}",
            update: "{{ route('reservations.update', ':id') }}",
            edit: "{{ route('reservations.edit', ':id') }}",
            done: "{{ route('reservations.done', ':id') }}",
            destroy: "{{ route('reservations.destroy', ':id') }}"
        };
    </script>
    <script src="{{ asset('js/admin/reservations.js') }}"></script>
</body>
</html>



