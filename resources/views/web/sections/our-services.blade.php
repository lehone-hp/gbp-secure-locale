<div class="our-services bg-section">
    <div class="container">
        <div class="row section-row">
            <div class="col-lg-12">
                <div class="section-title section-title-center">
                    <span class="section-sub-title wow fadeInUp">{{ __('home.services_mini') }}</span>
                    <h2 class="text-anime-style-3" data-cursor="-opaque">{{ __('home.services_title') }}</h2>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach (\App\Models\Service::visible()->get() as $service)
                <div class="col-xl-4 col-md-6">
                    <div class="service-item service-item--link wow fadeInUp" data-wow-delay="{{ ($loop->index % 3) * 0.2 }}s">
                        <div class="icon-box">
                            <img src="{{ asset('seoai/images/icon-service-item-' . (($loop->index % 5) + 1) . '.svg') }}" alt="">
                        </div>
                        <div class="service-item-body">
                            <div class="service-item-content">
                                <h2><a class="stretched-link" href="{{ route('services.show', $service->slug) }}">{{ $service->name }}</a></h2>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 140) }}</p>
                            </div>
                            <div class="service-item-btn">
                                <a href="{{ route('services.show', $service->slug) }}" class="readmore-btn">{{ __('common.read_more') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
