@include('layouts.header')
<body>
    <div class="container py-5">
        <div class="jumbotron text-center mt-4">
                <div class="logo-warper">
                    @if ( Config::get('app.locale') == 'cs')
                        <a href="/"><img class="logo-img" src="{{ url('images/webpage_logo_cs.png') }}" alt=""></a>
                    @elseif ( Config::get('app.locale') == 'en')
                        <a href="/"><img class="logo-img" src="{{ url('images/webpage_logo_en.png') }}" alt=""></a>
                    @elseif ( Config::get('app.locale') == 'sk')
                        <a href="/"><img class="logo-img" src="{{ url('images/webpage_logo_sk.png') }}" alt=""></a>
                    @elseif ( Config::get('app.locale') == 'hu')
                        <a href="/"><img class="logo-img" src="{{ url('images/webpage_logo_hu.png') }}" alt=""></a>
                    @else
                        <a href="/"><h1>{{ __('messages.homepage_title') }}</h1></a>
                    @endif
                </div>
        </div>
        <h1 class="mb-4">{{ __('messages.terms') }}</h1>
        <div class="accordion" id="termsAccordion">
            <div class="accordion-item">
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#termsAccordion">
                    <div class="accordion-body">
                        <h4>{{ __('messages.terms_content_header_1') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_1') }}</li>
                        </ul>
                        <p>{{__('messages.terms_content_11')}}</p>
                        <p>{{__('messages.terms_content_12')}}</p>
                        <p>{{__('messages.terms_content_13')}}</p>
                        <h4>{{ __('messages.terms_content_header_2') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_21') }}</li>
                            <li>{{ __('messages.terms_content_subheader_22') }}</li>
                            <li>{{ __('messages.terms_content_subheader_23') }}</li>
                        </ul>
                        <h4>{{ __('messages.terms_content_header_3') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_31') }}</li>
                        </ul>
                        <h4>{{ __('messages.terms_content_header_4') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_41') }}</li>
                            <li>{{ __('messages.terms_content_subheader_42') }}</li>
                            <li>{{ __('messages.terms_content_subheader_43') }}</li>
                        </ul>
                        <h4>{{ __('messages.terms_content_header_5') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_51') }}</li>
                            <li>{{ __('messages.terms_content_subheader_52') }}</li>
                            <li>{{ __('messages.terms_content_subheader_53') }}</li>
                        </ul>
                        <h4>{{ __('messages.terms_content_header_6') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_61') }}</li>
                            <li>{{ __('messages.terms_content_subheader_62') }}</li>
                            <li>{{ __('messages.terms_content_subheader_63') }}</li>
                            <li>{{ __('messages.terms_content_subheader_64') }}</li>
                        </ul>
                        <h4 id="privacy">{{ __('messages.privacy') }}</h4>
                        <ul>
                            <li>{{ __('messages.terms_content_subheader_1')}}</li>
                        </ul>
                        <p>{{__('messages.privacy_content_1')}}</p>
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
