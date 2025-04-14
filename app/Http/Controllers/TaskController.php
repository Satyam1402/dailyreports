<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            $sortColumnIndex = $request->get('order')[0]['column']; // value depend on datatable field not in table

            $sortDirection = $request->get('order')[0]['dir'];

            // Map column index to actual column names (you can adjust this as per your columns)
            $columns = [
                'id','task_info','user_id','status','created_at','updated_at',
            ]; // value depend on datatable field not in table

            // Get the column name for sorting
            $sortColumn = $columns[$sortColumnIndex];// value depend on datatable field not in table

            // Apply the sorting to the query
            $data = Task::select('*')->orderBy($sortColumn, $sortDirection);

            return DataTables::of($data)
                ->addIndexColumn()
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
                            <a href="' . route('admin.tasks.show', $row->id) . '" class="mb-1 btn btn-primary btn-sm mr-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('admin.tasks.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['status','action'])
                ->make(true);
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
          ]);
  
          Task::create([
              'task_info' => $request->task_info,
              'user_id' => $request->user_id,
              'status' => 'pending', // Default status
          ]);
  
          return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully!');
      }
  
      // Show specific task details
      public function show($id)
      {
          $task = Task::with('user')->findOrFail($id);
          return view('admin.tasks.show', compact('task'));
      }
  
      // Show form to edit a task
      public function edit($id)
      {
          $task = Task::findOrFail($id);
          $users = User::all();  // Get all users to assign tasks
          return view('admin.tasks.edit', compact('task', 'users'));
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
