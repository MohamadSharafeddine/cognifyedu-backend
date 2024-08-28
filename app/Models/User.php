<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'type', 'date_of_birth', 'address', 'profile_picture', 'parent_id', 'parent_name', 'parent_email',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
}
