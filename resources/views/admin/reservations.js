$(document).ready(function() {
    const routes = window.appRoutes || {};

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
        ajax: reservationsDataUrl,
        columns: [
            {data: 'reservation_id', name: 'reservation_id'},
            {data: 'destination_activity', name: 'destination_activity'},
            {data: 'rs_from', name: 'rs_from'},
            {data: 'rs_date_start', name: 'rs_date_start'},
            {data: 'rs_time_start', name: 'rs_time_start'},
            {data: 'rs_date_end', name: 'rs_date_end'},
            {data: 'rs_time_end', name: 'rs_time_end'},
            {data: 'requestor', name: 'requestor'},
            {data: 'office', name: 'office'},
            {data: 'driver_name', name: 'driver_name'},
            {data: 'vehicle_name', name: 'vehicle_name'},
            {data: 'rs_purpose', name: 'rs_purpose'},
            {data: 'rs_passengers', name: 'rs_passengers'},
            {data: 'rs_travel_type', name: 'rs_travel_type'},
            {
                data: 'created_at', 
                name: 'created_at',
                render: function(data) {
                    return moment(data).format('YYYY-MM-DD HH:mm:ss');
                }
            },
            {data: 'rs_approval_status', name: 'rs_approval_status'},
            {data: 'rs_status', name: 'rs_status'},
            {data: 'reason', name: 'reason'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    });

    // Function to load drivers and vehicles
    function loadDriversAndVehicles() {
        if (!routes.getDrivers || !routes.getVehicles) {
            console.error('Routes are not properly defined');
            return;
        }

        $.ajax({
            url: routes.getDrivers,
            method: 'GET',
            success: function(response) {
                updateDriverSelect(response.drivers);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching drivers:', error);
            }
        });

        $.ajax({
            url: routes.getVehicles,
            method: 'GET',
            success: function(response) {
                updateVehicleSelect(response.vehicles);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching vehicles:', error);
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

    // Function to toggle outsider fields
    function toggleOutsiderFields(isOutsider, prefix) {
        if (isOutsider) {
            $(`#office_requestor_fields_${prefix}`).hide();
            $(`#outside_fields_${prefix}`).show();
            $(`#office_${prefix}, #requestor_${prefix}`).prop('required', false);
            $(`#outside_office_${prefix}, #outside_requestor_${prefix}`).prop('required', true);
        } else {
            $(`#office_requestor_fields_${prefix}`).show();
            $(`#outside_fields_${prefix}`).hide();
            $(`#office_${prefix}, #requestor_${prefix}`).prop('required', true);
            $(`#outside_office_${prefix}, #outside_requestor_${prefix}`).prop('required', false);
        }
    }

    // Handle outsider toggle for edit modal
    $('#is_outsider_edit').change(function() {
        toggleOutsiderFields($(this).is(':checked'), 'edit');
    });

    function loadReservationData(reservationId) {
        $.ajax({
            url: routes.edit.replace(':id', reservationId),
            method: 'GET',
            success: function(response) {
                var reservation = response.reservation;
                console.log('Reservation data:', reservation);

                // Populate form fields with reservation data
                $('#edit_reservation_id').val(reservation.reservation_id);
                $('#destination_activity_edit').val(reservation.destination_activity || '');
                $('#rs_from_edit').val(reservation.rs_from || '');
                $('#rs_date_start_edit').val(reservation.rs_date_start || '');
                $('#rs_time_start_edit').val(reservation.rs_time_start || '');
                $('#rs_date_end_edit').val(reservation.rs_date_end || '');
                $('#rs_time_end_edit').val(reservation.rs_time_end || '');
                $('#rs_passengers_edit').val(reservation.rs_passengers || '');
                $('#rs_travel_type_edit').val(reservation.rs_travel_type || '');
                $('#rs_purpose_edit').val(reservation.rs_purpose || '');
                $('#reason_edit').val(reservation.reason || '');
                

                // Handle outsider status
                var isOutsider = !reservation.requestor_id && !reservation.off_id;
                $('#is_outsider_edit').prop('checked', isOutsider).trigger('change');

                if (isOutsider) {
                    $('#outside_office_edit').val(reservation.outside_office || '');
                    $('#outside_requestor_edit').val(reservation.outside_requestor || '');
                } else {
                    $('#office_edit').val(reservation.off_id || '').trigger('change');
                    $('#requestor_edit').val(reservation.requestor_id || '').trigger('change');
                }

                // Populate drivers and vehicles
                if (reservation.reservation_vehicles && reservation.reservation_vehicles.length > 0) {
                    var driverIds = [];
                    var vehicleIds = [];
                    reservation.reservation_vehicles.forEach(function(rv) {
                        if (rv.driver_id) driverIds.push(rv.driver_id);
                        if (rv.vehicle_id) vehicleIds.push(rv.vehicle_id);
                    });
                    $('#driver_id_edit').val(driverIds).trigger('change');
                    $('#vehicle_id_edit').val(vehicleIds).trigger('change');
                }

                // Update time displays
                updateTimeDisplay();

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
            updateReservationStatus(reservationId, 'cancel', reason);
        });
    }

    function showRejectionModal(reservationId) {
        $('#rejectionModal').modal('show');
        $('#rejectionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#rejectionReason').val();
            updateReservationStatus(reservationId, 'reject', reason);
        });
    }

    function updateReservationStatus(reservationId, action, reason = '') {
        if (!routes || !routes[action]) {
            console.error(`${action} route is not properly defined`);
            return;
        }

        $.ajax({
            url: routes[action].replace(':id', reservationId),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                reason: reason
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
                showErrorMessage('Error updating reservation: ' + (xhr.responseJSON ? xhr.responseJSON.error : error));
            }
        });
    }

    // Update the event handlers for the action buttons
    $(document).on('click', '.edit-btn', function() {
        var reservationId = $(this).data('id');
        $.ajax({
            url: routes.edit.replace(':id', reservationId),
            method: 'GET',
            success: function(response) {
                if (response && response.reservation) {
                    // Populate the edit form with the reservation data
                    populateEditForm(response.reservation);
                    $('#edit_reservation_modal').modal('show');
                } else {
                    showErrorMessage('Error: Invalid response from server');
                }
            },
            error: function(xhr) {
                showErrorMessage('Error fetching reservation data');
            }
        });
    });

    // Function to populate the edit form
    function populateEditForm(data) {
        if (data) {
            $('#edit_reservation_form #reservation_id').val(data.reservation_id);
            $('#edit_reservation_form #destination_activity_edit').val(data.destination_activity);
            $('#edit_reservation_form #rs_from_edit').val(data.rs_from);
            $('#edit_reservation_form #rs_date_start_edit').val(data.rs_date_start);
            $('#edit_reservation_form #rs_time_start_edit').val(data.rs_time_start);
            $('#edit_reservation_form #rs_date_end_edit').val(data.rs_date_end);
            $('#edit_reservation_form #rs_time_end_edit').val(data.rs_time_end);
            $('#edit_reservation_form #rs_passengers_edit').val(data.rs_passengers);
            $('#edit_reservation_form #rs_travel_type_edit').val(data.rs_travel_type);
            $('#edit_reservation_form #rs_purpose_edit').val(data.rs_purpose);
            $('#edit_reservation_form #reason_edit').val(data.reason);
            
            // Handle is_outsider checkbox
            $('#edit_reservation_form #is_outsider_edit').prop('checked', data.is_outsider);
            
            // Handle office and requestor fields based on is_outsider
            if (data.is_outsider) {
                $('#edit_reservation_form #outside_office_edit').val(data.outside_office);
                $('#edit_reservation_form #outside_requestor_edit').val(data.outside_requestor);
                $('#edit_reservation_form #office_requestor_fields_edit').hide();
                $('#edit_reservation_form #outside_fields_edit').show();
            } else {
                $('#edit_reservation_form #office_edit').val(data.off_id);
                $('#edit_reservation_form #requestor_edit').val(data.requestor_id);
                $('#edit_reservation_form #office_requestor_fields_edit').show();
                $('#edit_reservation_form #outside_fields_edit').hide();
            }
            
            // Handle multiple select for drivers and vehicles
            if (data.reservation_vehicles && data.reservation_vehicles.length > 0) {
                var driverIds = data.reservation_vehicles.map(rv => rv.driver_id);
                var vehicleIds = data.reservation_vehicles.map(rv => rv.vehicle_id);
                $('#edit_reservation_form #driver_id_edit').val(driverIds).trigger('change');
                $('#edit_reservation_form #vehicle_id_edit').val(vehicleIds).trigger('change');
            }
            
            // If you're using any date/time pickers, you might need to reinitialize them
            // For example, if you're using flatpickr:
            flatpickr("#rs_date_start_edit", {
                defaultDate: data.rs_date_start
            });
            flatpickr("#rs_date_end_edit", {
                defaultDate: data.rs_date_end
            });
            
            // Trigger change event for select fields to ensure proper rendering
            $('#edit_reservation_form select').trigger('change');
        } else {
            console.error('No data received to populate edit form');
        }
    }

    // Update reservation form submission
    $('#update_reservation_btn').click(function() {
        var formData = $('#edit_reservation_form').serialize();
        var reservationId = $('#edit_reservation_form #reservation_id').val();
        
        if (!reservationId) {
            showErrorMessage('Error: Reservation ID is missing');
            return;
        }

        $.ajax({
            url: routes.update.replace(':id', reservationId),
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#edit_reservation_modal').modal('hide');
                showSuccessMessage('Reservation updated successfully');
                table.ajax.reload();
            },
            error: function(xhr) {
                showErrorMessage('Error updating reservation');
            }
        });
    });

    $(document).on('click', '.approve-btn', function() {
        var reservationId = $(this).data('id');
        $.ajax({
            url: routes.approve.replace(':id', reservationId),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showSuccessMessage(response.success);
                table.ajax.reload();
            },
            error: function(xhr) {
                showErrorMessage('Error approving reservation');
            }
        });
    });

    $(document).on('click', '.reject-btn', function() {
        var reservationId = $(this).data('id');
        $('#rejectionModal').modal('show');
        $('#rejectionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#rejectionReason').val();
            $.ajax({
                url: routes.reject.replace(':id', reservationId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: reason
                },
                success: function(response) {
                    $('#rejectionModal').modal('hide');
                    showSuccessMessage(response.success);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    showErrorMessage('Error rejecting reservation');
                }
            });
        });
    });

    $(document).on('click', '.cancel-btn', function() {
        var reservationId = $(this).data('id');
        $('#cancellationModal').modal('show');
        $('#cancellationForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            var reason = $('#cancellationReason').val();
            $.ajax({
                url: routes.cancel.replace(':id', reservationId),
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    reason: reason
                },
                success: function(response) {
                    $('#cancellationModal').modal('hide');
                    showSuccessMessage(response.success);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    showErrorMessage('Error cancelling reservation');
                }
            });
        });
    });

    $(document).on('click', '.delete-btn', function() {
        var reservationId = $(this).data('id');
        if (confirm('Are you sure you want to delete this reservation?')) {
            $.ajax({
                url: routes.destroy.replace(':id', reservationId),
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    showSuccessMessage(response.success);
                    table.ajax.reload();
                },
                error: function(xhr) {
                    showErrorMessage('Error deleting reservation');
                }
            });
        }
    });

    $(document).on('click', '.done-btn', function() {
        var reservationId = $(this).data('id');
        $.ajax({
            url: routes.done.replace(':id', reservationId),
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                showSuccessMessage(response.success);
                table.ajax.reload();
            },
            error: function(xhr) {
                showErrorMessage('Error marking reservation as done');
            }
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
    $('#reservations-form').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = new FormData(this);
        var isOutsider = $('#outside_provincial_capitol').is(':checked');
        
        formData.set('is_outsider', isOutsider ? '1' : '0');

        if (isOutsider) {
            formData.delete('off_id');
            formData.delete('requestor_id');
        } else {
            formData.delete('outside_office');
            formData.delete('outside_requestor');
        }

        $.ajax({
            url: routes.store,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Success:', response);
                $('#insertModal').modal('hide');
                table.ajax.reload();
                showSuccessMessage('Reservation created successfully');
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                showErrorMessage('Error creating reservation: ' + (xhr.responseJSON ? JSON.stringify(xhr.responseJSON.error) : error));
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit', function() {
        var reservationId = $(this).data('id');
        loadReservationData(reservationId);
    });

    // Handle form submission for updating reservation
    $('#update_reservation_btn').on('click', function() {
        $('#edit_reservation_form').submit();
    });

    $('#edit_reservation_form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var reservationId = $('#edit_reservation_id').val();

        // Handle select fields
        $('select', this).each(function() {
            formData.append(this.name, $(this).val());
        });

        // Handle the is_outsider checkbox
        var isOutsider = $('#is_outsider_edit').is(':checked');
        formData.append('is_outsider', isOutsider ? '1' : '0');

        $.ajax({
            url: routes.update.replace(':id', reservationId),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                console.log('Update success:', response);
                $('#edit_reservation_modal').modal('hide');
                table.ajax.reload();
                showSuccessMessage('Reservation updated successfully');
            },
            error: function(xhr, status, error) {
                console.error('Update error:', xhr.responseText);
                showErrorMessage('Error updating reservation: ' + xhr.responseText);
            },
            complete: function() {
                // Prevent any further form submissions
                $('#edit_reservation_form').off('submit');
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
        approveReservation(reservationId);
    });

    $(document).on('click', '.cancel', function() {
        var reservationId = $(this).data('id');
        showCancellationModal(reservationId);
    });

    $(document).on('click', '.reject', function() {
        var reservationId = $(this).data('id');
        showRejectionModal(reservationId);
    });

    // Function to toggle fields based on checkbox state
    function toggleOutsideFields(form) {
        var isOutside = form.find('[name="is_outsider"]').is(':checked');
        form.find('#inside_fields, #office_requestor_fields_edit').toggle(!isOutside);
        form.find('#outside_fields, #outside_fields_edit').toggle(isOutside);

        // Enable/disable fields based on visibility
        form.find('#inside_fields select, #office_requestor_fields_edit select').prop('disabled', isOutside);
        form.find('#outside_fields input, #outside_fields_edit input').prop('disabled', !isOutside);

        // Clear values when toggling
        if (isOutside) {
            form.find('#inside_fields select, #office_requestor_fields_edit select').val('');
        } else {
            form.find('#outside_fields input, #outside_fields_edit input').val('');
        }
    }

    // Call this function for both forms
    $('#reservations-form, #edit_reservation_form').each(function() {
        toggleOutsideFields($(this));
    });

    // Toggle fields when checkbox is clicked
    $('#reservations-form [name="is_outsider"], #edit_reservation_form [name="is_outsider"]').on('change', function() {
        toggleOutsideFields($(this).closest('form'));
    });

    function toggleOutsideFields() {
        var isOutside = $('#outside_provincial_capitol').is(':checked');
        $('#inside_fields').toggle(!isOutside);
        $('#outside_fields').toggle(isOutside);

        // Enable/disable fields based on visibility
        $('#inside_fields select').prop('disabled', isOutside);
        $('#outside_fields input').prop('disabled', !isOutside);

        // Clear values when toggling
        if (isOutside) {
            $('#inside_fields select').val('');
        } else {
            $('#outside_fields input').val('');
        }
    }

    // Call this function when the page loads and when the checkbox is clicked
    $(document).ready(function() {
        toggleOutsideFields();
        $('#outside_provincial_capitol').on('change', toggleOutsideFields);
    });
});


// Make sure these functions are defined
function showSuccessMessage(message) {
    // Implementation of showing success message
    console.log('Success:', message);
}


function showErrorMessage(message) {
    // Implementation of showing error message
    console.error('Error:', message);
}


