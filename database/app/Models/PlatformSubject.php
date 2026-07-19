<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'subject',
        'pattern',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }
}
