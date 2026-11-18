@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    </ol>
                </div>
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    
    <div class="row">
        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Services"
                    total="{{ $servicesCount }}"
                    icon="fe-home"
                    url="{{ route('admin.services.index') }}"
                    color="danger"
            />
        </div>
        
        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Addons"
                    total="{{ $addonsCount }}"
                    icon="fe-truck"
                    url="{{ route('admin.services.index') }}"
                    color="success"
            />
        </div>

        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Orders"
                    total="{{ $ordersCount }}"
                    icon="fe-shopping-cart"
                    url="{{ route('admin.orders.index') }}"
                    color="primary"
            />
        </div>

        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Paid Orders"
                    total="{{ $paidOrdersCount }}"
                    icon="fe-check-circle"
                    url="{{ route('admin.orders.index', ['status' => 'paid']) }}"
                    color="success"
            />
        </div>

        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Pending Orders"
                    total="{{ $pendingOrdersCount }}"
                    icon="fe-clock"
                    url="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                    color="warning"
            />
        </div>

        <div class="col-md-4">
            <x-admin.dashboard.stat-card
                    title="Customers"
                    total="{{ $customersCount }}"
                    icon="fe-users"
                    url="{{ route('admin.orders.index') }}"
                    color="info"
            />
        </div>
    </div>
    <!-- end row-->

    <div class="row">
        <div class="col-12">
            <div class="card-box">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="header-title mb-0">Last 5 Purchases</h4>
                    <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="btn btn-sm btn-primary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Service</th>
                                <th>Total</th>
                                <th>Gateway</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestPurchases as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->customer_full_name }}<br><small class="text-muted">{{ $order->customer_email }}</small></td>
                                    <td>{{ $order->items->first()?->item_name }}</td>
                                    <td><strong>{{ money($order->total, $order->currency) }}</strong> {{ $order->currency }}</td>
                                    <td>{{ ucfirst($order->payment_gateway) }}</td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info">
                                            <i class="mdi mdi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No purchases yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer_script')
    <script></script>
@endsection

