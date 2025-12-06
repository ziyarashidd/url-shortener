<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'company_id',
        'role_id',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function shortUrls(): HasMany
    {
        return $this->hasMany(ShortUrl::class);
    }

    // Role checking methods
    public function isSuperAdmin(): bool
    {
        return $this->role->name === Role::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role->name === Role::ADMIN;
    }

    public function isMember(): bool
    {
        return $this->role->name === Role::MEMBER;
    }

    public function isSales(): bool
    {
        return $this->role->name === Role::SALES;
    }

    public function isManager(): bool
    {
        return $this->role->name === Role::MANAGER;
    }

    public function canCreateUrl(): bool
    {
        return $this->isSales() || $this->isManager();
    }

    public function canInviteUsers(): bool
    {
        return $this->isAdmin() || $this->isSuperAdmin();
    }

    public function canViewAllCompanyUrls(): bool
    {
        return $this->isAdmin();
    }
}
