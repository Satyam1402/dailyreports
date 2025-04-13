@extends('dashboard/master')
@section('title', 'Add Banner')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Banner</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            {{-- <li class="breadcrumb-item ">Banner</li> --}}
                            <li class="breadcrumb-item active">Add Banner</li>
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
                                <h3 class="card-title">Add Banner</h3>
                            </div>
                            {{-- <div class="card-header" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
                                    style="margin-left: 10px;">Back</a>
                            </div> --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('top-banner.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="book_call_template_id">Book Call:</label>
                                        <select class="form-control" id="book_call_template_id" name="book_call_template_id" required>
                                            <option value="" disabled selected>Select Category</option>
                                            @foreach($bookcalldata as $bookcall)
                                                <option value="{{ $bookcall->id }}">{{ $bookcall->book_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="heading">Heading :</label>
                                        <input type="text" class="form-control" id="heading" name="heading" >
                                        {{-- <textarea class="form-control" id="heading" name="heading"></textarea> --}}
                                    </div>
                                    <div class="form-group">
                                        <label for="sub_heading">Sub Heading :</label>
                                        {{-- <input type="text" class="form-control" id="sub_heading" name="sub_heading"> --}}
                                        <textarea class="form-control" id="sub_heading" name="sub_heading"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="banner_button_text">Button Text :</label>
                                        <input type="text" class="form-control" id="banner_button_text" name="banner_button_text" >
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="banner_button_url">Button Url :</label>
                                        <input type="text" class="form-control" id="banner_button_url" name="banner_button_url" >
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="banner_video_thumbnail">Video Thumbnail<span style="font-size: 0.9em; color: #777;">(Upload only JPG, PNG, JPEG, GIF - 706x665)</span>:</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="banner_video_thumbnail"
                                                    name="banner_video_thumbnail" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="banner_video_thumbnail">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="banner_video_url">Video Url :</label>
                                        <input type="text" class="form-control" id="banner_video_url" name="banner_video_url" >
                                    </div>

                                    <div class="form-group">
                                        <label for="display_order">Display Order :</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" value="">
                                    </div>  

                                    
                                    {{-- <div class="form-group">
                                        <label for="post_image">Post Image :</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="post_image"
                                                    name="post_image" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="post_image">Choose file</label>
                                            </div>
                                        </div>
                                    </div> --}}
                                   
                                    <div class="form-group">
                                        <label for="status">Status :</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="" disabled selected>Select status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add</button>
                                    <a href="{{ route('top-banner.index') }}" class="btn btn-primary"
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
        // $(function() {
        //     // Summernote
        //     $('#summernote').summernote({
        //         placeholder: 'Type your content here...',
        //         tabsize: 2,
        //         height: 300
        //     })
        // })

        // document.addEventListener("DOMContentLoaded", function() {
        //     const toggleAllButton = document.getElementById('toggle-all');
        //     const categoriesContainer = document.getElementById('categories-container');

        //     toggleAllButton.addEventListener('click', function() {
        //         event.preventDefault(); // Prevent default action
        //         if (categoriesContainer.style.display === "none" || categoriesContainer.style.display === "") {
        //             categoriesContainer.style.display = "block";
        //             toggleAllButton.textContent = 'Hide All Categories';

        //             // Show all subcategories
        //             const subcategories = categoriesContainer.querySelectorAll('.subcategories');
        //             subcategories.forEach(subcategory => {
        //                 subcategory.style.display = "block";
        //                 subcategory.classList.add('show');
        //             });
        //         } else {
        //             categoriesContainer.style.display = "none";
        //             toggleAllButton.textContent = 'Show All Categories';

        //             // Hide all subcategories
        //             const subcategories = categoriesContainer.querySelectorAll('.subcategories');
        //             subcategories.forEach(subcategory => {
        //                 subcategory.style.display = "none";
        //                 subcategory.classList.remove('show');
        //             });
        //         }
        //     });
        // });

function updateFileName() {
    const image = document.getElementById('banner_video_thumbnail');
    const label = document.querySelector('.custom-file-label');
    const fileName = image.files[0] ? image.files[0].name : 'Choose file';
    label.textContent = fileName;
}
    </script>

@endsection
