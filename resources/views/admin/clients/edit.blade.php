@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="page-title mb-4">Edit Client</h4>

                    <form method="POST" action="{{ route('admin.clients.update', $client->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Client Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $client->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Current Logo</label>
                            <div class="mb-2">
                                <img src="{{ $client->logo_url }}" 
                                     alt="{{ $client->name }}" 
                                     class="img-thumbnail" 
                                     style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Replace Logo (Optional)</label>
                            <input type="file"
                                   name="logo"
                                   class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                                   onchange="previewLogo(event)">
                            @error('logo')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: JPG, JPEG, PNG, SVG. Max size: 2MB</small>
                            
                            <div id="logo-preview" class="mt-2" style="display:none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Website URL (Optional)</label>
                            <input type="url"
                                   name="website_url"
                                   value="{{ old('website_url', $client->website_url) }}"
                                   class="form-control @error('website_url') is-invalid @enderror"
                                   placeholder="https://example.com">
                            @error('website_url')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Full URL including https://</small>
                        </div>

                        <div class="form-group">
                            <label>Display Order <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="display_order"
                                   value="{{ old('display_order', $client->display_order) }}"
                                   class="form-control @error('display_order') is-invalid @enderror"
                                   min="0">
                            @error('display_order')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Lower numbers appear first</small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update Client
                            </button>
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
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
function previewLogo(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('logo-preview');
        preview.src = reader.result;
        previewContainer.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
@endpush
@endsection
