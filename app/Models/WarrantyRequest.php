<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarrantyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'old_email',
        'new_email',
        'platform_id',
        'type',
        'reason',
        'status',
        'admin_notes',
        'resolved_at',
        'cancelled_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function getStatusAttribute($value)
    {
        if ($this->cancelled_at !== null) {
            return 'cancelled';
        }
        return $value;
    }
}
