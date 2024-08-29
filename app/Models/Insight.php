<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'cognitive_score_id',
        'behavioral_score_id',
        'profile_comment_id',
        'summary',
        'detailed_analysis',
        'recommendations',
        'progress_tracking',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
