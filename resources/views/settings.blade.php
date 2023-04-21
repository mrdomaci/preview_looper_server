@include('layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('messages.addon_title') }} - {{ __('messages.settings') }} [{{$eshop_name}}]</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['language' => $language, 'code' => $code]) }}">
        @csrf
        <div class="form-group row mt-4">
            <label for="settings_infinite_repeat" class="col-md-6 col-form-label">{{ __('messages.infinite_repeat') }}:</label>
            <div class="col-md-6">
                <select class="form-control" name="settings_infinite_repeat" id="settings_infinite_repeat">
                <option
                    value="0"
                    @if ($infinite_repeat === 0)
                        selected 
                    @endif
                >{{ __('messages.infinite_repeat_disabled') }}</option>
                <option
                    value="1"
                    @if ($infinite_repeat === 1)
                        selected 
                    @endif
                >{{ __('messages.infinite_repeat_enabled') }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row mt-4">
            <label for="settings_return_to_default" class="col-md-6 col-form-label">{{ __('messages.return_to_default') }}:</label>
            <div class="col-md-6">
                <select class="form-control" name="settings_return_to_default" id="settings_return_to_default">
                    <option
                        value="0"
                        @if ($return_to_default === 0)
                            selected 
                        @endif
                    >{{ __('messages.return_to_default_disabled') }}</option>
                    <option
                        value="1"
                        @if ($return_to_default === 1)
                            selected 
                        @endif
                    >{{ __('messages.return_to_default_enabled') }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row mt-4">
            <label for="settings_show_time" class="col-md-6 col-form-label">{{ __('messages.show_time') }}:</label>
            <div class="col-md-6">
                <select class="form-control" name="settings_show_time" id="settings_show_time">
                    <option
                        value="1000"
                        @if ($show_time <= 1000)
                            selected 
                        @endif
                    >{{ __('messages.show_time_short') }}</option>
                    <option
                        value="2000"
                        @if ($show_time > 1000 && $show_time <= 2000)
                            selected 
                        @endif
                    >{{ __('messages.show_time_medium') }}</option>
                    <option
                        value="3000"
                        @if ($show_time > 3000)
                            selected 
                        @endif
                    >{{ __('messages.show_time_long') }}</option>
                </select>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <input type="hidden" name="eshop_id" value="{{$eshop_id}}">
                <input type="hidden" name="code" value="{{$code}}">
                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
@include('layouts.footer')