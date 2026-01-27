<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Mail\ResetPassword;

describe('POST /api/auth/signin', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if input data is invalid', function () {
    $result = $this->postJson('/api/auth/signin', [
      'email' => '',
      'password' => '',
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('errors.email'))->toBeArray();
    expect($result->json('errors.password'))->toBeArray();
  });

  it('should return an error if credentials are invalid', function () {
    $result = $this->postJson('/api/auth/signin', [
      'email' => 'test@me.co',
      'password' => 'test12',
    ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Email or password is invalid');
  });

  it('should sign in if credentials are valid', function () {
    $result = $this->postJson('/api/auth/signin', [
      'email' => 'test@me.com',
      'password' => 'test123',
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('data.token'))->toBeString();
    expect($result->json('data.name'))->toBe('test');
    expect($result->json('data.email'))->toBe('test@me.com');
    expect($result->json('data.role'))->not->toBeNull();

    $decoded = JWT::decode(
      $result->json('data.token'),
      new Key(config('auth.jwt_secret'), config('auth.jwt_algo'))
    );

    expect($decoded->sub)->not->toBeNull();
    expect($decoded->role)->not->toBeNull();
    expect($result->headers->get('set-cookie'))->toContain('refreshToken=');
  });
});

describe('POST /api/auth/signout', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestRefreshTokens();
    removeAllTestDepartments();
  });

  it('should return an error if refresh token is not provided', function () {
    $result = $this->postJson('/api/auth/signout', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Refresh token is not provided');
  });

  it('should return an error if refresh token is not found in the database', function () {
    $result = $this->withCookie('refreshToken', test()->validUUID)
      ->post('/api/auth/signout', [], [
        'Authorization' => 'Bearer ' . test()->accessToken,
      ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Refresh token is invalid');
  });

  it('should sign out if refresh token is valid', function () {
    $refreshToken = createTestRefreshToken();
    $result = $this->withUnencryptedCookie('refreshToken', $refreshToken->token)->post('/api/auth/signout', [], [
      'Authorization' => 'Bearer ' . test()->accessToken
    ]);

    expect($result->status())->toBe(204);
  });
});

describe('POST /api/auth/refresh-token', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
    createAccessToken();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestRefreshTokens();
    removeAllTestDepartments();
  });

  it('should return an error if refresh token is not provided', function () {
    $result = $this->post('/api/auth/refresh-token', [], [
      'Authorization' => 'Bearer ' . test()->accessToken,
    ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Refresh token is not provided');
  });

  it('should return an error if refresh token is not found in the database', function () {
    $result = $this
      ->withCookie('refreshToken', test()->validUUID)
      ->post('/api/auth/refresh-token', [], [
        'Authorization' => 'Bearer ' . test()->accessToken,
      ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Refresh token is invalid');
  });

  it('should refresh token if refresh token is valid', function () {
    createTestRefreshToken();
    $refreshToken = getTestRefreshToken();

    $result = $this
      ->withUnencryptedCookie('refreshToken', $refreshToken->token)
      ->post('/api/auth/refresh-token', [], [
        'Authorization' => 'Bearer ' . test()->accessToken,
      ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Token refreshed successfully');

    $decoded = JWT::decode(
      $result->json('data.token'),
      new Key(config('auth.jwt_secret'), config('auth.jwt_algo'))
    );

    expect($decoded->sub)->not->toBeEmpty();
    expect($decoded->role)->not->toBeEmpty();
  });
});

describe('POST /api/auth/request-reset-password', function () {
  beforeEach(function () {
    Mail::fake();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
    Mail::clearResolvedInstances();
  });

  it('should return an error if input data is invalid', function () {
    $result = $this->postJson('/api/auth/request-reset-password', [
      'email' => '',
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('errors.email'))->toBeArray();
  });

  it('should not send reset password email if employee is not registered', function () {
    $result = $this->postJson('/api/auth/request-reset-password', [
      'email' => 'test1@me.com',
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('message'))->toBe('Validation errors');
  });

  it('should send reset password email if employee is registered', function () {
    createTestDepartment();
    createTestEmployee();

    $result = $this->postJson('/api/auth/request-reset-password', [
      'email' => 'test@me.com',
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Please check your email to reset your password');
    Mail::assertSent(ResetPassword::class, 1);
  });
});

describe('POST /api/auth/reset-password/{token}', function () {
  beforeEach(function () {
    createTestDepartment();
    createTestEmployee();
  });

  afterEach(function () {
    removeAllTestEmployees();
    removeAllTestDepartments();
  });

  it('should return an error if input data is invalid', function () {
    $result = $this->postJson('/api/auth/reset-password/invalid-token', [
      'newPassword' => '',
    ]);

    expect($result->status())->toBe(400);
    expect($result->json('errors.newPassword'))->toBeArray();
  });

  it('should return an error if reset token has expired', function () {
    updateTestEmployee([
      'reset_token' => '123',
      'reset_token_expires' => now()->subMinutes(5),
    ]);

    $result = $this->postJson('/api/auth/reset-password/123', [
      'newPassword' => 'test123',
    ]);

    expect($result->status())->toBe(401);
    expect($result->json('message'))->toBe('Reset token is invalid or has expired');
  });

  it('should reset password if reset token is valid', function () {
    updateTestEmployee([
      'reset_token' => '123',
      'reset_token_expires' => now()->addMinutes(5),
    ]);

    $result = $this->postJson('/api/auth/reset-password/123', [
      'newPassword' => 'test123',
    ]);

    expect($result->status())->toBe(200);
    expect($result->json('message'))->toBe('Password reset successfully');
  });
});

