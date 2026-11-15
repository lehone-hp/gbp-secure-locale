<?php

use App\Models\Page;

if (! function_exists('page_content')) {
    function page_content($name, $shouldStripTags = false) {
        $page = Page::where('name', $name)->first();

        if (!$page) {
            return null;
        }

        return $shouldStripTags ? strip_tags($page->content) : $page->content;
    }
}

if (! function_exists('money')) {
    /**
     * An amount that is already in $currency, e.g. "CA$176.73".
     */
    function money(float|int|string $amount, string $currency): string {
        return app(\App\Services\CurrencyManager::class)->symbol($currency).number_format((float) $amount, 2);
    }
}

if (! function_exists('format_price')) {
    /**
     * A base-currency catalogue price converted to the visitor's currency, e.g. "176.73 CAD".
     */
    function format_price(float|int|string $baseAmount, ?string $currency = null): string {
        $manager = app(\App\Services\CurrencyManager::class);
        $currency ??= $manager->current();
        $amount = $manager->convert((float) $baseAmount, $currency);

        return number_format($amount, floor($amount) == $amount ? 0 : 2).' '.$currency;
    }
}
