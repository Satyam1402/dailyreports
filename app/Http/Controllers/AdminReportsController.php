<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name')->orderBy('name')->get();
        return view('admin_reports.index', compact('users'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Get the user_id from the request
                $user_id = $request->get('user_id', null);
                // $start_date = $request->get('start_date');
                // $end_date = $request->get('end_date');
                $filter_date = $request->get('filter_date');

                // Build the query
                $query = Task::leftJoin('users', 'tasks.user_id', '=', 'users.id')
                             ->select('tasks.*', 'users.name as user_name')
                             ->orderBy('tasks.updated_at', 'desc');

                // If a user_id is provided, filter by that user
                if ($user_id) {
                    $query->where('tasks.user_id', $user_id);
                }

                // Date filtering
                // if ($start_date && $end_date) {
                //     $query->whereBetween(DB::raw('DATE(tasks.created_at)'), [$start_date, $end_date]);
                // } elseif ($start_date) {
                //     $query->whereDate('tasks.created_at', $start_date);
                // }
                if ($filter_date) {
                    $query->whereDate('tasks.created_at', $filter_date);
                }

                $data = $query->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('user_name', function ($row) {
                        return $row->user_name ?? 'N/A';
                    })
                    // ->editColumn('status', function ($row) {
                    //     return $row->status == 0
                    //         ? '<span class="badge bg-danger">Inactive</span>'
                    //         : '<span class="badge bg-success">Active</span>';
                    // })
                    // ->editColumn('created_at', function ($row) {
                    //     return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A');
                    // })
                    // ->editColumn('updated_at', function ($row) {
                    //     return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y h:i A');
                    // })
                    ->rawColumns(['user_name'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Datatable Error: ' . $e->getMessage());

                return response()->json([
                    'error' => 'Something went wrong: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

}
