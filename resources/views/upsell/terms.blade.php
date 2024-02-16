@include('layouts.header')
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-light">
            <a class="navbar-brand" href="{{ URL::route('plugin', array('serviceUrlPath' => $service_url_path) ) }}">{{ __('dynamic-preview-images.addon_title') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ URL::route('terms', array('serviceUrlPath' => $service_url_path) ) }}">{{ __('general.terms') }}</a>
                    </li>
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
        <h1 class="mb-4 mt-4">{{ __('general.terms') }}</h1>
        <div class="accordion" id="termsAccordion">
            <div class="accordion-item">
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#termsAccordion">
                    <div class="accordion-body">
                        <h4>{{ __('general.terms_content_header_1') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_1') }}</li>
                        </ul>
                        <p>{{__('dynamic-preview-images.terms_content_11')}}</p>
                        <p>{{__('dynamic-preview-images.terms_content_12')}}</p>
                        <p>{{__('dynamic-preview-images.terms_content_13')}}</p>
                        <h4>{{ __('general.terms_content_header_2') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_21') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_22') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_23') }}</li>
                        </ul>
                        <h4>{{ __('general.terms_content_header_3') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_31') }}</li>
                        </ul>
                        <h4>{{ __('general.terms_content_header_4') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_41') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_42') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_43') }}</li>
                        </ul>
                        <h4>{{ __('general.terms_content_header_5') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_51') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_52') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_53') }}</li>
                        </ul>
                        <h4>{{ __('general.terms_content_header_6') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_61') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_62') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_63') }}</li>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_64') }}</li>
                        </ul>
                        <h4 id="privacy">{{ __('general.privacy') }}</h4>
                        <ul>
                            <li>{{ __('dynamic-preview-images.terms_content_subheader_1')}}</li>
                        </ul>
                        <p>{{__('dynamic-preview-images.privacy_content_1')}}</p>
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
