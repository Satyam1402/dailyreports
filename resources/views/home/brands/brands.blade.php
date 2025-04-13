@extends('dashboard/master')
@section('title', 'Brands')
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
                        <h1>Brand</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Brands</li>
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
                                <a href="{{ route('brands.add') }}" class="btn btn-primary">+ Add New </a>
                                <!-- Add Modal End -->
                            </div>
                            <!-- /.card-header -->
                            {{-- <div class="card-body">

                                <br>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <tr>
                                            <th>ID</th>
                                            <th>Brand Name</th>
                                            <th>Brand Image</th>
                                            <th>Website</th>
                                            <th>Display order</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        @foreach ($data as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->brand_name }}</td>
                                            <td>
                                                <img src="{{ $item->brand_image }}" alt="{{ $item->brand_image }}" width="80" height="80">
                                            </td>
                                            <td>{{ $item->website_url }}</td>
                                            <td>{{ $item->display_order }}</td>
                                            <td>
                                                @if ($item->status == 0)
                                                    <span class="badge bg-danger">Inactive</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex">
                                                    <a href="{{ url('template/brands/show/'.$item->id) }}" style="margin-right: 3px" class="btn btn-primary me-3">Edit</a>
                                                    <a href="#" class="btn btn-danger" onclick="confirmDelete('{{ route('brands.destroy', $item->id) }}');">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </table>
                                </div>
                            </div> --}}
                            <div class="card-body">
                                <table id="data-table" class="table table-bordered table-hover" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Brand Name</th>
                                            <th>Brand Image</th>
                                            {{-- <th>Website</th> --}}
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
            ajax: "{{ route('brands.data') }}",
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
                { data: 'DT_RowIndex',
                 name: 'DT_RowIndex',
                  orderable: false, 
                  searchable: false 
                },  // Serial Number Column
                // {
                //     data: 'id',
                //     name: 'id',
                // },
                {
                    data: 'brand_name',
                    name: 'brand_name',
                    orderable: false, 
                    // searchable: false 
                },
                {
                    data: 'brand_image',
                    name: 'brand_image',
                    orderable: false, 
                    // searchable: false 
                },
                // {
                //     data: 'website_url',
                //     name: 'website_url'
                // },
                {
                    data: 'display_order',
                    name: 'display_order'
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
