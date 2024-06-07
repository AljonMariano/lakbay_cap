<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Events</title>
    <?php $title_page = 'LAKBAY Reservation System';?>
    @include('includes.header')

</head>
<body>

    <div class="row">
        <div class="col">
            <h4 class="text-uppercase">Events</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <a href="#insertModal" role="button" class="btn btn-lg btn-success" id="insertBtn" data-bs-toggle="modal">Register</a>
            <div id="insertModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Events Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" id="insert_form" name="insert_form" class="insert_form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="event_id" value="">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_name" class="form-label mb-0">Event Name</label>
                                                    <input type="text" class="form-control rounded-1" name="ev_name" id="ev_name" placeholder="Enter event name" value="">
                                                    <span id="ev_name_error"></span>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_venue" class="form-label mb-0">Venue</label>
                                                    <input type="text" class="form-control rounded-1" name="ev_venue" id="ev_venue" placeholder="Enter event's venue" value="">
                                                    <span id="ev_venue_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_date_start" class="form-label mb-0">Start Date</label>
                                                    <input type="date" class="form-control rounded-1" name="ev_date_start" id="ev_date_start" placeholder="Enter event's start date" value="">
                                                    <span id="ev_date_start_error"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_time_start" class="form-label mb-0">Start time</label>
                                                    <input type="time" class="form-control rounded-1" name="ev_time_start" id="ev_time_start" placeholder="Enter event's start time" value="">
                                                    <span id="ev_time_start_error"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_date_end" class="form-label mb-0">End Date</label>
                                                    <input type="date" class="form-control rounded-1" name="ev_date_end" id="ev_date_end" placeholder="Enter event's end date" value="">
                                                    <span id="ev_date_end_error"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="ev_time_end" class="form-label mb-0">End Time</label>
                                                    <input type="time" class="form-control rounded-1" name="ev_time_end" id="ev_time_end" placeholder="Enter event's end time" value="">
                                                    <span id="ev_time_end_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" name="submit" value="insert" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" id="events_form" class="form-horizontal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalLabel"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Event Name : </label>
                                    <input type="text" name="ev_name_modal" id="ev_name_modal" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label>Event Venue : </label>
                                    <input type="text" name="ev_venue_modal" id="ev_venue_modal" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="ev_date_start_modal" class="form-label mb-0">Start Date</label>
                                    <input type="date" class="form-control rounded-1" name="ev_date_start_modal" id="ev_date_start_modal" placeholder="Enter event's start date" value="">
                                </div>
                                <div class="form-group">
                                    <label for="ev_time_start_modal" class="form-label mb-0">Start time</label>
                                    <input type="time" class="form-control rounded-1" step="any" name="ev_time_start_modal" id="ev_time_start_modal" placeholder="Enter event's start time" value="">
                                </div>
                                <div class="form-group">
                                    <label for="ev_date_end_modal" class="form-label mb-0">End Date</label>
                                    <input type="date" class="form-control rounded-1" name="ev_date_end_modal" id="ev_date_end_modal" placeholder="Enter event's end date" value="">
                                </div>
                                <div class="form-group">
                                    <label for="ev_time_end_modal" class="form-label mb-0">End Time</label>
                                    <input type="time" class="form-control rounded-1" step="any" name="ev_time_end_modal" id="ev_time_end_modal" placeholder="Enter event's end time" value="">
                                </div>
                                <input type="hidden" name="action" id="action" value="Add" />
                                <input type="hidden" name="hidden_id" id="hidden_id" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <input type="submit" name="action_button" id="action_button" value="Add" class="btn btn-info" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!---DELETE--->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" id="events_form" class="form-horizontal">
                            <div class="modal-header">
                                <h5 class="modal-title" id="ModalLabel">Confirmation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span id="form_result"></span>
    <div class="row">
        <div class="col">
            <div class="container">
                <table class="events-table table table-bordered" id="events-table" name="events-table">
                    <thead>
                        <tr>
                            <td>ID</td>
                            <td>Event Name</td>
                            <td>Venue</td>
                            <td>Date Start</td>
                            <td>Time Start</td>
                            <td>Date End</td>
                            <td>Time End</td>
                            <td>Actions</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>


                        </tr>


                    </tbody>
                </table>
            </div>


        </div>
    </div>
    @include('includes.footer')
