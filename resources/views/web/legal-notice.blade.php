<x-legal-page :title="__('legal.legal_notice')">
	{!! __('legal.legal_notice_content', [
		'company' => e(config('invoice.company.name')),
		'address' => e(config('invoice.company.address_line_1') . ', ' . config('invoice.company.address_line_2')),
		'country' => e(config('invoice.company.country')),
		'contact_email' => e(settings('contact_email')),
		'website' => e(config('invoice.company.website')),
	]) !!}
</x-legal-page>
