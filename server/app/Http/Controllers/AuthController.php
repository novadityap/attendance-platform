<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Employee;
use App\Mail\ResetPassword;
use Illuminate\Support\Str;
use App\Models\RefreshToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\Auth\SigninRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordActionRequest;

class AuthController extends Controller
{
  public function signin(SigninRequest $request): JsonResponse
  {
    $employee = Employee::with('role')
      ->where('email', $request->input('email'))
      ->first();

    if (!$employee || !is_string($employee->password) || !Hash::check($request->input('password'), $employee->password)) {
      abort(401, 'Email or password is invalid');
    }

    $payload = [
      'sub' => $employee->id,
      'role' => $employee->role->name
    ];

    $token = JWT::encode(
      array_merge($payload, [
        'iat' => now()->timestamp,
        'exp' => now()->addMinutes((int) config('auth.jwt_expires'))->timestamp
      ]),
      config('auth.jwt_secret'),
      config('auth.jwt_algo')
    );

    $refreshToken = JWT::encode(
      array_merge($payload, [
        'iat' => now()->timestamp,
        'exp' => now()->addDays((int) config('auth.jwt_refresh_expires'))->timestamp
      ]),
      config('auth.jwt_refresh_secret'),
      config('auth.jwt_algo')
    );

    $decodedRefreshToken = JWT::decode($refreshToken, new Key(config('auth.jwt_refresh_secret'), config('auth.jwt_algo')));

    RefreshToken::create([
      'token' => $refreshToken,
      'employee_id' => $employee->id,
      'expires_at' => Carbon::createFromTimestamp($decodedRefreshToken->exp)
    ]);

    $employee->token = $token;

    Log::info('Signed in successfully');
    return response()
      ->json([
        'code' => 200,
        'message' => 'Signed in successfully',
        'data' => new EmployeeResource($employee),
      ], 200)
      ->cookie(
        'refreshToken',
        $refreshToken,
        config('auth.jwt_refresh_expires'),
        '/',
        null,
        false,
        true,
      );
  }

  public function signout(Request $request): Response
  {
    $refreshToken = $request->cookie('refreshToken');

    if (!$refreshToken)
      abort(401, 'Refresh token is not provided');

    try {
      JWT::decode($refreshToken, new Key(config('auth.jwt_refresh_secret'), config('auth.jwt_algo')));
    } catch (\Throwable $e) {
      if ($e instanceof ExpiredException) {
        abort(401, 'Refresh token has expired');
      } else {
        abort(401, 'Refresh token is invalid');
      }
    }

    $deletedToken = RefreshToken::where('token', $refreshToken)->delete();
    if (!$deletedToken)
      abort(401, 'Refresh token is invalid');

    Log::info('Signed out successfully');
    return response()->noContent()->withoutCookie('refreshToken');
  }

  public function refreshToken(Request $request): JsonResponse
  {
    $refreshToken = $request->cookie('refreshToken');

    if (!$refreshToken)
      abort(401, 'Refresh token is not provided');

    try {
      JWT::decode($refreshToken, new Key(config('auth.jwt_refresh_secret'), config('auth.jwt_algo')));
    } catch (\Throwable $e) {
      if ($e instanceof ExpiredException) {
        abort(401, 'Refresh token has expired');
      } else {
        abort(401, 'Refresh token is invalid');
      }
    }

    $storedToken = RefreshToken::with('employee.role')
      ->where('token', $refreshToken)
      ->where('expires_at', '>', Carbon::now())
      ->first();

    if (!$storedToken)
      abort(401, 'Refresh token is invalid');

    $newToken = JWT::encode(
      [
        'sub' => $storedToken->employee->id,
        'role' => $storedToken->employee->role->name,
        'email' => $storedToken->employee->email,
        'iat' => now()->timestamp,
        'exp' => now()->addMinutes((int) config('auth.jwt_expires'))->timestamp
      ],
      config('auth.jwt_secret'),
      config('auth.jwt_algo')
    );

    return response()->json([
      'code' => 200,
      'message' => 'Token refreshed successfully',
      'data' => ['token' => $newToken]
    ], 200);
  }

  public function requestResetPassword(ResetPasswordRequest $request): JsonResponse
  {
    $employee = Employee::where('email', $request->input('email'))
      ->first();

    if (!$employee)
      throw ValidationException::withMessages([
        'email' => ['Email is not registered']
      ]);

    $employee->update([
      'reset_token' => Str::random(32),
      'reset_token_expires' => Carbon::now()->addHour()
    ]);

    $url = config('app.client_url') . "/reset-password/{$employee->reset_token}";

    Mail::to($employee->email)->send(new ResetPassword($employee, $url));

    Log::info('Reset password request sent successfully');
    return response()->json([
      'code' => 200,
      'message' => 'Please check your email to reset your password'
    ], 200);
  }

  public function resetPassword(ResetPasswordActionRequest $request): JsonResponse
  {
    $employee = Employee::where('reset_token', $request->resetToken)
      ->where('reset_token_expires', '>', Carbon::now())
      ->first();

    if (!$employee)
      abort(401, 'Reset token is invalid or has expired');

    $employee->update([
      'password' => Hash::make($request->input('new_password')),
      'reset_token' => null,
      'reset_token_expires' => null
    ]);

    Log::info('Password reset successfully');
    return response()->json([
      'code' => 200,
      'message' => 'Password reset successfully'
    ], 200);
  }
}
