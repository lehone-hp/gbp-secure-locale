<x-app-layout>
	<!-- Page Header -->
	<x-page-header>
		<x-slot:title>{{ __('achievements.title') }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ __('achievements.breadcrumb') }}</li>
	</x-page-header>

	<!-- Certifications Section -->
	<div class="page-achievements section-padding-y">
		<div class="container">
			<div class="row section-row">
				<div class="col-lg-12">
					<div class="section-title section-title-center">
						<span class="section-sub-title wow fadeInUp">{{ __('achievements.certifications_subtitle') }}</span>
						<h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('achievements.certifications_title') }}</h2>
						<p class="wow fadeInUp" data-wow-delay="0.2s">{{ __('achievements.certifications_caption') }}</p>
					</div>
				</div>
			</div>

			@if($certifications->count() > 0)
				<div class="row stagger-animation" data-stagger-delay="50">
					@foreach($certifications as $certification)
						<div class="col-3 col-lg-2 mb-4 stagger-item" data-animation="zoomIn">
							<img src="{{ $certification->image_url }}"
								 alt="{{ $certification->name }}"
								 class="img-fluid"
								 title="{{ $certification->name }}">
						</div>
					@endforeach
				</div>
			@else
				<div class="row">
					<div class="col-12 text-center">
						<p class="text-muted">{{ __('achievements.no_certifications') }}</p>
					</div>
				</div>
			@endif
		</div>
	</div>

	<!-- Clients Section -->
	<div class="page-achievements section-padding-y bg-section-light">
		<div class="container">
			<div class="row section-row">
				<div class="col-lg-12">
					<div class="section-title section-title-center">
						<span class="section-sub-title wow fadeInUp">{{ __('achievements.clients_subtitle') }}</span>
						<h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('achievements.clients_title') }}</h2>
						<p class="wow fadeInUp" data-wow-delay="0.2s">{{ __('achievements.clients_caption') }}</p>
					</div>
				</div>
			</div>

			@if($clients->count() > 0)
				<div class="row stagger-animation" data-stagger-delay="50">
					@foreach($clients as $client)
						<div class="col-6 col-md-3 mb-4 stagger-item" data-animation="fadeInUp">
							<div class="client-logo-wrapper">
								@if($client->website_url)
									<a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer" title="{{ $client->name }}">
										<img src="{{ $client->logo_url }}"
											 alt="{{ $client->name }}"
											 class="img-fluid client-logo">
									</a>
								@else
									<img src="{{ $client->logo_url }}"
										 alt="{{ $client->name }}"
										 class="img-fluid client-logo"
										 title="{{ $client->name }}">
								@endif
							</div>
						</div>
					@endforeach
				</div>
			@else
				<div class="row">
					<div class="col-12 text-center">
						<p class="text-muted">{{ __('achievements.no_clients') }}</p>
					</div>
				</div>
			@endif
		</div>
	</div>
	
	@include('web.sections.testimonials')
	
	@include('web.sections.contact-cta')
</x-app-layout>
