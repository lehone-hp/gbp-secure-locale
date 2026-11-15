<x-app-layout>
	<x-page-header>
		<x-slot:title>Contact Us</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">Home</a></li>
		<li class="breadcrumb-item active" aria-current="page">Contact</li>
	</x-page-header>

	<div class="page-contact-us">
		<div class="container">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			@if(session('error'))
				<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
					{{ session('error') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<div class="row section-row">
				<div class="col-lg-12">
					<div class="section-title section-title-center">
						<span class="section-sub-title wow fadeInUp">{{ __('contact.section_mini') }}</span>
						<h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('contact.section_title') }}</h2>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xl-5">
					<div class="contact-info-box">
						<div class="contact-us-content wow fadeInUp">
							<h3>{{ __('navigation.contact_us') }}</h3>
							<p>{{ __('contact.section_description') }}</p>
						</div>

						<div class="contact-info-list wow fadeInUp" data-wow-delay="0.2s">
							@if(settings('contact_phone'))
								<div class="contact-info-item">
									<div class="icon-box"><img src="{{ asset('seoai/images/icon-phone-white.svg') }}" alt=""></div>
									<div class="contact-info-item-content">
										<h3>{{ __('contact.phone_number') }}</h3>
										<p>
											<a href="tel:{{ settings('contact_phone') }}">{{ settings('contact_phone') }}</a>
											@if(settings('contact_phone_2'))
												<br><a href="tel:{{ settings('contact_phone_2') }}">{{ settings('contact_phone_2') }}</a>
											@endif
										</p>
									</div>
								</div>
							@endif

							@if(settings('contact_email'))
								<div class="contact-info-item">
									<div class="icon-box"><img src="{{ asset('seoai/images/icon-mail-white.svg') }}" alt=""></div>
									<div class="contact-info-item-content">
										<h3>{{ __('contact.email_address') }}</h3>
										<p>
											<a href="mailto:{{ settings('contact_email') }}">{{ settings('contact_email') }}</a>
											@if(settings('contact_email_2'))
												<br><a href="mailto:{{ settings('contact_email_2') }}">{{ settings('contact_email_2') }}</a>
											@endif
										</p>
									</div>
								</div>
							@endif

							@if(settings('address'))
								<div class="contact-info-item">
									<div class="icon-box"><img src="{{ asset('seoai/images/icon-location-white.svg') }}" alt=""></div>
									<div class="contact-info-item-content">
										<h3>{{ __('contact.head_office') }}</h3>
										<p>{{ settings('address') }}</p>
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>

				<div class="col-xl-7">
					<div class="contact-form">
						<div class="contact-form-content">
							<h3 class="wow fadeInUp">{{ __('contact.send_message') }}</h3>
						</div>

						<form method="POST" action="{{ route('contact.submit') }}" class="wow fadeInUp" data-wow-delay="0.4s">
							@csrf
							<div class="row">
								<div class="form-group col-md-6 mb-4">
									<input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
									       placeholder="{{ __('contact.full_name') }} *" value="{{ old('name') }}" required>
									@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
								</div>

								<div class="form-group col-md-6 mb-4">
									<input type="email" name="address" class="form-control @error('address') is-invalid @enderror"
									       placeholder="{{ __('contact.email') }} *" value="{{ old('address') }}" required>
									@error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
								</div>

								<div class="form-group col-md-6 mb-4">
									<input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
									       placeholder="{{ __('contact.phone') }}" value="{{ old('phone') }}">
									@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
								</div>

								<div class="form-group col-md-6 mb-4">
									<select name="service" class="form-control @error('service') is-invalid @enderror">
										<option value="">{{ __('contact.service') }}</option>
										@foreach($services as $service)
											<option value="{{ $service->name }}" {{ old('service') == $service->name ? 'selected' : '' }}>{{ $service->name }}</option>
										@endforeach
										<option value="Other" {{ old('service') == 'Other' ? 'selected' : '' }}>Other</option>
									</select>
									@error('service')<div class="invalid-feedback">{{ $message }}</div>@enderror
								</div>

								<div class="form-group col-md-12 mb-4">
									<textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4"
									          placeholder="{{ __('contact.message') }} *" required>{{ old('message') }}</textarea>
									@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
								</div>

								<div class="col-lg-12">
									<div class="contact-form-btn">
										<button type="submit" class="btn-default">{{ __('contact.submit') }}</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('web.sections.contact-cta', ['showContactInfo' => false])
</x-app-layout>
