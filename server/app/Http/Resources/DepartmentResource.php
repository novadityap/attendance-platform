<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
      'name' => $this->name,
      'maxCheckInTime' => $this->max_check_in_time
        ? $this->max_check_in_time->format('H:i')
        : null,
      'maxCheckOutTime' => $this->max_check_out_time
        ? $this->max_check_out_time->format('H:i')
        : null,
    ];
  }
}
