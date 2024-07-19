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
                                                <option value="">Select Driver(s)</option>
                                            </select>
                                            <span id="driver_id_error"></span>
                                        </div>

                                        <div class="mb-2">
                                            <label for="vehicle_id" class="form-label mb-0">Vehicle</label>
                                            <select class="form-control" id="vehicle_id" name="vehicle_id[]" multiple required>
                                                <option value="">Select Vehicle(s)</option>
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
                                            <label for="off_id" class="form-label mb-0">Office</label>
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
                                        <option value="">Select Driver</option>
                                    </select>
                                    <span id="driver_id_edit_error"></span>
                                </div>
                                
                                <div class="mb-2">
                                    <label for="vehicle_id_edit" class="form-label mb-0">Vehicle</label>
                                    <select id="vehicle_id_edit" name="vehicle_id[]" class="form-control" multiple>
                                        <option value="">Select Vehicle</option>
                                    </select>
                                    <span id="vehicle_id_edit_error"></span>
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

    <script type="text/javascript">
        $(document).ready(function() {
            function initializeSelect2(selector) {
                $(selector).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Select option(s)',
                    allowClear: true,
                    closeOnSelect: true,
                    dropdownParent: $(selector).closest('.modal'),
                    templateSelection: function(data, container) {
                        $(container).css("max-width", "100%");
                        return $('<span class="select2-selection__choice__text"></span>').text(data.text);
                    }
                }).on('select2:select', function (e) {
                    $(this).select2('close');
                }).on('change', function(e) {
                    var $this = $(this);
                    setTimeout(function() {
                        $this.closest('.select2-container').find('.select2-selection--multiple')
                             .css('height', 'auto')
                             .css('height', $this.closest('.select2-container').find('.select2-selection--multiple')[0].scrollHeight + 'px');
                    }, 0);
                });
            }

            // Initialize Select2 for insert modal
            initializeSelect2('#driver_id');
            initializeSelect2('#vehicle_id');

            // Initialize Select2 for edit modal
            initializeSelect2('#driver_id_edit');
            initializeSelect2('#vehicle_id_edit');

            // Initialize DataTable
            var table = $('.reservations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.reservations.show') }}",
                    type: 'GET',
                    data: function (d) {
                        d.search = $('input[type="search"]').val()
                    },
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
                    {data: 'events.ev_name', name: 'events.ev_name'},
                    {data: 'rs_from', name: 'rs_from'},
                    {data: 'rs_date_start', name: 'rs_date_start'},
                    {
                        data: 'rs_time_start',
                        name: 'rs_time_start',
                        render: function(data, type, row) {
                            return type === 'display' ? formatTime(data) : data;
                        }
                    },
                    {data: 'rs_date_end', name: 'rs_date_end'},
                    {
                        data: 'rs_time_end',
                        name: 'rs_time_end',
                        render: function(data, type, row) {
                            return type === 'display' ? formatTime(data) : data;
                        }
                    },
                    {
                        data: 'vehicles',
                        name: 'vehicles',
                        render: function(data, type, row) {
                            if (data && Array.isArray(data) && data.length > 0) {
                                return data.map(function(vehicle) {
                                    return vehicle.vh_brand + ' ' + vehicle.vh_model + ' (' + vehicle.vh_type + ') - ' + vehicle.vh_plate;
                                }).join(', ');
                            } else if (typeof data === 'string') {
                                return data; // If it's already a formatted string
                            }
                            return 'N/A'; // Default value if data is not as expected
                        }
                    },
                    {
                        data: 'drivers',
                        name: 'drivers',
                        render: function(data, type, row) {
                            return data || 'N/A';
                        }
                    },
                    {data: 'requestors.rq_full_name', name: 'requestors.rq_full_name'},
                    {
                        data: 'office.off_name',
                        name: 'office.off_name',
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
                                <button type="button" class="btn btn-sm btn-success done" data-id="${row.reservation_id}">Done</button>
                            `;
                        }
                    }
                ],
                order: [[0, 'asc']],
                drawCallback: function(settings) {
                    console.log('DataTables draw callback', settings);
                },
                responsive: true,
                autoWidth: false,
                scrollX: true
            });

            // Add this to handle manual search
            $('input[type="search"]').on('keyup', function () {
                table.search(this.value).draw();
            });

            // Show the modal when the reserve button is clicked
            $('#insertBtn').on('click', function(e) {
                e.preventDefault(); // Prevent default action
                $('#insertModal').modal('show'); // Show the modal
                loadDriversAndVehicles();
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
                loadReservationData(reservationId);
            });

            // Handle form submission for updating reservation
            $('#edit_reservation_form').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var reservationId = $('#edit_reservation_id').val();
                console.log('Form data being sent:', formData);
                console.log('Reservation ID:', reservationId);
                $.ajax({
                    url: "{{ route('reservations.update', ':id') }}".replace(':id', reservationId),
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

            // Handle done button click
            $('#reservations-table').on('click', '.done', function() {
                var reservationId = $(this).data('id');
                $.ajax({
                    url: "{{ route('reservations.done', ['id' => ':id']) }}".replace(':id', reservationId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        showSuccessMessage('Reservation marked as done');
                        table.ajax.reload(null, false);
                        // Refresh the drivers and vehicles list
                        loadDriversAndVehicles();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking reservation as done:', xhr.responseText);
                        showErrorMessage('Error marking reservation as done');
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

            // Function to load drivers and vehicles
            function loadDriversAndVehicles(reservationId = null, callback = null) {
                var driversLoaded = false;
                var vehiclesLoaded = false;

                function checkAllLoaded() {
                    if (driversLoaded && vehiclesLoaded && callback) {
                        callback();
                    }
                }

                $.ajax({
                    url: "{{ route('get.drivers') }}",
                    method: 'GET',
                    data: { reservation_id: reservationId },
                    success: function(response) {
                        console.log('Drivers response:', response);
                        populateSelect('#driver_id, #driver_id_edit', response.drivers);
                        driversLoaded = true;
                        checkAllLoaded();
                    },
                    error: function(xhr) {
                        console.error('Error loading drivers:', xhr.responseText);
                        driversLoaded = true;
                        checkAllLoaded();
                    }
                });

                $.ajax({
                    url: "{{ route('get.vehicles') }}",
                    method: 'GET',
                    data: { reservation_id: reservationId },
                    success: function(response) {
                        console.log('Vehicles response:', response);
                        populateSelect('#vehicle_id, #vehicle_id_edit', response.vehicles);
                        vehiclesLoaded = true;
                        checkAllLoaded();
                    },
                    error: function(xhr) {
                        console.error('Error loading vehicles:', xhr.responseText);
                        vehiclesLoaded = true;
                        checkAllLoaded();
                    }
                });
            }

            // Function to populate select elements
            function populateSelect(selectors, data) {
                console.log('Populating selects:', selectors, 'with data:', data);
                $(selectors).each(function() {
                    var select = $(this);
                    select.empty().append('<option value="">Select option</option>');
                    if (Array.isArray(data) && data.length > 0) {
                        $.each(data, function(index, item) {
                            var option = $('<option></option>')
                                .attr('value', item.id)
                                .text(item.name);
                            if (item.reserved) {
                                option.attr('disabled', 'disabled');
                            }
                            select.append(option);
                        });
                    } else {
                        console.warn('No data available for', selectors);
                        select.append('<option value="">No options available</option>');
                    }
                });
                $(selectors).trigger('change');
            }

            function loadReservationData(reservationId) {
                $.ajax({
                    url: "{{ route('reservations.edit', ':id') }}".replace(':id', reservationId),
                    method: 'GET',
                    success: function(response) {
                        var reservation = response.reservation;
                        console.log('Reservation data:', reservation);
                        
                        // Populate form fields with reservation data
                        $('#edit_reservation_id').val(reservation.reservation_id);
                        $('#event_edit').val(reservation.events ? reservation.events.ev_name : '');
                        $('#rs_from_edit').val(reservation.rs_from || '');
                        $('#rs_date_start_edit').val(reservation.rs_date_start || '');
                        $('#rs_time_start_edit').val(reservation.rs_time_start || '');
                        $('#rs_date_end_edit').val(reservation.rs_date_end || '');
                        $('#rs_time_end_edit').val(reservation.rs_time_end || '');
                        $('#requestor_edit').val(reservation.requestor_id || '');
                        $('#office_edit').val(reservation.off_id || '');
                        $('#rs_passengers_edit').val(reservation.rs_passengers || '');
                        $('#rs_travel_type_edit').val(reservation.rs_travel_type || '');
                        $('#rs_voucher_edit').val(reservation.rs_voucher || '');
                        $('#rs_approval_status_edit').val(reservation.rs_approval_status || '');
                        $('#rs_status_edit').val(reservation.rs_status || '');

                        console.log('Populated fields:', {
                            from: $('#rs_from_edit').val(),
                            start_date: $('#rs_date_start_edit').val(),
                            start_time: $('#rs_time_start_edit').val(),
                            end_date: $('#rs_date_end_edit').val(),
                            end_time: $('#rs_time_end_edit').val(),
                            status: $('#rs_status_edit').val()
                        });

                        // Show the edit modal
                        $('#edit_reservation_modal').modal('show');

                        // Initialize Select2 after modal is shown
                        $('#edit_reservation_modal').on('shown.bs.modal', function () {
                            initializeSelect2();
                            loadDriversAndVehicles(reservationId, function() {
                                // Set selected driver(s) and vehicle(s)
                                if (reservation.reservation_vehicles && reservation.reservation_vehicles.length > 0) {
                                    var driverIds = reservation.reservation_vehicles.map(rv => rv.driver_id).filter(id => id);
                                    var vehicleIds = reservation.reservation_vehicles.map(rv => rv.vehicle_id).filter(id => id);
                                    
                                    if (driverIds.length > 0) {
                                        $('#driver_id_edit').val(driverIds).trigger('change');
                                    }
                                    if (vehicleIds.length > 0) {
                                        $('#vehicle_id_edit').val(vehicleIds).trigger('change');
                                    }

                                    console.log('Selected drivers:', driverIds);
                                    console.log('Selected vehicles:', vehicleIds);
                                }
                            });
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching reservation:', xhr.responseText);
                        showErrorMessage('Error fetching reservation details');
                    }
                });
            }

            // Function to format time
            function formatTime(time) {
                if (!time) return '';
                let [hours, minutes] = time.split(':');
                hours = parseInt(hours);
                let ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12; // the hour '0' should be '12'
                minutes = minutes.padStart(2, '0');
                return `${hours}:${minutes} ${ampm}`;
            }

            // Function to update time display
            function updateTimeDisplay() {
                let startTime = $('#rs_time_start').val();
                let endTime = $('#rs_time_end').val();
                let startTimeEdit = $('#rs_time_start_edit').val();
                let endTimeEdit = $('#rs_time_end_edit').val();

                $('#rs_time_start_display').text(startTime ? formatTime(startTime) : '');
                $('#rs_time_end_display').text(endTime ? formatTime(endTime) : '');
                $('#rs_time_start_display_edit').text(startTimeEdit ? formatTime(startTimeEdit) : '');
                $('#rs_time_end_display_edit').text(endTimeEdit ? formatTime(endTimeEdit) : '');
            }

            // Event listeners to update the display when time changes
            $('#rs_time_start, #rs_time_end, #rs_time_start_edit, #rs_time_end_edit').on('change', updateTimeDisplay);

            // Initial update
            updateTimeDisplay();

            // Ensure date and time inputs are clickable
            document.addEventListener('DOMContentLoaded', function() {
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

            // Initialize flatpickr for date and time inputs
            flatpickr('input[type="date"]', {
                dateFormat: "Y-m-d",
            });
            flatpickr('input[type="time"]', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });

            // Initialize Select2 for edit modal
            $('#driver_id_edit, #vehicle_id_edit').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select option(s)',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#edit_reservation_modal')
            }).on('select2:select', function (e) {
                $(this).select2('close');
            });

            // Initialize flatpickr for edit modal inputs
            flatpickr('#rs_date_start_edit, #rs_date_end_edit', {
                dateFormat: "Y-m-d",
            });
            flatpickr('#rs_time_start_edit, #rs_time_end_edit', {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
            });

            // Close modal when clicking the close button (X)
            $('.modal .btn-close').on('click', function() {
                var modalId = $(this).closest('.modal').attr('id');
                $('#' + modalId).modal('hide');
            });

            // Close modal when clicking the Cancel button
            $('.modal .btn-secondary[data-bs-dismiss="modal"]').on('click', function() {
                var modalId = $(this).closest('.modal').attr('id');
                $('#' + modalId).modal('hide');
            });

            // Ensure Bootstrap modal is properly initialized
            $('#insertModal, #edit_reservation_modal').each(function() {
                new bootstrap.Modal(this);
            });
        });
    </script>
</body>
</html>



