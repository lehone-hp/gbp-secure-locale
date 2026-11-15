@extends('admin.layouts.app')
@section('title', 'FAQs')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">FAQs</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">FAQs</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"
                  action="{{ $faq->id ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}">
                @csrf
                
                @if($faq->id)
                    @method('PUT')
                @endif
    
                <ul class="nav nav-tabs" role="tablist">
                    @foreach($locales as $index => $locale)
                        <li class="nav-item">
                            <a class="nav-link {{ $index === 0 ? 'active' : '' }}" data-toggle="tab" href="#tab-{{ $locale }}" role="tab">
                                <i class="mdi mdi-translate"></i> {{ $localeNames[$locale] }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content border border-top-0 p-3 mb-3">
                    @foreach($locales as $index => $locale)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="tab-{{ $locale }}" role="tabpanel">
                            <div class="form-group">
                                <label>Question ({{ $localeNames[$locale] }}) <em>*</em></label>
                                <input type="text" name="question_{{ $locale }}"
                                       value="{{ old('question_' . $locale, $faq->id ? $faq->getTranslation('question', $locale, false) : '') }}"
                                       class="form-control {{ $errors->has('question_' . $locale) ? ' is-invalid' : '' }}">
                                @error('question_' . $locale)
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Answer ({{ $localeNames[$locale] }}) <em>*</em></label>
                                <textarea class="form-control {{ $errors->has('answer_' . $locale) ? ' is-invalid' : '' }}"
                                          rows="8"
                                          name="answer_{{ $locale }}">{{ old('answer_' . $locale, $faq->id ? $faq->getTranslation('answer', $locale, false) : '') }}</textarea>
                                @error('answer_' . $locale)
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-right">
                    <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

@endsection
