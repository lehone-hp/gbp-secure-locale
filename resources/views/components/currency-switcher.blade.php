@php($currencies = app(\App\Services\CurrencyManager::class))

<div class="dropdown language-switcher currency-switcher">
    <button class="btn btn-link dropdown-toggle" type="button" id="currencyDropdown" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('navigation.currency') }}">
        <span class="currency-switcher__code">{{ $currencies->current() }}</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="currencyDropdown">
        @foreach($currencies->supported() as $code)
            <li>
                <a class="dropdown-item {{ $currencies->current() === $code ? 'active' : '' }}"
                   href="{{ route('currency.switch', $code) }}">
                    {{ $currencies->symbol($code) }} {{ $code }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
