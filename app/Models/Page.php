<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
    ];

    // HOME PAGE
    const WEBSITE_NAME = 'Website Name';
    const HOME_HERO_SUBTITLE = 'Home: Hero Subtitle';
    const HOME_WHAT_WE_DO = 'Home: What We Do';
    const HOME_FIND_PROPERTY_SUBTEXT = 'Home: Find Property subtext';
    const HOME_BEST_DEAL_SUBTEXT = 'Home: Best deal subtext';
    const HOME_FIND_HOME_SUBTEXT = 'Home: Find home subtext';
    const HOME_PROPERTIES_FOR_SALE = "Home: Properties for sale";
    const HOME_CTA_SUBTEXT = 'Home: CTA subtext';

    // SELL PAGE
    const SELL_HERO_SUBTITLE = 'Sell: Hero Subtitle';
    const SELL_MAKE_FORTUNE_SUBTEXT = 'Sell: Value of Your Land';
    const SELL_HOUSE_SUBTEXT = 'Sell: Sell your house subtext';
    const SELL_EVALUTE_PROPERTY_SUBTEXT = 'Sell: Evaluate your property subtext';

    // ACHITECTURAL DESIGN PAGE
    const DESIGN_HERO_SUBTITLE = 'DESIGN: Hero Subtitle';

    // MANAGE PAGE
    const MANAGING_FOR_SUBTEXT = 'Manage: Who we managing for';
    const MANAGE_BENEFITS_SUBTEXT_1 = 'Manage: benefits subtext 1';
    const MANAGE_BENEFITS_SUBTEXT_2 = 'Manage: benefits subtext 2';
    const MANAGE_PROPERTY_SUBTEXT = 'Manage: manage properties subtext';

    // ABOUT PAGE
    const ABOUT_COMPANY_PROFILE = 'About: Company Profile';
    const ABOUT_CORE_VALUES = 'About: Core Values';
    const ABOUT_MISSION = 'About: MISSION';
    const ABOUT_VISION = 'About: Vision';
    const ABOUT_CTA_SUBTEXT = 'About: CTA subtext';

    // CONTACT PAGE
    const CONTACT_GET_IN_TOUCH_SUBTEXT = 'Contact: get in touch';

    // FOOTER
    CONST FOOTER_ABOUT_SUBTEXT = 'Footer: About subtext';

}
