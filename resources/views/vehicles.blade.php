<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vehicles</title>
    <?php
    $title_page = 'LAKBAY Reservation System';   
?>
    @include('includes.header');
</head>
<body>
    <div class="row">
        <div class="col">
            <h4 class="text-uppercase">Vehicles</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <a href="#insertModal" role="button" class="btn btn-lg btn-success" id="insertBtn" data-bs-toggle="modal">Register</a>
            <div id="insertModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Vehicles Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('vehicles.store') }}" method="POST" class="vehicles-form" id="vehicles-form" name="vehicles-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="vehicle_id" value="">
                                        <div class="form-group">
                                            <label for="vh_plate" class="form-label mb-0">Plate Number</label>
                                            <input type="text" class="form-control rounded-1" name="vh_plate" id="vh_plate" placeholder="Enter vehicle plate number" value="">
                                            <span id="vh_plate_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_type" class="form-label mb-0">Type</label>
                                            <input type="text" class="form-control rounded-1" name="vh_type" id="vh_type" placeholder="Enter vehicle's type/model" value="">
                                            <span id="vh_type_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_brand" class="form-label mb-0">Brand</label>
                                            <input type="text" class="form-control rounded-1" name="vh_brand" placeholder="Enter vehicle's brand name" value="">
                                            <span id="vh_brand_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_year" class="form-label mb-0">Year</label>
                                            <input type="number" class="form-control rounded-1" name="vh_year" placeholder="Enter vehicle's lauch year" value="">
                                            <span id="vh_year_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_fuel_type" class="form-label mb-0">Fuel Type</label>
                                            <select class="form-select" name="vh_fuel_type" id="vh_fuel_type">
                                                <option value="Diesel">Diesel</option>
                                                <option value="Gasoline">Gasoline</option>
                                                <option value="Electric">Electric</option>
                                            </select>
                                            <span id="vh_fuel_type_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_condition" class="form-label mb-0">Condition</label>
                                            <select class="form-select" name="vh_condition" id="vh_condition_modal">
                                                <option value="Very Good">Very Good</option>
                                                <option value="Good">Good</option>
                                                <option value="Bad">Bad</option>
                                                <option value="Very Bad">Very Bad</option>
                                            </select>
                                            <span id="vh_condition_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_capacity" class="form-label mb-0">Capacity</label>
                                            <input type="number" class="form-control rounded-1" name="vh_capacity" id="vh_capacity" placeholder="Enter vehicle's capacity" value="">
                                            <span id="vh_capacity_error"></span>
                                        </div>
                                        <div class="form-group">
                                            <label for="vh_status" class="form-label mb-0">Status</label>
                                            <select name="vh_status" id="vh_status_modal">
                                                <option value="Available">Available</option>
                                                <option value="Not Available">Not Available</option>
                                                <option value="For Maintenance">For Maintenance</option>
                                            </select>
                                            <span id="vh_status_error"></span>
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
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-hover vehicle-table" id="vehicle-table" name="vehicle-table">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Plate Number</td>
                        <td>Type</td>
                        <td>Brand</td>
                        <td>Year</td>
                        <td>Fuel Type</td>
                        <td>Condition</td>
                        <td>Status</td>
                        <td>Capacity</td>
                        <td>Actions</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-------------EDIT MODAL --------------->
    <div class="modal fade" tabindex="-1" id="vehicle_modal" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="vehicle_edit" name="vehicle_edit" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">Edit Reservation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Plate Number:</label>
                            <input type="text" class="form-control rounded-1" name="vh_plate_modal" id="vh_plate_modal" value="">
                        </div>
                        <div class="form-group">
                            <label>Type:</label>
                            <input type="text" class="form-control rounded-1" name="vh_type_modal" id="vh_type_modal" value="">
                        </div>
                        <div class="form-group">
                            <label>Brand:</label>
                            <input type="text" class="form-control rounded-1" name="vh_brand_modal" id="vh_brand_modal" value="">
                        </div>
                        <div class="form-group">
                            <label>Year:</label>
                            <input type="text" class="form-control rounded-1" name="vh_year_modal" id="vh_year_modal" value="">
                        </div>
                        <div class="form-group">
                            <label>Fuel Type</label>
                            <select class="form-select" name="vh_fuel_type_modal" id="vh_fuel_type_modal">
                                <option value="Diesel">Diesel</option>
                                <option value="Gasoline">Gasoline</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Condition:</label>
                            <select class="form-select" name="vh_condition_modal" id="vh_condition_modal">
                                <option value="Very Good">Very Good</option>
                                <option value="Good">Good</option>
                                <option value="Bad">Bad</option>
                                <option value="Very Bad">Very Bad</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Capacity:</label>
                            <input type="number" class="form-control rounded-1" name="vh_capacity_modal" id="vh_capacity_modal" value="">
                        </div>
                        <div class="form-group">
                            <label>Status:</label>
                            <select name="vh_status_modal" id="vh_status_modal">
                                <option value="Available">Available</option>
                                <option value="Not Available">Not Available</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <input type="submit" name="action_button" id="action_button" value="Add" class="btn btn-info" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-------------DELETE MODAL --------------->
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



 <!------------------------------------------------------SCRIPT ----------------------------------------------------->
    <script type="text/javascript">
        $(document).ready(function() {
            $("#insertModal").modal("hide");
            $("#insertBtn").click(function() {
                $("#insertModal").modal("show");
            });
            var table = $('.vehicle-table').DataTable({
    lengthMenu: [
        [10, 25, 50, -1], [10, 25, 50, "All"]
    ],
    search: {
        return: true
    },
    processing: true,
    serverSide: true,
    dom: 'Blfrtip',
    buttons: [{
        text: 'Word',
        action: function(e, dt, node, config) {
            var searchValue = $('.dataTables_filter input').val();
            window.location.href = '/vehicle-word?search=' + searchValue;
        }
    }, {
        text: 'Excel',
        action: function(e, dt, node, config) {
            var searchValue = $('.dataTables_filter input').val();
            window.location.href = '/vehicle-excel?search=' + searchValue;
        }
    }, {
        text: 'PDF',
        action: function(e, dt, node, config) {
            var searchValue = $('.dataTables_filter input').val();
            window.location.href = '/vehicle-pdf?search=' + searchValue;
        }
    }],
    ajax: "{{ route('vehicles.index') }}",
    columns: [
        { data: 'vehicle_id', name: 'vehicle_id' },
        { data: 'vh_plate', name: 'vh_plate' },
        { data: 'vh_type', name: 'vh_type' },
        { data: 'vh_brand', name: 'vh_brand' },
        { data: 'vh_year', name: 'vh_year' },
        { data: 'vh_fuel_type', name: 'vh_fuel_type' },
        { data: 'vh_condition', name: 'vh_condition' },
        { data: 'vh_status', name: 'vh_status' },
        { data: 'vh_capacity', name: 'vh_capacity' },
        {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        }
    ]
});





            // STORE---------------------------//
            $('#vehicles-form').on('submit', function(event) {
                event.preventDefault();
                var action_url = "{{ url('/insert-vehicle') }}";
                var formData = $(this).serializeArray();

                // Add vh_capacity field to the form data
                var vh_capacity = $('#vh_capacity').val();
                formData.push({
                    name: 'vh_capacity'
                    , value: vh_capacity
                });

                $.ajax({
                    type: 'POST'
                    , headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    , url: action_url
                    , data: formData
                    , dataType: 'json'
                    , success: function(data) {
                        console.log('success: ' + data);
                        var html = '';
                        if (data.errors) {
                            html = '<div class="alert alert-danger">';
                            for (var key in data.errors) {
                                if (data.errors.hasOwnProperty(key)) {
                                    html += '<p>' + data.errors[key][0] + '</p>';
                                }
                            }
                            html += '</div>';
                        }
                        if (data.success) {
                            html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                            $('#vehicle-table').DataTable().ajax.reload();
                            $('#vehicles-form')[0].reset();
                        }
                        $('#form_result').html(html);
                    }
                    , error: function(data) {
                        var errors = data.responseJSON;
                        console.log(errors);
                    }
                });
            });









            // STORE---------------------------//
            // EDIT---------------------------//
            $(document).on('click', '.edit', function(event) {
    event.preventDefault();
    var vehicle_id = $(this).attr('id');
    $('#form_result').html('');
    
    // Perform AJAX request to fetch vehicle data
    $.ajax({
        url: "/edit-vehicle/" + vehicle_id + "/",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: "json",
        success: function(data) {
            // Populate modal fields with fetched data
            $('#vh_plate_modal').val(data.result.vh_plate);
            $('#vh_type_modal').val(data.result.vh_type);
            $('#vh_brand_modal').val(data.result.vh_brand);
            $('#vh_year_modal').val(data.result.vh_year);
            $('#vh_fuel_type_modal').val(data.result.vh_fuel_type_modal);



            $('#vh_condition_modal').val(data.result.vh_condition);
            $('#vh_status_modal').val(data.result.vh_status);
            $('#vh_capacity_modal').val(data.result.vh_capacity);
            $('#hidden_id').val(vehicle_id);
            
            // Change modal title and submit button text
            $('.modal-title').text('Edit Vehicle');
            $('#action_button').val('Update');
            
            // Show the modal
            $('#vehicle_modal').modal('show');
        },
        error: function(data) {
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
});

            // EDIT---------------------------//
            //UPDATE------------------------------------------//
            $('#vehicle_modal').on('submit', function(event) {
    event.preventDefault();
    var action_url = "{{ route('update-vehicle') }}";

    var formData = $('#vehicle_edit').serializeArray();
    

    var vh_capacity = $('#vh_capacity_modal').val();
    //formData.push({
      //  name: 'vh_capacity',
       //         value: vh_capacity
//    });

    $.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: action_url,
        data: formData,
        dataType: 'json',
        success: function(data) {
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
                $('#vehicle-table').DataTable().ajax.reload();
                $('#vehicle_modal').modal('hide');
                $('#vehicle_modal')[0].reset();
            }
            $('#form_result').html(html);
        },
        error: function(data) {
            var errors = data.responseJSON;
            console.log(errors);
        }
    });
});
            //UPDATE------------------------------------------//
            // DELETE---------------------------//
            var vehicle_id;
            $(document).on('click', '.delete', function() {
                vehicle_id = $(this).attr('id');
                $('#confirmModal').modal('show');
            });

            $('#ok_button').click(function() {
                $.ajax({
                    url: "/delete-vehicle/" + vehicle_id
                    , success: function(data) {
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                            $('#vehicle-table').DataTable().ajax.reload();
                        });
                        if (data.success) {
                            html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                            $('#vehicle-table').DataTable().ajax.reload();
                            $('#vehicle_modal').modal('hide');
                            $('#vehicle_modal')[0].reset();
                        }

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
</body>
@include('includes.footer');
</html>
