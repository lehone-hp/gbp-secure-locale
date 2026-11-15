@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])

<div class="mb-3">
	<label for="{{ $name }}" class="form-label">{{ $label }}@if($required) <span class="text-danger">*</span>@endif</label>
	<input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
	       class="form-control @error($name) is-invalid @enderror"
	       @if($type !== 'password') value="{{ old($name, $value) }}" @endif
	       @required($required) {{ $attributes }}>
	@if($hint)<div class="form-text">{{ $hint }}</div>@endif
	@error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
