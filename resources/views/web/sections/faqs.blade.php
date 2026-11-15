@php($faqs = \App\Models\Faq::orderBy('id')->get())
@if ($faqs->isNotEmpty())
<div class="our-faqs">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="faqs-image-box wow fadeInUp">
                    <div class="faqs-image">
                        <figure class="image-anime">
                            <img src="{{ asset('seoai/images/our-faqs-image.jpg') }}" alt="">
                        </figure>
                    </div>
                    <div class="faqs-contact-item-list">
                        @if (settings('contact_phone'))
                            <div class="faqs-contact-item">
                                <div class="icon-box"><img src="{{ asset('seoai/images/icon-phone-white.svg') }}" alt=""></div>
                                <div class="faqs-contact-item-content">
                                    <p>{{ __('footer.phone') }}</p>
                                    <h3><a href="tel:{{ settings('contact_phone') }}">{{ settings('contact_phone') }}</a></h3>
                                </div>
                            </div>
                        @endif
                        @if (settings('contact_email'))
                            <div class="faqs-contact-item">
                                <div class="icon-box"><img src="{{ asset('seoai/images/icon-mail-white.svg') }}" alt=""></div>
                                <div class="faqs-contact-item-content">
                                    <p>{{ __('footer.email') }}</p>
                                    <h3><a href="mailto:{{ settings('contact_email') }}">{{ settings('contact_email') }}</a></h3>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="faqs-content">
                    <div class="section-title">
                        <span class="section-sub-title wow fadeInUp">{{ __('home.faqs_mini') }}</span>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('home.faqs_title') }}</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.2s">{{ __('home.faqs_description') }}</p>
                    </div>

                    <div class="faq-accordion" id="accordion">
                        @foreach ($faqs as $faq)
                            <div class="accordion-item wow fadeInUp" data-wow-delay="{{ ($loop->index % 4) * 0.2 }}s">
                                <h2 class="accordion-header" id="heading{{ $faq->id }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $faq->id }}">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}. {{ $faq->question }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" role="region" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#accordion">
                                    <div class="accordion-body">
                                        <p>{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
