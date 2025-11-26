<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmployeeRequest extends FormRequest
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
      'department_id' => $this->get('departmentId'),
      'role_id' => $this->get('roleId')
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
      'department_id' => 'required|exists:departments,id',
      'role_id' => 'required|exists:roles,id',
      'name' => 'required|string|max:255',
      'email' => 'required|string',
      'password' => 'required|string',
    ];
  }
}
