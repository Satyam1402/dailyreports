<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin_PostController;


use App\Http\Controllers\All_Users_data_Controller;
use App\Http\Controllers\TaskController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('auth');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'register'])->name('register');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');





// // Routes for creating new Users
// Route::middleware(['auth:sanctum', 'verified','admin'])->group(function () {
//     Route::get('all/users', [All_Users_data_Controller::class, 'index'])->name('daily_reports.index');
//     Route::get('user/data', [All_Users_data_Controller::class, 'getData'])->name('daily_reports.data');
//     Route::get('user/add', [All_Users_data_Controller::class, 'add'])->name('daily_reports.add');
//     Route::post('user/store', [All_Users_data_Controller::class, 'store'])->name('daily_reports.store');
//     Route::get('user/show/{id}', [All_Users_data_Controller::class, 'show'])->name('daily_reports.show');
//     Route::post('user/update/{id}', [All_Users_data_Controller::class, 'update'])->name('daily_reports.update');
//     Route::get('user/destroy/{id}', [All_Users_data_Controller::class, 'destroy'])->name('daily_reports.destroy');
// });

// 🛡️ Admin-only routes for managing users
Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function () {
    Route::prefix('user')->name('daily_reports.')->group(function () {
        Route::get('/add', [All_Users_data_Controller::class, 'add'])->name('add');
        Route::post('/store', [All_Users_data_Controller::class, 'store'])->name('store');
        Route::get('/show/{id}', [All_Users_data_Controller::class, 'show'])->name('show');
        Route::post('/update/{id}', [All_Users_data_Controller::class, 'update'])->name('update');
        Route::get('/destroy/{id}', [All_Users_data_Controller::class, 'destroy'])->name('destroy');
        Route::get('/data', [All_Users_data_Controller::class, 'getData'])->name('data');
    });

    Route::get('all/users', [All_Users_data_Controller::class, 'index'])->name('daily_reports.index');
});

// Admin-only routes for managing tasks
Route::middleware(['auth', 'admin'])->group(function () {
    // Display all tasks
    Route::get('/admin/tasks', [TaskController::class, 'index'])->name('admin.tasks.index');

    // Show form for creating a new task
    Route::get('/admin/tasks/create', [TaskController::class, 'create'])->name('admin.tasks.create');

    // Store a new task
    Route::post('/admin/tasks', [TaskController::class, 'store'])->name('admin.tasks.store');

    // Show a specific task (for details or edit)
    Route::get('/admin/tasks/{id}', [TaskController::class, 'show'])->name('admin.tasks.show');

    // Edit task form
    Route::get('/admin/tasks/{id}/edit', [TaskController::class, 'edit'])->name('admin.tasks.edit');

    // Update task
    Route::put('/admin/tasks/{id}', [TaskController::class, 'update'])->name('admin.tasks.update');

    // Delete task
    Route::delete('/admin/tasks/{id}', [TaskController::class, 'destroy'])->name('admin.tasks.destroy');
});

// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::resource('/admin/tasks', TaskController::class);
// });

Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    // Add other employee-only routes here
});
