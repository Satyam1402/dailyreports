@extends('dashboard/master')
@section('title', 'Add Author')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Author Template</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            {{-- <li class="breadcrumb-item ">Banner</li> --}}
                            <li class="breadcrumb-item active">Add Author Template</li>
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
                                <h3 class="card-title">Add New Author Template</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('author-template.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="template_name">Template Name :</label>
                                        <input type="text" class="form-control" id="template_name" name="template_name" >
                                    </div> 
                            
                                    <div class="form-group">
                                        <label for="author_image">Author Image<span style="font-size: 0.9em; color: #777;">(Upload only JPG, PNG, JPEG, GIF - 80x80)</span>:</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="author_image"
                                                    name="author_image" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="author_image" id="thumbnail_label">Choose file</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="author_description">Author Description :</label>
                                        <textarea class="form-control" id="author_description" name="author_description"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="click_here_text">Click Here Text :</label>
                                        <input type="text" class="form-control" id="click_here_text" name="click_here_text" >
                                    </div> 
                                    <div class="form-group">
                                        <label for="click_here_url">Click Here Url :</label>
                                        <input type="text" class="form-control" id="click_here_url" name="click_here_url" >
                                    </div> 

                                     

                                    <div class="form-group">
                                        <label for="author_name">Author Name :</label>
                                        <input type="text" class="form-control" id="author_name" name="author_name" >
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="author_url">Author Url :</label>
                                        <input type="text" class="form-control" id="author_url" name="author_url" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="founder_text">Founder Text :</label>
                                        <input type="text" class="form-control" id="founder_text" name="founder_text" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="founder_url">Founder Url :</label>
                                        <input type="text" class="form-control" id="founder_url" name="founder_url" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="cto_text">CTO Text :</label>
                                        <input type="text" class="form-control" id="cto_text" name="cto_text" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="cto_url">CTO Url :</label>
                                        <input type="text" class="form-control" id="cto_url" name="cto_url" >
                                    </div> 
                                    {{-- <div class="form-group">
                                        <label for="final_output_video_url">Video :</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="final_output_video_url"
                                                    name="final_output_video_url" accept="video/*" onchange="updateVideoFileName()" >
                                                <label class="custom-file-label" for="final_output_video_url" id="video_label">Choose file</label>
                                            </div>
                                        </div>
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
                                    <a href="{{ route('author-template.index') }}" class="btn btn-primary"
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
            const image = document.getElementById('author_image');
            // const label = document.querySelector('.custom-file-label');
            const label = document.getElementById('thumbnail_label');
            const fileName = image.files[0] ? image.files[0].name : 'Choose file';
            label.textContent = fileName;
        }

        // function updateVideoFileName() {
        //     const video = document.getElementById('final_output_video_url');
        //     // const label1 = document.querySelector('.custom-file-label');
        //     const label = document.getElementById('video_label');
        //     const fileName = video.files[0] ? video.files[0].name : 'Choose file';
        //     label.textContent = fileName;
        // }
    </script>

@endsection
