<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    protected $guarded = [];
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
} 