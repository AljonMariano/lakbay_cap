<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Requestors</title>
</head>
<body>
    
</body>
</html>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

{{-- <link  href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet"> --}}
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

  @include('includes.admin_header')
  
    <div class="row">
        <div class="col">
            <h4 class="text-uppercase">Requestors</h4>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <form action="{{url('/store-requestor')}}" method="POST" class="">
                <div class="card rounded-0">
                    <div class="card-header fs-6 bg-transparent text-dark rounded-0 pt-2 text-uppercase">
                        Input/Filter Form
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col btn-group">
                                <a class="btn btn-success" onClick="add()" href="javascript:void(0)"> Add Requestor</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col">
            <table class="table table-bordered table-hover" id="requestor_table">
                <thead>
                    <tr>
                        <td>ID</td>
                        <td>Full Name</td>
                        <td>Office</td>
                        <td>Actions</td>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    
  <!--- DELETE MODAL --->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="ModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <form method="post" id="office_form" class="form-horizontal">
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
  <!--- DELETE MODAL --->

     <!-- boostrap requestor model -->
<div class="modal fade" id="requestor-modal" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="RequestorModal"></h4>
      </div>
      <div class="modal-body">



        {{-- Editable Form --}}
        <form action="javascript:void(0)" id="requestorForm" name="requestorForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="rq_full_name" class="col-sm-2 control-label">Full Name</label>
            <div class="col-sm-12">
              <input type="text" class="form-control" id="rq_full_name" name="rq_full_name" placeholder="Enter Full Name" maxlength="50" required="">
            </div>
          </div> 

          <div class="form-group">
            <label for="rq_office" class="col-sm-2 control-label">Office</label>
            <select class="form-select" name="rq_office" id="rq_office">
                <option value=""disabled selected>Select Office</option>
                @foreach ($offices as $office)
                <option value="{{$office->off_name}}">{{ $office->off_acr }} ({{$office->off_name}})</option>
                @endforeach
            </select>
          </div>       

          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-primary" id="btn-save">Update
            </button>
          </div>
        </form>
      </div>
      <div class="modal-footer">        
      </div>
    </div>
  </div>
</div>
<!-- end bootstrap model -->



<script type="text/javascript">          
     $(document).ready(function () {
     $.ajaxSetup({
     headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
     });

     $('#requestor_table').DataTable({
     processing: true,
     serverSide: true,
     dom: 'Bfrtip',
     ajax: {
     url: "{{ url('requestors') }}",
     type: "GET",
     error: function (xhr, error, code) {
     console.log(xhr);
     console.log(code);
     }
     },
     search: {
     return: true
     },
     columns: [
     { data: 'requestor_id', name: 'requestor_id' },
     { data: 'rq_full_name', name: 'rq_full_name' },
     { data: 'rq_office', name: 'rq_office' },
     {
     data: 'action',
     name: 'action',
     orderable: false,
     searchable: false
     }
     ],
     order: [[0, 'desc']],
     buttons: [
     {
     extend: 'excel',
     exportOptions: {
     columns: ':visible'
     }
     },
     {
     extend: 'print',
     exportOptions: {
     columns: ':visible'
     }
     },
     {
     extend: 'pdf',
     exportOptions: {
     columns: ':visible'
     }
     }
     ],
     columnDefs: [
     {
     targets: 0,
     visible: true
     }
     ],
     });
     });




       
      function add(){
           $('#requestorForm').trigger("reset");
           $('#RequestorModal').html("Add Requestor");
           $('#requestor-modal').modal('show');
           $('#id').val(''); 
      }  


      function editFunc(id){
        console.log(id);
        $.ajax({
            type:"POST",
            url: "{{ url('edit-requestor') }}",
            data: { requestor_id: id },
            dataType: 'json',
            success: function(res){
              $('#RequestorModal').html("Edit Requestor");
              $('#requestor-modal').modal('show');
              $('#rq_full_name').val(res.rq_full_name);
              $('#rq_office').val(res.rq_office);          
              $('#id').val(id);     
            }
        });
      } 
     
  function deleteFunc(id) {
    $('#confirmModal').modal('show');

    $('#ok_button').click(function () {
        $.ajax({
            type: "POST",
            url: "{{ url('/delete-requestor') }}",
            data: { requestor_id: id },
            dataType: 'json',
            success: function (res) {
                var oTable = $('#requestor_table').dataTable();
                oTable.fnDraw(false);
                $('#confirmModal').modal('hide');
            }
        });
    });
}
     
      $('#requestorForm').submit(function(e) {     
         e.preventDefault();       
         var formData = new FormData(this);       
         $.ajax({
            type:'POST',
            url: "{{ url('store-requestor')}}",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success: (data) => {
              $("#requestor-modal").modal('hide');
              var oTable = $('#requestor_table').dataTable();
              oTable.fnDraw(false);
              $("#btn-save").html('Submit');
              $("#btn-save"). attr("disabled", false);  
            },
            error: function(data){
               console.log(data);
             }
           });
       });

    </script>
    
    @include('includes.footer'); 
