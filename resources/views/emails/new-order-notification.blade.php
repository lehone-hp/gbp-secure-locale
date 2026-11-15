@php $emailTitle = __('emails.new_order_title') @endphp
@include('emails.partials.header')

    <p class="email-greeting">{{ __('emails.new_order_title') }} — #{{ $order->id }}</p>

    <div class="alert-box">
        {!! __('emails.action_required') !!}
    </div>

    <p class="section-label">{{ __('emails.order_summary_title') }}</p>
    <div class="card">
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.order_id') }}</span>
            <span class="detail-value accent">#{{ $order->id }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.order_date') }}</span>
            <span class="detail-value">{{ $order->created_at->format('F d, Y H:i:s') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.total_amount') }}</span>
            <span class="detail-value accent">{{ money($order->total, $order->currency) }} {{ $order->currency }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.payment_status') }}</span>
            <span class="detail-value accent">{{ ucfirst($order->payment_status) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.payment_gateway') }}</span>
            <span class="detail-value">{{ ucfirst($order->payment_gateway) }}</span>
        </div>
        @if($order->payment_transaction_id)
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.transaction_id') }}</span>
            <span class="detail-value">{{ $order->payment_transaction_id }}</span>
        </div>
        @endif
    </div>

    <p class="section-label">{{ __('emails.customer_info_title') }}</p>
    <div class="card">
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.customer_name') }}</span>
            <span class="detail-value">{{ $order->customer_full_name }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.customer_email') }}</span>
            <span class="detail-value"><a href="mailto:{{ $order->customer_email }}" style="color:#6254e7;">{{ $order->customer_email }}</a></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.customer_phone') }}</span>
            <span class="detail-value"><a href="tel:{{ $order->customer_phone }}" style="color:#6254e7;">{{ $order->customer_phone }}</a></span>
        </div>
        @if($order->customer_company)
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.customer_company') }}</span>
            <span class="detail-value">{{ $order->customer_company }}</span>
        </div>
        @endif
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.customer_address') }}</span>
            <span class="detail-value">
                {{ $order->address_line_1 }}@if($order->address_line_2), {{ $order->address_line_2 }}@endif<br>
                {{ $order->city }}, {{ $order->state }} {{ $order->postal_code }}<br>
                {{ $order->country }}
            </span>
        </div>
    </div>

    <p class="section-label">{{ __('emails.order_items_title') }}</p>
    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('emails.item') }}</th>
                    <th>{{ __('emails.type') }}</th>
                    <th>{{ __('emails.quantity') }}</th>
                    <th>{{ __('emails.price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ $item->item_type ? ucfirst(str_replace('_', ' ', $item->item_type)) : '—' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ money($item->subtotal, $order->currency) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right; padding-right:16px;">{{ __('emails.total') }}</td>
                    <td class="accent">{{ money($order->total, $order->currency) }} {{ $order->currency }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($order->notes)
    <p class="section-label">{{ __('emails.customer_notes_title') }}</p>
    <div class="card">
        <p style="margin:0; font-size:14px;">{{ $order->notes }}</p>
    </div>
    @endif

    <p style="margin-top: 28px; font-size:14px; color:#9b99b0;">{{ __('emails.admin_login_prompt') }}</p>

@php $automatedNote = __('emails.automated_notification') @endphp
@include('emails.partials.email-footer')
