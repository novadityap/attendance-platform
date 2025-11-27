<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\AttendanceHistoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceWithHistoriesResource extends JsonResource
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
      'employee' => new EmployeeResource($this->employee),
      'checkIn' => $this->check_in,
      'checkOut' => $this->check_out,
      'histories' => AttendanceHistoryResource::collection($this->histories),
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
    ];
  }
}
