@extends('admin.layouts.app')
@section('title', 'Contract Template')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Contract Template <small class="text-muted">(active: {{ $template->version }})</small></h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-9">
        <div class="card card-body">
            <form id="contractForm" method="POST" action="{{ route('admin.contract-template.update') }}">
                @csrf
                @method('PUT')
                <textarea name="body" class="tiny-textarea" rows="30">{{ old('body', $template->body) }}</textarea>
                @error('body')<span class="text-danger">{{ $message }}</span>@enderror

                <div class="form-inline mt-3">
                    <button type="submit" class="btn btn-primary mr-3">
                        <i class="mdi mdi-content-save"></i> Save as new version
                    </button>
                    <select name="order_id" class="form-control mr-2" form="contractForm">
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">#{{ $order->id }} - {{ $order->customer_full_name }} ({{ $order->payment_status }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-info" formaction="{{ route('admin.contract-template.preview') }}" formtarget="_blank">
                        <i class="mdi mdi-eye"></i> Preview PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card card-body">
            <h5>Placeholders</h5>
            <p class="text-muted small">Type these inside the template; they are replaced with order data.</p>
            @foreach($placeholders as $placeholder)
                <code class="d-block mb-1">&#123;&#123;{{ $placeholder }}&#125;&#125;</code>
            @endforeach
        </div>
        <div class="card card-body">
            <h5>Versions</h5>
            @foreach($versions as $version)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $version->version }} <small class="text-muted">{{ $version->created_at->format('Y-m-d') }}</small>
                        @if($version->is_active)<span class="badge badge-success">active</span>@endif
                    </span>
                    @unless($version->is_active)
                        <form method="POST" action="{{ route('admin.contract-template.restore', $version) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-secondary">Restore</button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
