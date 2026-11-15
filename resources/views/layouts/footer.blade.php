    <footer class="main-footer bg-section dark-section">
        <div class="container">
            <div class="row">
                <div class="col-xl-4">
                    <div class="about-footer">
                        <div class="about-footer-content">
                            <a href="{{ route('index') }}" class="footer-logo" aria-label="{{ config('app.name') }}"><x-logo :size="44" /></a>
                            <p>{{ __('footer.about_text') }}</p>
                        </div>

                        <div class="footer-social-links">
                            <ul>
                                @if(settings('facebook_url'))<li><a href="{{ settings('facebook_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a></li>@endif
                                @if(settings('twitter_url'))<li><a href="{{ settings('twitter_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a></li>@endif
                                @if(settings('instagram_url'))<li><a href="{{ settings('instagram_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>@endif
                                @if(settings('linkedin_url'))<li><a href="{{ settings('linkedin_url') }}" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a></li>@endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="footer-links-box">
                        <div class="footer-links">
                            <h2>{{ __('footer.quick_links') }}</h2>
                            <ul>
                                <li><a href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
                                <li><a href="{{ route('services.index') }}">{{ __('navigation.services') }}</a></li>
                                <li><a href="{{ route('achievements.index') }}">{{ __('navigation.achievements') }}</a></li>
                                <li><a href="{{ route('contact') }}">{{ __('navigation.contact') }}</a></li>
                            </ul>
                        </div>

                        <div class="footer-links">
                            <h2>{{ __('footer.our_services') }}</h2>
                            <ul>
                                @foreach(\App\Models\Service::visible()->get() as $service)
                                    <li><a href="{{ route('services.show', $service->slug) }}">{{ $service->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="footer-links">
                            <h2>{{ __('footer.legal') }}</h2>
                            <ul>
                                <li><a href="{{ route('terms-and-conditions') }}">{{ __('navigation.terms_conditions') }}</a></li>
                                <li><a href="{{ route('privacy-policy') }}">{{ __('navigation.privacy_policy') }}</a></li>
                                <li><a href="{{ route('refund-policy') }}">{{ __('navigation.refund_policy') }}</a></li>
                                <li><a href="{{ route('legal-notice') }}">{{ __('navigation.legal_notice') }}</a></li>
                            </ul>
                        </div>

                        <div class="footer-links footer-contact-box">
                            <h2>{{ __('footer.contact_title') }}</h2>
                            <div class="footer-contact-list">
                                @if(settings('contact_phone'))
                                <div class="footer-contact-item">
                                    <ul>
                                        <li><img src="{{ asset('seoai/images/icon-phone-accent.svg') }}" alt="">{{ __('footer.phone') }}</li>
                                        <li><a href="tel:{{ settings('contact_phone') }}">{{ settings('contact_phone') }}</a></li>
                                    </ul>
                                </div>
                                @endif
                                @if(settings('contact_email'))
                                <div class="footer-contact-item">
                                    <ul>
                                        <li><img src="{{ asset('seoai/images/icon-mail-accent.svg') }}" alt="">{{ __('footer.email') }}</li>
                                        <li><a href="mailto:{{ settings('contact_email') }}">{{ settings('contact_email') }}</a></li>
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="footer-copyright-text">
                        <p>{{ __('common.copyright', ['year' => date('Y')]) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
