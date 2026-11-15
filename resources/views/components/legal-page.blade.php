@props(['title', 'version' => null])

<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ $title }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
	</x-page-header>

	<div class="page-content">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					@if ($version)
						<p class="text-muted mb-4">{{ $version }}</p>
					@endif

					<div class="content-wrapper">
						{{ $slot }}
					</div>
				</div>
			</div>
		</div>
	</div>

	@include('web.sections.contact-cta')
</x-app-layout>
