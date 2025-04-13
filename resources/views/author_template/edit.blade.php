@extends('dashboard/master')
@section('title', 'Update Author')
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
                            <li class="breadcrumb-item active">Update Author Template</li>
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
                            {{-- <div class="card-header" >
                                  <a href="" style="float: right;" class="btn btn-warning">Update</a>
                                  <a href="{{route('category.index')}}" style="float: right;" class="btn btn-primary">Back</a>
                                </div> --}}
                            <div class="card-header">
                                <h3 class="card-title">Update Author Template</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('author-template.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- <div class="form-group"> --}}
                                    {{-- <label for="id">Category Name :</label> --}}
                                    <input type="hidden" class="form-control" id="id" name="id"
                                        value="{{ $data->id }}" required>
                                    {{-- </div> --}}
                                    {{-- <div class="form-group">
                                        <label for="creative_house_category_id">Category Name :</label>
                                        <input type="text" class="form-control" id="creative_house_category_id" value="{{$data->creative_house_category_id}}" name="creative_house_category_id" required>
                                    </div> --}}

                                    <div class="form-group">
                                        <label for="template_name">Template Name :</label>
                                        <input type="text" class="form-control" id="template_name" value="{{$data->template_name}}" name="template_name" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="author_image">Author Image<span style="font-size: 0.9em; color: #777;">(Upload only JPG, PNG, JPEG, GIF - 80x80)</span>:</label>
                                        @if ($data->author_image)
                                        <div>
                                           <img id="preview-image"
                                                src="{{env('AWS_URL') . '/' .$data->author_image}}"
                                                alt="{{ $data->author_image }}"
                                                style="max-width: 200px; max-height: 200px; border-radius:10px;">
                                        </div>
                                        @endif
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="author_image"
                                                    name="author_image" accept="image/*" onchange="displayImage(this); updateFileName()">
                                                <label class="custom-file-label" for="author_image" id='video_thumbnail_img_change'>{{ !empty($data->author_image) ? env('AWS_URL') . '/' . $data->author_image : 'Choose file' }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    


                                    <div class="form-group">
                                        <label for="author_description">Author Description :</label>
                                        <textarea class="form-control" id="author_description" name="author_description">{{$data->author_description}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="click_here_text">Click Here Text :</label>
                                        <input type="text" class="form-control" id="click_here_text" value="{{$data->click_here_text}}" name="click_here_text" >
                                    </div> 
                                    <div class="form-group">
                                        <label for="click_here_url">Click Here Url :</label>
                                        <input type="text" class="form-control" id="click_here_url" value="{{$data->click_here_url}}" name="click_here_url" >
                                    </div> 

                                     

                                    <div class="form-group">
                                        <label for="author_name">Author Name :</label>
                                        <input type="text" class="form-control" id="author_name" value="{{$data->author_name}}" name="author_name" >
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="author_url">Author Url :</label>
                                        <input type="text" class="form-control" id="author_url" value="{{$data->author_url}}" name="author_url" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="founder_text">Founder Text :</label>
                                        <input type="text" class="form-control" id="founder_text" value="{{$data->founder_text}}" name="founder_text" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="founder_url">Founder Url :</label>
                                        <input type="text" class="form-control" id="founder_url" value="{{$data->founder_url}}"  name="founder_url" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="cto_text">CTO Text :</label>
                                        <input type="text" class="form-control" id="cto_text" value="{{$data->cto_text}}" name="cto_text" >
                                    </div> 

                                    <div class="form-group">
                                        <label for="cto_url">CTO Url :</label>
                                        <input type="text" class="form-control" id="cto_url" value="{{$data->cto_url}}" name="cto_url" >
                                    </div> 

                               
                                     
                               
                                    {{-- <div class="form-group">
                                        <label for="final_output_video_url">Video :</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="final_output_video_url"
                                                    name="final_output_video_url" accept="video/*" onchange="displayVideo(this); updateVideoFileName()">
                                                <label class="custom-file-label" for="final_output_video_url" id='video_url_change'>{{$data->final_output_video_url}}</label>
                                            </div>
                                        </div>
                                    </div> --}}
                                  
                                  

                                    <div class="form-group">
                                        <label for="display_order">Display Order :</label>
                                        <input type="number" class="form-control" id="display_order" value="{{$data->display_order}}" name="display_order">
                                    </div>  
                                   
                                    <div class="form-group">
                                        <label for="status">Status :</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn btn-success">Update</button>
                                    <a href="{{ route('author-template.index') }}" class="btn btn-primary"
                                        style="margin-left: 10px;">Back</a>

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
    
        // function displayImage(inpu) {
        //     var preview = document.getElementById('preview-image');
            
        //     if (inpu) {
        //         var reader = new FileReader();

        //         reader.onload = function(e) {
        //             preview.src = e.target.result;

        //         }

        //         reader.readAsDataURL(inpu.files[0]);
        //     } else {
        //         // If no file is selected, display the old image
        //         preview.src = "{{$data->creative_house_thumbnail}}";
        //     }

        // }
        // function updateFileName() {
        //     const image = document.getElementById('creative_house_thumbnail');
        //     const label = document.querySelector('.custom-file-label');
        //     const fileName = image.files[0] ? image.files[0].name : 'Choose file';
        //     label.textContent = fileName;
        // }

        function displayImage(input) {
                var preview = document.getElementById('preview-image');
                
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    // If no file is selected, keep the old image (if exists)
                    preview.src = "{{ env('AWS_URL') . '/' .$data->author_image}}";
                }
            }
            function updateFileName() {
                const image = document.getElementById('author_image');
                const label = document.getElementById('video_thumbnail_img_change');
                const fileName = image.files[0] ? image.files[0].name : 'Choose file';
                label.textContent = fileName;
            }

        // function displayVideo(input) {
        
        // }


        // function updateVideoFileName() {
        //     const video = document.getElementById('final_output_video_url');
        //     const label = document.getElementById('video_url_change');
        //     const fileName = video.files[0] ? video.files[0].name : 'Choose file';
        //     label.textContent = fileName;
        // }
    </script>
@endsection
