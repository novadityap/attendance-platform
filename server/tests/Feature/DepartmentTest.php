<?php

describe('GET /api/departments', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');
    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->getJson('/api/departments', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return all departments', function () {
    createManyTestDepartments();

    $result = $this->getJson('/api/departments', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Departments retrieved successfully');
    expect($result->json('data'))->not()->toBeNull();
  });
});

describe('GET /api/departments/search', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createManyTestDepartments();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');

    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->getJson('/api/departments/search', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return a list of departments with default pagination', function () {
    $result = $this->getJson('/api/departments/search', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Departments retrieved successfully');
    expect($result->json('data'))->toHaveCount(10);
    expect($result->json('meta.pageSize'))->toBe(10);
    expect($result->json('meta.totalItems'))->toBeGreaterThanOrEqual(15);
    expect($result->json('meta.currentPage'))->toBe(1);
    expect($result->json('meta.totalPages'))->toBeGreaterThanOrEqual(2);
  });

  it('should return a list of departments with custom search', function () {
    $result = $this->getJson('/api/departments/search?q=test10', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Departments retrieved successfully');
    expect($result->json('data'))->toHaveCount(1);
    expect($result->json('meta.pageSize'))->toBe(10);
    expect($result->json('meta.totalItems'))->toBe(1);
    expect($result->json('meta.currentPage'))->toBe(1);
    expect($result->json('meta.totalPages'))->toBe(1);
  });
});

describe('GET /api/departments/{departmentId}', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if department is not found', function () {
    $result = $this->getJson('/api/departments/' . test()->validUUID, [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Department not found');
  });

  it('should return a department if department id is valid', function () {
    createTestDepartment();
    $department = getTestDepartment();

    $result = $this->getJson("/api/departments/{$department->id}", [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Department retrieved successfully');
    expect($result->json('data'))->not()->toBeNull();
  });
});

describe('POST /api/departments', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');

    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->postJson('/api/departments', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if input data is invalid', function () {
    $result = $this->postJson('/api/departments', [
      'name' => '',
    ], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.name'))->toBeArray();
  });

  it('should return an error if name already in use', function () {
    createTestDepartment();

    $result = $this->postJson('/api/departments', [
      'name' => 'test',
    ], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.name'))->toBeArray();
  });

  it('should create a department if input data is valid', function () {
    $result = $this->postJson('/api/departments', [
      'name' => 'test',
      'minCheckInTime' => '08:00',
      'minCheckOutTime' => '15:00',
      'maxCheckInTime' => '10:00',
      'maxCheckOutTime' => '18:00'
    ], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(201);
    expect($result->json('message'))->toBe('Department created successfully');
  });
});

describe('PUT /api/departments/{departmentId}', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createTestDepartment();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $department = getTestDepartment();
    $employeeRole = getTestRole('employee');

    updateTestEmployee(['role_id' => $employeeRole->id]);
    createAccessToken();

    $result = $this->putJson("/api/departments/{$department->id}", [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if name already in use', function () {
    createTestDepartment(['name' => 'test1']);

    $department = getTestDepartment();
    $result = $this->putJson("/api/departments/{$department->id}", [
      'name' => 'test1',
    ], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
    expect($result->json('errors.name'))->toBeArray();
  });

  it('should return an error if employee is not found', function () {
    $result = $this->putJson('/api/departments/' . test()->validUUID, [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Department not found');
  });

  it('should update department if input data is valid', function () {
    $department = getTestDepartment();

    $result = $this->putJson("/api/departments/{$department->id}", [
      'name' => 'test1',
    ], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Department updated successfully');
    expect($result->json('data.name'))->toBe('test1');
  });
});

describe('DELETE /api/departments/{departmentId}', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createTestDepartment();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $department = getTestDepartment();
    $employeeRole = getTestRole('employee');

    updateTestEmployee(['role_id' => $employeeRole->id]);
    createAccessToken();

    $result = $this->deleteJson("/api/departments/{$department->id}", [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if department is not found', function () {
    $result = $this->deleteJson('/api/departments/' . test()->validUUID, [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Department not found');
  });

  it('should delete department if department id is valid', function () {
    $department = getTestDepartment();

    $result = $this->deleteJson("/api/departments/{$department->id}", [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Department deleted successfully');
  });
});


