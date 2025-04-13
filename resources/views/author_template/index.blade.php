@extends('dashboard/master')
@section('title', 'Author Template')
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
                        <h1>Author Template</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Author Template</li>
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
                                <a href="{{ route('author-template.add') }}" class="btn btn-primary">+ Add New</a>
                                <!-- Add Modal End -->
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Template Name</th>
                                            <th>Author Image</th>
                                            <th>Author Name</th>
                                            <th>Author Description</th>
                                            {{-- <th>Click here Text</th> --}}
                                            {{-- <th>Click here Url</th> --}}
                                            <th>Author Url</th>
                                            {{-- <th>Founder Text</th> --}}
                                            {{-- <th>Founder Url</th> --}}
                                            {{-- <th>CTO Text</th> --}}
                                            {{-- <th>CTO Url</th> --}}
                                            {{-- <th>Title 2</th>
                                            <th>Description 2</th> --}}
                                            {{-- <th>Button Text</th> --}}
                                            {{-- <th>User Id</th> --}}
                                            <th>Display Order</th>
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
                ajax: "{{ route('author-template.data') }}",
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
                        data: 'template_name',
                        name: 'template_name',
                        orderable: false,
                        // searchable: false 
                    },
                    {
                        data: 'author_image',
                        name: 'author_image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'author_name',
                        name: 'author_name',
                        orderable: false,
                    },
                    {
                        data: 'author_description',
                        name: 'author_description',
                        orderable: false,
                    },
                    {
                        data: 'author_url',
                        name: 'author_url',
                        orderable: false,
                    },
                    // {
                    //     data: 'click_here_text',
                    //     name: 'click_here_text',
                    //     orderable: false,
                    // },
                    // {
                    //     data: 'click_here_url',
                    //     name: 'click_here_url',
                    //     orderable: false,
                    // },
                   
                    // {
                    //     data: 'founder_text',
                    //     name: 'founder_text',
                    //     orderable: false,
                    // },
                    // {
                    //     data: 'cto_text',
                    //     name: 'cto_text',
                    //     orderable: false,
                    // },
                    // {
                    //     data: 'cto_url',
                    //     name: 'cto_url',
                    //     orderable: false,
                    // },
                    {
                        data: 'display_order',
                        name: 'display_order',

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
