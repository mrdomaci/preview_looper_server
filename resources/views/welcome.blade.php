<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    @include('layouts.header')
    <div id="preview-looper-settings" data-infinite-repeat="0" data-return-to-default="0" data-show-time="1500"></div>
    <div class="container">
            <div class="jumbotron text-center mt-4">
            <h1>{{ __('messages.homepage_title') }}</h1>
            <p>{{ __('messages.homepage_description') }}</p>
            <a class="btn btn-primary btn-lg mb-4" href="#" role="button">{{ __('messages.homepage_button') }}</a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" id="product-1">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-1">
                        <a href="#" class="image"><img class="card-img-top" src="{{ url('images/product1_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('messages.desktop_version') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('messages.desktop_version_text') }}</p>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-2">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-2">
                        <a href="#" class="image"><img class="card-img-top" src="{{ url('images/product2_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('messages.mobile_version') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('messages.mobile_version_text') }}</p>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-3">
                <div class="card h-100 p-area">
                    <div class="p position-relative" data-micro-identifier="product-3">
                        <a href="#" class="image"><img class="card-img-top" src="{{ url('images/product3_1.png') }}" alt=""></a>
                    </div>
                    <div class="card-body">
                    <h4 class="card-title">
                        <h2>{{ __('messages.plugin_settings') }}</h2>
                    </h4>
                    <p class="card-text">{{ __('messages.plugin_settings_text') }}</p>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footer')
    <script src="{{ url('js/bootstrap.js') }}"></script>
    <script src="{{ url('js/carousel.js') }}"></script>
</body>
</html>
