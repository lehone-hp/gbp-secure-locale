@extends('admin.layouts.app')
@section('title', $page->name)

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.pages.index') }}">Page</a></li>
                        <li class="breadcrumb-item active">{{ $page->name }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Page</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->


    <div class="card">
        <div class="card-body">
            <h3>{{ $page->name }}</h3>
            <hr>
            <form method="POST" enctype="multipart/form-data"
                  action="{{ route('admin.pages.update', $page->id) }}">
                @csrf
                @method('PUT')


                <div class="form-group">
                    <label>Content <em>*</em></label>
                    <textarea class="form-control tiny-textarea {{ $errors->has('content') ? ' is-invalid' : '' }}"
                              rows="5"
                              name="content">{{ old('content', $page->content) }}</textarea>

                    @error('content')
                    <span class="text-danger"> {{ $message }} </span>
                    @enderror
                </div>

                <hr>

                <div class="text-right">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-info" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('footer_script')
    <script></script>
@endsection
