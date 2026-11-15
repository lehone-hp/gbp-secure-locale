@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="page-title mb-0">Manage Clients</h4>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Client
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
                                    <th width="80">Logo</th>
                                    <th>Name</th>
                                    <th>Website</th>
                                    <th width="120">Display Order</th>
                                    <th width="100">Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>
                                            <img src="{{ $client->logo_url }}" 
                                                 alt="{{ $client->name }}" 
                                                 class="img-thumbnail" 
                                                 style="max-width: 60px; max-height: 60px;">
                                        </td>
                                        <td>{{ $client->name }}</td>
                                        <td>
                                            @if($client->website_url)
                                                <a href="{{ $client->website_url }}" target="_blank" rel="noopener">
                                                    {{ Str::limit($client->website_url, 40) }}
                                                    <i class="mdi mdi-open-in-new"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{{ $client->display_order }}</td>
                                        <td>
                                            @if($client->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.clients.edit', $client->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.clients.destroy', $client->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this client?');">
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
                                        <td colspan="6" class="text-center">No clients found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $clients->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
