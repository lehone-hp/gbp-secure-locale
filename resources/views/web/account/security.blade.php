<x-account-layout :title="__('account.security_title')" tab="security">
	<div class="account-panel">
		<form method="POST" action="{{ route('account.security.update') }}" class="account-form-narrow">
			@csrf
			@method('PUT')
			<x-field name="current_password" type="password" :label="__('account.current_password')" required autocomplete="current-password" />
			<x-field name="password" type="password" :label="__('account.new_password')" :hint="__('account.password_rules')" required autocomplete="new-password" />
			<x-field name="password_confirmation" type="password" :label="__('account.confirm_password')" required autocomplete="new-password" />
			<button type="submit" class="btn btn-primary">{{ __('account.save') }}</button>
		</form>
	</div>
</x-account-layout>
