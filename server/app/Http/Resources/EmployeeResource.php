<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\RoleResource;
use App\Http\Resources\DepartmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'avatar' => $this->avatar,
      'name' => $this->name,
      'email' => $this->email,
      'roleId' => $this->role_id,
      'role' => new RoleResource($this->whenLoaded('role')),
      'departmentId' => $this->department_id,
      'department' => new DepartmentResource($this->whenLoaded('department')),
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
      'token' => $this->when(isset($this->token), $this->token)
    ];
  }
}
