<?php

namespace App\Traits;

trait Publishable
{
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
