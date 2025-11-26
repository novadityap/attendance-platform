<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AttendanceHistory extends Model
{
  use HasUuids;

  public $incrementing = false;
  protected $keyType = 'string';
  protected $guarded = [];
  protected $casts = [
    'date_attendance' => 'datetime'
  ];

  public function attendance(): BelongsTo
  {
    return $this->belongsTo(Attendance::class);
  }

  public function employee(): BelongsTo
  {
    return $this->belongsTo(Employee::class);
  }
}
