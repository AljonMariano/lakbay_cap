<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Drivers</title>
    <?php $title_page = 'Drivers'; ?>
    @include('includes.user_header')

    <!-- Include additional CSS/JS libraries -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.2/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.2/js/dataTables.bootstrap5.min.js"></script>
</head>
<body>
    <div class="container">
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
                                            <input type="hidden" name="driver_id" value="">
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-2">
                                                        <label for="dr_emp_id" class="form-label mb-0">Employee ID</label>
                                                        <input type="number" class="form-control" name="dr_emp_id" placeholder="Enter employee ID" value="">
                                                        <span id="dr_emp_id_error" class="text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-2">
                                                        <label for="dr_fname" class="form-label mb-0">First Name</label>
                                                        <input type="text" class="form-control rounded-1" name="dr_fname" placeholder="Enter driver's first name" value="">
                                                        <span id="dr_fname_error" class="text-danger"></span>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="mb-2">
                                                        <label for="dr_mname" class="form-label mb-0">Middle Name</label>
                                                        <input type="text" class="form-control rounded-1" name="dr_mname" placeholder="Enter driver's middle name" value="">
                                                        <span id="dr_mname_error" class="text-danger"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mb-2">
                                                        <label for="dr_lname" class="form-label mb-0">Last Name</label>
                                                        <input type="text" class="form-control rounded-1" name="dr_lname" placeholder="Enter driver's last name" value="">
                                                        <span id="dr_lname_error" class="text-danger"></span>
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
                                                        <span id="dr_office_error" class="text-danger"></span>
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
                                                        <span id="dr_status_error" class="text-danger"></span>
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
        <div class="row">
            <div class="col">
                <span id="form_result"></span>
                <table class="table table-bordered driver-table" id="driver-table" name="driver-table">
                    <thead>
                        <tr>
                            <th>EMP ID</th>
                            <th>Name</th>
                            <th>Office</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script type="text/javascript">
        $(document).ready(function() {
            $("#insertModal").modal("hide");
            $("#insertBtn").click(function() {
                $("#insertModal").modal("show");
            });

            var table = $('#driver-table').DataTable({
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
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
                            window.location.href = '/driver-word';
                        }
                    },
                    {
                        text: 'Excel',
                        action: function(e, dt, node, config) {
                            window.location.href = '/driver-excel';
                        }
                    },
                    {
                        text: 'PDF',
                        action: function(e, dt, node, config) {
                            var searchValue = $('.dataTables_filter input').val();
                            window.location.href = '/driver-pdf?search=' + searchValue;
                        }
                    }
                ],
                ajax: "{{ route('drivers.show') }}",
                columns: [
                    { data: 'dr_emp_id', name: 'dr_emp_id' },
                    { data: 'dr_full_name', name: 'dr_full_name' },
                    { data: 'off_name', name: 'offices.off_name' },
                    { data: 'dr_status', name: 'dr_status' }
                ]
            });

            // STORE---------------------------//
            $('#drivers-form').on('submit', function(event) {
                event.preventDefault();
                var action_url = "{{ url('/insert-driver') }}";
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: action_url,
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(data) {
                        console.log('success: ' + JSON.stringify(data));
                        var html = '';

                        $('.text-danger').html('');
                        if (data.success) {
                            html = "<div class='alert alert-info alert-dismissible fade show py-1 px-4 d-flex justify-content-between align-items-center' role='alert'><span>&#8505; &nbsp;" + data.success + "</span><button type='button' class='btn fs-4 py-0 px-0' data-bs-dismiss='alert' aria-label='Close'>&times;</button></div>";
                            $('#driver-table').DataTable().ajax.reload();
                            $('#drivers-form')[0].reset();
                            $("#insertModal").modal("hide");
                        }
                        $('#form_result').html(html);
                    },
                    error: function(data) {
                        console.log('error: ' + JSON.stringify(data));
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
        });
    </script>

    @include('includes.footer')
</body>
</html>
