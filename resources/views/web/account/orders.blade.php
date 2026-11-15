<x-account-layout :title="__('account.orders_title')" tab="orders">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<p class="mb-0 text-muted">{{ auth()->user()->customer_number }}</p>
		<a href="{{ route('services.index') }}" class="btn btn-primary">{{ __('account.add_service') }}</a>
	</div>

	@if($orders->isEmpty())
		<div class="account-panel text-center text-muted">{{ __('account.no_orders') }}</div>
	@else
		<div class="account-panel p-0">
			<div class="table-responsive">
				<table class="table mb-0 align-middle">
					<thead>
					<tr>
						<th>{{ __('account.order_date') }}</th>
						<th>{{ __('account.order_items') }}</th>
						<th class="text-end">{{ __('account.order_total') }}</th>
						<th>{{ __('account.order_status') }}</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					@foreach($orders as $order)
						<tr>
							<td>{{ $order->created_at->format('Y-m-d') }}<br><small class="text-muted">#{{ $order->id }}</small></td>
							<td>{{ $order->items->pluck('item_name')->join(', ') }}</td>
							<td class="text-end">{{ money($order->total, $order->currency) }} {{ $order->currency }}</td>
							<td><span class="badge status-{{ $order->payment_status }}">{{ __('account.status_' . $order->payment_status) }}</span></td>
							<td class="text-end text-nowrap">
								<a href="{{ route('account.orders.show', $order) }}">{{ __('account.view_order') }}</a>
								@if($order->payment_status === \App\Models\Order::PAYMENT_STATUS_PAID)
									· <a href="{{ route('account.orders.invoice', $order) }}">{{ __('account.download_invoice') }}</a>
								@endif
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	@endif
</x-account-layout>
