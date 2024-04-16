@include('layouts.header')
<body>
    <div class="container">
    <nav class="navbar navbar-expand-lg bg-light">
        <a class="navbar-brand" href="{{ URL::route('welcome') }}">Slabihoud.cz</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="https://shoptet.cz">shoptet.cz</a>
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

    <div class="jumbotron">
        <h1 class="display-4 mt-4">{{ __('general.introduction')}}</h1>
        <p class="lead">{{ __('general.subtitle')}}</p>
        <hr class="my-4">
    </div>
        <div class="row">
            <div class="col-md-3">
                <a href="{{ URL::route('plugin', array('serviceUrlPath' => 'easy-upsell')) }}">
                @if ( Config::get('app.locale') == 'cs')
                    <img class="logo-plugin" src="{{ url('images/easy-upsell/webpage_logo_cs.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'en')
                    <img class="logo-plugin" src="{{ url('images/easy-upsell/webpage_logo_en.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'sk')
                    <img class="logo-plugin" src="{{ url('images/easy-upsell/webpage_logo_sk.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'hu')
                    <img class="logo-plugin" src="{{ url('images/easy-upsell/webpage_logo_hu.png') }}" alt="">
                @else
                    <h1>{{ __('easy-upsell.addon_title') }}</h1>
                @endif
                </a>
            </div>
            <div class="col-md-5">
                <a href="{{ URL::route('plugin', array('serviceUrlPath' => 'easy-upsell')) }}"><h2>{{__('easy-upsell.addon_title')}}</h2></a>
                <p class="text-justify">{{ __('easy-upsell.homepage_description') }}</p>
                <p class="text-justify">{{ __('easy-upsell.general_description_1') }}</p>
                <p class="text-justify">{{ __('easy-upsell.general_description_2') }}</p>
                <div class="text-center">
                    <a class="btn btn-primary btn-lg mb-4" href="{{ __('easy-upsell.url_addon')}}" role="button">{{ __('general.homepage_button') }}</a>
                </div>
            </div>
            <div class="col-md-4">
                <img class="img-fluid p-1" src="{{ url('images/dynamic-preview-images/preview_1.gif') }}" alt="">
                <img class="img-fluid p-1" src="{{ url('images/dynamic-preview-images/preview_2.gif') }}" alt="">
            </div>
        </div>
        <hr class="my-4">
        <div class="row">
            <div class="col-md-3">
                <a href="{{ URL::route('plugin', array('serviceUrlPath' => 'dynamic-preview-images')) }}">
                @if ( Config::get('app.locale') == 'cs')
                    <img class="logo-plugin" src="{{ url('images/dynamic-preview-images/webpage_logo_cs.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'en')
                    <img class="logo-plugin" src="{{ url('images/dynamic-preview-images/webpage_logo_en.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'sk')
                    <img class="logo-plugin" src="{{ url('images/dynamic-preview-images/webpage_logo_sk.png') }}" alt="">
                @elseif ( Config::get('app.locale') == 'hu')
                    <img class="logo-plugin" src="{{ url('images/dynamic-preview-images/webpage_logo_hu.png') }}" alt="">
                @else
                    <h1>{{ __('dynamic-preview-images.addon_title') }}</h1>
                @endif
                </a>
            </div>
            <div class="col-md-5">
                <a href="{{ URL::route('plugin', array('serviceUrlPath' => 'dynamic-preview-images')) }}"><h2>{{__('dynamic-preview-images.addon_title')}}</h2></a>
                <p class="text-justify">{{ __('dynamic-preview-images.homepage_description') }}</p>
                <p class="text-justify">{{ __('dynamic-preview-images.general_description_1') }}</p>
                <p class="text-justify">{{ __('dynamic-preview-images.general_description_2') }}</p>
                <div class="text-center">
                    <a class="btn btn-primary btn-lg mb-4" href="{{ __('dynamic-preview-images.url_addon')}}" role="button">{{ __('general.homepage_button') }}</a>
                </div>
            </div>
            <div class="col-md-4">
                <img class="img-fluid p-1" src="{{ url('images/dynamic-preview-images/preview_1.gif') }}" alt="">
                <img class="img-fluid p-1" src="{{ url('images/dynamic-preview-images/preview_2.gif') }}" alt="">
            </div>
        </div>
        <hr class="my-4">

    </div>
    @include('layouts.footer')
</body>
</html>
