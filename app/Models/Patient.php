<?php

namespace App\Models;

use App\Enums\MaritalStatus;
use App\Enums\Sex;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'birth_date',
      'cpf',
      'marital_status',
      'sex',
      'responsible',
      'movement',
      'phone',
      'address',
      'internment_reason',
      'internment_date',
      'complaints',
      'internment_time',
      'internment_location',
      'bed',
      'diagnosis',
      'created_by'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'sex'=>Sex::class,
        'marital_status'=>MaritalStatus::class,
    ];

    public function evolutions(): hasMany
    {
        return $this->hasMany(Evolution::class);
    }


    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    protected static function booted(): void
    {
        static::saving(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updated(function ($patient) {
            $evolutions = $patient->evolutions;

            foreach ($evolutions as $evolution) {
                $biometricData = $evolution->biometricData;


                if ($biometricData && $patient->birthdate) {
                    $age = Carbon::parse($patient->birthdate)->age;

                    if ((int) $biometricData->age !== (int) $age) {
                        $biometricData->update(['age' => $age]);
                    }

                }

                $evolution->load([
                    'assessmentOptions.assessmentGroup',
                    'patient',
                    'calculatedMetrics',
                ]);

                $evolution->updateQuietly([
                    'evolution_text' => $evolution->generateEvolutionText($biometricData),
                ]);
            }
        });



    }
}
