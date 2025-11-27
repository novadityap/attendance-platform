<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\DepartmentResource;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\UpdateDepartmentRequest;
use App\Http\Requests\Department\SearchDepartmentRequest;

class DepartmentController extends Controller
{
  public function list(): JsonResponse {
    $departments = Department::all();

    if ($departments->isEmpty()) {
      return response()->json([
        'message' => 'No departments found',
        'data' => [],
      ], 200);
    }

    return response()->json([
      'message' => 'Departments retrieved successfully',
      'data' => DepartmentResource::collection($departments)
    ], 200);
  }

  public function create(CreateDepartmentRequest $request): JsonResponse
  {
    Department::create($request->validated());

    return response()->json([
      'message' => 'Department created successfully',
    ], 201);
  }

  public function search(SearchDepartmentRequest $request): JsonResponse
  {
    $page = $request->input('page', 1);
    $limit = $request->input('limit', 10);
    $q = $request->input('q');

    $departments = Department::query()
      ->when($q, function ($query) use ($q) {
        $query->where(function ($subQuery) use ($q) {
          $subQuery->where('name', 'ilike', "%{$q}%")
            ->orWhere('min_check_in_time', 'ilike', "%{$q}%")
            ->orWhere('min_check_out_time', 'ilike', "%{$q}%")
            ->orWhere('max_check_in_time', 'ilike', "%{$q}%")
            ->orWhere('max_check_out_time', 'ilike', "%{$q}%");
        });
      })
      ->orderBy('created_at', 'desc')
      ->paginate($limit, ['*'], 'page', $page);


    if ($departments->isEmpty()) {
      return response()->json([
        'message' => 'No departments found',
        'data' => [],
        'meta' => [
          'pageSize' => $limit,
          'totalItems' => 0,
          'currentPage' => $page,
          'totalPages' => 0,
        ],
      ]);
    }

    return response()->json([
      'message' => 'Departments retrieved successfully',
      'data' => DepartmentResource::collection($departments),
      'meta' => [
        'pageSize' => $limit,
        'totalItems' => $departments->total(),
        'currentPage' => $page,
        'totalPages' => $departments->lastPage(),
      ],
    ]);
  }

  public function show(Department $department): JsonResponse
  {
    return response()->json([
      'message' => 'Department retrieved successfully',
      'data' => new DepartmentResource($department)
    ], 200);
  }

  public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
  {
    $department->update($request->validated());

    return response()->json([
      'message' => 'Department updated successfully',
      'data' => new DepartmentResource($department)
    ], 200);
  }

  public function delete(Department $department): JsonResponse
  {
    $department->delete();

    return response()->json([
      'message' => 'Department deleted successfully'
    ], 200);
  }
}

