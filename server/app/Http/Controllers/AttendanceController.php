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
  public function __construct()
  {
    $this->attendance = new Attendance();
    $this->attendanceHistories = new AttendanceHistory();
  }

  public function search(Request $request): JsonResponse
  {
    $page = $request->input('page', 1);
    $limit = $request->input('limit', 10);
    $q = $request->input('q');
    $date = $request->input('date');
    $departmentId = $request->input('department_id');

    $attendances = Attendance::with(['employee.department', 'histories'])
      ->when($q, function ($query) use ($q) {
        $query->whereHas(
          'histories',
          fn($h) => $h
            ->where('date_attendance', 'ilike', "%{$q}%")
            ->orWhere('attendance_type', 'ilike', "%{$q}%")
        )->orWhereHas(
            'employee',
            fn($eq) => $eq
              ->where('name', 'ilike', "%{$q}%")
              ->orWhere('email', 'ilike', "%{$q}%")
              ->orWhereHas('department', fn($dq) => $dq->where('name', 'ilike', "%{$q}%"))
          );
      })
      ->when($date, fn($q) => $q->whereDate('check_in', $date))
      ->when($departmentId, fn($q) => $q->whereHas('employee', fn($eq) => $eq->where('department_id', $departmentId)))
      ->orderBy('check_in', 'desc')
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

  public function delete(Attendance $attendance): JsonResponse
  {
    $attendance->delete();

    return response()->json([
      'code' => 200,
      'message' => 'Attendance deleted successfully'
    ], 200);
  }

  public function today(Attendance $attendance): JsonResponse
  {
    $employeeId = auth()->user()->id;
    $attendance = Attendance::where('employee_id', $employeeId)
      ->whereDate('created_at', today())
      ->first();

    return response()->json([
      'message' => 'Attendance retrieved successfully',
      'data' => $attendance ? new AttendanceResource($attendance) : null,
    ]);
  }

  public function checkIn(Request $request): JsonResponse
  {
    $employee = auth()->user();
    $attendance = Attendance::where('employee_id', $employee->id)
      ->whereDate('check_in', today())
      ->exists();

    abort_if($attendance, 409, 'Already checked in today');

    abort_if(
      now('Asia/Jakarta')->format('H:i') < $employee->department->min_check_in_time,
      403,
      "Minimum check in time is {$employee->department->min_check_in_time}"
    );

    $attendance = Attendance::create([
      'employee_id' => $employee->id,
      'check_in' => now(),
      'check_out' => null,
    ]);

    AttendanceHistory::create([
      'employee_id' => $employee->id,
      'attendance_id' => $attendance->id,
      'date_attendance' => now(),
      'attendance_type' => 1,
    ]);

    return response()->json([
      'message' => 'Check in successfully',
      'data' => new AttendanceResource($attendance),
    ], 201);
  }

  public function checkOut(Request $request): JsonResponse
  {
    $employee = auth()->user();
    $attendance = Attendance::where('employee_id', $employee->id)
      ->whereDate('check_in', today())
      ->first();

    abort_if(
      !$attendance,
      400,
      'There is no check in today'
    );

    abort_if(
      $attendance->check_out !== null,
      409,
      'Already checked out today'
    );

    abort_if(
      now('Asia/Jakarta')->format('H:i') < $employee->department->min_check_out_time,
      403,
      "Minimum check out time is {$employee->department->min_check_out_time}"
    );

    $attendance->update([
      'check_out' => now(),
    ]);

    AttendanceHistory::create([
      'employee_id' => $employee->id,
      'attendance_id' => $attendance->id,
      'date_attendance' => now(),
      'attendance_type' => 2,
    ]);

    return response()->json([
      'message' => 'Check out successfully',
      'data' => new AttendanceResource($attendance),
    ], 200);
  }
}
