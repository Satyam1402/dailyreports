@extends('dashboard/master')
@section('title', 'Success Stories')
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
                        <h1>Success Stories</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Success Stories</li>
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
                                <a href="{{ route('client.add') }}" class="btn btn-primary">+ Add New</a>
                                <!-- Add Modal End -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            {{-- <th>Description</th> --}}
                                            {{-- <th>User Id</th> --}}
                                            <th>Home Display Order</th>
                                            <th>Service Display Order</th>
                                            {{-- <th>Created At</th> --}}
                                            {{-- <th>Updated At</th> --}}
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

@section('js')
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('client.data') }}",
                pageLength: 15,
                lengthMenu: [
                    [15, 30, 50, 100, 500, -1],
                    [15, 30, 50, 100, 500, "All"]
                ],
                order: [
                    [0, 'desc']
                ],
                columns: [
                    // {
                    //     data: null,
                    //     sortable: false,
                    //     render: function(data, type, row, meta) {
                    //         return meta.row + meta.settings._iDisplayStart + 1
                    //     }
                    // },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, // Serial Number Column
                    // {
                    //     data: 'id',
                    //     name: 'id',
                    // },
                    {
                        data: 'client_img',
                        name: 'client_img',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'client_title',
                        name: 'client_title',
                        orderable: false,
                        searchable: true
                    },
                    // {
                    //     data: 'client_description',
                    //     name: 'client_description',
                    //     orderable: false,
                    //     searchable: true
                    // },
                    {
                        data: 'display_order',
                        name: 'display_order'
                    },
                    {
                        data: 'service_display_order',
                        name: 'service_display_order'
                    },
                    // {
                    //     data: 'created_at',
                    //     name: 'created_at'
                    // },
                    // {
                    //     data: 'updated_at',
                    //     name: 'updated_at'
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        // orderable: false,
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
