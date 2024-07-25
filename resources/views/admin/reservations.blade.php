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
                                                <input class="form-check-input" type="checkbox" id="is_outsider" name="is_outsider">
                                                <label class="form-check-label" for="is_outsider">
                                                    Outside of Provincial Capitol?
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label for="off_id" class="form-label mb-0">Office</label>
                                            <select class="form-select" name="off_id" id="off_id">
                                                <option value="">Select Office</option>
                                                @foreach ($offices as $office)
                                                <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control rounded-1 d-none" name="outside_office" id="outside_office" placeholder="Enter Outside Office">
                                            <span id="office_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="requestor_id" class="form-label mb-0">Requestor</label>
                                            <select name="requestor_id" id="requestor_id" class="form-control">
                                                @foreach($requestors as $requestor)
                                                    <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" class="form-control rounded-1 d-none" name="outside_requestor" id="outside_requestor" placeholder="Enter Outside Requestor">
                                            <span id="requestor_id_error"></span>
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
                                            <label for="rs_purpose" class="form-label mb-0">Purpose</label>
                                            <input type="text" class="form-control rounded-1" name="rs_purpose" placeholder="Enter Purpose" id="rs_purpose" required>
                                            <span id="rs_purpose_error"></span>
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
                                                <option value="">Select Status</option>
                                                <option value="Queued">Queued</option>
                                                <option value="Ongoing">Ongoing</option>
                                                <option value="Done">Done</option>
                                                <option value="Cancelled">Cancelled</option>
                                                <option value="Inactive">Inactive</option>
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
                            <th>Purpose</th>
                            <th>Passengers</th>
                            <th>Travel Type</th>
                            <th>Created At</th>
                            <th>Approval Status</th>
                            <th>Status</th>
                            <th>Reason</th>
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
                <form id="edit_reservation_form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">Edit Reservation</h5>
                        <button type="button" class="btn-close" aria-label="Close"></button>
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
                                    <input type="date" class="form-control rounded-1" name="rs_date_start" id="rs_date_start_edit" placeholder="Select Start Date" required>
                                    <span id="rs_date_start_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_start_edit" class="form-label mb-0">Start Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_start" id="rs_time_start_edit" placeholder="Select Start Time" required>
                                    <span id="rs_time_start_edit_error"></span>
                                    <span id="rs_time_start_display_edit"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_date_end_edit" class="form-label mb-0">End Date</label>
                                    <input type="date" class="form-control rounded-1" name="rs_date_end" id="rs_date_end_edit" placeholder="Select End Date" required>
                                    <span id="rs_date_end_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="rs_time_end_edit" class="form-label mb-0">End Time</label>
                                    <input type="time" class="form-control rounded-1" name="rs_time_end" id="rs_time_end_edit" placeholder="Select End Time" required>
                                    <span id="rs_time_end_edit_error"></span>
                                    <span id="rs_time_end_display_edit"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="driver_id_edit" class="form-label mb-0">Driver</label>
                                    <select id="driver_id_edit" name="driver_id[]" class="form-control" multiple>
                                    </select>
                                    <span id="driver_id_edit_error"></span>
                                </div>
                                
                                <div class="mb-2">
                                    <label for="vehicle_id_edit" class="form-label mb-0">Vehicle</label>
                                    <select id="vehicle_id_edit" name="vehicle_id[]" class="form-control" multiple>
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

                                <div class="mb-2">
                                    <label for="office_edit" class="form-label mb-0">Office</label>
                                    <select class="form-select" name="off_id" id="office_edit">
                                        @foreach ($offices as $office)
                                        <option value="{{ $office->off_id }}">{{ $office->off_acr }} - {{ $office->off_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control d-none" name="outside_office" id="outside_office_edit" placeholder="Enter Outside Office">
                                    <span id="office_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="requestor_edit" class="form-label mb-0">Requestor</label>
                                    <select name="requestor_id" id="requestor_id_edit" class="form-control">
                                        @foreach($requestors as $requestor)
                                            <option value="{{ $requestor->requestor_id }}">{{ $requestor->rq_full_name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-control d-none" name="outside_requestor" id="outside_requestor_edit" placeholder="Enter Outside Requestor">
                                    <span id="requestor_edit_error"></span>
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
                                    <label for="rs_purpose_edit" class="form-label mb-0">Purpose</label>
                                    <input type="text" class="form-control rounded-1" name="rs_purpose" id="rs_purpose_edit" required>
                                    <span id="rs_purpose_edit_error"></span>
                                </div>

                                <div class="mb-2">
                                    <label for="reason_edit" class="form-label mb-0">Reason</label>
                                    <textarea class="form-control" id="reason_edit" name="reason" rows="3"></textarea>
                                    <span id="reason_edit_error"></span>
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
                                    <select class="form-select" id="rs_status_edit" name="rs_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Queued">Queued</option>
                                        <option value="Ongoing">Ongoing</option>
                                        <option value="Done">Done</option>
                                        <option value="Cancelled">Cancelled</option>
                                        <option value="Inactive">Inactive</option>
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
        var routes = {
            edit: "{{ route('reservations.edit', ':id') }}",
            update: "{{ route('reservations.update', ':id') }}",
            done: "{{ route('reservations.done', ':id') }}",
            getDrivers: "{{ route('get.drivers') }}",
            getVehicles: "{{ route('get.vehicles') }}",
            store: "{{ route('reservations.store') }}"
        };
    </script>
    <script src="{{ asset('js/admin/reservations.js') }}"></script>
</body>
</html>



