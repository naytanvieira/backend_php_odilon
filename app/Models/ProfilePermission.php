<?php

// app/Models/Permission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilePermission extends Model
{
    protected $table = 'profile_permission';
    public $timestamps = false;
    protected $fillable = ['profile_id', 'permission_id'];
}