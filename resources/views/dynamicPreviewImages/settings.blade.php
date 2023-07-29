@include('../layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('dynamic-preview-images.addon_title') }} - {{ __('general.settings') }}</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['serviceUrlPath' => $service_url_path, 'language' => $language, 'eshopId' => $client->eshop_id]) }}">
        @csrf
        @foreach ($settings_service as $setting)
            <div class="form-group row mt-4">
                <label for="settings_{{ $setting->name }}" class="col-md-6 col-form-label">{{ __($setting->name) }}:</label>
                <div class="col-md-6">
                    <select class="form-control" name="{{ $setting->id }}" id="{{ $setting->id }}">
                        @foreach ($setting->settingsServicesOptions as $option)
                            <option
                                @if ($option->isSelected($client))
                                    selected
                                @endif
                                value="{{ $option->id }}"
                            >{{ __($option->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endforeach
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <input type="hidden" name="eshop_id" value="{{$client->eshop_id}}">
                <button type="submit" class="btn btn-primary m-3">{{ __('general.save') }}</button>
            </div>
        </div>
      </form>
    </div>
  </div>
</div>
@include('../layouts.footer')