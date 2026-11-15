@extends('admin.layouts.app')
@section('title', 'Settings')

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
				<h4 class="page-title">Settings</h4>
			</div>
		</div>
	</div>
	
	<form action="{{ route('admin.settings.update') }}" method="POST">
		@csrf
		@method('PUT')
		<div class="card card-body mb-3">
			<h4>Contact Information</h4>
			<p class="text-muted">This info will be displayed on the site. (contact form, footer, etc)</p>
			
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Contact Email <em>*</em></label>
						<input type="email" value="{{ old('contact_email', $settings['contact_email']) }}"
						       class="form-control @error('contact_email') is-invalid @enderror"
						       name="contact_email">
						@error('contact_email')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Contact Email 2 </label>
						<input type="email" value="{{ old('contact_email_2', $settings['contact_email_2']) }}"
						       class="form-control @error('contact_email_2') is-invalid @enderror"
						       name="contact_email_2">
						@error('contact_email_2')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Contact Phone <em>*</em></label>
						<input type="text" value="{{ old('contact_phone', $settings['contact_phone']) }}"
						       class="form-control @error('contact_phone') is-invalid @enderror"
						       name="contact_phone">
						@error('contact_phone')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Contact Phone 2</label>
						<input type="text" value="{{ old('contact_phone_2', $settings['contact_phone_2']) }}"
						       class="form-control @error('contact_phone_2') is-invalid @enderror"
						       name="contact_phone_2">
						@error('contact_phone_2')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>
		</div>
		
		<div class="card card-body mb-3">
			<h4>Notification Emails</h4>
			<p class="text-muted">These emails will be used to notify you about events that occur on the site.</p>
			
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Default Email <em>*</em></label>
						<input type="email" value="{{ old('notify_email', $settings['notify_email']) }}"
						       class="form-control @error('notify_email') is-invalid @enderror"
						       name="notify_email">
						@error('notify_email')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Notify Email </label>
						<input type="email" value="{{ old('notify_email_2', $settings['notify_email_2']) }}"
						       class="form-control @error('notify_email_2') is-invalid @enderror"
						       name="notify_email_2">
						@error('notify_email_2')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				{{-- <div class="col-md-6">
					 <div class="form-group">
						 <label>Job Application <em>*</em></label>
						 <input type="email" value="{{ old('job_notify_email', $settings['job_notify_email']) }}"
								class="form-control @error('job_notify_email') is-invalid @enderror"
								name="job_notify_email">
						 @error('job_notify_email')
						 <span class="invalid-feedback">{{ $message }}</span>
						 @enderror
					 </div>
				 </div>--}}
			
			</div>
		</div>
		
		<div class="card card-body mb-3">
			<h4>About</h4>
			<p class="text-muted">This information will be displayed on the site</p>
			
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Working Hours <em>*</em></label>
						<input type="text" value="{{ old('work_hours', $settings['work_hours']) }}"
						       class="form-control @error('work_hours') is-invalid @enderror"
						       name="work_hours">
						@error('work_hours')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label>Address <em>*</em></label>
						<input type="text" value="{{ old('address', $settings['address']) }}"
						       class="form-control @error('address') is-invalid @enderror"
						       name="address">
						@error('address')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
			</div>
		</div>
		
		<div class="card card-body mb-3">
			<h4>Social Media Links</h4>
			
			
			<div class="form-group">
				<label>Facebook </label>
				<input type="text" value="{{ old('facebook_url', $settings['facebook_url']) }}"
				       class="form-control @error('facebook_url') is-invalid @enderror"
				       name="facebook_url">
				@error('facebook_url')
				<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
			<div class="form-group">
				<label>Twitter </label>
				<input type="text" value="{{ old('twitter_url', $settings['twitter_url']) }}"
				       class="form-control @error('twitter_url') is-invalid @enderror"
				       name="twitter_url">
				@error('twitter_url')
				<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
			<div class="form-group">
				<label>Instagram </label>
				<input type="text" value="{{ old('instagram_url', $settings['instagram_url']) }}"
				       class="form-control @error('instagram_url') is-invalid @enderror"
				       name="instagram_url">
				@error('instagram_url')
				<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
			<div class="form-group">
				<label>LinkedIn </label>
				<input type="text" value="{{ old('linkedin_url', $settings['linkedin_url']) }}"
				       class="form-control @error('linkedin_url') is-invalid @enderror"
				       name="linkedin_url">
				@error('linkedin_url')
				<span class="invalid-feedback">{{ $message }}</span>
				@enderror
			</div>
		</div>
		
		<div class="card card-body mb-3">
			<h4>Payment Settings</h4>
			<p class="text-muted">Configure payment processing settings.</p>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label>Square USD → EUR Exchange Rate <em>*</em></label>
						<input type="number" step="0.0001" min="0.0001"
						       value="{{ old('square_exchange_rate', $settings['square_exchange_rate'] ?? 1.0) }}"
						       class="form-control @error('square_exchange_rate') is-invalid @enderror"
						       name="square_exchange_rate">
						<small class="text-muted">Applied when charging via Square. e.g. 0.92 converts $100 USD → €92 EUR.</small>
						@error('square_exchange_rate')
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				@foreach(['cad' => 'CAD', 'eur' => 'EUR'] as $key => $code)
				<div class="col-md-4">
					<div class="form-group">
						<label>Storefront USD → {{ $code }} Rate <em>*</em></label>
						<input type="number" step="0.0001" min="0.0001"
						       value="{{ old('rate_' . $key, $settings['rate_' . $key] ?? config('currency.rates.' . $code)) }}"
						       class="form-control @error('rate_' . $key) is-invalid @enderror"
						       name="rate_{{ $key }}">
						<small class="text-muted">Units of {{ $code }} per 1 USD shown and charged when a visitor picks {{ $code }}. Existing orders keep the rate they were priced at.</small>
						@error('rate_' . $key)
						<span class="invalid-feedback">{{ $message }}</span>
						@enderror
					</div>
				</div>
				@endforeach
			</div>
		</div>

		<div class="text-right mb-4">
			<button type="submit" class="btn btn-info">Save Changes</button>
		</div>
	</form>
@endsection

@section('footer_script')
	<script></script>
@endsection
