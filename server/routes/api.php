<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DepartmentController;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
  Route::post('/signin', 'signin');
  Route::post('/refresh-token', 'refreshToken');
  Route::post('/reset-password/{resetToken}', 'resetPassword');
  Route::post('/request-reset-password', 'requestResetPassword');
  Route::post('/signout', 'signout')->middleware('auth:api');
});

Route::prefix('roles')->middleware('auth:api')->controller(RoleController::class)->group(function () {
  Route::middleware('authorize:admin')->group(function () {
    Route::get('/', 'list');
    Route::post('/', 'create');
    Route::get('/search', 'search');
    Route::get('/{role}', 'show');
    Route::patch('/{role}', 'update');
    Route::delete('/{role}', 'delete');
  });
});

Route::prefix('departments')->middleware('auth:api')->controller(DepartmentController::class)->group(function () {
  Route::middleware('authorize:admin')->group(function () {
    Route::get('/', 'list');
    Route::post('/', 'create');
    Route::get('/search', 'search');
    Route::get('/{department}', 'show');
    Route::patch('/{department}', 'update');
    Route::delete('/{department}', 'delete');
  });
});

Route::prefix('employees')->controller(EmployeeController::class)->group(function () {
  Route::middleware('authorize:employee')->group(function () {
    Route::post('/', 'create');
    Route::get('/search', 'search');
    Route::get('/{employee}', 'show')->middleware('can:show,employee');
    Route::patch('/{employee}', 'update')->middleware('can:update,employee');
    Route::delete('/{employee}', 'delete');
    Route::patch('/{employee}/profile', 'profile')->middleware('can:profile,employee');
  });
});

Route::prefix('attendances')->middleware('auth:api')->controller(AttendanceController::class)->group(function () {
  Route::get('/search', 'search')->middleware('authorize:admin');
  Route::post('/clockIn', 'clockIn');
  Route::patch('/clockOut', 'clockOut');
});

Route::prefix('dashboard')->middleware('auth:api')->controller(DashboardController::class)->group(function () {
  Route::middleware('authorize:admin')->group(function () {
    Route::get('/', 'stats');
  });
});
