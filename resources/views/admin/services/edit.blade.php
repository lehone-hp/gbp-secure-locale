@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="page-title mb-0">Edit Service</h4>
                        <a href="{{ route('admin.services.addons.index', $service->id) }}" class="btn btn-info">
                            <i class="mdi mdi-puzzle"></i> Manage Addons ({{ $service->addons->count() }})
                        </a>
                    </div>

                    <form method="POST" action="{{ route('admin.services.update', $service->id) }}">
                        @csrf
                        @method('PUT')

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
                                               value="{{ old('name_' . $locale, $service->getTranslation('name', $locale, false)) }}"
                                               class="form-control @error('name_' . $locale) is-invalid @enderror"
                                               >
                                        @error('name_' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label>Caption ({{ $localeNames[$locale] }}) <span class="text-danger">*</span></label>
                                        <input type="text"
                                               name="caption_{{ $locale }}"
                                               value="{{ old('caption_' . $locale, $service->getTranslation('caption', $locale, false)) }}"
                                               class="form-control @error('caption_' . $locale) is-invalid @enderror"
                                               maxlength="500"
                                               >
                                        @error('caption_' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Short tagline (max 500 characters)</small>
                                    </div>

                                    <div class="form-group">
                                        <label>Description ({{ $localeNames[$locale] }}) <span class="text-danger">*</span></label>
                                        <textarea name="description_{{ $locale }}"
                                                  class="form-control @error('description_' . $locale) is-invalid @enderror"
                                                  rows="8"
                                                  >{{ old('description_' . $locale, $service->getTranslation('description', $locale, false)) }}</textarea>
                                        @error('description_' . $locale)
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Rich text editor enabled</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_hidden" name="is_hidden" value="1" {{ old('is_hidden', $service->is_hidden ?? false) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_hidden">Hidden from public site (reachable by direct link only)</label>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Update Service
                            </button>
                            <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
