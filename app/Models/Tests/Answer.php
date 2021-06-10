<?php

namespace App\Models\Tests;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    protected $table = 'answers';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'questions_id',
        'name',
        'correct',
    ];

    protected $casts = [
        'correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'questions_id');
    }
}