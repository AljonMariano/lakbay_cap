$(document).ready(function() {
    const routes = window.appRoutes || {};

    // Initialize DataTable
    var table = $('#reservations-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: routes.getData || '/users/reservations/data',
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('DataTables error:', textStatus, errorThrown);
            }
        },
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
            {data: 'created_at', name: 'created_at'},
            {data: 'rs_approval_status', name: 'rs_approval_status'},
            {data: 'rs_status', name: 'rs_status'}
        ],
        order: [[0, 'desc']] // Order by the first column (usually ID) in descending order
    });

    // Initialize Select2 for dropdowns
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Initialize flatpickr for date inputs
    flatpickr(".datepicker", {
        dateFormat: "Y-m-d",
    });

    // Initialize flatpickr for time inputs
    $(".timepicker").flatpickr({
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false,
        allowInput: true,
        clickOpens: true
    });

    // Show the modal when the reserve button is clicked
    $('#insertBtn').on('click', function(e) {
        e.preventDefault();
        $('#insertModal').modal('show');
        loadDriversAndVehicles();
    });

    // Handle form submission for create
    $('#reservations-form').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // Ensure time is in correct format
        var startTime = moment($('#rs_time_start').val(), 'HH:mm').format('hh:mm A');
        var endTime = moment($('#rs_time_end').val(), 'HH:mm').format('hh:mm A');

        formData.set('rs_time_start', startTime);
        formData.set('rs_time_end', endTime);

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
                console.log('Success:', response);
                showSuccessMessage('Reservation created successfully');
                $('#insertModal').modal('hide');
                table.ajax.reload(); // Reload the DataTable
                clearReservationForm();
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseJSON);
                showErrorMessage('Error creating reservation: ' + (xhr.responseJSON ? xhr.responseJSON.message : error));
            }
        });
    });

    function loadDriversAndVehicles(startDate, startTime, endDate, endTime, currentReservationId = null) {
        $.ajax({
            url: routes.getDriversAndVehicles,
            method: 'GET',
            data: {
                start_date: startDate,
                start_time: startTime,
                end_date: endDate,
                end_time: endTime,
                current_reservation_id: currentReservationId
            },
            success: function(response) {
                populateSelect('#driver_id', response.drivers);
                populateSelect('#vehicle_id', response.vehicles);
                populateSelect('#driver_id_edit', response.drivers, true);
                populateSelect('#vehicle_id_edit', response.vehicles, true);
            },
            error: function(xhr, status, error) {
                console.error('Error loading drivers and vehicles:', error);
            }
        });
    }

    function populateSelect(selectId, data, isEditForm = false) {
        var select = $(selectId);
        var currentValues = select.val() || [];
        select.empty();
        select.append('<option value="">Select an option</option>');

        $.each(data, function(index, item) {
            var option = $('<option>', {
                value: item.id,
                text: item.text
            });
            
            if (item.is_reserved === 1) {
                option.prop('disabled', true);
                option.text(item.text + ' (Already Reserved)');
            } else if (item.vh_status === 'Not Available') {
                option.prop('disabled', true);
                option.text(item.text + ' (Not Available)');
            } else if (item.vh_status === 'For Maintenance') {
                option.prop('disabled', true);
                option.text(item.text + ' (For Maintenance)');
            }
            
            select.append(option);
        });

        if (isEditForm) {
            select.val(currentValues).trigger('change');
        } else {
            select.trigger('change');
        }
    }

    // Call loadDriversAndVehicles when any date or time input changes in the reservation form
    $('#rs_date_start, #rs_time_start, #rs_date_end, #rs_time_end').on('change', function() {
        var startDate = $('#rs_date_start').val();
        var startTime = $('#rs_time_start').val();
        var endDate = $('#rs_date_end').val();
        var endTime = $('#rs_time_end').val();
        loadDriversAndVehicles(startDate, startTime, endDate, endTime);
    });

    // Call loadDriversAndVehicles when any date or time input changes in the edit modal
    $('#rs_date_start_edit, #rs_time_start_edit, #rs_date_end_edit, #rs_time_end_edit').on('change', function() {
        var startDate = $('#rs_date_start_edit').val();
        var startTime = $('#rs_time_start_edit').val();
        var endDate = $('#rs_date_end_edit').val();
        var endTime = $('#rs_time_end_edit').val();
        var currentReservationId = $('#edit_reservation_id').val();
        loadDriversAndVehicles(startDate, startTime, endDate, endTime, currentReservationId);
    });

    // Initialize Select2 for both create and edit forms
    $('#driver_id, #vehicle_id, #driver_id_edit, #vehicle_id_edit').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select option(s)',
        allowClear: true,
        closeOnSelect: true,
        templateSelection: function(data, container) {
            $(container).css("max-width", "100%");
            return $('<span class="select2-selection__choice__text"></span>').text(data.text);
        }
    });

    // Refresh drivers and vehicles when the modal is opened
    $('#insertModal, #edit_reservation_modal').on('show.bs.modal', function() {
        var startDate, startTime, endDate, endTime, currentReservationId;
        if (this.id === 'insertModal') {
            startDate = $('#rs_date_start').val();
            startTime = $('#rs_time_start').val();
            endDate = $('#rs_date_end').val();
            endTime = $('#rs_time_end').val();
        } else {
            startDate = $('#rs_date_start_edit').val();
            startTime = $('#rs_time_start_edit').val();
            endDate = $('#rs_date_end_edit').val();
            endTime = $('#rs_time_end_edit').val();
            currentReservationId = $('#edit_reservation_id').val();
        }
        loadDriversAndVehicles(startDate, startTime, endDate, endTime, currentReservationId);
    });

    function toggleOutsideFields(form) {
        var isOutsider = form.find('[name="is_outsider"]').is(':checked');
        
        if (isOutsider) {
            form.find('[name="off_id"], [name="requestor_id"]').addClass('d-none').prop('disabled', true);
            form.find('[name="outside_office"], [name="outside_requestor"]').removeClass('d-none').prop('disabled', false).prop('required', true);
        } else {
            form.find('[name="off_id"], [name="requestor_id"]').removeClass('d-none').prop('disabled', false).prop('required', true);
            form.find('[name="outside_office"], [name="outside_requestor"]').addClass('d-none').prop('disabled', true).prop('required', false);
        }
    }

    function resetReservationForm() {
        $('#reservations-form')[0].reset();
        $('#reservations-form select').val(null).trigger('change');
        toggleOutsideFields($('#reservations-form'));
        loadDriversAndVehicles();
    }

    // Add event listener for the checkbox
    $('#is_outsider').on('change', function() {
        toggleOutsideFields($(this).closest('form'));
    });

    // Call toggleOutsideFields on page load
    toggleOutsideFields($('#reservations-form'));

    function showAlert(type, message) {
        const alertElement = type === 'success' ? $('#success-message') : $('#error-message');
        alertElement.text(message).removeClass('d-none');
        
        // Scroll to the alert
        $('html, body').animate({
            scrollTop: $("#alert-container").offset().top - 20
        }, 200);

        // Hide the alert after 5 seconds
        setTimeout(function() {
            alertElement.addClass('d-none').text('');
        }, 5000);
    }

    function showSuccessMessage(message) {
        showAlert('success', message);
    }

    function showErrorMessage(message) {
        showAlert('error', message);
    }

    // Function to set current time
    window.setCurrentTime = function(inputId) {
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
});
