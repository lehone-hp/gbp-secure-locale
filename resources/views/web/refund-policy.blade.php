<x-legal-page :title="__('legal.refund_policy')">
	{!! __('legal.refund_policy_content', ['company' => e(config('invoice.company.name')), 'contact_email' => e(settings('contact_email'))]) !!}
</x-legal-page>
