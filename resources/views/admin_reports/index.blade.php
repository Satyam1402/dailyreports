@extends('dashboard/master')

@section('title', 'All Reports')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1>Reports</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">All Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header py-2 px-3">
                    <div class="row align-items-end justify-content-between">
                        <!-- Employee Filter -->
                        <div class="col-md-4 px-1">
                            <div class="form-group mb-0">
                                <label for="userFilter">User Name</label>
                                <select id="userFilter" class="form-control">
                                    <option value="">-- select user --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date Filters (Right aligned) -->
                        {{-- <div class="col-md-7 mt-2">
                            <div class="form-row d-flex justify-content-end">
                                <div class="form-group mr-2">
                                    <label for="start_date">Start Date</label>
                                    <input type="date" id="start_date" class="form-control">
                                </div>
                                <div class="form-group mr-2">
                                    <label for="end_date">End Date</label>
                                    <input type="date" id="end_date" class="form-control">
                                </div>
                                <div class="form-group mt-4">
                                    <button id="filterBtn" class="btn btn-primary">Filter</button>
                                </div>
                            </div>
                        </div> --}}
                        <!-- In your Blade file -->
                        <div class="col-md-3  px-1">
                            <div class="form-group mb-0">
                                <label for="filter_date">Filter By Date</label>
                                <input type="date" id="filter_date" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

                <div class="card-body py-1 px-2">
                    <table id="data-table" class="table table-bordered table-hover table-sm w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Report</th>
                                {{-- <th>Created At</th>
                                <th>Updated At</th> --}}
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
   $(document).ready(function() {
    // Initialize DataTable
    var table = $('#data-table').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.reports.data') }}", // Your route to fetch data
            data: function (d) {
                d.user_id = $('#userFilter').val(); // Pass selected user ID
                d.filter_date = $('#filter_date').val(); // Send selected date
                // d.start_date = $('#start_date').val();
                // d.end_date = $('#end_date').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'user_name', name: 'user_name' },
            { data: 'task_info', name: 'task_info' },
            // { data: 'created_at', name: 'created_at' },
            // { data: 'updated_at', name: 'updated_at' }
        ]
    });

    // Trigger reload when user selection changes
    $('#userFilter,#filter_date').change(function () {
        table.ajax.reload(); // Reload the table with the new user filter
        });
    });

    // Filter on date or filter button click
    // $('#filterBtn').on('click', function () {
    //     table.ajax.reload();
    // });


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
