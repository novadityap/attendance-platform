<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepartmentRequest extends FormRequest
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
    $this->merge([
      'name' => $this->input('name'),
      'max_clock_in_time' => $this->input('maxClockInTime'),
      'max_clock_out_time' => $this->input('maxClockOutTime'),
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255|unique:departments,name',
      'max_clock_in_time' => 'required|date_format:H:i',
      'max_clock_out_time' => 'required|date_format:H:i|after:max_clock_in_time',
    ];
  }
}
