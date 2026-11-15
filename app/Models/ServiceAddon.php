<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class ServiceAddon extends Model
{
    use HasFactory, HasSlug, HasTranslations;

    public $translatable = ['name', 'description'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'service_id',
        'type',
        'payment_link',
    ];

    const PRICING_ONE_TIME = 'one_time';
    const PRICING_ANNUAL = 'annual';
    const PRICING_SIX_MONTHS = 'six_months';
    const PRICING_MONTHLY = 'monthly';
    const PRICING_THREE_MONTHS = 'three_months';

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public static function getAddonTypes()
    {
        return [
            self::PRICING_ONE_TIME => 'One Time',
            self::PRICING_MONTHLY => 'Monthly',
            self::PRICING_THREE_MONTHS => '3 Months',
            self::PRICING_SIX_MONTHS => '6 Months',
            self::PRICING_ANNUAL => 'Annual',
        ];
    }

    public function getPricingOption() {
        return match ($this->type) {
            self::PRICING_ONE_TIME => __('services.addon_type_one_time'),
            self::PRICING_ANNUAL => __('services.addon_type_annual'),
            self::PRICING_SIX_MONTHS => __('services.addon_type_six_months'),
            self::PRICING_MONTHLY => __('services.addon_type_monthly'),
            self::PRICING_THREE_MONTHS => __('services.addon_type_three_months'),
            default => null,
        };
    }

    public function getPriceFormattedAttribute() {
        return format_price($this->price);
    }
}
