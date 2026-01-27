<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

describe('GET /api/employees/search', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
    createManyTestEmployees();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');

    updateTestEmployee([
      'role_id' => $role->id,
    ]);
    createAccessToken();

    $result = $this->getJson('/api/employees/search', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return a list of employees with default pagination', function () {
    $result = $this->getJson('/api/employees/search', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Employees retrieved successfully');
    expect($result->json('data'))->toHaveCount(10);
    expect($result->json('meta.pageSize'))->toBe(10);
    expect($result->json('meta.totalItems'))->toBeGreaterThanOrEqual(15);
    expect($result->json('meta.currentPage'))->toBe(1);
    expect($result->json('meta.totalPages'))->toBeGreaterThanOrEqual(2);
  });

  it('should return a list of employees with custom search', function () {
    $result = $this->getJson('/api/employees/search?q=test10', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Employees retrieved successfully');
    expect($result->json('data'))->toHaveCount(1);
    expect($result->json('meta.pageSize'))->toBe(10);
    expect($result->json('meta.totalItems'))->toBe(1);
    expect($result->json('meta.currentPage'))->toBe(1);
    expect($result->json('meta.totalPages'))->toBe(1);
  });
});

describe('GET /api/employees/{employeeId}', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee is not owned by current employee', function () {
    $role = getTestRole('employee');
    $department = getTestDepartment();

    $otherEmployee = createTestEmployee([
      'name' => 'test1',
      'email' => 'test1@me.com',
      'role_id' => $role->id,
      'department_id' => $department->id
    ]);

    updateTestEmployee([
      'role_id' => $role->id,
    ]);
    createAccessToken();

    $result = $this->getJson("/api/employees/{$otherEmployee->id}", [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if employee is not found', function () {
    $result = $this->getJson('/api/employees/' . test()->validUUID, [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Employee not found');
  });

  it('should return a employee for employee id is valid', function () {
    $employee = getTestEmployee();

    $result = $this->getJson("/api/employees/{$employee->id}", [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Employee retrieved successfully');
    expect($result->json('data'))->not()->toBeNull();
  });
});

describe('POST /api/employees', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');

    updateTestEmployee([
      'role_id' => $role->id,
    ]);
    createAccessToken();

    $result = $this->postJson('/api/employees', [
      'name' => 'test1',
      'email' => 'test1@me.com',
      'password' => 'password',
      'roleId' => $role->id,
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if input data is invalid', function () {
    $result = $this->postJson('/api/employees', [
      'name' => '',
      'email' => '',
      'password' => '',
      'roleId' => '',
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.name'))->toBeArray();
    expect($result->json('errors.email'))->toBeArray();
    expect($result->json('errors.password'))->toBeArray();
    expect($result->json('errors.roleId'))->toBeArray();
  });

  it('should return an error if email already in use', function () {
    createTestEmployee([
      'name' => 'test1',
      'email' => 'test1@me.com',
    ]);

    $role = getTestRole('admin');
    $department = getTestDepartment();

    $result = $this->postJson('/api/employees', [
      'name' => 'test1',
      'email' => 'test@me.com',
      'password' => 'test123',
      'roleId' => $role->id,
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.email'))->toBeArray();
  });

  it('should return an error if role is invalid', function () {
    $department = getTestDepartment('IT');
    $result = $this->postJson('/api/employees', [
      'name' => 'test',
      'email' => 'test@me.com',
      'password' => 'test123',
      'roleId' => 'invalid-id',
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.roleId'))->toBeArray();
  });

  it('should create a employee if input data is valid', function () {
    $role = getTestRole('admin');
    $department = getTestDepartment('IT');

    $result = $this->postJson('/api/employees', [
      'name' => 'test1',
      'email' => 'test1@me.com',
      'password' => 'test123',
      'roleId' => $role->id,
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(201);
    expect($result->json('message'))->toBe('Employee created successfully');
  });
});

describe('PUT /api/employees/{employeeId}', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee is not owned by current employee', function () {
    $role = getTestRole('employee');
    $department = getTestDepartment();
    $otherEmployee = createTestEmployee([
      'name' => 'test1',
      'email' => 'test1@me.com',
      'role_id' => $role->id,
      'department_id' => $department->id
    ]);

    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->putJson("/api/employees/{$otherEmployee->id}", [], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if employee is not found', function () {
    $result = $this->putJson("/api/employees/" . test()->validUUID, [], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Employee not found');
  });

  it('should return an error if input data is invalid', function () {
    $employee = getTestEmployee();

    $result = $this->putJson("/api/employees/{$employee->id}", [
      'email' => '',
      'name' => '',
    ], [
      'Authorization' => "Bearer " . test()->accessToken
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.name'))->toBeArray();
    expect($result->json('errors.email'))->toBeArray();
  });

  it('should return an error if role is invalid', function () {
    $employee = getTestEmployee();
    $department = getTestDepartment('IT');

    $result = $this->putJson("/api/employees/{$employee->id}", [
      'email' => 'test1@me.com',
      'name' => 'test1',
      'roleId' => 'invalid-id',
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.roleId'))->toBeArray();
  });

  it('should return an error if email is already in use', function () {
    createTestEmployee([
      'name' => 'test1',
      'email' => 'test1@me.com',
    ]);

    $role = getTestRole('admin');
    $department = getTestDepartment();
    $employee = getTestEmployee();

    $result = $this->putJson("/api/employees/{$employee->id}", [
      'email' => 'test1@me.com',
      'roleId' => $role->id,
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.email'))->toBeArray();
  });

  it('should update employee', function () {
    $role = getTestRole('admin');
    $department = getTestDepartment('IT');
    $employee = getTestEmployee();

    $result = $this->putJson("/api/employees/{$employee->id}", [
      'email' => 'test1@me.com',
      'name' => 'test1',
      'roleId' => $role->id,
      'departmentId' => $department->id
    ], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Employee updated successfully');
    expect($result->json('data.email'))->toBe('test1@me.com');
    expect($result->json('data.name'))->toBe('test1');
    expect($result->json('data.roleId'))->toBe($role->id);
  });
});

describe('DELETE /api/employees/{employeeId}', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee is not owned by current employee', function () {
    $role = getTestRole('employee');
    $department = getTestDepartment();
    $otherEmployee = createTestEmployee([
      'name' => 'test1',
      'email' => 'test1@me.com',
      'role_id' => $role->id,
      'department_id' => $department->id
    ]);

    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->deleteJson("/api/employees/{$otherEmployee->id}", [], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if employee is not found', function () {
    $result = $this->deleteJson("/api/employees/" . test()->validUUID, [], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Employee not found');
  });

  it('should delete employee', function () {
    $employee = getTestEmployee();

    $result = $this->deleteJson("/api/employees/{$employee->id}", [], [
      'Authorization' => "Bearer " . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Employee deleted successfully');
  });
});
