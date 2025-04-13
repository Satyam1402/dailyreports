@extends('dashboard/master')
@section('title', 'Update Post')
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Post</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Post</li>
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
                                <h3 class="card-title">Update Post</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('admin-post.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    {{-- <div class="form-group"> --}}
                                    {{-- <label for="id">Category Name :</label> --}}
                                    <input type="hidden" class="form-control" id="id" name="id"
                                        value="{{ $data->id }}" required>
                                    {{-- </div> --}}
                                    <div class="form-group">
                                        <label for="category_name">Title :</label>
                                        <input type="text" class="form-control" id="title" name="title"
                                            value="{{ $data->post_title }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="insitution">Insitution :</label>
                                        <input type="text" class="form-control" id="insitution" name="insitution"
                                            value="{{ $data->insitution_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="post_name">Post :</label>
                                        <input type="text" class="form-control" id="post_name" name="post_name"
                                            value="{{ $data->post_name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="eligibility">Eligibility :</label>
                                        <input type="text" class="form-control" id="eligibility" name="eligibility" value="{{ $data->eligibility }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="place">Place :</label>
                                        <input type="text" class="form-control" id="place" name="place" value="{{ $data->place }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="salary">Salary :</label>
                                        <input type="number" class="form-control" id="salary" name="salary" value="{{ $data->salary }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="last_date">Last Date :</label>
                                        <input type="text" class="form-control" id="last_date" name="last_date" value="{{ $data->last_date }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="website">Webiste :</label>
                                        <input type="text" class="form-control" id="website" name="website"
                                            value="{{ $data->website }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="post_content">Post Content :</label>
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea id="summernote" name="post_content" required>{{ $data->post_content }} 
                                                    </textarea>
                                                </div>
                                                <!-- /.col-->
                                            </div>
                                            <!-- ./row -->
                                        </section>
                                    </div>
                                    {{-- <div class="form-group">
                                            <label for="toggle-all">Select Category :</label>
                                            <button id="toggle-all" class="btn btn-secondary form-control">Select Categories</button>
                                            <div class="mb-3" id="categories-container" style="display: none; height: 150px; overflow-y: auto; overflow-x: hidden;">
                                                <div class="row">
                                                    @foreach ($categories as $category)
                                                        <div class="col-md-6"> <!-- Adjust the column size as needed -->
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="category-{{ $category->id }}" name="categories[]"
                                                                    value="{{ $category->id }}"
                                                                    {{ in_array($category->id, $postCategory->pluck('category_id')->toArray()) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="category-{{ $category->id }}">
                                                                    {{ $category->category_name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div> --}}


                                    {{-- <div class="form-group">
                                        <label for="toggle-all">Select Category :</label>
                                        <button id="toggle-all" class="btn btn-secondary form-control">Select
                                            Categories</button>
                                        <div class="mb-3" id="categories-container"
                                            style="display: none; height: 150px; overflow-y: auto; overflow-x: hidden;">
                                            <div class="row">
                                                @foreach ($categories as $category)
                                                    <div class="col-md-4"> <!-- Adjust the column size as needed -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="category-{{ $category->id }}" name="categories[]"
                                                                value="{{ $category->id }}"
                                                                {{ in_array($category->id, $postCategory->pluck('category_id')->toArray()) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="category-{{ $category->id }}">
                                                                {{ $category->category_name }}
                                                            </label>
                                                            @if ($category->subcategories->count() > 0)
                                                                <div class="ms-4 subcategories" style="display: inline;">
                                                                    @foreach ($category->subcategories as $subcategory)
                                                                        <div class="form-check form-check-block">
                                                                            <input class="form-check-input" type="checkbox"
                                                                                id="subcategory-{{ $subcategory->id }}"
                                                                                name="subcategories[]"
                                                                                value="{{ $subcategory->id }}"
                                                                                {{ in_array($subcategory->id, $postCategory->pluck('sub_category_id')->toArray()) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                for="subcategory-{{ $subcategory->id }}">
                                                                                {{ $subcategory->sub_category_name }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="form-group">
                                        <label for="post_image">Post Image :</label>
                                        @if ($data->post_image)
                                        <div>
                                            {{-- <img src="{{ asset('post-image/' . $item->post_image) }}" alt="{{$item->post_image}}" width="80" height="80"> --}}
                                            <img id="preview-image"
                                                src="{{ asset('post-image/' . $data->post_image) }}"
                                                alt="{{ $data->post_image }}"
                                                style="max-width: 200px; max-height: 200px; border-radius:10px;">
                                        </div>
                                    @endif
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="post_image"
                                                    name="post_image" accept="image/*" onchange="displayImage(this); updateFileName()">
                                                <label class="custom-file-label" for="post_image">{{$data->post_image}}</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="category_name">Post Image :</label>
                                        @if ($data->post_image)
                                            <div>
                                                   <img id="preview-image"
                                                    src="{{ asset('post-image/' . $data->post_image) }}"
                                                    alt="{{ $data->post_image }}"
                                                    style="max-width: 200px; max-height: 200px; border-radius:10px;">
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" id="post_image" name="post_image"
                                            accept="image/*"  onchange="displayImage(this); updateFileName()">
                                    </div> --}}
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
                                    <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
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
        $(function() {
            // Summernote
            $('#summernote').summernote({
                placeholder: 'Type your content here...',
                tabsize: 2,
                height: 200
            })


        })

        document.addEventListener("DOMContentLoaded", function() {
            const toggleAllButton = document.getElementById('toggle-all');
            const categoriesContainer = document.getElementById('categories-container');

            toggleAllButton.addEventListener('click', function() {
                event.preventDefault(); // Prevent default action
                if (categoriesContainer.style.display === "none" || categoriesContainer.style.display ===
                    "") {
                    categoriesContainer.style.display = "block";
                    toggleAllButton.textContent = 'Hide All Categories';

                    // Show all subcategories
                    const subcategories = categoriesContainer.querySelectorAll('.subcategories');
                    subcategories.forEach(subcategory => {
                        subcategory.style.display = "block";
                        subcategory.classList.add('show');
                    });
                } else {
                    categoriesContainer.style.display = "none";
                    toggleAllButton.textContent = 'Show All Categories';

                    // Hide all subcategories
                    const subcategories = categoriesContainer.querySelectorAll('.subcategories');
                    subcategories.forEach(subcategory => {
                        subcategory.style.display = "none";
                        subcategory.classList.remove('show');
                    });
                }
            });
        });

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
                preview.src = "{{ asset('stored/' . $data->post_image) }}";
            }

        }
        function updateFileName() {
            const image = document.getElementById('post_image');
            const label = document.querySelector('.custom-file-label');
            const fileName = image.files[0] ? image.files[0].name : 'Choose file';
            label.textContent = fileName;
        }
    </script>
@endsection
