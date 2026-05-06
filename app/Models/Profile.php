<?php

// app/Models/Profile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = ['name', 'description', 'active'];

    public function permissions()
{
    return $this->belongsToMany(
        Permission::class,
        'profile_permission', // 👈 CONFERE ISSO
        'profile_id',
        'permission_id'
    );
}

    public function users()
    {
        return $this->hasMany(User::class);
    }
}