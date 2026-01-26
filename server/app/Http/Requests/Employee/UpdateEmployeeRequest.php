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
    $mapping = [
      'departmentId' => 'department_id',
      'roleId' => 'role_id',
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
      'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
      'department_id' => 'sometimes|required|uuid|exists:departments,id',
      'role_id' => 'sometimes|required|uuid|exists:roles,id',
      'name' => 'sometimes|required|string|max:255',
      'email' => 'sometimes|required|string|unique:employees,email,' . $this->route('employee')->id,
      'password' => 'sometimes|required|string',
    ];
  }
}
