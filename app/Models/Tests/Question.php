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

    public function test()
    {
        return $this->belongsTo(Test::class, 'lesson_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'questions_id');
    }
}