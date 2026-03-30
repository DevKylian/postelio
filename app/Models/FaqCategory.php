<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'sort_order',
    ];

    public function questions()
    {
        return $this->hasMany(FaqQuestion::class)->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}
