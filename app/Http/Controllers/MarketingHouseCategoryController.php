<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\MarketingHouseCategory;
use App\Models\User;

class MarketingHouseCategoryController extends Controller
{
    public function index()
    {
        // echo "hello world";
        // exit;
        $data = User::query()->get();
        return view('daily_reports.index', compact('data'));
    }

    public function getData(Request $request)
    {

        if ($request->ajax()) {
            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','name', 'email', 'user_role', 'status', 'created_at', 'updated_at',
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // Apply the sorting to the query
            $data = User::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    if ($row->status == 0) {
                        return '<span class="badge bg-danger">Inactive</span>';
                    } else {
                        return '<span class="badge bg-success">Active</span>';
                    }
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
                ->rawColumns(['status','action',])
                ->make(true);
        }
    }

    public function add()
    {
        $data = User::query()->get();
        // print_r($data);
        // die;

        return view('daily_reports.add');
    }

    public function store(Request $request)
    {

        // Get the authenticated user's ID
        // $userId = Auth::user()->id;
        // $image = upload_file_to_s3($request, 'marketing_house_icon', 'Marketing-house-icon');

        // Create a new MarketingHouseCategory instance
        $data = new User;
        $data->name = $request->name ?? ''; // Default to empty string if null
        $data->email = $request->email ?? ''; // Default to empty string if null
        $data->password = bcrypt($request->password); // Hash the password
        $data->user_role = $request->user_role ?? ''; // Default to empty string if null
        $data->status = $request->status ?? 0; // Default to 'Inactive' if null
        $data->save();

        // Redirect to the index page with a success message
        return redirect()->route('daily_reports.index');
    }


    public function show($id)
    {
        // echo "hello world";
        // die();
        $data = User::findOrFail($id);
        // print_r($category);
        // die();
        return view('daily_reports.edit', compact('data'));
    }

    public function update(Request $request)
    {
        // Validate the request (you can add specific validation rules as needed)
        // $request->validate([
        //     'category_name' => 'required|string|max:255',
        //     'display_order' => 'nullable|integer|min:0',
        //     'status' => 'required|integer|in:0,1',
        // ]);

        // Get the ID from the request
        $id = $request->id;

        // Find the category to update
        $data = User::find($id);
        // $image = upload_file_to_s3($request, 'marketing_house_icon', 'Marketing-house-icon');

        // Get the user ID (from the authenticated user)
        $userId = Auth::user()->id;

        // Prepare the data for updating
        $update = [
            'name' => $request->name ?? '',
            'email' => $request->email ?? '',
            'password' => bcrypt($request->password),
            'user_role' => $request->user_role ?? '',
            'status' => $request->status ?? 0,
        ];

        // Update the category
        $data->update($update);

        // Redirect to the desired page after updating
        return redirect()->route('daily_reports.index');
    }



    public function destroy($id)
    {
        $data = User::findOrFail($id);
        $data->delete();

        return redirect()->route('daily_reports.index');

    }
}
