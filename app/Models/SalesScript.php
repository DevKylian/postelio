<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesScript extends Model
{
    protected $fillable = ['name', 'sequences'];

    protected $casts = [
        'sequences' => 'array',
    ];
}
