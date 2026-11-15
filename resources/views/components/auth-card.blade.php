@props(['title', 'intro' => null])

<x-account-layout :title="$title">
	<div class="row justify-content-center">
		<div class="col-lg-6 col-xl-5">
			<div class="auth-card">
				@if($intro)
					<p class="text-muted mb-4">{{ $intro }}</p>
				@endif
				{{ $slot }}
			</div>
		</div>
	</div>
</x-account-layout>
