<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ __('checkout.title') }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('navigation.services') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ __('checkout.breadcrumb') }}</li>
	</x-page-header>

	<div class="page-checkout">
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

			@if($errors->any())
				<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
					<strong>{{ __('common.validation_errors') }}</strong>
					<ul class="mb-0 mt-2">
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			<form id="checkout-form" method="POST" action="{{ route('checkout.start') }}" novalidate>
				@csrf
				<input type="hidden" name="addon_id" value="{{ $addon->id }}">

				<div class="row justify-content-between">
					<div class="col-lg-7 mb-5">
						<p class="text-muted mb-2">{{ __('checkout.step_of', ['current' => 1, 'total' => 2]) }} · {{ __('checkout.step_details') }}</p>
						<div class="customer-info">
						<h4 class="mb-4">{{ __('checkout.customer_info') }}</h4>

						<div class="row">
							<div class="col-md-6 mb-3">
								<input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
									   placeholder="{{ __('checkout.first_name') }}" value="{{ old('first_name', $demoData['first_name'] ?? '') }}" required>
								@error('first_name')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<div class="col-md-6 mb-3">
								<input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
									   placeholder="{{ __('checkout.last_name') }}" value="{{ old('last_name', $demoData['last_name'] ?? '') }}" required>
								@error('last_name')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
									   placeholder="{{ __('checkout.email') }}" value="{{ old('email', $demoData['email'] ?? '') }}" required>
								@error('email')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<div class="col-md-6 mb-3">
								<input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
									   placeholder="{{ __('checkout.phone') }}" value="{{ old('phone', $demoData['phone'] ?? '') }}" required>
								@error('phone')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="mb-3">
							<input type="text" name="company" class="form-control @error('company') is-invalid @enderror"
								   placeholder="{{ __('checkout.company') }}" value="{{ old('company', $demoData['company'] ?? '') }}">
							@error('company')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<input type="text" name="address_line_1" class="form-control @error('address_line_1') is-invalid @enderror"
								   placeholder="{{ __('checkout.address_line_1') }}" value="{{ old('address_line_1', $demoData['address_line_1'] ?? '') }}" required>
							@error('address_line_1')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<input type="text" name="address_line_2" class="form-control @error('address_line_2') is-invalid @enderror"
								   placeholder="{{ __('checkout.address_line_2') }}" value="{{ old('address_line_2', $demoData['address_line_2'] ?? '') }}">
							@error('address_line_2')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<input type="text" name="city" class="form-control @error('city') is-invalid @enderror"
									   placeholder="{{ __('checkout.city') }}" value="{{ old('city', $demoData['city'] ?? '') }}" required>
								@error('city')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<div class="col-md-6 mb-3">
								<input type="text" name="state" class="form-control @error('state') is-invalid @enderror"
									   placeholder="{{ __('checkout.state') }}" value="{{ old('state', $demoData['state'] ?? '') }}" required>
								@error('state')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
									   placeholder="{{ __('checkout.postal_code') }}" value="{{ old('postal_code', $demoData['postal_code'] ?? '') }}" required>
								@error('postal_code')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
							<div class="col-md-6 mb-3">
								<select name="country" class="form-control @error('country') is-invalid @enderror" required>
									<option value="">{{ __('checkout.country') }}</option>
									@foreach($countries as $code => $name)
										<option value="{{ $code }}" {{ (old('country', $demoData['country'] ?? '') == $code) ? 'selected' : '' }}>
											{{ $name }}
										</option>
									@endforeach
								</select>
								@error('country')
									<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="mb-3">
							<textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
									  rows="3" placeholder="{{ __('checkout.notes') }}">{{ old('notes', $demoData['notes'] ?? '') }}</textarea>
							@error('notes')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
						</div>

						@php
							$recurring = match ($addon->type) {
								\App\Models\ServiceAddon::PRICING_MONTHLY => 'monthly',
								\App\Models\ServiceAddon::PRICING_ANNUAL => 'annual',
								default => null,
							};
						@endphp

						@if($recurring)
							<div class="alert alert-info mt-3 mb-0">
								<strong>{{ __('checkout.billing_' . $recurring . '_title') }}</strong>
								<ul class="mb-0 mt-2 ps-3">
									@foreach(__('checkout.billing_' . $recurring . '_points') as $point)
										<li>{{ $point }}</li>
									@endforeach
								</ul>
							</div>
						@endif

						<div class="consent-box mt-3">
							<p class="small mb-2">
								<a href="{{ route('refund-policy') }}" target="_blank" rel="noopener">{{ __('navigation.refund_policy') }}</a> ·
								<a href="{{ route('terms-and-conditions') }}" target="_blank" rel="noopener">{{ __('navigation.terms_conditions') }}</a> ·
								<a href="{{ route('privacy-policy') }}" target="_blank" rel="noopener">{{ __('navigation.privacy_policy') }}</a>
							</p>

							<div class="form-check mb-2">
								<input class="form-check-input @error('accept_terms') is-invalid @enderror" type="checkbox" id="accept_terms" name="accept_terms" value="1" required {{ old('accept_terms') ? 'checked' : '' }}>
								<label class="form-check-label fw-normal" for="accept_terms">
									{{ __('checkout.consent_terms', [
										'name' => '__NAME__',
										'service' => $addon->name,
										'amount' => number_format($chargePrice, 2) . ' ' . $chargeCurrency . ($recurring === 'monthly' ? ' / ' . __('checkout.month') : ''),
									]) }}
									@if($recurring)
										{{ __('checkout.consent_' . $recurring) }}
									@endif
								</label>
								@error('accept_terms')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>

							<div class="form-check">
								<input class="form-check-input @error('accept_immediate_start') is-invalid @enderror" type="checkbox" id="accept_immediate_start" name="accept_immediate_start" value="1" required {{ old('accept_immediate_start') ? 'checked' : '' }}>
								<label class="form-check-label fw-normal" for="accept_immediate_start">{{ __('checkout.consent_immediate_start') }}</label>
								@error('accept_immediate_start')
									<div class="invalid-feedback d-block">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<button type="submit" id="continue-button" class="btn btn-primary w-100 py-3 mt-3">
							{{ __('checkout.continue_to_payment') }}
						</button>
					</div>

					<div class="col-lg-5 col-xl-4">
						<div class="order-summary-card bg-light p-4 sticky-top" style="top: 120px;">
							<h4 class="mb-4">{{ __('checkout.selected_item') }}</h4>
							<p class="text-muted mb-1">{{ $addon->service->name }}</p>
							<div class="d-flex justify-content-between align-items-start">
								<strong>{{ $addon->name }} <small class="text-muted">({{ $addon->getPricingOption() }})</small></strong>
								<span class="fw-bold">{{ money($chargePrice, $chargeCurrency) }}</span>
							</div>
							<hr>
							<div class="d-flex justify-content-between">
								<strong>{{ __('checkout.total') }}</strong>
								<strong>{{ money($chargePrice, $chargeCurrency) }} {{ $chargeCurrency }}</strong>
							</div>
							@if($currencyFallback)
								<p class="small text-muted mt-3 mb-0">{{ __('checkout.currency_fallback', ['selected' => app(\App\Services\CurrencyManager::class)->current(), 'currency' => $chargeCurrency]) }}</p>
							@endif
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	@push('styles')
	<style>
		#checkout-form .form-control.is-invalid {
			border-color: #dc3545;
			box-shadow: 0 0 0 1px #dc3545;
		}
		#checkout-form .invalid-feedback {
			display: block;
			color: #dc3545;
			font-size: 0.875rem;
			margin-top: 0.25rem;
		}
	</style>
	@endpush

	@push('scripts')
	<script>
		(function () {
			const form = document.getElementById('checkout-form');
			const messages = {
				required: @json(__('checkout.field_required')),
				email: @json(__('checkout.field_email_invalid'))
			};

			const nameLabel = document.querySelector('label[for="accept_terms"]');
			if (nameLabel) {
				const template = nameLabel.firstChild.textContent;
				const first = form.querySelector('[name="first_name"]');
				const last = form.querySelector('[name="last_name"]');
				const fallback = @json(__('checkout.consent_name_placeholder'));
				nameLabel.dataset.template = template;
				const render = function () {
					const name = (first.value + ' ' + last.value).trim() || fallback;
					nameLabel.firstChild.textContent = nameLabel.dataset.template.replace('__NAME__', name);
				};
				first.addEventListener('input', render);
				last.addEventListener('input', render);
				render();
			}

			function setError(field, message) {
				clearError(field);
				field.classList.add('is-invalid');
				const feedback = document.createElement('div');
				feedback.className = 'invalid-feedback js-feedback';
				feedback.textContent = message;
				field.insertAdjacentElement('afterend', feedback);
			}

			function clearError(field) {
				field.classList.remove('is-invalid');
				const next = field.nextElementSibling;
				if (next && next.classList.contains('invalid-feedback')) {
					next.remove();
				}
			}

			function validate(field) {
				const value = field.value.trim();
				if (field.required && value === '') {
					setError(field, messages.required);
					return false;
				}
				if (field.type === 'email' && value !== '' && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value)) {
					setError(field, messages.email);
					return false;
				}
				clearError(field);
				return true;
			}

			form.addEventListener('submit', function (e) {
				let firstInvalid = null;
				form.querySelectorAll('input[required], select[required], textarea[required], input[type="email"]').forEach(function (field) {
					if (!validate(field) && !firstInvalid) {
						firstInvalid = field;
					}
				});

				if (firstInvalid) {
					e.preventDefault();
					firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
					firstInvalid.focus({ preventScroll: true });
				}
			});

			form.addEventListener('input', function (e) {
				if (e.target.classList.contains('is-invalid')) {
					validate(e.target);
				}
			});
			form.addEventListener('change', function (e) {
				if (e.target.classList.contains('is-invalid')) {
					validate(e.target);
				}
			});
		})();
	</script>
	@endpush
</x-app-layout>
