<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentGroup extends Model
{
    protected $fillable = [
        'name','slug'
    ];

    public function assessmentOptions(): HasMany
    {
        return $this->hasMany(AssessmentOption::class);
    }
}
