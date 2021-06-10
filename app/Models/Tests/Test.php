<?php

namespace App\Models\Tests;

use App\Models\Categories\Category;
use Illuminate\Database\Eloquent\Model;
use App\Models\Domains\Domain;

class Test extends Model
{
    protected $table = 'tests';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'domains_id',
    	'categories_id',
        'name',
    	'description',
        'price',
        'active',
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