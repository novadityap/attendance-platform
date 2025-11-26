<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Department::create([
      'id' => Str::uuid(),
      'name' => 'IT',
      'max_check_in_time' => '12:00:00',
      'max_check_out_time' => '17:00:00',
    ]);

    Department::create([
      'id' => Str::uuid(),
      'name' => 'HRD',
      'max_check_in_time' => '12:30:00',
      'max_check_out_time' => '16:30:00',
    ]);
  }
}
