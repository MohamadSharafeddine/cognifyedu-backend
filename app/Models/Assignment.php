<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'attachment',
        'due_date',
    ];
    
    protected $casts = [
        'due_date' => 'date',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['due_date'] = $this->due_date ? $this->due_date->format('Y-m-d') : null;
        return $array;
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
