<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'deliverable',
        'submission_date',
        'mark',
        'teacher_comment',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

//     public function cognitiveScores()
//     {
//         return $this->hasMany(CognitiveScore::class);
//     }

//     public function behavioralScores()
//     {
//         return $this->hasMany(BehavioralScore::class);
//     }
}
