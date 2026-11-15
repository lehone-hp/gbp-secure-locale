@php $emailTitle = __('emails.contact_title') @endphp
@include('emails.partials.header')

    <p class="email-greeting">{{ __('emails.contact_title') }}</p>
    <p>{{ __('emails.contact_intro') }}</p>

    <p class="section-label">{{ __('emails.contact_info_title') }}</p>
    <div class="card">
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.contact_name') }}</span>
            <span class="detail-value">{{ $name ?? 'N/A' }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.contact_email') }}</span>
            <span class="detail-value"><a href="mailto:{{ $address ?? '' }}" style="color:#6254e7;">{{ $address ?? 'N/A' }}</a></span>
        </div>
        @if(!empty($phone))
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.contact_phone') }}</span>
            <span class="detail-value"><a href="tel:{{ $phone }}" style="color:#6254e7;">{{ $phone }}</a></span>
        </div>
        @endif
        @if(!empty($service))
        <div class="detail-row">
            <span class="detail-label">{{ __('emails.contact_service') }}</span>
            <span class="detail-value">{{ $service }}</span>
        </div>
        @endif
    </div>

    <p class="section-label">{{ __('emails.contact_message_title') }}</p>
    <div class="card" style="border-left: 4px solid #6254e7; border-radius: 0 10px 10px 0;">
        <p style="margin:0; white-space: pre-wrap; font-size:14px;">{{ $clientMessage ?? __('emails.contact_no_message') }}</p>
    </div>

    <p style="font-size:13px; color:#9b99b0; margin-top:24px;">
        {!! __('emails.contact_note', ['name' => $name ?? 'the sender']) !!}
    </p>

@php $automatedNote = __('emails.contact_footer_automated') @endphp
@include('emails.partials.email-footer')
