<x-auth-card :title="__('account.reset_title')">
	<form method="POST" action="{{ route('password.update') }}">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}">
		<x-field name="email" type="email" :label="__('account.email')" :value="$email" required autocomplete="username" />
		<x-field name="password" type="password" :label="__('account.new_password')" :hint="__('account.password_rules')" required autofocus autocomplete="new-password" />
		<x-field name="password_confirmation" type="password" :label="__('account.confirm_password')" required autocomplete="new-password" />
		<button type="submit" class="btn btn-primary w-100 mt-2">{{ __('account.reset_password') }}</button>
	</form>
</x-auth-card>
