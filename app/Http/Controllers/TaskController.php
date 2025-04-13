<?php

namespace App\Http\Controllers;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // echo "hello world";
        // die;
        $tasks = Task::with('user')->latest()->get();
        return view('admin.tasks.index', compact('tasks'));
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
