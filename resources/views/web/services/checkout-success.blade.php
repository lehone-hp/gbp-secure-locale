<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ __('services.payment_successful') }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('navigation.services') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ __('services.breadcrumb_success') }}</li>
	</x-page-header>

	<div class="page-checkout">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="bg-light p-5 text-center">
						<div class="mb-4">
							<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="40" cy="40" r="40" fill="#6254e7"/>
								<path d="M25 40L35 50L55 30" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>

						<h2 class="mb-3">{{ __('services.thank_you') }}</h2>
						<p class="mb-4">{{ __('services.payment_processed') }}</p>

						<div class="bg-light p-4 mb-4">
							<div class="row text-start">
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.order_id') }}</small>
									<strong>#{{ $order->id }}</strong>
								</div>
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.transaction_id') }}</small>
									<strong>{{ $order->payment_transaction_id ?? 'N/A' }}</strong>
								</div>
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.customer') }}</small>
									<strong>{{ $order->customer_full_name }}</strong>
								</div>
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.email') }}</small>
									<strong>{{ $order->customer_email }}</strong>
								</div>
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.total_amount') }}</small>
									<strong class="text-primary">{{ money($order->total, $order->currency) }} {{ $order->currency }}</strong>
								</div>
								<div class="col-md-6 mb-3">
									<small class="text-muted d-block">{{ __('services.payment_status') }}</small>
									<strong class="text-success">{{ ucfirst($order->payment_status) }}</strong>
								</div>
							</div>
						</div>

						<div class="mb-4">
							<h5 class="mb-3">{{ __('services.order_items') }}</h5>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th class="text-start">{{ __('services.item') }}</th>
										<th>{{ __('services.type') }}</th>
										<th class="text-end">{{ __('services.price') }}</th>
									</tr>
								</thead>
								<tbody>
									@foreach($order->items as $item)
									<tr>
										<td class="text-start">{{ $item->item_name }}</td>
										<td>{{ $item->item_type ? ucfirst(str_replace('_', ' ', $item->item_type)) : '-' }}</td>
										<td class="text-end">{{ money($item->price, $order->currency) }}</td>
									</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2" class="text-start">{{ __('services.total') }}</th>
										<th class="text-end">{{ money($order->total, $order->currency) }} {{ $order->currency }}</th>
									</tr>
								</tfoot>
							</table>
						</div>

						<p class="text-muted mb-4">
							{!! __('services.confirmation_sent', ['email' => $order->customer_email]) !!}
						</p>

						<div class="d-flex gap-3 justify-content-center">
							<a href="{{ route('index') }}" class="btn btn-primary py-3 px-5">{{ __('services.back_to_home') }}</a>
							<a href="{{ route('services.index') }}" class="btn btn-secondary py-3 px-5">{{ __('services.browse_services') }}</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
