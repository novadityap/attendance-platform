<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Helpers\CloudinaryHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\Employee\ProfileRequest;
use App\Http\Requests\Employee\CreateEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
  public function search(Request $request): JsonResponse
  {
    $page = $request->input('page', 1);
    $limit = $request->input('limit', 10);
    $q = $request->input('q');

    $query = Employee::query()
      ->with('department')
      ->when($q, function ($query) use ($q) {
        $query->where(function ($subQuery) use ($q) {
          $subQuery->where('name', 'ilike', "%{$q}%")
            ->orWhere('email', 'ilike', "%{$q}%")
            ->orWhereHas('department', function ($departmentQuery) use ($q) {
              $departmentQuery->where('name', 'ilike', "%{$q}%");
            })
            ->orWhereHas('role', function ($roleQuery) use ($q) {
              $roleQuery->where('name', 'ilike', "%{$q}%");
            });
        });
      })
      ->orderBy('created_at', 'desc');

    $employees = $query->paginate($limit, ['*'], 'page', $page);

    if ($employees->isEmpty()) {
      return response()->json([
        'message' => 'No employees found',
        'data' => [],
        'meta' => [
          'pageSize' => $limit,
          'totalItems' => 0,
          'currentPage' => $page,
          'totalPages' => 0
        ]
      ], 200);
    }

    return response()->json([
      'message' => 'Employees retrieved successfully',
      'data' => EmployeeResource::collection($employees),
      'meta' => [
        'pageSize' => $limit,
        'totalItems' => $employees->total(),
        'currentPage' => $page,
        'totalPages' => $employees->lastPage()
      ]
    ], 200);
  }

  public function create(CreateEmployeeRequest $request): JsonResponse
  {
    Employee::create($request->validated());

    return response()->json([
      'message' => 'Employee created successfully',
    ], 201);
  }

  public function show(Employee $employee): JsonResponse
  {
    return response()->json([
      'message' => 'Employee retrieved successfully',
      'data' => new EmployeeResource($employee->load('department'))
    ], 200);
  }

  public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
  {
    $fields = $request->validated();
    
    if (isset($fields['password'])) {
      $fields['password'] = Hash::make($fields['password']);
    }
    
    if ($request->hasFile('avatar')) {
      $publicId = Storage::putFile('avatars', $request->file('avatar'));
      $fields['avatar'] = Storage::url($publicId);
      $this->deleteAvatar($employee->avatar);
    }

    $employee->update($fields);

    return response()->json([
      'message' => 'Employee updated successfully',
      'data' => new EmployeeResource($employee->load('department'))
    ], 200);
  }

  public function delete(Employee $employee): JsonResponse
  {
    $employee->delete();

    return response()->json([
      'message' => 'Employee deleted successfully'
    ], 200);
  }

  protected function deleteAvatar(string $avatarUrl): void
  {
    if (config('app.default_avatar_url') !== $avatarUrl) {
      Storage::delete(CloudinaryHelper::extractPublicId($avatarUrl));
    }
  }
}
