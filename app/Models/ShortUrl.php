<?php

// app/Models/ShortUrl.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'short_code',
        'original_url',
        'clicks',
    ];

    protected $casts = [
        'clicks' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeExceptUser($query, $userId)
    {
        return $query->where('user_id', '!=', $userId);
    }

    // Methods
    public static function generateShortCode(): string
    {
        do {
            $code = Str::random(8);
        } while (self::where('short_code', $code)->exists());
        
        return $code;
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
        $this->update(['last_accessed' => now()]);
    }

    public function getShortUrlAttribute(): string
    {
        return route('short-url.redirect', $this->short_code);
    }
}