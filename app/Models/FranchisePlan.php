<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchisePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_clients',
        'max_queries_per_day_per_client',
        'features',
        'price',
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'plan_id');
    }
}
