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
      'min_check_in_time' => '06:00:00',
      'max_check_in_time' => '16:00:00',
      'min_check_out_time' => '16:00:00',
      'max_check_out_time' => '22:00:00',
    ]);

    Department::create([
      'id' => Str::uuid(),
      'name' => 'HRD',
      'min_check_in_time' => '06:00:00',
      'max_check_in_time' => '16:00:00',
      'min_check_out_time' => '16:00:00',
      'max_check_out_time' => '22:30:00',
    ]);
  }
}
