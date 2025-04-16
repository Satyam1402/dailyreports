@extends('dashboard/master')
@section('title', 'Edit My Task')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Edit My Task</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            {{-- <li class="breadcrumb-item ">Banner</li> --}}
                            <li class="breadcrumb-item active">Edit Task</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-success">
                            <div class="card-header">
                                <h3 class="card-title">Update Task</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('employee.tasks.update', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <!-- Task Info -->
                                    <div class="form-group">
                                        <label for="task_info">Task Info</label>
                                        <textarea name="task_info" id="task_info" rows="10" class="form-control" required>{{ old('task_info', $task->task_info) }}</textarea>
                                    </div>

                                    <!-- Status -->
                                    {{-- <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="1" {{ $task->status == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $task->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div> --}}

                                    <!-- Buttons -->
                                    <button type="submit" class="btn btn-success">Update</button>
                                    <a href="{{ route('employee.tasks.index') }}" class="btn btn-secondary ml-2">Back</a>
                                </form>

                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('js')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>


@endsection
@section('ajax')

{{-- <script>
    function displayImage(inpu) {
           var preview = document.getElementById('preview-image');

           if (inpu) {
               var reader = new FileReader();

               reader.onload = function(e) {
                   preview.src = e.target.result;

               }

               reader.readAsDataURL(inpu.files[0]);
           } else {
               // If no file is selected, display the old image
               preview.src = "{{env('AWS_URL') . '/' .$data->marketing_house_icon}}";
           }

       }
       function updateFileName() {
           const image = document.getElementById('marketing_house_icon');
           const label = document.querySelector('.custom-file-label');
           const fileName = image.files[0] ? image.files[0].name : 'Choose file';
           label.textContent = fileName;
       }
</script> --}}

@endsection
