<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
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
      'department_id' => $this->input('departmentId'),
      'role_id' => $this->input('roleId')
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
      'department_id' => 'sometimes|required|exists:departments,id',
      'role_id' => 'sometimes|required|exists:roles,id',
      'name' => 'sometimes|required|string|max:255',
      'email' => 'sometimes|required|string',
      'password' => 'sometimes|required|string',
    ];
  }
}
