@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Service Info Card --}}
            <div class="card bg-primary text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $service->name }}</h4>
                            <p class="mb-0"><small>{{ $service->caption }}</small></p>
                        </div>
                        <a href="{{ route('admin.services.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Back to Services
                        </a>
                    </div>
                </div>
            </div>

            {{-- Addons List --}}
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="page-title mb-0">Manage Addons</h4>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('admin.services.addons.create', $service->id) }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Addon
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name (EN)</th>
                                    <th>Name (FR)</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($service->addons as $addon)
                                    <tr>
                                        <td>{{ $addon->id }}</td>
                                        <td>{{ $addon->getTranslation('name', 'en', false) }}</td>
                                        <td>{{ $addon->getTranslation('name', 'fr', false) }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $addon->getPricingOption() }}
                                            </span>
                                        </td>
                                        <td><strong>${{ number_format($addon->price, 2) }}</strong></td>
                                        <td>
                                            <a href="{{ route('admin.addons.edit', $addon->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            
                                            <form action="{{ route('admin.addons.destroy', $addon->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this addon?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            No addons found for this service.
                                            <a href="{{ route('admin.services.addons.create', $service->id) }}">Add one now</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
