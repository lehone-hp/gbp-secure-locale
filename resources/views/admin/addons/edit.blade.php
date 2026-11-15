@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="page-title mb-1">Edit Addon</h4>
                    <p class="text-muted mb-4">Service: <strong>{{ $addon->service->name }}</strong></p>

                    <form method="POST" action="{{ route('admin.addons.update', $addon->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Price <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           name="price" 
                                           value="{{ old('price', $addon->price) }}"
                                           class="form-control @error('price') is-invalid @enderror"
                                           step="0.01"
                                           min="0"
                                           required>
                                    @error('price')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type <span class="text-danger">*</span></label>
                                    <select name="type" 
                                            class="form-control @error('type') is-invalid @enderror">
                                        <option value="">Select Type</option>
                                        @foreach($addonTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('type', $addon->type) == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Payment Link</label>
                            <input type="url"
                                   name="payment_link"
                                   value="{{ old('payment_link', $addon->payment_link) }}"
                                   class="form-control @error('payment_link') is-invalid @enderror"
                                   placeholder="https://">
                            @error('payment_link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Optional. If set, "Buy Now" opens this link in a new tab instead of the checkout.</small>
                        </div>

                        <hr class="my-4">

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

                        <div class="tab-content border border-top-0 p-3">
                            @foreach($locales as $index => $locale)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                     id="tab-{{ $locale }}"
                                     role="tabpanel">
                                    
                                    <div class="form-group">
                                        <label>Name ({{ $localeNames[$locale] }}) <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               name="name_{{ $locale }}" 
                                               value="{{ old('name_' . $locale, $addon->getTranslation('name', $locale, false)) }}"
                                               class="form-control @error('name_' . $locale) is-invalid @enderror"
                                               required>
                                        @error('name_' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Description ({{ $localeNames[$locale] }})</label>
                                        <textarea name="description_{{ $locale }}" 
                                                  class="form-control tiny-textarea @error('description_' . $locale) is-invalid @enderror" 
                                                  rows="5">{{ old('description_' . $locale, $addon->getTranslation('description', $locale, false)) }}</textarea>
                                        @error('description_' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Rich text editor enabled</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update Addon
                            </button>
                            <a href="{{ route('admin.services.addons.index', $addon->service_id) }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to Addons
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
