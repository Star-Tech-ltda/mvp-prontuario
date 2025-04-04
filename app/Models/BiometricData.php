<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiometricData extends Model
{
    protected $fillable = [
      'evolution_id',
      'height',
      'weight',
      'age',
      'systolic_pressure',
      'diastolic_pressure',
      'heart_rate',
      'respiratory_rate',
      'oxygen_saturation',
      'temperature',
    ];
    public function evolution(): BelongsTo
    {
        return $this->belongsTo(Evolution::class);
    }
}
