@php $emailTitle = __('emails.account_request_subject') @endphp
@include('emails.partials.header')

    <p class="email-greeting">{{ __('emails.account_request_subject') }}</p>

    <div class="card">
        <div class="detail-row">
            <span class="detail-label">{{ __('account.request_type') }}</span>
            <span class="detail-value">{{ __('account.type_' . $accountRequest->type) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('account.customer') }}</span>
            <span class="detail-value">{{ $accountRequest->user->name }} ({{ $accountRequest->user->customer_number }})<br>{{ $accountRequest->user->email }}</span>
        </div>
        @if($accountRequest->order)
        <div class="detail-row">
            <span class="detail-label">{{ __('account.order') }}</span>
            <span class="detail-value">#{{ $accountRequest->order->id }}</span>
        </div>
        @endif
    </div>

    @if($accountRequest->reason)
    <div class="card" style="border-left: 4px solid #6254e7; border-radius: 0 10px 10px 0;">
        <p style="margin:0; white-space: pre-wrap; font-size:14px;">{{ $accountRequest->reason }}</p>
    </div>
    @endif

@php $automatedNote = __('emails.contact_footer_automated') @endphp
@include('emails.partials.email-footer')
