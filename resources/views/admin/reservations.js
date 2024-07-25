

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
    var table = $('#reservations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: reservationsDataUrl,
            error: function (xhr, error, thrown) {
                console.error('DataTables AJAX error:', error);
                console.error('DataTables AJAX error details:', xhr.responseText);
            }
        },
        columns: [
            {data: 'reservation_id', name: 'reservation_id'},
            {data: 'events.ev_name', name: 'events.ev_name'},
            {data: 'rs_from', name: 'rs_from'},
            {data: 'rs_date_start', name: 'rs_date_start'},
            {data: 'rs_time_start', name: 'rs_time_start',
                render: function(data, type, row) {
                    return type === 'display' ? formatTime(data) : data;
                }
            },
            {data: 'rs_date_end', name: 'rs_date_end'},
            {data: 'rs_time_end', name: 'rs_time_end',
                render: function(data, type, row) {
                    return type === 'display' ? formatTime(data) : data;
                }
            },
            {data: 'vehicles', name: 'vehicles',
                render: function(data, type, row) {
                    if (data && Array.isArray(data) && data.length > 0) {
                        return data.map(function(vehicle) {
                            return vehicle.vh_brand + ' ' + vehicle.vh_type + ' (' + vehicle.vh_plate + ')';
                        }).join(', ');
                    }
                    return 'N/A';
                }
            },
            {data: 'drivers', name: 'drivers'},
            {data: 'requestors.rq_full_name', name: 'requestors.rq_full_name'},
            {data: 'office.off_name', name: 'office.off_name'},
            {data: 'rs_purpose', name: 'rs_purpose'},
            {data: 'rs_passengers', name: 'rs_passengers'},
            {data: 'rs_travel_type', name: 'rs_travel_type'},
            {data: 'created_at', name: 'created_at'},
            {data: 'rs_approval_status', name: 'rs_approval_status'},
            {data: 'rs_status', name: 'rs_status'},
            {data: 'reason', name: 'reason'},
            {
                data: null,
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, full, meta) {
                    var editBtn = '<button type="button" class="btn btn-sm btn-primary edit-btn" data-id="' + full.reservation_id + '">Edit</button>';
                    var approveBtn = '<button type="button" class="btn btn-sm btn-success approve-btn" data-id="' + full.reservation_id + '">Approve</button>';
                    var cancelBtn = '<button type="button" class="btn btn-sm btn-warning cancel-btn" data-id="' + full.reservation_id + '">Cancel</button>';
                    var rejectBtn = '<button type="button" class="btn btn-sm btn-danger reject-btn" data-id="' + full.reservation_id + '">Reject</button>';
                    var doneBtn = '<button type="button" class="btn btn-sm btn-info done-btn" data-id="' + full.reservation_id + '">Done</button>';
                    var deleteBtn = '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' + full.reservation_id + '">Delete</button>';
                    
                    return editBtn + ' ' + approveBtn + ' ' + cancelBtn + ' ' + rejectBtn + ' ' + doneBtn + ' ' + deleteBtn;
                }
            }
        ],
        order: [[0, 'asc']],
        drawCallback: function(settings) {
            console.log('DataTables draw callback', settings);
        },
        responsive: true,
        autoWidth: false,
        scrollX: true,
        fixedHeader: true
    });

    // Function to load drivers and vehicles
    function loadDriversAndVehicles() {
        $.ajax({
            url: routes.getDrivers,
            method: 'GET',
            success: function(response) {
                updateDriverSelect(response.drivers);
            }
        });

        $.ajax({
            url: routes.getVehicles,
            method: 'GET',
            success: function(response) {
                updateVehicleSelect(response.vehicles);
            }
        });
    }

    function updateDriverSelect(drivers) {
        var $driverSelect = $('#driver_id, #driver_id_edit');
        $driverSelect.empty();
        drivers.forEach(function(driver) {
            $driverSelect.append(new Option(driver.name, driver.id, false, false));
        });
    }

    function updateVehicleSelect(vehicles) {
        var $vehicleSelect = $('#vehicle_id, #vehicle_id_edit');
        $vehicleSelect.empty();
        vehicles.forEach(function(vehicle) {
            $vehicleSelect.append(new Option(vehicle.name, vehicle.id, false, false));
        });
    }

    // Call this function when the page loads
    loadDriversAndVehicles();

    // Add this function to toggle fields visibility
    function toggleOutsiderFieldsEdit(isOutsider) {
        if (isOutsider) {
            $('#office_edit, #requestor_edit').addClass('d-none').prop('disabled', true);
            $('#outside_office_edit, #outside_requestor_edit').removeClass('d-none').prop('disabled', false);
        } else {
            $('#office_edit, #requestor_edit').removeClass('d-none').prop('disabled', false);
            $('#outside_office_edit, #outside_requestor_edit').addClass('d-none').prop('disabled', true);
        }
    }

    // Handle outsider toggle for edit modal
    $('#is_outsider_edit').change(function() {
        toggleOutsiderFieldsEdit($(this).is(':checked'));
    });

    // Modify the loadReservationData function
    function loadReservationData(reservationId) {
        // First, ensure drivers and vehicles are loaded
        loadDriversAndVehicles();

        $.ajax({
            url: routes.edit.replace(':id', reservationId),
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
                $('#rs_passengers_edit').val(reservation.rs_passengers || '');
                $('#rs_travel_type_edit').val(reservation.rs_travel_type || '');
                $('#rs_purpose_edit').val(reservation.rs_purpose || '');
                $('#reason_edit').val(reservation.reason || '');
                $('#rs_approval_status_edit').val(reservation.rs_approval_status || '');
                $('#rs_status_edit').val(reservation.rs_status || '');

                // Handle outsider status
                var isOutsider = reservation.requestor_id === null && reservation.off_id === null;
                $('#is_outsider_edit').prop('checked', isOutsider);
                toggleOutsiderFieldsEdit(isOutsider);

                if (isOutsider) {
                    $('#outside_office_edit').val(reservation.outside_office);
                    $('#outside_requestor_edit').val(reservation.outside_requestor);
                } else {
                    $('#office_edit').val(reservation.off_id);
                    $('#requestor_edit').val(reservation.requestor_id);
                }

                // Populate drivers and vehicles
                var driverIds = [];
                var vehicleIds = [];
                if (reservation.reservation_vehicles && reservation.reservation_vehicles.length > 0) {
                    reservation.reservation_vehicles.forEach(function(rv) {
                        if (rv.driver_id) driverIds.push(rv.driver_id);
                        if (rv.vehicle_id) vehicleIds.push(rv.vehicle_id);
                    });
                }
                $('#driver_id_edit').val(driverIds).trigger('change');
                $('#vehicle_id_edit').val(vehicleIds).trigger('change');

                // Ensure the reason field is editable
                $('#reason_edit').prop('readonly', false);

                $('#edit_reservation_modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error fetching reservation:', xhr.responseText);
                showErrorMessage('Error fetching reservation details');
            }
        });
    }

    function showCancellationModal(reservationId) {
        $('#cancellationModal').modal('show');
        $('#cancellationForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#cancellationReason').val();
            updateReservationStatus(reservationId, 'Cancelled', reason);
        });
    }

    function showRejectionModal(reservationId) {
        $('#rejectionModal').modal('show');
        $('#rejectionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#rejectionReason').val();
            updateReservationStatus(reservationId, 'Rejected', reason);
        });
    }

    function updateReservationStatus(reservationId, status, reason) {
        $.ajax({
            url: routes.update.replace(':id', reservationId),
            method: 'PUT',
            data: {
                rs_approval_status: status,
                rs_status: status,
                reason: reason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#cancellationModal').modal('hide');
                    $('#rejectionModal').modal('hide');
                    table.ajax.reload();
                    showSuccessMessage(response.success);
                } else {
                    showErrorMessage(response.error || 'Error updating reservation');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating reservation:', xhr.responseText);
                showErrorMessage('Error updating reservation: ' + error);
            }
        });
    }

    // Update the event handlers for the action buttons
    $(document).on('click', '.edit-btn', function() {
        var reservationId = $(this).data('id');
        loadReservationData(reservationId);
    });

    $(document).on('click', '.approve-btn', function() {
        var reservationId = $(this).data('id');
        updateReservationStatus(reservationId, 'Approved', 'Processing');
    });

    $(document).on('click', '.cancel-btn', function() {
        var reservationId = $(this).data('id');
        showCancellationModal(reservationId);
    });

    $(document).on('click', '.reject-btn', function() {
        var reservationId = $(this).data('id');
        showRejectionModal(reservationId);
    });

    $(document).on('click', '.done-btn', function() {
        var reservationId = $(this).data('id');
        $('#confirmModal').modal('show');
        $('#confirm_message').text('Are you sure you want to mark this reservation as done?');
        $('#ok_button').off('click').on('click', function() {
            $.ajax({
                url: routes.done.replace(':id', reservationId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#confirmModal').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccessMessage('Reservation marked as done successfully');
                },
                error: function(xhr, status, error) {
                    $('#confirmModal').modal('hide');
                    console.error('Error marking reservation as done:', xhr.responseText);
                    showErrorMessage('Error marking reservation as done');
                }
            });
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var reservationId = $(this).data('id');
        $('#confirmModal').modal('show');
        $('#confirm_message').text('Are you sure you want to delete this reservation?');
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
            url: routes.store,
            method: 'POST',
            data: formData,
            success: function(response) {
                console.log('Response:', response);
                if(response.success === "Reservation created successfully") {
                    // Hide the modal
                    $('#insertModal').modal('hide');

                    // Reload the DataTable
                    table.ajax.reload(null, false);

                    // Show success message
                    showSuccessMessage(response.success);

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
        var formData = new FormData(this);
        var reservationId = $('#edit_reservation_id').val();

        // Handle outsider data
        if ($('#is_outsider_edit').is(':checked')) {
            formData.append('is_outsider', 'true');
            formData.append('outside_office', $('#outside_office_edit').val());
            formData.append('outside_requestor', $('#outside_requestor_edit').val());
            formData.delete('off_id');
            formData.delete('requestor_id');
        } else {
            formData.append('is_outsider', 'false');
            var offId = $('#off_id_edit').val();
            var requestorId = $('#requestor_id_edit').val();
            
            if (offId) formData.append('off_id', offId);
            if (requestorId) formData.append('requestor_id', requestorId);
            
            formData.delete('outside_office');
            formData.delete('outside_requestor');
        }

        // Add multi-select values manually
        var driverIds = $('#driver_id_edit').val();
        var vehicleIds = $('#vehicle_id_edit').val();
        
        if (driverIds) {
            driverIds.forEach(function(id) {
                formData.append('driver_id[]', id);
            });
        }
        
        if (vehicleIds) {
            vehicleIds.forEach(function(id) {
                formData.append('vehicle_id[]', id);
            });
        }

        // Add _method field to simulate PUT request
        formData.append('_method', 'PUT');

        $.ajax({
            url: '/admin/reservations/' + reservationId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
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
                var errorMessage = 'Error updating reservation';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage += ': ' + xhr.responseJSON.error;
                } else if (error) {
                    errorMessage += ': ' + error;
                }
                showErrorMessage(errorMessage);
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete', function() {
        var reservationId = $(this).data('id');
        $('#confirmModal').modal('show');
        $('#confirm_message').text('Are you sure you want to delete this reservation?');
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
    $(document).on('click', '.done', function() {
        var reservationId = $(this).data('id');
        $('#confirmModal').modal('show');
        $('#confirm_message').text('Are you sure you want to mark this reservation as done?');
        $('#ok_button').off('click').on('click', function() {
            $.ajax({
                url: routes.done.replace(':id', reservationId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#confirmModal').modal('hide');
                    table.ajax.reload(null, false);
                    showSuccessMessage('Reservation marked as done successfully');
                },
                error: function(xhr, status, error) {
                    $('#confirmModal').modal('hide');
                    console.error('Error marking reservation as done:', xhr.responseText);
                    showErrorMessage('Error marking reservation as done');
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

    // Handle outsider toggle
    $('#is_outsider').change(function() {
        if ($(this).is(':checked')) {
            $('#off_id, #requestor_id').addClass('d-none').prop('disabled', true);
            $('#outside_office, #outside_requestor').removeClass('d-none').prop('disabled', false);
        } else {
            $('#off_id, #requestor_id').removeClass('d-none').prop('disabled', false);
            $('#outside_office, #outside_requestor').addClass('d-none').prop('disabled', true);
        }
    });

    // Handle new action buttons
    $(document).on('click', '.approve', function() {
        var reservationId = $(this).data('id');
        updateReservationStatus(reservationId, 'Approved', 'Processing');
    });

    $(document).on('click', '.cancel', function() {
        var reservationId = $(this).data('id');
        showCancellationModal(reservationId);
    });

    $(document).on('click', '.reject', function() {
        var reservationId = $(this).data('id');
        showRejectionModal(reservationId);
    });

    function submitReservationForm(formId, url, method) {
        $(formId).on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            // Handle outsider data
            if ($(formId + ' #is_outsider').is(':checked')) {
                formData.append('outside_office', $(formId + ' #outside_office').val());
                formData.append('outside_requestor', $(formId + ' #outside_requestor').val());
                formData.delete('off_id');
                formData.delete('requestor_id');
            } else {
                formData.append('off_id', $(formId + ' #office').val());
                formData.append('requestor_id', $(formId + ' #requestor').val());
                formData.delete('outside_office');
                formData.delete('outside_requestor');
            }

            // Add multi-select values manually
            var driverIds = $(formId + ' [name="driver_id[]"]').val();
            var vehicleIds = $(formId + ' [name="vehicle_id[]"]').val();
            
            if (driverIds) {
                formData.delete('driver_id[]');
                driverIds.forEach(function(id) {
                    formData.append('driver_id[]', id);
                });
            }
            
            if (vehicleIds) {
                formData.delete('vehicle_id[]');
                vehicleIds.forEach(function(id) {
                    formData.append('vehicle_id[]', id);
                });
            }

            // If it's an edit form, add the _method field
            if (method === 'PUT') {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                method: 'POST', // Always use POST for FormData
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showSuccessMessage(response.success);
                        $(formId).trigger('reset');
                        $('#reservations-table').DataTable().ajax.reload();
                        $('.modal').modal('hide');
                    } else {
                        console.error('Unexpected response structure:', response);
                        showErrorMessage('Unexpected response from server');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error submitting form:', xhr.responseText);
                    showErrorMessage('Error submitting form: ' + (xhr.responseJSON ? xhr.responseJSON.error : error));
                }
            });
        });
    }

    // Call this function for both insert and edit forms
    submitReservationForm('#insert_reservation_form', routes.store, 'POST');
    submitReservationForm('#edit_reservation_form', routes.update.replace(':id', ''), 'PUT');

    // Function to load reservation data for editing
    function loadReservationData(reservationId) {
        $.ajax({
            url: routes.edit.replace(':id', reservationId),
            method: 'GET',
            success: function(response) {
                var reservation = response.reservation;
                $('#edit_reservation_id').val(reservation.reservation_id);
                $('#event_name_edit').val(reservation.events.ev_name);
                $('#rs_from_edit').val(reservation.rs_from);
                $('#rs_date_start_edit').val(reservation.rs_date_start);
                $('#rs_time_start_edit').val(reservation.rs_time_start);
                $('#rs_date_end_edit').val(reservation.rs_date_end);
                $('#rs_time_end_edit').val(reservation.rs_time_end);
                $('#rs_passengers_edit').val(reservation.rs_passengers);
                $('#rs_travel_type_edit').val(reservation.rs_travel_type);
                $('#rs_purpose_edit').val(reservation.rs_purpose);
                $('#rs_approval_status_edit').val(reservation.rs_approval_status);
                $('#rs_status_edit').val(reservation.rs_status);
                $('#reason_edit').val(reservation.reason);

                // Handle outsider status
                var isOutsider = !reservation.requestor_id && !reservation.off_id;
                $('#is_outsider_edit').prop('checked', isOutsider).trigger('change');

                if (isOutsider) {
                    $('#outside_office_edit').val(reservation.outside_office);
                    $('#outside_requestor_edit').val(reservation.outside_requestor);
                } else {
                    $('#off_id_edit').val(reservation.off_id);
                    $('#requestor_id_edit').val(reservation.requestor_id);
                }

                // Clear and repopulate driver and vehicle selects
                $('#driver_id_edit').val(null).trigger('change');
                $('#vehicle_id_edit').val(null).trigger('change');

                if (reservation.reservation_vehicles && reservation.reservation_vehicles.length > 0) {
                    var driverIds = reservation.reservation_vehicles.map(rv => rv.driver_id);
                    var vehicleIds = reservation.reservation_vehicles.map(rv => rv.vehicle_id);
                    $('#driver_id_edit').val(driverIds).trigger('change');
                    $('#vehicle_id_edit').val(vehicleIds).trigger('change');
                }

                $('#edit_reservation_modal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('Error loading reservation data:', error);
                showErrorMessage('Error loading reservation data: ' + error);
            }
        });
    }

    // Function to toggle outsider fields
    function toggleOutsiderFields(isOutsider, formPrefix) {
        if (isOutsider) {
            $(`#${formPrefix}off_id, #${formPrefix}requestor_id`).addClass('d-none').prop('disabled', true);
            $(`#${formPrefix}outside_office, #${formPrefix}outside_requestor`).removeClass('d-none').prop('disabled', false);
        } else {
            $(`#${formPrefix}off_id, #${formPrefix}requestor_id`).removeClass('d-none').prop('disabled', false);
            $(`#${formPrefix}outside_office, #${formPrefix}outside_requestor`).addClass('d-none').prop('disabled', true);
        }
    }

    // Handle outsider toggle for insert modal
    $('#is_outsider').change(function() {
        toggleOutsiderFields($(this).is(':checked'), '');
    });

    // Handle outsider toggle for edit modal
    $('#is_outsider_edit').change(function() {
        toggleOutsiderFields($(this).is(':checked'), 'edit_');
    });
});


