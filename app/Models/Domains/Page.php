<?php

namespace App\Models\Domains;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'name',
    	'slug',
        'content',
        'seo_title',
        'seo_description',
        'seo_richsnippet',
    ];
}