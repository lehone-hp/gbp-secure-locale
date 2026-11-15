<x-account-layout :title="__('account.profile_title')" tab="profile">
	<div class="account-panel">
		<p class="mb-1"><strong>{{ $user->email }}</strong></p>
		<p class="text-muted small mb-1">{{ __('account.email_locked', ['email' => settings('contact_email')]) }}</p>
		<p class="text-muted small mb-4">{{ __('account.customer_number') }} : {{ $user->customer_number }}</p>

		<form method="POST" action="{{ route('account.profile.update') }}">
			@csrf
			@method('PUT')
			<div class="row">
				<div class="col-md-6"><x-field name="first_name" :label="__('account.first_name')" :value="$user->first_name" required /></div>
				<div class="col-md-6"><x-field name="last_name" :label="__('account.last_name')" :value="$user->last_name" required /></div>
				<div class="col-md-6"><x-field name="phone" type="tel" :label="__('account.phone')" :value="$user->phone" /></div>
				<div class="col-md-6"><x-field name="company" :label="__('account.company')" :value="$user->company" /></div>
				<div class="col-md-6 mb-3">
					<label for="country" class="form-label">{{ __('account.country') }}</label>
					<select id="country" name="country" class="form-control @error('country') is-invalid @enderror">
						<option value="">{{ __('account.select_country') }}</option>
						@foreach($countries as $code => $name)
							<option value="{{ $code }}" @selected(old('country', $user->country) == $code)>{{ $name }}</option>
						@endforeach
					</select>
					@error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
				</div>
			</div>
			<button type="submit" class="btn btn-primary">{{ __('account.save') }}</button>
		</form>
	</div>
</x-account-layout>
