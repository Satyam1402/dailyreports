<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class All_Users_data_Controller extends Controller
{
    public function index()
    {
        // echo "hello world";
        // exit;
        $data = User::query()->get();
        // print_r($data);
        // die;
        return view('daily_reports.index', compact('data'));
    }

    public function getData(Request $request)
    {

        if ($request->ajax()) {
            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','name','email','password','user_role','status','created_at','updated_at',
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // Apply the sorting to the query
            $data = User::select('*')->orderBy($sortColumn, $sortDirection);

            // Optional: Apply filters (if you want to enable filtering by status or role)
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('role')) {
                $query->where('user_role', $request->role);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('password', function ($row) {
                //     return '<code>' . e($row->password) . '</code>'; // Escaping to prevent XSS
                // })
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
                })
                ->addColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-m-Y h:i A');
                })
                ->addColumn('updated_at', function ($row) {
                    return Carbon::parse($row->updated_at)->format('d-m-Y h:i A');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex">
                            <a href="' . route('daily_reports.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('daily_reports.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['password','status','action'])
                ->make(true);
        }
    }

    public function add()
    {
        $data = User::query()->get();
        return view('daily_reports.add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // assuming table name is `users`
            'password' => 'required|string|min:4',
            'user_role' => 'required|in:admin,employee',
            'status' => 'required|in:1,0',
        ]);

        // Create a new MarketingHouseCategory instance
        $data = new User;
        $data->name = $request->name ?? ''; // Default to empty string if null
        $data->email = $request->email ?? ''; // Default to empty string if null
        $data->password = bcrypt($request->password); // Hash the password
        $data->user_role = $request->user_role ?? ''; // Default to empty string if null
        $data->status = $request->status ?? 0; // Default to 'Inactive' if null
        $data->save();

        // Redirect to the index page with a success message
        return redirect()->route('daily_reports.index')->with('success', 'User added successfully.');
    }


    public function show($id)
    {
        $data = User::findOrFail($id);
        return view('daily_reports.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $data = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:4',
            'user_role' => 'required|in:admin,employee',
            'status' => 'required|in:1,0',
        ]);

        $update = [
            'name' => $request->name,
            'email' => $request->email,
            'user_role' => $request->user_role,
            'status' => $request->status,
        ];

        if (!empty($request->password)) {
            $update['password'] = bcrypt($request->password);
        }

        $data->update($update);

        return redirect()->route('daily_reports.index')->with('success', 'User updated successfully!');
    }




    public function destroy($id)
    {
        $data = User::findOrFail($id);
        $data->delete();

        return redirect()->route('daily_reports.index');

    }
}
