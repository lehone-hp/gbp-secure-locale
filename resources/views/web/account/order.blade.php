<x-account-layout :title="__('account.order_number', ['id' => $order->id])" tab="orders">
	<p><a href="{{ route('account.orders') }}">&larr; {{ __('account.back_to_orders') }}</a></p>

	<div class="account-panel">
		<div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
			<div>
				<div class="text-muted small">{{ __('account.order_date') }}</div>
				{{ $order->created_at->format('Y-m-d H:i') }}
			</div>
			<div>
				<div class="text-muted small">{{ __('account.order_status') }}</div>
				<span class="badge status-{{ $order->payment_status }}">{{ __('account.status_' . $order->payment_status) }}</span>
			</div>
			@if($order->payment_status === \App\Models\Order::PAYMENT_STATUS_PAID)
				<div><a href="{{ route('account.orders.invoice', $order) }}" class="btn btn-primary">{{ __('account.download_invoice') }}</a></div>
			@endif
		</div>

		<table class="table order-summary-table">
			<thead>
			<tr>
				<th>{{ __('checkout.item') }}</th>
				<th class="text-end">{{ __('checkout.price') }}</th>
			</tr>
			</thead>
			<tbody>
			@foreach($order->items as $item)
				<tr>
					<td>{{ $item->item_name }}@if($item->item_type) <small class="text-muted">({{ str_replace('_', ' ', $item->item_type) }})</small>@endif</td>
					<td class="text-end">{{ money($item->price, $order->currency) }}</td>
				</tr>
			@endforeach
			</tbody>
			<tfoot>
			<tr>
				<th>{{ __('checkout.total') }}</th>
				<th class="text-end">{{ money($order->total, $order->currency) }} {{ $order->currency }}</th>
			</tr>
			</tfoot>
		</table>
	</div>
</x-account-layout>
