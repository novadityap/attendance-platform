<?php

describe('GET /api/dashboard', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestRoles();
    removeAllTestDepartments();
  });

  it('should return an error if employee does not have permission', function () {
    $role = getTestRole('employee');
    updateTestEmployee(['role_id' => $role->id]);
    createAccessToken();

    $result = $this->getJson('/api/dashboard', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return dashboard statistics data', function () {
    createManyTestDepartments();
    createManyTestRoles();
    createManyTestEmployees();
    createTestDepartment();
    createTestEmployee();
    createManyTestAttendances();

    $result = $this->getJson('/api/dashboard', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Statistics data retrieved successfully');
    expect($result->json('data.totalDepartments'))->toBeGreaterThanOrEqual(15);
    expect($result->json('data.totalRoles'))->toBeGreaterThanOrEqual(15);
    expect($result->json('data.totalEmployees'))->toBeGreaterThanOrEqual(15);
    expect($result->json('data.recentAttendances'))->toHaveCount(5);
  });
});
