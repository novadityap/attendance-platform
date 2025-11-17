<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
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
