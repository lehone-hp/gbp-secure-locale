<x-auth-card :title="__('account.forgot_title')" :intro="__('account.forgot_intro')">
	<form method="POST" action="{{ route('password.email') }}">
		@csrf
		<x-field name="email" type="email" :label="__('account.email')" required autofocus />
		<button type="submit" class="btn btn-primary w-100 mt-2">{{ __('account.send_reset_link') }}</button>
	</form>

	<p class="text-center mt-4 mb-0"><a href="{{ route('account.login') }}">{{ __('account.sign_in') }}</a></p>
</x-auth-card>
