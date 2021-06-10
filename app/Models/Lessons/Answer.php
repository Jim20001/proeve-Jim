<?php

namespace App\Models\Tests;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answers';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'question_id',
        'name',
        'correct',
    ];

    protected $casts = [
        'correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Test::class, 'questions_id');
    }
}