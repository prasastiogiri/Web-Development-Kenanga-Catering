<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory;

    protected $table = 'admins'; // Pastikan nama tabelnya benar

    // Tentukan guard untuk admin
    protected $guard = 'usersAdmin'; // Pastikan guard di sini sesuai dengan yang ada di config/auth.php

    protected $fillable = [
        'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
