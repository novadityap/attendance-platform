<?php

use App\Models\Role;
use App\Models\Employee;
use Firebase\JWT\JWT;
use App\Models\Attendance;
use App\Models\AttendanceHistory;
use App\Models\Department;
use App\Models\RefreshToken;
use Illuminate\Support\Carbon;
use App\Helpers\CloudinaryHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
  ->beforeEach(function () {
    Artisan::call('migrate:refresh --seed');
  })
  ->beforeEach(function () {
    test()->validUUID = Str::uuid()->toString();
    test()->testAvatarPath = base_path('tests/uploads/avatars/test-avatar.jpg');
  })
  ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
  return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getTestRefreshToken(): ?RefreshToken
{
  $employee = getTestEmployee();

  return RefreshToken::where('employee_id', $employee->id)->first();
}

function createTestRefreshToken(): RefreshToken
{
  $employee = getTestEmployee();
  $token = JWT::encode(
    [
      'sub' => $employee->id,
      'role' => $employee->role->name,
      'iat' => now()->timestamp,
      'exp' => now()->addMinutes((int) config('auth.jwt_refresh_expires'))->timestamp
    ],
    config('auth.jwt_refresh_secret'),
    config('auth.jwt_algo')
  );

  return RefreshToken::create([
    'token' => $token,
    'employee_id' => $employee->id,
    'expires_at' => Carbon::now()->addMinutes(5),
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
  $now = Carbon::now('Asia/Jakarta');

  return Department::create(array_merge([
    'name' => 'test',
    'min_check_in_time' => $now->copy()->subMinute()->format('H:i'),
    'min_check_out_time' => $now->copy()->subMinute()->format('H:i'),
    'max_check_in_time' => $now->copy()->addHours(8)->format('H:i'),
    'max_check_out_time' => $now->copy()->addHours(8)->format('H:i'),
  ], $fields));
}

function createManyTestDepartments(): void
{
  $now = Carbon::now('Asia/Jakarta');

  foreach (range(0, 14) as $i) {
    Department::create([
      'name' => "test{$i}",
      'min_check_in_time' => $now->copy()->subMinute()->format('H:i'),
      'min_check_out_time' => $now->copy()->subMinute()->format('H:i'),
      'max_check_in_time' => $now->copy()->addHours(8)->format('H:i'),
      'max_check_out_time' => $now->copy()->addHours(8)->format('H:i'),
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

function checkFileExists(string|array $url): bool
{
  try {
    $urls = is_array($url) ? $url : [$url];

    foreach ($urls as $url) {
      $publicId = CloudinaryHelper::extractPublicId($url);

      if (!$publicId)
        return false;

      if (!Storage::exists($publicId))
        return false;
    }

    return true;

  } catch (\Exception $e) {
    return false;
  }
}

function removeTestFile(string|array $url): void
{
  $urls = is_array($url) ? $url : [$url];

  foreach ($urls as $url) {
    Storage::delete(CloudinaryHelper::extractPublicId($url));
  }
}