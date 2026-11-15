<x-app-layout>
	<x-page-header>
		<x-slot:title>{{ __('services.our_services') }}</x-slot:title>
		<li class="breadcrumb-item"><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
		<li class="breadcrumb-item active" aria-current="page">{{ __('services.breadcrumb_services') }}</li>
	</x-page-header>
	
	@include('web.sections.our-services')
	
	@include('web.sections.contact-cta')
</x-app-layout>
