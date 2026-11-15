@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="page-title mb-0">Manage Services</h4>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Service
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
                                    <th>Slug</th>
                                    <th># Addons</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td>{{ $service->id }}</td>
                                        <td>{{ $service->getTranslation('name', 'en', false) }}</td>
                                        <td>{{ $service->getTranslation('name', 'fr', false) }}</td>
                                        <td><code>{{ $service->slug }}</code>@if($service->is_hidden) <span class="badge badge-warning">Hidden</span>@endif</td>
                                        <td>
                                            <a href="{{ route('admin.services.addons.index', $service->id) }}" class="badge badge-info">
                                                {{ $service->addons_count }} addons
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.services.edit', $service->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            
                                            <form action="{{ route('admin.services.destroy', $service->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this service?')">
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
                                        <td colspan="6" class="text-center">No services found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $services->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
