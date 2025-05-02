<?php

namespace App\Models;

use App\Enums\MaritalStatus;
use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    }
}