</body>
<script type="text/javascript">
    $(document).ready(function() {
        $("#insertModal").modal("hide");
        $("#insertBtn").click(function() {
            $("#insertModal").modal("show");
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var table = $('.events-table').DataTable({
            lengthMenu: [
                [10, 25, 50, -1]
                , [10, 25, 50, "All"]
            ]
            , search: {
                return: true
            }
            , processing: true
            , serverSide: true
            , dom: 'Blfrtip'
            , buttons: [{
                    text: 'Word'
                    , action: function(e, dt, node, config) {
                        var searchValue = $('.dataTables_filter input').val();
                        window.location.href = '/events-word?search=' + searchValue;
                    }
                }
                , {
                    text: 'Excel'
                    , action: function(e, dt, node, config) {
                        var searchValue = $('.dataTables_filter input').val();
                        window.location.href = '/events-excel?search=' + searchValue;
                    }
                }
                , {
                    text: 'PDF'
                    , action: function(e, dt, node, config) {
                        var searchValue = $('.dataTables_filter input').val();
                        window.location.href = '/events-pdf?search=' + searchValue;
                    }
                }
            ]
            , columnDefs: [{
                targets: 0
                , visible: true
            }]
            , ajax: "{{ route('events.show') }}"
            , columns: [{
                    data: 'event_id'
                    , name: 'event_id'
                }
                , {
                    data: 'ev_name'
                    , name: 'ev_name'
                }
                , {
                    data: 'ev_venue'
                    , name: 'ev_venue'
                }
                , {
                    data: 'ev_date_start'
                    , name: 'ev_date_start'
                    , render: function(data, type, row, meta) {
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric'
                            , month: 'long'
                            , day: 'numeric'
                        });
                        return formattedDate;
                    }
                }
                , {
                    data: 'ev_time_start'
                    , name: 'ev_time_start'
                }
                , {
                    data: 'ev_date_end'
                    , name: 'ev_date_end'
                    , render: function(data, type, row, meta) {
                        var date = new Date(data);
                        var formattedDate = date.toLocaleDateString('en-US', {
                            year: 'numeric'
                            , month: 'long'
                            , day: 'numeric'
                        });
                        return formattedDate;
                    }
                }
                , {
                    data: 'ev_time_end'
                    , name: 'ev_time_end'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }
            ]
        });

        $('#events_form').on('submit', function(event) {
            event.preventDefault();
            var action_url = "{{ url('/update-event')}}";

            $.ajax({
                type: 'post'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , url: action_url
                , data: $(this).serialize()
                , dataType: 'json'
                , success: function(data) {
                    console.log('success: ' + data);
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
                        $('#events-table').DataTable().ajax.reload();
                        $('#formModal').modal('hide');
                        $('#events_form')[0].reset();

                    }
                    $('#form_result').html(html);
                }
                , error: function(data) {
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
        $('#insert_form').on('submit', function(event) {
            event.preventDefault();
            var action_url = "{{url('/insert-event')}}";
            $.ajax({
                type: 'post'
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , url: action_url
                , data: $(this).serialize()
                , dataType: 'json'
                , success: function(data) {
                    console.log('success: ' + data);
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
                        $('#events-table').DataTable().ajax.reload();
                        $('#insertModal').modal('hide');

                        $('#insert_form')[0].reset();

                    }
                    $('#form_result').html(html);
                }
                , error: function(data) {
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
        //ADD----------------------------//

        //ADD----------------------------//
        //EDIT---------------------------//
        $(document).on('click', '.edit', function(event) {
            event.preventDefault();
            var event_id = $(this).attr('id');
            //  alert(event_id);
            $('#form_result').html('');
            $.ajax({
                url: "/edit-event/" + event_id + "/"
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , dataType: "json"
                , success: function(data) {
                    $('#ev_name_modal').val(data.result.ev_name);
                    $('#ev_venue_modal').val(data.result.ev_venue);
                    $('#ev_date_start_modal').val(data.result.ev_date_start);
                    $('#ev_time_start_modal').val(data.result.ev_time_start);
                    $('#ev_date_end_modal').val(data.result.ev_date_end);
                    $('#ev_time_end_modal').val(data.result.ev_time_end);
                    $('#hidden_id').val(event_id);
                    $('.modal-title').text('Edit Record');
                    $('#action_button').val('Update');
                    $('#action').val('Edit');
                    $('#formModal').modal('show');
                }
                , error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            })
        });
        // EDIT---------------------------//
        // DELETE---------------------------//
        var event_id;
        $(document).on('click', '.delete', function() {
            event_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function() {
            $.ajax({
                url: "/delete-event/" + event_id
                , success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                        $('#events-table').DataTable().ajax.reload();
                    });
                }
            })
        });
        //DELETE---------------------------//
        document.addEventListener("DOMContentLoaded", function() {
            var btn = document.getElementById("insertBtn");
            btn.addEventListener("click", function() {
                var insertModal = new bootstrap.Modal(document.getElementById("insertModal"));
                insertModal.show();
            });
        });
    });

</script>

</html>
