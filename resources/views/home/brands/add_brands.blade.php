@extends('dashboard/master')
@section('title', 'Add Brand')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Brands</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            {{-- <li class="breadcrumb-item ">Banner</li> --}}
                            <li class="breadcrumb-item active">Add Brand</li>
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
                                <h3 class="card-title">Add New Brand</h3>
                            </div>
                            {{-- <div class="card-header" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
                                    style="margin-left: 10px;">Back</a>
                            </div> --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="brand_name">Brand Name :</label>
                                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="brand_image">Brand Image/Logo :</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="brand_image"
                                                    name="brand_image" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="brand_image">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="website_url">Brand Website :</label>
                                        <input type="text" class="form-control" id="website_url" name="website_url" >
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="display_order">Display Order :</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" >
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
                                    <a href="{{ route('brands.index') }}" class="btn btn-primary"
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

        function updateFileName() {
            const image = document.getElementById('brand_image');
            const label = document.querySelector('.custom-file-label');
            const fileName = image.files[0] ? image.files[0].name : 'Choose file';
            label.textContent = fileName;
        }
    </script>

@endsection
