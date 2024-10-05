@include('../layouts.header')
<div class="container mt-5 text-center">
    <h1 class="display-1">401</h1>
    <h2>{{  $exception->getMessage() ?: __('general.client_error') }}</h2>
    <p class="lead">{{ __('general.sorry') }}</p>
    <a href="mailto:info@slabihoud.cz">info@slabihoud.cz</a>
</div>
@include('../layouts.footer')