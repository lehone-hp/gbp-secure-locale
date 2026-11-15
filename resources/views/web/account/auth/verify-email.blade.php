<x-auth-card :title="__('account.verify_title')" :intro="__('account.verify_text')">
	<form method="POST" action="{{ route('verification.send') }}" class="mb-3">
		@csrf
		<button type="submit" class="btn btn-primary w-100">{{ __('account.verify_resend') }}</button>
	</form>

	<form method="POST" action="{{ route('account.logout') }}" class="text-center">
		@csrf
		<button type="submit" class="btn btn-link">{{ __('account.sign_out') }}</button>
	</form>
</x-auth-card>
