@extends('dashboard/master')
@section('title', 'Add Post')
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
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Add New Post</h3>
                            </div>
                            {{-- <div class="card-header" style="display: flex; justify-content: flex-end;">
                                <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
                                    style="margin-left: 10px;">Back</a>
                            </div> --}}
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{ route('admin-post.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="title">Title :</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="insitution">Insitution :</label>
                                        <input type="text" class="form-control" id="insitution" name="insitution"
                                            >
                                    </div>
                                    <div class="form-group">
                                        <label for="post_name">Post :</label>
                                        <input type="text" class="form-control" id="post_name" name="post_name" >
                                    </div>
                                    <div class="form-group">
                                        <label for="eligibility">Eligibility :</label>
                                        <input type="text" class="form-control" id="eligibility" name="eligibility" >
                                    </div>
                                    <div class="form-group">
                                        <label for="place">Place :</label>
                                        <input type="text" class="form-control" id="place" name="place" >
                                    </div>
                                    <div class="form-group">
                                        <label for="salary">Salary :</label>
                                        <input type="number" class="form-control" id="salary" name="salary" >
                                    </div>
                                    <div class="form-group">
                                        <label for="last_date">Last Date :</label>
                                        <input type="text" class="form-control" id="last_date" name="last_date" >
                                    </div>
                                    <div class="form-group">
                                        <label for="webiste">Webiste :</label>
                                        <input type="text" class="form-control" id="website" name="website">
                                    </div>
                                    <div class="form-group">
                                        <label for="Post Content">Post Content :</label>
                                        {{-- <input type="text" class="form-control" id="post_content" name="post_content" required> --}}
                                        <section class="content">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <textarea id="summernote" name="post_content" required></textarea>
                                                </div>
                                                <!-- /.col-->
                                            </div>
                                            <!-- ./row -->
                                        </section>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="toggle-all">Select Category :</label>
                                        <button id="toggle-all" class="btn btn-secondary form-control">Select Categories</button>
                                    
                                        <!-- Search input for filtering categories and subcategories -->
                                        <input type="text" id="category-search" class="form-control mt-2" placeholder="Search categories..." style="display: none;">
                                    
                                        <div class="mb-3" id="categories-container" style="display: none; height: 350px; overflow-y: auto; overflow-x: hidden; border: 2px solid rgb(158, 158, 158); border-radius:5px;">
                                            <div class="row" style="margin-left:8px;">
                                                @foreach ($categories as $category)
                                                <div class="col-md-4 category-item" data-category="{{ $category->category_name }}"> <!-- Adjust the column size as needed -->
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}">
                                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                                            {{ $category->category_name }}
                                                        </label>
                                    
                                                        @if ($category->subcategories->count() > 0)
                                                        <div class="ms-4 subcategories" style="display: inline;">
                                                            @foreach ($category->subcategories as $subcategory)
                                                            <div class="form-check form-check-block subcategory-item" data-subcategory="{{ $subcategory->sub_category_name }}">
                                                                <input class="form-check-input" type="checkbox" id="subcategory-{{ $subcategory->id }}" name="subcategories[]" value="{{ $subcategory->id }}">
                                                                <label class="form-check-label" for="subcategory-{{ $subcategory->id }}">
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
                                    {{-- <div class="form-group">
                                        <label for="toggle-all">Select Category :</label>
                                        <button id="toggle-all" class="btn btn-secondary form-control">Select Categories</button>
                                        <div class="mb-3" id="categories-container" style="display: none; height: 350px; overflow-y: auto; overflow-x: hidden; border: 2px solid rgb(158, 158, 158); border-radius:5px;">
                                            <div class="row" style="margin-left:8px;">
                                                @foreach ($categories as $category)
                                                    <div class="col-md-4"> <!-- Adjust the column size as needed -->
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="category-{{ $category->id }}" name="categories[]" value="{{ $category->id }}">
                                                            <label class="form-check-label" for="category-{{ $category->id }}">
                                                                {{ $category->category_name }}
                                                            </label>
                                                            @if ($category->subcategories->count() > 0)
                                                                <div class="ms-4 subcategories" style="display: inline;">
                                                                    @foreach ($category->subcategories as $subcategory)
                                                                        <div class="form-check form-check-block">
                                                                            <input class="form-check-input" type="checkbox" id="subcategory-{{ $subcategory->id }}" name="subcategories[]" value="{{ $subcategory->id }}">
                                                                            <label class="form-check-label" for="subcategory-{{ $subcategory->id }}">
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
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="post_image"
                                                    name="post_image" accept="image/*" onchange="updateFileName()" >
                                                <label class="custom-file-label" for="post_image">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group">
                                        <label for="post_image">Post Image :</label>
                                        <input type="file" class="form-control" id="post_image" name="post_image"
                                            accept="image/*" required>
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
                                    <a href="{{ route('admin-post.index') }}" class="btn btn-primary"
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
                placeholder: 'Type your content here...',
                tabsize: 2,
                height: 300
            })
        })
