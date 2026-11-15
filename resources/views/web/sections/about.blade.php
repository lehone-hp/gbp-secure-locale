<div class="about-us">
    <div class="container">
        <div class="row">
            <div class="col-xl-6">
                <div class="about-us-image-box wow fadeInUp">
                    <div class="about-us-image">
                        <figure class="image-anime">
                            <img src="{{ asset('seoai/images/about-us-image.jpg') }}" alt="">
                        </figure>
                    </div>

                    <div class="about-review-box">
                        <div class="about-review-box-header">
                            <div class="about-review-box-content">
                                <h2>10K+ <span><i class="fa-solid fa-arrow-trend-up"></i></span></h2>
                                <p>{{ __('home.satisfied_growers') }}</p>
                            </div>
                            <div class="satisfy-client-images">
                                @foreach ([1, 2, 3] as $i)
                                    <div class="satisfy-client-image">
                                        <figure class="image-anime">
                                            <img src="{{ asset('assets/img/reviews/review-' . $i . '.webp') }}" alt="">
                                        </figure>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="about-review-box-body">
                            <h3><span class="about-review-rating-star">@for ($i = 0; $i < 5; $i++)<i class="fa fa-solid fa-star"></i>@endfor</span><span class="counter">4.9</span>/5</h3>
                            <p>{{ __('home.reviews') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="about-us-content">
                    <div class="section-title">
                        <span class="section-sub-title">{{ __('home.our_story_mini') }}</span>
                        <h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('home.our_story_title') }}</h2>
                        <p class="wow fadeInUp" data-wow-delay="0.2s">{!! __('home.our_story_description') !!}</p>
                    </div>

                    <div class="about-us-body">
                        <div class="about-body-content wow fadeInUp" data-wow-delay="0.2s">
                            <div class="about-us-feature-list">
                                <ul>
                                    @foreach ([1, 2, 3, 4] as $i)
                                        <li>{{ __('home.work_with_us_feature_' . $i . '_title') }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="about-us-btn">
                                <a href="{{ route('achievements.index') }}" class="btn-default">{{ __('home.about_button') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
