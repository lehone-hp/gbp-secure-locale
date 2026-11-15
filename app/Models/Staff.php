<?php

namespace App\Models;

use App\Contracts\ModelRanking\HasRanking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * @class Staff
 *
 * @property string $name
 * @property string $position
 * @property string $rank
 * @property string $image_path
 * @property string $facebook_url
 * @property string $twitter_url
 * @property string $linkedin_url
 * @property string $instagram_url
 *
 * @property string $image_url
 */
class Staff extends Model implements HasRanking
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'rank',
        'image_path',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
    ];

    public function getImageUrlAttribute() {
        return $this->image_path ? asset('/storage/'.$this->image_path)
            : asset('assets/img/avatar.png');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Staff $staff) {
            $staff->rank = self::max('rank') + 1;
        });

        static::deleting(function (Staff $staff) {
            if ($staff->image_path) {
                Storage::delete($staff->image_path);
            }
        });
    }
}