//         document.addEventListener("DOMContentLoaded", function() {
//     const toggleAllButton = document.getElementById('toggle-all');
//     const categoriesContainer = document.getElementById('categories-container');
//     const searchInput = document.getElementById('category-search');

//     toggleAllButton.addEventListener('click', function(event) {
//         event.preventDefault(); // Prevent default action
//         if (categoriesContainer.style.display === "none" || categoriesContainer.style.display === "") {
//             categoriesContainer.style.display = "block";
//             searchInput.style.display = "block";
//             toggleAllButton.textContent = 'Hide All Categories';

//             // Show all subcategories
//             const subcategories = categoriesContainer.querySelectorAll('.subcategories');
//             subcategories.forEach(subcategory => {
//                 subcategory.style.display = "block";
//                 subcategory.classList.add('show');
//             });
//         } else {
//             categoriesContainer.style.display = "none";
//             searchInput.style.display = "none";
//             toggleAllButton.textContent = 'Show All Categories';

//             // Hide all subcategories
//             const subcategories = categoriesContainer.querySelectorAll('.subcategories');
//             subcategories.forEach(subcategory => {
//                 subcategory.style.display = "none";
//                 subcategory.classList.remove('show');
//             });
//         }
//     });

//     // Filter categories and subcategories based on the search input
//     searchInput.addEventListener('input', function() {
//         const filter = searchInput.value.toLowerCase();
//         const categoryItems = document.querySelectorAll('.category-item');
//         const subcategoryItems = document.querySelectorAll('.subcategory-item');

//         categoryItems.forEach(categoryItem => {
//             const categoryName = categoryItem.getAttribute('data-category').toLowerCase();
//             const matchesCategory = categoryName.includes(filter);

//             let matchesSubcategory = false;

//             const subcategories = categoryItem.querySelectorAll('.subcategory-item');
//             subcategories.forEach(subcategory => {
//                 const subcategoryName = subcategory.getAttribute('data-subcategory').toLowerCase();
//                 if (subcategoryName.includes(filter)) {
//                     matchesSubcategory = true;
//                     subcategory.style.display = 'block'; // Show matching subcategories
//                 } else {
//                     subcategory.style.display = 'none'; // Hide non-matching subcategories
//                 }
//             });

//             // Show category if it or any of its subcategories match the search
//             if (matchesCategory || matchesSubcategory) {
//                 categoryItem.style.display = 'block';
//             } else {
//                 categoryItem.style.display = 'none';
//             }
//         });
//     });
// });

        document.addEventListener("DOMContentLoaded", function() {
            const toggleAllButton = document.getElementById('toggle-all');
            const categoriesContainer = document.getElementById('categories-container');

            toggleAllButton.addEventListener('click', function() {
                event.preventDefault(); // Prevent default action
                if (categoriesContainer.style.display === "none" || categoriesContainer.style.display === "") {
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

        function updateFileName() {
            const image = document.getElementById('post_image');
            const label = document.querySelector('.custom-file-label');
            const fileName = image.files[0] ? image.files[0].name : 'Choose file';
            label.textContent = fileName;
        }
    </script>

@endsection
