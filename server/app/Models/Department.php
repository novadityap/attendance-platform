<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
  use HasUuids;

  public $incrementing = false;
  protected $keyType = 'string';
  protected $guarded = [];

  protected function minCheckInTime(): Attribute
  {
    return Attribute::make(
      get: fn(string $value) => substr($value, 0, 5)
    );
  }

  protected function minCheckOutTime(): Attribute
  {
    return Attribute::make(
      get: fn(string $value) => substr($value, 0, 5)
    );
  }

  protected function maxCheckInTime(): Attribute
  {
    return Attribute::make(
      get: fn(string $value) => substr($value, 0, 5)
    );
  }

  protected function maxCheckOutTime(): Attribute
  {
    return Attribute::make(
      get: fn(string $value) => substr($value, 0, 5)
    );
  }

  public function employees(): HasMany
  {
    return $this->hasMany(Employee::class);
  }
}
