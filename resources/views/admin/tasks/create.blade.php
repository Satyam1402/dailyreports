@extends('dashboard/master')
@section('title', 'Add new user')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Add new user</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        {{-- <li class="breadcrumb-item ">B</li> --}}
                        <li class="breadcrumb-item active">Add new user</li>
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
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">New Task Form</h3>
                        </div>

                        <div class="card-body">
                            <form action="{{ route('admin.tasks.store') }}" method="POST">
                                @csrf

                                <!-- Task Info -->
                                <div class="form-group">
                                    <label for="task_info">Task Info</label>
                                    <textarea class="form-control" name="task_info" id="task_info" rows="3" placeholder="Enter task details...">{{ old('task_info') }}</textarea>
                                </div>

                                <!-- Assigned User -->
                                <div class="form-group">
                                    <label for="user_id">Assign To</label>
                                    <select class="form-control" id="user_id" name="user_id">
                                        <option value="" disabled selected>Select a user</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <!-- Submit & Back -->
                                <button type="submit" class="btn btn-success">Create Task</button>
                                <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary ml-2">Back</a>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                </div>
            </div>
        </div>
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

<script>
    function updateFileName() {
        const image = document.getElementById('marketing_house_icon');
        const label = document.querySelector('.custom-file-label');
        const fileName = image.files[0] ? image.files[0].name : 'Choose file';
        label.textContent = fileName;
    }
</script>

@endsection