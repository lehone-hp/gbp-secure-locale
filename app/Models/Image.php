<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class Image
 * @package App\Models
 *
 * @property int $id
 * @property string $path
 * @property bool $primary
 * @property int $imageable_id
 * @property string $imageable_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'primary',
        'imageable_id',
        'imageable_type',
        'original_filename'
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    public function scopePrimary($query)
    {
        return $query->where('primary', true);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if ($image->primary) {
                $imageable = $image->imageable;
                $primaryImage = $imageable->images()->where('id', '!=', $image->id)->first();
                if ($primaryImage) {
                    $primaryImage->update(['primary' => true]);
                }
            }

            if ($image->path) {
                Storage::delete($image->path);
            }
        });
    }
}
