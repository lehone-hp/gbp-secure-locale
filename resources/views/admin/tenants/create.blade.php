@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="page-title mb-0">Create Tenant Configuration</h4>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Validation Error:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <form action="{{ route('admin.tenants.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="domain">Domain <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('domain') is-invalid @enderror" 
                                   id="domain" 
                                   name="domain" 
                                   value="{{ old('domain') }}"
                                   placeholder="e.g., domain1.com"
                                   required>
                            <small class="form-text text-muted">
                                Enter the domain name without http:// or www (e.g., "domain1.com")
                            </small>
                            @error('domain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_gateway">Payment Gateway <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_gateway') is-invalid @enderror" 
                                    id="payment_gateway" 
                                    name="payment_gateway" 
                                    required>
                                <option value="">-- Select Payment Gateway --</option>
                                @foreach($gateways as $key => $name)
                                    <option value="{{ $key }}" {{ old('payment_gateway') == $key ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Select which payment gateway this domain should use. Gateway credentials are configured in your .env file.
                            </small>
                            @error('payment_gateway')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Active
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                If unchecked, this tenant configuration will be ignored and the default gateway will be used.
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="mdi mdi-information"></i>
                            <strong>Note:</strong> Make sure the selected payment gateway is properly configured in your <code>.env</code> file with valid credentials.
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i> Create Tenant
                            </button>
                            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
