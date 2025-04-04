<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Evolution extends Model
{
    protected $fillable = [
        'patient_id',
        'observation',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function calculatedMetrics(): HasMany
    {
        return $this->hasMany(CalculatedMetric::class);
    }

    public function biometricData(): HasOne
    {
        return $this->hasOne(BiometricData::class);
    }
}
