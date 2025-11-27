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
      'min_check_in_time' => $this->get('minCheckInTime'),
      'min_check_out_time' => $this->get('minCheckOutTime'),
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
      'min_check_in_time' => 'required|date_format:H:i',
      'min_check_out_time' => 'required|date_format:H:i',
      'max_check_in_time' => 'required|date_format:H:i',
      'max_check_out_time' => 'required|date_format:H:i',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $minIn = $this->input('min_check_in_time');
      $maxIn = $this->input('max_check_in_time');
      $minOut = $this->input('min_check_out_time');
      $maxOut = $this->input('max_check_out_time');

      if ($minIn && $maxIn && $minIn > $maxIn) {
        $validator->errors()->add('min_check_in_time', 'Minimum Check-In cannot be greater than Maximum Check-In.');
      }

      if ($minOut && $maxOut && $minOut > $maxOut) {
        $validator->errors()->add('min_check_out_time', 'Minimum Check-Out cannot be greater than Maximum Check-Out.');
      }
    });
  }
}
