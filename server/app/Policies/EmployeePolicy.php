<?php

namespace App\Policies;

use App\Models\Employee;

class EmployeePolicy
{
  public function show(Employee $authEmployee, Employee $employee): bool
  {
    return $authEmployee->id === $employee->id || $authEmployee->role->name === 'admin';
  }

  public function profile(Employee $authEmployee, Employee $employee): bool
  {
    return $authEmployee->id === $employee->id || $authEmployee->role->name === 'admin';
  }

  public function update(Employee $authEmployee, Employee $employee): bool
  {
    return $authEmployee->id === $employee->id || $authEmployee->role->name === 'admin';
  }
}
