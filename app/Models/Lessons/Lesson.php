<?php

namespace App\Models\Lessons;

use App\Models\Categories\Category;
use Illuminate\Database\Eloquent\Model;
use App\Models\Domains\Domain;

class Lesson extends Model
{
    protected $table = 'lesson';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'categories_id',
        'name',
    	'slucky',
        'body',
        'image',
        'video_url'
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'float',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domains_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'tests_id');
    }
}