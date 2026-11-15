@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <h4 class="page-title mb-0">Tenant Configurations</h4>
                            <p class="text-muted">Manage domain-specific payment gateway settings</p>
                        </div>
                        <div class="col-sm-6 text-right">
                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus"></i> Add New Tenant
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
                                    <th>Domain</th>
                                    <th>Payment Gateway</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                    <tr>
                                        <td>{{ $tenant->id }}</td>
                                        <td><code>{{ $tenant->domain }}</code></td>
                                        <td>
                                            @php
                                                $badgeClass = match($tenant->payment_gateway) {
                                                    \App\Models\Order::PAYMENT_GATEWAY_BRAINTREE => 'primary',
                                                    \App\Models\Order::PAYMENT_GATEWAY_STRIPE => 'info',
                                                    \App\Models\Order::PAYMENT_GATEWAY_PAYPAL => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ $tenant->getGatewayName() }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($tenant->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.tenants.edit', $tenant->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="mdi mdi-pencil"></i> Edit
                                            </a>
                                            
                                            <form action="{{ route('admin.tenants.destroy', $tenant->id) }}" 
                                                  method="POST" 
                                                  style="display: inline-block;"
                                                  onsubmit="return confirm('Are you sure you want to delete this tenant configuration?')">
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
                                            <p class="text-muted mb-0">No tenant configurations found.</p>
                                            <a href="{{ route('admin.tenants.create') }}" class="btn btn-sm btn-primary mt-2">
                                                Create your first tenant
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $tenants->links() }}
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">About Tenant Configurations</h5>
                    <p class="text-muted mb-2">
                        Tenant configurations allow you to specify which payment gateway each domain should use. 
                        All gateway credentials are configured in your <code>.env</code> file.
                    </p>
                    <ul class="text-muted">
                        <li>Each domain can be assigned to use Braintree, Stripe, or PayPal</li>
                        <li>Gateway credentials are shared across all tenants (configured in .env)</li>
                        <li>If a domain is not configured, it will use the default gateway from .env</li>
                        <li>Domain names should be entered without http:// or www (e.g., "domain.com")</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
