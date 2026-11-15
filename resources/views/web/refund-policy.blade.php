<x-legal-page :title="__('legal.refund_policy')" :version="__('legal.version_line', ['version' => config('legal.refund_version'), 'date' => config('legal.effective_date')])">
	{!! __('legal.refund_policy_content', ['company' => e(config('invoice.company.name')), 'contact_email' => e(settings('contact_email'))]) !!}
</x-legal-page>
