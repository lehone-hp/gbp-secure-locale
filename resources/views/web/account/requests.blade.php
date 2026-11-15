<x-account-layout :title="__('account.requests_title')" tab="requests">
	<div class="account-panel mb-4">
		<p class="text-muted">{{ __('account.requests_intro') }}</p>

		<form method="POST" action="{{ route('account.requests.store') }}" class="account-form-narrow">
			@csrf

			<div class="mb-3">
				<label for="type" class="form-label">{{ __('account.request_type') }}</label>
				<select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
					@foreach(\App\Models\AccountRequest::TYPES as $type)
						<option value="{{ $type }}" @selected(old('type') === $type)>{{ __('account.type_' . $type) }}</option>
					@endforeach
				</select>
				@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<div class="mb-3" id="subscription-field">
				<label for="order_id" class="form-label">{{ __('account.related_subscription') }}</label>
				@if($subscriptions->isEmpty())
					<p class="text-muted small mb-0">{{ __('account.no_subscriptions') }}</p>
				@else
					<select id="order_id" name="order_id" class="form-control @error('order_id') is-invalid @enderror">
						@foreach($subscriptions as $order)
							<option value="{{ $order->id }}" @selected(old('order_id') == $order->id)>#{{ $order->id }} · {{ $order->items->pluck('item_name')->join(', ') }}</option>
						@endforeach
					</select>
				@endif
				@error('order_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
			</div>

			<div class="mb-3">
				<label for="reason" class="form-label">{{ __('account.reason') }}</label>
				<textarea id="reason" name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror">{{ old('reason') }}</textarea>
				@error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
			</div>

			<button type="submit" class="btn btn-primary">{{ __('account.send_request') }}</button>
		</form>
	</div>

	@if($requests->isNotEmpty())
		<div class="account-panel">
			<h4 class="mb-3">{{ __('account.your_requests') }}</h4>
			<ul class="list-unstyled mb-0">
				@foreach($requests as $accountRequest)
					<li class="d-flex justify-content-between border-bottom py-2">
						<span>{{ __('account.type_' . $accountRequest->type) }}@if($accountRequest->order) <small class="text-muted">(#{{ $accountRequest->order->id }})</small>@endif
							<small class="text-muted d-block">{{ $accountRequest->created_at->format('Y-m-d') }}</small></span>
						<span class="badge status-{{ $accountRequest->status }}">{{ __('account.request_status_' . $accountRequest->status) }}</span>
					</li>
				@endforeach
			</ul>
		</div>
	@endif

	@push('scripts')
	<script>
		(function () {
			const type = document.getElementById('type');
			const field = document.getElementById('subscription-field');
			const toggle = function () { field.style.display = type.value === 'close_account' ? 'none' : ''; };
			type.addEventListener('change', toggle);
			toggle();
		})();
	</script>
	@endpush
</x-account-layout>
