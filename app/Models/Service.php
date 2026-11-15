<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    public $translatable = ['name', 'caption', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'caption',
        'description',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function scopeVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function addons()
    {
        return $this->hasMany(ServiceAddon::class);
    }
}
