<?php

namespace App\Services;

/**
 * Tracks the visitor's selected currency and converts catalogue prices
 * (stored in the base currency) into it.
 */
class CurrencyManager
{
    /**
     * @return array<int, string>
     */
    public function supported(): array
    {
        return array_keys(config('currency.supported'));
    }

    public function isSupported(string $code): bool
    {
        return in_array($code, $this->supported(), true);
    }

    public function current(): string
    {
        $code = session('currency');

        return is_string($code) && $this->isSupported($code) ? $code : config('currency.default');
    }

    public function set(string $code): void
    {
        session(['currency' => $code]);
    }

    /**
     * Units of $code per 1 unit of the base currency.
     */
    public function rate(string $code): float
    {
        if ($code === config('currency.base')) {
            return 1.0;
        }

        $configured = (float) settings('rate_'.strtolower($code));

        return $configured > 0 ? $configured : (float) config("currency.rates.{$code}", 1.0);
    }

    public function convert(float $baseAmount, ?string $to = null): float
    {
        return round($baseAmount * $this->rate($to ?? $this->current()), 2);
    }

    public function symbol(string $code): string
    {
        return config("currency.supported.{$code}.symbol", $code.' ');
    }
}
