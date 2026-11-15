@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="page-title mb-4">Add New Certification</h4>

                    <form method="POST" action="{{ route('admin.certifications.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Language Tabs --}}
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($locales as $index => $locale)
                                <li class="nav-item">
                                    <a class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                       data-toggle="tab"
                                       href="#tab-{{ $locale }}"
                                       role="tab">
                                        <i class="mdi mdi-translate"></i> {{ $localeNames[$locale] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content border border-top-0 p-3 mb-3">
                            @foreach($locales as $index => $locale)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                     id="tab-{{ $locale }}"
                                     role="tabpanel">
                                    
                                    <div class="form-group">
                                        <label>Name ({{ $localeNames[$locale] }}) <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="name[{{ $locale }}]"
                                               value="{{ old('name.' . $locale) }}"
                                               class="form-control @error('name.' . $locale) is-invalid @enderror">
                                        @error('name.' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <label>Certification Image <span class="text-danger">*</span></label>
                            <input type="file"
                                   name="image"
                                   class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg"
                                   onchange="previewImage(event)">
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG. Max size: 2MB</small>
                            
                            <div id="image-preview" class="mt-2" style="display:none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Display Order <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="display_order"
                                   value="{{ old('display_order', 0) }}"
                                   class="form-control @error('display_order') is-invalid @enderror"
                                   min="0">
                            @error('display_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Save Certification
                            </button>
                            <a href="{{ route('admin.certifications.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-cancel"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('image-preview');
        preview.src = reader.result;
        previewContainer.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
@endsection
