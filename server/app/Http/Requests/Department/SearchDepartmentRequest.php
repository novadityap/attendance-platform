<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class SearchDepartmentRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation(): void
  {
    $this->merge([
      'page' => $this->input('page', 1),
      'limit' => $this->input('limit', 10),
      'q' => $this->input('q', null),
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
      'page' => 'sometimes|integer|min:1',
      'limit' => 'sometimes|integer|min:1|max:100',
      'q' => 'nullable|string',
    ];
  }
}
