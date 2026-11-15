@props(['title', 'tab' => null])

<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ $title }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
	</x-page-header>

	<div class="page-account">
		<div class="container">
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			@endif

			@if($tab)
				<div class="account-bar">
					<ul class="nav nav-pills account-tabs">
						@foreach(['orders' => 'orders', 'profile' => 'profile', 'security' => 'security', 'requests' => 'requests'] as $key => $route)
							<li class="nav-item">
								<a class="nav-link {{ $tab === $key ? 'active' : '' }}" href="{{ route('account.' . $route) }}">{{ __('account.tab_' . $key) }}</a>
							</li>
						@endforeach
					</ul>
					<form method="POST" action="{{ route('account.logout') }}">
						@csrf
						<button type="submit" class="btn btn-link account-logout">{{ __('account.sign_out') }}</button>
					</form>
				</div>
			@endif

			{{ $slot }}
		</div>
	</div>
</x-app-layout>
