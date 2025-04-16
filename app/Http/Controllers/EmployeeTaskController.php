<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class EmployeeTaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())->get();
        return view('employee.tasks.index', compact('tasks'));
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            try {
                $sortColumnIndex = $request->get('order')[0]['column'] ?? 0;
                $sortDirection = $request->get('order')[0]['dir'] ?? 'asc';

                $columns = [
                    'id', 'task_info', 'status', 'created_at', 'updated_at'
                ];

                $sortColumn = $columns[$sortColumnIndex] ?? 'id';

                $data = Task::where('user_id', Auth::id())
                            ->orderBy($sortColumn, $sortDirection);

                return DataTables::of($data)
                    ->addIndexColumn()
                    // ->editColumn('status', function ($row) {
                    //     return $row->status == 1
                    //         ? '<span class="badge bg-success">Active</span>'
                    //         : '<span class="badge bg-danger">Inactive</span>';
                    // })
                    ->editColumn('created_at', function ($row) {
                        return Carbon::parse($row->created_at)->format('d M Y');
                    })
                    // ->editColumn('updated_at', function ($row) {
                    //     return Carbon::parse($row->updated_at)->format('d-m-Y h:i A');
                    // })
                    ->addColumn('action', function ($row) {
                        return '
                            <a href="' . route('employee.tasks.edit', $row->id) . '" class="btn btn-info btn-sm mr-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="mb-1 btn btn-danger btn-sm" onclick="confirmDelete(\'' . route('employee.tasks.destroy', $row->id) . '\');">
                                <i class="fas fa-trash"></i>
                            </a>
                        ';
                    })
                    ->rawColumns(['action'])
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
        return view('employee.tasks.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_info' => 'required|string|max:255',
            // 'status' => 'required|in:0,1',
        ]);

        Task::create([
            'user_id' => auth()->id(),
            'task_info' => $request->task_info,
            // 'status' => $request->status,
        ]);

        return redirect()->route('employee.tasks.index')->with('success', 'Task created successfully!');
    }
    // public function show(Task $task)
    // {
    //     // Only allow viewing if task belongs to logged-in user
    //     if ($task->user_id !== auth()->id()) {
    //         abort(403);
    //     }

    //     return view('employee.tasks.show', compact('task'));
    // }

    public function edit(Task $task)
    {
        // Only allow editing if task belongs to logged-in user
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        return view('employee.tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'task_info' => 'required|string|max:255',
            // 'status' => 'required|in:0,1',
        ]);

        $task->update([
            'task_info' => $request->task_info,
            // 'status' => $request->status,
        ]);

        return redirect()->route('employee.tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        // Only allow deletion if task belongs to logged-in user
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->route('employee.tasks.index')->with('success', 'Task deleted successfully!');
    }
}
