<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Employee::create([
      'id' => Str::uuid(),
      'department_id' => Department::where('name', 'IT')->first()->id,
      'name' => 'user',
      'email' => 'user@email.com',
      'password' => 'user123',
      'role_id' => Role::where('name', 'user')->first()->id
    ]);

    Employee::create([
      'id' => Str::uuid(),
      'department_id' => Department::where('name', 'HRD')->first()->id,
      'name' => 'admin',
      'email' => 'admin@email.com',
      'password' => 'admin123',
      'role_id' => Role::where('name', 'admin')->first()->id
    ]);
  }
}
