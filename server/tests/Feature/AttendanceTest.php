<?php

describe('GET /api/attendances/search', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createManyTestAttendances();
  });

  afterEach(function () {
    removeAllTestAttendances();
    removeAllTestEmployees();
  });

  it('should return a list of attendances with default pagination', function () {
    $result = $this->getJson('/api/attendances/search', [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Attendances retrieved successfully');
    expect(count($result->json('data')))->toBe(10);
    expect($result->json('meta.pageSize'))->toBe(10);
    expect($result->json('meta.totalItems'))->toBeGreaterThanOrEqual(15);
    expect($result->json('meta.currentPage'))->toBe(1);
    expect($result->json('meta.totalPages'))->toBeGreaterThanOrEqual(2);
  });
});

describe('GET /api/attendances/today', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createTestAttendance();
  });

  afterEach(function () {
    removeAllTestAttendances();
    removeAllTestEmployees();
  });

  it('should return a attendance', function () {
    $result = $this->getJson("/api/attendances/today", [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Attendance retrieved successfully');
    expect($result->json('data'))->toBeArray();
  });
});

describe('POST /api/attendances/checkIn', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestAttendances();
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee already checked in today', function () {
    createTestAttendance();

    $result = $this->postJson('/api/attendances/checkIn', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(409);
    expect($result->json('message'))->toBe('Already checked in today');
  });

  it('should check in successfully', function () {
    $result = $this->postJson('/api/attendances/checkIn', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(201);
    expect($result->json('message'))->toBe('Check in successfully');
  });
});

describe('PUT /api/attendances/checkOut', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestAttendances();
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if employee has not checked in today', function () {
    $result = $this->putJson('/api/attendances/checkOut', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('There is no check in today');
  });

  it('should check out successfully', function () {
    createTestAttendance(['check_out' => null]);

    $result = $this->putJson('/api/attendances/checkOut', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Check out successfully');
  });
});

describe('DELETE /api/attendances/{attendance}', function () {
  beforeEach(function () {
    createTestEmployee();
    createAccessToken();
    createTestAttendance();
  });

  afterEach(function () {
    removeAllTestAttendances();
    removeAllTestEmployees();
  });

  it('should return an error if employee does not have permission', function () {
    $employeeRole = getTestRole('employee');
    updateTestEmployee(['role_id' => $employeeRole->id]);
    createAccessToken();

    $attendance = getTestAttendance();

    $result = $this->deleteJson("/api/attendances/{$attendance->id}", [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(403);
    expect($result->json('message'))->toBe('Permission denied');
  });

  it('should return an error if attendance is not found', function () {
    $result = $this->deleteJson('/api/attendances/' . test()->validUUID, [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(404);
    expect($result->json('message'))->toBe('Attendance not found');
  });

  it('should delete attendance if atendance id is valid', function () {
    $attendance = getTestAttendance();

    $result = $this->deleteJson("/api/attendances/{$attendance->id}", [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Attendance deleted successfully');
  });
});

