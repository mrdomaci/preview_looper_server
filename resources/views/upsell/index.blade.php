@include('../layouts.header')
<body>
    <div id="upsell-settings" data-infinite-repeat="0" data-return-to-default="0" data-show-time="1500"></div>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-light">
            <a class="navbar-brand" href="{{ URL::route('plugin', array('serviceUrlPath' => $service_url_path) ) }}">{{ __('upsell.addon_title') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::route('terms', array('serviceUrlPath' => $service_url_path) ) }}">{{ __('general.terms') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://shoptet.cz">shoptet.cz</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ URL::route('welcome') }}">slabihoud.cz</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mailto:info@slabihoud.cz">info@slabihoud.cz</a>
                    </li>
                    @if (Config::get('app.locale') == 'cs')
                    <li class="nav-item"><bold class="nav-link active">CS</bold></li>
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ URL::route('homepage.setLocale', array('locale' => 'cs')) }}">CS</a></li>
                    @endif
                    @if (Config::get('app.locale') == 'sk')
                    <li class="nav-item"><bold class="nav-link active">SK</bold></li>
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ URL::route('homepage.setLocale', array('locale' => 'sk')) }}">SK</a></li>
                    @endif
                    @if (Config::get('app.locale') == 'hu')
                    <li class="nav-item"><bold class="nav-link active">HU</bold></li>
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ URL::route('homepage.setLocale', array('locale' => 'hu')) }}">HU</a></li>
                    @endif
                    @if (Config::get('app.locale') == 'en')
                    <li class="nav-item"><bold class="nav-link active">EN</bold></li>
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ URL::route('homepage.setLocale', array('locale' => 'en')) }}">EN</a></li>
                    @endif
                </ul>
            </div>
        </nav>

            <div class="jumbotron text-center mt-4">
                <div class="logo-warper">
                    @if ( Config::get('app.locale') == 'cs')
                        <img class="logo-img" src="{{ url('images/dynamic-preview-images/webpage_logo_cs.png') }}" alt="">
                    @elseif ( Config::get('app.locale') == 'en')
                        <img class="logo-img" src="{{ url('images/dynamic-preview-images/webpage_logo_en.png') }}" alt="">
                    @elseif ( Config::get('app.locale') == 'sk')
                        <img class="logo-img" src="{{ url('images/dynamic-preview-images/webpage_logo_sk.png') }}" alt="">
                    @elseif ( Config::get('app.locale') == 'hu')
                        <img class="logo-img" src="{{ url('images/dynamic-preview-images/webpage_logo_hu.png') }}" alt="">
                    @else
                        <h1>{{ __('dynamic-preview-images.addon_title') }}</h1>
                    @endif
                </div>
            <p>{{ __('dynamic-preview-images.homepage_description') }}</p>
            <a class="btn btn-primary btn-lg mb-4" href="{{ __('dynamic-preview-images.url_addon')}}" role="button">{{ __('general.homepage_button') }}</a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" id="product-1">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-1">
                        <a class="nav-link" href="#" class="image"><img class="card-img-top" src="{{ url('images/dynamic-preview-images/product1_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('dynamic-preview-images.desktop_version') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('dynamic-preview-images.desktop_version_text') }}</p>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-2">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-2">
                        <a class="nav-link" href="#" class="image"><img class="card-img-top" src="{{ url('images/dynamic-preview-images/product2_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('dynamic-preview-images.mobile_version') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('dynamic-preview-images.mobile_version_text') }}</p>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-3">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-3">
                        <a class="nav-link" href="#" class="image"><img class="card-img-top" src="{{ url('images/dynamic-preview-images/product3_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('dynamic-preview-images.plugin_settings') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('dynamic-preview-images.plugin_settings_text') }}</p>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    @include('../layouts.footer')
    <script src="{{ url('js/bootstrap.js') }}"></script>
    <script src="{{ url('js/carousel.js') }}"></script>
</body>
</html>
