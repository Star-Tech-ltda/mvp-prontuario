<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'tittle',
        'image',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
