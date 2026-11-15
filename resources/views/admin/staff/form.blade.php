@extends('admin.layouts.app')
@section('title', 'Our Team')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Our Team</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
                <h4 class="page-title">Our Team</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data"
                  action="{{ $staffMember->id ? route('admin.staff.update', $staffMember->id) : route('admin.staff.store') }}">
                @csrf
                
                @if($staffMember->id)
                    @method('PUT')
                @endif
                
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Full Name <em>*</em></label>
                            <input type="text" name="name"
                                   placeholder="e.g. John Doe"
                                   value="{{ old('name', $staffMember->name) }}"
                                   class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}">
                            @error('name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Position <em>*</em></label>
                            <input type="text" name="position"
                                   placeholder="e.g. General Manager"
                                   value="{{ old('position', $staffMember->position) }}"
                                   class="form-control {{ $errors->has('position') ? ' is-invalid' : '' }}">
                            @error('position')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="image" accept="image/*"
                                   class="form-control {{ $errors->has('image') ? ' is-invalid' : '' }}">
                            @error('image')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
    
    
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Facebook</label>
                            <input type="text" name="facebook_url"
                                   placeholder="e.g. http://facebook.com/johndoe"
                                   value="{{ old('facebook_url', $staffMember->facebook_url) }}"
                                   class="form-control {{ $errors->has('facebook_url') ? ' is-invalid' : '' }}">
                            @error('facebook_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Twitter</label>
                            <input type="text" name="twitter_url"
                                   placeholder="e.g. http://twitter.com/johndoe"
                                   value="{{ old('twitter_url', $staffMember->twitter_url) }}"
                                   class="form-control {{ $errors->has('twitter_url') ? ' is-invalid' : '' }}">
                            @error('twitter_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>LinkedIn</label>
                            <input type="text" name="linkedin_url"
                                   placeholder="e.g. http://linkedin.com/johndoe"
                                   value="{{ old('linkedin_url', $staffMember->linkedin_url) }}"
                                   class="form-control {{ $errors->has('linkedin_url') ? ' is-invalid' : '' }}">
                            @error('linkedin_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="form-group">
                            <label>Instagram</label>
                            <input type="text" name="instagram_url"
                                   placeholder="e.g. http://instagram.com/johndoe"
                                   value="{{ old('instagram_url', $staffMember->instagram_url) }}"
                                   class="form-control {{ $errors->has('instagram_url') ? ' is-invalid' : '' }}">
                            @error('instagram_url')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="text-right">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success" type="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>

@endsection
