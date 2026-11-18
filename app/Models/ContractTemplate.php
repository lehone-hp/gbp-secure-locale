<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTemplate extends Model
{
    protected $fillable = ['version', 'body', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public static function active(): self
    {
        return static::where('is_active', true)->latest('id')->firstOrFail();
    }

    /** Next version label, e.g. v2.0 becomes v2.1. */
    public static function nextVersion(): string
    {
        $latest = static::latest('id')->value('version') ?? 'v1.-1';
        [$major, $minor] = array_pad(explode('.', ltrim($latest, 'v')), 2, 0);

        return 'v'.(int) $major.'.'.((int) $minor + 1);
    }
}
