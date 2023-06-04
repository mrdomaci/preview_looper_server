<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>
    @include('layouts.header')

    <div class="container py-5">
        <h1 class="mb-4">{{ __('messages.terms') }}</h1>
        <div class="accordion" id="termsAccordion">
            <div class="accordion-item">
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#termsAccordion">
                    <div class="accordion-body">
                        <p>{{ __('messages.terms_content_1') }}</p>
                        <ol>
                            <li>{{ __('messages.terms_content_2') }}</li>
                            <li>{{ __('messages.terms_content_3') }}</li>
                            <li>{{ __('messages.terms_content_4') }}</li>
                            <li>{{ __('messages.terms_content_5') }}</li>
                            <li>{{ __('messages.terms_content_6') }}</li>
                            <li>{{ __('messages.terms_content_7') }}</li>
                            <li>{{ __('messages.terms_content_8') }}</li>
                            <li>{{ __('messages.terms_content_9') }}</li>
                        </ol>
                        <p id="privacy">{{ __('messages.privacy') }}</p>
                        <ol>
                            <li>{{ __('messages.privacy_content_1')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
    <script src="{{ url('js/jquery.js') }}"></script>
    <script src="{{ url('js/bootstrap.js') }}"></script>
    <script src="{{ url('js/carousel.js') }}"></script>
</body>
</html>
