<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'city',
        'brand_color',
        'logo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
