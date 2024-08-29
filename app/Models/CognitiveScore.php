<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CognitiveScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'submission_id',
        'critical_thinking',
        'logical_thinking',
        'linguistic_ability',
        'memory',
        'attention_to_detail',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
