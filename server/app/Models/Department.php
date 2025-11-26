<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Employee;

class Department extends Model
{
  use HasUuids;

  public $incrementing = false;
  protected $keyType = 'string';
  protected $guarded = [];
  protected $casts = [
    'max_check_in_time' => 'datetime:H:i',
    'max_check_out_time' => 'datetime:H:i'
  ];

  public function employees(): HasMany
  {
    return $this->hasMany(Employee::class);
  }
}
