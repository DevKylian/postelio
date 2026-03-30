<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FaqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_category_id',
        'title',
        'slug',
        'summary',
        'content',
        'sort_order',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            if (empty($question->slug)) {
                $baseSlug = Str::slug($question->title);
                $slug = $baseSlug;
                $count = 1;

                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                $question->slug = $slug;
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }
}
