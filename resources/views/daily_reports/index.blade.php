@extends('dashboard/master')
@section('title', 'All Users')
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
                        <h1>All Users</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">All Users List</li>
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
                                <a href="{{ route('daily_reports.add') }}" class="btn btn-primary">+ Create a new user</a>
                                <!-- Add Modal End -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Role</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            {{-- <th>Password</th> --}}
                                            <th>Created At</th>
                                            <th>Updated At</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                </table>
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
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('daily_reports.data') }}",
                pageLength: 15,
                lengthMenu: [
                    [15, 30, 50, 100, 500, -1],
                    [15, 30, 50, 100, 500, "All"]
                ],
                order: [
                    [0, 'desc']
                ],
                columns: [

                    { data: 'DT_RowIndex',
                     name: 'DT_RowIndex',
                      orderable: false,
                      searchable: false
                    },  // Serial Number Column
                    {
                        data: 'user_role',
                        name: 'user_role'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    // {
                    //     data: 'password',
                    //     name: 'password'
                    // },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                search: {
                    smart: true,
                    regex: false,
                    caseInsensitive: true
                }
            });
        });
    </script>
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
