<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function index()
    {
        // echo "hello world";
        // die;
        $tasks = Task::with('user')->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            try {
                $sortColumnIndex = $request->get('order')[0]['column'] ?? 0;
                $sortDirection = $request->get('order')[0]['dir'] ?? 'asc';

                $columns = [
                    'id', 'task_info', 'user_id', 'status', 'created_at', 'updated_at',
                ];

                $sortColumn = $columns[$sortColumnIndex] ?? 'id';

                $data = Task::leftJoin('users', 'tasks.user_id', '=', 'users.id')
                            ->select('tasks.*', 'users.name as user_name')
                            ->orderBy('tasks.updated_at', 'desc');

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('user_name', function ($row) {
                        return $row->user_name ?? 'N/A';
                    })
                    ->editColumn('status', function ($row) {
                        return $row->status == 0
                            ? '<span class="badge bg-danger">Inactive</span>'
                            : '<span class="badge bg-success">Active</span>';
                    })
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A');
                    })
                    ->editColumn('updated_at', function ($row) {
                        return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y h:i A');
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <div class="d-flex">
                                <a href="' . route('admin.tasks.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('admin.tasks.destroy', $row->id) . '\');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        ';
                    })
                    ->rawColumns(['user_name','status', 'action'])
                    ->make(true);
            } catch (\Exception $e) {
                Log::error('Datatable Error: ' . $e->getMessage());

                return response()->json([
                    'error' => 'Something went wrong: ' . $e->getMessage(),
                ], 500);
            }
        }
    }


    public function create()
    {
        $users = User::all();  // Get all users to assign tasks
        return view('admin.tasks.create', compact('users'));
    }

      // Store a new task
      public function store(Request $request)
      {
          $request->validate([
              'task_info' => 'required|string',
              'user_id' => 'required|exists:users,id',
              'status' => 'required|in:0,1', // Validate status if coming from form
          ]);

          Task::create([
              'task_info' => $request->task_info,
              'user_id' => $request->user_id,
              'status' => $request->status,
          ]);

          return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully!');
      }

      // Show specific task details
      public function show($id)
      {
          try {
              $task = Task::with('user')->findOrFail($id);
              $users = User::all(); // Get all users for the dropdown

              return view('admin.tasks.edit', compact('task', 'users'));
          } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
              return redirect()->route('admin.tasks.index')->with('error', 'Task not found.');
          }
      }

      // Update an existing task
      public function update(Request $request, $id)
      {
          $request->validate([
              'task_info' => 'required|string',
              'user_id' => 'required|exists:users,id',
          ]);

          $task = Task::findOrFail($id);
          $task->update([
              'task_info' => $request->task_info,
              'user_id' => $request->user_id,
              'status' => $request->status,  // Ensure you handle the status as needed
          ]);

          return redirect()->route('admin.tasks.index')->with('success', 'Task updated successfully!');
      }

      // Delete a task
      public function destroy($id)
      {
          $task = Task::findOrFail($id);
          $task->delete();

          return redirect()->route('admin.tasks.index')->with('success', 'Task deleted successfully!');
      }
}
