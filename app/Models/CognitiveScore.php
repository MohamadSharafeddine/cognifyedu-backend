<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CognitiveScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        // 'assignment_id',
        // 'submission_id',
        'critical_thinking',
        'logical_thinking',
        'linguistic_ability',
        'memory',
        'attention_to_detail',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
    
    // public function assignment()
    // {
    //     return $this->belongsTo(Assignment::class);
    // }

//     public function submission()
//     {
//         return $this->belongsTo(Submission::class);
//     }
}
