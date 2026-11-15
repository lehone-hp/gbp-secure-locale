<div class="hero hero-image-layout parallaxie">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="hero-box">
                    <div class="hero-content-box">
                        <div class="hero-sub-heading wow fadeInUp">
                            <div class="satisfy-client-images">
                                @foreach ([1, 2, 3] as $i)
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="{{ asset('assets/img/reviews/review-' . $i . '.webp') }}" alt="">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                            <div class="hero-satisfy-client-content">
                                <p>{{ __('home.customer_reviews') }}</p>
                            </div>
                        </div>

                        <div class="section-title">
                            <h1 class="wow fadeInUp" data-wow-delay="0.2s" data-cursor="-opaque">{{ __('home.hero_title') }}</h1>
                            <p class="wow fadeInUp" data-wow-delay="0.4s">{!! __('home.hero_description') !!}</p>
                        </div>

                        <div class="hero-content-btn wow fadeInUp" data-wow-delay="0.6s">
                            <a href="{{ route('contact') }}" class="btn-default">{{ __('home.get_quote') }}</a>
                        </div>
                    </div>

                    <div class="hero-counter-box wow fadeInUp">
                        <div class="hero-counter-box-content">
                            <p>{{ __('home.hero_trusted') }}</p>
                        </div>
                        <div class="hero-counter-item-list">
                            <div class="hero-counter-item">
                                <h2><span class="counter">10</span>K+</h2>
                                <p>{{ __('home.satisfied_growers') }}</p>
                            </div>
                            <div class="hero-counter-item">
                                <h2><span class="counter">4.9</span>/5</h2>
                                <p>{{ __('home.reviews') }}</p>
                            </div>
                            <div class="hero-counter-item">
                                <h2><span class="counter">98</span>%</h2>
                                <p>{{ __('home.stat_satisfaction') }}</p>
                            </div>
                            <div class="hero-counter-item">
                                <h2><span class="counter">3</span>+</h2>
                                <p>{{ __('home.stat_years') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
