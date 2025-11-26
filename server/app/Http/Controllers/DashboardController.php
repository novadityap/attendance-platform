<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\AttendanceResource;

class DashboardController extends Controller
{
  public function stats(): JsonResponse
  {
    $recentAttendances = Attendance::with(['employee:id,name'])
      ->orderByDesc('created_at')
      ->take(5)
      ->get();

    Log::info('Statistics data retrieved successfully');

    return response()->json([
      'code' => 200,
      'message' => 'Statistics data retrieved successfully',
      'data' => [
        'totalEmployees' => Employee::count(),
        'totalRoles' => Role::count(),
        'totalDepartments' => Department::count(),
        'totalAttendances' => Attendance::count(),
        'recentAttendances' => AttendanceResource::collection($recentAttendances),
      ],
    ]);
  }
}
