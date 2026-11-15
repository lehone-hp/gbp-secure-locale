<x-auth-card :title="__('account.register_title')" :intro="__('account.register_intro')">
	<form method="POST" action="{{ route('account.register') }}">
		@csrf
		<x-field name="email" type="email" :label="__('account.email')" required autofocus autocomplete="username" />
		<x-field name="password" type="password" :label="__('account.password')" :hint="__('account.password_rules')" required autocomplete="new-password" />
		<x-field name="password_confirmation" type="password" :label="__('account.confirm_password')" required autocomplete="new-password" />

		<button type="submit" class="btn btn-primary w-100 mt-2">{{ __('account.create_account') }}</button>
	</form>

	<p class="text-center mt-4 mb-0">{{ __('account.have_account') }} <a href="{{ route('account.login') }}">{{ __('account.sign_in') }}</a></p>
</x-auth-card>
