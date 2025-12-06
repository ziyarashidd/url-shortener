<?php

// app/Models/Role.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public $timestamps = false;
    
    protected $fillable = ['name'];

    const SUPER_ADMIN = 'SuperAdmin';
    const ADMIN = 'Admin';
    const MEMBER = 'Member';
    const SALES = 'Sales';
    const MANAGER = 'Manager';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}