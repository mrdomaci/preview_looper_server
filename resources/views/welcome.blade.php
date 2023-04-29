<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    @include('layouts.header')

    <div class="container">
            <div class="jumbotron text-center mt-4">
            <h1>{{ __('messages.homepage_title') }}</h1>
            <p>{{ __('messages.homepage_description') }}</p>
            <a class="btn btn-primary btn-lg mb-4" href="#" role="button">{{ __('messages.homepage_button') }}</a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" id="product-1">
                <div class="card h-100">
                    <a href="#"><img class="card-img-top" src="{{ url('images/product1_1.png') }}" alt=""></a>
                    <div class="card-body">
                    <h4 class="card-title">
                        <a href="#">{{ __('Product 1') }}</a>
                    </h4>
                    <h5>{{ __('$19.99') }}</h5>
                    <p class="card-text">{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id aliquet odio. Donec facilisis felis vel ante malesuada, eget faucibus magna bibendum.') }}</p>
                    </div>
                    <div class="card-footer">
                    <small class="text-muted">{{ __('★ ★ ★ ★ ☆') }}</small>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-2">
                <div class="card h-100">
                    <a href="#"><img class="card-img-top" src="{{ url('images/product2_1.png') }}" alt=""></a>
                    <div class="card-body">
                    <h4 class="card-title">
                        <a href="#">{{ __('Product 2') }}</a>
                    </h4>
                    <h5>{{ __('$24.99') }}</h5>
                    <p class="card-text">{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id aliquet odio. Donec facilisis felis vel ante malesuada, eget faucibus magna bibendum.') }}</p>
                    </div>
                    <div class="card-footer">
                    <small class="text-muted">{{ __('★ ★ ★ ☆ ☆') }}</small>
                    </div>
                </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4" id="product-3">
                <div class="card h-100">
                    <a href="#"><img class="card-img-top" src="{{ url('images/product3_1.png') }}" alt=""></a>
                    <div class="card-body">
                    <h4 class="card-title">
                        <a href="#">{{ __('Product 3') }}</a>
                    </h4>
                    <h5>{{ __('$14.99') }}</h5>
                    <p class="card-text">{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam id aliquet odio. Donec facilisis felis vel ante malesuada, eget faucibus magna bibendum.') }}</p>
                    </div>
                    <div class="card-footer">
                    <small class="text-muted">{{ __('★ ★ ★ ★ ★') }}</small>
                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer fixed-bottom bg-dark text-light p-4">
        <div class="container">
            <ul class="list-inline">
                @if (Config::get('app.locale') == 'cs')
                <li class="list-inline-item"><bold class="link-light">CS</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'cs')) }}">CS</a></li>
                @endif
                @if (Config::get('app.locale') == 'sk')
                <li class="list-inline-item"><bold class="link-light">SK</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'sk')) }}">SK</a></li>
                @endif
                @if (Config::get('app.locale') == 'hu')
                <li class="list-inline-item"><bold class="link-light">HU</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'hu')) }}">HU</a></li>
                @endif
                @if (Config::get('app.locale') == 'en')
                <li class="list-inline-item"><bold class="link-light">EN</bold></li>
                @else
                <li class="list-inline-item"><a href="{{ URL::route('homepage.setLocale', array('locale' => 'en')) }}">EN</a></li>
                @endif
                <li class="list-inline-item"><a href="">info@slabihoud.cz</a></li>
            </ul>
        </div>
    </footer>

    @include('layouts.footer')
    <script src="{{ url('js/jquery.js') }}"></script>
    <script src="{{ url('js/bootstrap.js') }}"></script>
    <script src="{{ url('js/carousel.js') }}"></script>
</body>
</html>
