<?php

use App\Models\Role;
use Firebase\JWT\JWT;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Models\RefreshToken;
use Illuminate\Support\Carbon;
use App\Models\AttendanceHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

pest()->extend(Tests\TestCase::class)
  ->use(DatabaseTransactions::class)
  ->beforeEach(function () {
    $this->validUUID = Str::uuid()->toString();
  })
  ->in('Feature');


function getTestRefreshToken(): ?RefreshToken
{
  $employee = getTestEmployee();

  return RefreshToken::where('employee_id', $employee->id)->first();
}

function createTestRefreshToken(): RefreshToken
{
  $employee = getTestEmployee();
  $issuedAt = Carbon::now();

  $token = JWT::encode(
    [
      'sub' => $employee->id,
      'role' => $employee->role->name,
      'iat' => $issuedAt->timestamp,
      'exp' => $issuedAt
        ->copy()
        ->addMinutes((int) config('auth.jwt_refresh_expires'))
        ->timestamp
    ],
    config('auth.jwt_refresh_secret'),
    config('auth.jwt_algo')
  );

  return RefreshToken::create([
    'token' => $token,
    'employee_id' => $employee->id,
    'expires_at' => $issuedAt
      ->copy()
      ->addMinutes((int) config('auth.jwt_refresh_expires')),
  ]);
}

function removeAllTestRefreshTokens(): void
{
  $testEmployees = Employee::where('name', 'ilike', 'test%')->pluck('id');

  RefreshToken::whereIn('employee_id', $testEmployees)->delete();
}

function getTestEmployee(string $name = 'test'): ?Employee
{
  return Employee::with('role')->where('name', $name)->first();
}

function createTestEmployee(array $fields = []): Employee
{
  $role = getTestRole('admin');
  $department = getTestDepartment();

  return Employee::create(array_merge([
    'name' => 'test',
    'email' => 'test@me.com',
    'password' => Hash::make('test123'),
    'department_id' => $department->id,
    'role_id' => $role->id,
  ], $fields));
}

function createManyTestEmployees(): void
{
  $role = getTestRole('admin');
  $department = getTestDepartment('IT');

  foreach (range(0, 14) as $i) {
    Employee::create([
      'name' => "test{$i}",
      'email' => "test{$i}@email.com",
      'password' => Hash::make('test123'),
      'role_id' => $role->id,
      'department_id' => $department->id,
      'avatar' => config('app.default_avatar_url'),
    ]);
  }
}

function updateTestEmployee(array $fields = []): ?Employee
{
  $employee = getTestEmployee();
  $employee->update($fields);
  return $employee->fresh();
}

function removeAllTestEmployees(): void
{
  Employee::where('name', 'ilike', 'test%')->delete();
}

function getTestDepartment(string $name = 'test'): ?Department
{
  return Department::where('name', $name)->first();
}

function createTestDepartment(array $fields = []): Department
{
  return Department::create(array_merge([
    'name' => 'test',
    'min_check_in_time' => '06:00',
    'max_check_in_time' => '16:00',
    'min_check_out_time' => '16:00',
    'max_check_out_time' => '23:00',
  ], $fields));
}

function createManyTestDepartments(): void
{
  foreach (range(0, 14) as $i) {
    Department::create([
      'name' => "test{$i}",
      'min_check_in_time' => '06:00',
      'max_check_in_time' => '16:00',
      'min_check_out_time' => '16:00',
      'max_check_out_time' => '23:00',
    ]);
  }
}

function removeAllTestDepartments(): void
{
  Department::where('name', 'ilike', 'test%')->delete();
}

function getTestAttendance(): ?Attendance
{
  $employee = getTestEmployee();
  return Attendance::where('employee_id', $employee->id)->first();
}

function createTestAttendance(array $fields = []): Attendance
{
  $employee = getTestEmployee('test');

  return Attendance::create(array_merge([
    'employee_id' => $employee->id,
    'check_in' => Carbon::today()->setHour(7)->setMinute(0)->setSecond(0),
    'check_out' => Carbon::today()->setHour(15)->setMinute(0)->setSecond(0),
  ], $fields));
}

function createManyTestAttendances(): void
{
  $employee = getTestEmployee('test');

  foreach (range(0, 14) as $i) {
    Attendance::create([
      'employee_id' => $employee->id,
      'check_in' => Carbon::today()->setHour(7)->setMinute(0)->setSecond(0),
      'check_out' => Carbon::today()->setHour(15)->setMinute(0)->setSecond(0),
    ]);
  }
}

function removeAllTestAttendances(): void
{
  $employee = getTestEmployee();

  Attendance::where('employee_id', $employee->id)->delete();
}

function getTestRole(string $name = 'test'): ?Role
{
  return Role::where('name', $name)->first();
}

function createTestRole(array $fields = []): Role
{
  return Role::create(array_merge(['name' => 'test'], $fields));
}

function createManyTestRoles(): void
{
  foreach (range(0, 14) as $i) {
    Role::create(['name' => "test{$i}"]);
  }
}

function removeAllTestRoles(): void
{
  Role::where('name', 'ilike', 'test%')->delete();
}

function createAccessToken(): void
{
  $employee = getTestEmployee();
  $token = JWT::encode(
    [
      'sub' => $employee->id,
      'role' => $employee->role->name,
      'iat' => now()->timestamp,
      'exp' => now()->addMinutes((int) config('auth.jwt_expires'))->timestamp
    ],
    config('auth.jwt_secret'),
    config('auth.jwt_algo')
  );

  test()->accessToken = $token;
}

