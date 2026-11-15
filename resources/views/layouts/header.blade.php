    <header class="main-header">
        <div class="header-sticky">
            <nav class="navbar navbar-expand-lg">
                <div class="container">
                    <a class="navbar-brand" href="{{ route('index') }}">
                        <x-logo :size="40" />
                    </a>

                    <div class="collapse navbar-collapse main-menu">
                        <div class="nav-menu-wrapper">
                            <ul class="navbar-nav mr-auto" id="menu">
                                <li class="nav-item"><a class="nav-link" href="{{ route('index') }}">{{ __('navigation.home') }}</a></li>
                                <li class="nav-item submenu"><a class="nav-link" href="{{ route('services.index') }}">{{ __('navigation.services') }}</a>
                                    <ul>
                                        @foreach (\App\Models\Service::visible()->get() as $service)
                                            <li class="nav-item"><a class="nav-link" href="{{ route('services.show', $service->slug) }}">{{ $service->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('achievements.index') }}">{{ __('navigation.achievements') }}</a></li>
                                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">{{ __('navigation.contact') }}</a></li>
                            </ul>
                        </div>

                        <div class="header-btn">
                            <x-language-switcher :icon-only="true" />
                            <x-currency-switcher />
                            <a href="{{ route('contact') }}" class="btn-default">{{ __('home.get_quote') }}</a>
                        </div>
                    </div>
                    <div class="navbar-toggle"></div>
                </div>
            </nav>
            <div class="responsive-menu"></div>
        </div>
    </header>
