<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceHistory;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\AttendanceWithHistoriesResource;

class AttendanceController extends Controller
{
  public function search(Request $request): JsonResponse
  {
    $page = $request->input('page', 1);
    $limit = $request->input('limit', 10);
    $q = $request->input('q');
    $date = $request->input('date');
    $departmentId = $request->input('department_id');

    $attendances = Attendance::with(['employee.department', 'histories'])
    ->when($q, function ($query) use ($q) {
        $query->whereHas('histories', fn($h) => $h
            ->where('date_attendance', 'ilike', "%{$q}%")
            ->orWhere('attendance_type', 'ilike', "%{$q}%")
        )->orWhereHas('employee', fn($eq) => $eq
            ->where('name', 'ilike', "%{$q}%")
            ->orWhere('email', 'ilike', "%{$q}%")
            ->orWhereHas('department', fn($dq) => $dq->where('name', 'ilike', "%{$q}%"))
        );
    })
    ->when($date, fn($q) => $q->whereDate('clock_in', $date))
    ->when($departmentId, fn($q) => $q->whereHas('employee', fn($eq) => $eq->where('department_id', $departmentId)))
    ->orderBy('clock_in', 'desc')
    ->paginate($limit, ['*'], 'page', $page);

    return response()->json([
      'message' => 'Attendances retrieved successfully',
      'data' => AttendanceWithHistoriesResource::collection($attendances),
      'meta' => [
        'pageSize' => $limit,
        'totalItems' => $attendances->total(),
        'currentPage' => $attendances->currentPage(),
        'totalPages' => $attendances->lastPage(),
      ]
    ]);
  }

  public function clockIn(Request $request): JsonResponse
  {
    $employeeId = auth()->user()->id;
    $attendance = Attendance::where('employee_id', $employeeId)
      ->whereDate('clock_in', Carbon::today())
      ->first();

    if ($attendance) {
      return response()->json(['message' => 'Already checked in today'], 400);
    }

    $attendance = Attendance::create([
      'employee_id' => $employeeId,
      'clock_in' => now(),
      'clock_out' => null,
    ]);

    AttendanceHistory::create([
      'employee_id' => $employeeId,
      'attendance_id' => $attendance->id,
      'date_attendance' => now(),
      'attendance_type' => 1,
    ]);

    return response()->json([
      'message' => 'Check in successfully',
      'data' => new AttendanceResource($attendance),
    ], 200);
  }

  public function clockOut(Request $request): JsonResponse
  {
    $employeeId = auth()->user()->id;
    $attendance = Attendance::where('employee_id', $employeeId)
      ->whereDate('clock_in', Carbon::today())
      ->first();

    if (!$attendance) {
      return response()->json(['message' => 'There is no check in today'], 400);
    }

    if ($attendance->clock_out) {
      return response()->json(['message' => 'Already checked out today'], 400);
    }

    $attendance->update([
      'clock_out' => now()
    ]);

    AttendanceHistory::create([
      'employee_id' => $employeeId,
      'attendance_id' => $attendance->id,
      'date_attendance' => now(),
      'attendance_type' => 2,
    ]);

    return response()->json([
      'message' => 'Check out successfully',
      'data' => new AttendanceResource($attendance)
    ], 200);
  }
}
