<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtractedCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_account_id',
        'platform_id',
        'recipient_email',
        'code',
        'code_type',
        'body',
        'uid',
        'subject',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function emailAccount()
    {
        return $this->belongsTo(EmailAccount::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
