@props(['buttonLabel' => 'Return Back'])
<div class="modal fade alert-modal" id="alertModal" tabindex="-1" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-body">
				<div class="text-end mb-3">
					<button type="button" class="btn btn-link" data-bs-dismiss="modal">
						<x-icons.x-close />
					</button>
				</div>
				<div class="text-center">
					@if(session('alert_success'))
						<img src="{{ asset('assets/img/img-submission-successful.png') }}" class="w-100">
						<div class="my-5">
							<h3>Submission Successful</h3>
							<p>
								Your message was sent to GBP Secure Local. Just be patient and we will get back to you
							</p>
						</div>
						<button class="btn btn-primary w-100" data-bs-dismiss="modal">{{ $buttonLabel }}</button>
					@elseif(session('alert_error'))
						<img src="{{ asset('assets/img/img-submission-failed.png') }}" class="w-100">
						<div class="my-5">
							<h3>Submission Failed</h3>
							<p>
								Something went wrong. Try again to get one step closer to contacting GBP Secure Local.
							</p>
						</div>
						<button class="btn btn-danger w-100" data-bs-dismiss="modal">Try Again</button>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<script>
    $(function() {
        const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
        @if (session('alert_success') || session('alert_error'))
			alertModal.show();
		@endif
    });
</script>
