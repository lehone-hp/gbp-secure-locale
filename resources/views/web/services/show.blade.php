<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ $service->name }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('services.breadcrumb_services') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
	</x-page-header>

	<div class="page-service-single">
		<div class="container">
			<div class="row">
				<div class="col-lg-4">
					<div class="page-single-sidebar">
						<div class="page-category-list wow fadeInUp">
							<h2 class="page-category-list-title">{{ __('services.our_services') }}</h2>
							<ul>
								@foreach (\App\Models\Service::visible()->get() as $other)
									<li><a href="{{ route('services.show', $other->slug) }}" @class(['active' => $other->is($service)])>{{ $other->name }}</a></li>
								@endforeach
							</ul>
						</div>

						<div class="sidebar-cta-box wow fadeInUp" data-wow-delay="0.25s">
							<div class="icon-box">
								<img src="{{ asset('seoai/images/icon-headphone-white.svg') }}" alt="">
							</div>
							<div class="sidebar-cta-box-content">
								<h2>{{ __('home.cta_title') }}</h2>
								<p>{{ __('home.cta_description') }}</p>
							</div>
							<div class="sidebar-cta-btn">
								<a href="{{ route('contact') }}" class="btn-default">{{ __('navigation.contact_us') }}</a>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-8">
					<div class="service-single-content">
						<div class="service-entry">
							<h2 class="text-anime-style-3">{{ $service->caption }}</h2>
							<p class="wow fadeInUp">{{ $service->description }}</p>
							<p class="wow fadeInUp" data-wow-delay="0.2s">{{ __('services.explore_offerings') }}</p>
						</div>

						<div class="service-addons">
							@foreach ($service->addons as $addon)
								@php $customLink = config('payment.use_custom_links') ? $addon->payment_link : null; @endphp
								<div class="service-addon-card wow fadeInUp">
									<div class="service-addon-card__head">
										<h3>{{ $addon->name }}</h3>
										<div class="service-addon-card__price">
											{{ $addon->price_formatted }}
											@if ($addon->getPricingOption())
												<small>({{ $addon->getPricingOption() }})</small>
											@endif
										</div>
									</div>
									<div class="service-addon__inner">
										{!! $addon->description !!}
									</div>
									<a href="{{ $customLink ?: route('checkout.index', ['service' => $service->id, 'addon' => $addon->id]) }}"
									   @if ($customLink) target="_blank" rel="noopener noreferrer" @endif
									   class="btn-default">{{ __('services.buy_now') }}</a>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('web.sections.contact-cta')
</x-app-layout>
