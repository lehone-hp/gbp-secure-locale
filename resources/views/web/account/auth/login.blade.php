<x-auth-card :title="__('account.login_title')" :intro="__('account.login_intro')">
	<form method="POST" action="{{ route('account.login') }}">
		@csrf
		<x-field name="email" type="email" :label="__('account.email')" required autofocus autocomplete="username" />
		<x-field name="password" type="password" :label="__('account.password')" required autocomplete="current-password" />

		<div class="d-flex justify-content-between align-items-center mb-4">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
				<label class="form-check-label fw-normal" for="remember">{{ __('account.remember_me') }}</label>
			</div>
			<a href="{{ route('password.request') }}">{{ __('account.forgot_password') }}</a>
		</div>

		<button type="submit" class="btn btn-primary w-100">{{ __('account.sign_in') }}</button>
	</form>

	<p class="text-center mt-4 mb-0">{{ __('account.no_account') }} <a href="{{ route('account.register') }}">{{ __('account.create_account') }}</a></p>
</x-auth-card>
