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

			<form id="checkout-form" method="POST" action="{{ $processUrl }}">
				@csrf
				<input type="hidden" name="payment_gateway" value="{{ $paymentGateway }}">
				<input type="hidden" name="payment_nonce" id="payment-nonce">
				<input type="hidden" name="payment_method_id" id="payment-method-id">
				<input type="hidden" name="payment_source_id" id="payment-source-id">
				<input type="hidden" name="verification_token" id="verification-token">

				<div class="row justify-content-between">
					<!-- Left Column: customer details -->
						<div class="col-lg-7 mb-5">
							<p class="text-muted mb-2">{{ __('checkout.step_of', ['current' => 2, 'total' => 2]) }} · {{ __('checkout.step_payment') }}</p>
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h4 class="mb-0">{{ __('checkout.customer_info') }}</h4>
								<a href="{{ $editUrl }}">{{ __('checkout.edit_details') }}</a>
							</div>
							<address class="mb-0">
								<strong>{{ $order->customer_full_name }}</strong><br>
								{{ $order->customer_email }} · {{ $order->customer_phone }}<br>
								@if($order->customer_company){{ $order->customer_company }}<br>@endif
								{{ $order->address_line_1 }}@if($order->address_line_2), {{ $order->address_line_2 }}@endif<br>
								{{ $order->postal_code }} {{ $order->city }}, {{ $order->state }}, {{ $order->country }}
							</address>
						</div>

						<!-- Right Column: Order Summary -->
					<div class="col-lg-5 col-xl-4">
						<div class="order-summary-card bg-light p-4 sticky-top" style="top: 120px;">
							<h4 class="mb-4">{{ __('checkout.order_summary') }}</h4>

							<table class="table order-summary-table mb-4">
								<thead>
									<tr>
										<th>{{ __('checkout.item') }}</th>
										<th class="text-end">{{ __('checkout.price') }}</th>
									</tr>
								</thead>
								<tbody id="order-items">
									@foreach($order->items as $item)
										<tr>
											<td>{{ $item->item_name }}@if($item->item_type) <small class="text-muted">({{ str_replace('_', ' ', $item->item_type) }})</small>@endif</td>
											<td class="text-end">{{ money($item->price, $order->currency) }}</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr class="total-row">
										<th class="pt-3">{{ __('checkout.total') }}</th>
										<th class="text-end pt-3" id="order-total">{{ money($order->total, $order->currency) }} {{ $order->currency }}</th>
									</tr>
								</tfoot>
							</table>

							<!-- Payment UI Container -->
							<div id="payment-container" class="mb-4">
								@if($paymentGateway === 'payunit')
									<!-- PayUnit Payment Message -->
									<div id="payunit-message" class="mb-4">
										<div class="alert alert-info">
											{{ __('checkout.payunit_redirect_message') }}
										</div>
									</div>
								@elseif($paymentGateway === 'whop')
									<!-- Whop embedded checkout mounts here after the order is created -->
									<div id="whop-message" class="alert alert-info">
										{{ __('checkout.whop_message') }}
									</div>
									<div id="whop-checkout-container"></div>
								@elseif($paymentGateway === 'stripe')
									<!-- Stripe Card Element Container -->
									<div id="stripe-card-container" class="mb-4">
										<h6 class="mb-3">{{ __('checkout.payment_details') }}</h6>
										
										<!-- Card Element -->
										<div class="mb-3">
											<label class="form-label">{{ __('checkout.card_information') }}</label>
											<div id="stripe-card-element" class="stripe-element"></div>
										</div>
										
										<!-- Card Errors -->
										<div id="stripe-card-errors" class="alert alert-danger" style="display:none;"></div>
									</div>
								@elseif($paymentGateway === 'paypal')
									<!-- Card Fields Container -->
									<div id="card-fields-container" class="mb-4">
										<h6 class="mb-3">{{ __('checkout.pay_with_card') }}</h6>
										
										<!-- Card Number Field -->
										<div class="mb-3">
											<label class="form-label">{{ __('checkout.card_number') }}</label>
											<div id="card-number-field-container"></div>
										</div>
										
										<!-- Expiry and CVV Fields -->
										<div class="row">
											<div class="col-md-6 mb-3">
												<label class="form-label">{{ __('checkout.expiration_date') }}</label>
												<div id="card-expiry-field-container"></div>
											</div>
											<div class="col-md-6 mb-3">
												<label class="form-label">{{ __('checkout.cvv') }}</label>
												<div id="card-cvv-field-container"></div>
											</div>
										</div>
										
										<!-- Cardholder Name -->
										<div class="mb-3">
											<label class="form-label">{{ __('checkout.cardholder_name') }}</label>
											<input type="text" id="card-holder-name" class="form-control" placeholder="{{ __('checkout.cardholder_name_placeholder') }}">
										</div>
										
										<!-- Card Field Errors -->
										<div id="card-field-errors" class="alert alert-danger" style="display:none;"></div>
										
										<!-- Submit Card Payment Button -->
										<button type="button" id="card-field-submit-button" class="btn btn-primary w-100 py-3 mb-3">
											<span id="card-field-button-text">{{ __('checkout.pay_with_card') }}</span>
											<span id="card-field-button-loading" style="display:none;">
												<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
												{{ __('checkout.processing') }}
											</span>
										</button>
										
										<div class="text-center mb-3">
											<span class="text-muted">{{ __('checkout.or') }}</span>
										</div>
									</div>
									
									<!-- PayPal Button Container -->
									<div id="paypal-button-container">
										<div class="text-center py-3">
											<div class="spinner-border text-primary" role="status">
												<span class="visually-hidden">{{ __('checkout.loading_paypal_short') }}</span>
											</div>
											<p class="text-muted mt-2 mb-0">{{ __('checkout.js_loading_paypal') }}</p>
										</div>
									</div>
								@elseif($paymentGateway === 'square')
									<!-- Square Web Payments SDK Card Container -->
									<div id="square-card-container" class="mb-4">
										<h6 class="mb-3">{{ __('checkout.payment_details') }}</h6>
										<div id="square-card-element"></div>
										<div id="square-card-errors" class="alert alert-danger mt-2" style="display:none;"></div>
									</div>
								@endif
							</div>

							<!-- Submit Button (for non-PayPal gateways) -->
							<div id="payment-errors" class="alert alert-danger" role="alert" style="display:none;"></div>

							<button type="submit" id="submit-payment" class="btn btn-primary w-100 py-3" style="{{ $paymentGateway === 'paypal' ? 'display:none;' : '' }}">
								<span id="button-text">{{ __('checkout.make_payment') }}</span>
								<span id="button-loading" style="display:none;">
									<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
									Processing...
								</span>
							</button>

							<p class="text-muted text-center mt-3 mb-0" style="font-size: 12px;">
								{{ __('checkout.payment_secure') }}
							</p>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	@push('styles')
	@if($paymentGateway === 'stripe')
	<style>
		/* Stripe Elements Styling */
		.stripe-element {
			background-color: #ffffff;
			border: 1px solid #6254e7;
			color: #3b3663;
			padding: 14px 16px;
			border-radius: 8px;
			transition: border-color 0.3s ease, box-shadow 0.3s ease;
		}

		.stripe-element:focus-within {
			border-color: #6254e7;
			box-shadow: 0 0 0 0.2rem rgba(98, 84, 231, 0.18);
		}

		.stripe-element.StripeElement--invalid {
			border-color: #D45769;
		}

		#stripe-card-container .form-label {
			color: #3b3663;
			font-weight: 500;
			margin-bottom: 0.5rem;
		}
	</style>
	@elseif($paymentGateway === 'paypal')
	<style>
		
		/* Cardholder name input to match PayPal card fields */
		#card-holder-name {
			background-color: #ffffff;
			border: 1px solid #6254e7;
			color: #3b3663;
			height: 45px;
			padding: 12px 16px;
			border-radius: 8px;
			font-family: 'Rubik', sans-serif;
			font-size: 16px;
			transition: border-color 0.3s ease, box-shadow 0.3s ease;
		}

		#card-holder-name:focus {
			background-color: #ffffff;
			border-color: #6254e7;
			color: #3b3663;
			box-shadow: 0 0 0 0.2rem rgba(98, 84, 231, 0.18);
			outline: 0;
		}

		#card-holder-name::placeholder {
			color: rgba(59, 54, 99, 0.4);
		}
		
		#card-fields-container .form-label {
			font-weight: 300;
			margin-bottom: 0.5rem;
		}
	</style>
	@elseif($paymentGateway === 'square')
	<style>
		/* Square Web Payments SDK Styling */
		#square-card-element:focus-within {
			border-color: #6254e7;
			box-shadow: 0 0 0 0.2rem rgba(98, 84, 231, 0.18);
		}

		#square-card-container .form-label,
		#square-card-container h6 {
			color: #3b3663;
			font-weight: 500;
		}
	</style>
	@endif
	@endpush

	@push('scripts')
	@if($paymentGateway === 'whop')
		<!-- Whop Elements SDK -->
		<script src="https://cdn.whop.com/elements/amber/elements.js" data-whop-elements async></script>
	@elseif($paymentGateway === 'stripe')
		<!-- Stripe.js SDK -->
		<script src="https://js.stripe.com/v3/"></script>
	@elseif($paymentGateway === 'square')
		@php
			$squareEnv = config('payment.gateways.square.environment', 'sandbox');
			$squareAppId = config('payment.gateways.square.application_id', '');
			$squareLocationId = config('payment.gateways.square.location_id', '');
			$squareLocale = app()->getLocale() === 'fr' ? 'fr-FR' : 'en-US';
		@endphp
		<!-- Square Web Payments SDK -->
		<script src="{{ $squareEnv === 'production' ? 'https://web.squarecdn.com/v1/square.js' : 'https://sandbox.web.squarecdn.com/v1/square.js' }}"></script>
	@elseif($paymentGateway === 'paypal')
		<!-- PayPal JavaScript SDK -->
		@php
			$paypalMode = config('paypal.mode', 'sandbox');
			$paypalClientId = $paypalMode === 'sandbox'
				? config('paypal.sandbox.client_id')
				: config('paypal.live.client_id');
			$paypalCurrency = $order->currency;
		@endphp
		@if($paypalClientId)
			<script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency={{ $paypalCurrency }}&components=buttons,card-fields&locale={{ app()->getLocale() }}_{{ strtoupper(config('paypal.locale_country', app()->getLocale() === 'fr' ? 'FR' : 'US')) }}"></script>
		@else
			<script>
				console.error('PayPal Client ID not configured. Please set PAYPAL_SANDBOX_CLIENT_ID or PAYPAL_LIVE_CLIENT_ID in your .env file');
			</script>
		@endif
	@endif
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		// Translation strings for JavaScript
		const translations = {
			selectAddon: @json(__('checkout.js_select_addon')),
			requiredFields: @json(__('checkout.js_required_fields')),
			cardholderName: @json(__('checkout.js_cardholder_name')),
			createOrderFailed: @json(__('checkout.js_create_order_failed')),
			paymentFailed: @json(__('checkout.js_payment_failed')),
			paymentCaptureFailed: @json(__('checkout.js_payment_capture_failed')),
			errorOccurred: @json(__('checkout.js_error_occurred')),
			paypalNotLoaded: @json(__('checkout.js_paypal_not_loaded')),
			loadingPaypal: @json(__('checkout.js_loading_paypal')),
				networkError: @json(__('checkout.js_network_error')),
				sessionExpired: @json(__('checkout.js_session_expired')),
				paymentCancelled: @json(__('checkout.js_payment_cancelled'))
		};

		$(document).ready(function() {
			// CSRF Token
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			const orderTotal = {{ (float) $order->total }};
			const processUrl = @json($processUrl);
			const paypalCreateUrl = @json($paypalCreateUrl);
			const customer = @json($customer);

			function resetButton() {
				$('#button-text').show();
				$('#button-loading').hide();
				$('#submit-payment').prop('disabled', false);
			}

			// The one place payment errors are shown, so none can be missed
			function showPaymentError(message) {
				resetButton();
				const box = document.getElementById('payment-errors');
				box.textContent = message;
				box.style.display = 'block';
				box.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}

			// POST that always resolves to {ok, data, error}, including validation, CSRF and server errors
			async function postJson(url, body) {
				let response;
				try {
					response = await fetch(url, {
						method: 'POST',
						headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						body: body
					});
				} catch (networkError) {
					return { ok: false, error: translations.networkError };
				}

				let data = null;
				try {
					data = await response.json();
				} catch (parseError) {
					return { ok: false, error: response.status === 419 ? translations.sessionExpired : translations.errorOccurred };
				}

				if (response.ok) {
					return { ok: true, data: data };
				}

				const fieldErrors = data.errors ? Object.values(data.errors).flat().join(' ') : null;
				return { ok: false, error: fieldErrors || data.error || data.message || translations.errorOccurred };
			}

			async function processPayment() {
				$('#payment-errors').hide();
				$('#button-text').hide();
				$('#button-loading').show();
				$('#submit-payment').prop('disabled', true);

				try {
					@if($paymentGateway === 'stripe')
					const {paymentMethod, error} = await stripe.createPaymentMethod({
						type: 'card',
						card: cardElement,
						billing_details: {
							name: customer.name,
							email: customer.email,
							phone: customer.phone,
							address: {
								line1: customer.address_line_1,
								line2: customer.address_line_2,
								city: customer.city,
								state: customer.state,
								postal_code: customer.postal_code,
								country: customer.country
							}
						}
					});

					if (error) {
						// Card entry problems belong in the card field's own error box, not a second one
						resetButton();
						$('#stripe-card-errors').text(error.message).show()[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
						return;
					}

					$('#payment-method-id').val(paymentMethod.id);
					@endif

					const result = await postJson(processUrl, new FormData(document.getElementById('checkout-form')));
					if (!result.ok) {
						showPaymentError(result.error);
						return;
					}
					const data = result.data;

					@if($paymentGateway === 'stripe')
					// 3D Secure: finish authentication in the browser; the success page confirms with Stripe
					if (data.requires_action) {
						const {error: actionError} = await stripe.handleNextAction({ clientSecret: data.client_secret });
						if (actionError) {
							showPaymentError(actionError.message);
							return;
						}
					}
					@elseif($paymentGateway === 'whop')
					// Mount Whop's embedded checkout; the webhook marks the order paid
					if (data.embedded_checkout_id) {
						$('#whop-message').hide();
						$('#submit-payment').hide();
						window.WhopElements().checkout.create({
							container: '#whop-checkout-container',
							checkoutConfiguration: data.embedded_checkout_id,
							returnUrl: data.redirect_url,
							onComplete: function() {
								window.location.href = data.redirect_url;
							}
						});
						return;
					}
					@endif

					window.location.href = data.redirect_url;
				} catch (error) {
					console.error('Payment error:', error);
					showPaymentError(translations.errorOccurred);
				}
			}

			// Form submission
			$('#checkout-form').on('submit', function(e) {
				e.preventDefault();
				processPayment();
			});

			@if($paymentGateway === 'stripe')
			// Stripe Elements Integration
			let stripe, cardElement;
			let stripeInitialized = false;
			
			function initStripeElements() {
				if (stripeInitialized) {
					console.log('Stripe already initialized');
					return;
				}
				
				if (typeof Stripe === 'undefined') {
					console.error('Stripe.js not loaded');
					$('#stripe-card-errors').text('Payment system not loaded. Please refresh the page.').show();
					return;
				}
				
				console.log('Initializing Stripe Elements...');
				
				// Initialize Stripe
				const publishableKey = '{{ config('payment.gateways.stripe.key') }}';
				if (!publishableKey) {
					console.error('Stripe publishable key not configured');
					$('#stripe-card-errors').text('Payment system not configured. Please contact support.').show();
					return;
				}
				
				stripe = Stripe(publishableKey);
				
				// Create Elements instance
				const elements = stripe.elements();
				
				// Create card element with custom styling
				cardElement = elements.create('card', {
					style: {
						base: {
							color: '#3b3663',
							fontFamily: '"Rubik", sans-serif',
							fontSmoothing: 'antialiased',
							fontSize: '16px',
							fontWeight: '400',
							'::placeholder': {
								color: 'rgba(59, 54, 99, 0.4)'
							}
						},
						invalid: {
							color: '#D45769',
							iconColor: '#D45769'
						}
					}
				});
				
				// Mount card element
				cardElement.mount('#stripe-card-element');
				
				// Handle real-time validation errors
				cardElement.on('change', function(event) {
					const displayError = $('#stripe-card-errors');
					if (event.error) {
						displayError.text(event.error.message).show();
					} else {
						displayError.hide();
					}
				});
				
				stripeInitialized = true;
				console.log('Stripe Elements initialized successfully');
			}
			@elseif($paymentGateway === 'square')
			// Square Web Payments SDK Integration
			let squareCard = null;
			let squarePayments = null;
			let squareInitialized = false;
			
			async function initSquareCard() {
				if (squareInitialized) return;

				if (typeof Square === 'undefined') {
					console.error('Square.js not loaded');
					$('#square-card-errors').text('Payment system not loaded. Please refresh the page.').show();
					return;
				}

				const appId = '{{ $squareAppId ?? '' }}';
				const locationId = '{{ $squareLocationId ?? '' }}';
				const squareLocale = '{{ $squareLocale }}';

				if (!appId || !locationId) {
					console.error('Square application ID or location ID not configured');
					$('#square-card-errors').text('Payment system not configured. Please contact support.').show();
					return;
				}

				try {
					const payments = Square.payments(appId, locationId, { locale: squareLocale });
					squarePayments = payments;

					squareCard = await payments.card({
						style: {
							'.input-container': {
								borderColor: '#6254e7',
								borderRadius: '8px',
							},
							'.input-container.is-focus': {
								borderColor: '#6254e7',
							},
							'.input-container.is-error': {
								borderColor: '#D45769',
							},
							input: {
								color: '#3b3663',
								fontSize: '16px',
							},
							'input::placeholder': {
								color: 'rgba(59, 54, 99, 0.4)',
							},
						},
					});

					await squareCard.attach('#square-card-element');
					squareInitialized = true;
					console.log('Square card initialized successfully');
				} catch (error) {
					console.error('Failed to initialize Square:', error);
					$('#square-card-errors').text('Payment system failed to load. Please refresh the page.').show();
				}
			}

			// Override submit handler for Square
			$('#submit-payment').off('click').on('click', async function(e) {
				e.preventDefault();

				if (!squareCard) {
					$('#square-card-errors').text('Payment system not ready. Please refresh the page.').show();
					return;
				}

				$('#button-text').hide();
				$('#button-loading').show();
				$('#submit-payment').prop('disabled', true);
				$('#square-card-errors').hide();

				try {
					const result = await squareCard.tokenize();

					if (result.status === 'OK') {
						$('#payment-source-id').val(result.token);

						if (squarePayments) {
							const verificationDetails = {
								amount: orderTotal.toFixed(2),
								currencyCode: '{{ strtoupper(config("payment.gateways.square.currency", $order->currency)) }}',
								intent: 'CHARGE',
								billingContact: {
									familyName: customer.last_name,
									givenName: customer.first_name,
									email: customer.email,
									phone: customer.phone,
									addressLines: [customer.address_line_1, customer.address_line_2].filter(Boolean),
									city: customer.city,
									state: customer.state,
									postalCode: customer.postal_code,
									countryCode: customer.country,
								},
							};

							try {
								const verificationResult = await squarePayments.verifyBuyer(result.token, verificationDetails);
								if (verificationResult && verificationResult.token) {
									$('#verification-token').val(verificationResult.token);
								}
							} catch (verifyError) {
								console.error('Square buyer verification failed:', verifyError);
									$('#square-card-errors').text(translations.errorOccurred).show();
									resetButton();
									return;
							}
						}

						await processPayment();
					} else {
						const errorMessages = result.errors
							? result.errors.map(e => e.message).join(', ')
							: 'Card tokenization failed. Please check your card details.';
						$('#square-card-errors').text(errorMessages).show();
						$('#button-text').show();
						$('#button-loading').hide();
						$('#submit-payment').prop('disabled', false);
					}
				} catch (error) {
					console.error('Square tokenization error:', error);
					$('#square-card-errors').text('An error occurred. Please try again.').show();
					$('#button-text').show();
					$('#button-loading').hide();
					$('#submit-payment').prop('disabled', false);
				}
			});
			@elseif($paymentGateway === 'paypal')
			// PayPal Advanced Checkout Integration
			let paypalButtonRendered = false;
			let cardFieldsInstance = null;

			function initPayPalButton() {
				if (paypalButtonRendered) {
					console.log('PayPal already rendered');
					return;
				}

				if (typeof paypal === 'undefined') {
					console.error('PayPal SDK not loaded. Please check your PayPal configuration.');
					$('#paypal-button-container').html('<div class="alert alert-danger">' + translations.paypalNotLoaded + '</div>');
					return;
				}

								console.log('Initializing PayPal Advanced Checkout...');
				
				// Clear loading message
				$('#paypal-button-container').empty();
				
				paypalButtonRendered = true;

				// Initialize Card Fields (Advanced Checkout)
				if (paypal.CardFields) {
					cardFieldsInstance = paypal.CardFields({
						createOrder: async function() {
							
							try {
								const response = await fetch(paypalCreateUrl, {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'Accept': 'application/json',
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									},
									body: '{}'
								});

								const data = await response.json();
								
								if (data.success) {
									return data.order_id;
								} else {
									$('#card-field-errors').text(data.error || translations.createOrderFailed).show();
									throw new Error(data.error || translations.createOrderFailed);
								}
							} catch (error) {
								console.error('Error creating order:', error);
								$('#card-field-errors').text(translations.createOrderFailed).show();
								throw error;
							}
						},
						onApprove: async function(data) {
							console.log('Payment approved, capturing...', data);
							
							$('#card-field-button-text').hide();
							$('#card-field-button-loading').show();
							$('#card-field-submit-button').prop('disabled', true);

							try {
								const response = await fetch('{{ route("checkout.paypal.capture") }}', {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										'Accept': 'application/json',
										'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
									},
									body: JSON.stringify({
										order_id: data.orderID
									})
								});

								const result = await response.json();
								
								if (result.success) {
									window.location.href = result.redirect_url;
								} else {
									$('#card-field-errors').text(result.error || translations.paymentFailed).show();
									$('#card-field-button-text').show();
									$('#card-field-button-loading').hide();
									$('#card-field-submit-button').prop('disabled', false);
								}
							} catch (error) {
								console.error('Error capturing payment:', error);
								$('#card-field-errors').text(translations.paymentCaptureFailed).show();
								$('#card-field-button-text').show();
								$('#card-field-button-loading').hide();
								$('#card-field-submit-button').prop('disabled', false);
							}
						},
						onError: function(err, err2) {
							$('#card-field-errors').text(translations.errorOccurred).show();
							$('#card-field-button-text').show();
							$('#card-field-button-loading').hide();
							$('#card-field-submit-button').prop('disabled', false);
						},
						style: {
							'input': {
								'font-size': '16px',
								'font-family': "'Rubik', sans-serif",
								'font-weight': '300',
								'color': '#3b3663',
								'background': '#ffffff',
								'padding': '12px',
								'border-radius': '8px',
                                'border': '1px solid #e0dff0'
							},
							':focus': {
								'color': '#3b3663',
                                'border': '1px solid #6254e7'
							},
							'.invalid': {
								'color': '#D45769',
                                'border': '1px solid #D45769'
							},
							'.valid': {
								'color': '#3b3663',
                                'border': '1px solid #6254e7'
							}
						}
					});

					// Check eligibility and render card fields
					if (cardFieldsInstance.isEligible()) {
						const nameField = cardFieldsInstance.NameField();
						nameField.render('#card-holder-name');

						const numberField = cardFieldsInstance.NumberField();
						numberField.render('#card-number-field-container');

						const expiryField = cardFieldsInstance.ExpiryField();
						expiryField.render('#card-expiry-field-container');

						const cvvField = cardFieldsInstance.CVVField();
						cvvField.render('#card-cvv-field-container');

						// Show card fields container
						$('#card-fields-container').show();

						// Handle card field submission
						$('#card-field-submit-button').off('click').on('click', async function() {
							$('#card-field-errors').hide();
							
							const cardholderName = $('#card-holder-name').val();
							if (!cardholderName) {
								$('#card-field-errors').text(translations.cardholderName).show();
								return;
							}

							// Show loading state
							$('#card-field-button-text').hide();
							$('#card-field-button-loading').show();
							$('#card-field-submit-button').prop('disabled', true);

							try {
								// Submit card fields with billing address
								await cardFieldsInstance.submit({
									cardholderName: cardholderName,
									billingAddress: {
										addressLine1: customer.address_line_1,
										addressLine2: customer.address_line_2,
										adminArea2: customer.city,
										adminArea1: customer.state,
										postalCode: customer.postal_code,
										countryCode: customer.country
									}
								});
							} catch (error) {
								// Hide loading state on error
								$('#card-field-button-text').show();
								$('#card-field-button-loading').hide();
								$('#card-field-submit-button').prop('disabled', false);
								
								// Error will be handled by onError callback
								console.error('Card submission error:', error);
							}
						});

						console.log('Card fields rendered successfully');
					} else {
						console.log('Card fields not eligible, hiding container');
						$('#card-fields-container').hide();
					}
				}

				// Render PayPal Buttons
				paypal.Buttons({
					style: {
						layout: 'vertical',
						color: 'gold',
						shape: 'rect',
						label: 'paypal'
					},

					// Create order on server
					createOrder: function(data, actions) {
						
						// Create order via AJAX
						return fetch(paypalCreateUrl, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
										'Accept': 'application/json',
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							body: '{}'
						})
						.then(function(response) {
							return response.json();
						})
						.then(function(data) {
							if (data.success) {
								return data.order_id;
							} else {
								showPaymentError('Error: ' + (data.error || 'Failed to create order'));
								return actions.reject();
							}
						})
						.catch(function(error) {
							console.error('Error creating PayPal order:', error);
							showPaymentError('An error occurred while creating the order. Please try again.');
							return actions.reject();
						});
					},

					// Capture payment on approval
					onApprove: function(data, actions) {
						return fetch('{{ route("checkout.paypal.capture") }}', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
										'Accept': 'application/json',
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},
							body: JSON.stringify({
								order_id: data.orderID
							})
						})
						.then(function(response) {
							return response.json();
						})
						.then(function(data) {
							if (data.success) {
								// Redirect to success page
								window.location.href = data.redirect_url;
							} else {
								showPaymentError('Payment failed: ' + (data.error || 'Unknown error'));
							}
						})
						.catch(function(error) {
							console.error('Error capturing PayPal payment:', error);
							showPaymentError('An error occurred while processing your payment. Please contact support.');
						});
					},

					// Handle errors
					onError: function(err) {
						console.error('PayPal error:', err);
						showPaymentError('An error occurred with PayPal. Please try again or contact support.');
					},

					// Handle cancellation
					onCancel: function(data) {
						showPaymentError(translations.paymentCancelled);
					}
				}).render('#paypal-button-container');
			}

			@endif

			@if($paymentGateway === 'stripe')
			initStripeElements();
			@elseif($paymentGateway === 'square')
			initSquareCard();
			@elseif($paymentGateway === 'paypal')
			initPayPalButton();
			@endif
		});
	</script>
	@endpush
</x-app-layout>
