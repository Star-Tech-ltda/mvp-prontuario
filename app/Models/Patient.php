<?php

namespace App\Models;

use App\Enums\Sex;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
      'name',
      'birth_date',
      'cpf',
      'sex',
      'phone',
      'address',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'sex'=>Sex::class,
    ];

    public function evolutions(): hasMany
    {
        return $this->hasMany(Evolution::class);
    }
}
