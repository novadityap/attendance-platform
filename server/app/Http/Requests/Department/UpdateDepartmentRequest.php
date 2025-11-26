<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $mapping = [
      'maxCheckInTime' => 'max_check_in_time',
      'maxCheckOutTime' => 'max_check_out_time'
    ];

    foreach ($mapping as $from => $to) {
      if ($this->has($from)) {
        $this->merge([$to => $this->get($from)]);
      }
    }
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'sometimes|required|string|max:255',
      'max_check_in_time' => 'sometimes|required|date_format:H:i',
      'max_check_out_time' => 'sometimes|required|date_format:H:i'
    ];
  }
}
