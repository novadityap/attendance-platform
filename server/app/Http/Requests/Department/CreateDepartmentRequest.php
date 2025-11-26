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
    logger('DEBUG', [
      'Request' => $this->all()
    ]);
    $this->merge([
      'max_check_in_time' => $this->get('maxCheckInTime'),
      'max_check_out_time' => $this->get('maxCheckOutTime'),
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
      'max_check_in_time' => 'required|date_format:H:i',
      'max_check_out_time' => 'required|date_format:H:i|after:max_check_in_time',
    ];
  }
}
