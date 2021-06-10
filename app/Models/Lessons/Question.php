<?php

namespace App\Models\Tests;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'lesson_id',
        'name',
    	'description',
        'answer_description',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function question()
    {
        return $this->belongsTo(Questions::class, 'question_id');
    }
    public function answers()
    {
        return $this->hasMany(Answer::class, 'questions_id');
    }
}