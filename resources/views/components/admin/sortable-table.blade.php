@props(['modelClass'])

<div class="table-responsive position-relative sortable-table"
     data-class="{{ $modelClass }}"
     data-action="{{ route('models.rank.update') }}">
	
	<x-admin.ajax-loader/>
	
	{{ $slot }}
</div>
