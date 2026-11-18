@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="page-title mb-0">Order Details #{{ $order->id }}</h4>
                <div class="d-flex gap-2 pt-2">
                    @if($order->payment_status === 'paid')
                    <button type="button" class="btn btn-primary mr-2"
                            data-toggle="modal" data-target="#resendEmailModal">
                        <i class="mdi mdi-email-send"></i> Resend Confirmation Email
                    </button>
                    <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-success mr-2">
                        <i class="mdi mdi-download"></i> Download Invoice PDF
                    </a>
                    <a href="{{ route('admin.orders.contract', $order) }}" class="btn btn-success mr-2">
                        <i class="mdi mdi-file-document-outline"></i> Download Contract PDF
                    </a>
                    @endif
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="mdi mdi-arrow-left"></i> Back to Orders
                    </a>
                </div>
            </div>

            <div class="row">
                {{-- Order Summary --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Order ID:</th>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Invoice Number:</th>
                                    <td><strong>{{ $invoiceNumber }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td>{{ $order->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Total Amount:</th>
                                    <td><strong class="text-primary">{{ money($order->total, $order->currency) }} {{ $order->currency }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Payment Status:</th>
                                    <td>
                                        @if($order->payment_status === 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @elseif($order->payment_status === 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Gateway:</th>
                                    <td>{{ ucfirst($order->payment_gateway) }}</td>
                                </tr>
                                @if($order->payment_transaction_id)
                                <tr>
                                    <th>Transaction ID:</th>
                                    <td><code>{{ $order->payment_transaction_id }}</code></td>
                                </tr>
                                @endif
                                @if($order->payment_response)
                                <tr>
                                    <th>Gateway Payload:</th>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-info" data-toggle="modal" data-target="#paymentDetailsModal">
                                            <i class="mdi mdi-code-json"></i> Show Details
                                        </button>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Customer Information --}}
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Customer Information</h5>
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Name:</th>
                                    <td>{{ $order->customer_full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></td>
                                </tr>
                                @if($order->customer_company)
                                <tr>
                                    <th>Company:</th>
                                    <td>{{ $order->customer_company }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        {{ $order->address_line_1 }}<br>
                                        @if($order->address_line_2)
                                            {{ $order->address_line_2 }}<br>
                                        @endif
                                        {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                                        {{ $order->country }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Item</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>{{ $item->item_name }}</td>
                                                <td>
                                                    @if($item->item_type)
                                                        <span class="badge badge-info">
                                                            {{ ucfirst(str_replace('_', ' ', $item->item_type)) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $item->quantity }}</td>
                                                <td class="text-right">{{ money($item->price, $order->currency) }}</td>
                                                <td class="text-right"><strong>{{ money($item->subtotal, $order->currency) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-right">Total:</th>
                                            <th class="text-right text-primary">
                                                {{ money($order->total, $order->currency) }} {{ $order->currency }}
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($order->notes)
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Customer Notes</h5>
                            <p class="mb-0">{{ $order->notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

{{-- Resend Email Modal --}}
<div class="modal fade" id="resendEmailModal" tabindex="-1" role="dialog" aria-labelledby="resendEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.orders.resend-email', $order) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="resendEmailModalLabel">
                        <i class="mdi mdi-email-send"></i> Resend Confirmation Email
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        The order confirmation email with invoice <strong>{{ $invoiceNumber }}</strong>
                        will be sent to the address below.
                    </p>
                    <div class="form-group mb-0">
                        <label for="resend_email">Recipient Email</label>
                        <input type="email" name="email" id="resend_email"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="{{ $order->customer_email }}">
                        <small class="form-text text-muted">
                            Leave blank to send to the customer's email
                            (<strong>{{ $order->customer_email }}</strong>).
                        </small>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-send"></i> Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($order->payment_response)
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" role="dialog" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailsModalLabel">
                    {{ ucfirst($order->payment_gateway) }} Payment Gateway Payload
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="paymentPayload" class="bg-light p-3 rounded" style="max-height: 450px; overflow-y: auto; font-size: 0.82rem; white-space: pre-wrap; word-break: break-all;">{{ json_encode($order->payment_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="copyPayloadBtn">
                    <i class="mdi mdi-content-copy"></i> Copy
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('copyPayloadBtn').addEventListener('click', function () {
    var text = document.getElementById('paymentPayload').innerText;
    navigator.clipboard.writeText(text).then(function () {
        var btn = document.getElementById('copyPayloadBtn');
        btn.innerHTML = '<i class="mdi mdi-check"></i> Copied!';
        btn.classList.replace('btn-outline-secondary', 'btn-success');
        setTimeout(function () {
            btn.innerHTML = '<i class="mdi mdi-content-copy"></i> Copy';
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    });
});
</script>
@endif

@endsection
