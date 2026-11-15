@props(['title', 'total', 'icon', 'url' => '#', 'color' => 'success'])

<a href="{{ $url }}">
	<div class="widget-rounded-circle card-box">
		<div class="row">
			<div class="col-5">
				<div class="avatar-lg rounded-circle bg-soft-{{ $color }} border-{{ $color }} border">
					<i class="{{ $icon }} font-22 avatar-title text-{{ $color }}"></i>
				</div>
			</div>
			<div class="col-7">
				<div class="text-right">
					<h3 class="mt-1">{{ $title }}</h3>
					<p class="text-muted mb-1 text-truncate">Total ({{ $total }}) </p>
				</div>
			</div>
		</div>
	</div>
</a>
