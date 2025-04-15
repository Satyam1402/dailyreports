<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EmployeeController;


use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin_PostController;
use App\Http\Controllers\EmployeeTaskController;
use App\Http\Controllers\All_Users_data_Controller;



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


// Admin-only routes for managing users
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

// Admin-only routes for managing Reports
Route::middleware(['auth','admin', 'verified'])->group(function () {
    // Display all tasks
    Route::get('/admin/reports', [AdminReportsController::class, 'index'])->name('admin.reports.index');
    Route::get('admin/reports/data', [AdminReportsController::class, 'getData'])->name('admin.reports.data');
});

// Employee-only routes for managing tasks
Route::middleware(['auth', 'employee','verified'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/tasks', [EmployeeTaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/data', [EmployeeTaskController::class, 'getData'])->name('tasks.data');
    Route::get('/tasks/create', [EmployeeTaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [EmployeeTaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}/edit', [EmployeeTaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/tasks/{task}', [EmployeeTaskController::class, 'update'])->name('tasks.update');
    Route::get('/tasks/{task}', [EmployeeTaskController::class, 'destroy'])->name('tasks.destroy');
});

// Admin-only routes for managing tasks
// Route::middleware(['auth', 'admin'])->group(function () {
//     // Display all tasks
//     Route::get('/admin/tasks', [TaskController::class, 'index'])->name('admin.tasks.index');
//     Route::get('admin/tasks/data', [TaskController::class, 'getData'])->name('admin.tasks.data');
//     Route::get('/admin/tasks/create', [TaskController::class, 'create'])->name('admin.tasks.create');
//     Route::post('/admin/tasks', [TaskController::class, 'store'])->name('admin.tasks.store');
//     Route::get('/admin/tasks/{id}', [TaskController::class, 'show'])->name('admin.tasks.show');
//     Route::put('/admin/tasks/{id}', [TaskController::class, 'update'])->name('admin.tasks.update');
//     Route::get('/admin/delete/{id}', [TaskController::class, 'destroy'])->name('admin.tasks.destroy');
// });
