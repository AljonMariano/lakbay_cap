<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Drivers</title>
    <?php $title_page = 'Drivers'; ?>
    @include('includes.header')
</head>
<body>
    <div class="row">
        <div class="col">
            <h4 class="text-uppercase">Drivers</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <a href="#insertModal" role="button" class="btn btn-lg btn-success" id="insertBtn" data-bs-toggle="modal">Register</a>
            <div id="insertModal" class="modal fade" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Drivers Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="POST" class="" id="drivers-form">
                                @csrf
                                <div class="card rounded-0">
                                    <div class="card-body">
                                        <input type="hidden" name="driver_id" value=">">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="dr_emp_id" class="form-label mb-0">Employee ID</label>
                                                    <input type="number" class="form-control" name="dr_emp_id" placeholder="Enter employee ID" value="">
                                                    <span id="dr_emp_id_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="dr_fname" class="form-label mb-0">First Name</label>
                                                    <input type="text" class="form-control rounded-1" name="dr_fname" placeholder="Enter driver's first name" value="">
                                                    <span id="dr_fname_error"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="dr_mname" class="form-label mb-0">Middle Name</label>
                                                    <input type="text" class="form-control rounded-1" name="dr_mname" placeholder="Enter driver's middle name" value="">
                                                    <span id="dr_mname_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="dr_lname" class="form-label mb-0">Last Name</label>
                                                    <input type="text" class="form-control rounded-1" name="dr_lname" placeholder="Enter driver's last name" value="">
                                                    <span id="dr_lname_error"></span>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>Office</label>
                                                    <select name="dr_office" id="dr_office" class="form-select">
                                                        <option value="" disabled selected>Select Office</option>
                                                        @foreach ($offices as $office)
                                                        <option value="{{ $office->off_id }}">{{ $office->off_acr }}</option>
                                                        @endforeach
                                                    </select>
                                                    <span id="dr_office_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-2">
                                                    <label for="dr_status">Status</label>
                                                    <select name="dr_status" id="dr_status" class="form-select">
                                                        <option value="" disabled selected>Select Status</option>
                                                        <option value="Idle">Idle</option>
                                                        <option value="Busy">Busy</option>
                                                        <option value="On Travel">On Travel</option>
                                                    </select>
                                                    <span id="dr_status_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2 d-flex justify-content-end align-items-center">
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
        </div>
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="sample_form" class="form-horizontal">
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

    <span id="form_result"></span>
    <div class="container">
        <table class="table table-bordered driver-table" id="driver-table" name="driver-table">
            <thead>
                <tr>
                    <th>EMP ID</th>
                    <th>Name</th>
                    <th>Office</th>
                    <th>Status</th>
                    <th width="100px">Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!-------------EDIT MODAL --------------->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" id="driver_modal" name="driver_modal" class="form-horizontal">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ModalLabel">Edit New Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <label>EMP ID : </label>
                            <input type="text" name="dr_emp_id_modal" id="dr_emp_id_modal" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>First Name : </label>
                            <input type="text" name="dr_fname_modal" id="dr_fname_modal" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Middle Name : </label>
                            <input type="text" name="dr_mname_modal" id="dr_mname_modal" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Last Name : </label>
                            <input type="text" name="dr_lname_modal" id="dr_lname_modal" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label>Office</label>
                            <select name="dr_office_modal" id="dr_office_modal" class="form-control">
                                <option id="off_text" name="off_text"></option>
                                @foreach ($offices as $office)
                                <option value="{{ $office->off_id }}">{{ $office->off_acr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="dr_status_modal" id="dr_status_modal">
                                <option value="Idle">Idle</option>
                                <option value="Busy">Busy</option>
                                <option value="On Travel">On Travel</option>
                            </select>
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
    <!-------------EDIT MODAL --------------->

</body>

<script type="text/javascript">
    $(document).ready(function() {
        $("#insertModal").modal("hide");
        $("#insertBtn").click(function() {
            $("#insertModal").modal("show");
        });
        var table = $('#driver-table').DataTable({
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
                        window.location.href = '/driver-word';
                    }
                }
                , {
                    text: 'Excel'
                    , action: function(e, dt, node, config) {
                        window.location.href = '/driver-excel';
                    }
                }
                , {
                    text: 'PDF'
                    , action: function(e, dt, node, config) {
                        var searchValue = $('.dataTables_filter input').val();
                        window.location.href = '/driver-pdf?search=' + searchValue;
                    }
                }
            ]
            , ajax: "{{ route('drivers.show') }}"
            , columns: [{
                    data: 'dr_emp_id'
                    , name: 'dr_emp_id'
                }
                , {
                    data: function(row) {
                        return row.dr_fname + ' ' + row.dr_lname;
                    }
                    , name: 'dr_full_name'
                }
                , {
                    data: 'off_name'
                    , name: 'offices.off_name'
                }
                , {
                    data: 'dr_status'
                    , name: 'dr_status'
                }
                , {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }
            ]
        });


        // STORE---------------------------//
        $('#drivers-form').on('submit', function(event) {
            event.preventDefault();
            var action_url = "{{ url('/insert-driver') }}";
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

                    $('.text-danger').html('');
                    if (data.success) {
                        html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                        $('#driver-table').DataTable().ajax.reload();
                        $('#drivers-form')[0].reset();
                        $("#insertModal").modal("hide");
                        // $('#insertModal')[0].reset();
                    }
                    $('#form_result').html(html);
                },

                error: function(data) {
                    console.log('error: ' + data);
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


        // STORE---------------------------//
        // EDIT---------------------------//
        $(document).on('click', '.edit', function(event) {
            event.preventDefault();
            var driver_id = $(this).attr('id');
            //  alert(driver_id);
            $('#form_result').html('');
            $.ajax({
                url: "/edit-driver/" + driver_id + "/"
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , dataType: "json"
                , success: function(data) {
                    //  alert(data.result.dr_emp_id);
                    $('#dr_emp_id_modal').val(data.result.dr_emp_id);
                    $('#dr_fname_modal').val(data.result.dr_fname);
                    $('#dr_mname_modal').val(data.result.dr_mname);
                    $('#dr_lname_modal').val(data.result.dr_lname);
                    $('#off_text').text(data.result.off_acr);
                    $('#off_text').val(data.result.off_id);
                    $('#dr_status_modal').val(data.result.dr_status);
                    $('#hidden_id').val(driver_id);
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
        //UPDATE------------------------------------------//
        $('#driver_modal').on('submit', function(event) {
            event.preventDefault();
            var action_url = "{{ url('/update-driver')}}";

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
                        $('#driver-table').DataTable().ajax.reload();
                        $('#formModal').modal('hide');
                        $('#driver_modal')[0].reset();


                    }
                    $('#form_result').html(html);
                }
                , error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });
        //UPDATE------------------------------------------//
        // DELETE---------------------------//
        var driver_id;
        $(document).on('click', '.delete', function() {
            driver_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function() {
            $.ajax({
                url: "/delete-driver/" + driver_id
                , success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                        $('#driver-table').DataTable().ajax.reload();
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
</body>
@include('includes.footer')
</html>
