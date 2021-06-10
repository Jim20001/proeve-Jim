<?php

namespace App\Models\Domains;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';
    protected $primaryKey = 'id';

    protected $fillable = [
    	'name',
    	'force_https',
    	'force_www',
    	'active',
    ];

    protected $casts = [
        'force_https' => 'boolean',
        'force_www' => 'boolean',
        'active' => 'boolean',
    ];

    public function options()
    {
        return $this->hasMany(Option::class, 'domains_id');
    }
}