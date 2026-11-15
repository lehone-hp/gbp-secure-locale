@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@if(isset($title)) {{ $title }} | @endif {{ config('app.name') }}</title>

	@if(isset($meta))
		{{ $meta }}
	@endif

	<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
	<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" sizes="any">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap" rel="stylesheet">

	<link href="{{ asset('seoai/css/bootstrap.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('seoai/css/slicknav.min.css') }}" rel="stylesheet">
	<link href="{{ asset('seoai/css/swiper-bundle.min.css') }}" rel="stylesheet">
	<link href="{{ asset('seoai/css/all.min.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('seoai/css/animate.css') }}" rel="stylesheet">
	<link href="{{ asset('seoai/css/magnific-popup.css') }}" rel="stylesheet">
	<link href="{{ asset('seoai/css/theme.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('seoai/css/site.css') }}" rel="stylesheet" media="screen">

	@stack('styles')
</head>
<body>

<div class="preloader">
	<div class="loading-container">
		<div class="loading"></div>
		<div id="loading-icon"><img src="{{ asset('seoai/images/logo-mark.svg') }}" alt=""></div>
	</div>
</div>

@include('layouts.header')

<main>
	{{ $slot }}
</main>

@include('layouts.footer')

<script src="{{ asset('seoai/js/jquery.min.js') }}"></script>
<script src="{{ asset('seoai/js/popper.min.js') }}"></script>
<script src="{{ asset('seoai/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('seoai/js/validator.min.js') }}"></script>
<script src="{{ asset('seoai/js/jquery.slicknav.js') }}"></script>
<script src="{{ asset('seoai/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('seoai/js/jquery.waypoints.min.js') }}"></script>
<script src="{{ asset('seoai/js/counter.min.js') }}"></script>
<script src="{{ asset('seoai/js/jquery.magnific-popup.min.js') }}"></script>
<script src="{{ asset('seoai/js/lenis.min.js') }}"></script>
<script src="{{ asset('seoai/js/parallaxie.js') }}"></script>
<script src="{{ asset('seoai/js/gsap.min.js') }}"></script>
<script src="{{ asset('seoai/js/SplitText.min.js') }}"></script>
<script src="{{ asset('seoai/js/ScrollTrigger.min.js') }}"></script>
<script src="{{ asset('seoai/js/wow.min.js') }}"></script>
<script src="{{ asset('seoai/js/function.js') }}"></script>

@stack('scripts')

</body>
</html>
