<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesScript extends Model
{
    protected $fillable = ['name', 'sequences', 'flow'];

    protected $casts = [
        'sequences' => 'array',
        'flow'      => 'array',
    ];
}
