<?php

namespace App\Models;

use App\Helpers\HeroHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Article extends Model
{
    use HasSlug;

    protected $fillable = [
        'category_id',
        'draf',
        'title',
        'slug',
        'hero',
        'description',
        'body'
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function minitutor() : BelongsTo
    {
        return $this->belongsTo(Minitutor::class);
    }

    public function images() : MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function heroUrl() : Array
    {
        return HeroHelper::getUrl($this->hero);
    }
}