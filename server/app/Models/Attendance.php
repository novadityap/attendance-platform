<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Attendance extends Model
{
  use HasUuids;

  public $incrementing = false;
  protected $keyType = 'string';
  protected $guarded = [];
  protected $casts = [
    'check_in' => 'datetime',
    'check_out' => 'datetime',
  ];

  public function histories(): HasMany
  {
    return $this->hasMany(AttendanceHistory::class);
  }

  public function employee(): BelongsTo
  {
    return $this->belongsTo(Employee::class);
  }
}
