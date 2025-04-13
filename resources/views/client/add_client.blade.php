@extends('dashboard/master')
@section('title', 'Add Success Stories')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Success Stories</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            {{-- <li class="breadcrumb-item ">Banner</li> --}}
                            <li class="breadcrumb-item active">Add Success Stories</li>
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
                                <h3 class="card-title">Add New Success Stories</h3>
                            </div>
                            {{-- <div class="card-header" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
                                    style="margin-left: 10px;">Back</a>
                            </div> --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('client.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="client_img">Image<span style="font-size: 0.9em; color: #777;">(Upload only JPG, PNG, JPEG, GIF - 455x281)</span>:</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="client_img"
                                                    name="client_img" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="client_img">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="client_title">Title :</label>
                                        <input type="text" class="form-control" id="client_title" name="client_title" >
                                        {{-- <textarea class="form-control" id="client_title" name="client_title"></textarea> --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="client_description">Article :</label>
                                        {{-- <input type="text" class="form-control" id="client_description" name="client_description" > --}}
                                        {{-- <textarea class="form-control" id="client_description" name="client_description"></textarea> --}}
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea id="summernote" name="client_description" required></textarea>
                                                </div>
                                                <!-- /.col-->
                                            </div>
                                            <!-- ./row -->
                                        </section>
                                    </div>
                                    <div class="form-group">
                                        <label for="display_order">Home Display Order :</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" >
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="service_display_order">Service Display Order :</label>
                                        <input type="number" class="form-control" id="service_display_order" name="service_display_order">
                                    </div> 
                                    <div class="form-group">
                                        <label for="status">Status :</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="" disabled selected>Select status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add</button>
                                    <a href="{{ route('client.index') }}" class="btn btn-primary"
                                        style="margin-left: 10px;">Back
                                    </a>
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
    <script>

        $(function() {
            // Summernote
            $('#summernote').summernote({
                placeholder: 'Type your Description here...',
                tabsize: 2,
                height: 300
            })
        })

        function updateFileName() {
            const image = document.getElementById('client_img');
            const label = document.querySelector('.custom-file-label');
            const fileName = image.files[0] ? image.files[0].name : 'Choose file';
            label.textContent = fileName;
        }
    </script>

@endsection
