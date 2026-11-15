@extends('admin.layouts.app')
@section('title', 'Customer Requests')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="page-title mb-1">Customer Requests</h4>
                    <p class="text-muted mb-4">Requests to suspend or cancel a subscription, or to close an account. Nothing is applied automatically: handle each one, then update its status.</p>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Request</th>
                                <th>Order</th>
                                <th>Reason</th>
                                <th style="min-width: 220px;">Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($requests as $accountRequest)
                                <tr>
                                    <td>{{ $accountRequest->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        {{ $accountRequest->user->name }}<br>
                                        <small class="text-muted">{{ $accountRequest->user->customer_number }} · {{ $accountRequest->user->email }}</small>
                                    </td>
                                    <td>{{ __('account.type_' . $accountRequest->type) }}</td>
                                    <td>
                                        @if($accountRequest->order)
                                            <a href="{{ route('admin.orders.show', $accountRequest->order) }}">#{{ $accountRequest->order->id }}</a>
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td style="max-width: 280px; white-space: pre-wrap;">{{ $accountRequest->reason ?: '—' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.account-requests.update', $accountRequest) }}" class="form-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-control form-control-sm mr-2">
                                                @foreach(\App\Models\AccountRequest::STATUSES as $status)
                                                    <option value="{{ $status }}" @selected($accountRequest->status === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-sm btn-info" type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No requests yet.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
