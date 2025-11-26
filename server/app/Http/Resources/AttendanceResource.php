<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\EmployeeResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceResource extends JsonResource
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
      'checkIn' => $this->check_in->format('Y-m-d H:i:s'),
      'checkOut' => $this->check_out ? $this->check_out->format('Y-m-d H:i:s') : null,
      'employee' => new EmployeeResource($this->employee),
      'createdAt' => $this->created_at,
      'updatedAt' => $this->updated_at,
    ];
  }
}
