@props(['current' => 1])
<ol class="checkout-steps" aria-label="{{ __('checkout.step_of', ['current' => $current, 'total' => 2]) }}">
	<li class="{{ $current > 1 ? 'is-done' : 'is-active' }}" @if($current === 1) aria-current="step" @endif>
		<span class="checkout-steps__num">{{ $current > 1 ? '✓' : 1 }}</span>
		<span class="checkout-steps__label">{{ __('checkout.step_details') }}</span>
	</li>
	<li class="{{ $current === 2 ? 'is-active' : '' }}" @if($current === 2) aria-current="step" @endif>
		<span class="checkout-steps__num">2</span>
		<span class="checkout-steps__label">{{ __('checkout.step_payment') }}</span>
	</li>
</ol>
