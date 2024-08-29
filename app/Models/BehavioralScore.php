<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehavioralScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'submission_id',
        'engagement',
        'time_management',
        'adaptability',
        'collaboration',
        'focus',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
