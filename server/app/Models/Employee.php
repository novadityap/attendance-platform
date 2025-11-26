<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
  use Notifiable, HasUuids;

  public $incrementing = false;
  protected $keyType = 'string';
  protected $guarded = [];
  protected $hidden = ['password'];

  protected function casts(): array
  {
    return [
      'password' => 'hashed',
    ];
  }

  public function getJWTIdentifier(): mixed
  {
    return $this->getKey();
  }

  public function getJWTCustomClaims(): array
  {
    return [
      'role' => $this->role->name
    ];
  }

  protected function name(): Attribute
  {
    return Attribute::make(
      get: fn(string $value) =>
      collect(explode(' ', strtolower($value)))
        ->filter(fn($word) => trim($word) !== '')
        ->map(fn($word) => ucfirst($word))
        ->join(' ')
    );
  }

  public function department(): BelongsTo
  {
    return $this->belongsTo(Department::class);
  }

  public function role(): BelongsTo
  {
    return $this->belongsTo(Role::class);
  }

  public function refreshToken(): HasMany
  {
    return $this->hasMany(RefreshToken::class);
  }

  public function attendances(): HasMany
  {
    return $this->hasMany(Attendance::class);
  }

  public function histories(): HasMany
  {
    return $this->hasMany(AttendanceHistory::class);
  }
}
