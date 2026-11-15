@php
    $testimonials = [
        ['image' => 'review-1.webp', 'name' => 'Harper Russo', 'text' => __('home.testimonial_1')],
        ['image' => 'review-2.webp', 'name' => 'Michael Liam', 'text' => __('home.testimonial_2')],
        ['image' => 'review-3.webp', 'name' => 'Tyler Curtis', 'text' => __('home.testimonial_3')],
    ];
@endphp
<div class="our-testimonials bg-section dark-section parallaxie">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-xl-6">
                <div class="section-title">
                    <span class="section-sub-title wow fadeInUp">{{ __('home.testimonials_mini') }}</span>
                    <h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('home.testimonials_title') }}</h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="row-lg-12">
                <div class="testimonial-slider">
                    <div class="swiper">
                        <div class="swiper-wrapper" data-cursor-text="Drag">
                            @foreach ($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="testimonial-item">
                                        <div class="testimonial-item-image">
                                            <figure>
                                                <img src="{{ asset('assets/img/reviews/' . $testimonial['image']) }}" alt="{{ $testimonial['name'] }}">
                                            </figure>
                                        </div>
                                        <div class="testimonial-item-body">
                                            <div class="testimonial-item-rating">
                                                @for ($i = 0; $i < 5; $i++)<i class="fa fa-solid fa-star"></i>@endfor
                                            </div>
                                            <div class="testimonial-item-content">
                                                <p>"{{ $testimonial['text'] }}"</p>
                                            </div>
                                            <div class="testimonial-item-author">
                                                <h2>{{ $testimonial['name'] }}</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="testimonial-btn">
                            <div class="testimonial-button-prev"></div>
                            <div class="testimonial-button-next"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
