<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ __('services.payment_cancelled') }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('navigation.services') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ __('services.breadcrumb_cancelled') }}</li>
	</x-page-header>

	<div class="page-checkout">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-6">
					<div class="bg-light p-5 text-center">
						<div class="mb-4">
							<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="40" cy="40" r="40" fill="#D45769"/>
								<path d="M30 30L50 50M50 30L30 50" stroke="white" stroke-width="4" stroke-linecap="round"/>
							</svg>
						</div>

						<h2 class="mb-3">{{ __('services.payment_cancelled') }}</h2>
						<p class="mb-4">{{ __('services.payment_cancelled_message') }}</p>

						<div class="d-flex gap-3 justify-content-center">
							<a href="{{ route('checkout.index') }}" class="btn btn-primary py-3 px-5">{{ __('services.try_again') }}</a>
							<a href="{{ route('index') }}" class="btn btn-secondary py-3 px-5">{{ __('services.back_to_home') }}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
