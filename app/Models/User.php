<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'usuario',
        'name',
        'email',
        'password',
        'telefone',
        'perfil',
        'endereco',
        'status',
        'profile_id',
        'setor_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 🔐 JWT - obrigatório
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function hasPermission($permission)
    {
        if (!$this->profile) return false;

        return $this->profile->permissions()
            ->where('name', $permission)
            ->exists();
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class, 'setor_id');
    }
}