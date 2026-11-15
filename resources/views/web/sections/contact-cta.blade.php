<div class="cta-box bg-section">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-xl-6">
                <div class="section-title">
                    <span class="section-sub-title wow fadeInUp">{{ __('contact.section_mini') }}</span>
                    <h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('home.cta_title') }}</h2>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="section-content-btn">
                    <div class="section-title-content wow fadeInUp" data-wow-delay="0.2s">
                        <p>{{ __('home.cta_description') }}</p>
                    </div>
                    <div class="section-btn wow fadeInUp" data-wow-delay="0.4s">
                        <a class="btn-default" href="{{ route('contact') }}">{{ __('home.cta_button') }}</a>
                    </div>
                </div>
            </div>
        </div>

        @if ($showContactInfo ?? true)
            <div class="cta-contact-panel wow fadeInUp" data-wow-delay="0.2s">
                @if (settings('contact_phone'))
                    <div class="contact-info-item">
                        <div class="icon-box"><img src="{{ asset('seoai/images/icon-phone-white.svg') }}" alt=""></div>
                        <div class="contact-info-item-content">
                            <h3>{{ __('contact.phone_number') }}</h3>
                            <p><a href="tel:{{ settings('contact_phone') }}">{{ settings('contact_phone') }}</a></p>
                        </div>
                    </div>
                @endif

                @if (settings('contact_email'))
                    <div class="contact-info-item">
                        <div class="icon-box"><img src="{{ asset('seoai/images/icon-mail-white.svg') }}" alt=""></div>
                        <div class="contact-info-item-content">
                            <h3>{{ __('contact.email_address') }}</h3>
                            <p><a href="mailto:{{ settings('contact_email') }}">{{ settings('contact_email') }}</a></p>
                        </div>
                    </div>
                @endif

                @if (settings('address'))
                    <div class="contact-info-item">
                        <div class="icon-box"><img src="{{ asset('seoai/images/icon-location-white.svg') }}" alt=""></div>
                        <div class="contact-info-item-content">
                            <h3>{{ __('contact.head_office') }}</h3>
                            <p>{{ settings('address') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
