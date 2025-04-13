@extends('dashboard/master')
@section('title', 'Admin Post')
@section('css')
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Admin Post</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active"> Admin Post</li>
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
                        <div class="card">
                            <div class="card-header">
                                <a href="{{ route('admin-post.add') }}" class="btn btn-primary">+ Add New Post</a>
                                <!-- Add Modal End -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                {{-- <div class="container"> --}}

                                {{-- <h1 style="color: #6e8386"><b><i>Laravel Crud </i></b></h1> --}}

                                {{-- <a href="{{ url('/add') }}" class='btn btn-warning' style="float:right">Add Data</a> --}}
                                {{-- <a href="{{ url('/add') }}" class="btn btn-warning" style="float:right; margin-top: 10px; margin-right: 3%; margin-bottom: 0.5%">Add Data</a> --}}
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>ID</th>
                                            <th>Post Image</th>
                                            <th>Title</th>
                                            <th>Insitution</th>
                                            <th>Post Name</th>
                                            <th>Website</th>
                                            {{-- <th>Post Content</th> --}}
                                            <th>User Id</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        @foreach ($data as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>
                                                    <img src="{{ asset('post-image/' . $item->post_image) }}"
                                                        alt="{{ $item->post_image }}" width="80" height="80">
                                                    {{-- {{ $item->post_image }} --}}
                                                </td>
                                                <td>{{ $item->post_title }}</td>
                                                <td>{{ $item->insitution_name }}</td>
                                                <td>{{ $item->post_name }}</td>
                                                <td>{{ $item->website }}</td>
                                                {{-- <td>{{ $item->post_content }}</td> --}}
                                                <td>{{ $item->admin_user_id }}</td>
                                                <td>
                                                    @if ($item->status == 0)
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{-- <a href="{{ url('/edit/'.$item->id) }}" class="btn btn-primary">Edit</a> --}}
                                                    <a href="{{ route('admin-post.edit', $item->id) }}"
                                                        class="btn btn-primary">Edit</a>
                                                    {{-- <a href="{{ route('item.edit', ['id' => $item->id]) }}" class="btn btn-primary">Edit</a> --}}

                                                    {{-- <a href="{{ route('category.destroy', $item->id) }}" class="btn btn-danger">Delete</a> --}}
                                                    <a href="#" class="btn btn-danger"
                                                        onclick="confirmDelete('{{ route('admin-post.destroy', $item->id) }}');">
                                                        Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
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
        function confirmDelete(url) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel it'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
    </script>


@endsection
