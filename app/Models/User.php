<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasAvatar
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function evolutions(): HasMany
    {
        return $this->hasMany(Evolution::class, 'created_by');
    }

//    public function getFilamentAvatarUrl(): ?string
//    {
//        return Storage::url(path: $this->avatar_url);
//    }

    public function getFilamentAvatarUrl(): ?string
    {
        return asset('storage/'.$this->avatar_url);
    }
//    public function getFilamentAvatarUrl(): ?string
//    {
//        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');
//        return $this->$avatarColumn ? Storage::url("$this->$avatarColumn") : null;
//    }
}
